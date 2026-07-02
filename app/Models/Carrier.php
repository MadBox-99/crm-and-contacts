<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Database\Factories\CarrierFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Override;

final class Carrier extends Model
{
    use BelongsToTeam;

    /** @use HasFactory<CarrierFactory> */
    use HasFactory;

    protected $fillable = [
        'team_id',
        'name',
        'is_active',
    ];

    #[Override]
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
