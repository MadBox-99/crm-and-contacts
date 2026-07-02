<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DiscountType;
use App\Enums\DiscountValueType;
use App\Models\Concerns\BelongsToTeam;
use Database\Factories\DiscountFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Override;

final class Discount extends Model
{
    use BelongsToTeam;

    /** @use HasFactory<DiscountFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'team_id',
        'name',
        'type',
        'value_type',
        'value',
        'min_quantity',
        'min_value',
        'valid_from',
        'valid_until',
        'customer_id',
        'product_id',
        'is_active',
        'description',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_quantity' => 'decimal:2',
            'min_value' => 'decimal:2',
            'valid_from' => 'date',
            'valid_until' => 'date',
            'is_active' => 'boolean',
            'type' => DiscountType::class,
            'value_type' => DiscountValueType::class,
        ];
    }
}
