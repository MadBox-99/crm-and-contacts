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
        $labels = [];
        $values = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('m-d');
            $values[] = FormSubmission::query()
                ->where('team_id', $teamId)
                ->whereDate('created_at', $date)
                ->count();
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
