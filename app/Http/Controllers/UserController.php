<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositories\VideoTapeRepository as VideoRepo;

use App\Jobs\BellNotificationJob;

use App\Http\Requests;

use App\Helpers\Helper;

use App\Settings;

use App\User;

use App\Wishlist;

use App\Page;

use App\Flag;

use App\Admin;

use Auth;

use DB;

use Validator;

use View;

use Setting;

use Exception;

use App\ChatMessage;

use Log;

use App\PayPerView;

use App\Card;

use App\BannerAd;

use App\Subscription;

use App\Channel;

use App\VideoTape;

use App\VideoTapeImage;

use App\Repositories\CommonRepository as CommonRepo;

use App\ChannelSubscription;

use App\UserPayment;

use App\Category;

use App\VideoTapeTag;

use App\Tag;

use App\LiveVideo;

use App\Viewer;

use App\LiveVideoPayment;

use App\Playlist;

use App\PlaylistVideo;

use App\Referral;

use App\UserReferrer;

use App\Redeem;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class UserController extends Controller {

    protected $UserAPI;

    protected $Paypal;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserApiController $API, NewUserApiController $NewAPI)
    { 
        $this->UserAPI = $API;
        
        $this->NewUserAPI = $NewAPI;

        $this->middleware(['auth'], ['except' => [
                'master_login',
                'index',
                'single_video',
                'contact',
                'trending', 
                'channels', 
                'add_history', 
                'page_view', 
                'channel_list', 
                'watch_count', 
                'partialVideos', 
                'payment_mgmt_videos', 
                'forgot_password' ,
                'channel_videos',
                'categories_view',
                'categories_videos',
                'categories_channels',
                'custom_live_videos',
                'single_custom_live_video',
                'tags_videos',
                'all_categories',
                'category_videos',
                'sub_category_videos',
                'android_web_page',
                'live_videos',
                'broadcasting',
                'referrals_signup',
                'channel_view',
                'video_view',
                'playlists_view',
                'custom_live_videos_view',
                'directory_delete','directory_files','directory_create','createFolder','upload_video_image',
                'move_files','video_save','r4d_video_delete','r4d_one_video_delete','delete_r4d_files'
        ]]);

        $this->middleware(['verifyUser'], ['except' => [
            'forgot_password'
        ]]);

    }


    public function deleteStreaming() {

        $model = LiveVideo::where('user_id',Auth::user()->id)->where('status', 0)->get();

        if (count($model) > 0) {

            Log::info("Logged In user id".Auth::user()->id);

             // Log::info("Model".print_r($model, true));

            foreach ($model as $key => $value) {

                Log::info("Usr Id".print_r($value->user_id,true));

                    
                if ($value->is_streaming) { 

                    Log::info("deleteStreaming");

                    // $value->status = DEFAULT_TRUE;

                    $value->save();

                } else {

                    $value->delete();

                }

            }

        }


    }


    public function broadcast(Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
        ]);        

        $response = $this->UserAPI->broadcast($request)->getData();


        if ($response->success) {

           return redirect(route('user.live_video.start_broadcasting', array('id'=>$response->data->unique_id,'c_id'=>$response->data->channel_id)))->with('flash_success', tr('video_going_to_broadcast'));

            // return redirect(route('user.android.video', array('u_id'=>$response->data->unique_id,'c_id'=>$response->data->channel_id, 'id'=>\Auth::user()->id)));
           
        } else {

            return back()->with('flash_error', $response->error_messages);

        }

    }


    public function broadcasting(Request $request) {

        if ($request->id) {

            $model = LiveVideo::where('unique_id', $request->id)
                        ->where('status', '!=', DEFAULT_TRUE)
                       // ->where('user_id', Auth::user()->id)
                        ->first();
    
            if ($model) {

                // $delete_videos = LiveVideo::

                $videoPayment = null;


                if (Auth::check()) {

                    // if(!count($subscription)) {

                    //     return redirect(route('user.dashboard'))->with('flash_error', tr('no_subscription_found'));
                    // }

                    $userModel = User::find(Auth::user()->id);

                    if ($model->user_id != $userModel->id) {

                        // Load Viewers model

                        $viewer = Viewer::where('video_id', $model->id)->where('user_id',Auth::user()->id)->first();

                        $new_user = 0;

                        if(!$viewer) {

                            $new_user = 1;

                            $viewer = new Viewer;

                            $viewer->video_id = $model->id;

                            $viewer->user_id = Auth::user()->id;
                        }

                        $viewer->count = ($viewer->count) ? $viewer->count + 1 : 1;

                        $viewer->save();

                        if ($new_user) {

                            if ($model) {

                                Log::info("test");

                                $model->viewer_cnt += 1;

                                $model->save();
                                
                            }

                        }

                        // video payment 
                        // $videoPayment = LiveVideoPayment::where('live_video_id', $model->id)
                        // ->where('live_video_viewer_id', Auth::user()->id)
                        // ->where('status',DEFAULT_TRUE)->first();
                    
                        $videoPayment = LiveVideoPayment::where('live_video_id', $model->id)
                            ->where('live_video_viewer_id', Auth::user()->id)
                            ->where('status',DEFAULT_TRUE)->first();
                        
                    }

                    $appSettings = json_encode([
                        'SOCKET_URL' => Setting::get('SOCKET_URL'),
                        'CHAT_ROOM_ID' => isset($model) ? $model->id : null,
                        'BASE_URL' => Setting::get('BASE_URL'),
                        'TURN_CONFIG' => [],
                        'TOKEN' =>  ($model->user_id == $userModel->id) ? Auth::user()->token : null,
                        'USER_PICTURE'=>$userModel->chat_picture,
                        'NAME'=>$userModel->name,
                        'CLASS'=>'left',
                        'USER' => ($model->user_id == $userModel->id) ? ['id' => $userModel->id, 'role' => "model"] : null,
                        'VIDEO_PAYMENT'=>($videoPayment) ? $videoPayment : null,
                    ]);

                    $comments = ChatMessage::where('live_video_id', $model->id)->get();

                } else {

                    $model->viewer_cnt += 1;

                    $model->save();

                    $appSettings = json_encode([
                        'SOCKET_URL' => Setting::get('SOCKET_URL'),
                        'CHAT_ROOM_ID' => isset($model) ? $model->id : null,
                        'BASE_URL' => Setting::get('BASE_URL'),
                        'TURN_CONFIG' => [],
                        'TOKEN' =>  null,
                        'USER_PICTURE'=>$model->user->chat_picture,
                        'NAME'=>$model->user->name,
                        'CLASS'=>'left',
                        'USER' => null,
                        'VIDEO_PAYMENT'=>($videoPayment) ? $videoPayment : null,
                    ]);

                    $comments = null;

                }

                $query = LiveVideo::where('is_streaming', DEFAULT_TRUE)
                    ->where('status', 0)->whereNotIn('id', [$model->id]);

                if (Auth::check()) {

                    $query->whereNotIn('user_id', [Auth::user()->id]);

                }

                $videos = $query->paginate(15);


                $is_streamer = $model->user_id == $request->id ? DEFAULT_TRUE : DEFAULT_FALSE;

                if (!$is_streamer) {

                    $video_url = "";

                    if ($model->unique_id == 'sample') {

                        $video_url = $model->video_url;

                    } else {

                        if ($model->video_url) {

                            if ($request->device_type == DEVICE_IOS) {

                                $video_url = CommonRepo::iosUrl($model);

                            } else if($model->browser_name == DEVICE_IOS){

                               $video_url = CommonRepo::rtmpUrl($model);

                            }

                            if (($request->browser == IOS_BROWSER || $request->browser == WEB_SAFARI) && ($model->browser_name == DEVICE_IOS)) {

                                $video_url = CommonRepo::iosUrl($model);

                            }

                        } else {

                            $video_url = "";

                        }

                    }

                } else {

                    $video_url = "";
                }

                $model->video_url = $video_url;

                return view('user.videos.live-video')->with('page', 'live-video')
                    ->with('subPage', 'broadcast')
                    ->with('data', $model)->with('appSettings', $appSettings)->with('comments',$comments)->with('videos', $videos);

            } else {

                return redirect(route('user.channel', ['id'=>$request->c_id]))->with('flash_error', tr('no_live_video_found'));
            }

        } else {

            if ($request->c_id) {

                return redirect(route('user.channel', ['id'=>$request->c_id]))->with('flash_error', tr('id_not_matching'));

            } else {

                return redirect(route('user.dashboard'))->with('flash_error', tr('something_error'));

            }


        }

    }


    public function stop_streaming(Request $request) {

        $model = LiveVideo::find($request->id);

        $model->status = DEFAULT_TRUE;

        if(Auth::check()) {

            if ($model->user_id == Auth::user()->id) {

                $model->end_time = getUserTime(date('H:i:s'), ($model->user) ? $model->user->timezone : '', "H:i:s");

                $model->no_of_minutes = getMinutesBetweenTime($model->start_time, $model->end_time);

                $message =  tr('streaming_stopped_success');


                if (Setting::get('wowza_server_url')) {

                    $this->UserAPI->disConnectStream($model->user_id.'-'.$model->id);

                }

                

                $route = route('user.channel', ['id'=>$model->channel_id]);

            } else {

                $message = tr('no_more_video_available');

                $route = route('user.live_videos');
            }

        } else {

            $message = tr('no_more_video_available');

            $route = route('user.live_videos');

        }

        if ($model->save()) {

            

        }

        return redirect($route)->with('flash_success',$message);
    }


    public function live_videos(Request $request) {

        $query = LiveVideo::where('is_streaming', DEFAULT_TRUE)
                    ->where('status', DEFAULT_FALSE)
                    ->orderBy('created_at' , 'desc');

        if (Auth::check()) {

            $query->whereNotIn('user_id', [Auth::user()->id]);

        }

        $videos = $query->paginate(15);
 
       return view('user.videos.live_videos_list')
                ->with('videos', $videos)
                ->with('page', 'live_videos')
                ->with('subPage', 'live_videos');

    }

    public function setCaptureImage(Request $req, $roomId) {
        //TODO - allow model of this room only

        $data = explode(',', $req->get('base64'));

        if ($data[1] != '') {
            file_put_contents(join(DIRECTORY_SEPARATOR, [public_path(), 'uploads', 'rooms', $roomId . '.png']), base64_decode($data[1]));
            $model = LiveVideo::find($roomId);
            $model->snapshot = Helper::web_url()."/uploads/rooms/".$roomId . '.png';
            $model->save();

            if ($model->save()) {
                return response()->json(true,200);
            } else {
                return response()->json(false,200);
            }
        }
         
    }


    public function get_viewer_cnt(Request $request) {

        $model = LiveVideo::find($request->id);

        if ($model) {

            $viewer_cnt = $model->viewer_cnt;

        } else {

            $viewer_cnt = 0;

        }

        return response()->json(['viewer_cnt'=>$viewer_cnt, 'model'=>$model]);

    }


    public function payment_url(Request $request) {

        $id = $request->id;

        $user_id = $request->user_id;

        if (!Auth::check() || !$user_id) {

            return redirect(route('user.login.form'));

        } else {

            $video_payment = LiveVideoPayment::where('live_video_viewer_id' , $user_id)->where('live_video_id' , $id)->where('status', DEFAULT_TRUE)->first();

            if ($video_payment) {

                return redirect(route('user.live_video.start_broadcasting', array('id'=>$video_payment->getVideo->unique_id, 'c_id'=>$video_payment->getVideo->channel_id)));


            }

            return redirect(route('user.live-video.invoice', array('id'=>$id)));

           /* if (Setting::get('payment_type') == 'stripe') {

                return redirect(route('user.stripe_payment_video', array('id'=>$id, 'user_id'=>$user_id)));

            } else {

                return redirect(route('user.live_video_paypal', array('id'=>$id, 'user_id'=>$user_id)));
            }*/
        }

    }

    public function live_videos_payment_url(Request $request) {

        $id = $request->id;

        $user_id = Auth::check() ? Auth::user()->id : '';

        if (!Auth::check() || !$user_id) {

            return redirect(route('user.login.form'));

        } else {

            $video_payment = LiveVideoPayment::where('live_video_viewer_id' , $user_id)->where('live_video_id' , $id)->where('status', DEFAULT_TRUE)->first();

            if ($video_payment) {

                return redirect(route('user.live_video.start_broadcasting', array('id'=>$video_payment->getVideo->unique_id, 'c_id'=>$video_payment->getVideo->channel_id)));


            }

            $coupon_code = $request->coupon_code ?  $request->coupon_code : '';
            //return redirect(route('user.live-video.invoice', array('id'=>$id)));

            if ($request->payment_type == 2) {

                return redirect(route('user.stripe_payment_video', array('id'=>$id, 'user_id'=>$user_id, 'coupon_code'=>$coupon_code)));

            } else {

                return redirect(route('user.live_video_paypal', array('id'=>$id, 'user_id'=>$user_id, 'coupon_code'=>$coupon_code)));
            }
        }

    }


    public function stripe_payment_video(Request $request) {


        $request->request->add([

            'video_id'=>$request->id,

            'id'=>Auth::check() ? Auth::user()->id : '',

        ]);

        $response = $this->UserAPI->stripe_live_ppv($request)->getData();

        if ($response->success) {


            $video_payment = LiveVideoPayment::where('live_video_viewer_id' , $request->id)->where('live_video_id' , $request->video_id)->where('status', DEFAULT_TRUE)->first();

            return redirect(route('user.live_video.start_broadcasting', array('id'=>$video_payment->getVideo->unique_id, 'c_id'=>$video_payment->getVideo->channel_id)));

            //return redirect(route(''));

        } else {

            return back()->with('flash_error', $response->error_messages);
        }

        if (\Auth::user()->card_id) {

            $user_card = Card::find(Auth::user()->card_id);

            if ($user_card && $user_card->is_default) {

                $video = LiveVideo::find($request->id);

                if($video && !$video->status && $video->is_streaming) {

                    $total = $video->amount;

                    // Get the key from settings table
                    $stripe_secret_key = Setting::get('stripe_secret_key');

                    $customer_id = $user_card->customer_id;
                    
                    if($stripe_secret_key) {

                        \Stripe\Stripe::setApiKey($stripe_secret_key);
                    } else {

                        // $response_array = array('success' => false, 'error' => Helper::error_message(902) , 'error_code' => 902);

                       // return response()->json($response_array , 200);

                        return back()->with('flash_error', Helper::get_error_message(902));
                    }

                    try {

                       $user_charge =  \Stripe\Charge::create(array(
                          "amount" => $total * 100,
                          "currency" => "usd",
                          "customer" => $customer_id,
                        ));

                       $payment_id = $user_charge->id;
                       $amount = $user_charge->amount/100;
                       $paid_status = $user_charge->paid;

                       if($paid_status) {
                            $user_payment = new LiveVideoPayment;
                            $user_payment->payment_id  = $payment_id;
                            $user_payment->live_video_viewer_id = Auth::user()->id;
                            $user_payment->user_id = $video->user_id;
                            $user_payment->live_video_id = $video->id;
                            $user_payment->status = 1;
                            $user_payment->amount = $amount;
                            // $user_payment->save();

                            // Commission Spilit 

                            $admin_commission = Setting::get('admin_commission');

                            $admin_commission = $admin_commission ? $admin_commission/100 : 0;

                            $admin_amount = $amount * $admin_commission;

                            $user_amount = $amount - $admin_amount;

                            $user_payment->admin_amount = $admin_amount;

                            $user_payment->user_amount = $user_amount;

                            $user_payment->save();

                            // Commission Spilit Completed

                            if($user = User::find($user_payment->user_id)) {

                                $user->total_admin_amount = $user->total_admin_amount + $admin_amount;

                                $user->total_user_amount = $user->total_user_amount + $user_amount;

                                $user->remaining_amount = $user->remaining_amount + $user_amount;

                                $user->total_amount = $user->total_amount + $total;

                                $user->save();

                                add_to_redeem($user->id, $user_amount);
                            
                            }



                            return redirect(route('user.live_video.start_broadcasting',array('id'=>$video->unique_id, 'c_id'=>$video->channel_id)));

                        } else {

                            return back()->with('flash_error', Helper::get_error_message(903));

                        }
                    
                    } catch (\Stripe\StripeInvalidRequestError $e) {

                        Log::info(print_r($e,true));

                        /*$response_array = array('success' => false , 'error' => Helper::get_error_message(903) ,'error_code' => 903);*/

                        return back()->with('flash_error', Helper::get_error_message(903));

                       // return response()->json($response_array , 200);
                    
                    }

                
                } else {

                    return back()->with('flash_error', tr('no_live_video_found'));
                    
                }


            } else {

                return back()->with('flash_error', tr('no_default_card_available'));

            }

        } else {

            return back()->with('flash_error', tr('no_default_card_available'));

        }

    }

    public function delete_video($id, $user_id) {

        // Load Model
        $model = LiveVideo::find($id);

        if ($model) {

            if ($model->user_id == $user_id) {

                if ($model->is_streaming) {

                    $model->status = DEFAULT_TRUE;

                    $model->end_time = getUserTime(date('H:i:s'), ($model->user) ? $model->user->timezone : '', "H:i:s");

                    // $model->no_of_

                    if ($model->save()) {

                       
                    } else {

                        $response_array = ['success'=>false, 'error_messages'=>tr('went_wrong')];

                    }

                    $response_array = ['success'=>true];
                }


            } else {

                $response_array = ['success'=>false, 'error_messages'=> tr('not_authorized_person')];

            }
            
        } else {

            $response_array = ['success'=>false, 'error_messages'=> tr('no_live_video_present')];

        }

        return response()->json($response_array);

    }

    public function live_history(Request $request) {

        $request->request->add([ 
            'id'=>Auth::user()->id,
            'token'=>Auth::user()->token,
            'device_type'=>DEVICE_WEB,
        ]); 

        $response = $this->UserAPI->live_history($request)->getData();

        if ($response->success) {

            return view('user.history.live_history')->with('page', 'history')
                ->with('subPage', 'live_history')
                ->with('response', $response);


        } else {

            return back()->with('flash_error', $response->error_messages);
        }

    }

    public function live_mgmt_videos(Request $request) {

        // Get Videos

        // $videos = VideoRepo::channel_videos($request->channel_id, null, $request->skip);

       // $payment_videos = VideoRepo::payment_videos($request->channel_id, null, $request->skip);

        $live_video_history = $this->UserAPI->live_video_revenue($request)->getData();


        $view = View::make('user.videos.partial_live_video_history')
                    ->with('live_video_history', $live_video_history)->render();

        return response()->json(['view'=>$view, 'length'=>count($live_video_history->data)]);
    }

    public function android_web_page(Request $request) {

        if ($request->u_id) {

            $model = LiveVideo::where('unique_id', $request->u_id)
                        ->where('status', '!=', DEFAULT_TRUE)
                       // ->where('user_id', Auth::user()->id)
                        ->first();
    

            if ($model) {

                $model->video_url = "";

                Auth::loginUsingId($request->id);

                // $delete_videos = LiveVideo::

                $videoPayment = null;

                if (Auth::check()) {

                    // $usrModel

                    /*$userModel = User::find(Auth::user()->id);

                    if ($model->user_id != $userModel->id) {

                            // Load Viewers model

                            $viewer = Viewer::where('video_id', $model->id)->where('user_id', Auth::user()->id)->first();

                            if(!$viewer) {

                                $viewer = new Viewer;

                                $viewer->video_id = $model->id;

                                $viewer->user_id = Auth::user()->id;

                            }

                            $viewer->count = ($viewer->count) ? $viewer->count + 1 : 1;

                            $viewer->save();

                            if ($viewer) {

                                $model->viewer_cnt += 1;

                                $model->save();

                            }
                            // video payment 

                            $videoPayment = LiveVideoPayment::where('live_video_id', $model->id)
                                ->where('live_video_viewer_id', Auth::user()->id)
                                ->where('status',DEFAULT_TRUE)->first();
                            

                    }*/


                } else {

                    $model->viewer_cnt += 1;

                    $model->save();
                }


            } else {

                Log::info(tr('no_live_video_found'));

            }

        } else {

            if ($request->c_id) {

                Log::info(tr('id_not_matching'));

            } else {

                Log::info(tr('something_error'));

            }


        }


        return view('user.android.android-video')->with('data', $model)->with('page', '')->with('sub_page','');
    }

    /**
     * Function Name : master_login()
     *
     * @uses To Activate Super user by admin
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param Object $request - User Details
     *
     * @return with Success/Failure Message
     */
    public function master_login(Request $request) {

        try {

            DB::beginTransaction();

            if (!Auth::guard('admin')->check()) {
                
                throw new Exception(tr('admin_not_logged_in'));

            }

            // Get current login admin details
            $master_user_id = Auth::guard('admin')->user()->user_id;

            $master_user_details = User::find($master_user_id);
            
            // Check the admin has logged in

            if(!$master_user_details) {

                // Check already record exists
                $check_admin_user_details = User::where('email' , Auth::guard('admin')->user()->email)->first();
               
                if($check_admin_user_details) {

                    $check_admin_user_details->is_master_user = 1;

                    if ($check_admin_user_details->save()) {


                    } else {

                        throw new Exception(tr('user_details_not_saved'));
                        
                    }

                } else {

                    $check_admin_user_details = new User;

                    $check_admin_user_details->name = "Master User";

                    $check_admin_user_details->email = Auth::guard('admin')->user()->email;

                    $check_admin_user_details->password = \Hash::make("123456");

                    $check_admin_user_details->user_type = $check_admin_user_details->is_master_user = $check_admin_user_details->is_verified = $check_admin_user_details->status = 1;

                    $check_admin_user_details->device_type = WEB;

                    if ($check_admin_user_details->save()) {

                            $admin = Admin::where('email',  Auth::guard('admin')->user()->email)->first();

                            if ($admin) {

                                $admin->user_id = $check_admin_user_details->id;

                                $admin->save();
                            }   

                    } else {

                        throw new Exception(tr('user_details_not_saved'));
                    }

                }

                $master_user_id = $check_admin_user_details->id;

            }

            $master_user_details = User::find($master_user_id);
            // If master user details is not empty -> Login the admin as user

            if(!$master_user_details) {
                
                throw new Exception(tr('user_not_found'));

            }
            
            $master_user_details->token = Helper::generate_token();

            $master_user_details->token_expiry = Helper::generate_token_expiry();

            $master_user_details->save();
            
            Auth::loginUsingId($master_user_id, true);

            DB::commit();

            return redirect()->to('/')->with('flash_success', tr('master_login_success'));

        } catch(Exception $e) {

            DB::rollback();

            $e = $e->getMessage();

            return back()->with('flash_error', $e);

        }

    }

    /**
     * Function Name : index()
     *
     * @uses Show the user dashboard.
     * 
     * @created Vithya R
     *
     * @updated 
     * 
     * @param Object $request - User Details
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        Log::info("Timezone ".print_r(date('Y-m-d H:i:s'), true));

        Log::info("Convert Timezone ".print_r(convertTimeToUSERzone(date('Y-m-d H:i:s'), 'Europe/London', 'Y-m-d H:i:s'), true));


        $database = config('database.connections.mysql.database');
        
        $username = config('database.connections.mysql.username');

        if($database && $username && Setting::get('installation_process') == 2) {

            counter('home');

            $watch_lists = $wishlists = array();

            if (Auth::check()) {
                
                $request->request->add([ 
                    'id'=>\Auth::user()->id,
                    'age' => \Auth::user()->age_limit,
                    'browser' => 'chrome',
                    'device_type' => DEVICE_WEB
                ]);   
            }

            $request->request->add([
                'browser' => 'chrome',
                'device_type' => DEVICE_WEB
            ]);

            if($request->has('id')){

                $wishlists = $this->UserAPI->wishlist_list($request)->getData();

                $watch_lists = $this->UserAPI->watch_list($request)->getData();  
            }   
            $live_videos_response = $this->UserAPI->live_videos($request)->getData();

            $live_videos = [];

            if($live_videos_response->success == true) {

                $live_videos = $live_videos_response->data;

            }
            $random_video = LiveVideo::where('status', '=', DEFAULT_TRUE)->inRandomOrder(1)->first();

            $recent_videos = $this->UserAPI->recently_added($request)->getData();

            $trendings = $this->UserAPI->trending_list($request)->getData();
            
            $suggestions  = $this->UserAPI->suggestion_videos($request)->getData();

            $channels = getChannels(WEB);

            $banner_videos = [];

            if (Setting::get('is_banner_video')) {

                $banner_videos = VideoTape::select('id as video_tape_id', 'banner_image as image', 'title as video_title', 'description as content')
                                ->where('video_tapes.is_banner' , 1 )
                                ->where('video_tapes.status', DEFAULT_TRUE)
                                ->orderBy('video_tapes.created_at' , 'desc')
                                ->get();
            }

            $banner_ads = [];

            if(Setting::get('is_banner_ad')) {

                $banner_ads = BannerAd::select('id as banner_id', 'file as image', 'title as video_title', 'description as content', 'link')
                            ->where('banner_ads.status', DEFAULT_TRUE)
                            ->orderBy('banner_ads.position' , 'asc')
                            ->get();

            }
            $live_video_num = 0;
            // $live_video_num = LiveVideo::where('status', '=', DEFAULT_TRUE)->whereNotNull('end_time')->whereNotNull('no_of_minutes')->orderByRaw("RAND()")->first()->id;
            $temp_live_video = VideoTape::where('watch_count', '>',0)->whereNotNull('video_resolutions')->whereNotNull('video_path')->orderByRaw("RAND()")->first();
            if($temp_live_video == null) {
                // return redirect()->back();
                $live_video_num = 0;
            }else {
                if($temp_live_video->id < 1)
                    $live_video_num = 1;
                else
                    $live_video_num = $temp_live_video->id;
            }
            
            $request->request->add([ 
                'video_tape_id' => $live_video_num,
            ]);
            
            if (Auth::check()) {
    
                $request->request->add([ 
                    'id'=>Auth::user()->id,
                    'age_limit'=>Auth::user()->age_limit,
                    'view_type' => VIEW_TYPE_OWNER
                ]);
    
            } else {
    
                 $request->request->add([ 
                    'id'=> '',
                    'view_type' => VIEW_TYPE_VIEWER,
                ]);
            }
            
            $data = $this->UserAPI->video_detail($request)->getData();
    
            // video url
            if (isset($data->url)) {
    
                return redirect($data->url);
            }
            $request->request->add([ 
                'r4d_check'=> '',
                'view_type' => VIEW_TYPE_OWNER,
                'r4d_check' => 1
            ]);
            
            $r4d_recent_videos = $this->UserAPI->recently_added($request)->getData();
            $r4d_trendings = $this->UserAPI->trending_list($request)->getData();
            $r4d_suggestions  = $this->UserAPI->suggestion_videos($request)->getData();
            
            $video = VideoTape::first();
            // dd($video);
            if ($data->success && Auth::check()) {
    
                // @todo minimize the code
                // get user playlists
                $data->response_array->playlists = $this->UserAPI->playlists($request)->getData();
    
                $playlists = array();
               
                $response = $data->response_array;
               
                if ($data->response_array->playlists->success) {
                      
                    // check video already exists in user playlits
                    $playlist_ids = array_column($data->response_array->playlists->data, 'playlist_id');
                    
                    if($request->video_tape_id > 0)
                        $is_video_exists_in_playlist = PlaylistVideo::whereIn('playlist_id', $playlist_ids)
                            ->where('video_tape_id', $request->video_tape_id)
                            ->where('user_id', Auth::user()->id)
                            ->get();
                    else
                        $is_video_exists_in_playlist = PlaylistVideo::whereIn('playlist_id', $playlist_ids)
                        ->where('user_id', Auth::user()->id)
                        ->get();

                    $playlist_ids_video_exists = array_column($is_video_exists_in_playlist->toArray(), 'playlist_id');
                    
                    // to set video exists in playlist    
                    $i = 0;
                   
                    foreach ($data->response_array->playlists->data as $value) {
                       
                        $data->response_array->playlists->data[$i]->is_video_exists = (in_array($value->playlist_id, $playlist_ids_video_exists)) ? YES : NO;
    
                        $i++;
                    }
    
                    $playlists = $response->playlists->data;
                }
                
                // Video is autoplaying ,so we are incrementing the watch count 
    
                if ($request->id != $response->video->channel_created_by) {
    
                    $user_id = Auth::check() ? Auth::user()->id : 0;
                    if($request->video_tape_id > 0)
                        VideoRepo::watch_count($request->video_tape_id,$user_id,YES);
                }
                
                return view('user.index')
                        ->with('page' , 'home')
                        ->with('subPage' , 'home')
                        ->with('wishlists' , $wishlists)
                        ->with('recent_videos' , $recent_videos)
                        ->with('r4d_recent_videos' , $r4d_recent_videos)
                        ->with('trendings' , $trendings)
                        ->with('r4d_trendings' , $r4d_trendings)
                        ->with('watch_lists' , $watch_lists)
                        ->with('r4d_watch_lists' , $watch_lists)
                        ->with('suggestions' , $suggestions)
                        ->with('r4d_suggestions' , $r4d_suggestions)
                        ->with('channels' , $channels)
                        ->with('banner_videos', $banner_videos)
                        ->with('banner_ads', $banner_ads)
                        ->with('live_videos',$live_videos)
                        
                            ->with('video' , $response->video)
                            ->with('comments' , $response->comments)
                            ->with('wishlist_status' , $response->wishlist_status)
                            ->with('history_status' , $response->history_status)
                            ->with('main_video' , $response->main_video)
                            ->with('url' , $response->main_video)
                            ->with('report_video', $response->report_video)
                            ->with('videoPath', $response->videoPath)
                            ->with('video_pixels', $response->video_pixels)
                            ->with('videoStreamUrl', $response->videoStreamUrl)
                            ->with('hls_video' , $response->hls_video)
                            ->with('flaggedVideo', $response->flaggedVideo)
                            ->with('ads', $response->ads)
                            ->with('subscribe_status', $response->subscribe_status)
                            ->with('like_count',$response->like_count)
                            ->with('dislike_count',$response->dislike_count)
                            ->with('like_status',$response->like_status)
                            ->with('dislike_status',$response->dislike_status)
                            ->with('subscriberscnt', $response->subscriberscnt)
                            ->with('comment_rating_status', $response->comment_rating_status)
                            ->with('embed_link', $response->embed_link)
                            ->with('tags', $response->tags)
                            ->with('playlists', $playlists);
           
            } 
        //    dd($recent_videos, $r4d_recent_videos, $trendings, $r4d_trendings, $watch_lists);
            return view('user.index')
                        ->with('page' , 'home')
                        ->with('subPage' , 'home')
                        ->with('wishlists' , $wishlists)
                        ->with('recent_videos' , $recent_videos)
                        ->with('r4d_recent_videos' , $r4d_recent_videos)
                        ->with('trendings' , $trendings)
                        ->with('r4d_trendings' , $r4d_trendings)
                        ->with('watch_lists' , $watch_lists)
                        ->with('r4d_watch_lists' , $watch_lists)
                        ->with('suggestions' , $suggestions)
                        ->with('r4d_suggestions' , $r4d_suggestions)
                        ->with('channels' , $channels)
                        ->with('banner_videos', $banner_videos)
                        ->with('banner_ads', $banner_ads)
                        ->with('video' , $video)
                        ->with('live_videos',$live_videos);
        } else {

            return redirect()->route('installTheme');

        }
        
    }

    /**
     * Function Name : trending()
     *
     * @uses To list out videos based on the watching count
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param object $request - User Details
     *
     * @return video details
     */
    public function trending(Request $request) {

        if (Auth::check()) {

            $request->request->add([ 
                'id' => \Auth::user()->id,
                'token' => \Auth::user()->token,
                'device_token' => \Auth::user()->device_token,
                'age'=>\Auth::user()->age_limit,
            ]);

        }

        $trending = $this->UserAPI->trending_list($request)->getData();
        
        return view('user.trending')->with('page', 'trending')
                                    ->with('videos',$trending);
    
    }

    /**
     * Function Name : channels()
     *
     * @uses To list out channels which is created by all the users
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param object $request - User Details
     *
     * @return channel details details
     */
    public function channels(Request $request){

        if(Auth::check()) {

            $request->request->add([ 
                'id' => \Auth::user()->id,
                'token' => \Auth::user()->token,
                'device_token' => \Auth::user()->device_token,
                'age'=>\Auth::user()->age_limit,
            ]);

        }

        $response = $this->UserAPI->channel_list($request)->getData();


        return view('user.channels.list')->with('page', 'channels')
                ->with('subPage', 'channel_list')
                ->with('response', $response);

    }    

    /**
     * Function Name : playlists_index()
     *
     * @uses To list out playlists which is created by the users
     *
     * @created 
     *
     * @updated 
     *
     * @param object $request - User Details
     *
     * @return channel details details
     */
    public function playlists_index(Request $request){

        if(Auth::check()) {
            
            $request->request->add([ 
                'id' => \Auth::user()->id,
                'token' => \Auth::user()->token,
                'device_token' => \Auth::user()->device_token,
                'age'=>\Auth::user()->age_limit,
            ]);
        }

        $response = $this->UserAPI->playlists($request)->getData();
        // $response = $this->UserAPI->playlists_index($request)->getData();

        return view('user.playlist.list')->with('page', 'channels')
                ->with('subPage', 'channel_list')
                ->with('response', $response);

    }

    /**
     * Function Name : history()
     *
     * @uses To list out history of user based
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param object $request - User Details
     *
     * @return array of history 
     */
    public function history(Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token,
            'age'=>\Auth::user()->age_limit,
        ]);

        $histories = $this->UserAPI->watch_list($request)->getData();
        
        return view('user.account.history')
                        ->with('page' , 'history')
                        ->with('subPage' , 'user-history')
                        ->with('histories' , $histories);
    
    }


    /**
     * Function Name : wishlist()
     *
     * @uses To list out wishlist of user based
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param object $request - User Details
     *
     * @return array of wishlist 
     */
    public function wishlist(Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token,
            'age'=>\Auth::user()->age_limit,
        ]);
        
        $videos = $this->UserAPI->wishlist_list($request)->getData();

        return view('user.account.wishlist')
                    ->with('page' , 'wishlist')
                    ->with('subPage' , 'user-wishlist')
                    ->with('videos' , $videos);
    
    }

    /**
     * Function Name : channel_view()
     *
     * @uses Based on the channel id , channel related videos will display
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param integer $id : Channel Id
     *
     * @return channel videos list
     */
    public function channel_view($id , Request $request) {

        $channel = Channel::where('id', $id)->first();
        $r4d_videos = [];
        if ($channel) {

            $request->request->add([ 
                'age' => \Auth::check() ? \Auth::user()->age_limit : "",
                'id'=> \Auth::check() ? \Auth::user()->id : "",
                'channel_id'=> $id,
                'view_type' => \Auth::check() ? \Auth::user()->id == $channel->user_id ? VIEW_TYPE_OWNER : VIEW_TYPE_VIEWER : VIEW_TYPE_VIEWER 
            ]);
            
            if ($request->id != $channel->user_id || !Auth::check()) {

                if ($channel->status == USER_CHANNEL_DECLINED || $channel->is_approved == ADMIN_CHANNEL_DECLINED) {

                    return redirect()->to('/')->with('flash_error', tr('channel_declined'));
                } 
            }

            $videos = $this->UserAPI->channel_videos($id, 0 , $request)->getData();
            
            $channel_owner_id = Auth::check() ? ($channel->user_id == Auth::user()->id ? $channel->user_id : "") : "";

            $trending_videos = $this->UserAPI->channel_trending($id, 4 , $channel_owner_id , $request)->getData();
            
            $channel_playlists = $this->UserAPI->playlists($request)->getData();

            $channel_playlists = $channel_playlists->data;
            
            $payment_videos = $this->UserAPI->payment_videos($id, 0)->getData();

            $live_videos = VideoRepo::live_videos_list($id, WEB, null);

            $subscribe_status = false;

            if ($request->id) {

                $subscribe_status = check_channel_status($request->id, $id);
            }

            $subscriberscnt = subscriberscnt($channel->id);

            $live_video_history = $channels = [];

            if (Auth::check()) {

                $request->request->add([
                    'skip'=>0,
                    'channel_id'=>$id,
                    'id'=>Auth::user()->id,

                ]);
                $channels = getChannels(Auth::user()->id);

                $live_video_history = $this->UserAPI->live_video_revenue($request)->getData();

            }

            $request->request->add([ 
                'r4d_check'=> 1
            ]);
            $r4d_videos = $this->UserAPI->channel_videos($id, 0 , $request)->getData();
           
            return view('user.channels.index')
                        ->with('page' , 'channels_'.$id)
                        ->with('subPage' , 'channels')
                        ->with('channel' , $channel)
                        ->with('channels' , $channels)
                        ->with('live_videos', $live_videos)
                        ->with('videos' , $videos)
                        ->with('r4d_video_lists' , $r4d_videos)
                        ->with('trending_videos', $trending_videos)
                        ->with('channel_playlists', $channel_playlists)
                        ->with('payment_videos', $payment_videos)
                        ->with('subscribe_status', $subscribe_status)
                        ->with('subscriberscnt', $subscriberscnt)
                        ->with('live_video_history', $live_video_history);
        } else {

            return back()->with('flash_error', tr('channel_not_found'));

        }
    }

    /**
     * Function Name : video_view()
     * 
     * @uses To view single video based on video id
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param integer $request - Video id
     *
     * @return based on video displayed all the details'
     */
    public function video_view(Request $request) {
        $videos_list = [];
        $request->request->add([ 
            'video_tape_id' => $request->id,
        ]);

        if (Auth::check()) {

            $request->request->add([ 
                'id'=>Auth::user()->id,
                'age_limit'=>Auth::user()->age_limit,
                'view_type' => VIEW_TYPE_OWNER
            ]);

        } else {

             $request->request->add([ 
                'id'=> '',
                'view_type' => VIEW_TYPE_VIEWER,
            ]);
        }
        
        $data = $this->UserAPI->video_detail($request)->getData();

        // video url
        if (isset($data->url)) {

            return redirect($data->url);
        }

        if ($data->success) {

            // @todo minimize the code
            // get user playlists
            $data->response_array->playlists = $this->UserAPI->playlists($request)->getData();

            $playlists = array();
           
            $response = $data->response_array;
           
            if ($data->response_array->playlists->success) {
                  
                // check video already exists in user playlits
                $playlist_ids = array_column($data->response_array->playlists->data, 'playlist_id');

                $is_video_exists_in_playlist = PlaylistVideo::whereIn('playlist_id', $playlist_ids)
                    ->where('video_tape_id', $request->video_tape_id)
                    ->where('user_id', Auth::user()->id)
                    ->get();

                $playlist_ids_video_exists = array_column($is_video_exists_in_playlist->toArray(), 'playlist_id');
                
                // to set video exists in playlist    
                $i = 0;
               
                foreach ($data->response_array->playlists->data as $value) {
                   
                    $data->response_array->playlists->data[$i]->is_video_exists = (in_array($value->playlist_id, $playlist_ids_video_exists)) ? YES : NO;

                    $i++;
                }

                $playlists = $response->playlists->data;
                
            }
            if($response->video) {
                if(isset($request->video_type) && $request->video_type == VIDEO_TYPE_R4D) {
               
                    $dirname = public_path('uploads/videos/'.$response->video->user_id.'/'.get_video_title($response->video->title));
                    $videos_list = $t_v = $t_f_v = []; $i = 0;
                    if(File::isDirectory($dirname)){
                        $temp_folder_list = File::directories($dirname);
                        sort($temp_folder_list);
                        if(count($temp_folder_list) > 0 ){
                            foreach($temp_folder_list as $folder_key=>$t) {
                                $t_f_v = [];
                                $files = File::files($t);
                                if(count($files) > 0) {
                                    $t_v = [];
                                    foreach($files as $key=>$f) {
                                        $i++;
                                        $r = explode("public/", $f);
                                        $file = end($r);
                                        $t_v[] = ['id'=>$i, 'video' => Helper::web_url().'/'.$file, 'folder_id'=>$folder_key];
                                        shuffle($t_v);
                                    }
                                    // $t_f_v[] = $t_v;
                                }
                                if(count($t_v)>0)
                                    $videos_list[] = $t_v;
                            }
                            // shuffle($t_v);
                        }
                    }
    
                    // if(count($videos_list) > 0) {
                    //     $videos_list = $videos_list[0];
                    // }
                }
                
            }
            // Video is autoplaying ,so we are incrementing the watch count 

            if ($request->id != $response->video->channel_created_by) {

                $user_id = Auth::check() ? Auth::user()->id : 0;

                VideoRepo::watch_count($request->video_tape_id,$user_id,YES);

            }
            
            if($request->has('video_type') && count($videos_list) == 0) {
                $error_message = isset($data->error_messages) ? $data->error_messages : tr('something_error');
                return redirect()->route('user.dashboard')->with('flash_error', $error_message);
            }
            return view('user.single-video')
                    ->with('page' , '')
                    ->with('subPage' , '')
                    ->with('video' , $response->video)
                    ->with('videos_lists' , $videos_list)
                    ->with('comments' , $response->comments)
                    ->with('suggestions',$response->suggestions)
                    ->with('wishlist_status' , $response->wishlist_status)
                    ->with('history_status' , $response->history_status)
                    ->with('main_video' , $response->main_video)
                    ->with('url' , $response->main_video)
                    ->with('channels' , $response->channels)
                    ->with('report_video', $response->report_video)
                    ->with('videoPath', $response->videoPath)
                    ->with('video_pixels', $response->video_pixels)
                    ->with('videoStreamUrl', $response->videoStreamUrl)
                    ->with('hls_video' , $response->hls_video)
                    ->with('flaggedVideo', $response->flaggedVideo)
                    ->with('ads', $response->ads)
                    ->with('subscribe_status', $response->subscribe_status)
                    ->with('like_count',$response->like_count)
                    ->with('dislike_count',$response->dislike_count)
                    ->with('like_status',$response->like_status)
                    ->with('dislike_status',$response->dislike_status)
                    ->with('subscriberscnt', $response->subscriberscnt)
                    ->with('comment_rating_status', $response->comment_rating_status)
                    ->with('embed_link', $response->embed_link)
                    ->with('tags', $response->tags)
                    ->with('playlists', $playlists);
       
        } 
       
        $error_message = isset($data->error_messages) ? $data->error_messages : tr('something_error');

        return redirect()->route('user.dashboard')->with('flash_error', $error_message);
        
    }

    // public function r4d_video_view(Request $request) {
    //     if(isset($request->video_type) && $request->video_type == VIDEO_TYPE_R4D) {
    //         $request->request->add([ 
    //             'video_type' => $request->video_type,
    //         ]);    
    //     }
    //     $temp_video  = VideoTape::where('id',$request->id)->first();
    //     $videos_lists = VideoTape::where('title', $temp_video->title)->pluck('id');
    //     if(count($videos_lists)>0){
    //         foreach($videos_lists as $videos_list) {
    //             $request->request->add([ 
    //                 'video_tape_id' => $request->id,
    //             ]);
                    
                    
    //         }
    //     }
    // }

    /**
     * Function Name : playlist_single_video()
     * 
     * @uses To view single video based on video id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param integer $request - Video id
     *
     * @return based on video displayed all the details'
     *
     */
    public function playlist_single_video(Request $request) { 
  
        $request->request->add([ 
                'video_tape_id' => $request->id,
        ]);

        if (Auth::check()) {

            $request->request->add([ 
                'id'=>Auth::user()->id,
                'age_limit'=>Auth::user()->age_limit,
            ]);

        } else {
             $request->request->add([ 
                'id'=> '',
            ]);
        }

        $data = $this->UserAPI->video_detail($request)->getData();

        if (isset($data->url)) {

            return redirect($data->url);
        }

        if ($data->success) {

            $response = $data->response_array;

            // Video is autoplaying ,so we are incrementing the watch count 

            if ($request->id != $response->video->channel_created_by) {

                $user_id = Auth::check() ? Auth::user()->id : 0;

                VideoRepo::watch_count($request->video_tape_id,$user_id,YES);

            }
        
            return view('user.videos.playlist_single_video')
                        ->with('page' , '')
                        ->with('subPage' , '')
                        ->with('video' , $response->video)
                        ->with('comments' , $response->comments)
                        ->with('suggestions',$response->suggestions)
                        ->with('wishlist_status' , $response->wishlist_status)
                        ->with('history_status' , $response->history_status)
                        ->with('main_video' , $response->main_video)
                        ->with('url' , $response->main_video)
                        ->with('channels' , $response->channels)
                        ->with('report_video', $response->report_video)
                        ->with('videoPath', $response->videoPath)
                        ->with('video_pixels', $response->video_pixels)
                        ->with('videoStreamUrl', $response->videoStreamUrl)
                        ->with('hls_video' , $response->hls_video)
                        ->with('flaggedVideo', $response->flaggedVideo)
                        ->with('ads', $response->ads)
                        ->with('subscribe_status', $response->subscribe_status)
                        ->with('like_count',$response->like_count)
                        ->with('dislike_count',$response->dislike_count)
                        ->with('subscriberscnt', $response->subscriberscnt)
                        ->with('comment_rating_status', $response->comment_rating_status)
                        ->with('embed_link', $response->embed_link)
                        ->with('tags', $response->tags);
       
        } else {

            $error_message = isset($data->error_messages) ? $data->error_messages : tr('something_error');

            return back()->with('flash_error', $error_message);
            
        } 

    }


    /**
     * Function Name : profile()
     *
     * @uses Show the profile list.
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param object $request - User Details
     *
     * @return \Illuminate\Http\Response
     */

    public function profile(Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token,
            'age'=>\Auth::user()->age_limit,
        ]);

        $wishlist = $this->UserAPI->wishlist_list($request)->getData();

        $user = User::find(\Auth::user()->id);

        return view('user.account.profile')
                    ->with('page' , 'profile')
                    ->with('user', $user)
                    ->with('subPage' , 'user-profile')->with('wishlist', $wishlist);
    }

    /**
     * Function Name : update_profile() 
     *
     * @uses Edit profile user details
     * 
     * @created Vithya R
     *
     * @updated 
     *
     * @param object $request - User Details
     *
     * @return \Illuminate\Http\Response
     */
    public function update_profile(Request $request){

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token,
            'age'=>\Auth::user()->age_limit,
        ]);

        $wishlist = $this->UserAPI->wishlist_list($request)->getData();

        return view('user.account.edit-profile')->with('page' , 'profile')
                    ->with('subPage' , 'user-update-profile')
                    ->with('wishlist', $wishlist);
    
    }

    /**
     * Function Name : update_profile() 
     *
     * @uses Save any changes to the users profile.
     * 
     * @created Vithya R
     *
     * @updated 
     *
     * @param object $request - User Details
     *
     * @return \Illuminate\Http\Response
     */
    public function profile_save(Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token,
        ]);

        $response = $this->UserAPI->update_profile($request)->getData();

        if($response->success) {

            if($request->is_json == 1) {

                $response_array = ['success' =>  true, 'message' => 'Profile Updated'];

                return response()->json($response_array, 200);
            }

            return redirect(route('user.profile'))->with('flash_success' , tr('profile_updated'));

        } else {

            $message = isset($response->error) ? $response->error : " "." ".$response->error_messages;

            if($request->is_json == 1) {

                $response_array = ['success' =>  false, 'error' => $response->error, 'error_messages' => $response->error_messages];

                return response()->json($response_array, 200);
            }

            return back()->with('flash_error' , $message);
        }
    
    }

    public function timezone_save(Request $request) {

        $user_details = User::find(Auth::user()->id);

        $user_details->timezone = $request->timezone ?: $user_details->timezone;

        if($user_details->save()) {

            if($request->is_json == 1) {

                $response_array = ['success' =>  true, 'message' => 'Profile Updated'];

                return response()->json($response_array, 200);
            }

            return redirect(route('user.profile'))->with('flash_success' , tr('profile_updated'));

        } else {

            if($request->is_json == 1) {

                $response_array = ['success' =>  false, 'error' => 'timezone save failed', 'error_messages' => 'timezone save failed'];

                return response()->json($response_array, 200);
            }

            return back()->with('flash_error', 'timezone save failed');
        }
    
    }

    /**
     * Function Name : profile_save_password() 
     * 
     * @uses Save changed password.
     * 
     * @created Vithya R
     *
     * @updated 
     *
     * @param object $request - User Details
     *
     * @return \Illuminate\Http\Response
     */
    public function profile_save_password(Request $request) {
        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token,
        ]);

        $response = $this->UserAPI->change_password($request)->getData();

        if($response->success) {

            return back()->with('flash_success' , tr('password_success'));

        } else {

            $message = $response->error." ".$response->error_messages;

            return back()->with('flash_error' , $message);
        }
    
    }

    /**
     * Function Name : profile_change_password() 
     * 
     * @uses Display only password change form
     * 
     * @created Vithya R
     *
     * @updated 
     *
     * @param object $request - User Details
     *
     * @return \Illuminate\Http\Response
     */
    public function profile_change_password(Request $request) {

        return view('user.account.change-password')->with('page' , 'profile')
                    ->with('subPage' , 'user-change-password');

    }

    /**
     * Function Name : add_history()
     *
     * @uses To Add in history based on user, once he complete the video , the video will save
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param Integer $request - Video Id
     *
     * @return response of Boolean with message
     */
    public function add_history(Request $request) {

        if(Auth::check()) {
            $request->request->add([ 
                'id' => \Auth::user()->id,
                'token' => \Auth::user()->token,
                'device_token' => \Auth::user()->device_token,
                'video_tape_id' => $request->video_tape_id
            ]);
        }

        $response = $this->UserAPI->add_history($request)->getData();

        if($response->success) {

            $response->message = Helper::get_message(118);

        } else {

            $response->success = false;

            $response->message = tr('something_error');

        }

        $response->status = $request->status;

        return response()->json($response);
    
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
    public function watch_count(Request $request) {

        if($video = VideoTape::where('id',$request->video_tape_id)
                ->where('status',1)
                ->where('video_tapes.is_approved' , 1)
                ->first()) {

            \Log::info("ADD History - Watch Count Start");

            $user_id = Auth::check() ? Auth::user()->id : 0;

            if($video->getVideoAds) {

                \Log::info("getVideoAds Relation Checked");

                if ($video->getVideoAds->status) {

                    \Log::info("getVideoAds Status Checked");

                    // User logged in or not

                    if ($user_id) {

                        if ($video->user_id != $user_id) {

                            // Check the video view count reached admin viewers count, to add amount for each view

                            if ($video->user_id != Auth::user()->id) {


                                if($video->watch_count >= Setting::get('viewers_count_per_video') && $video->ad_status) {

                                    \Log::info("Check the video view count reached admin viewers count, to add amount for each view");

                                    $video_amount = Setting::get('amount_per_video');

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
            }

            $video->watch_count += 1;

            $video->save();

            \Log::info("ADD History - Watch Count Start completed");

            return response()->json(['success'=>true, 
                    'data'=>['watch_count'=>number_format_short($video->watch_count)]]);

        } else {

            return response()->json(['success'=>false]);
        }

    }

    /**
     * Function Name : delete_history()
     *
     * @uses To delete a history based on logged in user id
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param integer $request - Video Tape Id
     *
     * @return response of success/falure message
     */
    public function delete_history(Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token
        ]);

        $response = $this->UserAPI->delete_history($request)->getData();

        if($response->success) {

            return back()->with('flash_success' , Helper::get_message(121));

        } else {

            return back()->with('flash_error' , tr('admin_not_error'));

        }
    
    }

    /**
     * Function Name : add_wishlist()
     *
     * @uses Add a wishlist based on logged in user id
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param integer $request - Video Tape Id
     *
     * @return response of success/falure message
     */
    public function wishlist_create(Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token,
            'video_tape_id' => $request->video_tape_id
        ]);

        $response = $this->UserAPI->wishlist_create($request)->getData();

        $response->status = $request->status;

        return response()->json($response);
    }

    /**
     * Function Name : delete_wishlist()
     *
     * @uses To delete wishlist based on user id
     * 
     * @created Vithya R
     *
     * @updated 
     *
     * @param intger $request - Video tape id
     *
     * @return response of success/failure message
     */
    public function wishlist_delete(Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token
        ]);

        $response = $this->UserAPI->wishlist_delete($request)->getData();
        
        if($response->success) {

            return back()->with('flash_success', $response->message);

        } else {

            return back()->with('flash_error',  $response->message);
        }
    } 

    /**
     * @method wishlist_operations() 
     *
     * @uses Add / Remove  Wishlist
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param
     *
     * @return json repsonse
     */ 
    public function wishlist_operations(Request $request) {

        try {

            $request->request->add([ 'clear_all_status' => NO]);

            $response = $this->NewUserAPI->wishlist_operations($request)->getData();

            return response()->json($response);

        } catch(Exception $e) {

            if($request->is_json) {

                $response_array = ['success' => false, 'error_messages' => $e->getMessage(), 'error_code' => $e->getCode()];

                return response()->json($response_array);
            }

            return redirect()->to('/')->with('flash_error' , $error_messages);

        }

    }

    /**
     * Function Name : add_comment()
     * 
     * @uses To Add comment based on single video
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param integer $video_tape_id - Video Tape ID
     *
     * @return response of success/failure message
     */
    public function add_comment(Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token,
            'video_tape_id'=>$request->video_tape_id
        ]);

        $response = $this->UserAPI->user_rating($request)->getData();

        if($response->success) {

            $response->message = Helper::get_message(118);

        } else {

            $response->success = false;

            $response->message = tr('something_error');
        }

        return response()->json($response);
    
    }

    public function comments(Request $request) {

        $videos = Helper::get_user_comments(\Auth::user()->id,WEB);

        return view('user.comments')
                    ->with('page' , 'profile')
                    ->with('subPage' , 'user-comments')
                    ->with('videos' , $videos);
    }

    /**
     * Function Name : channel_create()
     *
     * @uses To create a channel based on logged in user id  (Form Rendering)
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @return respnse with flash message
     */
    public function channel_create() {
        
        $model = new Channel;

        $channels = getChannels(Auth::user()->id);

        if((count($channels) == 0 || Setting::get('multi_channel_status'))) {

            // if (Auth::user()->user_type) {

            //     return view('user.channels.create')->with('page', 'my_channel')
            //         ->with('subPage', 'create_channel')->with('model', $model);

            // } else {

            //     return redirect(route('user.dashboard'))->with('flash_error', tr('subscription_error'));

            // }
            return view('user.channels.create')->with('page', 'my_channel')
            ->with('subPage', 'create_channel')->with('model', $model);
        } else {

            return redirect(route('user.dashboard'))->with('flash_error', tr('channel_create_error'));
        }

    }

    /**
     * Function Name : save_channel()
     *
     * @uses To create a channel based on logged in user id
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param Object $request - Channel Details
     *
     * @return respnse with flash message
     */
    public function save_channel(Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'channel_id' =>$request->id,
            'device_type'=>DEVICE_WEB,
        ]);
       
        $response = CommonRepo::channel_save($request)->getData();

        if($response->success) {
            // $response->message = Helper::get_message(118);
            return redirect(route('user.channel', ['id'=>$response->data->id]))
                ->with('flash_success', $response->message);
        } else {
            
            return back()->with('flash_error', $response->error_messages);
        }

    }

    /**
     * Function Name : channel_edit()
     *
     * @uses To edit a channel based on logged in user id (Form Rendering)
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param integer $id - Channel Id
     *
     * @return respnse with Html Page
     */
    public function channel_edit($id) {

        $model = Channel::find($id);

        if (Auth::check()) {

            if ($model) {

                if (Auth::user()->id != $model->user_id) {

                    return redirect(route('user.channel.mychannel'))->with('flash_error', tr('unauthroized_person'));

                }

            }

        }

        return view('user.channels.edit')->with('page', 'channels')
                    ->with('subPage', 'edit_channel')->with('model', $model);

    }

    /**
     * Function Name : channel_delete()
     *
     * @uses To delete a channel based on logged in user id & channel id (Form Rendering)
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param integer $request - Channel Id
     *
     * @return response with flash message
     */
    public function channel_delete(Request $request) {

        $channel = Channel::where('id' , $request->id)->first();

        if($channel) {  

            if (Auth::check()) {

                if (Auth::user()->id != $channel->user_id) {

                    return redirect(route('user.channel.mychannel'))->with('flash_error', tr('unauthroized_person'));

                }
                
            }     

            $channel->delete();

            return redirect(route('user.dashboard'))->with('flash_success',tr('channel_delete_success'));

        } else {

            return back()->with('flash_error',tr('something_error'));

        }

    }

    /**
     * Function Name : delete_account()
     *
     * @uses To delete account , based on the user (Form Rendering)
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param object $request - User Details
     *
     * @return response of success/failure message
     */
    public function delete_account(Request $request) {

        if(\Auth::user()->login_by == 'manual') {

            return view('user.account.delete-account')
                    ->with('page' , 'profile')
                    ->with('subPage' , 'delete-account');
        } else {

            return $this->delete_account_process($request);

        }
        
    }

    /**
     * Function Name : delete_account()
     *
     * @uses To delete account , based on the user
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param object $request - User Details
     *
     * @return response of success/failure message
     */
    public function delete_account_process(Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token
        ]);

        $response = $this->UserAPI->delete_account($request)->getData();

        if($response->success) {
            
            return redirect(route('user.dashboard'))->with('flash_success', tr('user_account_delete_success'));

        } else {

            return back()->with('flash_error', $response->error_messages);

        }

        return back()->with('flash_error', Helper::get_error_message(146));

    }

    /**
     * Function Name : save_report_videos
     *
     * @uses Save report videos based on user based
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param object $request - Post Attributes
     *
     * @return flash message
     */
    public function save_report_video(Request $request) {
       //  try {
            // Validate the coming post values
        $validator = Validator::make($request->all(), [
            'video_tape_id' => 'required',
            'reason' => 'required',
        ]);
        // If validator Fails, redirect same with error values
        if ($validator->fails()) {
             //throw new Exception("error", tr('admin_published_video_failure'));
            return back()->with('flash_error', tr('admin_published_video_failure'));
        }
        // Assign Post request values into Data variable
        $data = $request->all();

        // include user_id index into the data varaible  "Auth::user()->id" -> Logged In user id
        $data['user_id'] = \Auth::user()->id;
        $data['status'] = DEFAULT_TRUE;
        // Save the values in DB
        if (Flag::create($data)) {
            return redirect('/')->with('flash_success', tr('report_video_success_msg'));
        } else {
            //throw new Exception("error", tr('admin_published_video_failure'));
            return back()->with('flash_error', tr('admin_published_video_failure'));
        }
        /*} catch (Exception $e) {
            return back()->with('flash_error', $e);
        }*/
    
    }

    /**
     * Function Name : remove_report_video()
     *
     * @uses Remove the video from spam folder and make it as unspam
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param integer $id Flag id
     *
     * @return flash error/flash success
     */
    public function remove_report_video($id) {
        // Load Spam Video from flag section
        $model = Flag::where('video_tape_id', $id)->where('user_id', Auth::user()->id)->first();

        Log::info("Loaded Values : ".print_r($model, true));
        // If the flag model exists then delete the row
        if ($model) {
            Log::info("Loaded Values 1 : ".print_r($model, true));
            Log::info("Delete values :". print_r($model->delete()));
            $model->delete();
            return back()->with('flash_success', tr('unmark_report_video_success_msg'));
        } else {
            // throw new Exception("error", tr('admin_published_video_failure'));
            return back()->with('flash_error', tr('admin_published_video_failure'));
        }
    
    }

    /**
     * Function Name : spam_videos()
     *
     * @uses Based on logged in user load spam videos
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @return spam videos
     */
    public function spam_videos(Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token,
            'age'=>\Auth::user()->age_limit,
        ]);
        // Get logged in user id

        $model = $this->UserAPI->spam_videos($request, 12)->getData();

        // Return array of values
        return view('user.account.spam_videos')->with('model' , $model)
                        ->with('page' , 'Profile')
                        ->with('subPage' , 'Spam Videos');
    
    }   


    public function subscriptions() {

        $query = Subscription::where('status', DEFAULT_TRUE);

        if(Auth::check()) {

            if(Auth::user()->zero_subscription_status == 0) {

                $query->whereNotIn('amount', [0]);

            }

        }

        $model = $query->get();

        return view('user.account.subscriptions')->with('subscriptions', $model)->with('page', 'Profile')->with('subPage', 'Subscriptions');
    
    }

    public function ad_request(Request $request) {

        if($data = VideoTape::find($request->id)) {

            $data->ad_status  = $data->ad_status ? 0 : 1;

            if($data->save()) {

                if($data->getVideoAds) {

                    $data->getVideoAds->status = $data->ad_status;

                    $data->getVideoAds->save();
                }
            }

            return response()->json(['status'=>$data->ad_status, 'success'=>true], 200);

        } else {

            return response()->json(['success'=>false], 200);
            
        }
    
    }

    public function video_upload(Request $request) {

        $model = new VideoTape;

        $id = $request->id;

        $channel = '';

        if (Auth::check()) {

            $channel = Channel::where('user_id', Auth::user()->id)->where('id', $id)->first();

            // if(!Auth::user()->user_type) {

            //     return redirect(route('user.dashboard'))->with('flash_error', tr('subscribe_to_continue_video'));
            // }
            
        }

        if (!$channel) {

            return redirect(route('user.channel.mychannel'))->with('flash_error', tr('unauthroized_person'));
        }

        $categories_list = $this->UserAPI->categories_list($request)->getData();

        $tags = $this->UserAPI->tags_list($request)->getData()->data;
        $channels_list = Channel::where('user_id', Auth::user()->id)->get();
        
        return view('user.videos.create')->with('model', $model)->with('page', 'videos')
            ->with('subPage', 'upload_video')->with('id', $id)
            ->with('categories', $categories_list)
            ->with('channels', $channels_list)
            ->with('tags', $tags);
    
    }

    public function directory_delete(Request $request) {
        $video_title = get_video_title($request->video_title);
        $sub_folder = $request->sub_folder;
        $option = $request->option;

        if(auth()->user()) {
            $user_name = auth()->user()->id;
        }
        else    
            $user_name = $request->uploaded_by;

        $path = public_path('uploads/videos/'.$user_name.'/'.$video_title);
        $sub_path = public_path('uploads/videos/'.$user_name.'/'.$video_title.'/'.$sub_folder);
        
        $delete_directory = File::deleteDirectory($sub_path);
        $temp_list = File::directories($path);
        $t = [];
        foreach($temp_list as $temp) {
            $e = explode('/', $temp);
            $t[] = end($e);
        }
        return $t;
    }

    public function directory_create(Request $request) {
        $video_title = get_video_title($request->video_title);
        
        $option = $request->option;
        if(auth()->user()) {
            $user_name = auth()->user()->id;
        }
        else    
            $user_name = $request->uploaded_by;

        $path = public_path('uploads/videos/'.$user_name.'/'.$video_title);
            
        $this->createFolder($path);
        
        $temp_list = File::directories($path);
        
        if($option == 'root' && count($temp_list) == 0) {
            $new = count($temp_list)+1;
            $sub_path = $path.'/'.$new;
            $this->createFolder($sub_path);
            $temp_list = File::directories($path);
        }elseif($option == 'sub') {
            $new = count($temp_list)+1;
            $sub_path = $path.'/'.$new;
            $this->createFolder($sub_path);
            $temp_list = File::directories($path);
        }
        $t = [];
        foreach($temp_list as $temp) {
            $e = explode('/', $temp);
            $t[] = end($e);
        }
        return $t;
    }

    public function createFolder($path) {
        if(!File::isDirectory($path)){
            $makedirectory = File::makeDirectory($path, 0777, true, true);
        }
    }

    public function directory_files(Request $request) {
        $video_title = get_video_title($request->video_title);
        $t_temp = [];

        if(auth()->user()) {
            $user_name = auth()->user()->id;
        }
        else    
            $user_name = $request->uploaded_by;

        $folder_path = public_path('uploads/videos/'.$user_name.'/'.$video_title.'/'.$request->sub_folder);
        if(File::isDirectory($folder_path)){
            $files = File::files($folder_path);
            if(count($files) > 0) {
                foreach($files as $file) {
                    $t = explode('/', $file);
                    $temp = end($t);
                    $e = explode('608198452_', $temp);
                    $t_e = end($e);
                    $t_temp[] = [$t_e, $temp, $file];
                }
            }
        }
        return $t_temp;
    }
    public function move_files(Request $request) {
        $target_folder = $request->target_folder;
        $sub_folder = $request->sub_folder;
        if($target_folder != $sub_folder) {
            $video_title = get_video_title($request->video_title);
            $file_name = $request->file_name;

            if(auth()->user()) {
                $user_name = auth()->user()->id;
            }
            else    
                $user_name = $request->uploaded_by;

            if(File::isDirectory(public_path('uploads/videos/'.$user_name.'/'.$video_title.'/'.$sub_folder)) 
            && File::isDirectory('uploads/videos/'.$user_name.'/'.$video_title.'/'.$sub_folder))
            {
                File::move(public_path('uploads/videos/'.$user_name.'/'.$video_title.'/'.$sub_folder.'/'.$file_name),
                public_path('uploads/videos/'.$user_name.'/'.$video_title.'/'.$target_folder.'/'.$file_name));
                return response()->json(['success'=>true, 'filename' => $file_name] , 200);
            }
        }    
        return response()->json(['success'=>false, 'filename' => $file_name] , 200);
    }
    public function video_edit(Request $request) {

        $model = VideoTape::find($request->id);

        if($model) {

            if (Auth::check()) {

                if (Auth::user()->id != $model->user_id) {

                    return redirect(route('user.channel.mychannel'))->with('flash_error', tr('unauthroized_person'));

                }
                
            }    

            $model->publish_time = $model->publish_time ? (($model->publish_time != '0000-00-00 00:00:00') ? date('d-m-Y H:i:s', strtotime($model->publish_time)) : null) : null;

            $categories_list = $this->UserAPI->categories_list($request)->getData();

            $tags = $this->UserAPI->tags_list($request)->getData()->data;

            $model->tag_id = VideoTapeTag::where('video_tape_id', $request->id)->where('status', TAG_APPROVE_STATUS)->get()->pluck('tag_id')->toArray();

            return view('user.videos.edit')->with('model', $model)->with('page', 'videos')
                ->with('subPage', 'upload_video')
                ->with('categories', $categories_list)
                ->with('tags', $tags);

        } else {

            return back()->with('flash_error', tr('video_not_found'));

        }
   
    }
// =======================================
// public function test(Request $request) {
	// $response = CommonRepo::video_save($request)->getData();
	// echo "TEST ERRROR";
// }
// =======================================

    public function video_save(Request $request) {
        // dd($request->all(), $request->hasfile('file'));
        // if($request->r4d_status == 1) {
        //     Helper::video_upload($request->video, $request->title, $uploadfolder);
        //     return true;
        // }
        // dd(get_video_title($request->title));
        if($request->hasfile('file'))
            $new_size = $request->file('file')->getSize();
        else
            $new_size = $request->file('video')->getSize();
        $cur_size = Helper::getTotalVideoSize(auth()->user()->id);
        $cur_sub  = UserPayment::getCurSubscr(auth()->user()->id);

        if($new_size+$cur_size > $cur_sub->limit_data && $cur_sub->limit_data > 0) {
            $response = ['success'=>false, 'error_messages'=>'You can not upload files any more!'];
            return response()->json($response);
        }

        if($request->hasfile('file')) {
            // $request->request->add([ 
            //     'video' => $request->file('file')[0]
            // ]);
            $response = CommonRepo::r4d_video_save($request)->getData();
        }
        else
            $response = CommonRepo::video_save($request)->getData();
        
        if ($response->success) {

            $view = '';

            if ($response->data->video_type == VIDEO_TYPE_UPLOAD || $response->data->video_type == VIDEO_TYPE_R4D) {

                $tape_images = VideoTapeImage::where('video_tape_id', $response->data->id)->get();

                $view = \View::make('user.videos.select_image')
                        ->with('model', $response)
                        ->with('tape_images', $tape_images)
                        ->render();

            }

            $message = tr('user_video_upload_success');

            // Check the video status 

            if($response->data->is_approved == DEFAULT_FALSE) {

                $message = tr('user_video_upload_waiting_for_admin_approval');

            }

            \Session::set('flash_message_ajax' , $message);

            return response()->json(['success'=>true, 'path'=>$view, 'data'=>$response->data , 'message' => 'Successfull uploaded'], 200);

        } else {

            return response()->json($response);

        }

    }   

    public function video_delete($id) {
        if($video = VideoTape::where('id' , $id)->first())  {

            if (Auth::check()) {

                if (Auth::user()->id != $video->user_id) {

                    return redirect(route('user.channel.mychannel'))->with('flash_error', tr('unauthroized_person'));

                }
                
            }    
            
            Helper::delete_picture($video->video, "/uploads/videos/");

            Helper::delete_picture($video->subtitle, "/uploads/subtitles/"); 

            if ($video->banner_image) {

                Helper::delete_picture($video->banner_image, "/uploads/images/");
            }

            Helper::delete_picture($video->default_image, "/uploads/images/");

            if ($video->video_path) {

                $explode = explode(',', $video->video_path);

                if (count($explode) > 0) {


                    foreach ($explode as $key => $exp) {


                        Helper::delete_picture($exp, "/uploads/videos/");

                    }
                }
            }
            
            $video->delete();
        }

        return back()->with('flash_success', tr('video_delete_success'));
    }
    public function r4d_video_delete($id) {
        if($video = VideoTape::where('id' , $id)->first())  {
            $video_title = $video->title;
            if (Auth::check()) {
                if (Auth::user()->id != $video->user_id) {
                    return redirect(route('user.channel.mychannel'))->with('flash_error', tr('unauthroized_person'));
                }
            }    
            $group_list = VideoTape::where('id', $video->id)->pluck('id');
            if(count($group_list) > 0) {
                foreach($group_list as $video_id) {
                    $filename = '';
                    if($video = VideoTape::where('id', $video_id)->where('video_type', VIDEO_TYPE_R4D)->first())  {
                        if (Auth::check()) {
                            if (Auth::user()->id != $video->user_id) {
                                return redirect(route('user.channel.mychannel'))->with('flash_error', tr('unauthroized_person'));
                            }
                        }    
                        $filename = str_replace(url('/').'/','', $video->video);
                        Helper::delete_picture_r4d($video->video, $filename);
                        Helper::delete_picture($video->subtitle, "/uploads/subtitles/"); 
            
                        if ($video->banner_image) {
            
                            Helper::delete_picture($video->banner_image, "/uploads/images/");
                        }
            
                        Helper::delete_picture($video->default_image, "/uploads/images/");
            
                        if ($video->video_path) {
            
                            $explode = explode(',', $video->video_path);
            
                            if (count($explode) > 0) {
            
            
                                foreach ($explode as $key => $exp) {
            
            
                                    Helper::delete_picture($exp, "/uploads/videos/");
            
                                }
                            }
                        }
                        
                        $video->delete();
                    }
                    if (file_exists(public_path($filename))) {
                        File::delete(public_path($filename));
                    }
                }
            }

            $path = public_path('uploads/videos/'.auth()->user()->id.'/'.get_video_title($video_title));
            if(File::isDirectory($path))
                File::deleteDirectory($path);
    
        }   
        return back()->with('flash_success', tr('video_delete_success'));
    }
    public function r4d_one_video_delete(Request $request) {
        $dirname = public_path('uploads/videos/'.auth()->user()->id.'/'.get_video_title($request->video_title));
        $file_name = $request->filename;
        $videos_list = []; $i = 0;
        if(File::isDirectory($dirname)){
            $temp_folder_list = File::directories($dirname);
            // dd($temp_folder_list);
            if(count($temp_folder_list) > 0 ){
                foreach($temp_folder_list as $t) {
                    $files = File::files($t);
                    if(count($files) > 0) {
                        foreach($files as $key=>$f) {
                            if(strpos($f, $file_name) !== false){
                                $r = explode('public', $f);
                                $r_f = Helper::web_url().end($r);
                                $r_p = end($r);
                                if (file_exists(public_path($r_p))) {
                                    File::delete(public_path($r_p));
                                }
                                $delete_status = ['success'=>true, 's_file'=>$r_f, 'l_file'=>$file_name];
                                return response()->json($delete_status);
                            } 
                        }
                    }
                }
                // $videos_list[] = $t_v;
            }
        }
            
        // if (file_exists(public_path($filename))) {
        //     File::delete(public_path($filename));
        // }
        $delete_status = ['success'=>false, 's_file'=>'', 'l_file'=>$file_name];
        return response()->json($delete_status);
            
    }
    public function delete_r4d_files(Request $request) {
        $filename = '';
        $full_filename = $request->filename;
        $temp_fname = explode('public/', $full_filename);
        $filename = end($temp_fname);
        
        if($video = VideoTape::where('video','like', '%'.$filename)->where('video_type', VIDEO_TYPE_R4D)->first())  {
            if (Auth::check()) {

                if (Auth::user()->id != $video->user_id) {

                    return redirect(route('user.channel.mychannel'))->with('flash_error', tr('unauthroized_person'));

                }
                
            }    

            Helper::delete_picture_r4d($video->video, $filename);
            // Helper::delete_picture($video->subtitle, "/uploads/subtitles/"); 

            // if ($video->banner_image) {

            //     Helper::delete_picture($video->banner_image, "/uploads/images/");
            // }

            // Helper::delete_picture($video->default_image, "/uploads/images/");

            // if ($video->video_path) {

            //     $explode = explode(',', $video->video_path);

            //     if (count($explode) > 0) {


            //         foreach ($explode as $key => $exp) {


            //             Helper::delete_picture($exp, "/uploads/videos/");

            //         }
            //     }
            // }
            
            // $video->delete();
        }
        if (file_exists(public_path($filename))) {
            File::delete(public_path($filename));
        }
        return $request->shortname;
        // return back()->with('flash_success', tr('video_delete_success'));
    }

    public function save_default_img(Request $request) {

        $response = CommonRepo::set_default_image($request)->getData();

        return response()->json($response);

    }

    public function upload_video_image(Request $request) {
        if($request->r4d_status == "r4d_edit") {
            $response = CommonRepo::r4d_upload_video_image($request)->getData();
        }else
            $response = CommonRepo::upload_video_image($request)->getData();

        return response()->json($response);
    }


    public function user_subscription_save($s_id, $u_id) {

        $response = CommonRepo::save_subscription($s_id, $u_id)->getData();

        if($response->success) {

            return redirect()->route('user.channel.mychannel')->with('flash_success', $response->message);

        } else {

            return back()->with('flash_error', $response->message);

        }

    }

    public function get_images($id) {

        $response = CommonRepo::get_video_tape_images($id)->getData();

        $tape_images = VideoTapeImage::where('video_tape_id', $id)->get();

        $view = \View::make('user.videos.select_image')->with('model', $response)
            ->with('tape_images', $tape_images)->render();

        return response()->json(['path'=>$view, 'data'=>$response->data]);

    }  

    /**
     * Used to get the redeems
     *
     */

    public function redeems(Request $request) {

        $redeem_details = Auth::user()->userRedeem;

        if(!$redeem_details) {

            $redeem_details = new Redeem;

            $redeem_details->user_id = Auth::user()->id;

            $redeem_details->status = APPROVED;

            $redeem_details->remaining = $redeem_details->paid = $redeem_details->total = 0.00;

            $redeem_details->save();

        }

        $min_status = Setting::get('minimum_redeem') < $redeem_details->remaining;

        $redeem_details->send_redeem_btn_status = $redeem_details && $min_status;

        $redeem_requests = Auth::user()->userRedeemRequests()->orderBy('created_at', 'desc')->get();
        
        return view('user.redeems.index')
                    ->with('redeem_details', $redeem_details)
                    ->with('redeem_requests', $redeem_requests);

    }

    /**
     * Send Request to admin
     *
     */

    public function send_redeem_request(Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token
        ]);

        $response = $this->UserAPI->send_redeem_request($request)->getData();

        if($response->success) {

            return back()->with('flash_success', tr('send_redeem_request_success'));

        } else {

            return back()->with('flash_error', $response->error_messages);
        }

        return back()->with('flash_error', Helper::get_error_message(146));

    }

    /**
     * Send Request to admin
     *
     */

    public function redeem_request_cancel($id , Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token,
            'redeem_request_id' => $id,
        ]);

        $response = $this->UserAPI->redeem_request_cancel($request)->getData();

        if($response->success) {

            return back()->with('flash_success', tr('send_redeem_request_success'));

        } else {

            return back()->with('flash_error', $response->error_messages);
        }

        return back()->with('flash_error', Helper::get_error_message(146));

    }

    public function page_view($id) {

        $page = Page::find($id);

        if (!$page) {

            return back()->with('flash_error', tr('no_page_found'));

        }

        return view('static.common')->with('model' , $page)
                        ->with('page' , $page->type)
                        ->with('subPage' , '');

    }

    public function subscribe_channel(Request $request) {

        $validator = Validator::make( $request->all(), array(
            'user_id'     => 'required|exists:users,id',
            'channel_id'     => 'required|exists:channels,id',
        ));

        
        if ($validator->fails()) {

            $error_messages = implode(',', $validator->messages()->all());

            return back()->with('flash_error', $error_messages);

        } else {

            $model = ChannelSubscription::where('user_id', $request->user_id)->where('channel_id',$request->channel_id)->first();

            if (!$model) {

                $model = new ChannelSubscription;

                $model->user_id = $request->user_id;

                $model->channel_id = $request->channel_id;

                $model->status = DEFAULT_TRUE;

                $model->save();

                $channel_details = Channel::find($request->channel_id);

                $notification_data['from_user_id'] = $request->user_id; 

                $notification_data['to_user_id'] = $channel_details->user_id;

                $notification_data['notification_type'] = BELL_NOTIFICATION_NEW_SUBSCRIBER;

                $notification_data['channel_id'] = $channel_details->id;

                dispatch(new BellNotificationJob(json_decode(json_encode($notification_data))));

                return back()->with('flash_success', tr('channel_subscribed'));
                
            } else {
                
                return back()->with('flash_error', tr('already_channel_subscribed'));

            }
        }
   
    }

    public function unsubscribe_channel(Request $request) {

        $validator = Validator::make( $request->all(), array(
                'subscribe_id'     => 'required|exists:channel_subscriptions,id',
                ));


        if ($validator->fails()) {

            $error_messages = implode(',', $validator->messages()->all());

            return back()->with('flash_error', $error_messages);

        } 

        $model = ChannelSubscription::find($request->subscribe_id);

        if ($model) {

            $model->delete();

            
            return back()->with('flash_success', tr('channel_unsubscribed'));

        } else {
            
            return back()->with('flash_error', tr('not_found'));

        }

    }

    /**
     * @method channels_unsubscribe_subscribe() 
     *
     * @uses used to update the subscribe status
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param
     *
     * @return json repsonse
     */ 
    public function channels_unsubscribe_subscribe(Request $request) {

        try {

            $response = $this->NewUserAPI->channels_unsubscribe_subscribe($request)->getData();

            return response()->json($response->data);

        } catch(Exception $e) {

            if($request->is_json) {

                $response_array = ['success' => false, 'error_messages' => $e->getMessage(), 'error_code' => $e->getCode()];

                return response()->json($response_array);
            }

            return redirect()->to('/')->with('flash_error' , $error_messages);

        }

    } 

    public function likeVideo(Request $request)  {
        $request->request->add([
            'id' => Auth::user()->id,
            'token'=>Auth::user()->token
        ]);

        $response = $this->UserAPI->likevideo($request)->getData();

        // dd($response);
        return response()->json($response);

    }

    public function disLikeVideo(Request $request) {

        $request->request->add([ 
            'id' => Auth::user()->id,
            'token'=>Auth::user()->token
        ]);

        $response = $this->UserAPI->dislikevideo($request)->getData();

        return response()->json($response);

    }

    public function channel_subscribers(Request $request) {

        $list = [];

        $channel_id = $request->channel_id ? $request->channel_id : '';

        $channel = null;

        if ($channel_id) {

            $list[] = $request->channel_id;

            $channel = Channel::find($channel_id);

        } else {

            $channels = getChannels(Auth::user()->id);

            foreach ($channels as $key => $value) {
                $list[] = $value->id;
            }
        }

        $subscribers = ChannelSubscription::whereIn('channel_subscriptions.channel_id', $list)
                        ->select('channel_subscriptions.channel_id as channel_id',
                                'channels.name as channel_name',
                                'users.id as user_id',
                                'users.name as user_name',
                                'users.picture as user_image',
                                'channel_subscriptions.id as subscriber_id',
                                'channel_subscriptions.created_at as created_at')
                        ->leftJoin('channels', 'channels.id', '=', 'channel_subscriptions.channel_id')
                        ->leftJoin('users', 'users.id', '=', 'channel_subscriptions.user_id')
                        ->orderBy('created_at', 'desc')
                        ->paginate();

        return view('user.channels.subscribers')->with('page', 'channels')->with('subPage', 'subscribers')->with('subscribers', $subscribers)->with('channel_id', $channel_id)->with('channel', $channel);

    }

    public function card_details(Request $request) {

        $cards = Card::where('user_id', Auth::user()->id)->get();

        $video_id = $request->v_id ? $request->v_id : '';

        $subscription_id = $request->s_id ? $request->s_id : '';

        return view('user.account.cards')->with('page', 'account')
            ->with('subPage', 'cards')
            ->with('cards', $cards)
            ->with('video_id', $video_id)
            ->with('subscription_id', $subscription_id);
    }


    /**
     * Show the payment methods.
     *
     * @return \Illuminate\Http\Response
     */
    public function cards_add(Request $request) {

        $last_four = substr($request->number, -4);

        $stripe_secret_key = \Setting::get('stripe_secret_key');

        $response = json_decode('{}');

        if($stripe_secret_key) {

            \Stripe\Stripe::setApiKey($stripe_secret_key);

        } else {

            $response->success = false;
            
            $response->message = tr('adding_cards_not_enabled_application');

            return back()->with('flash_errors', $response);
        }

        try {

            // Get the key from settings table
            
            $customer = \Stripe\Customer::create([
                    "card" => $request->stripeToken,
                    "email" => \Auth::user()->email
                ]);

            if($customer) {

                $customer_id = $customer->id;


                $cards = new Card;
                
                $cards->user_id = \Auth::user()->id;

                $cards->customer_id = $customer_id;

                $cards->last_four = $customer->sources->data[0]->last4 ? $customer->sources->data[0]->last4 : "";

                $cards->card_token = $customer->sources->data ? $customer->sources->data[0]->id : "";

                // Check is any default is available
                $check_card = Card::where('user_id', \Auth::user()->id)->first();

                // $cards->cvv = $request->cvv;

                $cards->card_name = $request->card_name;

                // $cards->month = $request->month;

                // $cards->year = $request->year;

                $cards->is_default = $check_card ? 0 : 1;
                
                $cards->save();

                $user = User::find(\Auth::user()->id);

                if($user && $cards->is_default) {

                    $user->payment_mode = 'card';
                    $user->card_id = $cards->id;
                    $user->save();

                }

                $response_array = array('success' => true);

                $response_code = 200;

            } else {
                $response->message('Could not create client ID');
            }
        
        } catch(Exception $e) {

            return back()->with('flash_error' , $e->getMessage());

        }
            
        if ($request->video_id) {

            return redirect(route('user.subscription.ppv_invoice', $request->video_id))->with('flash_success', tr('successfully_created'));

        } else if($request->subscription_id) {

            return redirect(route('user.subscription.invoice', ['s_id'=>$request->subscription_id]))->with('flash_success', tr('successfully_created'));

        }

        return back()->with('flash_success', tr('successfully_created'));
    }



    public function payment_card_default(Request $request)
    {
        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
        ]);

        $response = $this->UserAPI->default_card($request)->getData();

        if($response->success) {
            $message = tr('card_default_success');
            $type = "flash_success";
        } else {
            $message = tr('unkown_error');
            $type = "flash_error";
        }

        return back()->with($type, $message);
    }

    /**
     * Show the payment methods.
     *
     * @return \Illuminate\Http\Response
     */
    public function payment_card_delete(Request $request)
    {
        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
        ]);

        $response = $this->UserAPI->delete_card($request)->getData();
        
        if($response->success) {

            $message = $response->message;

            $type = "flash_success";

        } else {
            $message = $response->error_messages;
            $type = "flash_error";
        }

        return back()->with($type, $message);
    }

    /**
     * Show the payment methods.
     *
     * @return \Illuminate\Http\Response
     */
    public function payment_update_default(Request $request) {

        $this->validate($request, [
                'payment_mode' => 'required',
            ]);

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
        ]);        

        $response = $this->UserAPI->payment_mode_update($request)->getData();

        if($response->success) {
            $message = tr('card_default_success');
            $type = "flash_success";
        } else {
            $message = tr('unkown_error');
            $type = "flash_error";
        }

        return back()->with($type, $message);
    }

    /**
     * Function Name : stripe_payment()
     *
     * To pay the payment of subscription through stripe 
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param object $request - user and subscription details
     *
     * @return json response details
     */
    public function stripe_payment(Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'subscription_id' => $request->subscription_id,
            'coupon_code'=>$request->coupon_code
        ]);        

        $response = $this->UserAPI->stripe_payment($request)->getData();

        if ($response->success) {

            return redirect(route('user.subscription.success'))->with('flash_success', $response->message);

        } else {

            if ($response->error_code == 901) {

                return back()->with('flash_error', $response->error_messages.'. '.tr('default_card_add_message').'  <a href='.route('user.card.card_details', ['s_id'=>$request->subscription_id]).'>'.tr('add_card').'</a>');

            }

            return back()->with('flash_error', $response->error_messages);
        }

    }

    /**
     * Function Name : subscribed_channels()
     *
     * @uses To list otu  subscribed channels based on logged in users
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param object $request - user details
     *
     * @return json response details
     */
    public function subscribed_channels(Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
        ]);        

        if ($request->id) {

            $channel_id = ChannelSubscription::where('user_id', $request->id)->pluck('channel_id')->toArray();

            $request->request->add([ 
                'channel_id' => $channel_id,
            ]);        
        }

        $response = $this->UserAPI->channel_list($request)->getData();

        // dd($response);

        return view('user.channels.list')->with('page', 'channels')
                ->with('subPage', 'channel_list')
                ->with('response', $response);

    }


    /**
     * Function Name : partialVideos()
     *
     * @uses To get video details of channels videos using skip & take
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param object $request - user and channel details
     *
     * @return json response details
     */
    public function partialVideos(Request $request) {

        $request->request->add([ 

               'age' => \Auth::check() ? \Auth::user()->age_limit : "",

        ]);

        $videos = $this->UserAPI->channel_videos($request->channel_id, $request->skip, $request)->getData();

        $channel = Channel::find($request->channel_id);

        $view = View::make('user.videos.partial_videos')
                    ->with('videos',$videos)
                    ->with('channel',$channel)
                    ->render();

        return response()->json(['view'=>$view, 'length'=>count($videos)]);
    
    }

    /**
     * Function Name : payment_mgmt_videos()
     *
     * @uses To get payment video details of logged in user using skip & Take
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param object $request - user and channel details
     *
     * @return json response details
     */
    public function payment_mgmt_videos(Request $request) {

        // Get Videos

        // $videos = VideoRepo::channel_videos($request->channel_id, null, $request->skip);

       // $payment_videos = VideoRepo::payment_videos($request->channel_id, null, $request->skip);

        $payment_videos = $this->UserAPI->payment_videos($request->channel_id, $request->skip)->getData();


        $view = View::make('user.videos.partial_payment_videos')
                    ->with('payment_videos', $payment_videos)->render();

        return response()->json(['view'=>$view, 'length'=>$payment_videos->count]);
    }


    /**
     * Function Name : invoice()
     *
     * @uses To Display subscription invoice page based on subscription id
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param integer $id - subscription id
     *
     * @return json response details
     */
    public function invoice(Request $request) {

        $request->request->add([ 
            'u_id'=>Auth::check() ? \Auth::user()->id : '',
        ]);
        
        $model = $request->all();

        if (!$request->s_id) {

            return back()->with('flash_error', tr('something_error'));

        }

        $subscription = Subscription::find($request->s_id);

        if(!count($subscription)) {
            return redirect(route('user.dashboard'))->with('flash_error', tr('no_subscription_found'));
        }


        return view('user.invoice')->with('page', 'invoice')->with('subPage', 'invoice')->with('model', $model)->with('subscription',$subscription)
            ->with('model',$model);
    
    }

    /**
     * Function Name : ppv_invoice()
     *
     * @uses To Display ppv invoice page based on video id
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param integer $id - video id
     *
     * @return json response details
     */
    public function ppv_invoice($id) {
       
        $video = VideoTape::find($id);

        if ($video) {

            if (Auth::check()) {

                $video->video_tape_id = $video->id;

                $ppv_status = VideoRepo::pay_per_views_status_check(Auth::user()->id, Auth::user()->user_type, $video)->getData();

                if ($ppv_status->success) {

                    return redirect()->route('user.single', $video->video_tape_id);
                }

            }

            return view('user.ppv_invoice')
                ->with('page', 'ppv-invoice')
                ->with('video',$video)
                ->with('subPage', 'ppv-invoice');
                
        } else {

            return back()->with('flash_error', tr('video_not_found'));
        }
    
    }

    /**
     * Function Name : pay_per_view()
     *
     * @uses To Display ppv video page
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param Object $request - video with user Details
     *
     * @return json response details
     */
    public function pay_per_view($id) {

        $video = VideoTape::find($id);

        if(!$video) {


            return back()->with('flash_error', tr('video_not_found'));

        }
        return view('user.pay_per_view')
                ->with('page', 'pay_per_view')
                ->with('subPage', 'pay_per_view')->with('video', $video);

    }

    /**
     * Function Name: payper_videos()
     * To load all the paper views
     *
     * @return view page
     */
    public function payper_videos(Request $request) {
        // Get Logged in user id
        $id = Auth::user()->id;

        $request->request->add([ 
            'id'=>\Auth::user()->id,
            'age' => \Auth::user()->age_limit,
        ]);  

        $model = $this->UserAPI->pay_per_videos($request)->getData();

        // Return the view page
        return view('user.payperview')->with('model' , $model)
                        ->with('page' , 'Profile')
                        ->with('subPage' , 'Payper Videos');
    }

    /**
     * Function Name : payment_type()
     *
     * @uses To Check whether the user is going to pay through paypal / stripe payment (For PPV)
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param Object $request - User Details
     *
     * @return json response details
     */
    public function payment_type($id, Request $request) {
       
        if($request->payment_type == 1) {
          
            return redirect(route('user.ppv-video-payment', ['id' => $id, 'coupon_code' => $request->coupon_code]));

        } else {

            return redirect(route('user.card.ppv-stripe-payment', ['video_tape_id' => $id, 'coupon_code' => $request->coupon_code]));
        }
   
    }

    /**
     * Function Name : subscription_payment()
     *
     * @uses To Check whether the user is going to pay through paypal / stripe payment (For subscription)
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param Object $request - User Details
     *
     * @return json response details
     */
    public function subscription_payment(Request $request) {

        if($request->payment_type == 1) {

            return redirect(route('user.paypal' ,['subscription_id' => $request->s_id, 'coupon_code'=>$request->coupon_code]));

        } else {

            return redirect(route('user.card.stripe_payment' , ['subscription_id' => $request->s_id, 'coupon_code'=>$request->coupon_code]));
        }
    
    }

    /**
     * Function Name : ppv_stripe_payment()
     *
     * @uses To Pay PPV amount through stripe payment gateway
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param Object $request - User Details
     *
     * @return json response details
     */
    public function ppv_stripe_payment(Request $request) {

        $request->request->add([
            'id'=>Auth::user()->id,
            ]);

        $payment = $this->UserAPI->stripe_ppv($request)->getData();


        if ($payment->success) {

            return redirect(route('user.video.success',$request->video_tape_id))->with('flash_success', $payment->message);

        } else {


            if ($payment->error_code == 901) {

                return back()->with('flash_error', $payment->error_messages.'. '.tr('default_card_add_message').'  <a href='.route('user.card.card_details', ['v_id'=>$request->video_tape_id]).'>'.tr('add_card').'</a>');

            }

            return back()->with('flash_error', $payment->error_messages);
        }
    
    }

    /**
     * Function Name : payment_success()
     *
     * @uses To displaye subscription success message
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param Object $request - User Details
     *
     * @return json response details
     */
    public function payment_success() {

        return view('user.subscription');
    }

    /**
     * Function Name : video_success()
     *
     * @uses To displaye video success messae
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param Object $request - User Details
     *
     * @return json response details
     */
    public function video_success($id = "") {

        if(!$id) {
            return redirect()->to('/')->with('flash_error' , tr('something_error'));
        }

        return view('user.video_subscription')->with('id', $id);
    
    }

    /**
     * Function Name : save_video_payment
     *
     * @uses To save the payment details
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param integer $id Video Id
     *
     * @param object  $request Object (Post Attributes)
     *
     * @return flash message
     */
    public function save_video_payment($id, Request $request) {

        // Load Video Model
        $model = VideoTape::find($id);

        // Get post attribute values and save the values
        if ($model) {

            $request->request->add([ 
                'ppv_created_by'=> Auth::user()->id ,
                'is_pay_per_view'=>PPV_ENABLED
            ]); 

            if ($data = $request->all()) {

                // Update the post
                if (VideoTape::where('id', $id)->update($data)) {
                    // Redirect into particular value
                    return back()->with('flash_success', tr('payment_added'));       
                } 
            }
        }
        
        return back()->with('flash_error', tr('admin_published_video_failure'));
    }

    /**
     * Function Name : remove_payper_view()
     *
     * @uses To remove pay per view
     * 
     * @created Vithya R
     *
     * @updated 
     *
     * @return falsh success
     */
    public function remove_payper_view($id) {
        
        // Load video model using auto increment id of the table
        $model = VideoTape::find($id);
        if ($model) {
            $model->ppv_amount = 0;
            $model->is_pay_per_view = PPV_DISABLED;
            $model->type_of_subscription = 0;
            $model->type_of_user = 0;
            $model->save();
            if ($model) {
                return back()->with('flash_success' , tr('removed_pay_per_view'));
            }
        }
        return back()->with('flash_error' , tr('admin_published_video_failure'));
    
    }

    /**
     * Function Name : my_channels()
     *
     * @uses To list out channels based on logged in users
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param Object $request - User Details
     *
     * @return json response details
     */
    public function my_channels(Request $request) {

        $request->request->add([
            'id'=>Auth::user()->id,
        ]);

        $response = $this->UserAPI->user_channel_list($request)->getData();

        return view('user.channels.list')->with('page', 'my_channel')
                ->with('subPage', 'channel_list')
                ->with('response', $response);
    }


    /**
     * Function Name : forgot_password()
     *
     * @uses To send password to the requested users
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param Object $request - User Details
     *
     * @return json response details
     */
    public function forgot_password(Request $request) {

        $response = $this->UserAPI->forgot_password($request)->getData();

        if ($response->success) {

            return back()->with('flash_success', $response->message);

        } else {

            return back()->with('flash_error', $response->error_messages);

        }
    }

    /**
     * Function Name : subscription_history()
     *
     * @uses To list out subscribed history based on id
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param Object $request - User Details
     *
     * @return json response details
     */
    public function subscription_history(Request $request) {

        $request->request->add([ 
            'id'=>Auth::user()->id,
            'token'=>Auth::user()->token,
            'device_type'=>DEVICE_WEB,
        ]); 

        $response = $this->UserAPI->subscribedPlans($request)->getData();

        if ($response->success) {

            return view('user.history.subscription_history')->with('page', 'history')
                ->with('subPage', 'subscription_history')
                ->with('response', $response);

        } else {

            return back()->with('flash_error', $response->error_messages);

        }

    }

    /**
     * Function Name : ppv_history()
     *
     * @uses To list out ppv history based on id
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param Object $request - User Details
     *
     * @return json response details
     */
    public function ppv_history(Request $request) {

        $request->request->add([ 
            'id'=>Auth::user()->id,
            'token'=>Auth::user()->token,
            'device_type'=>DEVICE_WEB,
        ]); 

        $response = $this->UserAPI->ppv_list($request)->getData();

        if ($response->success) {

            return view('user.history.ppv_history')->with('page', 'history')
                ->with('subPage', 'ppv_history')
                ->with('response', $response);

        } else {

            return back()->with('flash_error', $response->error_messages);

        }

    }


    /**
     * Function Name : tags_videos()
     *
     * @uses To list out tags videos based on tag id
     * 
     * @created Vithya 
     *
     * @updated
     *
     * @param integer $request->id - Category Id
     *
     * @return response of success/failure message
     */
    public function tags_videos(Request $request) {

        $tag = Tag::find($request->id);

        if ($tag) {

            if (Auth::check()) {

                $request->request->add([ 
                    'tag_id'=>$request->id,
                    'id' => \Auth::user()->id,
                    'token' => \Auth::user()->token,
                    'device_token' => \Auth::user()->device_token,
                    'age'=>\Auth::user()->age_limit,
                    'device_type'=>DEVICE_WEB
                ]);
            } else {

                $request->request->add([ 
                    'tag_id'=>$request->id,
                    'device_type'=>DEVICE_WEB
                ]);
            }

            $data = $this->UserAPI->tags_videos($request)->getData();


            if($data->success) {

                return view('user.tags.tags_videos')->with('page', 'tag_name'.$tag->id)
                                        ->with('videos',$data)
                                        ->with('tag', $tag);

            } else {

                return back()->with('flash_error', $data->error_messages);

            }
        } else {

            return back()->with('flash_error', tr('tag_not_found'));

        }
    }

   /**
    * Function Name : subscriptions_autorenewal_enable
    *
    * @uses To enable automatic subscription
    *
    * @created Vithya
    *
    * @updated -
    *
    * @param object $request - USer details & payment details
    *
    * @return boolean response with message
    */
    public function subscriptions_autorenewal_enable(Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token,
            'device_type'=>DEVICE_WEB
        ]);

        $response = $this->UserAPI->autorenewal_enable($request)->getData();

        if ($response->success) {

            return back()->with('flash_success', $response->message);

        } else {

            return back()->with('flash_error', $response->error_messages);
        }

    }

   /**
    * Function Name : subscriptions_autorenewal_pause
    *
    * @uses To cancel automatic subscription
    *
    * @created Vithya
    *
    * @updated -
    *
    * @param object $request - USer details & payment details
    *
    * @return boolean response with message
    */
    public function subscriptions_autorenewal_pause(Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token,
            'device_type'=>DEVICE_WEB
        ]);

        $response = $this->UserAPI->autorenewal_cancel($request)->getData();

        if ($response->success) {

            return back()->with('flash_success', $response->message);

        } else {

            return back()->with('flash_error', $response->error_messages);

        }

    }


   /**
    * Function Name : categories_view()
    *
    * @uses category details based on id
    *
    * @created Vithya R
    *
    * @updated
    *
    * @param - 
    * 
    * @return response of json
    */
    public function categories_view($id, Request $request) {
        
        $request->request->add([ 
            'category_id'=>$id,
            'id' => \Auth::check() ? \Auth::user()->id : '',
            'token' => \Auth::check() ? \Auth::user()->token : '',
            'device_token' => \Auth::check() ? \Auth::user()->device_token : '',
            'device_type'=>DEVICE_ANDROID
        ]);

        $category = Category::where('unique_id', $request->category_id)->first();

        if ($category) {

             $request->request->add([ 
                'category_id'=>$category->id,
            ]);


        } else {

            return back()->with('flash_error', tr('category_not_found'));

        }

        $response = $this->UserAPI->categories_view($request)->getData();

        if ($response->success) {

            $category = $response->category;

            $videos = $response->category_videos;

            $channels = $response->channels_list;

            return view('user.categories.view')
                        ->with('page' , 'categories_'.$request->category_id)
                        ->with('subPage' , 'categories')
                        ->with('category' , $category)
                        ->with('videos', $videos)
                        ->with('channels', $channels);

        } else {

            return back()->with('flash_error', $response->error_messages);

        }
    
    }

    /**
     * Function Name : categories_videos()
     *
     * @uses To display based on category
     *
     * @created Vithya R
     *
     * @updated
     *
     * @param object $request - User Details
     *
     * @return Response of videos list
     */
    public function categories_videos(Request $request) {
        
        $request->request->add([ 
            'id' => \Auth::check() ? \Auth::user()->id : '',
            'token' => \Auth::check() ? \Auth::user()->token : '',
            'device_token' => \Auth::check() ? \Auth::user()->device_token : '',
            'device_type'=>DEVICE_ANDROID
        ]);

        $response = $this->UserAPI->categories_videos($request)->getData();

        if ($response->success) {

            $view = View::make('user.categories.videos')
                    ->with('videos',$response->data)
                    ->render();

            return response()->json(['success'=>true, 'view'=>$view]);

        } else {

            return response()->json(['success'=>false, 'data'=>$response->error_messages]);

        }

    } 

    /**
     * Function Name : categories_channels
     *
     * @uses To list out all the channels which is in active status
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param Object $request - USer Details
     *
     * @return array of channel list
     */
    public function categories_channels(Request $request) {

        $request->request->add([ 
            'id' => \Auth::check() ? \Auth::user()->id : '',
            'token' => \Auth::check() ? \Auth::user()->token : '',
            'device_token' => \Auth::check() ? \Auth::user()->device_token : '',
            'device_type'=>DEVICE_ANDROID
        ]);

        $response = $this->UserAPI->categories_channels_list($request)->getData();

        if ($response->success) {

            $view = View::make('user.categories.channels')
                    ->with('channels',$response->data)
                    ->render();

            return response()->json(['success'=>true, 'view'=>$view]);

        } else {

            return response()->json(['success'=>false, 'data'=>$response->error_messages]);

        }

    }   

    /**
     *
     * Function : custom_live_videos()
     *
     * @uses return list of live videos created by admin
     *
     * @created Vithya
     *
     * @updated 
     *
     * @return list page for live videos
     */

    public function custom_live_videos(Request $request) {

        $request->request->add([
            'paginate' => 1
        ]);

        $response = $this->UserAPI->custom_live_videos($request)->getData();

        // dd($response->live);

        return view('user.custom_live_videos.index')->with('page', 'custom_live_videos')
                ->with('subPage', 'custom_live_videos')
                ->with('data', isset($response->live) ? $response->live : []);

    }

    /**
     *
     * Function : custom_live_videos_view()
     *
     * @uses return view details of live video
     *
     * @created Vithya
     *
     * @updated 
     *
     * @return view page for selected live video
     */
    public function custom_live_videos_view($id = "" , Request $request) {

        $request->request->add([
            'custom_live_video_id'=> $id,
        ]);

        $response = $this->UserAPI->custom_live_videos_view($request)->getData();

        if(!$response->success) {
            return redirect()->to('/')->with('flash_error' , "Details not found");
        } 

        return view('user.custom_live_videos.view')->with('page', 'custom_live_videos')
                ->with('subPage', 'custom_live_videos')
                ->with('suggestions', isset($response->suggestions) ? $response->suggestions : [])
                ->with('video', isset($response->model) ? $response->model : []);

    }


    /**
     *
     * Function : settings()
     *
     * @uses Display all the portion of the logged in user
     *
     * @created Vithya
     *
     * @updated 
     *
     * @return list of options
     */
    public function settings(Request $request) {

        return view('user.settings')
                ->with('page', 'settings')
                ->with('subPage', '');
    }


    /**
     * Function Name : live_videos_invoice()
     *
     * To view the live video invoice page
     *
     * @created_by shobana
     *
     * @updated by --
     *
     * @param integer $request - video id 
     *
     * @return response of json
     */
    public function live_videos_invoice(Request $request){

        $video = LiveVideo::find($request->id);

        if ($video) {

            if (Auth::check()) {

               $live_video_payment =  LiveVideoPayment::where('live_video_id', $video->id)
                                ->where('live_video_viewer_id', Auth::user()->id)
                                ->where('status',DEFAULT_TRUE)->first();

                if ($live_video_payment) {

                    return redirect(route('user.single', $video->video_tape_id));

                } 

            }

            return view('user.live-videos.invoice')
                ->with('page', 'live-video-invoice')
                ->with('video',$video)
                ->with('subPage', 'live-video-invoice');
                
        } else {

            return back()->with('flash_error', tr('video_not_found'));
        }

    }

    /**
     * Function Name : bell_notifications()
     *
     * @uses list of notifications for user
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param integer $id
     *
     * @return JSON Response / View Page
     */

    public function bell_notifications(Request $request) {

        try {

            $request->request->add([
                'id'=> Auth::user()->id,
                'token'=> Auth::user()->token
            ]);

            $response = $this->UserAPI->bell_notifications($request)->getData();

            if($response->success == false) {

                throw new Exception($response->error_messages, $response->error_code);
            }


            if($request->is_json) {

                return response()->json($response, 200);

            }

            $notifications = $response->data;

            foreach ($notifications as $key => $notification_details) {

                $notification_redirect_url = route('user.single', $notification_details->video_tape_id);

                if($notification_details->notification_type == BELL_NOTIFICATION_NEW_SUBSCRIBER) {
                    
                    $notification_redirect_url = route('user.channel', $notification_details->channel_id);

                }
                
                $notification_details->notification_redirect_url = $notification_redirect_url;
                
            }

            return view('user.notifications.index')->with('notifications', $notifications);

        } catch(Exception $e) {

            $error_messages = $e->getMessage(); $error_code = $e->getCode();

            $response_array = ['success' => false, 'error_messages' => $error_messages, 'error_code' => $error_code];

            if($request->is_json) {

                return response()->json($response_array);
            }

            return redirect()->to('/')->with('flash_error' , $error_messages);

        }

    } 

    /**
     * Function Name : bell_notifications_update()
     *
     * @uses list of notifications for user
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param integer $id
     *
     * @return JSON Response
     */

    public function bell_notifications_update(Request $request) {

    }  

    /**
     * Function Name : bell_notifications_count()
     * 
     * @uses Get the notification count
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param object $request - As of no attribute
     * 
     * @return response of boolean
     */
    public function bell_notifications_count(Request $request) {

        try {

            $request->request->add([
                'id'=> Auth::user()->id,
                'token'=> Auth::user()->token
            ]);

            $response = $this->UserAPI->bell_notifications_count($request)->getData();

            if($response->success == false) {

                throw new Exception($response->error_messages, $response->error_code);
            }

            return response()->json($response, 200);

        } catch(Exception $e) {

            $error_messages = $e->getMessage(); $error_code = $e->getCode();

            $response_array = ['success' => false, 'error_messages' => $error_messages, 'error_code' => $error_code];

            return response()->json($response_array);

            // return redirect()->to('/')->with('flash_error' , $error_messages);

        }

    }  

    /**
     *
     * Function name: playlists()
     *
     * @uses get the playlists
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param integer channel_id (Optional)
     *
     * @return JSON Response
     */
    public function playlists(Request $request) {

        try {

            $request->request->add([
                
                'id'=> Auth::user()->id,
                'token'=> Auth::user()->token,
                'view_type' => \Auth::check() ? VIEW_TYPE_OWNER : VIEW_TYPE_VIEWER 

            ]);

            $response = $this->NewUserAPI->playlists($request)->getData();

            if($response->success == false) {

                throw new Exception($response->error_messages, $response->error_code);
            }

            if($request->is_json) {

                return response()->json($response, 200);
            }

            $playlists = $response->data;

            return view('user.playlists.index')->with('playlists', $playlists)->with('playlist_type', PLAYLIST_TYPE_USER);

        } catch(Exception $e) {

            $error_messages = $e->getMessage(); $error_code = $e->getCode();

            $response_array = ['success' => false, 'error_messages' => $error_messages, 'error_code' => $error_code];

            if($request->is_json) {

                return response()->json($response_array);
            }

            return redirect()->to('/')->with('flash_error' , $error_messages);

        }

    } 

    /**
     *
     * Function name: channel_playlists_save()
     *
     * @uses get the playlists
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param integer channel_id (Optional)
     *
     * @return JSON Response
     */
    public function channel_playlists_save(Request $request) {
        
        try {
            
            DB::beginTransaction();

            $request->request->add([
                'id'=> Auth::user()->id,
                'token'=> Auth::user()->token, 
            ]); 
            
            $request->request->add([
                'playlist_type'=> $request->playlist_type ?: PLAYLIST_TYPE_USER,
                'playlist_display_type'=> $request->playlist_display_type ?: PLAYLIST_DISPLAY_PRIVATE
            ]);

            $response = $this->NewUserAPI->playlists_save($request)->getData();

            if($response->success) {

                $response->playlist_id = $response->data->playlist_id;

                $playlist_details = $response->data;

                $response->title = $response->data->title;

                $new_playlist_content = '';

                if(!empty($request->video_tapes_id)) {
                    // Remove unselected videos from playlists

                    PlaylistVideo::where('playlist_id', $response->playlist_id)->whereNotIn('video_tape_id', $request->video_tapes_id)
                                    ->where('user_id', $request->id)
                                    ->delete();

                    foreach ($request->video_tapes_id as $key => $video_tape_id) {

                        // Check the video already added in playlist

                        $check_video = PlaylistVideo::where('video_tape_id', $video_tape_id)->where('playlist_id', $response->playlist_id)->count();

                        if(!$check_video) {

                            $playlist_video_details = new PlaylistVideo;

                            $playlist_video_details->playlist_id = $response->playlist_id;

                            $playlist_video_details->video_tape_id = $video_tape_id;
                            
                            $playlist_video_details->user_id = $request->id;

                            $playlist_video_details->status = DEFAULT_TRUE;
                            
                            $playlist_video_details->save();

                        }
                    }

                    $response->data->total_videos =PlaylistVideo::where('playlist_id',$playlist_details->playlist_id)->count();

                    $first_video_from_playlist= PlaylistVideo::where('playlist_videos.playlist_id', $playlist_details->playlist_id)
                                                ->leftJoin('video_tapes', 'video_tapes.id', '=', 'playlist_videos.video_tape_id')
                                                ->select('video_tapes.id as video_tape_id', 'video_tapes.default_image as picture')
                                                ->first();

                    $response->data->picture = $first_video_from_playlist ? $first_video_from_playlist->picture : asset('images/playlist.png');

                    $new_playlist_content = view('user.channels.playlist_append')->with('channel_playlist_details', $response->data)->render();

                    $response->new_playlist_content = $new_playlist_content;

                }

                DB::commit();

                return response()->json($response);   

            }

            throw new Exception($response->error, $response->error_code);

        } catch(Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            $error_code = $e->getCode();

            $response = ['success' => false, 'error' => $error, 'error_code' => $error_code];
       
            return response()->json($response);

        }
    
    }

    /**
     *
     * Function name: playlists_view()
     *
     * @uses get the playlists
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param integer 
     *
     * @return 
     *
     */
    public function playlists_view(Request $request) {

        try {

            if (Auth::check()) {

                $request->request->add([ 
                    'id'=>Auth::user()->id,
                    'token'=> Auth::user()->token
                ]);
            } 

            $response = $this->NewUserAPI->playlists_view($request)->getData();
         
            if($response->success == false) {

                throw new Exception($response->error_messages, $response->error_code);
            }

            $playlist_details = $response->data;

            $user_details = User::find($playlist_details->user_id);

            if(!$user_details) {
                
                return back()->with('flash_error', tr('user_not_found'));
            }

            $playlist_details->user_name = $user_details->name;
           
            $playlist_details->user_picture = $user_details->picture;
            
            if($request->is_json) {

                $view = \View::make('user.playlists.playlists')
                    ->with('video_tapes',$response->data->video_tapes)
                    ->with('playlist_details',$playlist_details)
                    ->render();
           
                return response()->json(['success'=>true, 'view'=>$view, 'count' =>count($response->data->video_tapes)]);
            }

            $video_tapes = $response->data->video_tapes;

            $channel_videos = [];
      
            $playlist_video_ids = array_column($video_tapes , 'video_tape_id');
            
            // if ($playlist_details->playlist_type == PLAYLIST_TYPE_CHANNEL) {
                
                // if($playlist_details->channel_id) { 

                    $channel_videos = $this->UserAPI->channel_videos($playlist_details->channel_id, 0 , $request)->getData();
                   
                    if(!empty($channel_videos)) {
                       
                        foreach ($channel_videos as $value) {
                            
                            $value->exist_in_playlists = NO;      

                            if(in_array( $value->video_tape_id, $playlist_video_ids )) {
                            
                                $value->exist_in_playlists = YES;                      
                            }
                        }
                    }

                // }

            // }

            return view('user.playlists.view')
                    ->with('playlist_details', $playlist_details)
                    ->with('video_tapes', $video_tapes)
                    ->with('playlist_type', $request->playlist_type)
                    ->with('videos', $channel_videos);

        } catch(Exception $e) {

            $error_messages = $e->getMessage(); $error_code = $e->getCode();

            $response_array = ['success' => false, 'error_messages' => $error_messages, 'error_code' => $error_code];

            if($request->is_json) {

                return response()->json($response_array);
            }

            return redirect()->to('/')->with('flash_error' , $error_messages);
        }

    }    

    /**
     *
     * Function name: playlists_view()
     *
     * @uses get the playlists
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param integer 
     *
     * @return 
     *
     */
    public function playlists_play_all(Request $request) {

        
        if (Auth::check()) {

            $request->request->add([ 
                'id'=>Auth::user()->id,
                'age_limit'=>Auth::user()->age_limit,
            ]);

        } else {

            $request->request->add([ 
                'id'=> '',
            ]);
        }
        
        // For default assign the play_next index as 0
        $play_next = $request->play_next ?? 0;
        
        // Load all the videos based on playlist_id and playlist_type
        $play_all = $this->NewUserAPI->playlists_view($request)->getData();
        
        if($request->is_json && $play_all->success) {

            $view = \View::make('user.videos._playlist')
                    ->with('play_all',$play_all->data)
                    ->render();
           
            return response()->json(['success'=>true, 'view'=>$view, 'count' =>count($play_all->data)]);
        }

        $video_tapes = $play_all->data->video_tapes;
        
        // total videos count is greater than the play_next count reset the value to zero.
        $value = 0;
        if((count($video_tapes)) == $request->play_next)
        { 
            $value = 1;
            $play_next = 0;
        }
        
        // Load the video based on play_next index.
        $request->request->add([ 
            'video_tape_id' => $video_tapes[$play_next]->video_tape_id,
        ]);

        // Increment the play_next count - For playing the next video continuouly.
        if($value == 0) {
            $play_next++;
        }
        
        $data = $this->UserAPI->video_detail($request)->getData();

        // video url
        if (isset($data->url)) {

            return redirect($data->url);
        }
        
        if ($data->success) {

            $response = $data->response_array;
            
            // Video is autoplaying ,so we are incrementing the watch count 
            if ($request->id != $response->video->channel_created_by) {

                $user_id = Auth::check() ? Auth::user()->id : 0;

                VideoRepo::watch_count($request->video_tape_id,$user_id,YES);

            }

            return view('user.videos.play_all')
                        ->with('page' , '')
                        ->with('subPage' , '')
                        ->with('video' , $response->video)
                        ->with('comments' , $response->comments)
                        ->with('suggestions',$response->suggestions)
                        ->with('wishlist_status' , $response->wishlist_status)
                        ->with('history_status' , $response->history_status)
                        ->with('main_video' , $response->main_video)
                        ->with('url' , $response->main_video)
                        ->with('channels' , $response->channels)
                        ->with('report_video', $response->report_video)
                        ->with('videoPath', $response->videoPath)
                        ->with('video_pixels', $response->video_pixels)
                        ->with('videoStreamUrl', $response->videoStreamUrl)
                        ->with('hls_video' , $response->hls_video)
                        ->with('flaggedVideo', $response->flaggedVideo)
                        ->with('ads', $response->ads)
                        ->with('subscribe_status', $response->subscribe_status)
                        ->with('like_count',$response->like_count)
                        ->with('dislike_count',$response->dislike_count)
                        ->with('like_status',$response->like_status)
                        ->with('dislike_status',$response->dislike_status)
                        ->with('subscriberscnt', $response->subscriberscnt)
                        ->with('comment_rating_status', $response->comment_rating_status)
                        ->with('embed_link', $response->embed_link)
                        ->with('tags', $response->tags)
                        ->with('play_all', $play_all->data)
                        ->with('play_next', $play_next);
       
        } 
       
        $error_message = isset($data->error_messages) ? $data->error_messages : tr('something_error');

        return redirect()->back()->with('flash_error', $error_message);

    }    

    /**
     *
     * Function name: playlists_add_video()
     *
     * @uses get the playlists
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param integer channel_id (Optional)
     *
     * @return JSON Response
     */
    public function playlists_video_status(Request $request) {

        try {

            DB::beginTransaction();

            $playlist_video_details = PlaylistVideo::where('video_tape_id', $request->video_tape_id)
                                        ->where('user_id', $request->id)
                                        ->first();
           
            // if($playlist_video_details) {

            //     $message = Helper::get_message(127); $code = 127;

            //     $playlist_video_details->delete();

            // } else {

                $validator = Validator::make($request->all(),[
                    'playlist_id' => 'required',
                    'video_tape_id' => 'required|exists:video_tapes,id,status,'.APPROVED,
                ]);

                if($validator->fails()) {

                    $error_messages = implode(',',$validator->messages()->all());

                    throw new Exception($error_messages, 101);
                    
                }

                $playlist_ids = explode(',', $request->playlist_id);

                PlaylistVideo::whereNotIn('playlist_id', $playlist_ids)->where('video_tape_id', $request->video_tape_id)
                                ->where('user_id', $request->id)
                                ->delete();

                $total_playlists_update = 0;

                foreach ($playlist_ids as $key => $playlist_id) {

                    // Check the playlist id belongs to the logged user

                    $playlist_details = Playlist::where('id', $playlist_id)->where('user_id', $request->id)->count();

                    if($playlist_details) {

                        $playlist_video_details = PlaylistVideo::where('video_tape_id', $request->video_tape_id)
                                            ->where('user_id', $request->id)
                                            ->where('playlist_id', $playlist_id)
                                            ->first();
                        if(!$playlist_video_details) {

                            $playlist_video_details = new PlaylistVideo;
     
                        }

                        $playlist_video_details->user_id = $request->id;

                        $playlist_video_details->playlist_id = $playlist_id;

                        $playlist_video_details->video_tape_id = $request->video_tape_id;

                        $playlist_video_details->status = APPROVED;

                        $playlist_video_details->save();

                        $total_playlists_update++;

                    }
                
                }

            // }

            DB::commit();

            $code = $total_playlists_update > 0 ? 126 : 132;

            $message = Helper::get_message($code);

            $response_array = ['success' => true, 'message' => $message, 'code' => $code];

            return response()->json($response_array);

        } catch(Exception $e) {

            DB::rollback();

            $error_messages = $e->getMessage();

            $error_code = $e->getCode();

            $response_array = ['success' => false, 'error_messages' => $error_messages, 'error_code' => $error_code];

            return response()->json($response_array);

        }
    
    }

    /**
     *
     * Function name: playlists_video_remove()
     *
     * @uses Remove the video from playlist
     *
     * @created Aravinth R
     *
     * @updated vithya R
     *
     * @param integer video_tape_id (Optional)
     *
     * @return JSON Response
     */

    public function playlists_video_remove(Request $request) {

        try {

            $request->request->add([
                'id'=> Auth::user()->id,
                'token'=> Auth::user()->token,
                'playlist_id' => $request->playlist_id,
                'video_tape_id' => $request->video_tape_id
            ]);

            $response = $this->UserAPI->playlists_video_remove($request)->getData();

            if($response->success == false) {

                throw new Exception($response->error_messages, $response->error_code);
            }


            if($request->is_json) {

                return response()->json($response, 200);

            }

           return back()->with('flash_success', $response->message);

            return view('user.playlists.videos')->with('playlist_details', $playlist_details)->with('video_tapes', $video_tapes);

        } catch(Exception $e) {

            $error_messages = $e->getMessage(); $error_code = $e->getCode();

            $response_array = ['success' => false, 'error_messages' => $error_messages, 'error_code' => $error_code];

            if($request->is_json) {

                return response()->json($response_array);
            }

            return redirect()->to('/')->with('flash_error' , $error_messages);

        }
    
    }

    /**
     * Function Name : playlists_delete()
     *
     * @uses used to delete the user selected playlist
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param integer $playlist_id
     *
     * @return JSON Response
     */
    public function playlists_delete(Request $request) {

        try {

            $request->request->add([
                'id'=> Auth::user()->id,
                'token'=> Auth::user()->token,
                'playlist_id' => $request->playlist_id,
                'video_tape_id' => $request->video_tape_id ?: ''
            ]);

            $response = $this->UserAPI->playlists_delete($request)->getData();

            if($response->success == false) {

                throw new Exception($response->error_messages, $response->error_code);
            }


            if($request->is_json) {

                return response()->json($response, 200);

            }

           return back()->with('flash_success', $response->message);

        } catch(Exception $e) {

            $error_messages = $e->getMessage(); $error_code = $e->getCode();

            $response_array = ['success' => false, 'error_messages' => $error_messages, 'error_code' => $error_code];

            if($request->is_json) {

                return response()->json($response_array);
            }

            return redirect()->to('/')->with('flash_error' , $error_messages);

        }

    }

    /**
     * @method playlist_video_update
     *
     * @uses To add video to playlist 
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - Video id, playlist id
     * 
     * @return success/failure message
     *
     */
    public function playlist_video_update(Request $request)  {
        
        $request->request->add([
            'id' => Auth::user()->id,
            'token'=>Auth::user()->token
        ]);

        if($request->status == DEFAULT_TRUE)  {
            
            $response = $this->NewUserAPI->playlists_video_status($request)->getData();                  
        } 

        if($request->status == DEFAULT_FALSE) {

            $response = $this->NewUserAPI->playlists_video_remove($request)->getData();        
        }
      
        return response()->json($response);
    }


    /**
     *
     * @method playlist_save_video_add()
     *
     * @uses to save playlist and add video in playlist
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param request title, privacy( )
     *
     * @return JSON Response
     */
    public function playlist_save_video_add(Request $request) {

        try {
        
            $request->request->add([
                'id'=> Auth::user()->id,
                'token'=> Auth::user()->token
            ]);

            
            $playlists_response = $this->NewUserAPI->playlists_save($request)->getData();
            
            $request->request->add([
                'playlist_id'=> $playlists_response->data->playlist_id,
            ]);
            
            $response = $this->NewUserAPI->playlists_video_status($request)->getData();
            
            if($response->success) {

                $response->playlist_id = $playlists_response->data->playlist_id;

                $response->title = $playlists_response->data->title;
            
                Log::info("playlists_video_response ".print_r($response, true));

                return response()->json($response);   

            } else {

                throw new Exception($response->error_messages, $response->error_code);
            }
            
        } catch(Exception $e) {

            $error = $e->getMessage();

            $error_code = $e->getCode();

            $response = ['success' => false, 'error_messages' => $error, 'error_code' => $error_code];
       
            return response()->json($response);

        }

    }

    /**
     * Function Name : referral_code_signup()
     *
     * @uses signup user through referrals
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param string referral_code 
     *
     * @return redirect signup page
     */
    public function referrals_signup($referral_code){

        try {

            if(!$referral_code) {

                throw new Exception(tr('referral_code_invalid'), 101);
            }

            $check_referral_code =  UserReferrer::where('referral_code', $referral_code)->where('status', DEFAULT_TRUE)->first();

            if(!$check_referral_code) {

                throw new Exception(tr('referral_code_invalid'), 101);
            }

            $user_details = User::where('status', USER_APPROVED)->where('id', $check_referral_code->user_id)->first();

            if(!$user_details) {

                throw new Exception(tr('referral_code_invalid'), 101);
            }

            return redirect()->route('user.register.form', ['referral' => $referral_code]);

        } catch(Exception $e) {

            $error = $e->getMessage();

            $error_code = $e->getCode();

            return redirect(route('user.register.form'))->with('flash_error', $error);
        }

    }

    /**
     * Function Name : referrals()
     *
     * @uses signup user through referrals
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param string referral_code 
     *
     * @return redirect signup page
     */
    public function referrals(Request $request) {

        try {

            $user_details =  Auth::user();

            $user_referrer_details = UserReferrer::where('user_id', $user_details->id)->first();

            if(!$user_referrer_details) {

                $user_referrer_details = new UserReferrer;

                $user_referrer_details->user_id = $user_details->id;

                $user_referrer_details->referral_code = uniqid();

                $user_referrer_details->save();

            }

            $referrals = Referral::where('parent_user_id', $user_details->id)->orderBy('created_at', 'desc')->get();
            
            foreach ($referrals as $key => $referral_details) {
                
                $referral_user_details = $referral_details->userDetails;
                
                if($referral_user_details = $referral_details->userDetails) {

                    $referral_details->username = $referral_user_details->name ? : "";

                    $referral_details->picture = $referral_user_details->picture ? : "";

                }            
            }

            return view('user.referrals.index')
                    ->with('referrals', $referrals)
                    ->with('user_referrer_details', $user_referrer_details);

        } catch(Exception $e) {

            $error = $e->getMessage();

            $error_code = $e->getCode();

            return redirect()->back()->with('flash_error', $error);
        }

    }

    /**
     * Function Name: referrals_view()
     *
     * @uses get the subscription & PPV details for selected referral user
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param integer $user_id
     *
     * @return response of success / failure message.
     */

    public function referrals_view(Request $request) {
        
        $user_details = User::find($request->user_id);

        if(!$user_details) {
            
            return back()->with('flash_error', tr('user_not_found'));
        }

        $user_referrer_details = UserReferrer::where('user_id', $request->parent_user_id)->first();

        return view('user.referrals.view')
                    ->with('page', 'users')
                    ->with('sub_page', 'view-user')
                    ->with('user_details', $user_details)
                    ->with('user_referrer_details', $user_referrer_details);

    } 

        /**
     * Function Name : update_paypal_email() 
     *
     * @uses Update Paypal Email.
     * 
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param object $request - User Details
     *
     * @return \Illuminate\Http\Response
     */
    public function update_paypal_email(Request $request) {

        try{
            $request->request->add([ 
                'id' => \Auth::user()->id,
                'token' => \Auth::user()->token,
                'device_token' => \Auth::user()->device_token,
            ]);
            
            $validator = Validator::make($request->all(),array(
                    'paypal_email' => 'required|max:255',
            ));

            if ($validator->fails()) {
                // Error messages added in response for debugging
                $error_messages = implode(',',$validator->messages()->all());

                throw new Exception($error_messages, 101);
                
            } 

            if($user = User::find($request->id)) {
                
                $user->paypal_email = $request->paypal_email ? $request->paypal_email : $user->paypal_email;

                if($user->save()) {

                    return back()->with('flash_success' , tr('paypal_email_updated'));

                }
                
            } else {

                throw new Exception(tr('user_details_not_saved'));
                        
            }

        }  catch(Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * Function Name : check_user_live_video() 
     *
     * @uses Check Live  Video Present for the user
     * 
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param object $request - User Details
     *
     * @return \Illuminate\Http\Response
     */
    public function check_user_live_video(Request $request) {

        $request->request->add([ 
            'id' => Auth::user()->id,
            'token'=>Auth::user()->token
        ]);

        $model = LiveVideo::where('user_id', $request->id)
                    ->where('status', DEFAULT_FALSE)
                    ->first();
        
        if(count($model) > 0) {

            return response()->json(['success'=>false, 'data'=>$model, 'error_messages'=>tr('video_call_already_present')]);

        } else {

            return response()->json(['success'=>true]);

        }

    }    /**
     * @method erase_old_live_videos()
     *
     * @uses used to delete the user selected playlist
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param integer $playlist_id
     *
     * @return JSON Response
     */
    public function erase_old_live_videos(Request $request) {

        try {

            $request->request->add([
                'id'=> Auth::user()->id,
                'token'=> Auth::user()->token,
            ]);

            $response = $this->UserAPI->erase_streaming($request)->getData();

            if($response->success == false) {

                throw new Exception($response->error_messages, $response->error_code);
            }

            return response()->json($response, 200);

        } catch(Exception $e) {

            $error_messages = $e->getMessage(); $error_code = $e->getCode();

            $response_array = ['success' => false, 'error_messages' => $error_messages, 'error_code' => $error_code];

            return response()->json($response_array);

        }

    }
//=================================
       public function getUID(Request $request)
	   {
        $request->request->add([
            'id'=>Auth::user()->id,
        ]);

        $response = $this->UserAPI->user_channel_list($request)->getData();
		if(count($response->channels) == 0)
		{
			return view('user.channels.list')->with('page', 'my_channel')
                ->with('subPage', 'channel_list')
                ->with('response', $response);
		}
		else{			
		  foreach($response->channels as $i => $channel)
		   {
			 $id = $channel->channel_id;
		   }
		  return redirect(route('user.video_upload', ['id'=>$id]));
		}
       }

       public function getR4D(Request $request)
	   {
            $request->request->add([
                'id'=>Auth::user()->id,
            ]);

            $response = $this->UserAPI->user_channel_list($request)->getData();
            if(count($response->channels) == 0)
            {
                return view('user.channels.list')->with('page', 'my_channel')
                    ->with('subPage', 'channel_list')
                    ->with('response', $response);
            }
            else{
            foreach($response->channels as $i => $channel)
            {
                $id = $channel->channel_id;
            }
            if(isset($request->tape_id)) {
                    $edit_video = VideoTape::find($request->tape_id);
                    return redirect(route('user.video_upload', 
                    ['id'=>$edit_video->channel_id,'tape_id'=>$request->tape_id,'video_type'=>VIDEO_TYPE_R4D]))->with('edit_video', $edit_video);
                }
                else
                    return redirect(route('user.video_upload', ['id'=>$id, 'video_type'=>VIDEO_TYPE_R4D]));
            }
       }

       // public function fin(Request $request)
	   // {
		   // echo $request->id;
       // }
       public function video_del(Request $request)
        {
            $filename =  $request->get('filename');
            ImageUpload::where('filename',$filename)->delete();
            $path=public_path().'/images/'.$filename;
            if (file_exists($path)) {
                unlink($path);
            }
            return $filename;  
        }

}