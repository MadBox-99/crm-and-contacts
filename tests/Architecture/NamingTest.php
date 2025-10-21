<?php

declare(strict_types=1);

arch('models follow naming conventions')
    ->expect('App\Models')
    ->toHaveSuffix('')
    ->toBeClasses()
    ->not->toHaveSuffix('Model');

arch('factories follow naming conventions')
    ->expect('Database\Factories')
    ->toHaveSuffix('Factory');

arch('Enums use PascalCase for cases')
    ->expect('App\Enums')
    ->toBeEnums();

arch('controllers have Controller suffix')
    ->expect('App\Http\Controllers')
    ->toHaveSuffix('Controller')
    ->ignoring('App\Http\Controllers');

arch('middleware has proper naming')
    ->expect('App\Http\Middleware')
    ->toBeClasses()
    ->ignoring('App\Http\Middleware');

arch('form requests have Request suffix')
    ->expect('App\Http\Requests')
    ->toHaveSuffix('Request')
    ->ignoring('App\Http\Requests');

arch('jobs have proper naming')
    ->expect('App\Jobs')
    ->toBeClasses()
    ->ignoring('App\Jobs');

arch('notifications have Notification suffix')
    ->expect('App\Notifications')
    ->toHaveSuffix('Notification')
    ->ignoring('App\Notifications');

arch('events follow naming conventions')
    ->expect('App\Events')
    ->toBeClasses()
    ->ignoring('App\Events');

arch('listeners follow naming conventions')
    ->expect('App\Listeners')
    ->toBeClasses()
    ->ignoring('App\Listeners');
