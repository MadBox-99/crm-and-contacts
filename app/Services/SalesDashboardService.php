<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ComplaintSeverity;
use App\Enums\ComplaintStatus;
use App\Enums\ComplaintType;
use App\Models\Campaign;
use App\Models\Complaint;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Quote;
use Illuminate\Support\Facades\Date;

final readonly class SalesDashboardService
{
    public function __construct(
        private CrmReportingService $reportingService,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function getKpis(int $months = 1): array
    {
        $startDate = Date::now()->subMonths($months)->startOfMonth();

        $monthlyRevenue = (float) Order::query()
            ->where('order_date', '>=', $startDate)
            ->sum('total');

        $activeQuotes = Quote::query()
            ->where('status', 'draft')
            ->count();

        $totalQuotes = Quote::query()
            ->where('issue_date', '>=', $startDate)
            ->count();

        $acceptedQuotes = Quote::query()
            ->where('status', 'accepted')
            ->where('issue_date', '>=', $startDate)
            ->count();

        $conversionRate = $totalQuotes > 0
            ? round(($acceptedQuotes / $totalQuotes) * 100, 2)
            : 0.0;

        $avgDealSize = (float) Order::query()
            ->where('order_date', '>=', $startDate)
            ->avg('total');

        return [
            'monthly_revenue' => $monthlyRevenue,
            'active_quotes' => $activeQuotes,
            'conversion_rate' => $conversionRate,
            'avg_deal_size' => round($avgDealSize, 2),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getPipelineData(): array
    {
        return $this->reportingService->getPipelineSummary();
    }

    /**
     * @return array<string, mixed>
     */
    public function getTrendData(int $months = 12): array
    {
        return $this->reportingService->getMonthlySalesTrend($months);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getCampaignRoiData(): array
    {
        return Campaign::query()
            ->withCount('conversions')
            ->withSum('conversions', 'conversion_value')
            ->get()
            ->map(fn (Campaign $campaign): array => [
                'name' => $campaign->name,
                'budget' => (float) $campaign->budget,
                'actual_cost' => (float) $campaign->actual_cost,
                'revenue' => (float) ($campaign->conversions_sum_conversion_value ?? 0),
                'conversions' => $campaign->conversions_count ?? 0,
                'roi' => $campaign->getROI(),
            ])
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function getComplaintStats(): array
    {
        $byType = [];
        foreach (ComplaintType::cases() as $type) {
            $byType[$type->value] = Complaint::query()->where('type', $type)->count();
        }

        $bySeverity = [];
        foreach (ComplaintSeverity::cases() as $severity) {
            $bySeverity[$severity->value] = Complaint::query()->where('severity', $severity)->count();
        }

        $resolvedComplaints = Complaint::query()
            ->whereNotNull('resolved_at')
            ->whereNotNull('reported_at')
            ->get(['reported_at', 'resolved_at']);

        $avgResolutionHours = $resolvedComplaints->isNotEmpty()
            ? $resolvedComplaints->avg(fn (Complaint $c): float => (float) $c->reported_at->diffInHours($c->resolved_at))
            : 0.0;

        $overdueCount = Complaint::query()
            ->whereNotNull('sla_deadline_at')
            ->where('sla_deadline_at', '<', Date::now())
            ->whereNotIn('status', [ComplaintStatus::Resolved, ComplaintStatus::Closed])
            ->count();

        return [
            'by_type' => $byType,
            'by_severity' => $bySeverity,
            'avg_resolution_hours' => round($avgResolutionHours, 1),
            'overdue_count' => $overdueCount,
            'total' => Complaint::query()->count(),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getTopCustomers(int $limit = 10): array
    {
        return Customer::query()
            ->whereHas('orders')
            ->withCount('orders')
            ->withSum('orders', 'total')
            ->orderByDesc('orders_sum_total')
            ->limit($limit)
            ->get()
            ->map(fn (Customer $customer): array => [
                'name' => $customer->name,
                'orders_count' => $customer->orders_count ?? 0,
                'revenue' => (float) ($customer->orders_sum_total ?? 0),
            ])
            ->all();
    }
}
