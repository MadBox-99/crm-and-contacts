<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Database\Factories\QuoteVersionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

final class QuoteVersion extends Model
{
    use BelongsToTeam;

    /** @use HasFactory<QuoteVersionFactory> */
    use HasFactory;

    protected $fillable = [
        'team_id',
        'quote_id',
        'quote_template_id',
        'version_number',
        'snapshot',
        'changes_log',
        'pdf_path',
        'created_by',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(QuoteTemplate::class, 'quote_template_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'snapshot' => 'array',
            'changes_log' => 'array',
        ];
    }
}
