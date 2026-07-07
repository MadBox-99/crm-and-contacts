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
