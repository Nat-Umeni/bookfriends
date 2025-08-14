<?php

use App\Models\Book;
use App\Models\BookUser;

beforeEach(function () {
    $this->user = asUser();
});

it('shows each book only in its matching section', function (string $status) {
    $title = "My Book ({$status})";

    $book = Book::factory()->create([
        'title' => $title,
        'author' => 'ME!',
    ]);

    $this->user->books()->attach($book->id, ['status' => $status]);

    $this->assertDatabaseHas('book_user', [
        'user_id' => $this->user->id,
        'book_id' => $book->id,
        'status' => $status,
    ]);

    $response = $this->get(route('home'))->assertOk();
    $sectionId = "section-{$status}";

    expect($response)
        ->toHaveDescendantWithExactTextInTestId($sectionId, '[data-role="card-title"]', $title);

    foreach (array_diff(BookUser::allowedStatuses(), [$status]) as $other) {
        expect($response)->toNotContainTextInTestId("section-{$other}", $title);
    }
})->with(BookUser::allowedStatuses());
