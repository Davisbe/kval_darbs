<?php

namespace App\Http\Controllers;

use App\Models\Grupa;
use Illuminate\Http\Request;
use App\Models\Spele;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use Exception;

class Group extends Controller
{
    public function games_group($game_id) {
        $game_info = Spele::select('id', 'name', 'picture', 'start_time', 'end_time')
            ->findOrFail($game_id);

        // Prevent users joining new groups if game has ended
        if ($game_info->end_time < now()) {
            return redirect()->route('game.show', ['id' => $game_id]);
        }

        $auth_user = User::where('name', Auth::user()->name)
                ->select(['id', 'name'])
                ->firstOrFail();
        
        if (!$auth_user->isPartOfAGroup($game_id)) {
            $auth_user->createGroupInGame($game_id);
        }

        $group_user_connection = $auth_user->grupa()
            ->where('spele_id', $game_id)
            ->wherePivot('apstiprinats', 1)
            ->firstOrFail();
        
        $group = Grupa::where('id', $group_user_connection->id)
            ->firstOrFail();
        
        if ($group->isGroupReady()) {
            $group->setMemberActive($auth_user->name);
            return redirect()->route('active_game.index', ['id' => $game_id]);
        }

        $group_members = $auth_user->getMyGroupMembersFromGame($game_id);

        // could just use getGroupLeader method, but it's
        // going to be another SELECT query then
        $group_leader = [];

        $is_user_ready = $group->isMemberReady($auth_user->name);

        foreach ($group_members as $member) {
            if ($member->uzaicinats == 0) {
                $group_leader = $member;
            }
            $member->profile_picture = asset($member->profile_picture);
            $member->profile_link = route('profile.show', ['name' => $member->name]);
        }

        $friendlist_invitable = $auth_user->getFriendsExcludingAlreadyInGroup($game_id);
        foreach ($friendlist_invitable as $friend) {
            $friend->profile_picture = asset($friend->profile_picture);
            $friend->profile_link = route('profile.show', ['name' => $friend->name]);
        }

        return view('game_pages/games_group', compact('is_user_ready', 'group_members', 'game_info', 'group_leader', 'friendlist_invitable'));
    }

    public function group_deny($group_id) {
        $auth_user = User::where('name', Auth::user()->name)
                ->select(['id', 'name'])
                ->firstOrFail();

        $auth_user->denyGroupInvite($group_id);

        return redirect()->route('game.show', ['id' => $group_id]);
    }

    public function group_accept($group_id) {
        $auth_user = User::where('name', Auth::user()->name)
                ->select(['id', 'name'])
                ->firstOrFail();
            
        $game_id = Grupa::where('id', $group_id)
            ->select('spele_id')
            ->firstOrFail()
            ->spele_id;
        
        $game = Spele::where('id', $game_id)
        ->firstOrFail();
        
        // Prevent users joining new groups if game has ended
        if ($game->end_time < now()) {
            return redirect()->route('game.show', ['id' => $game_id]);
        }
        
        // leave group if already in one
        $auth_user->leaveGroupFromGame($game_id);

        $auth_user->acceptGroupInvite($group_id);

        return redirect()->route('game.group', ['id' => $game_id]);
    }

    public function group_leave($game_id) {
        $game = Spele::where('id', $game_id)
            ->firstOrFail();
        
        // Prevent users leaving their group if game has ended
        if ($game->end_time < now()) {
            return redirect()->route('game.show', ['id' => $game_id]);
        }

        $auth_user = User::where('name', Auth::user()->name)
                ->select(['id', 'name'])
                ->firstOrFail();

        $auth_user->leaveGroupFromGame($game_id);

        return redirect()->route('game.show', ['id' => $game_id]);
    }

    public function remove_member($game_id, $name) {
        $game = Spele::where('id', $game_id)
            ->firstOrFail();
        
        // Prevent users kicking eachother if game has ended
        if ($game->end_time < now()) {
            return redirect()->route('game.show', ['id' => $game_id]);
        }

        $auth_user = User::where('name', Auth::user()->name)
                ->select(['id', 'name'])
                ->firstOrFail();
        
        $group_user_connection = $auth_user->grupa()
            ->where('spele_id', $game_id)
            ->wherePivot('apstiprinats', 1)
            ->firstOrFail();
        
        $group = Grupa::where('id', $group_user_connection->id)
            ->firstOrFail();
        
        $group_leader = $group->getGroupLeader();

        if ($auth_user->name == $group_leader->name) {
            $group->removeGroupMember($name);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }


    public function invite_friend($game_id, $name) {
        $game = Spele::where('id', $game_id)
            ->firstOrFail();
        
        // Prevent users inviting other users if game has ended
        if ($game->end_time < now()) {
            return redirect()->route('game.show', ['id' => $game_id]);
        }

        $auth_user = User::where('name', Auth::user()->name)
                ->select(['id', 'name'])
                ->firstOrFail();
        
        $group_user_connection = $auth_user->grupa()
            ->where('spele_id', $game_id)
            ->wherePivot('apstiprinats', 1)
            ->wherePivot('uzaicinats', 0)   // gotta check if auth_user is group leader
            ->firstOrFail();
        
        $group = Grupa::where('id', $group_user_connection->id)
            ->firstOrFail();

        $auth_user->inviteUserToGroup($group->id, $name);

        return response()->json(['success' => true]);

    }

    public function toggle_user_ready($game_id) {
        $auth_user = User::where('name', Auth::user()->name)
                ->select(['id', 'name'])
                ->firstOrFail();
        
        $group_user_connection = $auth_user->grupa()
            ->where('spele_id', $game_id)
            ->wherePivot('apstiprinats', 1)
            ->firstOrFail();
        
        $group = Grupa::where('id', $group_user_connection->id)
            ->firstOrFail();
        
        if (!$group->isMemberReady($auth_user->name)) {
            // set member to not ready so 2 active games
            // are not possible for a user
            $auth_user->setNotReadyForAllGroups();

            $group->setMemberReady($auth_user->name);
        } else {
            $group->setMemberNotReady($auth_user->name);
        }

        return response()->json(['success' => true]);
    }

    // ajax poll for when game starts
    public function poll_users_ready($game_id) {
        $auth_user = User::where('name', Auth::user()->name)
            ->select(['id', 'name'])
            ->firstOrFail();
        
        $group_user_connection = $auth_user->grupa()
            ->where('spele_id', $game_id)
            ->wherePivot('apstiprinats', 1)
            ->firstOrFail();
        
        $group = Grupa::where('id', $group_user_connection->id)
            ->first();
        

        if (!$auth_user->isPartOfAGroup($game_id)) {
            $redirect_link_kicked = route('game.show', ['id' => $game_id]);
            return response()->json(['success' => false, 'redirect_link'=>$redirect_link_kicked]);
        }

        if ($group->isGroupReady()) {
            $redirect_link_allready = route('active_game.index');
            $group->setMemberActive($auth_user->name);
            return response()->json(['success' => false, 'redirect_link_allready'=>$redirect_link_allready]);
        }
        
        $group_members_updated = $auth_user->getUpdatedMembersFromGame($game_id);

        foreach ($group_members_updated as $member) {
            $member->profile_picture = asset($member->profile_picture);
            $member->profile_link = route('profile.show', ['name' => $member->name]);
            unset($member->pivot);
        }
        
        return response()->json(['success' => true, 'group_members'=>$group_members_updated]);
    }
}
