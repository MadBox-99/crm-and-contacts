<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\NavigationGroup;
use App\Enums\OpportunityStage;
use App\Filament\Resources\LeadOpportunities\LeadOpportunitiesResource;
use App\Models\Opportunity;
use App\Models\User;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Livewire\Attributes\Url;
use Override;
use UnitEnum;

final class KanbanBoard extends Page
{
    #[Url]
    public string $filterAssignee = '';

    #[Url]
    public string $filterPeriod = '';

    public string $filterMinValue = '';

    public string $filterMaxValue = '';

    /** @var array<string, array{label: string, color: string, opportunities: array<int, mixed>, count: int, total: float}> */
    public array $stages = [];

    /** @var array<int, array{id: int, name: string}> */
    public array $teamUsers = [];

    protected string $view = 'filament.pages.kanban-board';

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Customers;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedViewColumns;

    protected static ?int $navigationSort = 3;

    #[Override]
    public static function getNavigationLabel(): string
    {
        return __('Kanban Board');
    }

    #[Override]
    public function getTitle(): string
    {
        return __('Pipeline Kanban');
    }

    public function mount(): void
    {
        $this->loadTeamUsers();
        $this->loadBoard();
    }

    public function updatedFilterAssignee(): void
    {
        $this->loadBoard();
    }

    public function updatedFilterPeriod(): void
    {
        $this->loadBoard();
    }

    public function updatedFilterMinValue(): void
    {
        $this->loadBoard();
    }

    public function updatedFilterMaxValue(): void
    {
        $this->loadBoard();
    }

    public function moveOpportunity(int $opportunityId, string $newStage): void
    {
        $tenant = Filament::getTenant();
        $opportunity = Opportunity::query()->find($opportunityId);

        if (! $opportunity || $opportunity->team_id !== $tenant?->id) {
            return;
        }

        $stage = OpportunityStage::tryFrom($newStage);

        if (! $stage) {
            return;
        }

        $opportunity->update(['stage' => $stage]);

        $this->loadBoard();
    }

    public function getEditUrl(int $opportunityId): string
    {
        return LeadOpportunitiesResource::getUrl('edit', ['record' => $opportunityId]);
    }

    private function loadTeamUsers(): void
    {
        $tenant = Filament::getTenant();

        $this->teamUsers = User::query()
            ->whereHas('teams', fn ($q) => $q->where('teams.id', $tenant?->id))
            ->get(['id', 'name'])
            ->map(fn (User $user): array => ['id' => $user->id, 'name' => $user->name])
            ->all();
    }

    private function loadBoard(): void
    {
        $this->stages = [];

        foreach (OpportunityStage::cases() as $stage) {
            $opportunities = $this->getOpportunitiesForStage($stage);

            $this->stages[$stage->value] = [
                'label' => $stage->getLabel(),
                'color' => $stage->getColor(),
                'opportunities' => $opportunities->all(),
                'count' => $opportunities->count(),
                'total' => $opportunities->sum('value'),
            ];
        }
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function getOpportunitiesForStage(OpportunityStage $stage): Collection
    {
        $query = Opportunity::query()
            ->with(['customer', 'assignedUser'])
            ->where('stage', $stage);

        if ($this->filterAssignee !== '') {
            $query->where('assigned_to', (int) $this->filterAssignee);
        }

        if ($this->filterPeriod !== '') {
            $months = (int) $this->filterPeriod;
            $query->where('expected_close_date', '>=', Date::now()->subMonths($months));
        }

        if ($this->filterMinValue !== '') {
            $query->where('value', '>=', (float) $this->filterMinValue);
        }

        if ($this->filterMaxValue !== '') {
            $query->where('value', '<=', (float) $this->filterMaxValue);
        }

        return $query->orderByDesc('value')->get()->map(fn (Opportunity $opp): array => [
            'id' => $opp->id,
            'title' => $opp->title,
            'customer_name' => $opp->customer?->name ?? '—',
            'value' => (float) $opp->value,
            'probability' => $opp->probability,
            'assignee_name' => $opp->assignedUser?->name ?? '—',
            'assignee_initials' => $opp->assignedUser
                ? mb_strtoupper(mb_substr((string) $opp->assignedUser->name, 0, 1))
                : '?',
            'expected_close_date' => $opp->expected_close_date?->format('Y-m-d'),
        ]);
    }
}
