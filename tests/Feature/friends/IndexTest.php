<?php

use App\Models\User;

beforeEach(function () {
    $this->user = asUser();
});

it('redirects guest away from friends index', function () {
    asGuestExpectRedirect('get', route('friends.index'));
});

it('can view friends index and shows input for adding friend', function () {
    $response = $this->get(route('friends.index'))->assertOk();
    expect($response)->toHaveInput('email', '');
});

it('shows a list of the users pending friend requests', function () {
    $friends = User::factory()->times(2)->create();
    $friends->each(fn($friend) => $this->user->addFriend($friend));

    $this->get(route('friends.index'))->assertOk()
        ->assertSeeTextInOrder(
            array_merge(['Pending Friend Requests'], $friends->pluck('name')->toArray())
        );
});

it('shows a list of the users incoming friend requests', function () {
    $friends = User::factory()->times(2)->create();
    $friends->each(fn($friend) => $friend->addFriend($this->user));

    $this->get(route('friends.index'))->assertOk()
        ->assertSeeTextInOrder(
            array_merge(['Incoming Friend Requests'], $friends->pluck('name')->toArray())
        );
});

it('shows a list of the accepted friend requests', function () {
    $friends = User::factory()->times(2)->create();
    $friends->each(function ($friend) {
        $this->user->addFriend($friend);
        $friend->acceptFriend($this->user);
    });

    $this->get(route('friends.index'))->assertOk()
        ->assertSeeTextInOrder(
            array_merge(['Friends'], $friends->pluck('name')->toArray())
        );
});