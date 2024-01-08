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

use App\Models\Grupa;

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

    public function role():BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'users_roles', 'user_id', 'role_id')->withTimestamps();
    }

    public function location():HasMany {
        return $this->hasMany(Location::class);
    }

    public function grupa():BelongsToMany {
        return $this->belongsToMany(Grupa::class, 'lietotajsgrupa', 'user_id', 'grupa_id')->withTimestamps();
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

    // get all incoming friend requests to the current user
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
        return $this->friendsOfMine()
            ->wherePivot('denied', 0)
            ->wherePivotIn('user2_id', $this
                ->friendOf()->pluck('user1_id'))
            ->get();
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
            ->select('speles.id', 'speles.name', 'speles.start_time', 'speles.picture')
            ->orderBy('speles.start_time', 'desc')
            ->get()
            ->groupBy(function ($val) {
                return \Carbon\Carbon::parse($val->start_time)->format('Y-m-d');
            });

    }
    
    // get all group invites to games that user has recieved
    public function groupInvites()
    {
        return $this->grupa()->wherePivot('uzaicinats', 1)
            ->wherePivot('apstiprinats', -1)
            ->leftJoin('lietotajsgrupa as lietotajsgrupa2', 'lietotajsgrupa2.grupa_id', '=', 'grupas.id')
            ->leftJoin('users as inviter', 'inviter.id', '=', 'lietotajsgrupa2.user_id')
            ->where('inviter.id', '!=', $this->id)
            ->where('lietotajsgrupa2.uzaicinats', 0)
            ->select('grupas.id', 'inviter.name as inviter_name', 'lietotajsgrupa.created_at')
            ->orderBy('lietotajsgrupa.created_at', 'asc')
            ->get();
    }

    // get all group invites to a specific game, that user has recieved
    public function groupInvitesToGame($game_id)
    {
        return $this->grupa()->wherePivot('uzaicinats', 1)
            ->where('spele_id', $game_id)
            ->wherePivot('apstiprinats', -1)
            ->leftJoin('lietotajsgrupa as lietotajsgrupa2', 'lietotajsgrupa2.grupa_id', '=', 'grupas.id')
            ->leftJoin('users as inviter', 'inviter.id', '=', 'lietotajsgrupa2.user_id')
            ->where('inviter.id', '!=', $this->id)
            ->where('lietotajsgrupa2.uzaicinats', 0)
            ->select('grupas.id', 'inviter.name as inviter_name', 'lietotajsgrupa.created_at')
            ->orderBy('lietotajsgrupa.created_at', 'asc')
            ->get();
    }

    // get all the user's group members from a specific game
    public function getMyGroupMembersFromGame($game_id) {
        return $this->grupa()
            ->where('spele_id', $game_id)
            ->wherePivot('apstiprinats', 1)
            ->leftJoin('lietotajsgrupa as lietotajsgrupa2', 'lietotajsgrupa2.grupa_id', '=', 'grupas.id')
            ->leftJoin('users as members', 'members.id', '=', 'lietotajsgrupa2.user_id')
            ->where('lietotajsgrupa2.apstiprinats', 1)
            ->select('members.id', 'members.name', 'members.profile_picture', 'lietotajsgrupa2.uzaicinats', 'lietotajsgrupa2.active')
            ->orderBy('name', 'asc')
            ->get();
    }

    // get all the user's group members from a specific game
    // for ajax polling - less data is returned
    public function getUpdatedMembersFromGame($game_id) {
        return $this->grupa()
            ->where('spele_id', $game_id)
            ->wherePivot('apstiprinats', 1)
            ->leftJoin('lietotajsgrupa as lietotajsgrupa2', 'lietotajsgrupa2.grupa_id', '=', 'grupas.id')
            ->leftJoin('users as members', 'members.id', '=', 'lietotajsgrupa2.user_id')
            ->where('lietotajsgrupa2.apstiprinats', 1)
            ->select('members.name', 'members.profile_picture', 'lietotajsgrupa2.uzaicinats', 'lietotajsgrupa2.active')
            ->orderBy('name', 'asc')
            ->get();
    }

    // check if the user is part of a group in a specific game
    public function isPartOfAGroup($game_id) {
        return $this->grupa()
            ->where('spele_id', $game_id)
            ->wherePivot('apstiprinats', 1)
            ->exists();
    }

    public function getFriendsExcludingAlreadyInGroup($game_id) 
    {
        $groupMemberIds = $this->getMyGroupMembersFromGame($game_id)->pluck('id')->toArray();

        return $this->friendsOfMine()
                    ->wherePivot('denied', 0)
                    ->wherePivotIn('user2_id',
                        $this->friendOf()
                        ->pluck('user1_id'))
                    ->whereNotIn('id', $groupMemberIds)
                    ->get();
    }

    // create a new group in a specific game
    public function createGroupInGame($game_id) {
        // prevent user from being in 2 groups
        $this->leaveGroupFromGame($game_id);

        $new_group = $this->grupa()->create(['spele_id' => $game_id]);
        $new_group->user()->updateExistingPivot($this->id, ['uzaicinats' => '0', 'apstiprinats' => '1']);
    }

    // deny a group invite
    public function denyGroupInvite($group_id) {
        $this->grupa()->updateExistingPivot($group_id, ['uzaicinats' => '1', 'apstiprinats' => '0']);
    }

    // accept a group invite
    public function acceptGroupInvite($group_id) {

        // if the group already has 5 members, remove the invite
        if ($this->grupa($group_id)->where('apstiprinats', 1)->count() >= 5) {
            $this->grupa()->detach($group_id);
            return;
        }

        $this->grupa()->updateExistingPivot($group_id, ['uzaicinats' => '1', 'apstiprinats' => '1']);
    }

    // leave a group. If the user is the last member of the group, the group is deleted. The game ID is passed
    // to the function instead of the group ID, because the group ID is not known in all cases.
    public function leaveGroupFromGame($game_id) {
        $group_user_connection = $this->grupa()
            ->where('spele_id', $game_id)
            ->wherePivot('apstiprinats', 1)
            ->first();
        
        if (!$group_user_connection) {
            return false;
        }
        
        // select the group
        $group = Grupa::where('id', $group_user_connection->id)
            ->first();
        
        // select all relationships from the group-users pivot table
        $group_all_user_connections = $group->user();

        // select group member relationships from the group-users pivot table
        $group_members = $group->user()->where('apstiprinats', 1);

        // if the user is the last member of the group, delete the group and the pivot
        if ($group_members->count() == 1) {
            $group_all_user_connections->detach();
            $group->delete();
        } else {
            // find the current group leader
            $group_leader = clone $group_members;
            $group_leader = $group_leader->where('uzaicinats', 0)->first();
            
            // if current user is the group leader, assign a new leader
            if ($group_leader->id == $this->id) {
                // find leader candidate
                $new_group_leader = clone $group_members;
                $new_group_leader = $new_group_leader
                    ->where('uzaicinats', 1)
                    ->orderBy('pivot_created_at', 'asc')
                    ->first();
                
                // assign new leader
                $group_all_user_connections
                    ->updateExistingPivot($new_group_leader->id, ['uzaicinats' => 0]);
            }

            // detach the user from the group
            $this->grupa()->where('spele_id', $game_id)->detach();
            
        }

        return true;
    }


    // same thing as leaveGroupFromGame() but use the group id as a parameter
    public function leaveGroup($group_id) {
        $group_user_connection = $this->grupa()
            ->where('id', $group_id)
            ->wherePivot('apstiprinats', 1)
            ->first();
        
        if (!$group_user_connection) {
            return false;
        }
        
        // select the group
        $group = Grupa::where('id', $group_user_connection->id)
            ->first();
        
        // select all relationships from the group-users pivot table
        $group_all_user_connections = $group->user();

        // select group member relationships from the group-users pivot table
        $group_members = $group->user()->where('apstiprinats', 1);

        // if the user is the last member of the group, delete the group and the pivot
        if ($group_members->count() == 1) {
            $group_all_user_connections->detach();
            $group->delete();
        } else {
            // find the current group leader
            $group_leader = clone $group_members;
            $group_leader = $group_leader->where('uzaicinats', 0)->first();
            
            // if current user is the group leader, assign a new leader
            if ($group_leader->id == $this->id) {
                // find leader candidate
                $new_group_leader = clone $group_members;
                $new_group_leader = $new_group_leader
                    ->where('uzaicinats', 1)
                    ->orderBy('pivot_created_at', 'asc')
                    ->first();
                
                // assign new leader
                $group_all_user_connections
                    ->updateExistingPivot($new_group_leader->id, ['uzaicinats' => 0]);
            }

            // detach the user from the group
            $this->grupa()->where('id', $group_id)->detach();
            
        }

        return true;
    }

    public function inviteUserToGroup($group_id, $user_name) {
        $group = Grupa::where('id', $group_id)->first();
        $invited_user = User::where('name', $user_name)->firstOrFail();

        $user_group_connection = clone $invited_user;
        $user_group_connection = $user_group_connection->grupa()
            ->wherePivot('grupa_id', $group_id)
            ->first();

        if (!$user_group_connection) {
            if ($this->isFriendsWith($invited_user)) {
                $group->user()->attach($invited_user->id);
            }
        }

    }

    // check if user is active in a game
    // returns game id if user is active in a game, 0 otherwise
    public function checkIfPlayingGame() {
        $active_games = Spele::where('start_time', '<', now())
            ->where('end_time', '>', now())
            ->pluck('id')
            ->toArray();
        
        $user_active_games = $this->grupa()
            ->whereIn('spele_id', $active_games)
            ->wherePivot('active', 1)
            ->first();

        if ($user_active_games) {
            return $user_active_games->spele_id;
        } else {
            return 0;
        }

    }

    public function isAdmin() {
        return $this->role()->where('role', 'admin')->exists();
    }

    // set user as not ready for any group in any game
    // if user actually marked as ready in some group
    public function setNotReadyForAllGroups() {
        $this->grupa()->updateExistingPivot(null, ['active' => -1]);
    }

    public function getTwoRecentLocations() {
        return $this->location()
            ->where('created_at', '>=', now()->subMinutes(5))
            ->limit(2)
            ->get();
    }


}
