<?php

use App\Models\User;

it('greets the user when they are signed out', function () {
    $this->get('/')
        ->assertSee('Bookfriends')
        ->assertSee('Sign up to get started');
});

it('shows authenticated menu items if user is signed in', function () {
    // Sign in as a user
    $user = User::factory()->create();

    // Acting as
    // Assert that the authenticated menu items are visible
    $this->actingAs($user)
        ->get('/')
        ->assertSeeText([
            'Feed',
            'My Books',
            'Add a Book',
            'Friends',
            $user->name
        ]);
});

it('shows unauthenticated menu items if user is NOT signed in', function () {
    $this->get('/')
        ->assertSeeText([
            'Home',
            'Login',
            'Register'
        ])
        ->assertDontSeeText([
            'Feed',
            'My Books',
            'Add a Book',
            'Friends'
        ]);
});