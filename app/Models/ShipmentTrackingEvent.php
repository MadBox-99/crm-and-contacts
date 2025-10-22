<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ShipmentTrackingEventFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ShipmentTrackingEvent extends Model
{
    /** @use HasFactory<ShipmentTrackingEventFactory> */
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'status_code',
        'location',
        'description',
        'occurred_at',
        'metadata',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'metadata' => 'array',
        ];
    }
}
