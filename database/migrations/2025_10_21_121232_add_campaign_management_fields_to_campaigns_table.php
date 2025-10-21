<?php

declare(strict_types=1);

use App\Enums\CampaignType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table): void {
            $table->enum('campaign_type', array_column(CampaignType::cases(), 'value'))
                ->default(CampaignType::Other->value)
                ->after('status');
            $table->decimal('budget', 12, 2)->nullable()->after('campaign_type');
            $table->decimal('actual_cost', 12, 2)->default(0)->after('budget');
            $table->unsignedBigInteger('clicks')->default(0)->after('actual_cost');
            $table->unsignedBigInteger('impressions')->default(0)->after('clicks');
            $table->string('google_ads_campaign_id')->nullable()->after('impressions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table): void {
            $table->dropColumn([
                'campaign_type',
                'budget',
                'actual_cost',
                'clicks',
                'impressions',
                'google_ads_campaign_id',
            ]);
        });
    }
};
