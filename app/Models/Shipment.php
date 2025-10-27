<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ShipmentStatus;
use Database\Factories\ShipmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Shipment extends Model
{
    /** @use HasFactory<ShipmentFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'order_id',
        'external_customer_id',
        'external_order_id',
        'shipment_number',
        'carrier',
        'tracking_number',
        'status',
        'shipping_address',
        'shipped_at',
        'estimated_delivery_at',
        'delivered_at',
        'notes',
        'documents',
    ];

    public static function generateShipmentNumber(): string
    {
        $year = now()->format('Y');
        $lastShipment = self::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $nextNumber = $lastShipment ? ((int) mb_substr((string) $lastShipment->shipment_number, -4)) + 1 : 1;

        return 'SHP-'.$year.'-'.mb_str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function trackingEvents(): HasMany
    {
        return $this->hasMany(ShipmentTrackingEvent::class)->orderBy('occurred_at');
    }

    protected function casts(): array
    {
        return [
            'status' => ShipmentStatus::class,
            'shipping_address' => 'array',
            'documents' => 'array',
            'shipped_at' => 'datetime',
            'estimated_delivery_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }
}
