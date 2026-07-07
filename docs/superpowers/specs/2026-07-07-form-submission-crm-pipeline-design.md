# Form beküldés → CRM pipeline — Design

- **Dátum:** 2026-07-07
- **Csomag:** `madbox-99/filament-form-builder` (host-integráció, a csomagot NEM módosítjuk)
- **Cél:** A form-builderrel készített és weboldalba ágyazott űrlapok minden beküldése automatikusan CRM-adattá váljon (feldolgozás), mérhető legyen (dashboard) és értesítést váltson ki (e-mail).

## 1. Kontextus / jelenlegi állapot

- Az űrlapokat a Filament adminban építjük (`RegistrationForm`), és JS-snippettel ágyazzuk be tetszőleges oldalra.
- Beküldéskor a csomag `Livewire\PublicRegistrationForm::submit()`:
  1. elmenti a `FormSubmission` rekordot (`data` JSON a mező-kulcsok szerint, `team_id`, `lead_id` nullable, `ip_address`, `user_agent`),
  2. növeli a `registration_forms.submissions_count` számlálót,
  3. eldob egy `Madbox99\FilamentFormBuilder\Events\FormSubmissionProcessed` eventet:
     `($form, ?$submission, array $formData, SubmissionActions $actions)`.
- A CRM-ben **jelenleg nincs listener** erre az eventre → a beküldések tárolódnak, de nem folynak be a lead-pipeline-ba, és nincs mérés a nyers táblán túl.
- `SubmissionActions` (formonként, a csomag adja): `createSubmission`, `createLeadIfHasEmail`, `notifyEmails[]`.

### Fontos: multi-tenant + queue

- Minden CRM-modell a `BelongsToTeam` traitet + `TeamScope` global scope-ot használja.
- A `TeamScope` **csak akkor szűr**, ha a `Team::CONTAINER_BINDING` be van kötve a containerbe (HTTP-kérés a tenant-panelen). **Queue-ban NINCS bekötve.**
- Következmény: a queue-olt listenerben **minden lekérdezést és rekord-létrehozást explicit `team_id`-vel kell végezni** (a `team_id`-t a `FormSubmission`/`RegistrationForm` adja, sosem a containerből).

## 2. Architektúra áttekintés

```
PublicRegistrationForm::submit()
  └── FormSubmissionProcessed event (a csomag dobja)
        └── App\Listeners\ProcessFormSubmissionToCrm  (ShouldQueue)
              ├── FormCrmSettings feloldás (form-onkénti config, ha van) ─┐
              ├── SubmissionFieldMapper::map($form, $data, $settings) ────┘ → SubmissionData DTO
              ├── (ha van email && createLeadIfHasEmail)
              │     ├── Customer firstOrCreate (team_id + email)
              │     ├── Opportunity create (stage: Lead, campaign_id?)
              │     ├── Interaction create (type: FormSubmission)
              │     ├── LeadScoringService::calculateForCustomer()  (lead-scoring)
              │     └── FormSubmission->update(['lead_id' => customer.id])
              └── Mail: NewFormSubmissionMail → settings/notifyEmails címekre

Dashboard: Filament widgetek a form_submissions táblán (team-scoped).
```

A vendor-csomagot nem módosítjuk; kizárólag az eventre csatlakozunk és app-oldali táblát/erőforrásokat adunk.

## 3. Komponensek

### 3.1 App-oldali beállítás tábla — `form_crm_settings`

Form-onkénti CRM-konfiguráció (explicit mezőleképezés = B-verzió, kampány, scoring). Külön migráció + `App\Models\FormCrmSetting` modell (`BelongsToTeam`).

Oszlopok:
- `id`
- `registration_form_id` (unique, FK → `registration_forms`, cascadeOnDelete)
- `team_id` (nullable, tenant kulcs — a listener explicit tölti)
- `field_map` json nullable — CRM-mező → form-mező-kulcs felülírás, pl. `{"email":"email_2","name":"full_name","phone":"tel"}`
- `create_opportunity` boolean default true
- `opportunity_stage` string default `'lead'` (`OpportunityStage` értékei)
- `campaign_id` nullable (FK → `campaigns`) — fix kampányhoz kötés
- `enable_scoring` boolean default true
- `score_points` unsigned int default 10 — beküldésenként adott engagement-pont
- `timestamps`

A rekord **opcionális**: ha nincs beállítás egy formhoz, minden a heurisztikus alapértelmezéssel megy (A-verzió).

### 3.2 Mezőleképezés — `App\Services\SubmissionFieldMapper`

Bemenet: `RegistrationForm $form`, `array $data`, `?FormCrmSetting $settings`.
Kimenet: `SubmissionData` DTO (`readonly`): `?string $email, ?string $name, ?string $phone, ?string $companyName, string $notes, array $utm, ?string $referrer, array $raw`.

Feloldási sorrend mezőnként:
1. **Explicit `field_map`** (settings) — ha az adott CRM-mezőre van megadva form-kulcs, azt használja.
2. **Heurisztika (A-verzió)**, a `RegistrationForm->fields` blueprintekből:
   - `email`: `email` típusú mező; fallback: kulcs illeszkedik `/(e[-_]?mail)/i`-re.
   - `phone`: `phone` típusú mező; fallback: kulcs `/(phone|tel|telefon)/i`.
   - `name`: első `text_input`; fallback: kulcs `/(name|nev|név|teljes)/i`.
   - `companyName`: kulcs `/(company|cég|ceg|vállalkoz)/i` (opcionális). Ha van, a `Customer.name` **ebből** lesz, és a személynév a `notes`-ba kerül; ha nincs, a `Customer.name` a `name` mezőből.
   - `notes`: `textarea` mezők + a fel nem használt mezők `"Címke: érték"` formában összefűzve (a blueprint címkéivel).
3. **UTM/referrer**: a `data` kulcsaiból `utm_source|utm_medium|utm_campaign|utm_term|utm_content` és `referrer`, ha jelen vannak (l. 3.7).

A DTO `email` értéke normalizálva (trim + lowercase); üres → `null`.

### 3.3 Ingestion listener — `App\Listeners\ProcessFormSubmissionToCrm implements ShouldQueue`

`final readonly`, konstruktoron át `SubmissionFieldMapper` és `LeadScoringService`. `handle(FormSubmissionProcessed $event)`:

1. `$submission` null (`createSubmission=false`) → csak értesítés (4. pont), majd return.
2. `$teamId = $submission->team_id ?? $form->team_id`. Ha nincs → log warning, return.
3. `$settings = FormCrmSetting::query()->where('team_id',$teamId)->where('registration_form_id',$form->id)->first();`
4. `$mapped = SubmissionFieldMapper::map($form, $formData, $settings);`
5. Ha `$mapped->email === null || ! $actions->createLeadIfHasEmail`:
   - értesítés kiküldése, return (nincs CRM-rekord).
6. **Customer** (email alapján dedup, explicit team_id):
   ```php
   $customer = Customer::query()
       ->where('team_id', $teamId)->where('email', $mapped->email)->first()
       ?? Customer::query()->create([
           'team_id' => $teamId, 'email' => $mapped->email,
           'name' => $mapped->companyName ?? $mapped->name ?? $mapped->email,
           'phone' => $mapped->phone, 'is_active' => true,
       ]);
   ```
   Ha létező ügyfél és üres a `phone`/`name`, kiegészítjük a beküldésből (nem írjuk felül a meglévőt).
7. **Opportunity** (ha `settings?->create_opportunity ?? true`):
   - `stage` = settings szerint vagy `OpportunityStage::Lead`
   - `title` = `"{$form->name} – {$submission->created_at->format('Y-m-d H:i')}"`
   - `description` = `$mapped->notes` (+ UTM összefoglaló)
   - `campaign_id` = 3.7 szerint feloldva
   - `team_id`, `customer_id` explicit
8. **Interaction**:
   - `type` = `InteractionType::FormSubmission` (új eset, l. 3.5)
   - `subject` = `$form->name`
   - `description` = `$mapped->notes`
   - `interaction_date` = `$submission->created_at`
   - `team_id`, `customer_id` explicit (`user_id` null — rendszer generálta)
9. **Lead-scoring** (ha `settings?->enable_scoring ?? true`): `Team::find($teamId)` majd `LeadScoringService::calculateForCustomer($customer, $team)` — a friss interakció/opportunity beleszámít. (A `score_points` finomhangolás a service viselkedésének függvénye; ha a service nem fogad extra pontot, az újraszámítás önmagában elég — nincs duplikált scoring-logika.)
10. **Visszalink**: `$submission->update(['lead_id' => $customer->id])`.
11. Értesítés kiküldése (4. pont).

Minden CRM-írás egy `DB::transaction`-ben; a `lead_id` visszalink és az e-mail a tranzakció után.

### 3.4 Értesítés — `App\Mail\NewFormSubmissionMail`

- Címzettek: `$settings?->notify_emails` nincs (a notify a csomag `SubmissionActions->notifyEmails` mezőjéből jön) → `$actions->notifyEmails`.
- Tartalom: form neve, beküldő (név/email), a beküldött mezők `"Címke: érték"` listája, link a Filament `FormSubmission` nézetre és — ha készült — az ügyfélre.
- `Mail::to($actions->notifyEmails)->send(new NewFormSubmissionMail(...))`, csak ha a lista nem üres.
- A meglévő `App\Mail\TemplateEmail`/`QuoteEmail` konvencióit követi (Markdown mailable).

### 3.5 Enum-bővítés — `App\Enums\InteractionType`

Új eset: `case FormSubmission = 'form_submission';` — `HasLabel`/`HasColor` implementációkban címke („Űrlap beküldés") + szín a meglévő minta szerint. Így a beküldés-interakciók szűrhetők és mérhetők.

### 3.6 Mérés — Filament dashboard widgetek (team-scoped)

A meglévő widget-konvenciót követve (`app/Filament/Widgets/`):
- **`FormSubmissionStatsWidget`** (`StatsOverviewWidget`): ma / 7 nap / összes beküldés; lead-konverzió = (beküldésből létrejött / összes beküldés) az adott időszakban.
- **`FormSubmissionTrendWidget`** (`ChartWidget`): napi beküldésszám az utolsó 30 napból.
- **`FormSubmissionsByFormWidget`** (`ChartWidget` vagy táblázat): beküldés / űrlap bontás.

Adatforrás: `form_submissions` tábla, `team_id`-re szűrve a `Filament::getTenant()`-ból (a widget HTTP-kontextusban fut, itt a scope elérhető, de explicit szűrünk a konzisztenciáért). A widgetek regisztrálása a meglévő dashboard-mintát követi.

### 3.7 UTM / forrás-attribúció + kampányhoz kötés

- **Capture:** a beküldés `data`-ja tartalmazhat `utm_*` és `referrer` kulcsokat, ha az űrlap beágyazó oldala ezeket átadja (pl. rejtett/kitöltött mezőkként). A CRM-oldal ezeket **fogyasztja, ha jelen vannak**; ha nincsenek, az attribúció egyszerűen kimarad. A `data`-ba juttatásuk a beágyazó oldal / csomag felelőssége — ezt itt nem valósítjuk meg, csak feldolgozzuk.
- **Tárolás:** UTM-összefoglaló az `Opportunity->description` és `Interaction->description` végére; a nyers UTM a `FormSubmission->data`-ban amúgy is megmarad.
- **Kampány feloldás (`campaign_id`) prioritás:**
  1. `settings->campaign_id` (fix, form-onkénti) — ha megadva.
  2. `utm_campaign` → `Campaign::where('team_id',$teamId)->where('name', $utmCampaign)->first()` (case-insensitive), ha talál.
  3. egyébként `null`.

## 4. Regisztráció / bekötés

- **Listener:** Laravel 12 auto-discovery a `handle(FormSubmissionProcessed $event)` típus alapján (a meglévő `app/Listeners/*` is így működik) — nincs kézi regisztráció.
- **Migráció:** `form_crm_settings` tábla.
- **Widgetek:** a dashboard oldal `getWidgets()`/panel widget-regisztrációjához hozzáadva a meglévő minta szerint.
- **`FormCrmSetting` kezelő UI:** app-oldali Filament resource (`FormCrmSettingResource`) a beállítások szerkesztésére (mezőleképezés, kampány, scoring, opportunity kapcsoló) — a form kiválasztásával. (A vendor `RegistrationFormResource`-t nem módosítjuk.)

## 5. Tesztelés (Pest, feature)

`tests/Feature/FormSubmissionCrmPipelineTest.php` (+ szükség szerint unit a mapperhez):

1. **Alap feldolgozás:** emailes beküldés → `Customer` + `Opportunity(stage=Lead)` + `Interaction(type=FormSubmission)` létrejön, `submission.lead_id == customer.id`.
2. **Dedup:** ugyanaz az email kétszer → egy `Customer`, két `Opportunity` + két `Interaction`.
3. **Email nélkül:** nincs `Customer`/`Opportunity`, de a submission megmarad és értesítő megy.
4. **`createLeadIfHasEmail=false`:** nincs CRM-rekord, csak submission (+ értesítő ha van cím).
5. **Team-izoláció (queue-kontextus):** két külön team formjának beküldése nem keveredik; a listener explicit `team_id`-t használ (a container-tenant hiányában is helyes).
6. **Explicit `field_map`:** `FormCrmSetting.field_map` felülírja a heurisztikát.
7. **Kampány feloldás:** `settings.campaign_id` és `utm_campaign`→Campaign név-egyezés is köti az `Opportunity`-t.
8. **Scoring:** feldolgozás után létezik/frissül a `LeadScore` a customerhez.
9. **Értesítés:** `Mail::fake()` — `NewFormSubmissionMail` a `notifyEmails` címekre megy.
10. **Widgetek:** `FormSubmissionStatsWidget` helyes darabszámot/konverziót ad team-scope-olva.

A `FormSubmission`/`RegistrationForm` factory-k a csomagból elérhetők (`database/factories`). A listener queue-olt, tesztben szinkron futtatható (`Queue::fake` nélkül, sync driver) vagy közvetlenül példányosítva.

## 6. Most kihagyva / függőségek

- Az UTM-értékek **beküldésbe juttatása** a beágyazó oldal/csomag felelőssége (a CRM csak fogyasztja).
- In-app (Filament database) értesítés a beküldésről — később, ha kell (most e-mail).
- Automatikus lead-hozzárendelés (round-robin) beküldéskor — a meglévő `LeadScoringService::assignLeadsRoundRobin()` külön futhat, nem része ennek a pipeline-nak.

## 7. Érintett fájlok (várható)

Új:
- `database/migrations/xxxx_create_form_crm_settings_table.php`
- `app/Models/FormCrmSetting.php`
- `app/Services/SubmissionFieldMapper.php` (+ `SubmissionData` DTO)
- `app/Listeners/ProcessFormSubmissionToCrm.php`
- `app/Mail/NewFormSubmissionMail.php` (+ Markdown view)
- `app/Filament/Widgets/FormSubmissionStatsWidget.php`
- `app/Filament/Widgets/FormSubmissionTrendWidget.php`
- `app/Filament/Widgets/FormSubmissionsByFormWidget.php`
- `app/Filament/Resources/FormCrmSettings/*` (resource + oldalak)
- tesztek

Módosított:
- `app/Enums/InteractionType.php` (+ `FormSubmission` eset)
- dashboard/panel widget-regisztráció
