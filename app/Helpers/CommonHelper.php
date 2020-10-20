<?php 

namespace App\Helpers;

use App\Requests;

use App\Helpers\CommonHelper;

use Hash, Auth, AWS, Mail ,File ,Log ,Storage ,Setting ,DB;

use App\Admin;

use App\User; 

class CommonHelper {

    // Note: $error is passed by reference
    
    public static function is_token_valid($entity, $id, $token, &$error) {

        if (
            ( $entity== USER && ($row = User::where('id', '=', $id)->where('token', '=', $token)->first()))
        ) {

            if ($row->token_expiry > time()) {
                // Token is valid
                $error = NULL;
                return true;
            } else {
                $error = ['success' => false, 'error' => CommonHelper::error_message(1003), 'error_code' => 1003];
                return FALSE;
            }
        }

        $error = ['success' => false, 'error' => CommonHelper::error_message(1004), 'error_code' => 1004];
        return FALSE;
   
    }

	public static function send_email($page,$subject,$email,$email_data) {

	    // check the email notification

	    if(Setting::get('email_notification') == YES) {

	        // Don't check with envfile function. Because without configuration cache the email will not send

	        if( config('mail.username') &&  config('mail.password')) {

	            try {

	                $site_url=url('/');

	                $isValid = 1;

	                if(envfile('MAIL_DRIVER') == 'mailgun' && Setting::get('MAILGUN_PUBLIC_KEY')) {

	                    Log::info("isValid - STRAT");

	                    # Instantiate the client.

	                    $email_address = new Mailgun(Setting::get('MAILGUN_PUBLIC_KEY'));

	                    $validateAddress = $email;

	                    # Issue the call to the client.
	                    $result = $email_address->get("address/validate", ['address' => $validateAddress]);

	                    # is_valid is 0 or 1

	                    $isValid = $result->http_response_body->is_valid;

	                    Log::info("isValid FINAL STATUS - ".$isValid);

	                }
                    
	                if($isValid) {

	                    if (Mail::queue($page, ['email_data' => $email_data,'site_url' => $site_url], 
	                            function ($message) use ($email, $subject) {

	                                $message->to($email)->subject($subject);
	                            }
	                    )) {

	                        $message = CommonHelper::success_message(102);

	                        $response_array = ['success' => true , 'message' => $message];

	                        return json_decode(json_encode($response_array));

	                    } else {

	                        throw new Exception(Helper::error_message(116) , 116);
	                        
	                    }

	                } else {

	                    $error = Helper::error_message();

	                    throw new Exception($error, 115);                  

	                }

	            } catch(\Exception $e) {

	                $error = $e->getMessage();

	                $error_code = $e->getCode();

	                $response_array = ['success' => false , 'error' => $error , 'error_code' => $error_code];
	                
	                return json_decode(json_encode($response_array));

	            }
	        
	        } else {

	            $error = Helper::get_error_message(106);

	            $response_array = ['success' => false , 'error' => $error , 'error_code' => 106];
	                
	            return json_decode(json_encode($response_array));

	        }
	    
	    } else {
	        Log::info("email notification disabled by admin");
	    }

	}

	public static function error_message($code , $test ="") {

        switch($code) {
            
            case 101:
                $string = apitr('invalid_input');
                break;
            case 102:
                $string = apitr('username_password_not_match');
                break;
            case 103:
                $string = apitr('user_details_not_save');
                break;
            case 104: 
                $string = apitr('invalid_email_address');
                break;
            case 105: 
                $string = apitr('mail_send_failure');
                break;
            case 106: 
                $string = apitr('mail_not_configured');
                break;
            case 107:
                $string = apitr('stripe_not_configured');
                break;
            case 108:
                $string = apitr('password_not_correct');
                break;
            case 109:
                $string = apitr('user_no_payment_mode');
                break;
            case 110:
                $string = apitr('user_payment_not_saved');
                break;
            case 111:
                $string = apitr('no_default_card');
                break;
            case 112:
                $string = apitr('no_default_card'); // not used
                break;
            case 113:
                $string = apitr('stripe_payment_not_configured');
                break;
            case 114:
                $string = apitr('stripe_payment_failed');
                break;
            case 115:
                $string = apitr('stripe_payment_card_add_failed');
                break;
            case 116:
                $string = apitr('user_forgot_password_deny_for_social_login');
                break;
            case 117:
                $string = apitr('forgot_password_email_verification_error');
                break;
            case 118:
                $string = apitr('forgot_password_decline_error');
                break;
            case 119:
                $string = apitr('user_change_password_deny_for_social_login');
                break;
            case 200:
                $string = apitr('video_tape_not_found');
                break;
            case 201:
                $string = apitr('provider_details_not_found'); // Not used
                break;
            case 202:
                $string = apitr('invalid_request_input'); // Not used
                break;
            case 203:
                $string = apitr('subscription_not_found');
                break;
            case 204:
                $string = apitr('subscription_payment_error');
                break;
            case 205:
                $string = apitr('coupon_code_not_found');
                break;
            case 206:
                $string = apitr('coupon_code_expired');
                break;
            case 207:
                $string = apitr('coupon_code_limit_exceeds');
                break;
            case 208:
                $string = apitr('coupon_code_per_user_limit_exceeds');
                break;
            case 209:
                $string = apitr('wishlist_delete_error');
                break;
            case 210:
                $string = apitr('redeem_disabled_by_admin');
                break;
            case 211:
                $string = apitr('redeem_not_found');
                break;
            case 212:
                $string = apitr('redeem_wallet_empty');
                break;
            case 213:
                $string = apitr('redeem_minimum_limit_failed');
                break;
            case 214:
                $string = apitr('redeem_request_status_mismatch');
                break;
            case 215:
                $string = apitr('spam_video_add_failed');
                break;
            case 216:
                $string = apitr('spam_video_remove_failed');
                break;
            case 217:
                $string = apitr('video_comment_failed');
                break;
            case 218:
                $string = apitr('subscription_autorenewal_pause_failed');
                break;
            case 219:
                $string = apitr('subscription_autorenewal_enable_failed');
                break;
            case 220:
                $string = apitr('subscription_payment_details_not_found');
                break;
            case 221:
                $string = apitr('subscription_autorenewal_already_paused');
                break;
            case 222:
                $string = apitr('subscription_autorenewal_already_enabled');
                break;
            case 223:
                $string = apitr('channel_not_found');
                break;
            case 224:
                $string = apitr('video_tape_in_spam_list');
                break;
            case 225:
                $string = apitr('channel_create_purchase_subscription_error');
                break;
            

            // USE BELOW CONSTANTS FOR AUTHENTICATION CHECK
            case 1000:
                $string = apitr('user_login_decline');
                break;
            case 1001:
                $string = apitr('user_not_verified');
                break;
            case 1002:
                $string = apitr('user_details_not_found');
                break;
            case 1003:
                $string = apitr('token_expiry');
                break;
            case 1004:
                $string = apitr('invalid_token');
                break;
            case 1005:
                $string = apitr('without_id_token_user_accessing_request');
                break;

            default:
                $string = apitr('unknown_error_occured');
        }

        return $string;
    
    }

    public static function success_message($code) {

        switch($code) {
            case 101:
                $string = apitr('login_success');
                break;
            case 102:
                $string = apitr('mail_sent_success');
                break;
            case 103:
                $string = apitr('account_delete_success');
                break;
            case 104:
                $string = apitr('password_change_success');
                break;
            case 105:
                $string = apitr('card_added_success');
                break;
            case 106:
                $string = apitr('logout_success');
                break;
            case 107:
                $string = apitr('card_deleted_success');
                break;
            case 108:
                $string = apitr('card_default_success');
                break;  
            case 109:
                $string = apitr('user_payment_mode_update_success');
                break;
            case 200:
                $string = apitr('wishlist_add_success');
                break;
            case 201:
                $string = apitr('wishlist_delete_success');
                break;
            case 202:
                $string = apitr('wishlist_clear_success');
                break;
            case 203:
                $string = apitr('not_used');
                break;
            case 204:
                $string = apitr('bell_notification_updated');
                break;
            case 205: 
                $string = apitr('subscription_payment_success');
                break;
            case 206: 
                $string = apitr('notification_enable');
                break;
            case 207: 
                $string = apitr('notification_disable');
                break;
            case 208: 
                $string = apitr('history_video_added');
                break;
            case 209: 
                $string = apitr('history_video_tape_removed');
                break;
            case 210: 
                $string = apitr('history_cleared');
                break;
            case 211: 
                $string = apitr('redeem_request_cancelled_success');
                break;
            case 212: 
                $string = apitr('redeem_request_send_success');
                break;
            case 213: 
                $string = apitr('spam_video_added');
                break;
            case 214: 
                $string = apitr('spam_video_removed');
                break;
            case 215: 
                $string = apitr('spam_video_cleared');
                break;
            case 216: 
                $string = apitr('ppv_is_not_enabled');
                break;
            case 217: 
                $string = apitr('ppv_channel_owner_no_need_to_pay');
                break;
            case 218: 
                $string = apitr('ppv_already_paid');
                break;
            case 219: 
                $string = apitr('ppv_payment_success');
                break;
            case 220: 
                $string = apitr('coupon_code_appiled');
                break;
            case 221: 
                $string = apitr('channel_subscribed');
                break;
            case 222: 
                $string = apitr('channel_unsubscribed');
                break;
            case 223: 
                $string = apitr('video_comment_success');
                break;
            case 224:
                $string = apitr('subscription_autorenewal_paused');
                break;
            case 225:
                $string = apitr('subscription_autorenewal_enabled');
                break;
            case 226:
                $string = apitr('video_tape_liked');
                break;
            case 227:
                $string = apitr('video_tape_like_removed');
                break;
            case 228:
                $string = apitr('video_tape_disliked');
                break;
            case 229:
                $string = apitr('video_tape_dislike_removed');
                break;

            default:
                $string = "";
        
        }
        
        return $string;
    
    }


}