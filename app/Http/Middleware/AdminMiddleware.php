<?php

namespace App\Http\Middleware;

use Closure, Auth, Response;

class AdminMiddleware
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
        if(Auth::guard('admin')->check()) {

            if(Auth::guard('admin')->check() && Auth::guard('admin')->user()->role != ADMIN) {
                            
                return response()->view('unauthorized');

            }

        }

        return $next($request);
    }
}
