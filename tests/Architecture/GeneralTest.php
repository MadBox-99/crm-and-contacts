<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Seeder;

arch('application does not use debugging functions')
    ->expect(['dd', 'dump', 'var_dump', 'print_r', 'ray'])
    ->not->toBeUsed();

arch('application does not use die or exit')
    ->expect(['die', 'exit'])
    ->not->toBeUsed();

arch('strict types are declared')
    ->expect('App')
    ->toUseStrictTypes();

arch('no global functions in app namespace')
    ->expect('App')
    ->not->toUse(['eval', 'create_function']);

arch('Enums are enums')
    ->expect('App\Enums')
    ->toBeEnums();

arch('Enums have proper naming')
    ->expect('App\Enums')
    ->toHaveSuffix('');

arch('Enums do not use dump or dd')
    ->expect('App\Enums')
    ->not->toUse(['dd', 'dump', 'var_dump', 'print_r']);

arch('value objects are readonly')
    ->expect('App\ValueObjects')
    ->toBeReadonly()
    ->ignoring('App\ValueObjects');

arch('DTOs are readonly')
    ->expect('App\DataTransferObjects')
    ->toBeReadonly()
    ->ignoring('App\DataTransferObjects');

arch('factories are in correct namespace')
    ->expect('Database\Factories')
    ->toExtend(Factory::class)
    ->toHaveSuffix('Factory');

arch('migrations do not use raw SQL')
    ->expect('Database\Migrations')
    ->not->toUse(['DB::raw', 'DB::statement'])
    ->ignoring('Database\Migrations');

arch('seeders extend Seeder')
    ->expect('Database\Seeders')
    ->toExtend(Seeder::class)
    ->ignoring('Database\Seeders');
