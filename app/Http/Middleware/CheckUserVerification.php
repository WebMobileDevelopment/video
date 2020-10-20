<?php

namespace App\Http\Middleware;

use Closure;

use App\Helpers\Helper;

use Auth;

use Setting;

class CheckUserVerification
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
        if(\Auth::check()) {

            $token = Auth::user()->token;

            $user_id = Auth::user()->id;

            // 

            if (!Helper::is_token_valid('USER', $user_id, $token, $error)) {

                Auth::logout();

                $messages = isset($error['error_messages']) ? $error['error_messages'] : tr('unauthroized_person');
                    
                return back()->with('flash_error', $messages);

            }

            // check the status of the user

            if(Auth::user()->status == USER_DECLINED) {

                Auth::logout();
                    
                return back()->with('flash_error', Helper::get_error_message(502));

            }

            // check the email verification

            if(Auth::user()->is_verified == USER_EMAIL_NOT_VERIFIED) {

                if(Setting::get('email_verify_control') && !in_array(Auth::user()->login_by, ['facebook' , 'google'])) {

                    Auth::logout();

                    // Check the verification code expiry

                    Helper::check_email_verification("" , Auth::user(), $error, USER);
                
                    return back()->with('flash_error', Helper::get_error_message(503));

                }
            
            }
        }

        return $next($request);
    }
}
