# Bookfriends — Project Overview 📚

A small social reading feed built with Laravel. Built it to remind myself how to use PEST and TDD. Users can befriend each other, track books with a status (“Want to Read”, “Reading”, “Read”), and see a chronological feed of their friends’ book activity. 📖

---

## Contents 🗂️

-   ✨ [Key Features](#key-features)
-   🧠 [Domain Model](#domain-model)
-   🗄️ [Database Schema (Migrations)](#database-schema-migrations)
-   🧩 [Eloquent Models & Relationships](#eloquent-models--relationships)
-   📰 [Friends Feed: Query → Controller → Blade](#friends-feed-query--controller--blade)
-   🧱 [Blade Components](#blade-components)
-   🧪 [Testing Notes](#testing-notes)
-   🧰 [Local Setup](#local-setup)

---

## Key Features ✨

-   🤝 **Friendship system** (two-way acceptance, de-duplicated pair).
-   📚 **Books** with **per-user status** stored in the pivot (`book_user.status`).
-   🕒 **Activity feed** that lists friends’ book updates, newest first (based on `book_user.updated_at`).
-   🎴 Clean UI via Blade components: `<x-section>` and `<x-card>`.

---

## Domain Model 🧠

-   **User** 👤

    -   👥 Can befriend other users (pending/accepted).
    -   🔗 Belongs to many **Book** through `book_user`.

-   **Book** 📘

    -   🏷️ Fields: `title`, `author`.
    -   🔗 Belongs to many **User** through `book_user`.

-   **BookUser** (pivot) 🔗

    -   🗃️ Table: `book_user` with `book_id`, `user_id`, `status`, timestamps.
    -   📊 Status values:
        -   📌 `WANT_TO_READ` → “wants to read”
        -   📖 `READING` → “is reading”
        -   ✅ `READ` → “has read”
    -   ⏱️ `updated_at` on pivot doubles as the **activity timestamp** used in the feed.

-   **Friends** (pivot-like) 🤝
    -   🗃️ Table: `friends` with `user_id`, `friend_id`, `accepted` (boolean).
    -   🔁 Symmetric friendship managed via two relations (`friendsOfMine`, `friendsOf`).
    -   🧭 A **merged view** `friends_view` (via staudenmeir/laravel-merged-relations) exposes _all accepted friendships_ as a single relation.

---

## Database Schema (Migrations) 🗄️

-   🧱 `books` — `id`, `title`, `author`, timestamps.
-   🔗 `book_user` — `id`, `book_id`, `user_id`, `status`, timestamps.
-   👥 `friends` — `id`, `user_id`, `friend_id`, `accepted` (default `false`), timestamps, **unique** pair.
-   🧭 `friends_view` — created with `Schema::createMergeView()` combining accepted friendships from both directions.

> 💾 The app uses SQLite in tests.

---

## Eloquent Models & Relationships 🧩

### `App\Models\User` 👤

Key relations and helpers: 🔧

```php
public function books()
{
    return $this->belongsToMany(Book::class)
        ->using(BookUser::class)
        ->withPivot('status')
        ->withTimestamps();
}

public function friends()
{
    // Merged accepted friendships from both directions via 'friends_view'
    return $this->mergedRelationWithModel(User::class, 'friends_view');
}

/**
 * Deep relation: friends → book_user → books
 * Includes:
 * - pivot_status     : book_user.status
 * - pivot_updated_at : book_user.updated_at (for ordering)
 * - friend_id/name   : from friends_view
 */
public function booksOfFriends()
{
    return $this->hasManyDeepFromRelations($this->friends(), (new User())->books())
        ->select('books.*')
        ->withIntermediate(BookUser::class, [
            'status as pivot_status',
            'updated_at as pivot_updated_at',
        ], 'pivot')
        ->addSelect('friends_view.id as friend_id', 'friends_view.name as friend_name')
        ->orderBy('pivot_updated_at', 'desc');
}
```

Friendship helpers: 🤝

```php
public function addFriend(User $friend): void { /* attaches with accepted=false */ }
public function acceptFriend(User $friend): void { /* sets accepted=true */ }
public function removeFriend(User $friend): void { /* detaches both directions */ }
```

### `App\Models\Book` 📘

```php
class Book extends Model
{
    /** @use HasFactory<\Database\Factories\BookFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('status')
            ->withTimestamps();
    }

    public function getActionAttribute(): ?string
    {
        $status = $this->pivot->status
            ?? $this->pivot_status
            ?? $this->__book_user__status
            ?? null;

        return $status ? BookUser::actionFor($status) : null;
    }
}
```

### `App\Models\BookUser` (Pivot) 🔗

```php
class BookUser extends Pivot
{
    protected $table = 'book_user';

    // Allowed statuses for the pivot
    protected static array $allowedStatuses = [
        'WANT_TO_READ' => 'Want To Read',
        'READING' => 'Reading',
        'READ' => 'Read',
    ];

    public static function rawAllowedStatuses()
    {
        return static::$allowedStatuses;
    }

    public static function allowedStatuses(): array
    {
        return array_keys(static::$allowedStatuses);
    }

    public function isValidStatus(string $status): bool
    {
        return array_key_exists($status, static::$allowedStatuses);
    }

    // BookUser.php
    public static function actionFor(string $status): ?string
    {
        return match ($status) {
            'WANT_TO_READ' => 'wants to read',
            'READING' => 'is reading',
            'READ' => 'has read',
            default => null,
        };
    }

}
```

---

## Friends Feed: Query → Controller → Blade 📰

### Controller 🧠

```php
// FeedController.php
public function index(Request $request)
{
    $books = $request->user()->booksOfFriends()->get();
    return view('feed.index', compact('books'));
}
```

### Blade (Feed) 🎨

Current feed style:

-   **Card Title:** `"{friend_name} {action}"`
-   **Card Subtitle:** `"{book title} by {author}"`
-   **Actions slot:** relative timestamp from `book_user.updated_at` (fallback: `books.updated_at`)

---

## Testing Notes 🧪

The project uses Pest with a set of helpful **DOM assertions** built on Symfony DomCrawler, e.g.:

-   🔍 `toHaveDescendantWithExactTextInTestId($containerTestId, $selector, $text)`
-   📄 `toContainTextInTestId($containerTestId, $text)`
-   🚪 `guestToBeRedirectedTo($route)`

Example (feed):

```php
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

```

---

## Local Setup 🛠️

1. **Install dependencies** 📦

    ```bash
    composer install
    npm install && npm run build
    ```

2. **Environment** 🔧

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

    Configure DB (SQLite is fine for local)

3. **Migrate** 🗄️

    ```bash
    php artisan migrate
    ```

4. **Run** ▶️

    ```bash
    npm run dev
    php artisan serve
    ```

5. **Tests** 🧪

    ```bash
    php artisan test
    ```

---
