<?php

use App\Models\User;
use App\Models\Book;

beforeEach(function () {
    $this->user = asUser();    
});

it('redirects guest away from feed index', function () {
    asGuestExpectRedirect('get', route('feed.index'));
});

it('renders feed index', function () {
    $this->from(route('home'))->get(route('feed.index'))->assertOk();
});

it('shows books of friends', function () {
    $friend = User::factory()->create();
    $friend2 = User::factory()->create();

    $friend->books()->attach($book1 = Book::factory()->create(), ['status' => 'READING', 'updated_at' => now()->subDay()]);
    $friend2->books()->attach($book2 = Book::factory()->create(), ['status' => 'WANT_TO_READ']);

    $this->user->addFriend($friend);
    $friend->acceptFriend($this->user);

    $friend2->addFriend($this->user);
    $this->user->acceptFriend($friend2);

    $this->from(route('home'))
        ->get(route('feed.index'))
        ->assertSeeInOrder([
            "{$friend2->name} wants to read $book2->title",
            "{$friend->name} is reading {$book1->title}",
        ]);
});