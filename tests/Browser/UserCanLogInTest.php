<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('can sign in the user', function (): void {
    $team = Team::factory()->create();
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
    ]);
    $user->teams()->attach($team);

    $page = visit('/app/login');

    $page->assertSee('Sign in')
        ->assertNoJavaScriptErrors()
        ->type('#form\\.email', 'test@example.com')
        ->type('#form\\.password', 'password')
        ->submit()
        ->wait(2)
        ->assertPathIs('/app/'.$team->slug)
        ->assertSee($user->name)
        ->assertNoJavaScriptErrors()
        ->screenshot(fullPage: true, filename: screenshotPath('auth/login-success'));

    $this->assertAuthenticated();
});

it('cannot sign in with invalid credentials', function (): void {
    $team = Team::factory()->create();
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
    ]);
    $user->teams()->attach($team);

    $page = visit('/app/login');

    $page->assertSee('Sign in')
        ->assertNoJavaScriptErrors()
        ->type('#form\\.email', 'test@example.com')
        ->type('#form\\.password', 'wrong-password')
        ->submit()
        ->wait(1)
        ->assertPathIs('/app/login')
        ->assertSee('Sign in')
        ->assertNoJavaScriptErrors()
        ->screenshot(fullPage: true, filename: screenshotPath('auth/login-failed'));

    $this->assertGuest();
});
