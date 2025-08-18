<?php

use App\Models\User;

it('redirects guests away from the friends update route', function () {
    asGuestExpectRedirect('patch', route('friends.update', 1));
});

it('removes a friend or friend request', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();

    $user->addFriend($friend);

    $this->actingAs($friend)
        ->from(route('friends.index'))
        ->delete(route('friends.destroy', $user))
        ->assertRedirect(route('friends.index'));

    $friend->addFriend($user);

    $this->actingAs($user)
        ->from(route('friends.index'))
        ->delete(route('friends.destroy', $friend))
        ->assertRedirect(route('friends.index'));

    $this->assertDatabaseMissing('friends', [
        'user_id' => $user->id,
        'friend_id' => $friend->id,
    ]);

    $this->assertDatabaseMissing('friends', [
        'user_id' => $friend->id,
        'friend_id' => $user->id,
    ]);
});