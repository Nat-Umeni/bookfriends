<?php

use App\Models\User;
use App\Models\Book;

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

    expect($user->acceptedFriendsOfMine)
        ->toHaveCount(1)
        ->pluck('id')->toContain($friend->id);

});

it('can get all friends', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    $anotherFriend = User::factory()->create();
    $yetAnotherFriend = User::factory()->create();

    $user->addFriend($friend);
    $user->addFriend($anotherFriend); // not accepted yet
    $user->addFriend($yetAnotherFriend);

    $friend->acceptFriend($user);
    $yetAnotherFriend->acceptFriend($user);

    expect($user->friends)->toHaveCount(2);
    expect($friend->friends)->toHaveCount(1);
    expect($anotherFriend->friends)->toHaveCount(0);
    expect($yetAnotherFriend->friends)->toHaveCount(1);
});

it('can remove a friend', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();

    $user->addFriend($friend);
    $friend->acceptFriend($user);

    $user->removeFriend($friend);

    expect($user->friends)->toHaveCount(0);
    expect($friend->friends)->toHaveCount(0);
});

it('can get books of friends', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    $friend2 = User::factory()->create();
    $friend3 = User::factory()->create();

    $friend->books()->attach($bookOne = Book::factory()->create(), ['status' => 'READING']);
    $friend2->books()->attach(Book::factory()->create(), ['status' => 'WANT_TO_READ', 'updated_at' => now()->subDay()]);
    $friend3->books()->attach(Book::factory()->create(), ['status' => 'READ']);

    // User adds first friend and friend accepts
    // (first scenario)
    $user->addFriend($friend);
    $friend->acceptFriend($user);

    // Friend 2 adds User, user accepts
    // (second scenario, covers the both way around logic)
    $friend2->addFriend($user);
    $user->acceptFriend($friend2);

    // User adds friend 3, but is not accepted
    // (third scenario)
    $user->addFriend($friend3);

    expect($user->booksOfFriends)
        ->toHaveCount(2)
        ->first()->title->toBe($bookOne->title);

});