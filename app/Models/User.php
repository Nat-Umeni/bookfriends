<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\BookUser;
use InvalidArgumentException;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Staudenmeir\LaravelMergedRelations\Eloquent\HasMergedRelationships;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasMergedRelationships, HasRelationships;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function books()
    {
        return $this->belongsToMany(Book::class)
            ->using(BookUser::class)
            ->withPivot('status')
            ->withTimestamps();
    }

    public function friends()
    {
        return $this->mergedRelationWithModel(User::class, 'friends_view');
    }

    public function addFriend(User $friend): void
    {
        if ($friend->is($this)) {
            throw new InvalidArgumentException('You cannot add yourself as a friend.');
        }

        $this->friendsOfMine()->syncWithoutDetaching([
            $friend->id => ['accepted' => false],
        ]);
    }

    public function acceptFriend(User $friend): void
    {
        $friend->friendsOfMine()->updateExistingPivot($this->id, ['accepted' => true]);
    }

    public function friendsOfMine()
    {
        return $this->belongsToMany(User::class, 'friends', 'user_id', 'friend_id')
            ->withPivot('accepted');
    }

    public function friendsOf()
    {
        return $this->belongsToMany(User::class, 'friends', 'friend_id', 'user_id')
            ->withPivot('accepted');
    }

    public function pendingFriendsOfMine()
    {
        return $this->friendsOfMine()
            ->wherePivot('accepted', false);
    }

    public function pendingFriendsOf()
    {
        return $this->friendsOf()
            ->wherePivot('accepted', false);
    }

    public function acceptedFriendsOfMine()
    {
        return $this->friendsOfMine()
            ->wherePivot('accepted', true);
    }

    public function acceptedFriendsOf()
    {
        return $this->friendsOf()
            ->wherePivot('accepted', true);
    }

    public function removeFriend(User $friend): void
    {
        // If I initiated the request / friendship
        $this->friendsOfMine()->detach($friend->id);

        // If the other person initiated the request / friendship
        $this->friendsOf()->detach($friend->id);
    }

    public function booksOfFriends()
    {
        return $this->hasManyDeepFromRelations($this->friends(), (new User())->books())
            ->withIntermediate(BookUser::class)
            ->orderBy('__book_user__updated_at', 'desc');
    }
}
