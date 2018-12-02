<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Http\JsonResponse;

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
            try{
                $username = $request->route('username');
                $user_eloquent = User::where('username', '=', $username);
                $request->attributes->add([
                    'user_eloquent' => $user_eloquent
                ]);
                $user_object = $user_eloquent->firstOrFail();
                $request->attributes->add([
                    'user_object' => $user_object
                ]);
            }catch(\Exception $e){
                // return new JsonResponse([
                //     'ok' => false,
                //     'description' => $e->getMessage(),
                //     'details'    => $e->getTrace()
                // ], 403);
                return new JsonResponse([
                    'ok' => false,
                    'status_code' => 404,
                    'description' => 'Could not find username'
                ], 404);
            }
        }
        return $next($request);
    }
}
