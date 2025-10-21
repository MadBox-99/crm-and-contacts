<?php

declare(strict_types=1);

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
        Schema::create('google_ads_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->date('report_date');
            $table->json('metadata')->nullable();
            $table->json('keyword_data')->nullable();
            $table->json('historical_metrics')->nullable();
            $table->json('bulk_results')->nullable();
            $table->json('statistics')->nullable();
            $table->json('raw_data')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['campaign_id', 'report_date']);
            $table->index('report_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_ads_reports');
    }
};
