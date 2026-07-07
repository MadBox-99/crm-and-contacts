<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Database\Factories\InvoiceItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

#[Fillable([
    'team_id',
    'invoice_id',
    'product_id',
    'description',
    'quantity',
    'unit_price',
    'discount_amount',
    'tax_rate',
    'total',
])]
final class InvoiceItem extends Model
{
    use BelongsToTeam;

    /** @use HasFactory<InvoiceItemFactory> */
    use HasFactory;

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function calculateTotal(): void
    {
        $subtotal = $this->unit_price * $this->quantity;
        $afterDiscount = $subtotal - $this->discount_amount;
        $this->total = $afterDiscount + ($afterDiscount * ($this->tax_rate / 100));
        $this->save();
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }
}
