<?php

declare(strict_types=1);

arch('application does not use dangerous functions')
    ->expect(['exec', 'shell_exec', 'system', 'passthru', 'popen', 'proc_open'])
    ->not->toBeUsed();

arch('application does not use extract')
    ->expect(['extract'])
    ->not->toBeUsed();

arch('application does not use compact excessively')
    ->expect('App\Filament')
    ->not->toUse(['compact']);

arch('no direct database queries in models')
    ->expect('App\Models')
    ->not->toUse(['DB::select', 'DB::insert', 'DB::update', 'DB::delete']);

arch('no raw SQL in application')
    ->expect('App')
    ->not->toUse(['DB::raw'])
    ->ignoring([
        'App\Providers',
        'Database\Migrations',
    ]);

arch('sensitive data is not logged')
    ->expect('App')
    ->not->toUse(['Log::info', 'Log::debug'])
    ->ignoring([
        'App\Providers',
        'App\Console',
        'App\Exceptions',
    ]);

arch('models do not use mass assignment vulnerability')
    ->expect('App\Models')
    ->not->toUse(['Model::unguard']);
