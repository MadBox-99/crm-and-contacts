<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LoyaltyPointSource;
use App\Enums\LoyaltyTransactionType;
use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Override;

#[Fillable([
    'team_id',
    'customer_id',
    'points',
    'type',
    'source',
    'description',
    'reference_type',
    'reference_id',
    'balance_after',
])]
final class LoyaltyPoint extends Model
{
    use BelongsToTeam;
    use HasFactory;

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'balance_after' => 'integer',
            'type' => LoyaltyTransactionType::class,
            'source' => LoyaltyPointSource::class,
        ];
    }
}
