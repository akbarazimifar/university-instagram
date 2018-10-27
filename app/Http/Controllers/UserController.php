<?php

namespace App\Http\Controllers;

use App\UserRelationship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function follow(Request $request)
    {
        $user = $request->get('user_object');
        if ($user->id === Auth::user()->id) return response([
            'ok'          => false,
            'status_code' => 400,
            'description' => 'You can\'t follow your self.'
        ], 400);
        // check user is followed or not
        try {
            $relationship = UserRelationship::where('follower_id', '=', Auth::user()->id)
                ->where('following_id', '=', $user->id)
                ->firstOrFail();
            if ($relationship->is_accepted) {
                return response([
                    'ok'          => true,
                    'status_code' => 200,
                    'description' => 'User is already followed.'
                ], 200);
            } else {
                return response([
                    'ok'          => true,
                    'status_code' => 202,
                    'description' => 'There is an ongoing follow request that need to be accepted.'
                ], 202);
            }
        } catch (\Exception $e) {
            if ($user->profile_status === "PUBLIC") {
                $is_accepted = true;
            } else {
                $is_accepted = false;
            }
            try {
                UserRelationship::create([
                    'follower_id'  => Auth::user()->id,
                    'following_id' => $user->id,
                    'is_accepted'  => $is_accepted
                ]);
                return response([
                    'ok'          => true,
                    'status_code' => 201,
                    'description' => 'The follow request has been sent.'
                ], 201);
            } catch (\Exception $e) {
                return response([
                    'ok'          => false,
                    'status_code' => 500,
                    'description' => 'There is a problem with the follow request'
                ], 500);
            }
        }
    }


    public function unFollow(Request $request)
    {

    }

    public function show(Request $request)
    {
        return $request->get('user_eloquent')
            ->withCount(['followers', 'followings', 'medias'])
            ->with('profile')
            ->first();
    }
}
