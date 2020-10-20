<?php 

   namespace App\Helpers;

    use Hash;

    use App\Admin;

    use App\User;

    use Auth;

    use AWS;

    use App\Requests;

    use Mail;

    use File;

    use Log;

    use Storage;

    use Setting;

    use DB;

    use App\Jobs\OriginalVideoCompression;

    use App\VideoTape;

    use App\Wishlist;

    use App\UserHistory;

    use App\UserRating;

    use App\LiveVideo;

    use Intervention\Image\ImageManagerStatic as Image;

    use App\LikeDislikeVideo;
    
    use App\PayPerView;

    use Mailgun\Mailgun;

    class Helper
    {

        /**
         * Used to generate index.php
         *
         * 
         */

        public static function generate_index_file($folder) {

            $filename = public_path()."/".$folder."/index.php"; 

            if(!file_exists($filename)) {

                $index_file = fopen($filename,'w');

                $sitename = Setting::get("site_name");

                fwrite($index_file, '<?php echo "You Are trying to access wrong path!!!!--|E"; ?>');       

                fclose($index_file);
            }
        
        }

        public static function clean($string)
        {
            $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

            return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
        }

        public static function web_url()
        {
            return url('/');
        }

        public static function generate_email_code($value = "")
        {
            return uniqid($value);
        }

        public static function generate_email_expiry()
        {
            return time() + 24*3600*30;  // 30 days
        }

        // Check whether email verification code and expiry

        public static function check_email_verification($verification_code , $data , &$error) 
        {

            // Check the data exists

            if($data) {

                // Check whether verification code is empty or not

                if($verification_code) {

                    if ($verification_code !=  $data->verification_code ) {

                        $error = tr('verification_code_mismatched');

                        return FALSE;

                    }

                }
                    
                // Check whether verification code expiry 

                if ($data->verification_code_expiry > time()) {

                    // Token is valid

                    $error = NULL;

                    return true;

                } else {

                    $data->verification_code = Helper::generate_email_code();

                    $data->verification_code_expiry = Helper::generate_email_expiry();

                    $data->save();

                    // If code expired means send mail to that user

                    $subject = tr('verification_code_title');
                    $email_data = $data;
                    $page = "emails.welcome";
                    $email = $data['email'];
                    $result = Helper::send_email($page,$subject,$email,$email_data);

                    $error = tr('verification_code_expired');

                    return FALSE;
                }
            }
        }

        // Note: $error is passed by reference
        public static function is_token_valid($entity, $id, $token, &$error)
        {
            if (
                ( $entity== 'USER' && ($row = User::where('id', '=', $id)->where('token', '=', $token)->first()) ) ||
                ( $entity== 'PROVIDER' && ($row = Provider::where('id', '=', $id)->where('token', '=', $token)->first()) )
            ) {
                if ($row->token_expiry > time()) {
                    // Token is valid
                    $error = NULL;
                    return $row;
                } else {
                    $error = array('success' => false, 'error_messages' => Helper::get_error_message(103), 'error_code' => 103);
                    return FALSE;
                }
            }
            $error = array('success' => false, 'error_messages' => Helper::get_error_message(104), 'error_code' => 104);
            return FALSE;
        }

        // Convert all NULL values to empty strings
        public static function null_safe($arr)
        {
            $newArr = array();
            foreach ($arr as $key => $value) {
                $newArr[$key] = ($value == NULL) ? "" : $value;
            }
            return $newArr;
        }

        public static function generate_token()
        {
            return Helper::clean(Hash::make(rand() . time() . rand()));
        }

        public static function generate_token_expiry()
        {
            return time() + 24*3600*30;  // 30 days
        }

        public static function send_email($page,$subject,$email,$email_data) {

            \Log::info(config('mail.username'));
            Log::info("Password :".print_r(config('mail.password'),true));
            Log::info("username :".print_r(config('mail.username'),true));
            
            if(Setting::get('email_notification') == YES) {

                if(config('mail.username') && config('mail.password')) {

                    try {

                       /* $mail_status = Mail::queue($page, array('email_data' => $email_data), function ($message) use ($email, $subject) {

                            $message->to($email)->subject($subject);
                            
                        });*/

                        $site_url=url('/');

                        $isValid = 1;

                        if(envfile('MAIL_DRIVER') == 'mailgun' && Setting::get('MAILGUN_PUBLIC_KEY')) {

                            Log::info("isValid - STRAT");

                            # Instantiate the client.
                            
                            $email_address = new Mailgun(Setting::get('MAILGUN_PUBLIC_KEY'));

                            $validateAddress = $email;

                            # Issue the call to the client.
                            $result = $email_address->get("address/validate", array('address' => $validateAddress));

                            # is_valid is 0 or 1

                            $isValid = $result->http_response_body->is_valid;

                            Log::info("isValid FINAL STATUS - ".$isValid);

                        }

                        Log::info("Status :".print_r($isValid,true));
                    
                        if($isValid) {

                            if (Mail::queue($page, array('email_data' => $email_data,'site_url' => $site_url), 
                                    function ($message) use ($email, $subject) {

                                        $message->to($email)->subject($subject);
                                    }
                            )) {

                               //  return Helper::get_message(106);

                            } else {

                                throw new Exception(Helper::get_error_message(123));
                                
                            }

                        } else {

                           // throw new Exception(Helper::get_message(106), 106);

                        }

                    } catch(Exception $e) {

                        \Log::info($e);

                        $message = Helper::get_error_message(123);

                        $response_array = ['success' => false , 'message' => $message, 'error_code' =>123];

                        return json_decode(json_encode($response_array));

                    }

                    $message = Helper::get_message(105);

                    $response_array = ['success' => true , 'message' => $message,'error_code' =>105];

                    return json_decode(json_encode($response_array));

                } else {

                    $message = Helper::get_error_message(123);

                    $response_array = ['success' => false , 'message' => $message,'error_code' => 123];

                    return json_decode(json_encode($response_array));
                }

            } else {
                
                Log::info("email_notification OFF");
            }
        }

        public static function get_error_message($code)
        {
            switch($code) {

                case 9001:
                    $string = tr('invalid_input');
                    break;
                case 101:
                    $string = tr('invalid_input');
                    break;
                case 102:
                    $string = tr('email_address_already_use');
                    break;
                case 103:
                    $string = tr('token_expiry');
                    break;
                case 104:
                    $string = tr('invalid_token');
                    break;
                case 105:
                    $string = tr('invalid_email_password');
                    break;
                case 106:
                    $string = tr('all_fields_required');
                    break;
                case 107:
                    $string = tr('current_password_incorrect');
                    break;
                case 108:
                    $string = tr('password_donot_match');
                    break;
                case 109:
                    $string = tr('problem_with_server_try_again');
                    break;
                case 111:
                    $string = tr('email_not_activated');
                    break;
                case 115:
                    $string = tr('invalid_refresh_token');
                    break;
                case 123:
                    $string = tr('something_went_wrong_error');
                    break;
                case 124:
                    $string = tr('email_not_registered');
                    break;
                case 125:
                    $string = tr('not_valid_social_register');
                    break;
                case 130:
                    $string = tr('no_result_found');
                    break;
                case 131:
                    $string = tr('password_doesnot_match');
                    break;
                case 132:
                    $string = tr('provider_id_not_found');
                    break;
                case 133:
                    $string = tr('user_id_not_found');
                    break;
                case 141:
                    $string = tr('something_went_wrong_paying_amount');
                    break;
                case 144:
                    $string = tr('account_disabled_admin');
                    break;
                case 145:
                    $string = tr('video_already_added_history');
                    break;
                case 146:
                    $string = tr('something_error_try_again');
                    break;
                case 147:
                    $string = tr('redeem_disabled_by_admin');
                    break;
                case 148:
                    $string = tr('minimum_redeem_not_have');
                    break;
                case 149:
                    $string = tr('redeem_wallet_empty');
                    break;
                case 150:
                    $string = tr('redeem_request_status_mismatch');
                    break;
                case 151:
                    $string = tr('redeem_not_found');
                    break;
                
                case 162:
                    $string = tr('failed_to_upload');

                    break;

                case 163 :
                    $string = tr('streaming_stopped');

                    break;
                case 164:
                    
                    $string = tr('not_yet_started');

                    break;

                case 165 :
                    $string = tr('no_video_found');
                    break;

                case 166 :

                    $string = tr('no_user_found');

                    break;
                case 167 :

                    $string = tr('user_not_subscribed');

                    break;

                case 168 :
                    $string = tr('channel_create_error');
                    break;
                case 169 :
                    $string = tr('subscription_error');
                    break;
                case 170 :

                    $string = tr('already_you_have_video');

                    break;

                case 171:
                    $string = tr('subscription_amount_should_be_grater');
                    break;

                case 172:
                    $string = tr('video_amount_should_be_grater');
                    break;

                case 173:
                    $string = tr('expired_coupon_code');
                    break;

                 case 174:
                    $string = tr('coupon_not_found');
                    break;

                case 175:
                    $string = tr('subscription_autorenewal_already_cancelled');
                    break;
                case 176:
                    $string = tr('subscription_autorenewal_already_enabled');
                    break;
                case 177:
                    $string = tr('user_payment_details_not_found');
                    break;

               
                case 178:
                    $string = tr('coupon_inactive_status');
                    break;

                case 179:
                    $string = tr('subscription_not_found');
                    break;

                case 180:
                    $string = tr('subscription_inactive_status');
                    break;

                case 901:
                    $string = tr('default_card_not_available');
                    break;
                case 902:
                    $string = tr('something_went_payment_configuration');
                    break;
                case 903:
                    $string = tr('payment_not_completed_try_again');
                    break;

                case 906:
                    $string = tr('video_data_not_found');
                    break;
                case 175:
                    $string = tr('playlist_not_found');
                    break;
                case 176:
                    $string = tr('playlist_video_not_found');
                    break;
                case 179:
                    $string = tr('playlist_save_error');
                    break;
                case 180:
                    $string = tr('playlist_not_associated_to_user');
                    break;

                case 906:
                    $string = tr('video_data_not_found');
                    break;

                // version 5.0

                case 50101:
                    $string = tr('referral_code_invalid');
                    break;
                case 50102:
                    $string = tr('channel_not_found');
                    break;
                case 50103:
                    $string = tr('not_your_channel');
                    break;
                case 50104:
                    $string = tr('user_action_not_enough_data');
                    break;
                case 50105:
                    $string = tr('user_video_tapes_status_update_failed');
                    break;
                case 50106:
                    $string = tr('user_video_tapes_ppv_status_failed');
                    break;
                case 50107:
                    $string = tr('user_video_tapes_delete_error');
                    break;

                case 50108:
                    $string = tr('add_card_is_not_enabled');
                    break;


                case 1000:
                    $string = tr('video_is_in_flag_list');
                    break;
                case 1001:
                    $string = tr('video_not_found');
                    break; 

                case 1002:
                    $string = tr('video_is_declined');
                    break;

                case 502:
                    $string = tr('user_account_declined_by_admin');
                    break;
                case 503:
                    $string = tr('user_account_email_not_verified');
                    break;
                case 504:
                    $string = tr('login_account_record_not_found');
                    break;

                case 505:
                    $string = tr('user_wishlist_video_exists');
                    break;

                case 506:
                    $string = tr('user_wishlist_delete_error');
                    break;

                default:

                    $string = tr('unknown_error_occured');
            }
            return $string;
        }

        public static function get_message($code)
        {
            switch($code) {
                case 101:
                    $string = tr('success');
                    break;
                case 102:
                    $string = tr('password_change_success');
                    break;
                case 103:
                    $string = tr('successfully_logged_in');
                    break;
                case 104:
                    $string = tr('successfully_logged_out');
                    break;
                case 105:
                    $string = tr('successfully_sign_up');
                    break;
                case 106:
                    $string = tr('mail_sent_successfully');
                    break;
                case 107:
                    $string = tr('payment_successful_done');
                    break;
                case 108:
                    $string = tr('favourite_provider_delete');
                    break;
                case 109:
                    $string = tr('payment_mode_changed');
                    break;
                case 110:
                    $string = tr('payment_mode_changed');
                    break;
                case 111:
                    $string = tr('service_accepted');
                    break;
                case 112:
                    $string = tr('provider_started');
                    break;
                case 113:
                    $string = tr('arrived_service_location');
                    break;
                case 114:
                    $string = tr('service_started');
                    break;
                case 115:
                    $string = tr('service_completed');
                    break;
                case 116:
                    $string = tr('user_rating_done');
                    break;
                case 117:
                    $string = tr('request_cancelled_successfully');
                    break;
                case 118:
                    $string = tr('wishlist_added');
                    break;
                case 119:
                    $string = tr('payment_confirmed_successfully');
                    break;
                case 120:
                    $string = tr('history_added');
                    break;
                case 121:
                    $string = tr('history_deleted_successfully');
                    break;

                case 122:
                    $string = tr('autorenewal_enable_success');
                    break;
                case 123:
                    $string = tr('ppv_not_set');
                    break;
                case 124:
                    $string = tr('watch_video_success');
                    break;
                case 125:
                    $string = tr('pay_and_watch_video');
                    break;
                case 126:
                    $string = tr('playlist_added_video');
                    break;
                case 127:
                    $string = tr('playlist_removed_video');
                    break;
                case 128:
                    $string = tr('playlist_added');
                    break;
                case 129:
                    $string = tr('playlist_updated');
                    break;
                case 130:
                    $string = tr('bell_notification_updated');
                    break;
                case 131:
                    $string = tr('playlist_deleted');
                    break;
                case 132:
                    $string = tr('playlist_video_add_empty');
                    break;

                case 50001:
                    $string = tr('referral_code_valid');
                    break;
                case 50002:
                    $string = tr('user_video_tapes_approved');
                    break;
                case 50003:
                    $string = tr('user_video_tapes_declined');
                    break;
                case 50004:
                    $string = tr('user_video_tapes_ppv_added');
                    break;
                case 50005:
                    $string = tr('user_video_tapes_ppv_removed');
                    break;
                case 50006:
                    $string = tr('user_video_tapes_delete_success');
                    break;
                case 50007:
                    $string = tr('add_card_success');
                    break;
                default:
                    $string = "";
            
            }
            
            return $string;
        }

        public static function get_push_message($code) {

            switch ($code) {
                case 601:
                    $string = tr('no_provider_available');
                    break;
                case 602:
                    $string = tr('no_provider_available_take_service');
                    break;
                case 603:
                    $string = tr('request_complted_successfully');
                    break;
                case 604:
                    $string = tr('new_request');
                    break;
                default:
                    $string = "";
            }

            return $string;

        }

        public static function generate_password()
        {
            $new_password = time();
            $new_password .= rand();
            $new_password = sha1($new_password);
            $new_password = substr($new_password,0,8);
            return $new_password;
        }

        public static function upload_picture($picture)
        {
            Helper::delete_picture($picture, "/uploads/");

            $s3_url = "";

            $file_name = Helper::file_name();

            $ext = $picture->getClientOriginalExtension();
            $local_url = $file_name . "." . $ext;

            if(config('filesystems')['disks']['s3']['key'] && config('filesystems')['disks']['s3']['secret']) {

                Storage::disk('s3')->put($local_url, file_get_contents($picture) ,'public');

                $s3_url = Storage::url($local_url);
            } else {
                $ext = $picture->getClientOriginalExtension();
                $picture->move(public_path() . "/uploads", $file_name . "." . $ext);
                $local_url = $file_name . "." . $ext;

                $s3_url = Helper::web_url().'/uploads/'.$local_url;
            }

            return $s3_url;
        }

        public static function normal_upload_picture($picture, $path, $user = null)
        {
            $s3_url = "";

            $file_name = Helper::file_name();

            $ext = $picture->getClientOriginalExtension();

            $local_url = $file_name . "." . $ext;

            if($path == '')
                $path = '/uploads/images/';

            $inputFile = base_path('public'.$path.$local_url);

            // Convert bytes into MB
            $bytes = convertMegaBytes($picture->getClientSize());

            if ($bytes > Setting::get('image_compress_size')) {

                // Compress the video and save in original folder
                $FFmpeg = new \FFmpeg;

                $FFmpeg
                    ->input($picture->getPathname())
                    ->output($inputFile)
                    ->ready();
                // dd($FFmpeg->command);
            } else {

                $picture->move(public_path() . $path, $local_url);

            }


            if ($user) {

                // open an image file
                $img = Image::make(public_path().$path.$local_url);

                // resize image instance
                $img->resize(60, 60);

                // save image in desired format
                $img->save(public_path()."/uploads/user_chat_img/".$local_url);


                $user->chat_picture = Helper::web_url()."/uploads/user_chat_img/".$local_url;
            }
            
            $s3_url = Helper::web_url().$path.$local_url;
            
            return $s3_url;
        }


        public static function subtitle_upload($subtitle)
        {
            $s3_url = "";

            $file_name = Helper::file_name();

            $ext = $subtitle->getClientOriginalExtension();

            $local_url = $file_name . "." . $ext;

            $path = '/uploads/subtitles/';

            $subtitle->move(public_path() . $path, $local_url);

            $s3_url = Helper::web_url().$path.$local_url;

            return $s3_url;
        }


        public static function video_upload($picture, $video_title, $path)
        {
            $s3_url = "";
            $file_name = Helper::file_name();
            if($video_title != '') {
                $file_name = $file_name.'-'.$video_title;
            }
            $ext = $picture->getClientOriginalExtension();

            $local_url = $file_name .".mp4";
            if($path == '')
                $path = '/uploads/videos/';

            // Convert bytes into MB
            $bytes = convertMegaBytes($picture->getClientSize());

            $inputFile = public_path().$path.$local_url;

            if ($bytes > Setting::get('video_compress_size')) {

                // dispatch(new OriginalVideoCompression($picture->getPathname(), $inputFile));

                Log::info("Compress Video : ".'Success');

                // Compress the video and save in original folder
                $FFmpeg = new \FFmpeg;

                $FFmpeg
                    ->input($picture->getPathname())
                    ->vcodec('h264')
                    ->constantRateFactor('28')
                    ->output($inputFile, 'mp4')
                    ->ready();

            } else {
                Log::info("Original Video");

                $picture->move(public_path() . $path, $local_url);
            }

            $s3_url = Helper::web_url().$path.$local_url;        

            Log::info("Compress Video completed");
            
            // File::move(resource_path('views/projects/'.$oldSlug.'.blade.php'),resource_path('views/projects/'.$project->slug.'.blade.php'));

            return ['db_url'=>$s3_url, 'baseUrl'=> $inputFile, 'local_url'=>$local_url, 'file_name'=>$file_name];
        }

        public static function delete_picture($picture, $path) {

            if (file_exists(public_path() . $path . basename($picture))) {
                
                File::delete( public_path() . $path . basename($picture));

            }
            return true;
        }
        public static function delete_picture_r4d($picture, $path) {
            $path = public_path($path);
            if (file_exists($path)) {
                
                File::delete($path);

            }
            return true;
        }
        public static function s3_delete_picture($picture) {
            Log::info($picture);

            Storage::Delete(basename($picture));
            return true;
        }

        public static function file_name() {

            $file_name = time();
            $file_name .= rand();
            $file_name = sha1($file_name);

            return $file_name;
        }

        public static function send_notification($title = "STREMHASH" , $user_details , $push_notification_data ) {

            if(!$user_details || !$push_notification_data) {

                Log::info("send_notification ----- Data missing");
               
                return false;

            }

            if(!$user_details->device_token) {

                Log::info('User device_token empty');

                return false;
            }

            if ($user_details->device_type == 'ios') {

                require_once app_path().'/ios/apns.php';

                $msg = ["alert" => '$message',"status" => "success","title" => '$title',"message" => '$push_message',"badge" => 1,"sound" => "default","status" => "","rid" => ""];

                if (!isset($user_details->device_token) || empty($user_details->device_token)) {
                    
                    $deviceTokens = array();

                } else {

                    $deviceTokens = $user_details->device_token;
                }

                $apns = new \Apns();

                $apns->send_notification($deviceTokens, $msg);

                Log::info("iOS push end");

            } else {

                Log::info("Andriod push Started");

                require_once app_path().'/gcm/GCM_1.php';

                require_once app_path().'/gcm/const.php';

                if (!isset($user_details->device_token) || empty($user_details->device_token)) {

                    $registatoin_ids = "0";

                } else {

                    $registatoin_ids = trim($user_details->device_token);

                }

                $message = array(TEAM => trim($title) , MESSAGE => $push_notification_data);

                $gcm = new \GCM();

                $registatoin_ids = array($registatoin_ids);
                // $registatoin_ids = array('APA91bGQpH74-VqRGxLIjkuSOeJIGdZ9C5w0IPUkKzEulv5jMx5HuRvj2dID_YPxwePk7HBZ4majWpGRQmPzp4ytUdzOEmVRNqVqObBbJu7J-XJ7ir9TeJxDurQS1Zg6t-ooD0cc5pXK' , 'APA91bFz6VxbSURJyaM1pe8GQtLAKCQL3oT1lk0bAjTKeDmVYMiAckn00jadZZbV6vKu8xttXHGGyeTfnLOmE76jykMeiHUHb7aw2KFOPQcXO2eMWqkcUuHNqPa8mj56MZZn6d9jYgUX');
                $gcm->send_notification($registatoin_ids, $message);

                Log::info("Andriod push end");

            }

        }



        /**
         *  Function Name : search_video()
         */
        public static function search_video($request,$key,$web = NULL,$skip = 0) {

            $videos_query = VideoTape::where('video_tapes.is_approved' ,'=', 1)
                        ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                        ->leftJoin('categories' , 'categories.id' , '=' , 'video_tapes.category_id') 
                        ->where('title','like', '%'.$key.'%')
                        ->where('video_tapes.status' , 1)
                        ->where('video_tapes.publish_status' , 1)
                        ->videoResponse()
                        ->where('channels.is_approved', 1)
                        ->where('channels.status', 1)
                        ->where('video_tapes.age_limit','<=', checkAge($request))
                        ->where('categories.status', CATEGORY_APPROVE_STATUS)
                        ->orderBy('video_tapes.created_at' , 'desc');
            if($web) {
                $videos = $videos_query->paginate(16);
            } else {
                $videos = $videos_query->skip($skip)->take(Setting::get('admin_take_count' ,12))->get();
            }

            return $videos;
        }

        public static function wishlists($user_id ) {

            $data = $ids = [];

            if($user_id) {

                $data = Wishlist::where('wishlists.user_id' , $user_id)->orderby('wishlists.created_at', 'desc')->pluck('video_tape_id');

                if(count($data) > 0) {

                    foreach ($data as $key => $value) {

                        $ids[] = $value;

                    }
                }


            }

            return $ids;
        }

        public static function history($user_id ) {

            $data = $ids = [];

            if($user_id) {

                $data = UserHistory::where('user_histories.user_id' , $user_id)->pluck('video_tape_id');

                if(count($data) > 0) {

                    foreach ($data as $key => $value) {

                        $ids[] = $value;

                    }
                }


            }

            return $ids;
        }


        public static function live_video_search($request,$key,$web = NULL,$skip = 0) {

            $videos_query = LiveVideo::where('live_videos.is_streaming' ,DEFAULT_TRUE)
                        ->where('title','like', '%'.$key.'%')
                        ->where('live_videos.status' ,DEFAULT_FALSE)
                        ->orderBy('live_videos.created_at' , 'desc');
            if($web) {
                $videos = $videos_query->paginate(16);
            } else {
                $videos = $videos_query->skip($skip)->take(Setting::get('admin_take_count' ,12))->get();
            }

            return $videos;
        }

        public static function check_wishlist_status($user_id,$video_id) {

            $status = Wishlist::where('wishlists.user_id' , $user_id)
                                        ->where('video_tape_id' , $video_id)
                                        ->first();

            return $status ? $status : 0;
        }


        public static function history_status($video_id,$user_id) {
            if(UserHistory::where('video_tape_id' , $video_id)->where('user_histories.user_id' , $user_id)->count()) {
                return 1;
            } else {
                return 0;
            }
        }


        public static function upload_avatar($folder,$picture) {

            $file_name = Helper::file_name();

            $ext = $picture->getClientOriginalExtension();

            $local_url = $file_name . "." . $ext;

            $ext = $picture->getClientOriginalExtension();

            $picture->move(public_path()."/".$folder, $file_name . "." . $ext);

            $url = Helper::web_url().'/'.$folder."/".$local_url;

            return $url;
        
        }

        public static function delete_avatar($folder,$picture) {
            File::delete( public_path() . "/".$folder."/". basename($picture));
            return true;
        }


        public static function banner_videos() {

            $videos = VideoTape::where('video_tapes.is_approved' , 1)
                            ->where('video_tapes.status' , 1)
                            ->where('video_tapes.is_banner' , 1)
                            ->select(
                                'video_tapes.id as video_tape_id' ,
                                'video_tapes.title','video_tapes.ratings',
                                'video_tapes.banner_image as default_image'
                                )
                            ->orderBy('created_at' , 'desc')
                            ->get();

            return $videos;
        }

         public static function video_ratings($video_id) {

            $ratings = UserRating::where('video_tape_id' , $video_id)
                            ->leftJoin('users' , 'user_ratings.user_id' , '=' , 'users.id')
                            ->select('users.id as user_id' , 'users.name as username',
                                    'users.picture as picture' ,
                                    'user_ratings.rating' , 'user_ratings.comment',
                                    'user_ratings.created_at')
                            ->orderby('created_at', 'desc')
                            ->get();
            if(!$ratings) {
                $ratings = array();
            }

            return $ratings;
        }


        public static function get_user_comments($user_id,$web = NULL) {

            $videos_query = UserRating::where('user_id' , $user_id)
                            ->leftJoin('video_tapes' ,'user_ratings.video_tape_id' , '=' , 'video_tapes.id')
                            ->where('video_tapes.is_approved' , 1)
                            ->where('video_tapes.status' , 1)
                            ->select('video_tapes.id as video_tape_id' ,
                                'video_tapes.title','video_tapes.description' ,
                                'default_image','video_tapes.watch_count',
                                'video_tapes.duration',
                                DB::raw('DATE_FORMAT(video_tapes.publish_time , "%e %b %y") as publish_time'))
                            ->orderby('user_ratings.created_at' , 'desc')
                            ->groupBy('video_tapes.id');

            if($web) {
                $videos = $videos_query->paginate(16);
            } else {
                $videos = $videos_query->skip($skip)->take(Setting::get('admin_take_count' ,12))->get();
            }

            return $videos;

        }


        public static function upload_language_file($folder,$picture,$filename) {

            $ext = $picture->getClientOriginalExtension();
            
            $picture->move(base_path() . "/resources/lang/".$folder ."/", $filename);

        }

        public static function delete_language_files($folder, $boolean, $filename = NULL) {

            if ($boolean) {

                $path = base_path() . "/resources/lang/" .$folder;

                \File::deleteDirectory( $path );

            } else {

                \File::delete( base_path() . "/resources/lang/" . $folder ."/".$filename);
            }
            return true;
        }

        public static function like_status($user_id,$video_id) {

            if(LikeDislikeVideo::where('video_tape_id' , $video_id)->where('user_id' , $user_id)->where('like_status' , DEFAULT_TRUE)->count()) {

                return 1;

            } else {

                return 0;
            }
        }

        public static function wishlist_status($video_id,$user_id) {
            if($wishlist = Wishlist::where('video_tape_id' , $video_id)->where('user_id' , $user_id)->first()) {
                if($wishlist->status)
                    return $wishlist->id;
                else
                    return 0 ;
            } else {
                return 0;
            }
        }

        /**
         * Function name: RTMP Secure video url 
         *
         * @description: used to convert the video to rtmp secure link
         *
         * @created: vidhya R
         * 
         * @edited: 
         *
         * @param string $video_name
         *
         * @param string $video_link
         *
         * @return RTMP SECURE LINK or Normal video link
         */

        public static function convert_rtmp_to_secure($video_name  = "", $video_link = "") {

            if(Setting::get('RTMP_SECURE_VIDEO_URL') != "") {

                // HLS_STREAMING_URL
            
                // validity of the link in seconds (if rtmp and www are on two different machines, it is better to give a higher value, because there may be a time difference.

                $e = date('U')+20; 

                $secret_word = "cgshlockkey"; 

                $user_remote_address = $_SERVER['REMOTE_ADDR']; 

                Log::info("user_remote_address - - - - ".$user_remote_address);

                $user_remote_address = "49.249.233.178"; 

                $md5 = base64_encode(md5($secret_word . $user_remote_address . $e , true)); 

                $md5 = strtr($md5, '+/', '-_'); 

                $md5 = str_replace('=', '', $md5); 

                $rtmp = $video_name."?token=".$md5."&e=".$e; 
                
                $secure_url = Setting::get('RTMP_SECURE_VIDEO_URL').$rtmp;

                return $secure_url; 
            
            } elseif (Setting::get('streaming_url')) {

                $rtmp_video_url = Setting::get('streaming_url').$video_name;

                return $rtmp_video_url;

            } else {

                return $video_link;

            }
            
        }

        /**
         * Function name: RTMP Secure video url 
         *
         * @description: used to convert the video to rtmp secure link
         *
         * @created: vidhya R
         * 
         * @edited: 
         *
         * @param string $video_name
         *
         * @param string $video_link
         *
         * @return RTMP SECURE LINK or Normal video link
         */

        public static function convert_hls_to_secure($video_name  = "", $video_link = "") {

            if(Setting::get('HLS_SECURE_VIDEO_URL') != "") {


                // HLS_STREAMING_URL
            
                // validity of the link in seconds (if rtmp and www are on two different machines, it is better to give a higher value, because there may be a time difference.

                $expires = date('U')+200;

                // secure_link_md5 "$secure_link_expires$uri$remote_addr cgshlockkey";

                $secret_word = "cgshlockkey"; 
 
                $user_remote_address = $_SERVER['REMOTE_ADDR']; 

                Log::info("user_remote_address".$user_remote_address);

                $md5 = md5("$expires/$video_name$user_remote_address $secret_word", true);

                $md5 = base64_encode($md5); 

                $md5 = strtr($md5, '+/', '-_'); 

                $md5 = str_replace('=', '', $md5); 

                $hls = $video_name."?md5=".$md5."&expires=".$expires; 
                
                $secure_url = Setting::get('HLS_SECURE_VIDEO_URL').$hls;

                return $secure_url; 
            
            } elseif (Setting::get('HLS_STREAMING_URL')) {

                $hls_video_url = Setting::get('HLS_STREAMING_URL').$video_name;

                return $hls_video_url;

            } else {

                return $video_link;

            }
            
        }

        /**
         * Function name: RTMP Secure video url 
         *
         * @description: used to convert the video to rtmp secure link
         *
         * @created: vidhya R
         * 
         * @edited: 
         *
         * @param string $video_name
         *
         * @param string $video_link
         *
         * @return RTMP SECURE LINK or Normal video link
         */

        public static function convert_smil_to_secure($smil_file  = "", $smil_link = "") {

            if(Setting::get('VIDEO_SMIL_URL') != "") {
            
                // validity of the link in seconds (if rtmp and www are on two different machines, it is better to give a higher value, because there may be a time difference.

                $expires = date('U')+20;

                // secure_link_md5 "$secure_link_expires$uri$remote_addr cgshlockkey";

                $secret_word = "cgshlockkey"; 
 
                $user_remote_address = $_SERVER['REMOTE_ADDR']; 

                Log::info("user_remote_address".$user_remote_address);

                $md5 = md5("$expires/$smil_file$user_remote_address $secret_word", true);

                $md5 = base64_encode($md5); 

                $md5 = strtr($md5, '+/', '-_'); 

                $md5 = str_replace('=', '', $md5); 

                $smil = $smil_file."?md5=".$md5."&expires=".$expires; 
                
                $secure_url = Setting::get('VIDEO_SMIL_URL').$smil;

                return $secure_url; 
            
            } else {

                return $smil_link;

            }
            
        }

        public static function getTotalVideoSize($user_id) {
            $dirname = public_path('uploads/videos/'.$user_id);
            $total_size = 0;
            if(File::isDirectory($dirname)){
                $temp_folders = File::directories($dirname);
                if(count($temp_folders) > 0 ){
                    $temp_folder_list = File::directories($temp_folders);
                    if($temp_folder_list > 0) 
                    foreach($temp_folder_list as $folder_key=>$t) {
                        $files = File::files($t);
                        if(count($files) > 0) {
                            foreach($files as $key=>$f) {
                                $total_size += filesize($f);
                            }
                        }
                    }
                }
            }
            if($total_size > 0)
                $total_size = number_format($total_size / 1048576,2);

            return $total_size;
        }

    }



