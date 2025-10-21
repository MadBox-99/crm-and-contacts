<?php

declare(strict_types=1);

use Filament\Resources\Resource;

arch('Filament resources are final')
    ->expect('App\Filament\Resources')
    ->classes()
    ->toBeFinal();

arch('Filament main resources extend Resource')
    ->expect('App\Filament\Resources\CustomerResource')
    ->toExtend(Resource::class);

arch('Filament pages are in proper namespace')
    ->expect('App\Filament\Resources')
    ->classes()
    ->toBeClasses();

arch('Filament resources should not use dump or dd')
    ->expect('App\Filament\Resources')
    ->not->toUse(['dd', 'dump', 'var_dump', 'print_r']);
