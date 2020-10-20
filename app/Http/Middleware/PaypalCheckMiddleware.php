<?php

namespace App\Http\Middleware;

use Closure;

class PaypalCheckMiddleware
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
        if (!envfile('PAYPAL_ID') || !envfile('PAYPAL_SECRET') || !envfile('PAYPAL_MODE')) {

            return redirect()->route('payment.failure');
        }

        if(!in_array(envfile('PAYPAL_MODE'), ['sandbox' , 'live'])) {

            return redirect()->route('payment.failure');
 
        }

        return $next($request);
    }
}
