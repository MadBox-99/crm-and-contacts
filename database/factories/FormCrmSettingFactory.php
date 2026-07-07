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
