<?php


namespace App\Repositories;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Validator;
use Hash;
use Log;
use DB;
use Exception;
use App\VideoAd;
use App\AdsDetail;
use App\VideoTape;
use App\AssignVideoAd;
use App\Language;
use App\CustomLiveVideo;
use App\Settings;

class AdminRepository {

    /**
     * Function Name : video_ads_save()
     *
     * To save the video ads when edit by the admin
     *
     * @created Vithya R
     *
     * @updated
     *
     * @param Integer $request : Video ad id with video ad details
     *
     * @return response of succes/failure response of details
     */
    public static function video_ads_save($request) {

    	try {

            DB::beginTransaction();

            $model = ($request->has('video_ad_id')) ? VideoAd::find($request->video_ad_id) : new VideoAd();

            $newOne = ($model->id) ? DEFAULT_FALSE : DEFAULT_TRUE;

            $model->video_tape_id = $request->has('video_tape_id') ? $request->video_tape_id : $model->video_tape_id;

            $model->status = DEFAULT_TRUE;

            $ad_types = [];

            if ($model->save()) {

                if(!$request->has('pre_ad_type') && !empty($request->pre_ad_type_id)) {

                    $p_ad = AssignVideoAd::find($request->pre_ad_type_id);

                    if($p_ad) {

                        $p_ad->delete();
                        
                    }

                }

                if($request->has('pre_ad_type')) {

                    if($request->pre_ad_type == PRE_AD) {

                        if(empty($request->pre_ad_time)) {

                            $p_ad = AssignVideoAd::find($request->pre_ad_type_id);

                            if($p_ad) {

                                $p_ad->delete();
                                
                            }

                        } else {

                            $ad_types[] = PRE_AD;

                            $pre_ad_model = ($request->has('pre_ad_type_id')) ? AssignVideoAd::find($request->pre_ad_type_id) : new AssignVideoAd;

                            $pre_ad_model->ad_id = $request->pre_parent_ad_id;

                            $pre_ad_model->video_ad_id = $model->id;

                            $pre_ad_model->ad_type = PRE_AD;

                            $pre_ad_model->video_time = "00:00:00";

                            $pre_ad_model->ad_time = $request->has('pre_ad_time') ? $request->pre_ad_time : $pre_ad_model->pre_ad_time;

                            if ($pre_ad_model->save()) {


                            } else {

                                throw new Exception(tr('something_error'));

                            }
                        }

                    }

                }

                if(!$request->has('post_ad_type') && !empty($request->post_ad_type_id)) {

                    $post_ad = AssignVideoAd::find($request->post_ad_type_id);

                    if($post_ad) {

                        $post_ad->delete();
                        
                    }

                }


                if($request->has('post_ad_type')) {

                    if($request->post_ad_type == POST_AD) {

                        if(empty($request->post_ad_time)) {

                            $post_ad = AssignVideoAd::find($request->post_ad_type_id);

                            if($post_ad) {

                                $post_ad->delete();
                                
                            }

                        } else {

                            $ad_types[] = POST_AD;

                            $post_ad_model = ($request->has('post_ad_type_id')) ? AssignVideoAd::find($request->post_ad_type_id) : new AssignVideoAd;

                            $post_ad_model->ad_type = POST_AD;

                            $post_ad_model->ad_id = $request->post_parent_ad_id;

                            $post_ad_model->video_ad_id = $model->id;

                            $post_ad_model->video_time = $model->getVideoTape ? $model->getVideoTape->duration : '00:00:00';

                            $post_ad_model->ad_time = $request->has('post_ad_time') ? $request->post_ad_time : $post_ad_model->post_ad_time;

                            if ($post_ad_model->save()) {


                            } else {


                                throw new Exception(tr('something_error'));
                                
                            }

                        }

                    }

                }

               
                if(!$request->has('between_ad_type')) {

                    if(count($model->getBetweenAdDetails) > 0) {

                        foreach ($model->getBetweenAdDetails as $key => $value) {
                          
                              if(!in_array($value->id, $request->between_ad_type_id)) {

                                    $value->delete();

                              }

                        }
                    }
                }


                if($request->has('between_ad_type')) {

                    if(!$newOne) {

                        if(count($model->getBetweenAdDetails) > 0) {

                            foreach ($model->getBetweenAdDetails as $key => $value) {
                              
                                  if(!in_array($value->id, $request->between_ad_type_id)) {

                                        $value->delete();

                                  }

                            }
                        }

                    }

                    $b_type = DEFAULT_FALSE;

                    foreach ($request->between_ad_type as $key => $value) {
                   
                        if($value == BETWEEN_AD) {

                            $delete = 0;

                            if($request->has('between_ad_time')) {

                                $b_time = $request->between_ad_time[$key] ? $request->between_ad_time[$key] : '';

                                if(empty($b_time)) {

                                    $delete = 1;

                                    $b_ad = AssignVideoAd::find($request->between_ad_type_id[$key]);

                                    if($b_ad) {

                                        $b_ad->delete();

                                    }

                                }

                            }



                            if($delete == 0) {

                                $id = ($request->has('between_ad_type_id')) ? $request->between_ad_type_id[$key] : '';

                                $between_ad_model = ($id) ? AssignVideoAd::find($id) : new AssignVideoAd;

                                $between_ad_model->ad_type = BETWEEN_AD;

                                $between_ad_model->ad_id = $request->has('between_parent_ad_id') ? $request->between_parent_ad_id[$key] : $between_ad_model->ad_id;

                                $between_ad_model->video_ad_id = $model->id;

                                $time = $request->has('between_ad_video_time') ? $request->between_ad_video_time[$key] : $between_ad_model->video_time;

                                $expTime = explode(':', $time);

                                if (count($expTime) == 3) {

                                    $between_ad_model->video_time = $time;

                                }

                                if (count($expTime) == 2) {

                                     $between_ad_model->video_time = "00:".$expTime[0].":".$expTime[1];
                                }

                                $between_ad_model->ad_time = $request->has('between_ad_time') ? $request->between_ad_time[$key] : $between_ad_model->between_ad_time;

                              
                                if ($between_ad_model->save()) {

                                    $b_type = DEFAULT_TRUE;

                                } else {

                                    throw new Exception(tr('something_error'));                                    
                                }
                            }

                        }
                    }

                    if($b_type) {

                        $ad_types[] = BETWEEN_AD;

                    }

                }

                $model->types_of_ad = ($ad_types) ? implode(',', $ad_types) : $model->types_of_ad;

                if ($model->save()) {


                } else {

                    throw new Exception(tr('something_error'));

                }

            } else {

                throw new Exception(tr('something_error'));

            }

            DB::commit();

            $response_array = ['success' => true,'message' => ($request->video_ad_id) ? tr('ad_update_success') : tr('ad_create_success'), 'data'=>$model];

        } catch(Exception $e) {

            DB::rollBack();

            $response_array = ['success' => false,'message' => $e->getMessage()];

        }

        return response()->json($response_array, 200);
    }


/*    public static function ad_index() {

        $model = VideoAd::with('getVideoTape')->get();

        return response()->json($model, 200);

    }*/

    /**
     * Function Name : ads_details_index()
     *
     * To List out all the ads which is created by admin
     *
     * @created Vithya R
     *
     * @updated
     *
     * @param -
     *
     * @return response of Ad Details array of objects
     */
    public static function ads_details_index() {

        $model = AdsDetail::orderBy('created_at', 'desc')->get();

        return response()->json($model, 200);

    }


    /**
     * Function Name : video_ads_view()
     *
     * To get ads with video (Single video based on id)
     *
     * @created Vithya R
     *
     * @updated
     *
     * @param Integer $request->id : Video id
     *
     * @return response of Ad Details Object with video details
     */
    public static function video_ads_view($request) {

        $model = VideoAd::with('getVideoTape')->find($request->id);
       
        $model = $model ? $model : '';

        return response()->json($model);

    }

    /**
     * Function Name : ads_details_save()
     *
     * To save the ad for new & old object details
     *
     * @created Vithya R
     *
     * @updatedAd Details
     *
     * @param - 
     *
     * @return response of Ad Details Object
     */
    public static function ads_details_save($request) {

        try {

            DB::beginTransaction();

             $validator = Validator::make( $request->all(),array(
                    'ads_detail_id' => 'exists:ads_details,id' ,
                    'name' => 'required',
                    'ad_time' => 'required|integer',
                    'file' => 'mimes:jpeg,jpg,png,mp4,avi,wmv',
                    'ad_url'=>'required|url|max:255'
                )
            );
            
            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());
                
                throw new Exception($error);

            } else {
                
                $ads_detail_details = ($request->has('ads_detail_id')) ? AdsDetail::find($request->ads_detail_id) : new AdsDetail();

                $ads_detail_details->status = DEFAULT_TRUE;

                $ads_detail_details->name = $request->has('name') ? $request->name : $ads_detail_details->name;

                $ads_detail_details->ad_time = $request->has('ad_time') ? $request->ad_time : $ads_detail_details->ad_time;

                $ads_detail_details->ad_url = $request->has('ad_url') ? $request->ad_url : $ads_detail_details->ad_url;

                $ads_detail_details->type = $request->has('type') ? 1 : 0;

                if ($request->file) {

                    if($request->has('id')) {

                        Helper::delete_picture($ads_detail_details->file, "/uploads/ad/");
                    }

                    $ads_detail_details->file = Helper::normal_upload_picture($request->file, "/uploads/ad/");
                }

                if ($ads_detail_details->save()) {
                    
                    DB::commit();

                } else {

                    throw new Exception(tr('something_error'));
                }
            }

            $response_array = ['success' => true,'message' => ($request->id) ? tr('ad_update_success') : tr('ad_create_success'), 'data'=>$ads_detail_details];

        } catch(Exception $e) {

            DB::rollBack();

            $response_array = ['success' => false,'message' => $e->getMessage()];
        }

        return response()->json($response_array, 200);
    }


    public static function languages_save($request) {

        try {
            
            DB::beginTransaction();

            $validator = Validator::make($request->all(),[
                'folder_name' => 'required|max:64',
                'language'=> 'required|max:64',
                'auth_file'=> !($request->language_id) ? 'required' : '',
                'messages_file'=>!($request->language_id) ? 'required' : '',
                'pagination_file'=>!($request->language_id) ? 'required' : '',
                'passwords_file'=>!($request->language_id) ? 'required' : '',
                'validation_file'=>!($request->language_id) ? 'required' : '',
            ]);
            
            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);
            } 

            $language_details = ($request->language_id != '') ? Language::find($request->language_id) : new Language;

            $lang = ($request->language_id != '') ? $language_details->folder_name : '';

            $language_details->folder_name = $request->folder_name;

            $language_details->language = $request->language;

            $language_details->status = APPROVED;
            
            if ($request->hasFile('auth_file')) {

                // Read File Length

                $originallength = readFileLength(base_path().'/resources/lang/en/auth.php');

                $length = readFileLength($_FILES['auth_file']['tmp_name']);

                if ($originallength != $length) {

                    throw new Exception(Helper::get_error_message(162), 162);
                }

                if ($language_details->id != '') {

                    $boolean = ($lang != $request->folder_name) ? DEFAULT_TRUE : DEFAULT_FALSE;

                    Helper::delete_language_files($lang, $boolean, 'auth.php');
                }

                Helper::upload_language_file($language_details->folder_name, $request->auth_file, 'auth.php');

            }

            if ($request->hasFile('messages_file')) {

                 // Read File Length

                $originallength = readFileLength(base_path().'/resources/lang/en/messages.php');

                $length = readFileLength($_FILES['messages_file']['tmp_name']);

                if ($originallength != $length) {

                    throw new Exception(Helper::get_error_message(162), 162);
                }

                if ($language_details->id != '') {

                    $boolean = ($lang != $request->folder_name) ? DEFAULT_TRUE : DEFAULT_FALSE;

                    Helper::delete_language_files($lang, $boolean, 'messages.php');
                }

                Helper::upload_language_file($language_details->folder_name, $request->messages_file, 'messages.php');

            }

            if ($request->hasFile('pagination_file')) {

                // Read File Length

                $originallength = readFileLength(base_path().'/resources/lang/en/pagination.php');

                $length = readFileLength($_FILES['pagination_file']['tmp_name']);

                if ($originallength != $length) {

                    throw new Exception(Helper::get_error_message(162), 162);
                }

                if ($language_details->id != '') {

                    $boolean = ($lang != $request->folder_name) ? DEFAULT_TRUE : DEFAULT_FALSE;

                    Helper::delete_language_files($lang, $boolean, 'pagination.php');
                }

                Helper::upload_language_file($language_details->folder_name, $request->pagination_file, 'pagination.php');
            }

            if ($request->hasFile('passwords_file')) {

                 // Read File Length

                $originallength = readFileLength(base_path().'/resources/lang/en/passwords.php');

                $length = readFileLength($_FILES['passwords_file']['tmp_name']);

                if ($originallength != $length) {
                    
                    throw new Exception(Helper::get_error_message(162), 162);

                }

                if ($language_details->id != '') {

                    $boolean = ($lang != $request->folder_name) ? DEFAULT_TRUE : DEFAULT_FALSE;

                    Helper::delete_language_files($lang, $boolean , 'passwords.php');
                }

                Helper::upload_language_file($language_details->folder_name, $request->passwords_file, 'passwords.php');
            }

            if($request->hasFile('validation_file')) {

                // Read File Length

                $originallength = readFileLength(base_path().'/resources/lang/en/validation.php');

                $length = readFileLength($_FILES['validation_file']['tmp_name']);

                if ($originallength != $length) {
                    
                    throw new Exception(Helper::get_error_message(162), 162);
                }
                
                if ($language_details->id != '') {
                    $boolean = ($lang != $request->folder_name) ? DEFAULT_TRUE : DEFAULT_FALSE;

                    Helper::delete_language_files($lang, $boolean, 'validation.php');
                }

                Helper::upload_language_file($language_details->folder_name, $request->validation_file, 'validation.php');
            } 

            $language_details->save();

            if ($request->language_id && $language_details->save() ) {

                if($lang != $request->folder_name)  {
                   
                    $current_path = base_path('resources/lang/'.$lang);
                   
                    $new_path = base_path('resources/lang/'.$request->folder_name);
                   
                    rename($current_path,$new_path);
                }

                // if currently language file is being changed set in config to
                $setting_details = Settings::where('key','default_lang')->first();

                if (!$setting_details) { 

                    throw new Exception( tr('something_error'), 101 );           
                }
                
                $setting_details->value = $request->language_file;

                if( $setting_details->save()) {
                
                    if($lang == $setting_details->value) {

                        $setting_details->value = $request->folder_name;

                        $fp = fopen(base_path() .'/config/new_config.php' , 'w');

                        fwrite($fp, "<?php return array( 'locale' => '".$request->language_file."', 'fallback_locale' => '".$request->language_file."');?>");
                        
                        fclose($fp);
                                        
                        \Log::info("Key : ".config('app.locale'));

                    }

                }

            }

            if($language_details) {
                
                DB::commit();

                $response_array = ['success' => true, 'message'=> $request->language_id != '' ? tr('language_update_success') : tr('language_create_success')];
           
            } else {
                
                throw new Exception(tr('something_error'), 101);
            }
            
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();
            
            $code = $e->getCode();
            
            $response_array = ['success' => false , 'error' => $error, 'code' => $code];

        }

        return $response_array;
    }

    /**
     * Function : custom_live_videos_save()
     *
     * @created Vithya R
     *
     * @updated -
     *
     * @return Save the form data of the live video
     */
    public static function save_custom_live_video($request) {

        if ($request->custom_live_video_id) {

            $validator = Validator::make($request->all(),[
                    'title' => 'required|max:255',
                    'description' => 'required',
                    'rtmp_video_url'=>'required|max:255',
                    'hls_video_url'=>'required|max:255',
                    'image' => 'mimes:jpeg,jpg,png'
                ]
            );

         } else {

             $validator = Validator::make($request->all(),[
                    'title' => 'max:255|required',
                    'description' => 'required',
                    'rtmp_video_url'=>'required|max:255',
                    'hls_video_url'=>'required|max:255',
                    'image' => 'required|mimes:jpeg,jpg,png'
                ]
            );

         }
        
        if($validator->fails()) {

            $error_messages = implode(',', $validator->messages()->all());

            $response_array = ['success'=>false, 'message'=>$error_messages];

        } else {
            
            $model = ($request->custom_live_video_id) ? CustomLiveVideo::find($request->custom_live_video_id) : new CustomLiveVideo;
            
            $model->title = $request->has('title') ? $request->title : $model->title;

            $model->description = $request->has('description') ? $request->description : $model->description;

            $model->rtmp_video_url = $request->has('rtmp_video_url') ? $request->rtmp_video_url : $model->rtmp_video_url;

            $model->hls_video_url = $request->has('hls_video_url') ? $request->hls_video_url : $model->hls_video_url;

            if($request->hasFile('image')) {

                if($request->custom_live_video_id) {

                    Helper::delete_picture($model->image, "/uploads/images/");
                }

                $model->image = Helper::normal_upload_picture($request->image , "/uploads/images/");
            }
                
            $model->status = DEFAULT_TRUE;

            if ($model->save()) {

                $response_array = ['success'=>true, 'message'=> ($request->custom_live_video_id) ? tr('live_custom_video_update_success') : tr('live_custom_video_create_success'), 'data' => $model];

            } else {

                $response_array = ['success'=>false, 'message'=>tr('something_error')];

            }
            
        }

        return response()->json($response_array);
    }

}