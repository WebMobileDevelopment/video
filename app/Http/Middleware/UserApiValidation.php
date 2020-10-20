<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Http\Request;

use App\Helpers\Helper;

use Validator;

use Log;

use App\User;

use DB;

use Setting;

class UserApiValidation
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
        $validator = Validator::make(
                $request->all(),
                array(
                        'token' => 'required|min:5',
                        'id' => 'required|integer|exists:users,id'
                ));

        if ($validator->fails()) {

            $error_messages = implode(',', $validator->messages()->all());

            $response = array('success' => false, 'error' => Helper::get_error_message(9001), 'error_code' => 9001, 'error_messages'=>$error_messages);

            return response()->json($response,200);

        } else {

            $token = $request->token;

            $user_id = $request->id;

            if (!Helper::is_token_valid('USER', $user_id, $token, $error)) {

                $response = response()->json($error, 200);

                return $response;

            } else {

                $user_details = User::find($request->id);

                // if(!$user_details) {
                    
                //     $response = array('success' => false , 'error_messages' => Helper::get_error_message(133) , 'error_code' => 133);
                //     return response()->json($response, 200);
                // }


                if(!$user_details) {
                    
                    $response = array('success' => false , 'error_messages' => Helper::get_error_message(504) , 'error_code' => 504);
                    return response()->json($response, 200);
                }

                if($user_details->is_verified == USER_EMAIL_NOT_VERIFIED) {

                    if(Setting::get('email_verify_control') && !in_array($user_details->login_by, ['facebook' , 'google'])) {

                        // Check the verification code expiry

                        Helper::check_email_verification("" , $user_details, $error, USER);
                    
                        $response = array('success' => false , 'error_messages' => Helper::get_error_message(503) , 'error_code' => 503);

                        return response()->json($response, 200);

                    }
                }

                if($user_details->status == USER_DECLINED) {
                    
                    $response = array('success' => false , 'error_messages' => Helper::get_error_message(502) , 'error_code' => 502);
                    return response()->json($response, 200);
                }

                
            }
        }

        return $next($request);
    }
}
