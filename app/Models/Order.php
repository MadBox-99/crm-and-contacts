<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\OrderStatus;
use App\Models\Concerns\BelongsToTeam;
use App\Observers\OrderObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Madbox99\FilamentWooCommerce\Concerns\HasWooMapping;

#[ObservedBy(OrderObserver::class)]
final class Order extends Model
{
    use BelongsToTeam;
    use HasFactory;
    use HasWooMapping;
    use SoftDeletes;

    protected $fillable = [
        'team_id',
        'customer_id',
        'quote_id',
        'order_number',
        'order_date',
        'status',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total',
        'notes',
        'shipping_address_id',
        'billing_address_id',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class, 'shipping_address_id');
    }

    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class, 'billing_address_id');
    }

    public function calculateTotals(): void
    {
        $this->subtotal = $this->orderItems()->sum('unit_price * quantity');
        $this->total = $this->subtotal - $this->discount_amount + $this->tax_amount;
        $this->save();
    }

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'status' => OrderStatus::class,
        ];
    }
}
