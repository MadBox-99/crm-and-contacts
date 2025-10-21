<?php

declare(strict_types=1);

use App\Models\User;

it('can login with valid credentials', function (): void {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

    $response = $this->postJson('/api/v1/login', [
        'email' => 'test@example.com',
        'password' => 'password',
        'device_name' => 'test-device',
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'token',
            'token_type',
            'user' => ['id', 'name', 'email'],
        ]);

    expect($response->json('token_type'))->toBe('Bearer');
    expect($response->json('user.email'))->toBe('test@example.com');
});

it('cannot login with invalid credentials', function (): void {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

    $response = $this->postJson('/api/v1/login', [
        'email' => 'test@example.com',
        'password' => 'wrong-password',
        'device_name' => 'test-device',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('requires all login fields', function (): void {
    $response = $this->postJson('/api/v1/login', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['email', 'password', 'device_name']);
});

it('can get authenticated user info', function (): void {
    $user = User::factory()->create();
    $token = $user->createToken('test-device')->plainTextToken;

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$token,
    ])->getJson('/api/v1/me');

    $response->assertOk()
        ->assertJson([
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
            ],
        ]);
});

it('cannot access protected routes without token', function (): void {
    $response = $this->getJson('/api/v1/me');

    $response->assertUnauthorized();
});

it('can logout', function (): void {
    $user = User::factory()->create();
    $token = $user->createToken('test-device')->plainTextToken;

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$token,
    ])->postJson('/api/v1/logout');

    $response->assertOk()
        ->assertJson(['message' => 'Logged out successfully']);

    // Verify token was deleted from database
    expect($user->tokens()->count())->toBe(0);
});
