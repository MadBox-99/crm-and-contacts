<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\QuoteStatus;
use App\Models\Concerns\BelongsToTeam;
use App\Observers\QuoteObserver;
use Database\Factories\QuoteFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Override;

#[ObservedBy(QuoteObserver::class)]
final class Quote extends Model
{
    use BelongsToTeam;

    /** @use HasFactory<QuoteFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'team_id',
        'customer_id',
        'opportunity_id',
        'quote_number',
        'issue_date',
        'valid_until',
        'status',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total',
        'notes',
        'view_token',
        'sent_at',
        'viewed_at',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(QuoteVersion::class);
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'valid_until' => 'date',
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'status' => QuoteStatus::class,
            'sent_at' => 'datetime',
            'viewed_at' => 'datetime',
        ];
    }
}
