<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'description',
        'quantity',
        'unit_price',
        'discount_amount',
        'tax_rate',
        'total',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function calculateTotals(): void
    {
        $this->total = ($this->unit_price * $this->quantity) - $this->discount_amount + (($this->unit_price * $this->quantity - $this->discount_amount) * ($this->tax_rate / 100));
        $this->save();
    }

    /**
     * @return array<string, float>['subtotal' => float, 'total' => float]
     */
    public function getCalculatedTotals(): array
    {
        $this->total = ($this->unit_price * $this->quantity) - $this->discount_amount + (($this->unit_price * $this->quantity - $this->discount_amount) * ($this->tax_rate / 100));
        $this->save();

        return [
            'subtotal' => $this->unit_price * $this->quantity,
            'total' => $this->total,
        ];
    }

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
