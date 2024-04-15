<?php

namespace App\Http\Middleware;

use Closure;

class IsAdmin
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
        $isAdmin = $request->user() && $request->user()->is_admin;

        if (!$isAdmin) {
            return response()->json([], 403);
        }

        return $next($request);
    }
}
