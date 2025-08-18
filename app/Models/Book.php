<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        return $this->belongsToMany(\App\Models\User::class)
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
