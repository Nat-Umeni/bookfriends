<?php

use App\Models\User;

beforeEach(function () {
    $this->user = asUser();
});

it('redirects guest away from friends index', function () {
    asGuestExpectRedirect('get', route('friends.index'));
});

it('can view friends index', function () {
    $this->get(route('friends.index'))->assertOk();
});

it('shows a list of the users pending friend requests', function () {

    $friends = User::factory()->times(2)->create();
    $friends->each(fn ($friend) => $this->user->addFriend($friend));

    $this->get(route('friends.index'))->assertOk()
        ->assertSeeTextInOrder(
            array_merge(['Pending Friend Requests'], $friends->pluck('name')->toArray())
        );

});