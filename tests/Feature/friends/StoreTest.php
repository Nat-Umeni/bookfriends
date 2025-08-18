<?php

use App\Models\User;

beforeEach(function () {
   $this->user = asUser(); 
});

it('stops unauthed users from hitting endpoint', function () {
   asGuestExpectRedirect('post', route('friends.store')); 
});

it('validates the email is required', function () {
   $this->from(route('friends.index'))->post(route('friends.store'), ['email' => ''])->assertSessionHasErrors('email'); 
});

it('validates the email exists', function () {
   $this->from(route('friends.index'))->post(route('friends.store'), [
        'email' => 'nat@trutrade.co.uk'
    ])->assertSessionHasErrors('email'); 
});

it('cannot add yourself', function () {
   $this->from(route('friends.index'))->post(route('friends.store'), [
        'email' => $this->user->email
    ])->assertSessionHasErrors('email'); 
});

it('stores the friend request', function () {
   $friend = User::factory()->create();
   $this->from(route('friends.index'))->post(route('friends.store'), [
        'email' => $friend->email
    ])->assertRedirect(route('friends.index'));

    $this->assertDatabaseHas('friends', [
        'user_id' => $this->user->id,
        'friend_id' => $friend->id,
        'accepted' => false
    ]);
});