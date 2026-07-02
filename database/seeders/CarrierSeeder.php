<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Carrier;
use App\Models\Team;
use Illuminate\Database\Seeder;

final class CarrierSeeder extends Seeder
{
    public function run(): void
    {
        $carriers = [
            'DPD',
            'GLS',
            'FoxPost',
            'MPL',
            'DHL',
            'FedEx',
            'UPS',
            'TNT',
            'Sprinter',
            'Packeta',
        ];

        Team::all()->each(function (Team $team) use ($carriers): void {
            foreach ($carriers as $name) {
                Carrier::query()->firstOrCreate(['team_id' => $team->id, 'name' => $name], ['is_active' => true]);
            }
        });
    }
}
