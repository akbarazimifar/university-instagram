<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class CheckUsername
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->route('username')){
            $username = $request->route('username');
            User::where('username', '=', $username)->firstOrFail();
        }
        return $next($request);
    }
}
