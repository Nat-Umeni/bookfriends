<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class BookUser extends Pivot
{
    protected $table = 'book_user';

    // Allowed statuses for the pivot
    protected static array $allowedStatuses = [
        'WANT_TO_READ' => 'Want to use',
        'READING' => 'Reading',
        'READ' => 'Read',
    ];

    public static function allowedStatuses(): array
    {
        return array_keys(static::$allowedStatuses);
    }

    public function isValidStatus(string $status): bool
    {
        return in_array($status, static::$allowedStatuses, true);
    }
}
