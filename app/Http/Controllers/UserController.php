<?php

namespace App\Http\Controllers;

use App\User;
use App\UserRelationship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|min:3|max:255|string'
        ]);

        if ($validator->fails()) {
            return response([
                'ok'          => false,
                'status_code' => 400,
                'description' => $validator->messages()
            ], 400);
        }

        $query = $request->get('query');
        return User::where('username', 'LIKE', '%' . $query . '%')
            ->orWhere('first_name', 'LIKE', '%' . $query . '%')
            ->orWhere('last_name', 'LIKE', '%' . $query . '%')
            ->withCount(['followers', 'followings', 'medias'])
            ->with('profile')
            ->paginate(10);
    }

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
                $status_code = 200;
                $description = 'User successfully followed.';
            } else {
                $is_accepted = false;
                $status_code = 201;
                $description = 'The follow request has been sent.';
            }
            try {
                UserRelationship::create([
                    'follower_id'  => Auth::user()->id,
                    'following_id' => $user->id,
                    'is_accepted'  => $is_accepted
                ]);
                return response([
                    'ok'          => true,
                    'status_code' => $status_code,
                    'description' => $description
                ], $status_code);
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
        $user = $request->get('user_object');
        if ($user->id === Auth::user()->id) return response([
            'ok'          => false,
            'status_code' => 400,
            'description' => 'You can\'t unfollow your self.'
        ], 400);
        // check user is followed or not
        try {
            UserRelationship::where('follower_id', '=', Auth::user()->id)
                ->where('following_id', '=', $user->id)
                ->firstOrFail()
                ->delete();
            return response([
                'ok'          => true,
                'status_code' => 200,
                'description' => 'User successfully unfollowed.'
            ], 200);
        } catch (\Exception $e) {
            return response([
                'ok'          => false,
                'status_code' => 406,
                'description' => 'User it is not followed.'
            ], 406);
        }
    }

    public function followers(Request $request)
    {
        $user = $request->get('user_object');
        $result = User::checkUserViewPermission($user);
        if (!empty($result)) return $result;
        return User::select('users.*')
            ->with(['profile'])
            ->join('users_relationships', 'users.id', '=', 'users_relationships.follower_id')
            ->where('users_relationships.following_id', '=', $user->id)
            ->where('users_relationships.is_accepted', '=', true)
            ->orderByDesc('id')
            ->paginate();
    }

    public function followings(Request $request)
    {
        $user = $request->get('user_object');
        $result = User::checkUserViewPermission($user);
        if (!empty($result)) return $result;
        return User::select('users.*')
            ->with(['profile'])
            ->join('users_relationships', 'users.id', '=', 'users_relationships.following_id')
            ->where('users_relationships.follower_id', '=', $user->id)
            ->where('users_relationships.is_accepted', '=', true)
            ->orderByDesc('id')
            ->paginate();
    }

    public function show(Request $request)
    {
        return $request->get('user_eloquent')
            ->withCount(['followers', 'followings', 'medias'])
            ->with('profile')
            ->first();
    }
}
