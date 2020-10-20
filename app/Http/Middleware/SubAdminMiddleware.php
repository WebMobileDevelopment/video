<?php

namespace App\Http\Middleware;

use Closure;

use Response;

class SubAdminMiddleware
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
        if ($request->user() && $request->user()->role != SUBADMIN) {

            return new Response(view('unauthorized')->with('role', SUBADMIN));
        }

        return $next($request);
    }
}
