<?php

use Illuminate\Support\Facades\RateLimiter;

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('registration is throttled after multiple accounts from same ip', function () {
    // First registration should succeed
    $this->post(route('register.store'), [
        'name' => 'User One',
        'email' => 'user1@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    auth()->logout();

    // Second registration from same IP should be throttled
    $response = $this->post(route('register.store'), [
        'name' => 'User Two',
        'email' => 'user2@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('registration throttle resets after decay period', function () {
    // First registration
    $this->post(route('register.store'), [
        'name' => 'User One',
        'email' => 'user1@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    auth()->logout();

    // Clear the rate limiter to simulate time passing
    RateLimiter::clear('register:127.0.0.1');

    // Should succeed after rate limit clears
    $response = $this->post(route('register.store'), [
        'name' => 'User Two',
        'email' => 'user2@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});
