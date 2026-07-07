<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ShipmentTrackingEventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

#[Fillable([
    'shipment_id',
    'status_code',
    'location',
    'description',
    'occurred_at',
    'metadata',
])]
final class ShipmentTrackingEvent extends Model
{
    /** @use HasFactory<ShipmentTrackingEventFactory> */
    use HasFactory;

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'metadata' => 'array',
        ];
    }
}
