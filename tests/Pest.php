<?php

use Symfony\Component\DomCrawler\Crawler;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

function responseCrawler(\Illuminate\Testing\TestResponse $response): Crawler {
    return new Crawler($response->getContent());
}

expect()->extend('toHaveInput', function (string $name) {
    $crawler = new Crawler($this->value->getContent());
    expect($crawler->filter("input[name='{$name}']")->count())->toBe(1);
    return $this;
});

expect()->extend('toHaveSelectWithOptions', function (string $name, array $values) {
    $crawler = new Crawler($this->value->getContent());
    expect($crawler->filter("select[name='{$name}']")->count())->toBe(1);

    $actual = $crawler->filter("select[name='{$name}'] option")->each(fn($n) => $n->attr('value'));
    expect($actual)->toEqual($values);
    return $this;
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

function asUser(array $overrides = []): \App\Models\User
{
    $user = \App\Models\User::factory()->create($overrides);
    test()->actingAs($user);
    return $user;
}