<?php

use App\Helpers\Helper;

use App\Helpers\EnvEditorHelper;

use App\Repositories\VideoTapeRepository as VideoRepo;

use Carbon\Carbon;

use App\Wishlist;

use App\VideoTape;

use App\UserHistory;

use App\UserRating;

use App\User;

use App\MobileRegister;

use App\PageCounter;

use App\UserPayment;

use App\Settings;

use App\Flag;

use App\Channel;

use App\Redeem;

use App\Page;

use App\ChannelSubscription;

use App\Language;

use App\LiveVideoPayment;

use App\PayPerView;

use App\VideoAd;

use App\Category;

use App\VideoTapeTag;

use App\Playlist;

use App\PlaylistVideo;

use App\UserReferrer;

function tr($key , $otherkey = "") {

    if(Auth::guard('admin')->check()) {
        // $locale = config('app.locale');
        $locale = Setting::get('default_lang' , 'en');
        
    } else {
        
        if (!\Session::has('locale')) {

            // $locale = \Session::put('locale', config('app.locale'));
            $locale = Setting::get('default_lang' , 'en');

        }else {

            $locale = \Session::get('locale');

        }

    }
    
    return \Lang::choice('messages.'.$key, 0, Array('otherkey' => $otherkey), $locale);
}

function apitr($key , $otherkey = "") {

    if (!\Session::has('locale'))

        \Session::put('locale', \Config::get('app.locale'));

    // return \Lang::choice('messages.'.$key, 0, Array(), \Session::get('locale'));

    return \Lang::choice('api-messages.'.$key, 0, Array('otherkey' => $otherkey), \Session::get('locale'));
}

function envfile($key) {

    $data = EnvEditorHelper::getEnvValues();

    if($data) {
        return $data[$key];
    }

    return "";
}

function get_video_end($video_url) {
    $url = explode('/',$video_url);
    $result = end($url);
    return $result;
}

function get_video_end_smil($video_url) {
    $url = explode('/',$video_url);
    $result = end($url);
    if ($result) {
        $split = explode('.', $result);
        if (count($split) == 2) {
            $result = $split[0];
        }
    }
    return $result;
}

function register_mobile($device_type) {
    if($reg = MobileRegister::where('type' , $device_type)->first()) {
        $reg->count = $reg->count + 1;
        $reg->save();
    }   
}

/**
 * Function Name : subtract_count()
 * While Delete user, subtract the count from mobile register table based on the device type
 *
 * @param string $device_ype : Device Type (Andriod,web or IOS)
 * 
 * @return boolean
 */
function subtract_count($device_type) {
    if($reg = MobileRegister::where('type' , $device_type)->first()) {
        $reg->count = $reg->count - 1;
        $reg->save();
    }
}

function get_register_count() {

    $ios_count = MobileRegister::where('type' , 'ios')->first()->count;

    $android_count = MobileRegister::where('type' , 'android')->first()->count;

    $web_count = MobileRegister::where('type' , 'web')->first()->count;

    $total = $ios_count + $android_count + $web_count;

    return array('total' => $total , 'ios' => $ios_count , 'android' => $android_count , 'web' => $web_count);
}

function last_days($days){

  $views = PageCounter::orderBy('created_at','asc')->where('created_at', '>', Carbon::now()->subDays($days))->where('page','home');
  $arr = array();
  $arr['count'] = $views->count();
  $arr['get'] = $views->get();

  return $arr;

}

function counter($page){

    $count_home = PageCounter::wherePage($page)->where('created_at', '>=', new DateTime('today'));

        if($count_home->count() > 0){
            $update_count = $count_home->first();
            $update_count->count = $update_count->count + 1;
            $update_count->save();
        }else{
            $create_count = new PageCounter;
            $create_count->page = $page;
            $create_count->count = 1;
            $create_count->save();
        }
}

function get_recent_users() {
    $users = User::orderBy('created_at' , 'desc')->skip(0)->take(12)->get();

    return $users;
}
function get_recent_videos() {
    $videos = AdminVideo::orderBy('publish_time' , 'desc')->skip(0)->take(12)->get();

    return $videos;
}

function total_revenue() {

    $user_payments = UserPayment::sum('amount');

    $video_payments = VideoTape::sum('admin_ppv_amount');

    $user_referral_payments = UserReferrer::sum('total_referrals_earnings');

    return $video_payments + $user_payments + $user_referral_payments;

}

function check_s3_configure() {

    $key = config('filesystems.disks.s3.key');

    $secret = config('filesystems.disks.s3.secret');

    $bucket = config('filesystems.disks.s3.bucket');

    $region = config('filesystems.disks.s3.region');

    if($key && $secret && $bucket && $region) {
        return 1;
    } else {
        return false;
    }
}


function check_valid_url($file) {

    return 1;

}

function check_nginx_configure() {
    $nginx = shell_exec('nginx -V');
    if($nginx) {
        return true;
    } else {
        if(file_exists("/usr/local/nginx-streaming/conf/nginx.conf")) {
            return true;
        } else {
           return false; 
        }
    }
}

function check_php_configure() {
    return phpversion();
}

function check_mysql_configure() {

    $output = shell_exec('mysql -V');

    $data = 1;

    if($output) {
        preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $version); 
        $data = $version[0];
    }

    return $data; 
}

function check_database_configure() {

    $status = 0;

    $database = config('database.connections.mysql.database');
    $username = config('database.connections.mysql.username');

    if($database && $username) {
        $status = 1;
    }
    return $status;

}

function check_settings_seeder() {
    return Settings::count();
}



/**
 * Function Name : getReportVideoTypes()
 * Load all report video types in settings table
 *
 * @return array of values
 */ 
function getReportVideoTypes() {
    // Load Report Video values
    $model = Settings::where('key', REPORT_VIDEO_KEY)->get();
    // Return array of values
    return $model;

}

/**
 * Function Name : getFlagVideos()
 * To load the videos based on the user
 *
 * @param int $id User Id
 *
 * @return array of values
 */
function flag_videos($id) {

    // Load Flag videos based on logged in user id
    $model = Flag::where('flags.user_id', $id)
        ->leftJoin('video_tapes' , 'flags.video_tape_id' , '=' , 'video_tapes.id')
        ->where('video_tapes.is_approved' , 1)
        ->where('video_tapes.status' , 1)
        ->pluck('video_tape_id')->toArray();

    // Return array of id's
    return $model;
}

/**
 * Function Name : getFlagVideosCnt()
 * To load the videos cnt based on the user 
 *
 * @param int $id User Id
 *
 * @return cnt
 */
function getFlagVideosCnt($id) {
    // Load Flag videos based on logged in user id
    $model = Flag::where('flags.user_id', $id)
        ->leftJoin('video_tapes' , 'flags.video_tape_id' , '=' , 'video_tapes.id')
        ->where('video_tapes.is_approved' , 1)
        ->where('video_tapes.status' , 1)
        ->count();
    // Return array of id's
    return $model;

}


/**
 * Function Name : getImageResolutions()
 * Load all image resoltions types in settings table
 *
 * @return array of values
 */ 
function getImageResolutions() {
    // Load Report Video values
    $model = Settings::where('key', IMAGE_RESOLUTIONS_KEY)->get();
    // Return array of values
    return $model;
}

/**
 * Function Name : getVideoResolutions()
 * Load all video resoltions types in settings table
 *
 * @return array of values
 */ 
function getVideoResolutions() {
    // Load Report Video values
    $model = Settings::where('key', VIDEO_RESOLUTIONS_KEY)->get();
    // Return array of values
    return $model;
}

/**
 * Function Name : convertMegaBytes()
 * Convert bytes into mega bytes
 *
 * @return number
 */
function convertMegaBytes($bytes) {
    return number_format($bytes / 1048576, 2);
}

/**
 * Function Name : get_video_attributes()
 * To get video Attributes
 *
 * @param string $video Video file name
 *
 * @return attributes
 */
function get_video_attributes($video) {

    $command = 'ffmpeg -i ' . $video . ' -vstats 2>&1';

    Log::info("Path ".$video);

    $output = shell_exec($command);

    Log::info("Shell Exec : ".$output);


    $codec = null; $width = null; $height = null;

    $regex_sizes = "/Video: ([^,]*), ([^,]*), ([0-9]{1,4})x([0-9]{1,4})/";

    Log::info("Preg Match :" .preg_match($regex_sizes, $output, $regs));
    if (preg_match($regex_sizes, $output, $regs)) {
        $codec = $regs [1] ? $regs [1] : null;
        $width = $regs [3] ? $regs [3] : null;
        $height = $regs [4] ? $regs [4] : null;
    }

    $hours = $mins = $secs = $ms = null;
    
    $regex_duration = "/Duration: ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2}).([0-9]{1,2})/";
    if (preg_match($regex_duration, $output, $regs)) {
        $hours = $regs [1] ? $regs [1] : null;
        $mins = $regs [2] ? $regs [2] : null;
        $secs = $regs [3] ? $regs [3] : null;
        $ms = $regs [4] ? $regs [4] : null;
    }

    Log::info("Width of the video : ".$width);
    Log::info("Height of the video : ".$height);

    return array('codec' => $codec,
        'width' => $width,
        'height' => $height,
        'hours' => $hours,
        'mins' => $mins,
        'secs' => $secs,
        'ms' => $ms
    );
}


/**
 * Function Name :readFile()
 * To read a input file and get attributes
 * 
 * @param string $inputFile File name
 *
 * @return $attributes
 */
function readFileName($inputFile) {

    $finfo = finfo_open(FILEINFO_MIME_TYPE);

    $mime_type = finfo_file($finfo, $inputFile); // check mime type


    finfo_close($finfo);

    $video_attributes = [];
    
    if (preg_match('/video\/*/', $mime_type)) {

        Log::info("Inside ffmpeg");

        $video_attributes = get_video_attributes($inputFile, 'ffmpeg');
    } 

    return $video_attributes;
}

function getResolutionsPath($video, $resolutions, $streaming_url) {

    $video_resolutions = ($streaming_url) ? [$streaming_url.Setting::get('original_key').get_video_end($video)] : [$video];

    $pixels = ['Original'];
    $exp = explode('original/', $video);

    if (count($exp) == 2) {
        if ($resolutions) {
            $split = explode(',', $resolutions);
            foreach ($split as $key => $resoltuion) {
                $streamUrl = ($streaming_url) ? $streaming_url.Setting::get($resoltuion.'_key').$exp[1] : $exp[0].$resoltuion.'/'.$exp[1];
                array_push($video_resolutions, $streamUrl);
                $splitre = explode('x', $resoltuion);
                array_push($pixels, $splitre[1].'p');
            }
        }
    }
    $video_resolutions = implode(',', $video_resolutions);

    $pixels = implode(',', $pixels);
    return ['video_resolutions' => $video_resolutions, 'pixels'=> $pixels];
}


function deleteVideoAndImages($video) {

    if ($video->video_type == VIDEO_TYPE_UPLOAD ) {
        if($video->video_upload_type == VIDEO_UPLOAD_TYPE_s3) {
            Helper::s3_delete_picture($video->video);   
            Helper::s3_delete_picture($video->trailer_video);  
        } else {
            $videopath = '/uploads/videos/original/';
            Helper::delete_picture($video->video, $videopath); 
            $splitVideos = ($video->video_resolutions) 
                        ? explode(',', $video->video_resolutions)
                        : [];
            foreach ($splitVideos as $key => $value) {
               Helper::delete_picture($video->video, $videopath.$value.'/');
            }

            Helper::delete_picture($video->trailer_video, $videopath);
            // @TODO
            $splitTrailer = ($video->trailer_video_resolutions) 
                        ? explode(',', $video->trailer_video_resolutions)
                        : [];
            foreach ($splitTrailer as $key => $value) {
               Helper::delete_picture($video->trailer_video, $videopath.$value.'/');
            }
        }
    }

    if($video->default_image) {
        Helper::delete_picture($video->default_image, "/uploads/images/");
    }

    if($video->is_banner == 1) {
        if($video->banner_image) {
            Helper::delete_picture($video->banner_image, "/uploads/images/");
        }
    }
}

/**
 * Check the default subscription is enabled by admin
 *
 */

function user_type_check($user) {

    $user = User::find($user);

    if($user) {

        if(Setting::get('is_default_paid_user') == DEFAULT_TRUE) {

            $user->user_type = DEFAULT_TRUE;

        } else {

            // User need subscripe the plan

            if(Setting::get('is_subscription')) {

                $user->user_type = DEFAULT_TRUE;

            } else {
                // Enable the user as paid user
                $user->user_type = DEFAULT_FALSE;
            }
        }

        $user->save();
    }
}


function get_expiry_days($id) {
    
    $data = UserPayment::where('user_id' , $id)->where('status', DEFAULT_TRUE)->orderBy('created_at', 'desc')->first();

    // User Amount

    $amt = UserPayment::select('user_id', DB::raw('sum(amount) as amt'))->where('user_id', $id)->groupBy('user_id')->first();

    $days = 0;

    if($data) {

        if(strtotime($data->expiry_date) >= strtotime(date('Y-m-d H:i:s'))) {

            $start_date = new \DateTime(date('Y-m-d h:i:s'));
            $end_date = new \DateTime($data->expiry_date);

            $time_interval = date_diff($start_date,$end_date);
            $days = $time_interval->days;

        }
    }

    return ['days'=>$days, 'amount'=>($amt) ? $amt->amt : 0];
}


//this function convert string to UTC time zone

function convertTimeToUTCzone($str, $userTimezone, $format = 'Y-m-d H:i:s') {

    try {
        $new_str = new DateTime($str, new DateTimeZone($userTimezone));

        $new_str->setTimeZone(new DateTimeZone('UTC'));
    }
    catch(\Exception $e) {

    }

    return $new_str->format( $format);
}

//this function converts string from UTC time zone to current user timezone

function convertTimeToUSERzone($str, $userTimezone, $format = 'Y-m-d H:i:s') {

    if(empty($str)){
        return '';
    }
    try{
        $new_str = new DateTime($str, new DateTimeZone('UTC') );
        $new_str->setTimeZone(new DateTimeZone( $userTimezone ));
    }
    catch(\Exception $e) {
        // Do Nothing
    }
    
    return $new_str->format( $format);
}


/**
 * Function Name : getFlagVideos()
 * To load the videos based on the user
 *
 * @param int $id User Id
 *
 * @return array of values
 */
function getFlagVideos($id) {
    // Load Flag videos based on logged in user id
    $model = Flag::where('flags.user_id', $id)
        ->leftJoin('video_tapes' , 'flags.video_tape_id' , '=' , 'video_tapes.id')
        ->where('video_tapes.is_approved' , 1)
        ->where('video_tapes.status' , 1)
        ->pluck('video_tape_id')
        ->toArray();
    // Return array of id's
    return $model;
}

function total_subscription_revenue($id = "") {

    if($id) {
        return UserPayment::where('subscription_id' , $id)->sum('amount');
    }
    return UserPayment::sum('amount');
}

function loadChannels() {

    $age = 0;

    if(Auth::check()) {

        $age = \Auth::user()->age_limit;

        $age = $age ? ($age >= Setting::get('age_limit') ? 1 : 0) : 0;

    }

    $model = Channel::where('channels.is_approved', DEFAULT_TRUE)
                ->select('channels.*', 'video_tapes.id as video_tape_id', 'video_tapes.is_approved',
                    'video_tapes.status', 'video_tapes.channel_id')
                ->leftJoin('video_tapes', 'video_tapes.channel_id', '=', 'channels.id')
                ->where('channels.status', DEFAULT_TRUE)
                ->where('video_tapes.is_approved', DEFAULT_TRUE)
                ->where('video_tapes.status', DEFAULT_TRUE)
                ->where('video_tapes.publish_status', DEFAULT_TRUE)
                ->where('video_tapes.age_limit','<=', $age)
                ->havingRaw('COUNT(video_tapes.id) > 0')
                ->groupBy('video_tapes.channel_id')
                ->get();
   
    return $model;
}


function getChannels($id = null) {

    $model = Channel::where('is_approved', DEFAULT_TRUE)->where('status', DEFAULT_TRUE);


    if ($id) {
        $model->where('user_id', $id);
    }

    $response = $model->get();

    return $response;
}

// changes by vidhya

function getAmountBasedChannel($id) {

    $ppv_amount = VideoTape::where('channel_id', $id)->sum('user_ppv_amount');

    $ad_amount = VideoTape::where('channel_id', $id)->sum('amount');

    /*$payment = 0;

    foreach ($videos as $key => $value) {

        $payment += $value->sum('user_ppv_amount') ? $value->sum('user_ppv_amount') : 0;

        // $payment += PayPerView::where('video_id', $value->video_tape_id)->sum('user_ppv_amount');

    }*/

    $amount = $ppv_amount+$ad_amount;

    return $amount;

}

function getAmountBasedCategory($id) {

    $ppv_amount = VideoTape::where('category_id', $id)->sum('user_ppv_amount');

    $ad_amount = VideoTape::where('category_id', $id)->sum('amount');

    /*$payment = 0;

    foreach ($videos as $key => $value) {

        $payment += $value->sum('user_ppv_amount') ? $value->sum('user_ppv_amount') : 0;

        // $payment += PayPerView::where('video_id', $value->video_tape_id)->sum('user_ppv_amount');

    }*/

    $amount = $ppv_amount+$ad_amount;

    return $amount;

}

// changes by vidhya


function ppv_amount($id) {

    $model = VideoTape::find($id);

    if($model) {

        return $model->user_ppv_amount > 0 ? $model->user_ppv_amount : 0;

    } else {

        return 0;
    }


}

// Based on the request type, it will return string value for that request type

function redeem_request_status($status) {
    
    if($status == REDEEM_REQUEST_SENT) {
        $string = tr('REDEEM_REQUEST_SENT');
    } elseif($status == REDEEM_REQUEST_PROCESSING) {
        $string = tr('REDEEM_REQUEST_PROCESSING');
    } elseif($status == REDEEM_REQUEST_PAID) {
        $string = tr('REDEEM_REQUEST_PAID');
    } elseif($status == REDEEM_REQUEST_CANCEL) {
        $string = tr('REDEEM_REQUEST_CANCEL');
    } else {
        $string = tr('REDEEM_REQUEST_SENT');
    }

    return $string;
}

/**
 * Function : add_to_redeem()
 * 
 * @param $id = role ID
 *
 * @param $amount = earnings
 *
 * @description : If the role earned any amount, use this function to update the redeems
 *
 */

function add_to_redeem($id , $amount , $admin_amount = 0) {

    \Log::info('Add to Redeem Start');

    if($id && $amount) {

        $redeems_details = Redeem::where('user_id' , $id)->first();

        if(!$redeems_details) {

            $redeems_details = new Redeem;

            $redeems_details->user_id = $id;
        }

        $redeems_details->total = $redeems_details->total + $amount;
        
        $redeems_details->remaining = $redeems_details->remaining+$amount;

        // Update the earnings for moderator and admin amount

        $redeems_details->total_admin_amount = $redeems_details->total_admin_amount + $admin_amount;

        $redeems_details->total_user_amount = $redeems_details->total_user_amount + $amount;
        
        $redeems_details->save();   
    }

    \Log::info('Add to Redeem End');
}

function get_banner_count() {
    return VideoTape::where('is_banner' , DEFAULT_TRUE)->count();
}

function getTypeOfAds($ad_type) {

        $types = [];

        if ($ad_type) {

            $exp = explode(',', $ad_type);

            $ads = [];

            foreach ($exp as $key => $value) {

                if ($value == PRE_AD) {
                
                    $ads[] =  'Pre Ad';

                }

                if ($value == POST_AD) {
                
                    $ads[] =  'Post Ad';

                }


                if ($value == BETWEEN_AD) {
                
                    $ads[] =  'Between Ad';

                }

            }

            // $types = implode(',', $ads);

            $types = $ads;

        }

        return $types;
}

function pages() {

    $all_pages = Page::all();

    return $all_pages;
}


function get_history_count($id) {

    $base_query = UserHistory::where('user_histories.user_id' , $id)
                ->leftJoin('video_tapes' ,'user_histories.video_tape_id' , '=' , 'video_tapes.id')
                ->where('video_tapes.is_approved' , DEFAULT_TRUE)
                ->where('video_tapes.status' , DEFAULT_TRUE)
                ->where('video_tapes.publish_status' , DEFAULT_TRUE);
        
    if (Auth::check()) {

        // Check any flagged videos are present

        $flag_videos = flag_videos(Auth::user()->id);

        if($flag_videos) {
            $base_query->whereNotIn('video_tapes.id',$flag_videos);
        }
    
    }

    $data = $base_query->count();

    return $data;

}

function get_wishlist_count($video_tape_id) {
    
    $data = Wishlist::where('wishlists.user_id' , $video_tape_id)
                ->leftJoin('video_tapes' ,'wishlists.video_tape_id' , '=' , 'video_tapes.id')
                ->where('video_tapes.is_approved' , DEFAULT_TRUE)
                ->where('video_tapes.status' , DEFAULT_TRUE)
                ->where('wishlists.status' , DEFAULT_TRUE)
                ->count();

    return $data;

}

function get_video_comment_count($video_id) {

    $count = UserRating::where('video_tape_id' , $video_id)
                ->leftJoin('video_tapes' ,'user_ratings.video_tape_id' , '=' , 'video_tapes.id')
                ->where('video_tapes.is_approved' , DEFAULT_TRUE)
                ->where('video_tapes.status' , DEFAULT_TRUE)
                ->count();

    return $count;

}

function convertToBytes($from){
    $number=substr($from,0,-2);
    switch(strtoupper(substr($from,-2))){
        case "KB":
            return $number*1024;
        case "MB":
            return $number*pow(1024,2);
        case "GB":
            return $number*pow(1024,3);
        case "TB":
            return $number*pow(1024,4);
        case "PB":
            return $number*pow(1024,5);
        default:
            return $from;
    }
}

function checkSize() {

    $php_ini_upload_size = convertToBytes(ini_get('upload_max_filesize')."B");

    $php_ini_post_size = convertToBytes(ini_get('post_max_size')."B");

    $setting_upload_size = convertToBytes(Setting::get('upload_max_size')."B");

    $setting_post_size = convertToBytes(Setting::get('post_max_size')."B");

    if(($php_ini_upload_size < $setting_upload_size) || ($php_ini_post_size < $setting_post_size)) {

        return true;
    }

    return false;
}


function videos_count($channel_id, $channel_type = OTHERS_CHANNEL) {

    $videos_query = VideoTape::leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                        ->where('video_tapes.channel_id' , $channel_id)
                        ->leftJoin('categories' , 'categories.id' , '=' , 'video_tapes.category_id')
                        ->videoResponse()
                        ->orderby('video_tapes.created_at' , 'asc');
    if (Auth::check()) {
        // Check any flagged videos are present
        $flagVideos = getFlagVideos(Auth::user()->id);

        if($flagVideos) {
            $videos_query->whereNotIn('video_tapes.id', $flagVideos);
        }
    }

    if ($channel_type == OTHERS_CHANNEL) {

        $videos_query->where('video_tapes.is_approved' , DEFAULT_TRUE)
                        ->where('video_tapes.status' , DEFAULT_TRUE)
                        ->where('video_tapes.publish_status' , DEFAULT_TRUE)
                        ->where('channels.status', DEFAULT_TRUE)
                        ->where('channels.is_approved', DEFAULT_TRUE)
                        ->where('categories.status', CATEGORY_APPROVE_STATUS);
    }

    $cnt = $videos_query->count();

    return $cnt ? $cnt : 0;
}


function check_channel_status($user_id, $channel_id) {

    $model = ChannelSubscription::where('user_id', $user_id)->where('channel_id', $channel_id)->first();

    return $model ? $model->id : 0;

}

function getActiveLanguages() {
    return Language::where('status', DEFAULT_TRUE)->get();
}


function readFileLength($file)  {

    $variableLength = 0;
    if (($handle = fopen($file, "r")) !== FALSE) {
         $row = 1;
         while (($data = fgetcsv($handle, 1000, "\n")) !== FALSE) {
            $num = count($data);
            $row++;
            for ($c=0; $c < $num; $c++) {
                $exp = explode("=>", $data[$c]);
                if (count($exp) == 2) {
                    $variableLength += 1; 
                }
            }
        }
        fclose($handle);
    }

    return $variableLength;
}


function checkAge($request , $user_age = "") {

    $age = 18;

    if($user_age) {
        
        $age = $user_age ? ($user_age >= Setting::get('age_limit') ? $request->age : 18) : 18;

    } else {

        if($request->all()) {

            $age = $request->age ? ($request->age >= Setting::get('age_limit') ? $request->age : 18) : 18;

        }
    }

    return $age;
}


function subscriberscnt($id = null) {

    $list = [];

    if (!$id) {

        $channels = getChannels(Auth::user()->id);

        foreach ($channels as $key => $value) {
            $list[] = $value->id;
        }

    } else {

        $list[] = $id;

    }

    $subscribers = ChannelSubscription::whereIn('channel_subscriptions.channel_id', $list)
                    ->count();
    return $subscribers;
}




function getUserTime($time, $timezone = "Asia/Kolkata", $format = "H:i:s") {

    if ($timezone) {

        $new_str = new DateTime($time, new DateTimeZone('UTC') );

        $new_str->setTimeZone(new DateTimeZone( $timezone ));

        return $new_str->format($format);

    }

}

function total_video_revenue_merge() {

    $model = LiveVideoPayment::sum('amount');

    return $model;
}



function isPaidAmount($id) {

    if(Auth::check()) {

        $video = LiveVideoPayment::where('live_video_id', $id)
                    ->where('live_video_viewer_id', Auth::user()->id)->first();

        if ($video) {

            return true;

        } else {

            return false;  
        }

    } else {

        return false;

    }

}

function admin_commission($id) {

    $video = LiveVideoPayment::where('live_video_id', $id)->sum('admin_amount');

    return $video ? $video : 0;
}

function user_commission($id) {

    $video = LiveVideoPayment::where('live_video_id', $id)->sum('user_amount');

    return $video ? $video : 0;
}

function number_format_short( $n, $precision = 1 ) {
   
    if ($n < 900) {
        // 0 - 900
        $n_format = number_format($n, $precision);
        $suffix = '';
    } else if ($n < 900000) {
        // 0.9k-850k
        $n_format = number_format($n / 1000, $precision);
        $suffix = 'K';
    } else if ($n < 900000000) {
        // 0.9m-850m
        $n_format = number_format($n / 1000000, $precision);
        $suffix = 'M';
    } else if ($n < 900000000000) {
        // 0.9b-850b
        $n_format = number_format($n / 1000000000, $precision);
        $suffix = 'B';
    } else {
        // 0.9t+
        $n_format = number_format($n / 1000000000000, $precision);
        $suffix = 'T';
    }
  // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
  // Intentionally does not affect partials, eg "1.50" -> "1.50"
    if ( $precision > 0 ) {
        $dotzero = '.' . str_repeat( '0', $precision );
        $n_format = str_replace( $dotzero, '', $n_format );
    }
    return $n_format . $suffix;
}

function getMinutesBetweenTime($startTime, $endTime) {

    $to_time = strtotime($endTime);

    $from_time = strtotime($startTime);

    $diff = abs($to_time - $from_time);

    if ($diff <= 0) {

        $diff = 0;

    } else {

        $diff = round($diff/60);

    }

    return $diff;

}


/**
 * Function Name : watchFullVideo()
 * To check whether the user has to pay the amount or not
 * 
 * @param integer $user_id User id
 * @param integer $user_type User Type
 * @param integer $video_id Video Id
 * 
 * @return true or not
 */
function watchFullVideo($user_id, $user_type, $video) {


    if ($user_type == DEFAULT_TRUE) {

        if ($video->ppv_amount == 0) {
            return true;
        }else if($video->ppv_amount > 0 && ($video->type_of_user == PAID_USER || $video->type_of_user == BOTH_USERS)) {

            $paymentView = PayPerView::where('user_id', $user_id)->where('video_id', $video->video_tape_id)
                ->where('amount', '>', 0)
                ->orderBy('created_at', 'desc')
                ->first();
            if ($video->type_of_subscription == ONE_TIME_PAYMENT) {
                // Load Payment view
                if ($paymentView) {
                    return true;
                }
            } else {
                if ($paymentView) {
                    if ($paymentView->status == DEFAULT_FALSE) {
                        return true;
                    }
                }   
            }
        } else if($video->ppv_amount > 0 && $video->type_of_user == NORMAL_USER){
            return true;
        }
    } else {

        if ($video->ppv_amount == 0) {
            return true;
        } else if($video->ppv_amount > 0 && ($video->type_of_user == NORMAL_USER || $video->type_of_user == BOTH_USERS)) {
            $paymentView = PayPerView::where('user_id', $user_id)->where('video_id', $video->video_tape_id)
            ->where('amount', '>', 0)
            ->orderBy('created_at', 'desc')->first();

            if ($video->type_of_subscription == ONE_TIME_PAYMENT) {
                // Load Payment view
                if ($paymentView) {
                    return true;
                }
            } else {

                if ($paymentView) {
                    if ($paymentView->status == DEFAULT_FALSE) {
                        return true;
                    }
                }  
            }
        } else if($video->ppv_amount > 0 && $video->type_of_user == PAID_USER){
            return true;
        }
    }
    return false;
}

function displayVideoDetails($data,$userId) {

    
    $user = User::find($userId);

    if (Setting::get('is_payper_view')) {

        if ($userId == $data->channel_created_by) {

            $ppv_status = true;

            $url = route('user.single', $data->video_tape_id);

        } else {

            if ($data->ppv_amount > 0) {

                $ppv_status = $user ? VideoRepo::pay_per_views_status_check($user->id, $user->user_type, $data)->getData()->success : false;

               
                if ($ppv_status) {

                    $url = route('user.single', $data->video_tape_id);


                } else {
                
                    if ($userId) {

                        Log::info(print_r($user, true));

                        if ($user) {

                            if ($user->user_type) {        

                                $url = route('user.subscription.ppv_invoice', $data->video_tape_id);

                            } else {

                                $url = route('user.subscription.pay_per_view', $data->video_tape_id);
                            }

                        } else {

                            $url = route('user.subscription.pay_per_view', $data->video_tape_id);

                        } 

                    } else {

                        $url = route('user.subscription.pay_per_view', $data->video_tape_id);
                    }              
                }

            } else {

                $ppv_status = true;

                $url = route('user.single', $data->video_tape_id);
            }
        }

    } else {

        $ppv_status = true;

        $url = route('user.single', $data->video_tape_id);

    }

    $is_ppv_status = DEFAULT_TRUE;

    if ($user) {

        $is_ppv_status = ($data->type_of_user == NORMAL_USER || $data->type_of_user == BOTH_USERS) ? ( ( $user->user_type == DEFAULT_FALSE ) ? DEFAULT_TRUE : DEFAULT_FALSE ) : DEFAULT_FALSE; 

    } 

    $wishlist_status = $history_status = $like_status = 0;

    if ($user) {

        $wishlist_status = Helper::wishlist_status($data->video_tape_id, $user->id);

        $history_status = Helper::history_status($data->video_tape_id,$user->id);

        $like_status = Helper::like_status($user->id,$data->video_tape_id);
    }

    $pay_per_view_status = VideoRepo::pay_per_views_status_check($user ? $user->id : '', $user ? $user->user_type : '', $data)->getData();

    $ppv_notes = !$pay_per_view_status->success ? ($data->type_of_user == 1 ? tr('normal_user_note') : tr('paid_user_note')) : ''; 

    $tags = VideoTapeTag::select('tag_id', 'tags.name as tag_name')
                ->leftJoin('tags', 'tags.id', '=', 'video_tape_tags.tag_id')
                ->where('video_tape_id', $data->video_tape_id)->get()->toArray();

    $category = Category::find($data->category_id);

    $category_unique_id = $category ? $category->unique_id : '';

    $playlists = Playlist::where('playlists.status', APPROVED)->where('user_id', $userId)->select('playlists.id as playlist_id', 'title', 'user_id')->get();

    foreach ($playlists as $key => $playlist_details) {

        $check_video = PlaylistVideo::where('playlist_id', $playlist_details->playlist_id)->where('video_tape_id', $data->video_tape_id)->count();

        $playlist_details->is_selected = $check_video ? YES : NO;
    }

   
    $model = [
        'video_tape_id'=>$data->video_tape_id,
        'video_type'=>$data->video_type,
        'title'=>$data->title,
        'video_image'=>$data->default_image,
        'watch_count'=>number_format_short($data->watch_count),
        'duration'=>$data->duration,
        'ppv_status'=>$ppv_status,
        'ppv_amount'=>$data->ppv_amount,
        'channel_id'=>$data->channel_id,
        'channel_name'=>$data->channel_name,
        'channel_image'=>$data->channel_picture,
        'created_at'=>date('d F Y', strtotime($data->created_at)),
        // 'created_at'=>$data->created_at->diffForHumans(),   
        'ad_status'=>$data->ad_status,
        'description'=>$data->description,
        'ratings'=>$data->ratings ? $data->ratings : 0,
        'amount'=>$data->amount,
        'url'=>$url,
        'type_of_user'=>$data->type_of_user,
        'type_of_subscription'=>$data->type_of_subscription,
        'user_ppv_amount' => $data->user_ppv_amount,
        'status'=>$data->status,
        'is_approved'=>$data->is_approved,
        'pay_per_view_status'=>$pay_per_view_status->success,
        'is_ppv_subscribe_page'=>$is_ppv_status, // 0 - Dont show subscribe+ppv_ page 1- Means show ppv subscribe page
        'currency'=>Setting::get('currency'),
        // 'publish_time'=>date('F Y', strtotime($data->publish_time)),
        'publish_time'=>$data->created_at->diffForHumans(),
        'is_liked'=>$like_status,
        'wishlist_status'=>$wishlist_status,
        'history_status'=>$history_status,
        'subtitle'=>$data->subtitle,
        'embed_link'=>route('embed_video', array('u_id'=>$data->unique_id)),
        'currency'=>Setting::get('currency'),
        'share_url'=>route('user.single' , $data->video_tape_id),
        'ppv_notes'=>$ppv_notes,
        'category_id'=>$data->category_id,
        'category_unique_id'=>$category_unique_id,
        'category_name'=>$data->category_name,
        'tags'=>$tags,
        'is_my_channel' => $userId == $data->channel_created_by ? YES : NO
        // 'playlists' => $playlists
    ];


    return $model;

}

/**
 * Function Name : total_video_revenue
 * To sum all the payment based on video subscription
 *
 * @return amount
 */


function total_video_revenue($type = "") {

    return PayPerView::sum('amount');

}

function total_ppv_video_revenue($type = "") {
    if($type == 'admin') {
        return PayPerView::sum('admin_ppv_amount');
    }
    return PayPerView::sum('amount');
}


/**
 * Function Name : user_total_amount
 * To sum all the payment based on video subscription
 *
 * @return amount
 */
function user_total_amount() {
    return PayPerView::where('user_id', Auth::user()->id)->sum('amount');
}

function get_commission_percentage($total , $actual_amount) {

    $percentage = $total > 0 ? ($actual_amount/$total ) * 100 : 0;

    return $percentage;
}


/**
 * Function Name : total_video_revenue
 * To sum all the payment based on video subscription
 *
 * @return amount
 */
function total_ppv_admin_video_revenue() {
    return PayPerView::sum('admin_ppv_amount');
}

/**
 * function routefreestring()
 * 
 * @uses used for remove the route parameters from the string
 *
 * @created Maheswari S
 *
 * @edited Maheswari S
 *
 * @param string $string
 *
 * @return Route parameters free string
 */

function routefreestring($string) {

    $search = array(' ', '&', '%', "?",'=','{','}','$');

    $replace = array('-', '-', '-' , '-', '-', '-' , '-','-');

    $string = str_replace($search, $replace, $string);

    return $string;
   
}

/**
 * Function Name : convertDurationIntoSeconds()
 *
 * Convert duration into seconds
 *
 * @param - duration
 *
 * @return response of seconds
 */
function convertDurationIntoSeconds($str_time) {

    $time = explode(':', $str_time);

    if(count($time) == 3) {

        return ($time[0]*3600) + ($time[1]*60) + $time[2];

    }

    return 0;

}


/**
 * Function Name getVideoAdsTpe()
 *
 * To list out the types of ad
 *
 * @param integer $id - Video Id
 *
 * @return response of types of ad
 */
function getVideoAdsTpe($video_id) {

    $video_ads = VideoAd::where('video_tape_id', $video_id)->first();

    $types = [];

    if ($video_ads) {

        $ads = $video_ads->types_of_ad;

        $types = getTypeOfAds($ads);


    }

    return $types;

}



/**
 * Function Name : amount_convertion()
 *
 * To change the amount based on percentafe (Percentage/absolute)
 *
 * @created Vithya R
 *
 * @updated
 *
 * @param - Percentage and amount
 *
 * @return response of converted amount
 */
function amount_convertion($percentage, $amt) {

    $converted_amt = $amt * ($percentage/100);

    return $converted_amt;

}

/**
 * Function Name : seoUrl()
 *
 * To change the string/ sentance into seo url
 *
 * @created Vithya R
 *
 * @updated
 *
 * @param - String
 *
 * @return response of string url
 */
function seoUrl($string) {
    //Lower case everything
    $string = strtolower($string);
    //Make alphanumeric (removes all other characters)
    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
    //Clean up multiple dashes or whitespaces
    $string = preg_replace("/[\s-]+/", " ", $string);
    //Convert whitespaces and underscore to dash
    $string = preg_replace("/[\s_]/", "-", $string);
    return $string;
}

function userChannelId() {

    $userId = Auth::user()->id;

    $channel = Channel::where('user_id', $userId)->first();

    $channelid = $channel ? $channel->id : '';

    if($channelid) {

        return route('user.video_upload', ['id'=>$channelid]);

    } else {

        if(Auth::check())  {

            if(Setting::get('create_channel_by_user') == CREATE_CHANNEL_BY_USER_ENABLED || Auth::user()->is_master_user == 1) {

                if(Auth::user()->user_type) {

                    return route('user.create_channel');

                }
            }
        }

        return route('user.subscriptions');
    }
}

function formatted_amount($amount = 0.00, $currency = "") {

    $currency = $currency ?: Setting::get('currency', '$');

    $formatted_amount = $currency."".$amount ?: 0.00;

    return $formatted_amount;
}

function generate_payment_id($user_id = 0, $other_id = 0, $amount = 0) {

    $payment_id = $user_id."-".$other_id."-".strtoupper(uniqid()).$amount;

    return $payment_id;
}

function type_of_user($status) {

    $list_status = [
            NORMAL_USER => tr('normal_users'),
            PAID_USER => tr('paid_users'),
            BOTH_USERS => tr('both_users')
        ];
    return isset($list_status[$status]) ? $list_status[$status] : "NONE" ;
}

function type_of_subscription($status) {

    $list_status = [
            ONE_TIME_PAYMENT => tr('one_time_payment'),
            RECURRING_PAYMENT => tr('recurring_payment')
        ];
    return isset($list_status[$status]) ? $list_status[$status] : "NONE" ;
}

function common_date($date , $timezone = "America/New_York" , $format = "d M Y") {

    if($date == "0000-00-00 00:00:00") {
        
        return $date;
    }

    if($timezone) {

        $date = convertTimeToUSERzone($date , $timezone , $format);

    }

    return date($format , strtotime($date));

}

function video_type_text($video_type) {

    $video_types = [
        VIDEO_TYPE_UPLOAD => apitr('VIDEO_TYPE_UPLOAD'),
        VIDEO_TYPE_LIVE => apitr('VIDEO_TYPE_LIVE'),
        VIDEO_TYPE_YOUTUBE => apitr('VIDEO_TYPE_YOUTUBE'),
        VIDEO_TYPE_OTHERS => apitr('VIDEO_TYPE_OTHERS')
    ];

    return isset($video_types[$video_type]) ? $video_types[$video_type] : "NONE";

}

/**
 * @method check_push_notification_configuration()
 *
 * @uses check the push notification configuration
 *
 * @created Vidhya
 *
 * @updated Vidhya
 *
 * @param boolean $is_user
 *
 * @return boolean $push_notification_status
 */

function check_push_notification_configuration() {

    if(Setting::get('user_fcm_sender_id') && Setting::get('user_fcm_server_key')) {
        return YES;
    }

    return NO;
}

function get_video_tape($tape_id) {
    return VideoTape::find($tape_id);
}

function get_video_title($title) {
    $video_title = preg_replace('~[\\\\/:*?"<>|]~', '', $title);
    $video_title = str_replace(' ', '-', $video_title);
    return $video_title;
}
function get_current_user_name($video_id) {
    $video = VideoTape::find($video_id);
    $user_name = "Not found";
    if($video) {
        $user = User::find($video->user_id);
        if($user)
            $user_name = $user->name;
    }
    return $user_name;
}
function get_folder_count($id, $user_name) {
    $video = VideoTape::find($id);
    $folders = [];
    if($video) {
        $path = 'uploads/videos/'.$user_name.'/'.get_video_title($video->title);
        if($video) {
            $path = public_path($path);
            if(File::isDirectory($path))
                $folders = File::directories($path);     
        }
    }
    return count($folders);
}

function get_video_files_count($id, $user_name) {
    $video = VideoTape::find($id);
    $files = [];
    if($video) {
        $path = 'uploads/videos/'.$user_name.'/'.get_video_title($video->title);
        if($video) {
            $path = public_path($path);
            if(File::isDirectory($path))
                $files = File::allFiles($path);     
        }
    }
    return count($files);
}
function get_video_filename($video_name_string, $type) {
    $ciphering = "BF-CBC"; 
    // Use OpenSSl encryption method 
    $iv_length = openssl_cipher_iv_length($ciphering); 
    $options = 0; 
    // Use random_bytes() function which gives 
    // randomly 16 digit values 
    $encryption_iv = random_bytes($iv_length); 
    // Alternatively, we can use any 16 digit 
    // characters or numeric for iv 
    $encryption_key = openssl_digest(php_uname(), 'MD5', TRUE); 
    if($type == 'encrypt') {
        // Encryption of string process starts 
        $encryption = openssl_encrypt($video_name_string, $ciphering, 
                $encryption_key, $options, $encryption_iv); 
        return $encryption;        
    }
    
    $decryption_iv = random_bytes($iv_length); 
    
    // Store the decryption key 
    $decryption_key = openssl_digest(php_uname(), 'MD5', TRUE); 
    
    if($type == 'decrypt') {
        // Descrypt the string 
        $decryption = openssl_decrypt ($video_name_string, $ciphering, 
                $decryption_key, $options, $encryption_iv); 
        return $decryption;        
    }    
}