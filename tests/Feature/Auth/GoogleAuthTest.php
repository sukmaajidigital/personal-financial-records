<?php

use App\Models\User;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;

use function Pest\Laravel\get;

beforeEach(function () {
    $this->mockSocialiteUser = Mockery::mock(SocialiteUser::class);
    $this->mockSocialiteUser->shouldReceive('getId')->andReturn('google-123456');
    $this->mockSocialiteUser->shouldReceive('getName')->andReturn('Test User');
    $this->mockSocialiteUser->shouldReceive('getEmail')->andReturn('test@example.com');
    $this->mockSocialiteUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar.jpg');
});

it('redirects to Google', function () {
    $response = get(route('auth.google.redirect'));

    $response->assertRedirect();
    expect($response->headers->get('Location'))->toContain('accounts.google.com');
});

it('creates a new user from Google callback', function () {
    Socialite::shouldReceive('driver')
        ->with('google')
        ->andReturnSelf()
        ->shouldReceive('user')
        ->andReturn($this->mockSocialiteUser);

    $response = get(route('auth.google.callback'));

    $response->assertRedirect('/dashboard');

    $user = User::where('email', 'test@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->google_id)->toBe('google-123456')
        ->and($user->name)->toBe('Test User')
        ->and($user->email_verified_at)->not->toBeNull()
        ->and($user->password)->toBeNull();
});

it('logs in an existing Google user', function () {
    $user = User::factory()->google()->create([
        'email' => 'test@example.com',
        'google_id' => 'google-123456',
    ]);

    Socialite::shouldReceive('driver')
        ->with('google')
        ->andReturnSelf()
        ->shouldReceive('user')
        ->andReturn($this->mockSocialiteUser);

    $response = get(route('auth.google.callback'));

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
});

it('links Google account to existing email user', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'email_verified_at' => null,
    ]);

    Socialite::shouldReceive('driver')
        ->with('google')
        ->andReturnSelf()
        ->shouldReceive('user')
        ->andReturn($this->mockSocialiteUser);

    $response = get(route('auth.google.callback'));

    $response->assertRedirect('/dashboard');

    $user->refresh();
    expect($user->google_id)->toBe('google-123456')
        ->and($user->email_verified_at)->not->toBeNull();
});

it('redirects to login on Google auth failure', function () {
    Socialite::shouldReceive('driver')
        ->with('google')
        ->andReturnSelf()
        ->shouldReceive('user')
        ->andThrow(new \Exception('Auth failed'));

    $response = get(route('auth.google.callback'));

    $response->assertRedirect(route('login'));
});
