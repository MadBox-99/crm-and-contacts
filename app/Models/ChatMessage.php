<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ChatMessageSenderType;
use App\Models\Concerns\BelongsToTeam;
use Database\Factories\ChatMessageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Override;

final class ChatMessage extends Model
{
    use BelongsToTeam;

    /** @use HasFactory<ChatMessageFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'team_id',
        'chat_session_id',
        'parent_message_id',
        'sender_type',
        'sender_id',
        'message',
        'is_read',
        'read_at',
        'edited_at',
    ];

    public function chatSession(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class);
    }

    public function sender(): MorphTo
    {
        return $this->morphTo();
    }

    public function parentMessage(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_message_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_message_id');
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'sender_type' => ChatMessageSenderType::class,
            'is_read' => 'boolean',
            'read_at' => 'datetime',
            'edited_at' => 'datetime',
        ];
    }
}
