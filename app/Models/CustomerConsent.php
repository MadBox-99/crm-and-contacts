<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ConsentType;
use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[Fillable([
    'team_id',
    'customer_id',
    'granted_by',
    'type',
    'is_granted',
    'granted_at',
    'revoked_at',
    'ip_address',
    'notes',
])]
final class CustomerConsent extends Model
{
    use BelongsToTeam;
    use HasFactory;
    use LogsActivity;

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function grantedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['type', 'is_granted', 'granted_at', 'revoked_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'type' => ConsentType::class,
            'is_granted' => 'boolean',
            'granted_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }
}
