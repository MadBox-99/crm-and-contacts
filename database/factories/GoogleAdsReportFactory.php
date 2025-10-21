<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Campaign;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GoogleAdsReport>
 */
final class GoogleAdsReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $keywords = ['seo services', 'digital marketing', 'web design', 'content marketing', 'social media'];
        $keywordData = [];

        foreach (fake()->randomElements($keywords, fake()->numberBetween(3, 5)) as $keyword) {
            $keywordData[] = [
                'keyword' => $keyword,
                'search_volume' => fake()->numberBetween(100, 10000),
                'difficulty' => fake()->randomFloat(2, 0, 100),
                'competition' => fake()->randomFloat(2, 0, 1),
                'suggested_bid' => fake()->randomFloat(2, 0.5, 10),
            ];
        }

        return [
            'campaign_id' => Campaign::factory(),
            'report_date' => fake()->date(),
            'metadata' => [
                'location' => fake()->country(),
                'language' => 'en',
                'currency' => 'USD',
            ],
            'keyword_data' => $keywordData,
            'historical_metrics' => [],
            'bulk_results' => [],
            'statistics' => [
                'total_keywords' => count($keywordData),
                'avg_search_volume' => collect($keywordData)->avg('search_volume'),
                'avg_difficulty' => collect($keywordData)->avg('difficulty'),
            ],
            'raw_data' => [
                'api_version' => 'v18',
                'generated_at' => now()->toIso8601String(),
            ],
        ];
    }
}
