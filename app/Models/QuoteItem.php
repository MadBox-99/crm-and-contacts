<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

#[Fillable([
    'team_id',
    'quote_id',
    'product_id',
    'description',
    'quantity',
    'unit_price',
    'discount_percent',
    'discount_amount',
    'tax_rate',
    'total',
])]
final class QuoteItem extends Model
{
    use BelongsToTeam;
    use HasFactory;

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function updateTotal(): void
    {

        $this->total = ($this->quantity * $this->unit_price) * (1 - (float) $this->discount_percent / 100) + ($this->quantity * $this->unit_price * $this->tax_rate / 100);
        $this->save();

    }

    #[Override]
    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }
}
