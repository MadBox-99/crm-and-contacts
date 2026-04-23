<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Madbox99\FilamentWooCommerce\Concerns\HasWooMapping;

final class Product extends Model
{
    use BelongsToTeam;
    use HasFactory;
    use HasWooMapping;
    use SoftDeletes;

    protected $fillable = [
        'team_id',
        'name',
        'sku',
        'description',
        'category_id',
        'unit_price',
        'tax_rate',
        'is_active',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    protected static function booted(): void
    {
        self::creating(function (self $product): void {
            if (blank($product->sku)) {
                $product->sku = 'AUTO-'.Str::upper(Str::random(10));
            }
        });
    }

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}
