<?php

use App\Models\User;

// it('displays the register page')->get('/register')->assertSee('Register')->assertStatus(200);
it('displays the register page', function () {
    $this->get(route('register'))->assertStatus(200)->assertSeeText('Register');
});

it('throws validation errors for missing required attrs', function () {
    $this->from(route('register'))
        ->post(route('register.store'))
        ->assertRedirect(route('register'))
        ->assertSessionHasErrors(['name', 'email', 'password']);
});

it('cannot hit register page if already logged in', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('register'))
        ->assertRedirect(route('home'));
});

it('successfully registers a new user with valid data and redirects to the home page', function () {
    $this->from(route('register'))
        ->post(route('register.store'), [
            'name' => 'John Doe',
            'email' => 'H2a0K@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('home'));

    $this->assertDatabaseHas('users', [
        'name' => 'John Doe',
        'email' => 'H2a0K@example.com',
    ]);
});