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

    $mapped = new SubmissionFieldMapper()->map($form, $data);

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

    $mapped = new SubmissionFieldMapper()->map($form, ['e_mail' => 'x@y.hu', 'telefon' => '123']);

    expect($mapped->email)->toBe('x@y.hu')
        ->and($mapped->phone)->toBe('123');
});

it('honours explicit field_map overrides', function (): void {
    $form = formWithFields([
        field(FormFieldBlueprint::TYPE_EMAIL, 'email', 'Email'),
        field(FormFieldBlueprint::TYPE_TEXT, 'contact', 'Contact'),
    ]);
    $settings = new FormCrmSetting(['field_map' => ['name' => 'contact']]);

    $mapped = new SubmissionFieldMapper()->map($form, ['email' => 'a@b.hu', 'contact' => 'Nagy Béla'], $settings);

    expect($mapped->name)->toBe('Nagy Béla');
});

it('extracts utm and returns null email when absent', function (): void {
    $form = formWithFields([field(FormFieldBlueprint::TYPE_TEXT, 'name', 'Name')]);

    $mapped = new SubmissionFieldMapper()->map($form, [
        'name' => 'X', 'utm_source' => 'google', 'utm_campaign' => 'summer', 'referrer' => 'https://g.com',
    ]);

    expect($mapped->email)->toBeNull()
        ->and($mapped->hasEmail())->toBeFalse()
        ->and($mapped->utm)->toBe(['utm_source' => 'google', 'utm_campaign' => 'summer'])
        ->and($mapped->referrer)->toBe('https://g.com');
});
