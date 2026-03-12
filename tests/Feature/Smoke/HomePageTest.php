<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

it('renders the welcome page', function () {
    $response = $this->get('/');

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->has('canRegister')
            ->has('stats.totalViews')
            ->has('stats.totalRegistered'));
});
