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
            if (in_array($blueprint->key, self::UTM_KEYS, true)) {
                continue;
            }

            if ($blueprint->key === 'referrer') {
                continue;
            }

            $value = $this->stringValue($data[$blueprint->key] ?? null);
            if ($value === null) {
                continue;
            }

            if ($value === '') {
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
            $trimmed = mb_trim((string) $value);

            return $trimmed === '' ? null : $trimmed;
        }

        return null;
    }

    private function normaliseEmail(?string $email): ?string
    {
        if ($email === null) {
            return null;
        }

        $normalised = mb_strtolower(mb_trim($email));

        return $normalised === '' ? null : $normalised;
    }
}
