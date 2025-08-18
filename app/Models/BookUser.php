<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

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

    public function getActionAttribute() 
    {
        return match ($this->status) {
            'WANT_TO_READ' => 'wants to read',
            'READING' => 'is reading',
            'READ' => 'has read',
        };
    }
}
