<?php

declare(strict_types=1);

namespace App\Filament\Resources\Quotes\RelationManagers;

use App\Models\QuoteVersion;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Override;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class VersionsRelationManager extends RelationManager
{
    protected static string $relationship = 'versions';

    protected static ?string $recordTitleAttribute = 'version_number';

    #[Override]
    public static function getTitle(mixed $ownerRecord, string $pageClass): string
    {
        return __('Versions');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('version_number')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('createdBy.name')
                    ->label(__('Created by'))
                    ->placeholder('-'),
                TextColumn::make('template.name')
                    ->label(__('Template'))
                    ->placeholder('-'),
                TextColumn::make('changes_summary')
                    ->label(__('Changes'))
                    ->state(function (QuoteVersion $record): string {
                        $log = $record->changes_log;
                        if (empty($log)) {
                            return '-';
                        }

                        if (isset($log['initial'])) {
                            return 'Initial version';
                        }

                        $parts = [];
                        if (isset($log['quote'])) {
                            $parts[] = count($log['quote']).' field(s) changed';
                        }

                        if (isset($log['items_count'])) {
                            $parts[] = 'items: '.$log['items_count']['old'].' → '.$log['items_count']['new'];
                        }

                        return implode(', ', $parts) ?: '-';
                    }),
                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('version_number', 'desc')
            ->recordActions([
                Action::make('download_pdf')
                    ->label(__('Generate PDF'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->visible(fn (QuoteVersion $record): bool => $record->pdf_path !== null
                        && Storage::disk('local')->exists(str_replace(Storage::disk('local')->path(''), '', $record->pdf_path)))
                    ->action(fn (QuoteVersion $record): BinaryFileResponse => response()->download($record->pdf_path)),
            ]);
    }

    #[Override]
    protected static function getModelLabel(): string
    {
        return __('Version');
    }
}
