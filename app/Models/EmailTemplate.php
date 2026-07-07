<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EmailTemplateCategory;
use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[Fillable([
    'team_id',
    'name',
    'subject',
    'body',
    'category',
    'variables',
    'is_active',
    'created_by',
])]
final class EmailTemplate extends Model
{
    use BelongsToTeam;
    use HasFactory;
    use LogsActivity;

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(Interaction::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'subject', 'category', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'variables' => 'array',
            'category' => EmailTemplateCategory::class,
        ];
    }
}
