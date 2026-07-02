<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Database\Factories\CustomerContactFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

final class CustomerContact extends Model
{
    use BelongsToTeam;

    /** @use HasFactory<CustomerContactFactory> */
    use HasFactory;

    protected $fillable = [
        'team_id',
        'customer_id',
        'name',
        'email',
        'phone',
        'position',
        'is_primary',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }
}
