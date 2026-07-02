<?php

declare(strict_types=1);

namespace App\Filament\Resources\Quotes\Pages;

use App\Filament\Resources\Quotes\QuoteResource;
use App\Models\Quote;
use App\Models\QuoteTemplate;
use App\Services\QuoteTemplateService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Override;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class EditQuote extends EditRecord
{
    protected static string $resource = QuoteResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate_pdf')
                ->label('Generate PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->schema([
                    Select::make('template_id')
                        ->label('Template')
                        ->options(
                            QuoteTemplate::query()
                                ->where('is_active', true)
                                ->pluck('name', 'id'),
                        )
                        ->placeholder('Use default template'),
                ])
                ->action(function (array $data): BinaryFileResponse {
                    /** @var Quote $record */
                    $record = $this->record;

                    $template = isset($data['template_id'])
                        ? QuoteTemplate::query()->find($data['template_id'])
                        : null;

                    $service = resolve(QuoteTemplateService::class);
                    $path = $service->generatePdf($record, $template);

                    return response()->download($path, $record->quote_number.'.pdf');
                }),
            Action::make('send_quote')
                ->label('Send Quote')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->schema([
                    TextInput::make('recipient_email')
                        ->email()
                        ->required()
                        ->default(fn (): ?string => $this->record->customer?->email),
                    TextInput::make('recipient_name')
                        ->required()
                        ->default(fn (): ?string => $this->record->customer?->name),
                    Select::make('template_id')
                        ->label('PDF Template')
                        ->options(
                            QuoteTemplate::query()
                                ->where('is_active', true)
                                ->pluck('name', 'id'),
                        )
                        ->placeholder('Use default template'),
                ])
                ->action(function (array $data): void {
                    /** @var Quote $record */
                    $record = $this->record;

                    $template = isset($data['template_id'])
                        ? QuoteTemplate::query()->find($data['template_id'])
                        : null;

                    $service = resolve(QuoteTemplateService::class);
                    $service->sendQuote(
                        $record,
                        $data['recipient_email'],
                        $data['recipient_name'],
                        $template,
                    );

                    Notification::make()
                        ->title('Quote sent successfully')
                        ->success()
                        ->send();
                }),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
