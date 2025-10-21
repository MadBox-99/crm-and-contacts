<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InteractionType;
use Database\Factories\InteractionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Interaction extends Model
{
    /** @use HasFactory<InteractionFactory> */
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'user_id',
        'type',
        'subject',
        'description',
        'interaction_date',
        'duration',
        'next_action',
        'next_action_date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'interaction_date' => 'datetime',
            'next_action_date' => 'date',
            'duration' => 'integer',
            'type' => InteractionType::class,
        ];
    }
}
