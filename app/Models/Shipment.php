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

/**
 * @property int $id
 * @property int|null $customer_id
 * @property int|null $order_id
 * @property string|null $external_customer_id
 * @property string|null $external_order_id
 * @property string $shipment_number
 * @property string|null $carrier
 * @property string|null $tracking_number
 * @property ShipmentStatus $status
 * @property array<array-key, mixed>|null $shipping_address
 * @property \Carbon\CarbonImmutable|null $shipped_at
 * @property \Carbon\CarbonImmutable|null $estimated_delivery_at
 * @property \Carbon\CarbonImmutable|null $delivered_at
 * @property string|null $notes
 * @property array<array-key, mixed>|null $documents
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read \App\Models\Customer|null $customer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ShipmentItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Order|null $order
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ShipmentTrackingEvent> $trackingEvents
 * @property-read int|null $tracking_events_count
 * @method static \Database\Factories\ShipmentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereCarrier($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereDeliveredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereDocuments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereEstimatedDeliveryAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereExternalCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereExternalOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereShipmentNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereShippedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereShippingAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereTrackingNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment withoutTrashed()
 * @mixin \Eloquent
 */
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
