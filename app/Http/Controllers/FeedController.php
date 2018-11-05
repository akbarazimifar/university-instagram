<?php

namespace App\Http\Controllers;

use App\Media;
use App\UserRelationship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedController extends Controller
{
    public function homepage(Request $request)
    {
        if (!$this->checkUserHasFollowings()) {
            return response([
                'ok'          => false,
                'status_code' => 403,
                'description' => 'You have not followed anyone yet'
            ], 403);
        }
        return Media::select('medias.*')
            ->with([
                'user',
                'user.profile'
            ])
            ->withCount('likes')
            ->join('users', 'user_id', '=', 'users.id')
            ->join('users_relationships', 'users.id', '=', 'users_relationships.following_id')
            ->where('users_relationships.is_accepted', '=', true)
            ->orderByDesc('created_at')
            ->paginate();
    }

    protected function checkUserHasFollowings()
    {
        return (bool) UserRelationship::whereFollowerId(Auth::user()->id)
            ->whereIsAccepted(true)
            ->limit(1)
            ->count();
    }
}
