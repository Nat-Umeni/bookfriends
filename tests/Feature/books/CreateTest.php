<?php

use App\Models\BookUser;

it('shows the create page to authed users only', function () {
    $this->get(route('books.create'))->assertRedirect(route('login'));

    asUser();
    $this->get(route('books.create'))
        ->assertStatus(200)
        ->assertSeeText('Add a Book');
});

it('shows the correct inputs and select in the form', function () {
    asUser();
    $response = $this->get(route('books.create'))->assertOk();

    expect($response)
        ->toHaveInput('title')
        ->toHaveInput('author')
        ->toHaveSelectWithOptions('status', ['', ...BookUser::allowedStatuses()]);
});