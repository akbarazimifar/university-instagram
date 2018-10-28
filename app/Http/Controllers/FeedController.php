<?php

namespace App\Http\Controllers;

use App\UserRelationship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedController extends Controller
{
    public function homepage(Request $request)
    {
        if (!$this->checkUserHasFollowings()) return response([
            'ok'          => false,
            'status_code' => 403,
            'description' => 'You have not followed anyone yet'
        ], 403);

        // TODO: complete this
    }

    protected function checkUserHasFollowings()
    {
        return (boolean) UserRelationship::whereFollowerId(Auth::user()->id)
            ->whereIsAccpeted(true)
            ->limit(1)
            ->count();
    }
}
