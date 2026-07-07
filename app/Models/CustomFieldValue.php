<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CustomFieldType;
use Database\Factories\CustomFieldValueFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Date;

#[Fillable([
    'custom_field_id',
    'model_type',
    'model_id',
    'value',
])]
final class CustomFieldValue extends Model
{
    /** @use HasFactory<CustomFieldValueFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<CustomField, $this>
     */
    public function customField(): BelongsTo
    {
        return $this->belongsTo(CustomField::class);
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the typed value based on the custom field type.
     */
    public function getTypedValue(): mixed
    {
        if ($this->value === null) {
            return null;
        }

        return match ($this->customField->type) {
            CustomFieldType::Number => (float) $this->value,
            CustomFieldType::Checkbox => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            CustomFieldType::Date => Date::parse($this->value),
            default => $this->value,
        };
    }
}
