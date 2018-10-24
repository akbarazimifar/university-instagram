<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function show(string $username)
    {
        return User::where('username', '=', $username)->firstOrFail();
    }
}
