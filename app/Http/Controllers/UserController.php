<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function follow(Request $request)
    {

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
