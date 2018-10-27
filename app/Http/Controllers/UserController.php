<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function follow(Request $request)
    {
        $user = $request->get('user_object');
        if ($user->id === Auth::user()->id) return response('You can\'t follow your self.',400);

        // TODO check user is followed or not

        // TODO check if there is an ongoing follow request that need to be accepted or not

        // TODO insert follow data to user relationship table

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
