<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

// IDE Complaining too hard about data sets
// dataset($providers, ['github']);
$providers = ['github'];

afterEach(fn() => Mockery::close());

it('redirects to provider', function (string $provider) {
    mockSocialiteRedirect($provider, 'https://example.test/oauth/' . $provider);
    $this->get(route('auth.oauth.redirect', ['provider' => $provider]))
        ->assertRedirect('https://example.test/oauth/' . $provider);
})->with($providers);

it('logs in an existing linked user', function (string $provider) {
    $user = User::factory()->create([
        'provider' => $provider,
        'provider_id' => 'abc123',
        'email' => 'user@example.test',
    ]);

    mockSocialiteUser($provider, fakeSocialUser([
        'id' => 'abc123',
        'email' => 'user@example.test',
        'token' => 'new-token',
    ]));

    $this->get(route('auth.oauth.callback', ['provider' => $provider]))
        ->assertRedirect(route('home'));

    $this->assertAuthenticatedAs($user);
    expect($user->fresh()->token)->toBe('new-token');
})->with($providers);

it('links an existing local account by email', function (string $provider) {
    $local = User::factory()->create([
        'email' => 'linkme@example.test',
        'provider' => null,
        'provider_id' => null,
    ]);

    mockSocialiteUser($provider, fakeSocialUser([
        'id' => '98765',
        'email' => 'linkme@example.test',
        'token' => 'linked-token',
    ]));

    $this->get(route('auth.oauth.callback', ['provider' => $provider]))
        ->assertRedirect(route('home'));

    $this->assertAuthenticatedAs($local);
    $local->refresh();
    expect($local->provider)->toBe($provider)
        ->and($local->provider_id)->toBe('98765')
        ->and($local->token)->toBe('linked-token');
})->with($providers);

it('creates a new user when email is hidden', function (string $provider) {
    mockSocialiteUser($provider, fakeSocialUser([
        'id' => 'no-email-1',
        'email' => null,
        'name' => 'Hidden Email',
    ]));

    $this->get(route('auth.oauth.callback', ['provider' => $provider]))
        ->assertRedirect(route('home'));

    $this->assertAuthenticated();
    $created = Auth::user()->fresh();

    expect($created->provider)->toBe($provider)
        ->and($created->provider_id)->toBe('no-email-1')
        ->and($created->email)->toEndWith('@users.noreply.' . $provider . '.com')
        ->and($created->name)->toBe('Hidden Email');
})->with($providers);

it('handles provider failure', function (string $provider) {
    // chain mock: driver->user throws
    Socialite::shouldReceive('driver->user')
        ->andThrow(new Exception('boom'));

    $this->get(route('auth.oauth.callback', ['provider' => $provider]))
        ->assertRedirect(route('login'));
})->with($providers);

it('handles unknown provider', function () {
    $this->get(route('auth.oauth.callback', ['provider' => 'unknown']))
        ->assertNotFound(route('login'));
});