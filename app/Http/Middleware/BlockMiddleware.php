<?php

namespace App\Http\Middleware;

use Closure;

class BlockMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next)
    {
        if (auth()->user()->block === 1) {
            throw new \Exception('You are blocked.', 401);
        }

        return $next($request);
    }
}
