<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CustomFieldModel;
use App\Enums\CustomFieldType;
use Database\Factories\CustomFieldFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Override;

#[Fillable([
    'name',
    'slug',
    'type',
    'model_type',
    'options',
    'description',
    'sort_order',
    'is_active',
    'is_visible_in_form',
    'is_visible_in_table',
    'is_visible_in_infolist',
])]
final class CustomField extends Model
{
    /** @use HasFactory<CustomFieldFactory> */
    use HasFactory;

    /**
     * @return HasMany<CustomFieldValue, $this>
     */
    public function values(): HasMany
    {
        return $this->hasMany(CustomFieldValue::class);
    }

    #[Override]
    protected static function booted(): void
    {
        self::creating(function (CustomField $customField): void {
            if (empty($customField->slug)) {
                $customField->slug = Str::slug($customField->name).'-'.Str::random(6);
            }
        });
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'type' => CustomFieldType::class,
            'model_type' => CustomFieldModel::class,
            'options' => 'array',
            'is_active' => 'boolean',
            'is_visible_in_form' => 'boolean',
            'is_visible_in_table' => 'boolean',
            'is_visible_in_infolist' => 'boolean',
        ];
    }
}
