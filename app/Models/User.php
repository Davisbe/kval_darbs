<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_oauth'
    ];

    

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google_oauth',
        'pivot'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function role():HasOne
    {
        return $this->hasOne(Role::class);
    }

    public function location():HasMany {
        return $this->hasMany(Location::class);
    }

    public function grupa():BelongsToMany {
        return $this->belongsToMany(Grupa::class, 'lietotajsgrupa', 'user_id', 'grupa_id');
    }

    public function sazina():HasMany {
        return $this->hasMany(Sazina::class);
    }

    // The following methods and relaationship definitions are a result of
    // Laravel Eloquent ORM not supporting composite PKs.
    protected function friendsOfMine()
    {
        return $this->belongsToMany(User::class, 'friends', 'user1_id', 'user2_id')->withTimestamps();
    }

    protected function friendOf()
    {
        return $this->belongsToMany(User::class, 'friends', 'user2_id', 'user1_id')->withTimestamps();
    }

    /*
    ____________________________________________________________________________

    Methods for more specific queries
    ____________________________________________________________________________
    */
    
    // method that returns whether the current user is friends with another user
    // (checks if there is both a (user1_id, user2_id) and a (user2_id, user1_id) row in the friends table, in one query)
    public function isFriendsWith(User $otherUser)
    {
        return $this->friendsOfMine()->where('user2_id', $otherUser->id)->exists() && $this->friendOf()->where('user1_id', $otherUser->id)->exists();
    }


    public function hasSentFriendRequestTo(User $otherUser)
    {
        return $this->friendsOfMine()->where('user2_id', $otherUser->id)->exists();
    }

    public function hasSentFriendRequestToAndDenied(User $otherUser)
    {
        return $this->friendsOfMine()->where('user2_id', $otherUser->id)->where('denied', 1)->exists();
    }

    public function incomingFriendRequests()
    {
        return $this->friendOf()->where('denied', 0)->whereNotIn('user1_id', $this->friendsOfMine()->pluck('user2_id'))->get();
    }
    
    // method that sends a friend request to another user
    // (creates a row in the friends table, where the user1_id is the current user's id)
    public function sendFriendRequestTo(User $otherUser)
    {
        $this->friendsOfMine()->attach($otherUser->id);
    }

    // method that accepts a friend request from another user
    // (creates a second row in the friends table, where the user1_id and user2_id are swapped)
    public function acceptFriendRequestFrom(User $otherUser)
    {
        $this->friendsOfMine()->attach($otherUser->id);
        $this->friendsOfMine()->updateExistingPivot($otherUser->id, ['denied' => 0]);
    }

    // method that denies a friend request from another user
    // (updates the denied column in the friends table)
    public function denyFriendRequestFrom(User $otherUser)
    {
        $this->friendsOfMine()->updateExistingPivot($otherUser->id, ['denied' => 1]);
    }

    // method that removes a friend from the friends table
    public function removeFriend(User $otherUser)
    {
        $this->friendsOfMine()->detach($otherUser->id);
        $this->friendOf()->detach($otherUser->id);
    }

    // get all friends (users) of the current user
    public function getFriends()
    {
        return $this->friendsOfMine()->wherePivot('denied', 0)->wherePivotIn('user2_id', $this->friendOf()->pluck('user1_id'))->get();
    }

    // get all games that the user has participated in, grouped by the start_time month and day
    public function gamesHistory()
    {
        return DB::table('speles')
            ->join('grupas', 'speles.id', '=', 'grupas.spele_id')
            ->join('lietotajsgrupa', 'grupas.id', '=', 'lietotajsgrupa.grupa_id')
            ->where('lietotajsgrupa.user_id', $this->id)
            ->where('lietotajsgrupa.active', 1)
            ->where('speles.end_time', '<', now())
            ->select('speles.name', 'speles.start_time', 'speles.picture')
            ->orderBy('speles.start_time', 'desc')
            ->get()
            ->groupBy(function ($val) {
                return \Carbon\Carbon::parse($val->start_time)->format('Y-m-d');
            });

    }
    



}
