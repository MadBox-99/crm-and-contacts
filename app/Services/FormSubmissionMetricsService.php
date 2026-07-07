<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Carbon;
use Madbox99\FilamentFormBuilder\Models\FormSubmission;

final class FormSubmissionMetricsService
{
    /**
     * @return array{today: int, week: int, total: int, converted: int, conversion_rate: float}
     */
    public function stats(int $teamId): array
    {
        $base = FormSubmission::query()->where('team_id', $teamId);

        $total = (clone $base)->count();
        $today = (clone $base)->whereDate('created_at', Carbon::today())->count();
        $week = (clone $base)->where('created_at', '>=', Carbon::now()->subDays(7))->count();
        $converted = (clone $base)->whereNotNull('lead_id')->count();

        return [
            'today' => $today,
            'week' => $week,
            'total' => $total,
            'converted' => $converted,
            'conversion_rate' => $total > 0 ? round($converted / $total * 100, 1) : 0.0,
        ];
    }

    /**
     * @return array{labels: list<string>, values: list<int>}
     */
    public function dailyTrend(int $teamId, int $days = 30): array
    {
        $start = Carbon::today()->subDays($days - 1);

        $counts = FormSubmission::query()
            ->where('team_id', $teamId)
            ->where('created_at', '>=', $start->copy()->startOfDay())
            ->get(['created_at'])
            ->groupBy(fn ($submission): string => $submission->created_at->format('Y-m-d'))
            ->map->count();

        $labels = [];
        $values = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i);
            $labels[] = $date->format('m-d');
            $values[] = (int) ($counts[$date->format('Y-m-d')] ?? 0);
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * @return array{labels: list<string>, values: list<int>}
     */
    public function byForm(int $teamId): array
    {
        $rows = FormSubmission::query()
            ->selectRaw('registration_form_id, COUNT(*) as aggregate')
            ->where('team_id', $teamId)
            ->groupBy('registration_form_id')
            ->with('registrationForm:id,name')
            ->get();

        $labels = [];
        $values = [];

        foreach ($rows as $row) {
            $labels[] = (string) ($row->registrationForm?->name ?? '#'.$row->registration_form_id);
            $values[] = (int) $row->aggregate;
        }

        return ['labels' => $labels, 'values' => $values];
    }
}
