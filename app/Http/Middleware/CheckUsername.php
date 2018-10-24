<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class CheckUsername
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->route('username')) {
            $username = $request->route('username');
            $user_eloquent = User::where('username', '=', $username);
            $request->attributes->add([
                'user_eloquent' => $user_eloquent
            ]);
            $user_object = $user_eloquent->firstOrFail();
            $request->attributes->add([
                'user_object' => $user_object
            ]);
        }
        return $next($request);
    }
}
