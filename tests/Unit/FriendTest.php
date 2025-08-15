<?php

use App\Models\User;


it('does not allow adding yourself as a friend', function () {
    $user = User::factory()->create();

    expect(fn() => $user->addFriend($user))
        ->toThrow(InvalidArgumentException::class);

    // Also assert nothing got inserted
    expect($user->pendingFriendsOfMine()->count())->toBe(0);
});


it('does not allow duplicate friend requests', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();

    $user->addFriend($friend);
    $user->addFriend($friend);

    expect($user->pendingFriendsOfMine()->count())->not->toBe(2);
});

it('can have pending friends', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();

    $user->addFriend($friend);

    expect($user->pendingFriendsOfMine()->count())->toBe(1);
});


it('can have friend requests', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();

    $friend->addFriend($user);

    expect($user->pendingFriendsOf()->count())->toBe(1);
});


it('can accept friend requests', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();

    $user->addFriend($friend);
    $friend->acceptFriend($user);

    expect($user->acceptedFriendsOfMine()->count())->toBe(1);
    expect($user->acceptedFriendsOfMine->pluck('id'))->toContain($friend->id);

});