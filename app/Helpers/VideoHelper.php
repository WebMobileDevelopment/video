<?php 

namespace App\Helpers;

use Auth, DB, Validator, Setting, Exception, Log;

use App\Repositories\V5Repository as V5Repo;

use App\Wishlist;

use App\VideoTape;

use App\UserHistory;

use App\PayPerView;

use App\Flag;


class VideoHelper {

    protected $skip, $take;

    public function __construct(Request $request) {

        $this->take = $request->take ?: (Setting::get('take') ?: 12);

        $this->skip = $request->skip ?: 0;

    }

    /**
     *
     * @method mobile_home()
     *
     * @uses used to get the list of videos
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $skip
     *
     * @return list of videos
     */

    public static function mobile_home($request) {

        try {

            $base_query = VideoTape::where('video_tapes.is_approved', ADMIN_VIDEO_APPROVED)   
                            ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id') 
                            ->leftJoin('categories' , 'categories.id' , '=' , 'video_tapes.category_id') 
                            ->where('video_tapes.status' , USER_VIDEO_APPROVED)
                            ->where('video_tapes.publish_status' , VIDEO_PUBLISHED)
                            ->where('channels.is_approved', ADMIN_CHANNEL_APPROVED)
                            ->where('channels.status', USER_CHANNEL_APPROVED)
                            ->where('categories.status', CATEGORY_APPROVE_STATUS)
                            ->orderby('video_tapes.created_at' , 'desc');

            // check page type 

            $base_query = self::get_page_type_query($request, $base_query);

            // Check any flagged videos are present

            $spam_video_ids = self::get_flag_video_ids($request->id);
            
            if($spam_video_ids) {

                $base_query->whereNotIn('video_tapes.id', $spam_video_ids);

            }

            $base_query->where('video_tapes.age_limit','<=', checkAge($request));

            $take = $request->take ?: (Setting::get('take') ?: 12);

            $skip = $request->skip ?: 0;

            $video_tape_ids = $base_query->skip($skip)->take($take)->lists('video_tapes.id')->toArray();

            $video_tapes = V5Repo::video_list_response($video_tape_ids, $request->id, $orderBy = "video_tapes.created_at", $other_select_columns = 'video_tapes.description');

            return $video_tapes;

        }  catch( Exception $e) {

            return [];

        }

    }


    /**
     *
     * @method trending()
     *
     * @uses used to get the list of trending videos
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $skip
     *
     * @return list of videos
     */

    public static function trending($request) {

        try {

            $base_query = VideoTape::where('video_tapes.is_approved', ADMIN_VIDEO_APPROVED)   
                            ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id') 
                            ->leftJoin('categories' , 'categories.id' , '=' , 'video_tapes.category_id') 
                            ->where('video_tapes.status' , USER_VIDEO_APPROVED)
                            ->where('video_tapes.publish_status' , VIDEO_PUBLISHED)
                            ->where('channels.is_approved', ADMIN_CHANNEL_APPROVED)
                            ->where('channels.status', USER_CHANNEL_APPROVED)
                            ->where('categories.status', APPROVED)
                            ->orderby('video_tapes.watch_count' , 'desc');

            // check page type 

            $base_query = self::get_page_type_query($request, $base_query);

            // Check any flagged videos are present

            $spam_video_ids = self::get_flag_video_ids($request->id);
            
            if($spam_video_ids) {

                $base_query->whereNotIn('video_tapes.id', $spam_video_ids);

            }

            $base_query->where('video_tapes.age_limit','<=', checkAge($request));

            $take = $request->take ?: (Setting::get('take') ?: 12);

            $skip = $request->skip ?: 0;

            $video_tape_ids = $base_query->skip($skip)->take($take)->lists('video_tapes.id')->toArray();

            $video_tapes = V5Repo::video_list_response($video_tape_ids, $request->id, $orderBy = "video_tapes.created_at", $other_select_columns = 'video_tapes.description');

            return $video_tapes;

        }  catch( Exception $e) {

            return [];

        }

    }

    /**
     *
     * @method tags_based_videos()
     *
     * @uses used to get the list of tags_based_videos
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $skip
     *
     * @return list of videos
     */

    public static function tags_based_videos($request) {

        try {

            $base_query = VideoTape::where('video_tapes.is_approved', ADMIN_VIDEO_APPROVED)   
                            ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id') 
                            ->leftJoin('categories' , 'categories.id' , '=' , 'video_tapes.category_id') 
                            ->where('video_tapes.status' , USER_VIDEO_APPROVED)
                            ->where('video_tapes.publish_status' , VIDEO_PUBLISHED)
                            ->where('channels.is_approved', ADMIN_CHANNEL_APPROVED)
                            ->where('channels.status', USER_CHANNEL_APPROVED)
                            ->where('categories.status', APPROVED)
                            ->where('tag_id', $request->tag_id)
                            ->orderby('video_tapes.updated_at' , 'desc');

            // check page type 

            $base_query = self::get_page_type_query($request, $base_query);

            // Check any flagged videos are present

            $spam_video_ids = self::get_flag_video_ids($request->id);
            
            if($spam_video_ids) {

                $base_query->whereNotIn('video_tapes.id', $spam_video_ids);

            }

            $base_query->where('video_tapes.age_limit','<=', checkAge($request));

            $take = $request->take ?: (Setting::get('take') ?: 12);

            $skip = $request->skip ?: 0;

            $video_tape_ids = $base_query->skip($skip)->take($take)->lists('video_tapes.id')->toArray();

            $video_tapes = V5Repo::video_list_response($video_tape_ids, $request->id, $orderBy = "video_tapes.created_at", $other_select_columns = 'video_tapes.description');

            return $video_tapes;

        }  catch( Exception $e) {

            return [];

        }

    }

    /**
     *
     * @method video_tapes_search()
     *
     * @uses used to get the list of video_tapes_search
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $skip
     *
     * @return list of videos
     */

    public static function video_tapes_search($request) {

        try {

            $base_query = VideoTape::where('video_tapes.is_approved', ADMIN_VIDEO_APPROVED)   
                            ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id') 
                            ->leftJoin('categories' , 'categories.id' , '=' , 'video_tapes.category_id') 
                            ->where('video_tapes.status' , USER_VIDEO_APPROVED)
                            ->where('video_tapes.publish_status' , VIDEO_PUBLISHED)
                            ->where('channels.is_approved', ADMIN_CHANNEL_APPROVED)
                            ->where('channels.status', USER_CHANNEL_APPROVED)
                            ->where('categories.status', APPROVED)
                            ->orderby('video_tapes.updated_at' , 'desc');

            if($request->key) {
                
                $base_query = $base_query->where('title', 'like', "%".$request->key."%");
            }

            // check page type 

            $base_query = self::get_page_type_query($request, $base_query);

            // Check any flagged videos are present

            $spam_video_ids = self::get_flag_video_ids($request->id);
            
            if($spam_video_ids) {

                $base_query->whereNotIn('video_tapes.id', $spam_video_ids);

            }

            $base_query->where('video_tapes.age_limit','<=', checkAge($request));

            $take = $request->take ?: (Setting::get('take') ?: 12);

            $skip = $request->skip ?: 0;

            $video_tape_ids = $base_query->skip($skip)->take($take)->lists('video_tapes.id')->toArray();

            $video_tapes = V5Repo::video_list_response($video_tape_ids, $request->id, $orderBy = "video_tapes.created_at", $other_select_columns = 'video_tapes.description');

            return $video_tapes;

        }  catch( Exception $e) {

            return [];

        }

    }

    /**
     *
     * @method categories_based_videos()
     *
     * @uses used to get the list of categories_based_videos
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $skip
     *
     * @return list of videos
     */

    public static function categories_based_videos($request) {

        try {

            $base_query = VideoTape::where('video_tapes.is_approved', ADMIN_VIDEO_APPROVED)   
                            ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id') 
                            ->leftJoin('categories' , 'categories.id' , '=' , 'video_tapes.category_id') 
                            ->where('video_tapes.status' , USER_VIDEO_APPROVED)
                            ->where('video_tapes.publish_status' , VIDEO_PUBLISHED)
                            ->where('channels.is_approved', ADMIN_CHANNEL_APPROVED)
                            ->where('channels.status', USER_CHANNEL_APPROVED)
                            ->where('categories.status', APPROVED)
                            ->where('video_tapes.category_id', $request->category_id)
                            ->orderby('video_tapes.updated_at' , 'desc');

            // check page type 

            $base_query = self::get_page_type_query($request, $base_query);

            // Check any flagged videos are present

            $spam_video_ids = self::get_flag_video_ids($request->id);
            
            if($spam_video_ids) {

                $base_query->whereNotIn('video_tapes.id', $spam_video_ids);

            }

            $base_query->where('video_tapes.age_limit','<=', checkAge($request));

            $take = $request->take ?: (Setting::get('take') ?: 12);

            $skip = $request->skip ?: 0;

            $video_tape_ids = $base_query->skip($skip)->take($take)->lists('video_tapes.id')->toArray();

            $video_tapes = V5Repo::video_list_response($video_tape_ids, $request->id, $orderBy = "video_tapes.created_at", $other_select_columns = 'video_tapes.description');

            return $video_tapes;

        }  catch( Exception $e) {

            return [];

        }

    }

    /**
     *
     * @method wishlist_videos()
     *
     * @uses used to get the list of contunue watching videos
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $skip
     *
     * @return list of videos
     */

    public static function wishlist_videos($request) {

        try {

            $base_query = Wishlist::select('wishlists.video_tape_id')
                                ->where('wishlists.user_id' , $request->id)
                                ->leftJoin('video_tapes', 'video_tapes.id', '=' , 'wishlists.video_tape_id')
                                ->orderby('wishlists.updated_at', 'desc');
            // check page type 

            $base_query = self::get_page_type_query($request, $base_query);

            // Check any flagged videos are present

            $spam_video_ids = self::get_flag_video_ids($request->id);
            
            if($spam_video_ids) {

                $base_query->whereNotIn('wishlists.video_tape_id', $spam_video_ids);

            }

            $take = $request->take ?: (Setting::get('take') ?: 12);

            $skip = $request->skip ?: 0;

            $wishlist_video_ids = $base_query->skip($skip)->take($take)->lists('video_tape_id')->toArray();

            $video_tapes = V5Repo::video_list_response($wishlist_video_ids, $request->id, $orderBy = "video_tapes.created_at", $other_select_columns = 'video_tapes.description');

            return $video_tapes;

        }  catch( Exception $e) {

            return [];

        }

    }

    /**
     *
     * @method history_videos()
     *
     * @uses used to get the list of contunue watching videos
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $sub_profile_id
     *
     * @param integer $skip
     *
     * @return list of videos
     */

    public static function history_videos($request) {

        try {

            $base_query = UserHistory::select('user_histories.video_tape_id')
                                ->where('user_histories.user_id' , $request->id)
                                ->leftJoin('video_tapes', 'video_tapes.id', '=' , 'user_histories.video_tape_id')
                                ->orderby('user_histories.updated_at', 'desc');
            // check page type 

            $base_query = self::get_page_type_query($request, $base_query);

            // Check any flagged videos are present

            $spam_video_ids = self::get_flag_video_ids($request->id);
            
            if($spam_video_ids) {

                $base_query->whereNotIn('user_histories.video_tape_id', $spam_video_ids);

            }

            $take = Setting::get('admin_take_count', 12);

            $skip = $request->skip ?: 0;

            $user_history_ids = $base_query->skip($skip)->take($take)->lists('video_tape_id')->toArray();
            
            $video_tapes = V5Repo::video_list_response($user_history_ids, $request->id,$orderBy = "video_tapes.created_at", $other_select_columns = '');
            
            return $video_tapes;

        }  catch( Exception $e) {

            return [];

        }

    }

    /**
     *
     * @method ppv_videos()
     *
     * @uses used to get the list of contunue watching videos
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $skip
     *
     * @return list of videos
     */

    public static function ppv_videos($request) {

        try {

            $base_query = PayPerView::select('pay_per_views.video_id')
                                ->where('pay_per_views.user_id' , $request->id)
                                ->leftJoin('video_tapes', 'video_tapes.id', '=' , 'pay_per_views.video_id')
                                ->orderby('pay_per_views.updated_at', 'desc');
            // check page type 

            $base_query = self::get_page_type_query($request, $base_query);

            // Check any flagged videos are present

            $spam_video_ids = self::get_flag_video_ids($request->id);
            
            if($spam_video_ids) {

                $base_query->whereNotIn('pay_per_views.video_id', $spam_video_ids);

            }

            $take = Setting::get('admin_take_count', 12);

            $skip = $request->skip ?: 0;

            $video_tape_ids = $base_query->skip($skip)->take($take)->lists('video_id')->toArray();

            $video_tapes = V5Repo::video_list_response($video_tape_ids, $request->id,$orderBy = "video_tapes.created_at", $other_select_columns = 'video_tapes.description');

            return $video_tapes;

        }  catch( Exception $e) {

            return [];

        }

    }

    /**
     *
     * @method spam_videos()
     *
     * @uses used to get the list of contunue watching videos
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $skip
     *
     * @return list of videos
     */

    public static function spam_videos($request) {

        try {

            $base_query = Flag::where('flags.user_id', $request->id)
                            ->where('flags.status', DEFAULT_TRUE)
                            ->leftJoin('video_tapes', 'flags.video_tape_id', '=', 'video_tapes.id')
                            ->where('video_tapes.is_approved' , ADMIN_VIDEO_APPROVED)
                            ->where('video_tapes.status' , USER_VIDEO_APPROVED)
                            ->where('video_tapes.age_limit','<=', checkAge($request))
                            ->orderBy('flags.created_at', 'desc');

            $take = $request->take ?: (Setting::get('take') ?: 12);

            $skip = $request->skip ?: 0;

            $spam_video_ids = $base_query->skip($skip)->take($take)->lists('video_tape_id')->toArray();

            $video_tapes = V5Repo::video_list_response($spam_video_ids, $request->id, $orderBy = "video_tapes.created_at", $other_select_columns = 'video_tapes.description');

            return $video_tapes;

        }  catch( Exception $e) {

            return [];

        }

    }

    /**
     *
     * @method suggestion_videos()
     *
     * @uses used to get the list of contunue watching videos
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $sub_profile_id
     *
     * @param integer $skip
     *
     * @return list of videos
     */

    public static function suggestion_videos($request) {

        try {

            $base_query = UserHistory::where('user_id' , $request->id)->orderByRaw('RAND()');
                       
            // Check any flagged videos are present

            $spam_video_ids = self::get_flag_video_ids($request->id);
            
            if($spam_video_ids) {

                $base_query->whereNotIn('user_histories.video_tape_id', $spam_video_ids);

            }

            $take = $request->take ?: Setting::get('admin_take_count', 12); 

            $skip = $request->skip ?: 0;

            $suggestion_video_ids = $base_query->skip($skip)->take($take)->lists('video_tape_id')->toArray();

            $suggestion_video_ids[] = $request->video_tape_id;

            // Get the channel videos 

            $suggestion_channel_ids = VideoTape::whereIn('video_tapes.id', $suggestion_video_ids)->whereNotIn('video_tapes.id', $spam_video_ids)->lists('channel_id')->toArray();

            // Based on the selected channel ids

            $spam_video_ids[] = $request->video_tape_id;

            $video_tape_ids = VideoTape::whereNotIn('video_tapes.id', $spam_video_ids)->whereIn('channel_id', $suggestion_channel_ids)->orderByRaw('RAND()')->lists('video_tapes.id')->toArray();

            if(!$video_tape_ids) {

                $video_tape_ids = VideoTape::whereNotIn('video_tapes.id', $spam_video_ids)->orderByRaw('RAND()')->lists('video_tapes.id')->toArray();

            }

            $video_tape_ids = array_slice($video_tape_ids, 0, $take);

            $video_tapes = V5Repo::video_list_response($video_tape_ids, $request->id, $orderBy = "video_tapes.updated_at", $other_select_columns = 'video_tapes.description', $is_random_order = YES);

            return $video_tapes;

        }  catch( Exception $e) {

            return [];

        }

    }

    /**
     *
     * @method wishlist_status()
     *
     * @uses used to get the wishlist status of the video
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $sub_profile_id
     *
     * @return boolean 
     */
    public static function wishlist_status($video_tape_id,$user_id) {

        $wishlist_details = Wishlist::where('video_tape_id' , $video_tape_id)
                        ->where('user_id' , $user_id)
                        ->where('status' , YES)
                        ->count();

        $wishlist_status = $wishlist_details ? YES : NO;

        return $wishlist_status;

        
    }

    /**
     *
     * @method history_status()
     *
     * @uses used to get the wishlist status of the video
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $sub_profile_id
     *
     * @return boolean 
     */
    public static function history_status($admin_video_id,$sub_profile_id) {

        $history_details = UserHistory::where('admin_video_id' , $admin_video_id)->where('sub_profile_id' , $sub_profile_id)->count();

        $history_status = $history_details ? YES : NO;

        return $history_status;

    }

    /**
     *
     * @method like_status()
     *
     * @uses used to get the like status of the video
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $sub_profile_id
     *
     * @return boolean 
     */
    public static function like_status($admin_video_id,$sub_profile_id) {

        $like_video_details = LikeDislikeVideo::where('admin_video_id' , $admin_video_id)->where('sub_profile_id' , $sub_profile_id)->first();

        $like_status = NO;

        if($like_video_details) {

            if($like_video_details->like_status == DEFAULT_TRUE) {

                $like_status = YES;

            } else if($like_video_details->dislike_status == DEFAULT_TRUE){

                $like_status = -1;

            }
        
        }

        return $like_status;

    }

    /**
     *
     * @method likes_count()
     *
     * @uses used to get the like status of the video
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $sub_profile_id
     *
     * @return boolean 
     */
    public static function likes_count($admin_video_id) {

        $likes_count = LikeDislikeVideo::where('admin_video_id' , $admin_video_id)->where('like_status' , DEFAULT_TRUE)->count();

        return $likes_count ?: 0;

    }
    /**
     *
     * @method get_page_type_query()
     *
     * @uses based on the page type, change the query
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param Request $request
     *
     * @param $base_query
     *
     * @return $base_query 
     */
    public static function get_page_type_query($request, $base_query) {

        if($request->page_type == API_PAGE_TYPE_SERIES) {

            $base_query  = $base_query->where('video_tapes.genre_id', "!=", 0);

        } elseif($request->page_type == API_PAGE_TYPE_FLIMS) {

            $base_query  = $base_query->where('video_tapes.genre_id', "=", 0);

        } elseif($request->page_type == API_PAGE_TYPE_KIDS) {

            $base_query  = $base_query->where('video_tapes.is_kids_video', "=", KIDS_SECTION_YES);

        } elseif($request->page_type == API_PAGE_TYPE_CATEGORY) {

            $base_query  = $base_query->where('video_tapes.category_id', $request->category_id);

        } elseif($request->page_type == API_PAGE_TYPE_SUB_CATEGORY) {

            $base_query  = $base_query->where('video_tapes.sub_category_id', $request->sub_category_id);

        } elseif($request->page_type == API_PAGE_TYPE_GENRE) {

            $base_query  = $base_query->where('video_tapes.genre_id', $request->genre_id);

        }

        return $base_query;

    }

    /**
     *
     * @method get_ppv_page_type()
     *
     * @uses based on the page type, change the query
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param Request $request
     *
     * @param $base_query
     *
     * @return $base_query 
     */
    public static function get_ppv_page_type($admin_video_details, $user_type, $is_pay_per_view = NO) {

        if($is_pay_per_view == NO) {

            $data['ppv_page_type'] = PPV_PAGE_TYPE_NONE;

            $data['ppv_page_type_content'] = [];

            return json_decode(json_encode($data));

        }

        $ppv_page_type = PPV_PAGE_TYPE_INVOICE; $data = $ppv_page_type_content = [];

        if($admin_video_details->type_of_user == NORMAL_USER || $admin_video_details->type_of_user == BOTH_USERS) {

            if($user_type == NON_SUBSCRIBED_USER) {

                $ppv_page_type = PPV_PAGE_TYPE_CHOOSE_SUB_OR_PPV;

                $subscription_data['title'] = tr('api_choose_subscription');

                $subscription_data['description'] = tr('api_click_here_to_subscribe');

                $subscription_data['type'] = SUBSCRIPTION;

                $ppv_data['title'] = tr('api_ppv_title', 'Recurring');

                $ppv_data['description'] = tr('api_click_here_to_ppv', type_of_subscription($admin_video_details->type_of_subscription));

                $ppv_data['type'] = PPV;

                $ppv_page_type_content = json_decode(json_encode([$subscription_data, $ppv_data]));

            }

        }

        $data['ppv_page_type'] = $ppv_page_type;

        $data['ppv_page_type_content'] = $ppv_page_type_content;

        return json_decode(json_encode($data));


    }

    /**
     *
     * @method get_ppv_page_type()
     *
     * @uses based on the page type, change the query
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param Request $request
     *
     * @param $base_query
     *
     * @return $base_query 
     */
    public static function videoPlayDuration($admin_video_id, $sub_profile_id) {

        $continue_watching_video_details = ContinueWatchingVideo::where('admin_video_id', $admin_video_id)
                                        ->where('sub_profile_id', $sub_profile_id)
                                        ->first();

        return $continue_watching_video_details;

    }

    /**
     *
     * @method get_flag_video_ids()
     *
     * @uses based on the page type, change the query
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param Request $request
     * 
     * @return $base_query 
     */
    public static function get_flag_video_ids($user_id) {

        // Load Flag videos based on logged in user id
        
        $video_tape_ids = Flag::where('flags.user_id', $user_id)
                            ->leftJoin('video_tapes' , 'flags.video_tape_id' , '=' , 'video_tapes.id')
                            ->where('video_tapes.is_approved' , ADMIN_VIDEO_APPROVED)
                            ->where('video_tapes.status' , USER_VIDEO_APPROVED)
                            ->pluck('video_tape_id')
                            ->toArray();

        // Return array of id's   

        return $video_tape_ids;
    }

}
