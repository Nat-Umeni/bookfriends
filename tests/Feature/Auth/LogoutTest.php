<?php

use App\Models\User;

it('cannot be seen by unauthed users', function () {
    $this->get(route('home'))->assertOk()->assertDontSeeText('Logout');
});

it('redirects guests away from the logout route', function () {
    asGuestExpectRedirect('post', route('logout'));
});

it('successfully logs out a user and redirects to the home page', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('logout'))
        ->assertRedirect(route('home'));

    $this->assertGuest();
});