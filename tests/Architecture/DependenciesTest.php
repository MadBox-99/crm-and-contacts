<?php

declare(strict_types=1);

use App\Models\User;

arch('models do not depend on Filament')
    ->expect('App\Models')
    ->not->toUse('Filament\Resources')
    ->not->toUse('Filament\Forms')
    ->not->toUse('Filament\Tables')
    ->ignoring(User::class);

arch('models do not depend on HTTP layer')
    ->expect('App\Models')
    ->not->toUse('Illuminate\Http')
    ->not->toUse('Illuminate\Routing');

arch('Enums do not depend on Eloquent')
    ->expect('App\Enums')
    ->not->toUse('Illuminate\Database\Eloquent');

arch('Enums do not depend on Filament')
    ->expect('App\Enums')
    ->not->toUse('Filament');

arch('Factories use proper dependencies')
    ->expect('Database\Factories')
    ->not->toUse([
        'Filament',
        'Illuminate\Http',
    ]);

arch('Resources can use Models')
    ->expect('App\Filament\Resources')
    ->toUse('App\Models');

arch('Resources can use Enums')
    ->expect('App\Filament\Resources')
    ->toUse('App\Enums');

arch('application does not use Laravel Debugbar in production code')
    ->expect('App')
    ->not->toUse('Barryvdh\Debugbar');

arch('application does not use helpers where facades exist')
    ->expect('App')
    ->not->toUse(['app', 'config', 'env'])
    ->ignoring([
        'App\Providers',
        'App\Console',
    ]);
