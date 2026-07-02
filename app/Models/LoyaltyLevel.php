<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

final class LoyaltyLevel extends Model
{
    use BelongsToTeam;
    use HasFactory;

    protected $fillable = [
        'team_id',
        'name',
        'minimum_points',
        'discount_percentage',
        'color',
        'sort_order',
        'is_active',
    ];

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'minimum_points' => 'integer',
            'discount_percentage' => 'decimal:2',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
