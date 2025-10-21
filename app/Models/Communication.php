<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CommunicationChannel;
use App\Enums\CommunicationDirection;
use App\Enums\CommunicationStatus;
use Database\Factories\CommunicationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Communication extends Model
{
    /** @use HasFactory<CommunicationFactory> */
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'channel',
        'direction',
        'subject',
        'content',
        'status',
        'sent_at',
        'delivered_at',
        'read_at',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'delivered_at' => 'datetime',
            'read_at' => 'datetime',
            'status' => CommunicationStatus::class,
            'channel' => CommunicationChannel::class,
            'direction' => CommunicationDirection::class,
        ];
    }
}
