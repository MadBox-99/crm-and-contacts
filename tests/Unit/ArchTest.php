<?php

declare(strict_types=1);

use App\Console\Commands\FetchInboundEmails;
use App\Console\Commands\RunNotificationWorkflows;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\IntegrationController;
use App\Http\Controllers\Api\V1\WebhookController;
use App\Http\Middleware\ApplyTenantScopes;
use App\Http\Responses\LoginResponse;
use App\Http\Responses\RegistrationResponse;
use Illuminate\Database\Eloquent\Model;

arch()->preset()->php();
// arch()->preset()->strict();
arch()->preset()->laravel()->ignoring([
    AuthController::class,
    LoginResponse::class,
    RegistrationResponse::class,
    ApplyTenantScopes::class,
    WebhookController::class,
    IntegrationController::class,
    FetchInboundEmails::class,
    RunNotificationWorkflows::class,
]);
arch()->preset()->security();
arch()->expect('App\Models')
    ->toExtend(Model::class)
    ->ignoring('App\Models\Concerns')
    ->ignoring('App\Models\Scopes');
arch()->expect('App\Controllers\Controller')->toBeAbstract();
