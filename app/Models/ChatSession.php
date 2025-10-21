<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ChatSessionStatus;
use Database\Factories\ChatSessionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class ChatSession extends Model
{
    /** @use HasFactory<ChatSessionFactory> */
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'user_id',
        'started_at',
        'ended_at',
        'status',
        'last_message_at',
        'unread_count',
        'priority',
        'rating',
        'notes',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    protected function casts(): array
    {
        return [
            'status' => ChatSessionStatus::class,
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'last_message_at' => 'datetime',
            'unread_count' => 'integer',
        ];
    }
}
