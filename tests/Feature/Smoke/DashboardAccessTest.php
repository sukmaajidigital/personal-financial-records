<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects guests away from the dashboard', function () {
    $this->get('/dashboard')
        ->assertRedirect('/login');
});

it('allows verified users to open the dashboard', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('Dashboard');
});
