<?php

use App\Models\User;

it('redirects guests away from the friends update route', function () {
    asGuestExpectRedirect('patch', route('friends.update', 1));
});

it('accepts a friend request', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();

    $user->addFriend($friend);

    $this->actingAs($friend)
        ->from(route('friends.index'))
        ->patch(route('friends.update', $user->id))
        ->assertRedirect(route('friends.index'));

    $this->assertDatabaseHas('friends', [
        'user_id' => $user->id,
        'friend_id' => $friend->id,
        'accepted' => true
    ]);
});