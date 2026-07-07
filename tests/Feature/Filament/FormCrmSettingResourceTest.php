<?php

declare(strict_types=1);

use App\Filament\Resources\FormCrmSettings\Pages\CreateFormCrmSetting;
use App\Filament\Resources\FormCrmSettings\Pages\EditFormCrmSetting;
use App\Filament\Resources\FormCrmSettings\Pages\ListFormCrmSettings;
use App\Models\FormCrmSetting;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Madbox99\FilamentFormBuilder\Models\RegistrationForm;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->team = Team::factory()->create();
    $this->user->teams()->attach($this->team);

    $this->actingAs($this->user);

    Filament::setTenant($this->team);
    Filament::bootCurrentPanel();

    $this->form = RegistrationForm::factory()->create(['team_id' => $this->team->id]);
});

it('can render the list page', function (): void {
    livewire(ListFormCrmSettings::class)
        ->assertSuccessful();
});

it('can list crm settings', function (): void {
    $setting = FormCrmSetting::factory()->create([
        'team_id' => $this->team->id,
        'registration_form_id' => $this->form->id,
    ]);

    livewire(ListFormCrmSettings::class)
        ->assertCanSeeTableRecords([$setting]);
});

it('creates a crm setting through the panel', function (): void {
    livewire(CreateFormCrmSetting::class)
        ->fillForm([
            'registration_form_id' => $this->form->id,
            'create_opportunity' => true,
            'opportunity_stage' => 'lead',
            'enable_scoring' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(FormCrmSetting::query()->where('registration_form_id', $this->form->id)->exists())->toBeTrue();
});

it('does not allow selecting another team\'s registration form', function (): void {
    $otherTeam = Team::factory()->create();
    $otherTeamForm = RegistrationForm::factory()->create(['team_id' => $otherTeam->id]);

    livewire(CreateFormCrmSetting::class)
        ->fillForm([
            'registration_form_id' => $otherTeamForm->id,
            'create_opportunity' => true,
            'opportunity_stage' => 'lead',
            'enable_scoring' => true,
        ])
        ->call('create')
        ->assertHasFormErrors(['registration_form_id']);

    expect(FormCrmSetting::query()->where('registration_form_id', $otherTeamForm->id)->doesntExist())->toBeTrue();
});

it('validates required fields on create', function (): void {
    livewire(CreateFormCrmSetting::class)
        ->fillForm([
            'registration_form_id' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['registration_form_id' => 'required']);
});

it('can update a crm setting', function (): void {
    $setting = FormCrmSetting::factory()->create([
        'team_id' => $this->team->id,
        'registration_form_id' => $this->form->id,
        'enable_scoring' => true,
    ]);

    livewire(EditFormCrmSetting::class, ['record' => $setting->id])
        ->fillForm([
            'enable_scoring' => false,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($setting->refresh()->enable_scoring)->toBeFalse();
});
