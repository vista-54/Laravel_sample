<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;

class OwnOrAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param $parameter
     * @return mixed
     * @throws \ErrorException
     */
    public function handle($request, Closure $next)
    {
        foreach ($request->route()->parameters() as $item) {

            if ($request->user()->role === User::ROLE_SUPER_ADMIN
                || $item->user_id == $request->user()->id) {
                return $next($request);
            }
        }
        throw new \ErrorException('Permission denied', 403);


    }
}
