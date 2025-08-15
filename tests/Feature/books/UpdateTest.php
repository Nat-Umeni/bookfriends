<?php

use App\Models\Book;

beforeEach(function () {
    $this->user = asUser();
});

it('does not allow guests to update a book', function () {
    asGuestExpectRedirect('patch', route('books.update', 1));
});

it('forbids updating a book not attached to this user', function () {
    // Create a book as someone else
    $someoneElse = asUser();
    $book = Book::factory()->create();
    $someoneElse->books()->attach($book, ['status' => 'READ']);

    // Switch back to the tests first user (from before each)
    $this->actingAs($this->user);
    // Try to view the book
    $this->patch(route('books.update', $book))->assertForbidden();
});

it('returns a 404 if the book does not exist', function () {
    $this->patch(route('books.update', 1))->assertNotFound();
});

it('throws validation errors for missing required attrs', function () {
    $book = Book::factory()->create();
    $this->user->books()->attach($book, ['status' => 'READING']);

    $this->from(route('books.edit', $book))
        ->patch(route('books.update', $book), [
            'title' => '',
            'author' => '',
            'status' => '',
        ])
        ->assertRedirect(route('books.edit', $book))
        ->assertSessionHasErrors(['title', 'author', 'status']);
});

it('updates a book', function () {
    $book = Book::factory()->create();
    $this->user->books()->attach($book, ['status' => 'READING']);

    $response = $this->patch(route('books.update', $book), [
        'title' => 'NEW BOOK TITLE',
        'author' => 'NEW AUTHOR',
        'status' => 'READ',
    ]);

    $this->assertDatabaseHas('books', [
        'id' => $book->id,
        'title' => 'NEW BOOK TITLE',
        'author' => 'NEW AUTHOR',
    ])->assertDatabaseHas('book_user', [
        'user_id' => $this->user->id,
        'book_id' => $book->id,
        'status' => 'READ',
    ]);

    $response->assertRedirect(route('home'));
});