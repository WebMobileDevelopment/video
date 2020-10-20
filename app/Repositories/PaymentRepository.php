<?php

/**
|--------------------------------------------------------------------------
| PaymentRepository
|--------------------------------------------------------------------------
|
| @uses This repository used to do all functions related payments.
|
| @author vidhyar2612
|
| Date Created: 30 Dec 2017
*/
   
namespace App\Repositories;

use Illuminate\Http\Request;

use App\Helpers\Helper, App\Helpers\CommonHelper;

use Validator, Hash, Log, Setting, Session, Exception, DB;

use App\User;

use App\VideoTape;

use App\PayPerView;

use App\Subscription, App\UserPayment;

use App\Coupon, App\UserCoupon;

use App\Card;

class PaymentRepository {

    /**
     * @uses to store the payment failure
     *
     * @param $user_id
     *
     * @param $subscription_id
     *
     * @param $reason
     *
     * @param $payment_id = After payment - if any configuration failture or timeout
     *
     * @return boolean response
     */

    public static function subscription_payment_failure_save($user_id = 0 , $subscription_id = 0 , $reason = "" , $payment_id = "") {

        Log::info("subscription_payment_failure_save STRAT");

        /*********** DON't REMOVE LOGS **************/

        // Log::info("1- Subscription ID".$subscription_id);

        // Log::info("2- USER ID".$user_id);
        
        // Log::info("3- MESSAGE ID".$reason);

        // Check the user_id and subscription id not null

        /************ AFTER user paid, if any configuration failture *******/

        if($payment_id) {

            $user_payment_details = UserPayment::where('payment_id',$payment_id)->first();

            $user_payment_details->reason = "After_Payment"." - ".$reason;

            $user_payment_details->save();

            return true;

        }

        /************ Before user payment, if any configuration failture or TimeOut *******/

        if(!$user_id || !$subscription_id) {

            Log::info('Payment failure save - USER ID and Subscription ID not found');

            return false;

        }

        // Get the user payment details

        $user_payment = new UserPayment();

        $user_payment->expiry_date = date('Y-m-d H:i:s');

        $user_payment->payment_id  = "Payment-Failed";
        
        $user_payment->user_id = $user_id;
        
        $user_payment->subscription_id = $subscription_id;
        
        $user_payment->status = 0;

        $user_payment->reason = $reason ? $reason : "";

        $user_payment->save();

        return true;
        

    }

    /**
     * @uses to store the PPV payment failure
     *
     * @param $user_id
     *
     * @param $admin_video_id
     *
     * @param $payment_id
     *
     * @param $reason
     *
     * @param $payment_id = After payment - if any configuration failture or timeout
     *
     * @return boolean response
     */

	public static function ppv_payment_failure_save($user_id = 0 , $video_tape_id = 0 , $reason = "" , $payment_id = "") {

        /*********** DON't REMOVE LOGS **************/

        // Log::info("1- Subscription ID".$subscription_id);

        // Log::info("2- USER ID".$user_id);
        
        // Log::info("3- MESSAGE ID".$reason);

	    // Check the user_id and subscription id not null

        /************ AFTER user paid, if any configuration failture  or timeout *******/

        if($payment_id) {

            $ppv_payment_details = PayPerView::where('payment_id',$payment_id)->first();

            $ppv_payment_details->reason = "After_Payment"." - ".$reason;

            $ppv_payment_details->save();

            return true;

        }

        /************ Before user payment, if any configuration failture or TimeOut *******/

        if(!$user_id || !$video_tape_id) {

            Log::info('Payment failure save - USER ID and Subscription ID not found');

            return false;

        }

        $ppv_user_payment_details = PayPerView::where('user_id' , $user_id)->where('video_id' , $video_tape_id)->where('amount',0)->first();

        if(empty($ppv_user_payment_details)) {

            $ppv_user_payment_details = new PayPerView;

        }

        $ppv_user_payment_details->expiry_date = date('Y-m-d H:i:s');

        $ppv_user_payment_details->payment_id  = "Payment-Failed";

        $ppv_user_payment_details->user_id = $user_id;

        $ppv_user_payment_details->video_id = $video_tape_id;

        $ppv_user_payment_details->reason = "BEFORE-".$reason;

        $ppv_user_payment_details->save();

        return true;
	    

	}

    /**
     * @uses to store the payment with commission split 
     *
     * @param $admin_video_id
     *
     * @param $payperview_id
     *
     * @param $moderator_id
     * 
     * @return boolean response
     */

    public static function ppv_commission_split($video_tape_id = "" , $payperview_id = "") {

        if(!$video_tape_id || !$payperview_id) {

            Log::info("VideoTape"+$video_tape_id);

            Log::info("payperview_id"+$payperview_id);

            return false;
        }

        /***************************************************
         *
         * commission details need to update in following sections 
         *
         * admin_videos table - how much earnings for particular video
         *
         * pay_per_views - On Payment how much commission has calculated 
         *
         * Moderator - If video uploaded_by moderator means need add commission amount to their redeems
         *
         ***************************************************/

        // Get the details

        $video_tape_details = VideoTape::find($video_tape_id);

        if(count($video_tape_details) == 0 ) {

            Log::info('ppv_commission_split - VideoTape Not Found');

            return false;
        }

        $ppv_details = PayPerView::find($payperview_id);

        if(count($ppv_details) == 0 ) {

            Log::info('ppv_commission_split - PayPerView Not Found');

            return false;

        }

        $total = $ppv_details->amount;

        // Commission split 

        $admin_commission = Setting::get('admin_ppv_commission')/100;

        $admin_ppv_amount = $total * $admin_commission;

        $user_ppv_amount = $total - $admin_ppv_amount;

        // Update video earnings

        $video_tape_details->admin_ppv_amount += $admin_ppv_amount ?: 0.00;

        $video_tape_details->user_ppv_amount += $user_ppv_amount ?: 0.00;

        $video_tape_details->save();

        // Update PPV Details

        if($ppv_details) {

            Log::info("PPV DETAILS INSIDE");

            $ppv_details->currency = Setting::get('currency', '$');

            $ppv_details->admin_ppv_amount = $admin_ppv_amount ?: 0.00;

            $ppv_details->user_ppv_amount = $user_ppv_amount ?: 0.00;

            $ppv_details->save();
        
        } else {

            Log::info("payperview_id".$payperview_id);

            Log::info("PPV DETAILS  - NOOOOOOO");

        }

        add_to_redeem($video_tape_details->user_id , $user_ppv_amount , $admin_ppv_amount);

        return true;

    }

    public static function check_coupon_code($request, $user_details, $original_total) {

        $coupon_amount = 0; $coupon_reason = ""; $total = $original_total; $is_coupon_applied =COUPON_NOT_APPLIED;

        $coupon_details = Coupon::where('coupon_code', $request->coupon_code)->where('status', APPROVED)->first();

        if (!$coupon_details) {

            $coupon_reason = CommonHelper::error_message(205);

            goto couponend;
        
        }

        $check_coupon = self::check_coupon_applicable_to_user($user_details, $coupon_details)->getData();

        if($check_coupon->success == false) {

            $coupon_reason = $check_coupon->error_messages;

            goto couponend;

        }

        $is_coupon_applied = COUPON_APPLIED;

        $converted_coupon_amount = $coupon_details->amount;

        // $original_total = ""; // Either subscription or PPV

        if ($coupon_details->amount_type == PERCENTAGE) {

            $converted_coupon_amount = amount_convertion($coupon_details->amount, $original_total);

        }

        // If the Module amount less than coupon amount , then substract the amount.

        if ($converted_coupon_amount < $original_total) {

            $total = $original_total - $converted_coupon_amount;

            $coupon_amount = $converted_coupon_amount;

        } else {

            // If the coupon amount greater than Module amount, then assign to zero.

            $total = 0;

            $coupon_amount = $converted_coupon_amount;
            
        }

        if($check_coupon->code == 2002) {

            $user_coupon = UserCoupon::where('user_id', $user_details->id)->where('coupon_code', $request->coupon_code)->first();

            // If user coupon not exists, create a new row

            if ($user_coupon) {

                if ($user_coupon->no_of_times_used < $coupon_details->per_users_limit) {

                    $user_coupon->no_of_times_used += 1;

                    $user_coupon->save();

                }

            }

        } else {

            $user_coupon = new UserCoupon;

            $user_coupon->user_id = $user_details->id;

            $user_coupon->coupon_code = $request->coupon_code;

            $user_coupon->no_of_times_used = 1;

            $user_coupon->save();

        }

        couponend:

        $data = ['coupon_amount' => $coupon_amount, 'coupon_reason' => $coupon_reason, 'total' => $total, 'is_coupon_applied' => $is_coupon_applied];

        return $data;
        
    }

    /**
     * @method check_coupon_applicable_to_user()
     *
     * @uses To check the coupon code applicable to the user or not
     *
     * @created vithya
     *
     * @updated
     *
     * @param objects $coupon - Coupon details
     *
     * @param objects $user - User details
     *
     * @return response of success/failure message
     */
    public static function check_coupon_applicable_to_user($user, $coupon) {

        try {

            $no_of_times_used = UserCoupon::where('coupon_code', $coupon->coupon_code)->sum('no_of_times_used');

            if ($no_of_times_used < $coupon->no_of_users_limit) {

            } else {

                throw new Exception(CommonHelper::error_message(207), 207);
                
            }

            $user_coupon = UserCoupon::where('user_id', $user->id)->where('coupon_code', $coupon->coupon_code)->first();

            // If user coupon not exists, create a new row

            if (!$user_coupon) {

                $response_array = ['success' => true, 'message' => tr('create_a_new_coupon_row'), 'code' => 2001]; // Based on this the user coupon will create

                return response()->json($response_array);

            }

            if ($user_coupon->no_of_times_used < $coupon->per_users_limit) {

                $response_array = ['success' => true, 'message' => tr('add_no_of_times_used_coupon'), 'code' => 2002]; // Based on this the user coupon will update

            } else {

                throw new Exception(CommonHelper::error_message(208), 208);
            }


            return response()->json($response_array);

        } catch (Exception $e) {

            $response_array = ['success' => false, 'error_messages' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response_array);
        }

    }

    /**
     * @method ppv_payment_save()
     *
     * @uses subscription payment record update
     *
     * @created vithya
     *
     * @updated
     *
     * @param objects $video_tape_details
     *
     * @param objects $user_details
     *
     * @return response of success/failure message
     */
    
    public static function ppv_payment_save($request, $video_tape_details, $user_details) {

        $ppv_details = new PayPerView;
        
        $ppv_details->payment_id  = $request->payment_id;

        $ppv_details->user_id = $request->id;

        $ppv_details->video_id = $request->video_tape_id;

        $ppv_details->status = PAID_STATUS;

        $ppv_details->is_watched = NOT_YET_WATCHED;

        $ppv_details->payment_mode = $request->payment_mode;

        $ppv_details->ppv_date = date('Y-m-d H:i:s');

        $ppv_details->type_of_user = type_of_user($video_tape_details->type_of_user);

        $ppv_details->type_of_subscription = type_of_subscription($video_tape_details->type_of_subscription);

        // Coupon details

        $ppv_details->is_coupon_applied = $request->is_coupon_applied;

        $ppv_details->coupon_code = $request->coupon_code ?: '';

        $ppv_details->coupon_amount = $request->coupon_amount;

        $ppv_details->coupon_reason = $request->is_coupon_applied == COUPON_APPLIED ? '' : $request->coupon_reason;


        // Amount update

        $ppv_details->ppv_amount = $video_tape_details->ppv_amount;

        $ppv_details->amount = $request->total;


        $ppv_details->save();

        if($ppv_details) {

            // Do Commission spilit  and redeems for moderator

            Log::info("ppv_commission_spilit started");

            self::ppv_commission_split($video_tape_details->id , $ppv_details->id , "");

            Log::info("ppv_commission_spilit END"); 

        } 

        $data = [
                    'id' => $user_details->id , 
                    'token' => $user_details->token, 
                    'payment_id' => $ppv_details->payment_id,
                    'amount' => $request->total ?: 0.00,
                    'amount_formatted' => formatted_amount($request->total),
                    'paid_status' => YES,
                ];

        $response_array = ['success' => true, 'message' => CommonHelper::success_message(219), 'code' => 219, 'data' => $data];
    

        return $response_array;
    }

    /**
     * @method subscriptions_payment_save()
     *
     * @uses subscription payment record update
     *
     * @created vithya
     *
     * @updated
     *
     * @param objects $subscription_details
     *
     * @param objects $user_details
     *
     * @return response of success/failure message
     */
    
    public static function subscriptions_payment_save($request, $subscription_details, $user_details) {

        $previous_payment = UserPayment::where('user_id' , $request->id)->where('status', PAID_STATUS)->orderBy('created_at', 'desc')->first();

        $user_payment = new UserPayment;

        $user_payment->expiry_date = date('Y-m-d H:i:s',strtotime("+{$subscription_details->plan} months"));

        if($previous_payment) {

            if (strtotime($previous_payment->expiry_date) >= strtotime(date('Y-m-d H:i:s'))) {

                $user_payment->expiry_date = date('Y-m-d H:i:s', strtotime("+{$subscription_details->plan} months", strtotime($previous_payment->expiry_date)));

            }

        }

        $user_payment->payment_id = $request->payment_id ?: "FREE-".uniqid();

        $user_payment->user_id = $request->id;

        $user_payment->subscription_id = $request->subscription_id;

        $user_payment->status = PAID_STATUS;

        $user_payment->payment_mode = $request->payment_mode;

        // Update previous current subscriptions as zero

        UserPayment::where('user_id', $request->id)->update(['is_current' => NO]);

        $user_payment->is_current = YES;

        // Coupon details

        $user_payment->is_coupon_applied = $request->is_coupon_applied;

        $user_payment->coupon_code = $request->coupon_code  ? $request->coupon_code  :'';

        $user_payment->coupon_amount = $request->coupon_amount;

        $user_payment->coupon_reason = $request->is_coupon_applied == COUPON_APPLIED ? '' : $request->coupon_reason;

        // Amount update

        $user_payment->subscription_amount = $subscription_details->amount;

        $user_payment->amount = $request->total;

        if ($user_payment->save()) {

            $user_details->zero_subscription_status = $subscription_details->amount <= 0 ? YES : NO;

            $user_details->user_type = PAID_USER;

            $user_details->save();

            $data = [
                        'id' => $user_details->id , 
                        'token' => $user_details->token, 
                        'payment_id' => $user_payment->payment_id,
                        'amount' => $user_payment->amount,
                        'amount_formatted' => formatted_amount($user_payment->amount),
                        'paid_status' => $user_payment->status
                    ];

            $response_array = ['success' => true, 'message' => CommonHelper::success_message(205), 'code' => 205, 'data' => $data];

        } else {

            $response_array = ['success' => false, 'error_messages' => Helper::error_message(204), 'error_code' => 204];

        }

        return $response_array;
    
    }

    public static function is_user_can_watch_now($user_id, $video_tape_details) {

        $ppv_details = PayPerView::where('user_id', $user_id)
                            ->where('video_id', $video_tape_details->id)
                            ->where('status', PAID_STATUS)
                            ->orderBy('ppv_date', 'desc')
                            ->first();

        $is_user_can_watch_now = NO;

        if ($ppv_details) {

            if ($video_tape_details->type_of_subscription == RECURRING_PAYMENT && $ppv_details->is_watched == WATCHED) {

                $is_user_can_watch_now = NO;

            } else {

                $is_user_can_watch_now = DEFAULT_TRUE;

            }
        
        }

        return $is_user_can_watch_now;
    }
}
