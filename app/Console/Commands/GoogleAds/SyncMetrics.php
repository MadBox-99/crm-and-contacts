<?php

declare(strict_types=1);

namespace App\Console\Commands\GoogleAds;

use App\Enums\CampaignType;
use App\Models\Campaign;
use App\Services\GoogleAdsService;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

final class SyncMetrics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google-ads:sync-metrics
                            {campaign? : The campaign ID to sync metrics for}
                            {--start-date= : Start date for syncing (YYYY-MM-DD)}
                            {--end-date= : End date for syncing (YYYY-MM-DD)}
                            {--all : Sync metrics for all Google Ads campaigns}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync campaign metrics (clicks, impressions, cost) from Google Ads API';

    /**
     * Execute the console command.
     */
    public function handle(GoogleAdsService $googleAdsService): int
    {
        $campaignId = $this->argument('campaign');
        $syncAll = $this->option('all');

        $startDate = $this->option('start-date')
            ? Carbon::parse($this->option('start-date'))
            : null;

        $endDate = $this->option('end-date')
            ? Carbon::parse($this->option('end-date'))
            : null;

        if (! $campaignId && ! $syncAll) {
            $this->error('Please provide a campaign ID or use --all flag');

            return self::FAILURE;
        }

        $campaigns = $this->getCampaigns($campaignId, $syncAll);

        if ($campaigns->isEmpty()) {
            $this->warn('No Google Ads campaigns found to sync');

            return self::SUCCESS;
        }

        $this->info("Syncing metrics for {$campaigns->count()} campaign(s)...");

        $progressBar = $this->output->createProgressBar($campaigns->count());
        $progressBar->start();

        $synced = 0;
        $failed = 0;

        foreach ($campaigns as $campaign) {
            try {
                $success = $googleAdsService->syncMetrics($campaign, $startDate, $endDate);

                if ($success) {
                    $synced++;
                    $this->newLine();
                    $this->info("✓ Synced metrics for campaign: {$campaign->name}");
                }
            } catch (Exception $e) {
                $failed++;
                $this->newLine();
                $this->error("✗ Failed to sync campaign: {$campaign->name} - {$e->getMessage()}");
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info('Sync completed:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Campaigns processed', $campaigns->count()],
                ['Successfully synced', $synced],
                ['Failed campaigns', $failed],
            ]
        );

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * Get campaigns to sync
     */
    private function getCampaigns(?string $campaignId, bool $syncAll): \Illuminate\Support\Collection
    {
        if ($syncAll) {
            return Campaign::query()
                ->where('campaign_type', CampaignType::GoogleAds)
                ->whereNotNull('google_ads_campaign_id')
                ->get();
        }

        $campaign = Campaign::find($campaignId);

        if (! $campaign) {
            $this->error("Campaign with ID {$campaignId} not found");

            return collect();
        }

        if ($campaign->campaign_type !== CampaignType::GoogleAds) {
            $this->error("Campaign {$campaign->name} is not a Google Ads campaign");

            return collect();
        }

        if (! $campaign->google_ads_campaign_id) {
            $this->error("Campaign {$campaign->name} does not have a Google Ads Campaign ID");

            return collect();
        }

        return collect([$campaign]);
    }
}
