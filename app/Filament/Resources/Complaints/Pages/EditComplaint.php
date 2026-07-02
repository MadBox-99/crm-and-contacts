<?php

declare(strict_types=1);

namespace App\Filament\Resources\Complaints\Pages;

use App\Enums\ComplaintStatus;
use App\Filament\Resources\Complaints\ComplaintResource;
use App\Models\Complaint;
use App\Models\User;
use App\Services\ComplaintService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Override;

/** @property Complaint $record */
final class EditComplaint extends EditRecord
{
    protected static string $resource = ComplaintResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            Action::make('escalate')
                ->label('Escalate')
                ->icon('heroicon-o-arrow-up-circle')
                ->color('danger')
                ->schema([
                    Select::make('escalated_to')
                        ->label('Escalate To')
                        ->relationship('assignedUser', 'name', modifyQueryUsing: fn ($query) => $query->whereRelation('teams', 'teams.id', resolve('current_team')->getKey()))
                        ->required(),
                    Textarea::make('reason')
                        ->label('Reason')
                        ->required(),
                ])
                ->action(function (array $data, Complaint $record, ComplaintService $complaintService): void {
                    $escalatedTo = User::query()->findOrFail($data['escalated_to']);
                    $escalatedBy = Auth::user();

                    $complaintService->escalate($record, $escalatedTo, $escalatedBy, $data['reason']);

                    Notification::make()
                        ->success()
                        ->title('Complaint Escalated')
                        ->body(sprintf('Escalated to %s (Level %d).', $escalatedTo->name, $record->fresh()->escalation_level))
                        ->send();

                    $this->refreshFormData(['status', 'assigned_to', 'escalation_level']);
                })
                ->visible(fn (Complaint $record): bool => ! in_array($record->status, [ComplaintStatus::Resolved, ComplaintStatus::Closed])),

            Action::make('resolve')
                ->label('Resolve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->schema([
                    Textarea::make('resolution')
                        ->label('Resolution')
                        ->required(),
                ])
                ->action(function (array $data, Complaint $record, ComplaintService $complaintService): void {
                    $complaintService->resolve($record, $data['resolution']);

                    Notification::make()
                        ->success()
                        ->title('Complaint Resolved')
                        ->body('The complaint has been resolved successfully.')
                        ->send();

                    $this->refreshFormData(['status', 'resolution', 'resolved_at']);
                })
                ->visible(fn (Complaint $record): bool => ! in_array($record->status, [ComplaintStatus::Resolved, ComplaintStatus::Closed])),

            DeleteAction::make(),
        ];
    }
}
