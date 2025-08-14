<?php

use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    $this->user = asUser();
});

it('only allows authenticated users to store a book', function () {
    // Because I'm testing guest stuff in this one
    Auth::logout();
    $this->post(route('books.store'))->assertRedirect(route('login'));
});

it('creates a new book', function () {
    $this->post(route('books.store'), [
        'title' => 'My Book',
        'author' => 'ME!',
        'status' => 'WANT_TO_READ',
    ]);

    $this->assertDatabaseHas('books', [
        'title' => 'My Book',
        'author' => 'ME!',
    ])
        ->assertDatabaseHas('book_user', [
            'user_id' => $this->user->id,
            'status' => 'WANT_TO_READ',
        ]);
});

it('requires a title, author and status', function () {
    $this->post(route('books.store'))->assertSessionHasErrors(['title', 'author', 'status']);
});

it('requires a valid status', function () {
    $this->post(route('books.store'), [
        'title' => 'My Book',
        'author' => 'ME!',
        'status' => 'INVALID_STATUS',
    ])->assertSessionHasErrors(['status']);
});