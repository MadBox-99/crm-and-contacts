# Form Submission → CRM Pipeline Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** A `madbox-99/filament-form-builder` csomaggal beküldött minden űrlap automatikusan CRM-adattá válik (ügyfél/opportunity/interakció), mérhető dashboardon, és e-mail értesítést vált ki.

**Architecture:** A csomag `FormSubmissionProcessed` eventjére egy queue-olt host-listener (`ProcessFormSubmissionToCrm`) csatlakozik. Egy `SubmissionFieldMapper` service a beküldött mezőket CRM-mezőkké normalizálja (heurisztika + opcionális explicit leképezés a `form_crm_settings` táblából). A listener email alapján deduplikálva ügyfelet keres/hoz létre, Opportunity-t nyit, Interaction-t naplóz, lead-scoringot frissít, és értesítőt küld. A mérés Filament dashboard widgetekkel történik a `form_submissions` táblán. A vendor-csomagot NEM módosítjuk.

**Tech Stack:** PHP 8.4, Laravel 12, Filament v5, Livewire 4, Pest v4, `madbox-99/filament-form-builder` v0.4.

## Global Constraints

- Minden `.php` fájl tetején `declare(strict_types=1);`.
- Kapcsos zárójel minden kontrollstruktúrán, explicit típusok és visszatérési típusok mindenhol.
- A vendor-csomagot (`vendor/madbox-99/filament-form-builder`) TILOS módosítani — csak az eventre csatlakozunk.
- **Multi-tenant + queue:** a listener queue-ban fut, ahol a `TeamScope` global scope NEM szűr (nincs bekötött `current_team`). Ezért a listenerben minden lekérdezés és rekord-létrehozás **explicit `team_id`-vel** történik, amit a `FormSubmission`/`RegistrationForm` ad — soha nem a containerből.
- A `team_id`-t (`config('filament-form-builder.tenant_foreign_key')` alapértéke) `team_id`-ként kezeljük.
- Minden PHP-módosítás után: `vendor/bin/pint --dirty --format agent`.
- Tesztfuttatás: `php artisan test --compact --filter=...`.
- Enum címke-konvenció: `InteractionType` csak `HasLabel`-t implementál (nincs `HasColor`).

---

## Fájlstruktúra

Új fájlok:
- `database/migrations/2026_07_07_100000_create_form_crm_settings_table.php` — beállítás-tábla
- `app/Models/FormCrmSetting.php` — form-onkénti CRM-config modell
- `database/factories/FormCrmSettingFactory.php` — teszt factory
- `app/Services/SubmissionData.php` — normalizált beküldés DTO
- `app/Services/SubmissionFieldMapper.php` — mezőleképező service
- `app/Listeners/ProcessFormSubmissionToCrm.php` — az event feldolgozója
- `app/Mail/NewFormSubmissionMail.php` + `resources/views/emails/form-submission.blade.php` — értesítő
- `app/Services/FormSubmissionMetricsService.php` — mérési aggregációk
- `app/Filament/Widgets/FormSubmissionStatsWidget.php`
- `app/Filament/Widgets/FormSubmissionTrendWidget.php`
- `app/Filament/Widgets/FormSubmissionsByFormWidget.php`
- `app/Filament/Resources/FormCrmSettings/FormCrmSettingResource.php` (+ Pages, Schema, Table)
- `tests/Unit/SubmissionFieldMapperTest.php`
- `tests/Feature/ProcessFormSubmissionToCrmTest.php`
- `tests/Feature/FormSubmissionMetricsTest.php`
- `tests/Feature/FormCrmSettingResourceTest.php`

Módosított fájlok:
- `app/Enums/InteractionType.php` — új `FormSubmission` eset
- `app/Filament/Pages/SalesReports.php` — widgetek regisztrálása

---

## Task 1: `InteractionType::FormSubmission` enum-eset

**Files:**
- Modify: `app/Enums/InteractionType.php`
- Test: `tests/Unit/InteractionTypeTest.php` (Create)

**Interfaces:**
- Produces: `App\Enums\InteractionType::FormSubmission` (value `'form_submission'`), `getLabel()` visszaad nem-üres címkét.

- [ ] **Step 1: Write the failing test**

`tests/Unit/InteractionTypeTest.php`:
```php
<?php

declare(strict_types=1);

use App\Enums\InteractionType;

it('has a form submission case with a label', function (): void {
    expect(InteractionType::FormSubmission->value)->toBe('form_submission')
        ->and(InteractionType::FormSubmission->getLabel())->not->toBeNull();
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=InteractionTypeTest`
Expected: FAIL (undefined case `FormSubmission`).

- [ ] **Step 3: Add the enum case + label**

`app/Enums/InteractionType.php` — add case after `Note` and a match arm:
```php
    case Note = 'note';
    case FormSubmission = 'form_submission';
```
```php
            self::Note => __('Note'),
            self::FormSubmission => __('Form submission'),
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter=InteractionTypeTest`
Expected: PASS.

- [ ] **Step 5: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Enums/InteractionType.php tests/Unit/InteractionTypeTest.php
git commit -m "feat: add FormSubmission interaction type"
```

---

## Task 2: `form_crm_settings` tábla + `FormCrmSetting` modell + factory

**Files:**
- Create: `database/migrations/2026_07_07_100000_create_form_crm_settings_table.php`
- Create: `app/Models/FormCrmSetting.php`
- Create: `database/factories/FormCrmSettingFactory.php`
- Test: `tests/Feature/FormCrmSettingModelTest.php` (Create)

**Interfaces:**
- Produces: `App\Models\FormCrmSetting` — attribútumok: `registration_form_id:int`, `team_id:?int`, `field_map:array`, `create_opportunity:bool`, `opportunity_stage:?string`, `campaign_id:?int`, `enable_scoring:bool`. Relációk: `registrationForm(): BelongsTo`, `campaign(): BelongsTo`. Használja `BelongsToTeam`.
- `FormCrmSetting::factory()` state: `->forForm(RegistrationForm $form)`.

- [ ] **Step 1: Write the failing test**

`tests/Feature/FormCrmSettingModelTest.php`:
```php
<?php

declare(strict_types=1);

use App\Models\FormCrmSetting;
use App\Models\Team;
use Madbox99\FilamentFormBuilder\Models\RegistrationForm;

it('persists a crm setting with casts', function (): void {
    $team = Team::factory()->create();
    $form = RegistrationForm::factory()->create(['team_id' => $team->id]);

    $setting = FormCrmSetting::query()->create([
        'team_id' => $team->id,
        'registration_form_id' => $form->id,
        'field_map' => ['email' => 'email_field'],
        'create_opportunity' => true,
        'opportunity_stage' => 'lead',
        'enable_scoring' => false,
    ]);

    expect($setting->field_map)->toBe(['email' => 'email_field'])
        ->and($setting->create_opportunity)->toBeTrue()
        ->and($setting->enable_scoring)->toBeFalse()
        ->and($setting->registrationForm->is($form))->toBeTrue();
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=FormCrmSettingModelTest`
Expected: FAIL (no table / no model).

- [ ] **Step 3: Create the migration**

`database/migrations/2026_07_07_100000_create_form_crm_settings_table.php`:
```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_crm_settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('registration_form_id')
                ->unique()
                ->constrained('registration_forms')
                ->cascadeOnDelete();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->json('field_map')->nullable();
            $table->boolean('create_opportunity')->default(true);
            $table->string('opportunity_stage')->nullable()->default('lead');
            $table->foreignId('campaign_id')->nullable()->constrained('campaigns')->nullOnDelete();
            $table->boolean('enable_scoring')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_crm_settings');
    }
};
```

- [ ] **Step 4: Create the model**

`app/Models/FormCrmSetting.php`:
```php
<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Madbox99\FilamentFormBuilder\Models\RegistrationForm;
use Override;

final class FormCrmSetting extends Model
{
    use BelongsToTeam;
    use HasFactory;

    protected $fillable = [
        'registration_form_id',
        'team_id',
        'field_map',
        'create_opportunity',
        'opportunity_stage',
        'campaign_id',
        'enable_scoring',
    ];

    /**
     * @return BelongsTo<RegistrationForm, $this>
     */
    public function registrationForm(): BelongsTo
    {
        return $this->belongsTo(RegistrationForm::class);
    }

    /**
     * @return BelongsTo<Campaign, $this>
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * @return array<string, string>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'field_map' => 'array',
            'create_opportunity' => 'boolean',
            'enable_scoring' => 'boolean',
        ];
    }
}
```

- [ ] **Step 5: Create the factory**

`database/factories/FormCrmSettingFactory.php`:
```php
<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\FormCrmSetting;
use Illuminate\Database\Eloquent\Factories\Factory;
use Madbox99\FilamentFormBuilder\Models\RegistrationForm;

/**
 * @extends Factory<FormCrmSetting>
 */
final class FormCrmSettingFactory extends Factory
{
    protected $model = FormCrmSetting::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'registration_form_id' => RegistrationForm::factory(),
            'team_id' => null,
            'field_map' => null,
            'create_opportunity' => true,
            'opportunity_stage' => 'lead',
            'campaign_id' => null,
            'enable_scoring' => true,
        ];
    }

    public function forForm(RegistrationForm $form): static
    {
        return $this->state(fn (array $attributes): array => [
            'registration_form_id' => $form->id,
            'team_id' => $form->getAttribute('team_id'),
        ]);
    }
}
```

- [ ] **Step 6: Run test to verify it passes**

Run: `php artisan test --compact --filter=FormCrmSettingModelTest`
Expected: PASS.

- [ ] **Step 7: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add database/migrations app/Models/FormCrmSetting.php database/factories/FormCrmSettingFactory.php tests/Feature/FormCrmSettingModelTest.php
git commit -m "feat: add form_crm_settings table and model"
```

---

## Task 3: `SubmissionData` DTO + `SubmissionFieldMapper`

**Files:**
- Create: `app/Services/SubmissionData.php`
- Create: `app/Services/SubmissionFieldMapper.php`
- Test: `tests/Unit/SubmissionFieldMapperTest.php`

**Interfaces:**
- Consumes: `Madbox99\FilamentFormBuilder\Support\FormFieldBlueprint::fromForm(RegistrationForm $form): list<FormFieldBlueprint>` (public props `->type:string`, `->key:string`, `->label:string`; típuskonstansok `TYPE_EMAIL`, `TYPE_PHONE`, `TYPE_TEXT`, `TYPE_TEXTAREA`). `App\Models\FormCrmSetting` (Task 2).
- Produces:
  - `App\Services\SubmissionData` (`final readonly`): `__construct(?string $email, ?string $name, ?string $phone, ?string $companyName, string $notes, array $utm, ?string $referrer, array $raw)`; metódus `hasEmail(): bool`.
  - `App\Services\SubmissionFieldMapper`: `map(RegistrationForm $form, array $data, ?FormCrmSetting $settings = null): SubmissionData`.

- [ ] **Step 1: Write the failing tests**

`tests/Unit/SubmissionFieldMapperTest.php`:
```php
<?php

declare(strict_types=1);

use App\Models\FormCrmSetting;
use App\Services\SubmissionFieldMapper;
use Madbox99\FilamentFormBuilder\Models\RegistrationForm;
use Madbox99\FilamentFormBuilder\Support\FormFieldBlueprint;

function formWithFields(array $fields): RegistrationForm
{
    return RegistrationForm::factory()->make(['fields' => $fields]);
}

function field(string $type, string $name, string $label): array
{
    return ['type' => $type, 'data' => ['label' => $label, 'name' => $name, 'required' => false]];
}

it('maps by field type heuristically', function (): void {
    $form = formWithFields([
        field(FormFieldBlueprint::TYPE_TEXT, 'full_name', 'Full name'),
        field(FormFieldBlueprint::TYPE_EMAIL, 'email', 'Email'),
        field(FormFieldBlueprint::TYPE_PHONE, 'tel', 'Phone'),
        field(FormFieldBlueprint::TYPE_TEXTAREA, 'msg', 'Message'),
    ]);

    $data = ['full_name' => 'Kiss Anna', 'email' => 'ANNA@Example.com', 'tel' => '+36 30 111 2222', 'msg' => 'Hello'];

    $mapped = (new SubmissionFieldMapper())->map($form, $data);

    expect($mapped->email)->toBe('anna@example.com')
        ->and($mapped->name)->toBe('Kiss Anna')
        ->and($mapped->phone)->toBe('+36 30 111 2222')
        ->and($mapped->hasEmail())->toBeTrue()
        ->and($mapped->notes)->toContain('Message: Hello');
});

it('falls back to key patterns when no typed field exists', function (): void {
    $form = formWithFields([
        field(FormFieldBlueprint::TYPE_TEXT, 'e_mail', 'E-mail'),
        field(FormFieldBlueprint::TYPE_TEXT, 'telefon', 'Telefon'),
    ]);

    $mapped = (new SubmissionFieldMapper())->map($form, ['e_mail' => 'x@y.hu', 'telefon' => '123']);

    expect($mapped->email)->toBe('x@y.hu')
        ->and($mapped->phone)->toBe('123');
});

it('honours explicit field_map overrides', function (): void {
    $form = formWithFields([
        field(FormFieldBlueprint::TYPE_EMAIL, 'email', 'Email'),
        field(FormFieldBlueprint::TYPE_TEXT, 'contact', 'Contact'),
    ]);
    $settings = new FormCrmSetting(['field_map' => ['name' => 'contact']]);

    $mapped = (new SubmissionFieldMapper())->map($form, ['email' => 'a@b.hu', 'contact' => 'Nagy Béla'], $settings);

    expect($mapped->name)->toBe('Nagy Béla');
});

it('extracts utm and returns null email when absent', function (): void {
    $form = formWithFields([field(FormFieldBlueprint::TYPE_TEXT, 'name', 'Name')]);

    $mapped = (new SubmissionFieldMapper())->map($form, [
        'name' => 'X', 'utm_source' => 'google', 'utm_campaign' => 'summer', 'referrer' => 'https://g.com',
    ]);

    expect($mapped->email)->toBeNull()
        ->and($mapped->hasEmail())->toBeFalse()
        ->and($mapped->utm)->toBe(['utm_source' => 'google', 'utm_campaign' => 'summer'])
        ->and($mapped->referrer)->toBe('https://g.com');
});
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `php artisan test --compact --filter=SubmissionFieldMapperTest`
Expected: FAIL (classes not defined).

- [ ] **Step 3: Create the DTO**

`app/Services/SubmissionData.php`:
```php
<?php

declare(strict_types=1);

namespace App\Services;

final readonly class SubmissionData
{
    /**
     * @param  array<string, string>  $utm
     * @param  array<string, mixed>  $raw
     */
    public function __construct(
        public ?string $email,
        public ?string $name,
        public ?string $phone,
        public ?string $companyName,
        public string $notes,
        public array $utm,
        public ?string $referrer,
        public array $raw,
    ) {}

    public function hasEmail(): bool
    {
        return $this->email !== null && $this->email !== '';
    }
}
```

- [ ] **Step 4: Create the mapper**

`app/Services/SubmissionFieldMapper.php`:
```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\FormCrmSetting;
use Madbox99\FilamentFormBuilder\Models\RegistrationForm;
use Madbox99\FilamentFormBuilder\Support\FormFieldBlueprint;

final class SubmissionFieldMapper
{
    private const array UTM_KEYS = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];

    /**
     * @param  array<string, mixed>  $data
     */
    public function map(RegistrationForm $form, array $data, ?FormCrmSetting $settings = null): SubmissionData
    {
        /** @var list<FormFieldBlueprint> $blueprints */
        $blueprints = FormFieldBlueprint::fromForm($form);
        $map = $settings?->field_map ?? [];

        $email = $this->normaliseEmail(
            $this->resolve($data, $blueprints, $map, 'email', [FormFieldBlueprint::TYPE_EMAIL], '/(e[-_ ]?mail)/i')
        );
        $phone = $this->resolve($data, $blueprints, $map, 'phone', [FormFieldBlueprint::TYPE_PHONE], '/(phone|tel|telefon)/i');
        $name = $this->resolve($data, $blueprints, $map, 'name', [FormFieldBlueprint::TYPE_TEXT], '/(name|nev|név|teljes)/iu');
        $company = $this->resolve($data, $blueprints, $map, 'companyName', [], '/(company|cég|ceg|vállalkoz|vallalkoz)/iu');

        return new SubmissionData(
            email: $email,
            name: $name,
            phone: $phone,
            companyName: $company,
            notes: $this->buildNotes($blueprints, $data),
            utm: $this->extractUtm($data),
            referrer: isset($data['referrer']) && is_scalar($data['referrer']) ? (string) $data['referrer'] : null,
            raw: $data,
        );
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<FormFieldBlueprint>  $blueprints
     * @param  array<string, string>  $map
     * @param  list<string>  $types
     */
    private function resolve(array $data, array $blueprints, array $map, string $crmField, array $types, string $keyPattern): ?string
    {
        if (isset($map[$crmField]) && array_key_exists($map[$crmField], $data)) {
            return $this->stringValue($data[$map[$crmField]]);
        }

        foreach ($blueprints as $blueprint) {
            if (in_array($blueprint->type, $types, true) && array_key_exists($blueprint->key, $data)) {
                return $this->stringValue($data[$blueprint->key]);
            }
        }

        foreach ($blueprints as $blueprint) {
            if (preg_match($keyPattern, $blueprint->key) === 1 && array_key_exists($blueprint->key, $data)) {
                return $this->stringValue($data[$blueprint->key]);
            }
        }

        return null;
    }

    /**
     * @param  list<FormFieldBlueprint>  $blueprints
     * @param  array<string, mixed>  $data
     */
    private function buildNotes(array $blueprints, array $data): string
    {
        $lines = [];

        foreach ($blueprints as $blueprint) {
            if (in_array($blueprint->key, self::UTM_KEYS, true) || $blueprint->key === 'referrer') {
                continue;
            }
            $value = $this->stringValue($data[$blueprint->key] ?? null);
            if ($value === null || $value === '') {
                continue;
            }
            $lines[] = $blueprint->label.': '.$value;
        }

        return implode("\n", $lines);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, string>
     */
    private function extractUtm(array $data): array
    {
        $utm = [];
        foreach (self::UTM_KEYS as $key) {
            if (isset($data[$key]) && is_scalar($data[$key]) && (string) $data[$key] !== '') {
                $utm[$key] = (string) $data[$key];
            }
        }

        return $utm;
    }

    private function stringValue(mixed $value): ?string
    {
        if (is_array($value)) {
            return implode(', ', array_map(static fn (mixed $v): string => is_scalar($v) ? (string) $v : '', $value));
        }
        if (is_scalar($value)) {
            $trimmed = trim((string) $value);

            return $trimmed === '' ? null : $trimmed;
        }

        return null;
    }

    private function normaliseEmail(?string $email): ?string
    {
        if ($email === null) {
            return null;
        }
        $normalised = mb_strtolower(trim($email));

        return $normalised === '' ? null : $normalised;
    }
}
```

- [ ] **Step 5: Run tests to verify they pass**

Run: `php artisan test --compact --filter=SubmissionFieldMapperTest`
Expected: PASS (all 4).

- [ ] **Step 6: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Services/SubmissionData.php app/Services/SubmissionFieldMapper.php tests/Unit/SubmissionFieldMapperTest.php
git commit -m "feat: add submission field mapper"
```

---

## Task 4: `ProcessFormSubmissionToCrm` listener (CRM rekordok)

**Files:**
- Create: `app/Listeners/ProcessFormSubmissionToCrm.php`
- Test: `tests/Feature/ProcessFormSubmissionToCrmTest.php`

**Interfaces:**
- Consumes: `Madbox99\FilamentFormBuilder\Events\FormSubmissionProcessed` (`->form:RegistrationForm`, `->submission:?FormSubmission`, `->formData:array`, `->actions:SubmissionActions`); `SubmissionFieldMapper` (Task 3); `App\Services\LeadScoringService::calculateForCustomer(Customer, Team): LeadScore`; `App\Models\FormCrmSetting` (Task 2); `App\Enums\InteractionType::FormSubmission` (Task 1); `App\Enums\OpportunityStage::Lead`.
- Produces: `App\Listeners\ProcessFormSubmissionToCrm` (`final`, `implements ShouldQueue`), `handle(FormSubmissionProcessed $event): void`. Auto-discovery regisztrálja.

> Megjegyzés: az értesítés (Mail) NEM része ennek a tasknak — azt Task 5 fűzi be. Ez a task csak a CRM-rekordokat hozza létre.

- [ ] **Step 1: Write the failing tests**

`tests/Feature/ProcessFormSubmissionToCrmTest.php`:
```php
<?php

declare(strict_types=1);

use App\Enums\InteractionType;
use App\Enums\OpportunityStage;
use App\Listeners\ProcessFormSubmissionToCrm;
use App\Models\Campaign;
use App\Models\Customer;
use App\Models\FormCrmSetting;
use App\Models\Interaction;
use App\Models\LeadScore;
use App\Models\Opportunity;
use App\Models\Team;
use Madbox99\FilamentFormBuilder\Events\FormSubmissionProcessed;
use Madbox99\FilamentFormBuilder\Models\FormSubmission;
use Madbox99\FilamentFormBuilder\Models\RegistrationForm;
use Madbox99\FilamentFormBuilder\Support\FormFieldBlueprint;
use Madbox99\FilamentFormBuilder\ValueObjects\SubmissionActions;

function leadForm(Team $team): RegistrationForm
{
    return RegistrationForm::factory()->create([
        'team_id' => $team->id,
        'fields' => [
            ['type' => FormFieldBlueprint::TYPE_TEXT, 'data' => ['label' => 'Name', 'name' => 'name', 'required' => true]],
            ['type' => FormFieldBlueprint::TYPE_EMAIL, 'data' => ['label' => 'Email', 'name' => 'email', 'required' => true]],
            ['type' => FormFieldBlueprint::TYPE_PHONE, 'data' => ['label' => 'Phone', 'name' => 'phone', 'required' => false]],
        ],
    ]);
}

/**
 * @param  array<string, mixed>  $data
 */
function fireSubmission(RegistrationForm $form, Team $team, array $data, ?SubmissionActions $actions = null): FormSubmission
{
    $submission = FormSubmission::factory()->create([
        'registration_form_id' => $form->id,
        'team_id' => $team->id,
        'data' => $data,
    ]);

    app(ProcessFormSubmissionToCrm::class)->handle(new FormSubmissionProcessed(
        $form,
        $submission,
        $data,
        $actions ?? new SubmissionActions(),
    ));

    return $submission->refresh();
}

it('creates customer, opportunity and interaction from a submission with email', function (): void {
    $team = Team::factory()->create();
    $form = leadForm($team);

    $submission = fireSubmission($form, $team, ['name' => 'Kiss Anna', 'email' => 'anna@example.com', 'phone' => '+3630']);

    $customer = Customer::query()->where('team_id', $team->id)->where('email', 'anna@example.com')->first();

    expect($customer)->not->toBeNull()
        ->and($customer->name)->toBe('Kiss Anna')
        ->and($customer->phone)->toBe('+3630')
        ->and($submission->lead_id)->toBe($customer->id);

    expect(Opportunity::query()->where('customer_id', $customer->id)->where('stage', OpportunityStage::Lead)->count())->toBe(1);
    expect(Interaction::query()->where('customer_id', $customer->id)->where('type', InteractionType::FormSubmission)->count())->toBe(1);
});

it('deduplicates the customer by email but adds a new opportunity and interaction', function (): void {
    $team = Team::factory()->create();
    $form = leadForm($team);

    fireSubmission($form, $team, ['name' => 'Anna', 'email' => 'anna@example.com']);
    fireSubmission($form, $team, ['name' => 'Anna', 'email' => 'anna@example.com']);

    expect(Customer::query()->where('team_id', $team->id)->where('email', 'anna@example.com')->count())->toBe(1);

    $customer = Customer::query()->where('email', 'anna@example.com')->first();
    expect(Opportunity::query()->where('customer_id', $customer->id)->count())->toBe(2);
    expect(Interaction::query()->where('customer_id', $customer->id)->count())->toBe(2);
});

it('skips crm records when there is no email', function (): void {
    $team = Team::factory()->create();
    $form = leadForm($team);

    $submission = fireSubmission($form, $team, ['name' => 'No Email']);

    expect(Customer::query()->where('team_id', $team->id)->count())->toBe(0)
        ->and($submission->lead_id)->toBeNull();
});

it('respects createLeadIfHasEmail = false', function (): void {
    $team = Team::factory()->create();
    $form = leadForm($team);

    fireSubmission($form, $team, ['name' => 'Anna', 'email' => 'anna@example.com'], new SubmissionActions(createLeadIfHasEmail: false));

    expect(Customer::query()->where('team_id', $team->id)->count())->toBe(0);
});

it('never mixes data across teams in queue context', function (): void {
    $teamA = Team::factory()->create();
    $teamB = Team::factory()->create();
    Customer::factory()->create(['team_id' => $teamB->id, 'email' => 'anna@example.com']);
    $form = leadForm($teamA);

    fireSubmission($form, $teamA, ['name' => 'Anna', 'email' => 'anna@example.com']);

    expect(Customer::query()->where('team_id', $teamA->id)->where('email', 'anna@example.com')->count())->toBe(1)
        ->and(Customer::query()->where('team_id', $teamB->id)->where('email', 'anna@example.com')->count())->toBe(1);
});

it('links the opportunity to a fixed campaign from settings', function (): void {
    $team = Team::factory()->create();
    $form = leadForm($team);
    $campaign = Campaign::factory()->create(['team_id' => $team->id]);
    FormCrmSetting::query()->create([
        'team_id' => $team->id,
        'registration_form_id' => $form->id,
        'campaign_id' => $campaign->id,
    ]);

    fireSubmission($form, $team, ['name' => 'Anna', 'email' => 'anna@example.com']);

    $customer = Customer::query()->where('email', 'anna@example.com')->first();
    expect(Opportunity::query()->where('customer_id', $customer->id)->value('campaign_id'))->toBe($campaign->id);
});

it('links the opportunity to a campaign matched by utm_campaign', function (): void {
    $team = Team::factory()->create();
    $form = leadForm($team);
    $campaign = Campaign::factory()->create(['team_id' => $team->id, 'name' => 'Summer']);

    fireSubmission($form, $team, ['name' => 'Anna', 'email' => 'anna@example.com', 'utm_campaign' => 'summer']);

    $customer = Customer::query()->where('email', 'anna@example.com')->first();
    expect(Opportunity::query()->where('customer_id', $customer->id)->value('campaign_id'))->toBe($campaign->id);
});

it('recalculates the lead score for the customer', function (): void {
    $team = Team::factory()->create();
    $form = leadForm($team);

    fireSubmission($form, $team, ['name' => 'Anna', 'email' => 'anna@example.com']);

    $customer = Customer::query()->where('email', 'anna@example.com')->first();
    expect(LeadScore::query()->where('team_id', $team->id)->where('customer_id', $customer->id)->exists())->toBeTrue();
});
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `php artisan test --compact --filter=ProcessFormSubmissionToCrmTest`
Expected: FAIL (listener not defined).

- [ ] **Step 3: Create the listener**

`app/Listeners/ProcessFormSubmissionToCrm.php`:
```php
<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\InteractionType;
use App\Enums\OpportunityStage;
use App\Models\Campaign;
use App\Models\Customer;
use App\Models\FormCrmSetting;
use App\Models\Interaction;
use App\Models\Opportunity;
use App\Models\Team;
use App\Services\LeadScoringService;
use App\Services\SubmissionData;
use App\Services\SubmissionFieldMapper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Madbox99\FilamentFormBuilder\Events\FormSubmissionProcessed;
use Madbox99\FilamentFormBuilder\Models\FormSubmission;
use Madbox99\FilamentFormBuilder\Models\RegistrationForm;

final class ProcessFormSubmissionToCrm implements ShouldQueue
{
    public function handle(FormSubmissionProcessed $event): void
    {
        $form = $event->form;
        $submission = $event->submission;
        $actions = $event->actions;

        $teamId = $submission?->team_id ?? $form->getAttribute('team_id');

        $settings = $teamId !== null
            ? FormCrmSetting::query()
                ->where('team_id', $teamId)
                ->where('registration_form_id', $form->id)
                ->first()
            : null;

        $mapped = app(SubmissionFieldMapper::class)->map($form, $event->formData, $settings);

        if ($submission === null || $teamId === null || ! $mapped->hasEmail() || ! $actions->createLeadIfHasEmail) {
            return;
        }

        $customer = DB::transaction(function () use ($teamId, $form, $submission, $mapped, $settings): Customer {
            $customer = $this->resolveCustomer($teamId, $mapped);

            if ($settings?->create_opportunity ?? true) {
                $this->createOpportunity($teamId, $customer, $form, $submission, $mapped, $settings);
            }

            $this->logInteraction($teamId, $customer, $form, $submission, $mapped);

            $submission->update(['lead_id' => $customer->id]);

            return $customer;
        });

        if (($settings?->enable_scoring ?? true) && ($team = Team::query()->find($teamId)) instanceof Team) {
            app(LeadScoringService::class)->calculateForCustomer($customer, $team);
        }
    }

    private function resolveCustomer(int $teamId, SubmissionData $data): Customer
    {
        $customer = Customer::query()
            ->where('team_id', $teamId)
            ->where('email', $data->email)
            ->first();

        if (! $customer instanceof Customer) {
            return Customer::query()->create([
                'team_id' => $teamId,
                'email' => $data->email,
                'name' => $data->companyName ?? $data->name ?? $data->email,
                'phone' => $data->phone,
                'is_active' => true,
            ]);
        }

        if (($customer->phone === null || $customer->phone === '') && $data->phone !== null) {
            $customer->update(['phone' => $data->phone]);
        }

        return $customer;
    }

    private function createOpportunity(int $teamId, Customer $customer, RegistrationForm $form, FormSubmission $submission, SubmissionData $data, ?FormCrmSetting $settings): void
    {
        $stage = $settings?->opportunity_stage !== null
            ? OpportunityStage::from($settings->opportunity_stage)
            : OpportunityStage::Lead;

        Opportunity::query()->create([
            'team_id' => $teamId,
            'customer_id' => $customer->id,
            'campaign_id' => $this->resolveCampaignId($teamId, $data, $settings),
            'title' => $form->name.' – '.$submission->created_at->format('Y-m-d H:i'),
            'description' => $this->descriptionWithUtm($data),
            'stage' => $stage,
        ]);
    }

    private function logInteraction(int $teamId, Customer $customer, RegistrationForm $form, FormSubmission $submission, SubmissionData $data): void
    {
        Interaction::query()->create([
            'team_id' => $teamId,
            'customer_id' => $customer->id,
            'user_id' => null,
            'type' => InteractionType::FormSubmission,
            'subject' => $form->name,
            'description' => $this->descriptionWithUtm($data),
            'interaction_date' => $submission->created_at,
        ]);
    }

    private function resolveCampaignId(int $teamId, SubmissionData $data, ?FormCrmSetting $settings): ?int
    {
        if ($settings?->campaign_id !== null) {
            return $settings->campaign_id;
        }

        $utmCampaign = $data->utm['utm_campaign'] ?? null;
        if ($utmCampaign === null || $utmCampaign === '') {
            return null;
        }

        $id = Campaign::query()
            ->where('team_id', $teamId)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($utmCampaign)])
            ->value('id');

        return $id !== null ? (int) $id : null;
    }

    private function descriptionWithUtm(SubmissionData $data): string
    {
        $description = $data->notes;

        if ($data->utm !== []) {
            $utm = collect($data->utm)
                ->map(static fn (string $value, string $key): string => $key.': '.$value)
                ->implode(', ');
            $description .= "\n\nUTM: ".$utm;
        }

        if ($data->referrer !== null) {
            $description .= "\nReferrer: ".$data->referrer;
        }

        return $description;
    }
}
```

- [ ] **Step 4: Run tests to verify they pass**

Run: `php artisan test --compact --filter=ProcessFormSubmissionToCrmTest`
Expected: PASS (all 8).

- [ ] **Step 5: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Listeners/ProcessFormSubmissionToCrm.php tests/Feature/ProcessFormSubmissionToCrmTest.php
git commit -m "feat: process form submissions into CRM records"
```

---

## Task 5: `NewFormSubmissionMail` értesítő + bekötés a listenerbe

**Files:**
- Create: `app/Mail/NewFormSubmissionMail.php`
- Create: `resources/views/emails/form-submission.blade.php`
- Modify: `app/Listeners/ProcessFormSubmissionToCrm.php`
- Test: `tests/Feature/ProcessFormSubmissionToCrmTest.php` (add cases)

**Interfaces:**
- Consumes: `App\Services\SubmissionData`; `Madbox99\FilamentFormBuilder\Models\{RegistrationForm, FormSubmission}`; `App\Models\Customer`.
- Produces: `App\Mail\NewFormSubmissionMail` (`final`, `implements ShouldQueue`), konstruktor `(RegistrationForm $form, FormSubmission $submission, SubmissionData $data, ?Customer $customer)`.

- [ ] **Step 1: Write the failing tests (append to the file)**

Add to top of `tests/Feature/ProcessFormSubmissionToCrmTest.php` after existing `use` lines:
```php
use App\Mail\NewFormSubmissionMail;
use Illuminate\Support\Facades\Mail;
```
Append these tests:
```php
it('sends a notification to the configured emails', function (): void {
    Mail::fake();
    $team = Team::factory()->create();
    $form = leadForm($team);

    fireSubmission($form, $team, ['name' => 'Anna', 'email' => 'anna@example.com'], new SubmissionActions(notifyEmails: ['sales@acme.hu']));

    Mail::assertSent(NewFormSubmissionMail::class, fn (NewFormSubmissionMail $mail): bool => $mail->hasTo('sales@acme.hu'));
});

it('does not send a notification when no emails are configured', function (): void {
    Mail::fake();
    $team = Team::factory()->create();
    $form = leadForm($team);

    fireSubmission($form, $team, ['name' => 'Anna', 'email' => 'anna@example.com']);

    Mail::assertNothingSent();
});

it('still notifies when there is no email but notifyEmails are set', function (): void {
    Mail::fake();
    $team = Team::factory()->create();
    $form = leadForm($team);

    fireSubmission($form, $team, ['name' => 'No Email'], new SubmissionActions(notifyEmails: ['sales@acme.hu']));

    Mail::assertSent(NewFormSubmissionMail::class);
});
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `php artisan test --compact --filter=ProcessFormSubmissionToCrmTest`
Expected: FAIL (`NewFormSubmissionMail` not defined / nothing sent).

- [ ] **Step 3: Create the mailable**

`app/Mail/NewFormSubmissionMail.php`:
```php
<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Customer;
use App\Services\SubmissionData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Madbox99\FilamentFormBuilder\Models\FormSubmission;
use Madbox99\FilamentFormBuilder\Models\RegistrationForm;

final class NewFormSubmissionMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public RegistrationForm $form,
        public FormSubmission $submission,
        public SubmissionData $data,
        public ?Customer $customer,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('New form submission').': '.$this->form->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.form-submission',
        );
    }

    /**
     * @return array<int, mixed>
     */
    public function attachments(): array
    {
        return [];
    }
}
```

- [ ] **Step 4: Create the Markdown view**

`resources/views/emails/form-submission.blade.php`:
```blade
<x-mail::message>
# {{ __('New form submission') }}

**{{ __('Form') }}:** {{ $form->name }}

@if ($data->email)
**{{ __('Email') }}:** {{ $data->email }}
@endif
@if ($data->name)
**{{ __('Name') }}:** {{ $data->name }}
@endif
@if ($data->phone)
**{{ __('Phone') }}:** {{ $data->phone }}
@endif

@if ($data->notes !== '')
---
{!! nl2br(e($data->notes)) !!}
@endif

@if ($customer)
<x-mail::button :url="url('/')">
{{ __('Open in CRM') }}
</x-mail::button>
@endif

{{ config('app.name') }}
</x-mail::message>
```

- [ ] **Step 5: Wire the notification into the listener**

In `app/Listeners/ProcessFormSubmissionToCrm.php` add imports:
```php
use App\Mail\NewFormSubmissionMail;
use Illuminate\Support\Facades\Mail;
use Madbox99\FilamentFormBuilder\ValueObjects\SubmissionActions;
```
Replace the early-return block and end of `handle()` so notification runs on BOTH paths. Change:
```php
        if ($submission === null || $teamId === null || ! $mapped->hasEmail() || ! $actions->createLeadIfHasEmail) {
            return;
        }
```
to:
```php
        if ($submission === null || $teamId === null || ! $mapped->hasEmail() || ! $actions->createLeadIfHasEmail) {
            $this->notify($actions, $form, $submission, $mapped, null);

            return;
        }
```
Then after the scoring block (end of `handle()`), append:
```php
        $this->notify($actions, $form, $submission, $mapped, $customer);
```
Add this private method to the class:
```php
    private function notify(SubmissionActions $actions, RegistrationForm $form, ?FormSubmission $submission, SubmissionData $data, ?Customer $customer): void
    {
        if ($actions->notifyEmails === [] || $submission === null) {
            return;
        }

        Mail::to($actions->notifyEmails)->send(new NewFormSubmissionMail($form, $submission, $data, $customer));
    }
```

- [ ] **Step 6: Run tests to verify they pass**

Run: `php artisan test --compact --filter=ProcessFormSubmissionToCrmTest`
Expected: PASS (all 11).

- [ ] **Step 7: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Mail/NewFormSubmissionMail.php resources/views/emails/form-submission.blade.php app/Listeners/ProcessFormSubmissionToCrm.php tests/Feature/ProcessFormSubmissionToCrmTest.php
git commit -m "feat: notify team on new form submissions"
```

---

## Task 6: Mérési service + dashboard widgetek

**Files:**
- Create: `app/Services/FormSubmissionMetricsService.php`
- Create: `app/Filament/Widgets/FormSubmissionStatsWidget.php`
- Create: `app/Filament/Widgets/FormSubmissionTrendWidget.php`
- Create: `app/Filament/Widgets/FormSubmissionsByFormWidget.php`
- Modify: `app/Filament/Pages/SalesReports.php`
- Test: `tests/Feature/FormSubmissionMetricsTest.php`

**Interfaces:**
- Produces: `App\Services\FormSubmissionMetricsService`:
  - `stats(int $teamId): array{today:int, week:int, total:int, converted:int, conversion_rate:float}`
  - `dailyTrend(int $teamId, int $days = 30): array{labels: list<string>, values: list<int>}`
  - `byForm(int $teamId): array{labels: list<string>, values: list<int>}`

- [ ] **Step 1: Write the failing test**

`tests/Feature/FormSubmissionMetricsTest.php`:
```php
<?php

declare(strict_types=1);

use App\Services\FormSubmissionMetricsService;
use App\Models\Team;
use Madbox99\FilamentFormBuilder\Models\FormSubmission;
use Madbox99\FilamentFormBuilder\Models\RegistrationForm;

it('aggregates submission stats scoped to the team', function (): void {
    $team = Team::factory()->create();
    $other = Team::factory()->create();
    $form = RegistrationForm::factory()->create(['team_id' => $team->id]);

    FormSubmission::factory()->count(3)->create(['registration_form_id' => $form->id, 'team_id' => $team->id, 'lead_id' => null]);
    FormSubmission::factory()->create(['registration_form_id' => $form->id, 'team_id' => $team->id, 'lead_id' => 999]);
    FormSubmission::factory()->create(['registration_form_id' => $form->id, 'team_id' => $other->id]);

    $stats = (new FormSubmissionMetricsService())->stats($team->id);

    expect($stats['total'])->toBe(4)
        ->and($stats['converted'])->toBe(1)
        ->and($stats['conversion_rate'])->toBe(25.0);
});

it('builds a per-form breakdown', function (): void {
    $team = Team::factory()->create();
    $formA = RegistrationForm::factory()->create(['team_id' => $team->id, 'name' => 'Alpha']);
    $formB = RegistrationForm::factory()->create(['team_id' => $team->id, 'name' => 'Beta']);
    FormSubmission::factory()->count(2)->create(['registration_form_id' => $formA->id, 'team_id' => $team->id]);
    FormSubmission::factory()->create(['registration_form_id' => $formB->id, 'team_id' => $team->id]);

    $byForm = (new FormSubmissionMetricsService())->byForm($team->id);

    expect($byForm['labels'])->toContain('Alpha', 'Beta')
        ->and(array_sum($byForm['values']))->toBe(3);
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=FormSubmissionMetricsTest`
Expected: FAIL (service not defined).

- [ ] **Step 3: Create the metrics service**

`app/Services/FormSubmissionMetricsService.php`:
```php
<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Carbon;
use Madbox99\FilamentFormBuilder\Models\FormSubmission;

final class FormSubmissionMetricsService
{
    /**
     * @return array{today: int, week: int, total: int, converted: int, conversion_rate: float}
     */
    public function stats(int $teamId): array
    {
        $base = FormSubmission::query()->where('team_id', $teamId);

        $total = (clone $base)->count();
        $today = (clone $base)->whereDate('created_at', Carbon::today())->count();
        $week = (clone $base)->where('created_at', '>=', Carbon::now()->subDays(7))->count();
        $converted = (clone $base)->whereNotNull('lead_id')->count();

        return [
            'today' => $today,
            'week' => $week,
            'total' => $total,
            'converted' => $converted,
            'conversion_rate' => $total > 0 ? round($converted / $total * 100, 1) : 0.0,
        ];
    }

    /**
     * @return array{labels: list<string>, values: list<int>}
     */
    public function dailyTrend(int $teamId, int $days = 30): array
    {
        $labels = [];
        $values = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('m-d');
            $values[] = FormSubmission::query()
                ->where('team_id', $teamId)
                ->whereDate('created_at', $date)
                ->count();
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * @return array{labels: list<string>, values: list<int>}
     */
    public function byForm(int $teamId): array
    {
        $rows = FormSubmission::query()
            ->selectRaw('registration_form_id, COUNT(*) as aggregate')
            ->where('team_id', $teamId)
            ->groupBy('registration_form_id')
            ->with('registrationForm:id,name')
            ->get();

        $labels = [];
        $values = [];

        foreach ($rows as $row) {
            $labels[] = (string) ($row->registrationForm?->name ?? '#'.$row->registration_form_id);
            $values[] = (int) $row->aggregate;
        }

        return ['labels' => $labels, 'values' => $values];
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter=FormSubmissionMetricsTest`
Expected: PASS.

- [ ] **Step 5: Create the stats widget**

`app/Filament/Widgets/FormSubmissionStatsWidget.php`:
```php
<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Team;
use App\Services\FormSubmissionMetricsService;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Override;

final class FormSubmissionStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 7;

    protected ?string $pollingInterval = '60s';

    #[Override]
    protected function getStats(): array
    {
        $tenant = Filament::getTenant();
        if (! $tenant instanceof Team) {
            return [];
        }

        $stats = resolve(FormSubmissionMetricsService::class)->stats($tenant->id);

        return [
            Stat::make(__('Submissions today'), (string) $stats['today'])
                ->icon('heroicon-o-inbox-arrow-down')
                ->color('info'),
            Stat::make(__('Submissions this week'), (string) $stats['week'])
                ->icon('heroicon-o-calendar-days')
                ->color('gray'),
            Stat::make(__('Lead conversion'), $stats['conversion_rate'].'%')
                ->description($stats['converted'].' / '.$stats['total'])
                ->icon('heroicon-o-user-plus')
                ->color('success'),
        ];
    }
}
```

- [ ] **Step 6: Create the trend widget**

`app/Filament/Widgets/FormSubmissionTrendWidget.php`:
```php
<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Team;
use App\Services\FormSubmissionMetricsService;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Override;

final class FormSubmissionTrendWidget extends ChartWidget
{
    protected static ?int $sort = 8;

    protected ?string $pollingInterval = '60s';

    #[Override]
    public function getHeading(): string
    {
        return __('Form submissions (30 days)');
    }

    #[Override]
    protected function getData(): array
    {
        $tenant = Filament::getTenant();
        if (! $tenant instanceof Team) {
            return ['datasets' => [], 'labels' => []];
        }

        $trend = resolve(FormSubmissionMetricsService::class)->dailyTrend($tenant->id);

        return [
            'datasets' => [
                [
                    'label' => __('Submissions'),
                    'data' => $trend['values'],
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'fill' => true,
                ],
            ],
            'labels' => $trend['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
```

- [ ] **Step 7: Create the by-form widget**

`app/Filament/Widgets/FormSubmissionsByFormWidget.php`:
```php
<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Team;
use App\Services\FormSubmissionMetricsService;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Override;

final class FormSubmissionsByFormWidget extends ChartWidget
{
    protected static ?int $sort = 9;

    protected ?string $pollingInterval = '60s';

    #[Override]
    public function getHeading(): string
    {
        return __('Submissions by form');
    }

    #[Override]
    protected function getData(): array
    {
        $tenant = Filament::getTenant();
        if (! $tenant instanceof Team) {
            return ['datasets' => [], 'labels' => []];
        }

        $byForm = resolve(FormSubmissionMetricsService::class)->byForm($tenant->id);

        return [
            'datasets' => [
                [
                    'data' => $byForm['values'],
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(234, 179, 8, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                    ],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $byForm['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
```

- [ ] **Step 8: Register widgets on the reports page**

`app/Filament/Pages/SalesReports.php` — add imports and append to `getFooterWidgets()`:
```php
use App\Filament\Widgets\FormSubmissionStatsWidget;
use App\Filament\Widgets\FormSubmissionTrendWidget;
use App\Filament\Widgets\FormSubmissionsByFormWidget;
```
In `getFooterWidgets()` return array, after `TopCustomersWidget::class,` add:
```php
            FormSubmissionStatsWidget::class,
            FormSubmissionTrendWidget::class,
            FormSubmissionsByFormWidget::class,
```

- [ ] **Step 9: Run tests + verify page renders**

Run: `php artisan test --compact --filter=FormSubmissionMetricsTest`
Expected: PASS.

- [ ] **Step 10: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Services/FormSubmissionMetricsService.php app/Filament/Widgets/FormSubmission*.php app/Filament/Pages/SalesReports.php tests/Feature/FormSubmissionMetricsTest.php
git commit -m "feat: add form submission dashboard widgets"
```

---

## Task 7: `FormCrmSettingResource` (Filament UI)

**Files:**
- Create: `app/Filament/Resources/FormCrmSettings/FormCrmSettingResource.php`
- Create: `app/Filament/Resources/FormCrmSettings/Schemas/FormCrmSettingForm.php`
- Create: `app/Filament/Resources/FormCrmSettings/Tables/FormCrmSettingsTable.php`
- Create: `app/Filament/Resources/FormCrmSettings/Pages/ListFormCrmSettings.php`
- Create: `app/Filament/Resources/FormCrmSettings/Pages/CreateFormCrmSetting.php`
- Create: `app/Filament/Resources/FormCrmSettings/Pages/EditFormCrmSetting.php`
- Test: `tests/Feature/FormCrmSettingResourceTest.php`

**Interfaces:**
- Consumes: `App\Models\FormCrmSetting` (Task 2); `App\Enums\OpportunityStage`.
- Produces: Filament resource a `form_crm_settings` szerkesztéséhez a tenant-panelen.

> Kövesd a meglévő resource-struktúrát. Ha bizonytalan a v5 API a `list-artisan-commands`/`search-docs` alapján, generáld a vázat: `php artisan make:filament-resource FormCrmSetting --model-namespace="App\Models" --no-interaction`, majd igazítsd az alábbiakhoz. A resource a tenant-panelen (`AdminPanelServiceProvider`) automatikusan regisztrálódik a `discoverResources` révén.

- [ ] **Step 1: Write the failing test**

`tests/Feature/FormCrmSettingResourceTest.php`:
```php
<?php

declare(strict_types=1);

use App\Filament\Resources\FormCrmSettings\Pages\CreateFormCrmSetting;
use App\Models\FormCrmSetting;
use App\Models\Team;
use App\Models\User;
use Madbox99\FilamentFormBuilder\Models\RegistrationForm;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('creates a crm setting through the panel', function (): void {
    $team = Team::factory()->create();
    $user = User::factory()->create();
    $user->teams()->attach($team);
    $form = RegistrationForm::factory()->create(['team_id' => $team->id]);

    actingAs($user);
    filament()->setTenant($team);

    livewire(CreateFormCrmSetting::class)
        ->fillForm([
            'registration_form_id' => $form->id,
            'create_opportunity' => true,
            'opportunity_stage' => 'lead',
            'enable_scoring' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(FormCrmSetting::query()->where('registration_form_id', $form->id)->exists())->toBeTrue();
});
```

> A `User`/team attach relációt a projekt konvenciója szerint igazítsd (l. meglévő Filament resource tesztek a `tests/Feature`-ben a tenant beállításához).

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=FormCrmSettingResourceTest`
Expected: FAIL (resource/pages not defined).

- [ ] **Step 3: Create the form schema**

`app/Filament/Resources/FormCrmSettings/Schemas/FormCrmSettingForm.php`:
```php
<?php

declare(strict_types=1);

namespace App\Filament\Resources\FormCrmSettings\Schemas;

use App\Enums\OpportunityStage;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

final class FormCrmSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('registration_form_id')
                ->label(__('Form'))
                ->relationship('registrationForm', 'name')
                ->required()
                ->unique(ignoreRecord: true)
                ->searchable(),
            KeyValue::make('field_map')
                ->label(__('Field mapping (CRM field → form field key)'))
                ->keyLabel(__('CRM field'))
                ->valueLabel(__('Form field key'))
                ->helperText(__('Leave empty to auto-detect. Keys: email, name, phone, companyName')),
            Toggle::make('create_opportunity')
                ->label(__('Create opportunity'))
                ->default(true),
            Select::make('opportunity_stage')
                ->label(__('Opportunity stage'))
                ->options(OpportunityStage::class)
                ->default(OpportunityStage::Lead->value),
            Select::make('campaign_id')
                ->label(__('Campaign'))
                ->relationship('campaign', 'name')
                ->searchable(),
            Toggle::make('enable_scoring')
                ->label(__('Enable lead scoring'))
                ->default(true),
        ]);
    }
}
```

- [ ] **Step 4: Create the table**

`app/Filament/Resources/FormCrmSettings/Tables/FormCrmSettingsTable.php`:
```php
<?php

declare(strict_types=1);

namespace App\Filament\Resources\FormCrmSettings\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class FormCrmSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('registrationForm.name')->label(__('Form'))->searchable(),
                IconColumn::make('create_opportunity')->label(__('Opportunity'))->boolean(),
                TextColumn::make('campaign.name')->label(__('Campaign'))->placeholder('—'),
                IconColumn::make('enable_scoring')->label(__('Scoring'))->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
```

- [ ] **Step 5: Create the resource**

`app/Filament/Resources/FormCrmSettings/FormCrmSettingResource.php`:
```php
<?php

declare(strict_types=1);

namespace App\Filament\Resources\FormCrmSettings;

use App\Filament\Resources\FormCrmSettings\Pages\CreateFormCrmSetting;
use App\Filament\Resources\FormCrmSettings\Pages\EditFormCrmSetting;
use App\Filament\Resources\FormCrmSettings\Pages\ListFormCrmSettings;
use App\Filament\Resources\FormCrmSettings\Schemas\FormCrmSettingForm;
use App\Filament\Resources\FormCrmSettings\Tables\FormCrmSettingsTable;
use App\Models\FormCrmSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

final class FormCrmSettingResource extends Resource
{
    protected static ?string $model = FormCrmSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'CRM';

    public static function form(Schema $schema): Schema
    {
        return FormCrmSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FormCrmSettingsTable::configure($table);
    }

    /**
     * @return array<string, class-string<\Filament\Resources\Pages\Page>>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListFormCrmSettings::route('/'),
            'create' => CreateFormCrmSetting::route('/create'),
            'edit' => EditFormCrmSetting::route('/{record}/edit'),
        ];
    }
}
```

> A `navigationGroup`/`navigationIcon` a projekt konvenciójához igazítandó (nézd meg egy meglévő resource fejlécét, pl. `app/Filament/Resources/Interactions/InteractionResource.php`).

- [ ] **Step 6: Create the pages**

`app/Filament/Resources/FormCrmSettings/Pages/ListFormCrmSettings.php`:
```php
<?php

declare(strict_types=1);

namespace App\Filament\Resources\FormCrmSettings\Pages;

use App\Filament\Resources\FormCrmSettings\FormCrmSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListFormCrmSettings extends ListRecords
{
    protected static string $resource = FormCrmSettingResource::class;

    /**
     * @return array<int, \Filament\Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
```
`app/Filament/Resources/FormCrmSettings/Pages/CreateFormCrmSetting.php`:
```php
<?php

declare(strict_types=1);

namespace App\Filament\Resources\FormCrmSettings\Pages;

use App\Filament\Resources\FormCrmSettings\FormCrmSettingResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateFormCrmSetting extends CreateRecord
{
    protected static string $resource = FormCrmSettingResource::class;
}
```
`app/Filament/Resources/FormCrmSettings/Pages/EditFormCrmSetting.php`:
```php
<?php

declare(strict_types=1);

namespace App\Filament\Resources\FormCrmSettings\Pages;

use App\Filament\Resources\FormCrmSettings\FormCrmSettingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

final class EditFormCrmSetting extends EditRecord
{
    protected static string $resource = FormCrmSettingResource::class;

    /**
     * @return array<int, \Filament\Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
```

- [ ] **Step 7: Run test to verify it passes**

Run: `php artisan test --compact --filter=FormCrmSettingResourceTest`
Expected: PASS.

- [ ] **Step 8: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Filament/Resources/FormCrmSettings tests/Feature/FormCrmSettingResourceTest.php
git commit -m "feat: add form CRM settings resource"
```

---

## Task 8: Teljes futtatás + záró ellenőrzés

**Files:** none (verifikáció)

- [ ] **Step 1: Run the full affected suite**

Run: `php artisan test --compact --filter="InteractionType|FormCrmSetting|SubmissionFieldMapper|ProcessFormSubmissionToCrm|FormSubmissionMetrics|FormCrmSettingResource"`
Expected: minden PASS.

- [ ] **Step 2: Pint the whole diff**

Run: `vendor/bin/pint --dirty --format agent`
Expected: nincs hátralévő formázási hiba.

- [ ] **Step 3: Végső ellenőrzés az élő panelen (kézi)**

- Beküldés egy publikus űrlapon → a Filament `FormSubmissions` táblában megjelenik, és létrejön a Customer/Opportunity/Interaction.
- A SalesReports oldalon a három új widget adatot mutat.
- A `FormCrmSetting` resource-ban beállítható a leképezés/kampány.

---

## Self-Review megjegyzések (a tervhez)

- **Spec-lefedettség:** feldolgozás (T4), mezőleképezés A+B (T3+T2/T7), Customer/Opportunity/Interaction (T4), lead-scoring (T4 a meglévő `LeadScoringService`-szel), kampányhoz kötés + UTM (T4), értesítés (T5), mérés (T6), enum (T1). Az UTM beküldésbe juttatása szándékosan kimarad (a beágyazó oldal felelőssége), a listener csak fogyasztja.
- **Eltérés a spectől:** a `form_crm_settings` tábláról elhagytam a `score_points` oszlopot (YAGNI) — a scoringot a `LeadScoringService::calculateForCustomer()` végzi az ügyfél interakciói/opportunity-jei alapján, extra pont nem szükséges.
- **Típuskonzisztencia:** `SubmissionData` mezőnevek (`companyName`, `utm`, `referrer`) végig egységesek a mapperben, listenerben és a mailable view-ban.
