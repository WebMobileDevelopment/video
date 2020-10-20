<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Redeem, App\RedeemRequest, App\User;

use Exception, Validator, DB, Setting;


class version4AdminController extends Controller
{
    /**
     * @method video_tapes_youtube_grapper_save()
     * 
     * Get the videos based on the channel ID from youtube API 
     *
     * @created Vidhya R 
     *
     * @updated Vidhya R
     *
     * @param form details
     * 
     * @return redirect to page with success or error
     *
     */

    public function video_tapes_youtube_grapper_save($youtube_channel_id , Request $request) {

        try {

            DB::beginTransaction();

            if(!$request->youtube_channel_id) {

                throw new Exception(tr('youtube_grabber_channel_id_not_found'), 101);

            }

            // Check the channel is exists in YouTube

            $channel = Youtube::getChannelById($request->youtube_channel_id);

            if($channel == false) {

                throw new Exception(tr('youtube_grabber_channel_id_not_found'), 101);
                
            }

            $youtube_videos = Youtube::listChannelVideos($request->youtube_channel_id, 40);

            foreach ($youtube_videos as $key => $youtube_video_details) {

                $youtube_video_details = Youtube::getVideoInfo($youtube_video_details->id->videoId);

                if($youtube_video_details) {

                    // check the youtube video already exists

                    $check_video_tape_details = $video_tape_details = VideoTape::where('youtube_video_id' , $youtube_video_details->id->videoId)->first();

                    if(count($check_video_tape_details) == 0) {

                        $video_tape_details = new VideoTape;

                        $video_tape_details->publish_time = date('Y-m-d H:i:s');

                        $video_tape_details->duration = "00:00:10";

                        $master_user_details = User::where('is_master_user' , 1)->first();

                        $video_tape_details->user_id = $master_user_details ? $master_user_details->id : 1;

                        $video_tape_details->publish_status = $video_tape_details->is_approved = 1;

                        $video_tape_details->reviews = "YOUTUBE";

                        $video_tape_details->video = "https://youtu.be/".$youtube_video_details->id;

                        $video_tape_details->ratings = 5;

                        $video_tape_details->video_publish_type = PUBLISH_NOW;

                        $video_tape_details->channel_id = 1;
                        
                        $video_tape_details->status = USER_VIDEO_APPROVED_STATUS;
                    
                    }

                    $video_tape_details->title = $youtube_video_details->snippet->title;

                    $video_tape_details->description = $youtube_video_details->snippet->description;

                    $video_tape_details->youtube_channel_id = $youtube_video_details->snippet->channelId;

                    $video_tape_details->youtube_video_id = $youtube_video_details->id;

                    $default_image = isset($youtube_video_details->snippet->thumbnails->maxres) ? $youtube_video_details->snippet->thumbnails->maxres->url : $youtube_video_details->snippet->thumbnails->default->url;

                    $video_tape_details->default_image = $default_image;

                    $video_tape_details->compress_status = 1;

                    $video_tape_details->watch_count = $youtube_video_details->statistics->viewCount;

                    $video_tape_details->save();


                    $second_image = $youtube_video_details->snippet->thumbnails->default->url;

                    $third_image = $youtube_video_details->snippet->thumbnails->high->url;

                    $check_video_image_2 = $video_image_2 = VideoTapeImage::where('position' , 2)->where('video_tape_id' , $video_tape_details->id)->first();

                    if(!$check_video_image_2) {

                        $video_image_2 = new VideoTapeImage;

                    }

                    $video_image_2->image = $second_image;

                    $video_image_2->is_default = 0;

                    $video_image_2->position = 2;

                    $video_image_2->save();

                    $check_video_image_3 = $video_image_3 = VideoTapeImage::where('position' , 3)->where('video_tape_id' , $video_tape_details->id)->first();

                    if(!$check_video_image_3) {

                        $video_image_3 = new VideoTapeImage;

                    }

                    $video_image_3->image = $second_image;

                    $video_image_3->is_default = 0;

                    $video_image_3->position = 3;

                    $video_image_3->save();

                }
                
            }

            return back()->with('flash_success' , "videos grpped from youtube");

        } catch(Exception $e) {

            DB::rollBack();

            $error_messages = $e->getMessage();

            $error_code = $e->getCode();

            return back()->with('flash_error', $error_messages);
        }
    
    }

    /**
     * Function: redeems_payout_invoice()
     * 
     * @uses user redeem payment invoice page
     *
     * @created vidhya R
     *
     * @updated Vidhya R
     *
     * @param - 
     *
     * @return redirect to view page with success/failure message
     */

    public function redeems_payout_invoice(Request $request) {
        
    	try {

        	$validator = Validator::make($request->all() , 
			        	[
				            'redeem_request_id' => 'required|exists:redeem_requests,id',
				            'paid_amount' => 'required', 
				            'user_id' => 'required'
			            ]);

	        if($validator->fails()) {

	        	$error_messages = implode(',', $validator->messages()->all());

	        	throw new Exception($error_messages, 101);

	        }

            $redeem_request_details = RedeemRequest::find($request->redeem_request_id);

            if(!$redeem_request_details) {

	        	throw new Exception(tr('redeem_not_found'), 101);
            }

            if($redeem_request_details->status == REDEEM_REQUEST_PAID ) {

	        	throw new Exception(tr('redeem_request_status_mismatch'), 101);

            }

            $invoice_data['user_details'] = $user_details = User::find($request->user_id);
            
            $invoice_data['redeem_request_id'] = $request->redeem_request_id;

            $invoice_data['redeem_request_status'] = $redeem_request_details->status;

            $invoice_data['user_id'] = $request->user_id;

            $invoice_data['item_name'] = Setting::get('site_name')." - Checkout to"."$user_details ? $user_details->name : -";

            $invoice_data['payout_amount'] = $request->paid_amount;

            $data = json_decode(json_encode($invoice_data));
            
            return view('new_admin.payments.redeems-payout')->with('data' , $data)->withPage('redeems')->with('sub_page' , 'redeems');
           
	        
        } catch(Exception $e) {

        	$error_messages = $e->getMessage();

        	$error_code = $e->getCode();

        	return redirect()->back()->with('flash_error' , $error_messages)->withInput();

        }

    }

    /**
     * @method redeems_payout_direct()
     *
     * @uses used for the payout user by offlin
     *
     * @created Vidhy R
     *
     * @updated Vidhya R
     * 
     * @param integer redeem_request_id 
     *
     * @param integer paid_amount
     * 
     */

    public function redeems_payout_direct(Request $request) {

        try {

            DB::beginTransaction();

            $validator = Validator::make($request->all() , [
                'redeem_request_id' => 'required|exists:redeem_requests,id',
                'paid_amount' => 'required', 
                ]);

            if($validator->fails()) {

                $error_messages = implode(',', $validator->messages()->all());

                throw new Exception($error_messages, 101);

            }

            $redeem_request_details = RedeemRequest::find($request->redeem_request_id);

            if(!$redeem_request_details) {

                throw new Exception(tr('redeem_request_not_found'), 101);

      		}

            if($redeem_request_details->status == REDEEM_REQUEST_PAID ) {

                throw new Exception(tr('redeem_request_status_mismatch'), 101);

            }

            $message = tr('action_success');

            $redeem_amount = $admin_pay_amount = $request->paid_amount ?: 0;

            // Check the requested and admin paid amount is equal 

            if($admin_pay_amount == $redeem_request_details->request_amount) {

                $redeem_request_details->paid_amount = $redeem_request_details->paid_amount + $request->paid_amount;

                $redeem_request_details->status = REDEEM_REQUEST_PAID;

                $redeem_request_details->save();

            } else if($admin_pay_amount > $redeem_request_details->request_amount) {

                $redeem_request_details->paid_amount = $redeem_request_details->paid_amount + $redeem_request_details->request_amount;

                $redeem_request_details->status = REDEEM_REQUEST_PAID;

                $redeem_request_details->save();

                $redeem_amount = $redeem_request_details->request_amount;

                $message = tr('action_success').' - '.tr('redeem_request_greater_than_your_redeem_amount');

            } else if($admin_pay_amount < $redeem_request_details->request_amount) {

                $redeem_request_details->paid_amount = $request->paid_amount;

                $redeem_amount = $request->paid_amount;

                $redeem_request_details->status = REDEEM_REQUEST_PAID;
              
                $redeem_request_details->save();
                
                if($redeem_request_details->paid_amount < $redeem_request_details->request_amount) {
                    
                    $redeem_details = Redeem::where('user_id', $redeem_request_details->user_id)->first();

                    if($redeem_details) { 
                    
                        $redeem_details->remaining += ($redeem_request_details->request_amount - $request->paid_amount);

                        $redeem_details->save();

                    } 

                }

            }

            $redeem_details = Redeem::where('user_id' , $redeem_request_details->user_id)->first();

            if(count($redeem_details) > 0 ) {

                $redeem_details->paid = $redeem_details->paid + $redeem_amount;

                $redeem_details->save();
            }

            DB::commit();

            return redirect()->route('admin.users.redeems')->with('flash_success' , $message);
               
        } catch (Exception $e) {

            DB::rollback();

            $error_messages = $e->getMessage();

            return back()->with('flash_error', $error_messages);

        }

    }

    /**
     * Function: redeems_payout_response()
     * 
     * @uses used to get the response from paypal checkout
     *
     * @created vidhya R
     *
     * @updated Vidhya R
     *
     * @param - 
     *
     * @return redirect to view page with success/failure message
     */

    public function redeems_payout_response(Request $request) {

        try {

            DB::beginTransaction();

            $validator = Validator::make($request->all() , [
                'redeem_request_id' => 'required|exists:redeem_requests,id',
                ]);

            if($validator->fails()) {

                return redirect()->route('admin.users.redeems')->with('flash_error' , $validator->messages()->all())->withInput();

            } else {

                if($request->success == false) {

                    return redirect()->route('admin.users.redeems')->with('flash_error' , tr('redeem_paypal_cancelled'));
                    
                }

                $redeem_request_details = RedeemRequest::find($request->redeem_request_id);

                if($redeem_request_details) {

                    if($redeem_request_details->status == REDEEM_REQUEST_PAID ) {

                        return redirect()->route('admin.users.redeems')->with('flash_error' , tr('redeem_request_status_mismatch'));

                    } else {

                        $message = tr('action_success');

		                $redeem_amount = $request->paid_amount ? $request->paid_amount : 0;

		                // Check the requested and admin paid amount is equal 

		                if($request->paid_amount == $redeem_request_details->request_amount) {

		                    $redeem_request_details->paid_amount = $redeem_request_details->paid_amount + $request->paid_amount;

		                    $redeem_request_details->status = REDEEM_REQUEST_PAID;

		                    $redeem_request_details->save();

		                }


		                else if($request->paid_amount > $redeem_request_details->request_amount) {

		                    $redeem_request_details->paid_amount = $redeem_request_details->paid_amount + $redeem_request_details->request_amount;

		                    $redeem_request_details->status = REDEEM_REQUEST_PAID;

		                    $redeem_request_details->save();

		                    $redeem_amount = $redeem_request_details->request_amount;

		                } else if($admin_pay_amount < $redeem_request_details->request_amount) {

                            $redeem_request_details->paid_amount = $request->paid_amount;

                            $redeem_amount = $request->paid_amount;

                            $redeem_request_details->status = REDEEM_REQUEST_PAID;
                          
                            $redeem_request_details->save();
                            
                            if($redeem_request_details->paid_amount < $redeem_request_details->request_amount) {
                                
                                $redeem_details = Redeem::where('user_id', $redeem_request_details->user_id)->first();

                                if($redeem_details) { 
                                
                                    $redeem_details->remaining += ($redeem_request_details->request_amount - $request->paid_amount);

                                    $redeem_details->save();

                                } 

                            }

		                }

		                $redeem_details = Redeem::where('user_id' , $redeem_request_details->user_id)->first();

		                if(count($redeem_details) > 0 ) {

		                    $redeem_details->paid = $redeem_details->paid + $redeem_amount;

		                    $redeem_details->save();
		                }

                        DB::commit();

                        return redirect()->route('admin.users.redeems')->with('flash_success' , $message);

                    }
                
                } else {

                    return redirect()->route('admin.users.redeems')->with('flash_error' , tr('redeem_not_found'));

                }
            
            }

        } catch (Exception $e) {

            DB::rollback();

            return back()->with('flash_error', $e->getMessage());

        }

    }
}
