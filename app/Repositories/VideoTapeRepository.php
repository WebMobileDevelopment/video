<?php


namespace App\Repositories;

use App\Helpers\Helper;

use Illuminate\Http\Request;

use Validator;

use Log;

use Auth;

use App\VideoTape;

use App\Wishlist;

use App\UserHistory;

use DB;

use Setting;

use App\LiveVideo;

use App\ChannelSubscription;

use App\Flag;

use App\UserRating;

use App\User;

use App\VideoTapeTag;

use App\PayPerView;

class VideoTapeRepository {


	/**
	 * Usage : Register api - validation for the basic register fields 
	 *
	 */

	public static function basic_validation($data = [], &$errors = []) {

		$validator = Validator::make( $data,array(
                'device_type' => 'required|in:'.DEVICE_ANDROID.','.DEVICE_IOS.','.DEVICE_WEB,
                'device_token' => 'required',
                'login_by' => 'required|in:manual,facebook,google',
            )
        );
        
	    if($validator->fails()) {
	        $errors = implode(',', $validator->messages()->all());
	        return false;
	    }

	    return true;

	}


	/**
	 * Trending videos based on the watch count
	 * 
	 */

	public static function trending($request, $web, $skip = null, $count = 0) {

	    $base_query = VideoTape::where('watch_count' , '>' , 0)
                        ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                        ->where('video_tapes.publish_status' , 1)
                        ->where('video_tapes.status' , 1)
                        ->where('video_tapes.is_approved' , 1)
	                    ->videoResponse()
                        ->where('video_tapes.age_limit','<=', checkAge($request))
	                    ->orderby('watch_count' , 'desc');

	    if (Auth::check()) {

            // Check any flagged videos are present

            $flag_videos = flag_videos($request->id);

            if($flag_videos) {
                
                $base_query->whereNotIn('video_tapes.id',$flag_videos);
            }
        }

	   if($skip) {

            $videos = $base_query->skip($skip)->take(Setting::get('admin_take_count' ,12))->get();

        } else if($count > 0){

            $videos = $base_query->skip(0)->take($count)->get();

        } else {

            $videos = $base_query->paginate(16);
            
        }

	    return $videos;
	
	}


    public static function channel_trending($id, $web, $skip = null, $count = 0) {

        $base_query = VideoTape::where('watch_count' , '>' , 0)
                        ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                        ->videoResponse()
                        ->where('channel_id', $id)
                        ->orderby('watch_count' , 'desc');

        if (Auth::check()) {

            // Check any flagged videos are present

            $flag_videos = flag_videos(Auth::user()->id);

            if($flag_videos) {
                
                $base_query->whereNotIn('video_tapes.id',$flag_videos);
            }
        }

       if($skip) {

            $videos = $base_query->skip($skip)->take(Setting::get('admin_take_count' ,12))->get();

        } else if($count > 0){

            $videos = $base_query->skip(0)->take($count)->get();

        } else {

            $videos = $base_query->paginate(16);
            
        }

        return $videos;
    
    }


    public static function payment_videos($id, $web, $skip = null, $count = 0) {

        $base_query = VideoTape::where('amount' , '>' , 0)
                        ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                        ->videoResponse()
                        ->where('channel_id', $id)
                        ->orderby('amount' , 'desc');

       if($skip) {

            $videos = $base_query->skip($skip)->take(Setting::get('admin_take_count' ,12))->get();

        } else if($count > 0){

            $videos = $base_query->skip(0)->take($count)->get();

        } else {

            $videos = $base_query->paginate(16);
            
        }

        return $videos;
    
    }

	/**
	 * Suggestion videos based on the Created At 
	 * 
	 */

	public static function suggestion_videos($request, $web = 1, $skip = null, $id = null) {

		$base_query = VideoTape::where('video_tapes.is_approved' , 1)   
                            ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id') 
                            ->where('video_tapes.status' , 1)
                            ->where('video_tapes.publish_status' , 1)
                            ->orderby('video_tapes.created_at' , 'desc')
                            ->videoResponse()
                            ->where('video_tapes.age_limit','<=', checkAge($request))
                            ->orderByRaw('RAND()');
        if($id) {

            $base_query->whereNotIn('video_tapes.id', [$id]);
        }

        if (Auth::check()) {

            // Check any flagged videos are present

            $flag_videos = flag_videos($request->id);

            if($flag_videos) {
                $base_query->whereNotIn('video_tapes.id',$flag_videos);
            }
        }

        if($skip) {

            $videos = $base_query->skip($skip)->take(Setting::get('admin_take_count' ,12))->get();
            
        } else {

            $videos = $base_query->paginate(16);
        }

        return $videos;
	
	}

	/**
	 * User Wishlist
	 * 
	 */

	public static function wishlist($request, $web = NULL , $skip = 0) {

        $base_query = Wishlist::where('wishlists.user_id' , $request->id)
                            ->leftJoin('video_tapes' ,'wishlists.video_tape_id' , '=' , 'video_tapes.id')
                            ->leftJoin('channels' ,'video_tapes.channel_id' , '=' , 'channels.id')
                            ->where('video_tapes.is_approved' , 1)
                            ->where('video_tapes.status' , 1)
                            ->where('wishlists.status' , 1)
                            ->select(
                                    'wishlists.id as wishlist_id','video_tapes.id as video_tape_id' ,
                                    'video_tapes.title','video_tapes.description' ,
                                    'default_image','video_tapes.watch_count','video_tapes.ratings',
                                    'video_tapes.duration','video_tapes.channel_id',
                                    DB::raw('DATE_FORMAT(video_tapes.publish_time , "%e %b %y") as publish_time') , 'channels.name as channel_name', 'wishlists.created_at')
                            ->where('video_tapes.age_limit','<=', checkAge($request))
                            ->orderby('wishlists.created_at' , 'desc');

        if (Auth::check()) {

            // Check any flagged videos are present

	       	$flag_videos = flag_videos(Auth::user()->id);

            if($flag_videos) {

                $base_query->whereNotIn('video_tapes.id',$flag_videos);

            }
        
        }

        if($web) {

            $videos = $base_query->paginate(16);

        } else {

            $videos = $base_query->skip($skip)->take(Setting::get('admin_take_count' ,12))->get();

        }

        return $videos;

    }

    /**
	 * User Watch List
	 * 
	 */

    public static function watch_list($request, $web = NULL , $skip = 0) {

        $base_query = UserHistory::where('user_histories.user_id' , $request->id)
                            ->leftJoin('video_tapes' ,'user_histories.video_tape_id' , '=' , 'video_tapes.id')
                            ->leftJoin('channels' ,'video_tapes.channel_id' , '=' , 'channels.id')
                            ->where('video_tapes.is_approved' , 1)
                            ->where('video_tapes.status' , 1)
                            ->select('user_histories.id as history_id','video_tapes.id as video_tape_id' ,
                                'video_tapes.title','video_tapes.description' , 'video_tapes.duration',
                                'default_image','video_tapes.watch_count','video_tapes.ratings',
                                DB::raw('DATE_FORMAT(video_tapes.publish_time , "%e %b %y") as publish_time'), 'video_tapes.channel_id','channels.name as channel_name', 'user_histories.created_at')
                            ->where('video_tapes.age_limit','<=', checkAge($request))
                            ->orderby('user_histories.created_at' , 'desc');
        
        if (Auth::check()) {

            // Check any flagged videos are present

	       	$flag_videos = flag_videos(Auth::user()->id);

            if($flag_videos) {
                $base_query->whereNotIn('video_tapes.id',$flag_videos);
            }
        
        }

        if($web) {
            $videos = $base_query->paginate(16);

        } else {
            $videos = $base_query->skip($skip)->take(Setting::get('admin_take_count' ,12))->get();
        }

        return $videos;

    }


    public static function channel_videos($channel_id, $web = NULL , $skip = 0) {

        $videos_query = VideoTape::where('video_tapes.is_approved' , 1)
                    ->where('video_tapes.status' , 1)
                    ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                    ->where('video_tapes.channel_id' , $channel_id)
                    ->videoResponse()
                    ->orderby('video_tapes.created_at' , 'desc');

        if (Auth::check()) {
            // Check any flagged videos are present
            $flagVideos = getFlagVideos(Auth::user()->id);

            if($flagVideos) {

                $videos_query->whereNotIn('video_tapes.id', $flagVideos);

            }

        }

        if($web) {
            $videos = $videos_query->paginate(16);

           
        } else {
            $videos = $videos_query->skip($skip)->take(Setting::get('admin_take_count' ,12))->get();
        }

        return $videos;
    }

    public static function channelVideos($request, $channel_id, $web = NULL , $skip = 0) {

        $videos_query = VideoTape::where('video_tapes.is_approved' , 1)
                    ->where('video_tapes.status' , 1)
                    ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                    ->where('video_tapes.channel_id' , $channel_id)
                    ->videoResponse()
                    ->orderby('video_tapes.created_at' , 'desc');

        if ($request->id) {
            // Check any flagged videos are present
            $flagVideos = getFlagVideos($request->id);

            if($flagVideos) {

                $videos_query->whereNotIn('video_tapes.id', $flagVideos);

            }

        }

        $videos_query->where('video_tapes.age_limit','<=', checkAge($request));

        if($web) {

            $videos = $videos_query->paginate(16);

        } else {

            $videos = $videos_query->skip($skip)->take(Setting::get('admin_take_count' ,12))->get();
        }


        $data = [];

        if(count($videos) > 0) {

            foreach ($videos as $key => $value) {

                $data[] = displayVideoDetails($value, $request->id);
            }

        }

        return $data;
    }


    public static function all_videos($web = NULL , $skip = 0) {

        $videos_query = VideoTape::where('video_tapes.is_approved' , 1)
                    ->where('video_tapes.status' , 1)
                    ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                    ->where('video_tapes.channel_id' , $channel_id)
                    ->videoResponse()
                    ->rand()
                    ->orderby('video_tapes.created_at' , 'asc');
        if (Auth::check()) {
            // Check any flagged videos are present
            $flagVideos = getFlagVideos(Auth::user()->id);

            if($flagVideos) {
                $videos_query->whereNotIn('video_tapes.id', $flagVideos);
            }
        }

        if($web) {
            $videos = $videos_query->paginate(16);
        } else {
            $videos = $videos_query->skip($skip)->take(Setting::get('admin_take_count' ,12))->get();
        }

       
        return $videos;
    }


    public static function admin_recently_added() {

        $base_query = VideoTape::where('video_tapes.is_approved' , 1)                                       
                            ->where('video_tapes.status' , 1)
                            ->where('video_tapes.publish_status' , 1)
                            ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                            ->orderby('video_tapes.created_at' , 'desc')
                            ->videoResponse()->paginate(16);


        return $base_query;

    }


    public static function getUrl($video, $request) {


        $sdp = $video->user_id.'-'.$video->id.'.sdp';

        $device_type = $request->device_type;

        $browser = $request->browser;

        if ($device_type == DEVICE_ANDROID) {

            $url = "rtmp://".Setting::get('cross_platform_url')."/live/".$sdp;

        } else if($device_type == DEVICE_IOS) {

            $url = "http://".Setting::get('cross_platform_url')."/live/".$sdp."/playlist.m3u8";

        } else {

            $browser = $browser ? $browser : get_browser();

            if (strpos($browser, 'safari') !== false) {
                
                $url = "http://".Setting::get('cross_platform_url')."/live/".$sdp."/playlist.m3u8";  

            } else {

                $url = "rtmp://".Setting::get('cross_platform_url')."/live/".$sdp;
            }

        }

        return $url;
    }

    public static function live_videos_list($channel_id, $web,$skip) {

        $query = LiveVideo::where('is_streaming', DEFAULT_TRUE)
                    ->where('channel_id', $channel_id)
                    ->where('status', 0);

        if (Auth::check()) {

            $query->whereNotIn('user_id', [Auth::user()->id]);

        }

        if ($web) {

            $videos = $query->paginate(16);

        } else {

            $videos = $query->skip($skip)->take(Setting::get('admin_take_count'))->get();

        }

        return $videos;

    }

    /**
     *
     * return the common response for single video
     *
     */

    public static function single_response($video_tape_id , $user_id = "" , $login_by) {

        // Initialize the empty array

        $data = [];

        $video_tape_details = VideoTape::where('video_tapes.id' , $video_tape_id)
                                    ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id') 
                                    ->leftJoin('categories' , 'categories.id' , '=' , 'video_tapes.category_id') 
                                    ->where('video_tapes.status' , 1)
                                    ->where('video_tapes.publish_status' , 1)
                                    ->where('video_tapes.is_approved' , 1)
                                    ->where('categories.status', CATEGORY_APPROVE_STATUS)
                                    ->videoResponse()
                                    ->first();
        if($video_tape_details) {

            $video_tape_details->publish_time = $video_tape_details->publish_time ? $video_tape_details->publish_time : '';

           // $video_tape_details->

            $data = $video_tape_details->toArray();

            $data['wishlist_status'] = $data['history_status'] = $data['is_subscribed'] = $data['is_liked'] = $data['pay_per_view_status'] = $data['user_type'] = $data['flaggedVideo'] = 0;

            $data['comment_rating_status'] = 1;

            $user_details = '';

            $is_ppv_status = DEFAULT_TRUE;

            if($user_id) {


                $data['flaggedVideo'] = Flag::where('video_tape_id',$video_tape_id)->where('user_id', $user_id)->first();

                $data['wishlist_status'] = Helper::check_wishlist_status($user_id,$video_tape_id) ? 1 : 0;

                $data['history_status'] = count(Helper::history_status($user_id,$video_tape_id)) > 0? 1 : 0;

                $data['is_subscribed'] = check_channel_status($user_id, $video_tape_details->channel_id);

                $data['is_liked'] = Helper::like_status($user_id,$video_tape_id);

                $mycomment = UserRating::where('user_id', $user_id)->where('video_tape_id', $video_tape_id)->where('rating', '>', 0)->first();

                $data['is_rated'] = DEFAULT_FALSE;

                $data['ratingcomment'] = "";

                $data['ratingvalue'] = 0;

                if ($mycomment) {

                    $data['comment_rating_status'] = DEFAULT_FALSE;

                    $data['is_rated'] = DEFAULT_TRUE;

                    $data['ratingcomment'] = $mycomment->comment;

                    $data['ratingvalue'] = $mycomment->rating;
                }

                if($user_details = User::find($user_id)) {

                    $data['user_type'] = $user_details->user_type;

                    $is_ppv_status = ($video_tape_details->type_of_user == NORMAL_USER || $video_tape_details->type_of_user == BOTH_USERS) ? ( ( $user_details->user_type == 0 ) ? DEFAULT_TRUE : DEFAULT_FALSE ) : DEFAULT_FALSE; 

                }

            }

            $resolutions = [];

            if ($video_tape_details->video_resolutions) {

                $exp_resolution = explode(',', $video_tape_details->video_resolutions);

                $exp_resize_path = $video_tape_details->video_path ? explode(',', $video_tape_details->video_path) : [];

                foreach ($exp_resolution as $key => $value) {
                    
                    $resolutions[$value] = isset($exp_resize_path[$key]) ? 
                    $exp_resize_path[$key] : $video_tape_details->video;

                }

                $resolutions['original'] = $video_tape_details->video;

            }

            if (!$resolutions) {


                $resolutions['original'] = $video_tape_details->video;
                
            }

            $data['resolutions'] = $resolutions;

            $pay_per_view_status = self::pay_per_views_status_check($user_details ? $user_details->id : '', $user_details ? $user_details->user_type : '', $video_tape_details)->getData()->success;

            $ppv_notes = !$pay_per_view_status ? ($video_tape_details->type_of_user == 1 ? tr('normal_user_note') : tr('paid_user_note')) : ''; 

            $data['currency'] = Setting::get('currency');

            $data['is_ppv_subscribe_page'] = $is_ppv_status;

            $data['pay_per_view_status'] = $pay_per_view_status;

            $data['ppv_notes'] = $ppv_notes;

            $data['subscriberscnt'] = subscriberscnt($video_tape_details->channel_id);

            $data['share_url'] = route('user.single' , $video_tape_id);

            $data['my_channel']= $video_tape_details->channel_created_by == $user_id ? DEFAULT_TRUE : DEFAULT_FALSE;

            $data['embed_link'] = route('embed_video', array('u_id'=>$video_tape_details->unique_id));

            $data['tags'] = VideoTapeTag::select('tag_id', 'tags.name as tag_name')
                ->leftJoin('tags', 'tags.id', '=', 'video_tape_tags.tag_id')
                ->where('video_tape_tags.status', TAG_APPROVE_STATUS)
                ->where('video_tape_id', $video_tape_id)->get()->toArray();

            $video_url = $video_tape_details->video;

            if($login_by == DEVICE_ANDROID) {

                $video_url = Helper::convert_rtmp_to_secure(get_video_end($data['video']) , $data['video']); 

                // Setting::get('streaming_url') ? Setting::get('streaming_url').get_video_end($data['video']) : $video_url;

            }

            if($login_by == DEVICE_IOS) {

                $video_url = Helper::convert_hls_to_secure(get_video_end($data['video']) , $data['video']); 

                // $video_url = Setting::get('HLS_STREAMING_URL') ? Setting::get('HLS_STREAMING_URL').get_video_end($data['video']) : $video_url;

            }

            $data['video'] = $video_url;


        }


        return $data;

    }

    public static function suggestions($request) {

         $data = [];

        $base_query = VideoTape::where('video_tapes.is_approved' , 1)   
                            ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id') 
                            ->leftJoin('categories' , 'categories.id' , '=' , 'video_tapes.category_id')
                            ->where('video_tapes.status' , 1)
                            ->where('video_tapes.publish_status' , 1)
                            ->orderby('video_tapes.watch_count' , 'desc')
                            ->where('categories.status', CATEGORY_APPROVE_STATUS)
                            ->where('channels.is_approved', 1)
                            ->where('channels.status', 1)
                            ->videoResponse();

        if ($request->id) {

            // Check any flagged videos are present

            $flag_videos = flag_videos($request->id);

            if($flag_videos) {

                $base_query->whereNotIn('video_tapes.id',$flag_videos);

            }

            $user = User::find($request->id);

            if ($user) {

                $request->request->add([
                    'age_limit'=>$user->age_limit
                ]);

            }
            $base_query->where('video_tapes.age_limit','<=', checkAge($request));
        
        }

        if ($request->video_tape_id) {

            $base_query->whereNotIn('video_tapes.id', [$request->video_tape_id]);

        }

        $base_query->where('video_tapes.age_limit','<=', checkAge($request));

        $videos = $base_query->skip($request->skip)->take(4)->get();

        if(count($videos) > 0) {

            foreach ($videos as $key => $value) {

                $data[] = displayVideoDetails($value, $request->id);

            }
        }

        return $data;

        
    }


    public static function rtmpUrl($model) {

        $rtmpUrl = 'rtmp://'.Setting::get('cross_platform_url').'/live/';

        $url = $rtmpUrl.$model->user_id.'_'.$model->id;

        return $url;
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

            // Check the video having ppv or not

            if ($video_data->is_pay_per_view) {

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
                
                $response_array = ['success'=>true, 'message'=>Helper::get_message(123), 'code'=>124];

            }

        } else {

            $response_array = ['success'=>false, 'error_messages'=>Helper::get_error_message(906), 
                'error_code'=>906];

        }

        return response()->json($response_array);
    
    }

    /**
     *
     * Function Name: video_tape_list()
     *
     * @uses common video response
     *
     */

    public static function video_tape_list($video_ids, $logged_in_user_id) {

        $list = VideoTape::whereIn('video_tapes.id', $video_ids)->orderBy('updated_at', 'desc')->get();

        $video_tapes = [];

        foreach ($list as $key => $value) {
            
            $check_flag_video = Flag::where('video_tape_id' , $value->video_tape_id)->where('user_id' ,$logged_in_user_id)->count();

            if($check_flag_video == 0) {

                $user_details = User::find($logged_in_user_id);

                $video_tape_details = new \stdClass();

                $video_tape_details->title = $value->title;

                $video_tape_details->default_image = $value->default_image;

                $video_tape_details->video_tape_id = $value->id;

                $video_tape_details->duration = $value->duration;

                $video_tape_details->watch_count = $value->watch_count;

                $video_tape_details->wishlist_status = Helper::check_wishlist_status($logged_in_user_id,$value->id) ? 1 : 0;

                $channel_details = $value->getChannel;

                $video_tape_details->channel_id = $channel_details->id;

                $video_tape_details->channel_name = $channel_details ? $channel_details->name : "";

                // PPV data start 

                $value->video_tape_id = $value->id; // Don't remove, this is used in below ppv_status check 

                $pay_per_view_status = self::pay_per_views_status_check($logged_in_user_id, $user_details ? $user_details->user_type : '', $value)->getData()->success;

                $video_tape_details->pay_per_view_status = $pay_per_view_status;

                $is_ppv_subscribe_page = ($value->type_of_user == NORMAL_USER || $value->type_of_user == BOTH_USERS) ? ( ( $user_details->user_type == 0 ) ? YES : NO ) : NO; 

                $video_tape_details->is_ppv_subscribe_page = $is_ppv_subscribe_page;

                $video_tape_details->ppv_amount = $value->ppv_amount;

                $video_tape_details->currency = Setting::get('currency');

                $video_tape_details->created_at = $value->created_at;

                // PPV data end 

                array_push($video_tapes, $video_tape_details);
            
            }

        }

        return $video_tapes;
    }

    /**
     * Function Name : watch_count()
     *
     * @uses To save watch count when ever user see the video
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param Integer $request - Video Tape Id
     *
     * @return response of boolean
     */
    public static function watch_count($video_tape_id,$user_id,$type) {

        if($video = VideoTape::where('id',$video_tape_id)
                ->where('status',1)
                ->where('video_tapes.is_approved' , 1)
                ->first()) {

            \Log::info("ADD History - Watch Count Start");

            // $user_id = Auth::check() ? Auth::user()->id : 0;

            if($video->getVideoAds) {

                \Log::info("getVideoAds Relation Checked");

                if ($video->getVideoAds->status) {

                    \Log::info("getVideoAds Status Checked");

                    // User logged in or not

                    if ($user_id) {

                        if ($video->user_id != $user_id) {

                            // Check the video view count reached admin viewers count, to add amount for each view

                            if($video->watch_count >= Setting::get('viewers_count_per_video') && $video->ad_status) {

                                \Log::info("Check the video view count reached admin viewers count, to add amount for each view");

                                $video_amount = Setting::get('amount_per_video');

                                // $video->redeem_count = 1;

                                // $video->watch_count = $video->watch_count + 1;

                                $video->amount += $video_amount;

                                add_to_redeem($video->user_id , $video_amount);

                                \Log::info("ADD History - add_to_redeem");


                            } else {

                                \Log::info("ADD History - NO REDEEM");

                                // $video->redeem_count += 1;

                                // $video->watch_count = $video->watch_count + 1;
                            }

                            
                        }

                    }

                }
            }

            $video->watch_count += 1;

            $video->save();

            \Log::info("ADD History - Watch Count Start completed");
            
            if($type == YES) {
                return response()->json(['success'=>true, 
                    'data'=>['watch_count'=>number_format_short($video->watch_count)]]);
            }
            
        } else {

            return response()->json(['success'=>false]);
        }

    }
}