<?php

namespace App\Http\Controllers;

use App\User;
use App\UserProfile;
use App\UserRelationship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Laravel\Passport\Passport;

class UserController extends Controller
{
    public function self()
    {
        return Auth::user();
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username'         => 'required|unique:users',
            'first_name'       => 'required|string',
            'last_name'        => 'required|nullable',
            'email'            => 'required|string|email|unique:users',
            'password'         => 'required|string',
            'password_confirm' => 'required|same:password'
        ]);
        if ($validator->fails()) return response(['error' => $validator->errors()], 401);
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        try {
            $user = User::create($input);
            Passport::actingAs($user);
            return response([
                'user'  => $user,
                'token' => $user->createToken(config('app.name'))->accessToken
            ], 200);
        } catch (\Exception $e) {
            return response(['error' => 'Could not create new user.', 'description' => $e->getMessage()], 500);
        }
    }

    public function logout()
    {
        try {
            if (Auth::check()) Auth::user()->OauthAcessToken()->delete();
            return response(['ok' => true], 200);
        } catch (\Exception $e) {
            return response(['ok' => false, 'description' => $e->getMessage()], 500);
        }
    }

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

    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile_photo'  => 'nullable|file|image|dimensions:min_width=200,min_height=200',
            'first_name'     => 'required|string|min:2|max:60',
            'last_name'      => 'nullable|string|min:2|max:60',
            'password'       => 'min:6|confirmed',
            'profile_status' => 'in:PRIVATE,PUBLIC'
        ]);
        if ($validator->fails()) {
            return response([
                'ok'          => false,
                'status_code' => 400,
                'description' => $validator->errors()
            ], 400);
        }

        $user = Auth::user();
        if ($request->hasFile('profile_photo')) {
            $file_path = Storage::put('public/profiles', $request->file('profile_photo'));
            $img = Image::make($request->file('profile_photo'));
            $width = $img->width();
            $height = $img->height();
            $img->fit(300, 300, function ($constraint) {
                $constraint->upsize();
            });
            $img->save('public/storage/profiles/thumbs/' . basename($file_path));
            $user_profile_data = [
                'user_id'    => $user->id,
                'thumb_path' => 'thumbs/' . basename($file_path),
                'file_path'  => basename($file_path),
                'width'      => $width,
                'height'     => $height
            ];
            UserProfile::updateOrCreate(['user_id' => $user->id], $user_profile_data);
        }
        if ($request->has('password')) {
            $user->password = Hash::make($request->get('password'));
            $user->save();
        }
        $user->update($request->only([
            'first_name', 'last_name', 'profile_status'
        ]));
        return response([
            'ok'          => true,
            'status_code' => 200,
            'description' => 'Profile updated.'
        ], 200);
    }
}
