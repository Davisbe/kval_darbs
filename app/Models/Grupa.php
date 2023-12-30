<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Grupa extends Model
{
    use HasFactory;

    protected $table = 'grupas';
    protected $fillable = [
        'spele_id'
    ];

    public function vieta():BelongsToMany
    {
        return $this->belongsToMany(Vieta::class, 'atrastavieta', 'grupa_id', 'vieta_id')->withTimestamps();
    }

    public function user():BelongsToMany {
        return $this->belongsToMany(User::class, 'lietotajsgrupa', 'grupa_id', 'user_id')->withTimestamps();
    }

    public function spele():BelongsTo {
        return $this->belongsTo(Spele::class);
    }

    public function getGroupLeader() {
        return $this->user()
            ->where('apstiprinats', 1)
            ->where('uzaicinats', 0)
            ->first();
    }

    public function removeGroupMember($name) {
        $user_to_be_kicked = $this->user()
            ->where('users.name', $name)
            ->first();

        $group_user_connection = $this->user()
            ->wherePivot('apstiprinats', 1)
            ->wherePivot('user_id', $user_to_be_kicked->id)
            ->first();
        
        $game = $this->spele()
            ->first();
        
        if (!$game->IsTimeBetweenStartAndEnd()) {
            return false;
        }

        if (!$group_user_connection) {
            return false;
        }
        
        $this->user()->detach($user_to_be_kicked->id);

        return true;
    }

    // Set user as ready to play
    public function setMemberReady($name) {
        $user = $this->user()
            ->where('users.name', $name)
            ->first();

        $group_user_connection = $this->user()
            ->wherePivot('apstiprinats', 1)
            ->wherePivot('user_id', $user->id)
            ->withPivot('active')
            ->first();

        $game = $this->spele()
        ->first();
        
        
        if (!$game->IsTimeBetweenStartAndEnd()) {
            return false;
        }
        
        if (!$group_user_connection) {
            return false;
        }

        if ($group_user_connection->pivot->active != 0) {
            $this->user()->updateExistingPivot($user->id, ['active' => 0]);
        }
        
        return true;
    }

    // Unset user as ready to play
    public function setMemberNotReady(string $name) {
        $user = $this->user()
            ->where('users.name', $name)
            ->first();

        $group_user_connection = $this->user()
            ->wherePivot('apstiprinats', 1)
            ->wherePivot('user_id', $user->id)
            ->first();
        
        $game = $this->spele()
        ->first();
        
        if (!$game->IsTimeBetweenStartAndEnd()) {
            return false;
        }
        
        if (!$group_user_connection) {
            return false;
        }

        $group_user_connection->pivot->active = -1;
        $group_user_connection->pivot->save();

        return true;
    }

    // Check if user is ready to play
    // !! 
    //    DO NOT check IsTimeBetweenStartAndEnd for the game here
    //    otherwise some places like Group controller, toggle_user_ready method
    //    will fall into an infinate loop
    // !!
    public function isMemberReady(string $name) {
        $user = $this->user()
            ->where('users.name', $name)
            ->first();

        $group_user_connection = $this->user()
            ->wherePivot('apstiprinats', 1)
            ->wherePivot('user_id', $user->id)
            ->withPivot('active')
            ->first();
        
        if (!$group_user_connection) {
            return false;
        }

        if ($group_user_connection->pivot->active == 0) {
            return true;
        }

        return false;
    }

    // Check if all group members are ready to play
    public function isGroupReady() {
        $group_members = $this->user()
            ->wherePivot('apstiprinats', 1)
            ->get();

        foreach ($group_members as $group_member) {
            if ($group_member->pivot->active != 0) {
                return false;
            }
        }

        return true;
    }
}
