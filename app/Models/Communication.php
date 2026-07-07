<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CommunicationChannel;
use App\Enums\CommunicationDirection;
use App\Enums\CommunicationStatus;
use App\Models\Concerns\BelongsToTeam;
use Database\Factories\CommunicationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

#[Fillable([
    'team_id',
    'customer_id',
    'channel',
    'direction',
    'subject',
    'content',
    'message_id',
    'in_reply_to',
    'thread_id',
    'from_email',
    'to_email',
    'cc',
    'has_attachments',
    'status',
    'sent_at',
    'delivered_at',
    'read_at',
])]
final class Communication extends Model
{
    use BelongsToTeam;

    /** @use HasFactory<CommunicationFactory> */
    use HasFactory;

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get all communications in the same thread.
     *
     * @return HasMany<self, self>
     */
    public function thread(): HasMany
    {
        return $this->hasMany(self::class, 'thread_id', 'thread_id');
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'delivered_at' => 'datetime',
            'read_at' => 'datetime',
            'cc' => 'array',
            'has_attachments' => 'boolean',
            'status' => CommunicationStatus::class,
            'channel' => CommunicationChannel::class,
            'direction' => CommunicationDirection::class,
        ];
    }
}
