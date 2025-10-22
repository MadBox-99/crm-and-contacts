<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('can sign in the user', function () {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
    ]);

    $page = visit('/admin/login');

    $page->assertSee('Sign in')
        ->assertNoJavaScriptErrors()
        ->type('#form\\.email', 'test@example.com')
        ->type('#form\\.password', 'password')
        ->submit()
        ->wait(2)
        ->assertPathIs('/admin')
        ->assertSee('Dashboard')
        ->assertNoJavaScriptErrors();

    $this->assertAuthenticated();
});

it('cannot sign in with invalid credentials', function () {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
    ]);

    $page = visit('/admin/login');

    $page->assertSee('Sign in')
        ->assertNoJavaScriptErrors()
        ->type('#form\\.email', 'test@example.com')
        ->type('#form\\.password', 'wrong-password')
        ->submit()
        ->wait(1)
        ->assertPathIs('/admin/login')
        ->assertSee('Sign in')
        ->assertNoJavaScriptErrors();

    $this->assertGuest();
});
