<?php


namespace App\Repositories;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Helpers\Helper;

use App\Helpers\VideoHelper;

use Auth, DB, Validator, Setting, Exception, Log;

use App\User;

use App\VideoTape, App\PayPerView;

use App\Channel;

use App\UserRating;

use App\Category;

use App\VideoTapeTag;

use App\ChannelSubscription;

class V5Repository {


    public static function home_first_section(Request $request) {

    	try {

            $user_details = User::find($request->id);

            $data = [];

            /* - - - - - - - - - - - Trending section - - - - - - - - - - - */

            $trending_videos = VideoHelper::trending_videos($request);

            $trending_videos_data['title'] = tr('header_trending');

            $trending_videos_data['description'] = tr('header_trending');

            $trending_videos_data['url_type'] = URL_TYPE_TRENDING;

            $trending_videos_data['url_page_id'] = 0;

            $trending_videos_data['see_all_url'] = "";

            $trending_videos_data['data'] = $trending_videos ?: [];

            array_push($data, $trending_videos_data);

            /* - - - - - - - - - - - Trending section - - - - - - - - - - - */

            /* - - - - - - - - - - - My List section - - - - - - - - - - - */

            $wishlist_videos = VideoHelper::wishlist_videos($request);

            $wishlist_videos_data['title'] = tr('header_wishlist');

            $wishlist_videos_data['description'] = tr('header_wishlist');

            $wishlist_videos_data['url_type'] = URL_TYPE_WISHLIST;

            $wishlist_videos_data['url_page_id'] = 0;

            $wishlist_videos_data['see_all_url'] = "";

            $wishlist_videos_data['data'] = $wishlist_videos ?: [];

            array_push($data, $wishlist_videos_data);

            /* - - - - - - - - - - - My List section - - - - - - - - - - - */

            /* - - - - - - - - - - - New Release section - - - - - - - - - - - */

            $recent_videos = VideoHelper::new_releases_videos($request);

            $recent_videos_data['title'] = tr('header_new_releases');

            $recent_videos_data['description'] = tr('header_new_releases');

            $recent_videos_data['url_type'] = URL_TYPE_NEW_RELEASE;

            $recent_videos_data['url_page_id'] = 0;

            $recent_videos_data['see_all_url'] = "";

            $recent_videos_data['data'] = $recent_videos ?: [];

            array_push($data, $recent_videos_data);

            /* - - - - - - - - - - - New Release section - - - - - - - - - - - */

            /* - - - - - - - - - - - Suggestions section - - - - - - - - - - - */

            $recent_videos = VideoHelper::suggestion_videos($request);

            $recent_videos_data['title'] = tr('header_new_releases');

            $recent_videos_data['description'] = tr('header_new_releases');

            $recent_videos_data['url_type'] = URL_TYPE_NEW_RELEASE;

            $recent_videos_data['url_page_id'] = 0;

            $recent_videos_data['see_all_url'] = "";

            $recent_videos_data['data'] = $recent_videos ?: [];

            array_push($data, $recent_videos_data);

            /* - - - - - - - - - - - Suggestions section - - - - - - - - - - - */

            return $data;


	} catch(Exception $e) {

		$error_messages = $e->getMessage();

		$error_code = $e->getCode();

		$response_array = ['success' => false , 'error_messages' => $error_messages , 'error_code' => $error_code];

		return response()->json($response_array , 200);

	}
    
    }

    /**
	 *
	 * @method video_list_response
	 *
	 * @uses used to get the common list details for video
	 *
	 * @created Vidhya R
	 *
	 * @updated Vidhya R
	 *
	 * @param 
	 *
	 * @return
	 */

 	public static function video_list_response($video_tape_ids, $user_id, $orderby = 'video_tapes.updated_at', $other_select_columns = "", $is_random_order = "", $is_owner = NO) {
        
        $user_details = User::find($user_id);

 		$base_query = VideoTape::whereIn('video_tapes.id' , $video_tape_ids);

        if($is_random_order) {

            $base_query = $base_query->orderByRaw('RAND()');

        } else {

            $base_query = $base_query->orderBy($orderby , 'desc');

        }

        // @todo

 		if($other_select_columns != "") {

            if($is_owner == YES) {
                $base_query = $base_query->OwnerShortVideoResponse($other_select_columns);

            } else {
                $base_query = $base_query->ShortVideoResponse($other_select_columns);
            }

 		} else {

 			$base_query = $is_owner == YES ? $base_query->OwnerShortVideoResponse($other_select_columns) : $base_query->ShortVideoResponse();
 		}
 		
 		$video_tapes = $base_query->get();
        
        foreach ($video_tapes as $key => $video_tape_details) {


            $video_tape_details->currency = Setting::get('currency', '$');

            $video_tape_details->share_url = route('user.single' , $video_tape_details->video_tape_id);

            $video_tape_details->watch_count = number_format_short($video_tape_details->watch_count);

            $video_tape_details->should_display_ppv = $video_tape_details->is_my_channel = NO;

            $video_tape_details->ppv_amount_formatted = formatted_amount($video_tape_details->ppv_amount ?: 0.00);

            if($user_details) {

                // check the channer owner status

                $channel_details = Channel::find($video_tape_details->channel_id);

                $is_my_channel = NO;

                if($channel_details) {

                    $is_my_channel = $channel_details->user_id == $user_details->id ? YES: NO;

                }

                $video_tape_details->is_my_channel = $is_my_channel;

                // check the PPV status for owner and guest, logged in user

                $should_display_ppv = NO;

                if($is_my_channel == NO) {

                    $ppv_details = self::pay_per_views_status_check($user_details->id, $user_details->user_type, $video_tape_details)->getData();

                    $watch_video_free = DEFAULT_TRUE;

                    // Log::info("watch_video_free".print_r($ppv_details, true));

                    $should_display_ppv = $ppv_details->success == false ? PAY_WATCH_VIDEO : FREE_VIDEO;

                }

                $video_tape_details->should_display_ppv = $should_display_ppv;
           
            } else {

                // Guest user pay and watch video

                if($video_tape_details->is_pay_per_view == YES) {

                    $video_tape_details->should_display_ppv = PAY_WATCH_VIDEO;
                        
                }
            }

            $video_tape_details->publish_time = common_date($video_tape_details->publish_time, "", 'd M Y');

            $video_tape_details->wishlist_status = VideoHelper::wishlist_status($video_tape_details->video_tape_id, $user_id);

        }

 		return $video_tapes;

 	}

    public static function single_video_response($video_tape_id, $user_id) {

        $user_details = User::find($user_id);

        $video_tape_details = VideoTape::where('video_tapes.id' , $video_tape_id)->ShortVideoResponse()->first();

        $video_tape_details->currency = Setting::get('currency', '$');

        $video_tape_details->share_url = route('user.single' , $video_tape_details->video_tape_id);

        $video_tape_details->watch_count = number_format_short($video_tape_details->watch_count);

        $video_tape_details->should_display_ppv = $video_tape_details->is_my_channel = NO;

        $timezone = Setting::get('timezone');

        if($user_details) {

            // check the channer owner status

            $channel_details = Channel::find($video_tape_details->channel_id);

            $is_my_channel = NO;

            if($channel_details) {

                $is_my_channel = $channel_details->user_id == $user_details->id ? YES: NO;

                 // check subscribe status

                $channel_details->is_user_subscribed_the_channel = CHANNEL_UNSUBSCRIBED;

                if($user_id) {

                    if($channel_details->user_id == $user_id) {


                        $channel_details->is_user_subscribed_the_channel = CHANNEL_OWNER;

                    } else {

                        $check_channel_subscription = ChannelSubscription::where('user_id', $user_id)->where('channel_id', $channel_details->channel_id)->count();

                        $channel_details->is_user_subscribed_the_channel = $check_channel_subscription ? CHANNEL_SUBSCRIBED : CHANNEL_UNSUBSCRIBED;

                    }

                }

            }

            $video_tape_details->is_my_channel = $is_my_channel;

            // check the PPV status for owner and guest, logged in user

            $should_display_ppv = NO;

            if($is_my_channel == NO) {

                $ppv_details = self::pay_per_views_status_check($user_details->id, $user_details->user_type, $video_tape_details)->getData();

                $watch_video_free = DEFAULT_TRUE;

                $should_display_ppv = $ppv_details->success == $watch_video_free ? NO : YES;

            }

            $video_tape_details->should_display_ppv = $should_display_ppv;

            $video_tape_details->ppv_amount_formatted = formatted_amount($video_tape_details->ppv_amount);

            $timezone = $user_details->timezone;
       
        }
    
        $video_tape_details->publish_time = $video_tape_details->publish_time ? common_date($video_tape_details->publish_time, $timezone ?: Setting::get('timezone'), 'd M Y'): "0000-00-00";

        $video_tape_details->wishlist_status = VideoHelper::wishlist_status($video_tape_details->video_tape_id, $user_id);

        $video_tape_details->history_status = count(Helper::history_status($user_id,$video_tape_id)) > 0? 1 : 0;


        $video_tape_details->is_user_subscribed_the_channel = check_channel_status($user_id, $video_tape_details->channel_id) ? CHANNEL_SUBSCRIBED : CHANNEL_UNSUBSCRIBED;

        $video_tape_details->is_liked = Helper::like_status($user_id, $video_tape_id);

        $video_tape_details->total_channel_subscribers = subscriberscnt($video_tape_details->channel_id);

        $my_rating = UserRating::where('user_id', $user_id)->where('video_tape_id', $video_tape_id)->where('rating', '>', 0)->first();

        $video_tape_details->is_user_rated = NO;

        $rating_data = [];

        if ($my_rating) {

            $video_tape_details->is_user_rated = YES;

            $rating_data['comment'] = $my_rating->comment;

            $rating_data['ratings'] = $my_rating->rating;
        }

        $video_tape_details->my_rating = $rating_data;

        // Category details

        $category_details = Category::where('id', $video_tape_details->category_id)->where('status',  APPROVED)->first();

        $video_tape_details->category_id = $video_tape_details->category_name = "";

        if($category_details) {

            $video_tape_details->category_id = $category_details->id;

            $video_tape_details->category_name = $category_details->name;
        }

        $video_tape_details->tags = VideoTapeTag::select('tag_id', 'tags.name as tag_name')
                                            ->leftJoin('tags', 'tags.id', '=', 'video_tape_tags.tag_id')
                                            ->where('video_tape_tags.status', TAG_APPROVE_STATUS)
                                            ->where('video_tape_id', $video_tape_id)
                                            ->get()
                                            ->toArray();

        $resolution_video = VideoTape::find($video_tape_id);

        $resolutions = [];

        if($resolution_video->video_resolutions) {

            $exp_resolution = explode(',', $resolution_video->video_resolutions);

            $exp_resize_path = $resolution_video->video_path ? explode(',', $resolution_video->video_path) : [];

            foreach ($exp_resolution as $key => $value) {
                
                $resolutions[$value] = isset($exp_resize_path[$key]) ? 
                $exp_resize_path[$key] : $resolution_video->video;

            }

            $resolutions['original'] = $resolution_video->video;

        }

        if (!$resolutions) {

            $resolutions['original'] = $resolution_video->video;
            
        }

        $video_tape_details->resolutions = $resolutions;

        $video_tape_details->android_video_url = $video_tape_details->ios_video_url = $video_tape_details->website_video_url =  $resolution_video->video;

        if($resolution_video->video_type == VIDEO_TYPE_UPLOAD) {

            $video_tape_details->android_video_url = Helper::convert_rtmp_to_secure(get_video_end($resolution_video->video) , $resolution_video->video);

            $video_tape_details->ios_video_url = Helper::convert_hls_to_secure(get_video_end($resolution_video->video) , $resolution_video->video); 

        }

        $video_tape_details->video_type_text = video_type_text($resolution_video->video_type);

        return $video_tape_details;

    }

    /**
     * Function Name : pay_per_views_status_check
     *
     * To check the status of the pay per view in each video
     *
     * @created Vithya
     * 
     * @updated
     *
     * @param object $request - Video related details, user related details
     *
     * @return response of success/failure response of datas
     */
    public static function pay_per_views_status_check($user_id, $user_type, $video_data) {

        // Check video details present or not

        if ($video_data) {

            if($video_data->is_pay_per_view && !$user_id) {
                
                $response_array = ['success'=>false, 'message'=>Helper::get_message(125), 'code'=>125];

                return response()->json($response_array, 200);

            }

            // Check the video having ppv or not

            if ($video_data->is_pay_per_view && $user_id) {

                $is_ppv_applied_for_user = DEFAULT_FALSE; // To check further steps , the user is applicable or not

                // Check Type of User, 1 - Normal User, 2 - Paid User, 3 - Both users

                switch ($video_data->type_of_user) {

                    case NORMAL_USER:
                        
                        if (!$user_type) {

                            $is_ppv_applied_for_user = DEFAULT_TRUE;
                        }

                        break;

                    case PAID_USER:
                        
                        if ($user_type) {

                            $is_ppv_applied_for_user = DEFAULT_TRUE;
                        }
                        
                        break;
                    
                    default:

                        // By default it will taks as Both Users

                        $is_ppv_applied_for_user = DEFAULT_TRUE;

                        break;
                
                }

                if ($is_ppv_applied_for_user) {

                    // Check the user already paid or not

                    $ppv_model = PayPerView::where('status', DEFAULT_TRUE)
                        ->where('user_id', $user_id)
                        ->where('video_id', $video_data->video_tape_id)
                        ->orderBy('id','desc')
                        ->first();

                    $watch_video_free = DEFAULT_FALSE;

                    if ($ppv_model) {

                        // Check the type of payment , based on that user will watch the video 

                        switch ($video_data->type_of_subscription) {

                            case ONE_TIME_PAYMENT:
                                
                                $watch_video_free = DEFAULT_TRUE;
                                
                                break;

                            case RECURRING_PAYMENT:

                                // If the video is recurring payment, then check the user already watched the paid video or not 
                                
                                if (!$ppv_model->is_watched) {

                                    $watch_video_free = DEFAULT_TRUE;
                                }
                                
                                break;
                            
                            default:

                                // By default it will taks as true

                                $watch_video_free = DEFAULT_TRUE;

                                break;
                        }

                        if ($watch_video_free) {

                            $response_array = ['success'=>true, 'message'=>Helper::get_message(124), 'code'=>124];

                        } else {

                            $response_array = ['success'=>false, 'message'=>Helper::get_message(125), 'code'=>125];

                        }

                    } else {

                        // 125 - User pay and watch the video

                        $response_array = ['success'=>false, 'message'=>Helper::get_message(125), 'code'=>125];
                    }

                } else {

                    $response_array = ['success'=>true, 'message'=>Helper::get_message(124), 'code'=>124];

                }

            } else {

                // 124 - User can watch the video
                
                $response_array = ['success' => true, 'message'=>Helper::get_message(123), 'code'=>124];
            }

        } else {

            $response_array = ['success'=>false, 'error_messages'=>Helper::get_error_message(906), 
                'error_code'=>906];
        }

        return response()->json($response_array);
    
    }

}