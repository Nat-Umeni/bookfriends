<?php

use App\Models\Book;
use App\Models\BookUser;

beforeEach(function () {
    $this->user = asUser();
});

it('redirects guest away from book edit page', function () {
    asGuestExpectRedirect('get', route('books.edit', 1));
});

it('shows an edit heading with the book title', function () {
    $book = Book::factory()->create();
    $this->user->books()->attach($book, ['status' => 'READING']);

    $this->assertDatabaseHas('book_user', [
        'user_id' => $this->user->id,
        'book_id' => $book->id,
        'status' => 'READING',
    ]);

    $res = $this->get(route('books.edit', $book))->assertOk();

    expect($res)->assertSeeText("Change the details of {$book->title}");
});


it('forbids viewing edit form for a book not attached to this user', function () {
    $someoneElse = asUser();
    $book = Book::factory()->create();
    $someoneElse->books()->attach($book, ['status' => 'READ']);

    $this->get(route('books.edit', $book))->assertForbidden();
});



it('prefills form inputs with the current book', function (string $status) {
    $book = Book::factory()->create();

    $this->user->books()->attach($book->id, ['status' => $status]);

    $this->assertDatabaseHas('book_user', [
        'user_id' => $this->user->id,
        'book_id' => $book->id,
        'status' => $status,
    ]);

    $response = $this->get(route('books.edit', $book->id))->assertOk();

    expect($response)
        ->toHaveInput('title', $book->title)
        ->toHaveInput('author', $book->author)
        ->toHaveSelectWithSelectedOption('status', $status)
        ->toHaveSelectWithOptions('status', ['', ...BookUser::allowedStatuses()]);
})->with(['status' => BookUser::allowedStatuses()]);