<?php

use App\Models\User;

it('loads the login page', function () {
    $this->get(route('login'))->assertStatus(200)->assertSeeText('Sign In');
});

it('throws validation errors for missing required attrs', function () {
    $this->from(route('login'))
        ->post(route('login.store'))
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors(['email', 'password']);
});

it('cannot hit login page if already logged in', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('login'))
        ->assertRedirect(route('home'));

    $this->get(route('login'))->assertRedirect(route('home'));
});

it('successfully logs in a user with valid data and redirects to the home page', function () {
    $user = User::factory()->create([
        'email' => 'H2a0K@example.com',
        'password' => 'password',
    ]);

    $this->from(route('login'))
        ->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ])
        ->assertRedirect(route('home'))
        ->assertSessionHasNoErrors();

    $this->assertAuthenticatedAs($user);
});