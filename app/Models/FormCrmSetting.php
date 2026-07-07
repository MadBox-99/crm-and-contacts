<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Madbox99\FilamentFormBuilder\Models\RegistrationForm;
use Override;

final class FormCrmSetting extends Model
{
    use BelongsToTeam;
    use HasFactory;

    protected $fillable = [
        'registration_form_id',
        'team_id',
        'field_map',
        'create_opportunity',
        'opportunity_stage',
        'campaign_id',
        'enable_scoring',
    ];

    /**
     * @return BelongsTo<RegistrationForm, $this>
     */
    public function registrationForm(): BelongsTo
    {
        return $this->belongsTo(RegistrationForm::class);
    }

    /**
     * @return BelongsTo<Campaign, $this>
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * @return array<string, string>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'field_map' => 'array',
            'create_opportunity' => 'boolean',
            'enable_scoring' => 'boolean',
        ];
    }
}
