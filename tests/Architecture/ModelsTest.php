<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

arch('models are final')
    ->expect('App\Models')
    ->toBeFinal();

arch('models use HasFactory trait')
    ->expect('App\Models')
    ->toUse(HasFactory::class);

arch('models extend Model')
    ->expect('App\Models')
    ->toExtend(Model::class)
    ->ignoring(User::class);

arch('User model extends Authenticatable')
    ->expect(User::class)
    ->toExtend(Illuminate\Foundation\Auth\User::class);

arch('models have proper namespace')
    ->expect('App\Models')
    ->toBeClasses();

arch('models do not use dump or dd')
    ->expect('App\Models')
    ->not->toUse(['dd', 'dump', 'var_dump', 'print_r']);

arch('models do not use Controllers or HTTP')
    ->expect('App\Models')
    ->not->toUse([
        Request::class,
        'Illuminate\Routing',
        'App\Http\Controllers',
    ]);

arch('models should not depend on Filament directly')
    ->expect('App\Models')
    ->not->toUse('Filament\Resources')
    ->ignoring(User::class);
