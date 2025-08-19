<?php

use Illuminate\Testing\TestResponse;
use Symfony\Component\DomCrawler\Crawler;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Contracts\User as SocialiteUser;

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
    ->in('Feature', 'Unit');

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

expect()->extend('toHaveInput', function (string $name, ?string $expectedValue = null) {
    $crawler = new Crawler($this->value->getContent());

    $input = $crawler->filter("input[name='{$name}']");
    expect($input->count())
        ->toBe(1, "Expected exactly 1 input[name='{$name}'], found {$input->count()}");

    if (!is_null($expectedValue)) {
        $actualValue = $input->attr('value');
        expect($actualValue)->toBe(
            $expectedValue,
            "Expected input[name='{$name}'] value to be \"{$expectedValue}\", got \"" . ($actualValue ?? '') . "\"."
        );
    }

    return $this;
});


expect()->extend('toHaveSelectWithOptions', function (string $name, array $values) {
    $crawler = new Crawler($this->value->getContent());
    expect($crawler->filter("select[name='{$name}']")->count())->toBe(1);

    $actual = $crawler->filter("select[name='{$name}'] option")->each(fn($n) => $n->attr('value'));
    expect($actual)->toEqual($values);
    return $this;
});

expect()->extend('toHaveSelectWithSelectedOption', function (string $name, ?string $expectedValue = null) {
    $crawler = new Crawler($this->value->getContent());

    $select = $crawler->filter("select[name='{$name}']");
    expect($select->count())
        ->toBe(1, "Expected exactly 1 select[name='{$name}'], found {$select->count()}.");

    // Ensure the specific option exists
    $option = $select->filter("option[value='{$expectedValue}']");
    expect($option->count())
        ->toBeGreaterThan(0, "Option with value '{$expectedValue}' not found in select[name='{$name}'].");

    // Ensure it is selected
    $isSelected = $option->attr('selected') !== null
        || $option->matches('option[selected]');

    expect($isSelected)->toBeTrue(
        "Option value '{$expectedValue}' in select[name='{$name}'] is not selected."
    );

    return $this;
});



expect()->extend('toHaveDescendantWithExactTextInTestId', function (string $containerTestId, string $descendantSelector, string $expectedText) {
    $html = $this->value->getContent();
    $crawler = new Crawler($html);

    $container = $crawler->filter("[data-test='{$containerTestId}']");
    expect($container->count())
        ->toBeGreaterThan(0, "Element with data-test=\"{$containerTestId}\" not found.");

    $descendants = $container->filter($descendantSelector);
    expect($descendants->count())
        ->toBeGreaterThan(0, "No descendant found for selector \"{$descendantSelector}\" in [data-test=\"{$containerTestId}\"].");

    $normalizedExpected = preg_replace('/\s+/', ' ', trim($expectedText));

    $matches = $descendants->each(function (Crawler $node) use ($normalizedExpected) {
        return preg_replace('/\s+/', ' ', trim($node->text())) === $normalizedExpected;
    });

    expect(in_array(true, $matches, true))->toBeTrue(
        "No descendant \"{$descendantSelector}\" in [data-test=\"{$containerTestId}\"] has exact text \"{$expectedText}\"."
    );

    return $this;
});

/**
 * Negative form: assert that NO descendant exists matching the selector + attributes.
 */
expect()->extend('toNotHaveDescendantInTestId', function (string $containerTestId, string $descendantSelector, array $descendantAttributes = []) {
    $responseHtml = $this->value->getContent();
    $crawler = new Crawler($responseHtml);

    $containerElement = $crawler->filter("[data-test='{$containerTestId}']");
    // If the container isn't present, that's fine for a negative assertion.
    if ($containerElement->count() === 0) {
        return $this;
    }

    $attributeSelector = buildAttributeSelector($descendantAttributes);
    $fullSelector = "{$descendantSelector}{$attributeSelector}";

    $matchingDescendants = $containerElement->filter($fullSelector);
    expect($matchingDescendants->count())
        ->toBe(0, "Unexpected descendant matching \"{$fullSelector}\" found inside data-test=\"{$containerTestId}\".");

    return $this;
});


expect()->extend('toContainTextInTestId', function (string $containerTestId, string|array $expected) {
    $response = $this->value; // should be a TestResponse
    $crawler = new Crawler($response->getContent());

    $container = $crawler->filter("[data-testid='{$containerTestId}']");
    expect($container->count())->toBeGreaterThan(0, "Element [data-testid='{$containerTestId}'] not found.");

    $normalized = trim(preg_replace('/\s+/', ' ', $container->text()));

    foreach ((array) $expected as $needle) {
        // no custom message arg here — just assert
        expect($normalized)->toContain($needle);
    }

    return $this;
});

expect()->extend('toNotContainTextInTestId', function (string $containerTestId, string|array $unexpected) {
    $html = $this->value->getContent(); // TestResponse expected
    $crawler = new Crawler($html);

    // NOTE: data-testid (not data-test)
    $container = $crawler->filter("[data-testid='{$containerTestId}']");
    if ($container->count() === 0) {
        // Keep your original semantics: missing container = pass
        return $this;
    }

    $normalized = trim(preg_replace('/\s+/', ' ', $container->text()));

    foreach ((array) $unexpected as $needle) {
        expect($normalized)->not->toContain($needle);
    }

    return $this;
});

expect()->extend('guestToBeRedirectedTo', function (string $route) {
    Auth::logout();
    expect($this->value->isRedirect())->toBeTrue();
    expect($this->value->getTargetUrl())->toBe(route($route));
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

function asUser(array $overrides = []): \App\Models\User
{
    $user = \App\Models\User::factory()->create($overrides);
    test()->actingAs($user, 'web');
    return $user;
}

function buildAttributeSelector(array $attributes): string
{
    return collect($attributes)
        ->map(fn($value, $name) => sprintf('[%s="%s"]', $name, addcslashes((string) $value, '"')))
        ->implode('');
}

/**
 * Visit a URL as a GUEST and assert it redirects to the expected URL.
 *
 * @param 'get'|'post' $method
 * @param string       $visitUrl         Full URL (e.g. route('books.edit', 1))
 * @param string|null  $expectedRedirect Full URL (defaults to route('login'))
 * @param array        $postData         Request payload for POSTs
 *
 * @return TestResponse
 */
function asGuestExpectRedirect(string $method, string $visitUrl, ?string $expectedRedirect = null): TestResponse
{
    Auth::logout();

    $method = strtolower($method);
    $response = match ($method) {
        'get' => test()->get($visitUrl),
        'post' => test()->post($visitUrl),
        'patch' => test()->patch($visitUrl),
        'delete' => test()->delete($visitUrl),
        default => throw new InvalidArgumentException("Unsupported method '{$method}'. Use 'get' or 'post'."),
    };

    $expectedRedirect ??= route('login');

    expect($response->isRedirect())->toBeTrue("Expected a redirect to {$expectedRedirect}, got a non-redirect response.");
    expect($response->getTargetUrl())->toBe($expectedRedirect, "Expected redirect to {$expectedRedirect}, got {$response->getTargetUrl()}.");
    expect($response->status())->toBe(302);

    return $response;
}

/** Minimal fake Socialite user that works for any oauth provider */
function fakeSocialUser(array $overrides = []): SocialiteUser
{
    return new class ($overrides) implements SocialiteUser {
        public string $id;
        public ?string $nickname;
        public ?string $name;
        public ?string $email;
        public ?string $avatar;
        public $token;
        public $refreshToken;
        public $expiresIn;

        public function __construct(array $overrides)
        {
            $d = array_merge([
                'id' => '12345',
                'nickname' => 'octo',
                'name' => 'OAuth User',
                'email' => 'oauth@example.test',
                'avatar' => 'https://example.test/avatar.png',
                'token' => 'fake-token',
                'refreshToken' => 'fake-refresh',
                'expiresIn' => 3600,
            ], $overrides);

            $this->id = (string) $d['id'];
            $this->nickname = $d['nickname'];
            $this->name = $d['name'];
            $this->email = $d['email'];
            $this->avatar = $d['avatar'];
            $this->token = $d['token'];
            $this->refreshToken = $d['refreshToken'];
            $this->expiresIn = $d['expiresIn'];
        }

        public function getId() { return $this->id; }
        public function getNickname() { return $this->nickname; }
        public function getName() { return $this->name; }
        public function getEmail() { return $this->email; }
        public function getAvatar() { return $this->avatar; }
    };
}

/** Mock redirect for a given provider */
function mockSocialiteRedirect(string $provider, string $toUrl): void
{
    Socialite::shouldReceive('driver')
        ->once()->with($provider)->andReturnSelf();

    Socialite::shouldReceive('redirect')
        ->once()->andReturn(redirect()->away($toUrl));
}

/** Mock user() for a given provider on callback */
function mockSocialiteUser(string $provider, SocialiteUser $user): void
{
    Socialite::shouldReceive('driver')
        ->once()->with($provider)->andReturnSelf();

    Socialite::shouldReceive('user')
        ->once()->andReturn($user);
}