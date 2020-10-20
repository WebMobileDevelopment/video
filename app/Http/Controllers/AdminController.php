<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Jobs\sendPushNotification;

use App\Requests;

use App\Admin;

use App\Moderator;

use App\VideoTape;

use App\AdminVideoImage;

use App\User;

use App\Subscription;

use App\UserPayment;

use App\UserHistory;

use App\Helpers\AppJwt;

use App\Wishlist;

use App\Flag;

use App\LiveVideo;

use App\UserRating;

use App\Settings;

use App\Coupon;

use App\Page;

use App\Helpers\Helper;

use App\Helpers\EnvEditorHelper;

use Validator;

use App\BannerAd;

use Auth;

use Setting;

use Log;

use DB;

use Exception;

use App\Jobs\CompressVideo;

use App\VideoAd;

use App\AssignVideoAd;

use App\VideoTapeImage;

use App\AdsDetail;

use App\Channel;

use App\Redeem;

use App\RedeemRequest;

use App\PayPerView;

use App\Repositories\CommonRepository as CommonRepo;

use App\Repositories\AdminRepository as AdminRepo;

use App\Repositories\VideoTapeRepository as VideoRepo;

use App\Jobs\NormalPushNotification;

use App\ChannelSubscription; 

use App\Category;

use App\Tag;

use App\VideoTapeTag;

use App\CustomLiveVideo;

use App\LiveVideoPayment;

class AdminController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');  
    }

    public function login() {
        
        return view('admin.login')->withPage('admin-login')->with('sub_page','');
    }

    /**
     *
     *
     *
     *
     */

    public function dashboard() {

        $admin = Admin::first();

        $admin->token = Helper::generate_token();

        $admin->token_expiry = Helper::generate_token_expiry();

        $admin->save();
        
        $user_count = User::count();

        $channel_count = Channel::count();

        $video_count = VideoTape::count();
 
        $recent_videos = VideoRepo::admin_recently_added();

        $get_registers = get_register_count();

        $recent_users = get_recent_users();

        $total_revenue = total_revenue();

        $view = last_days(10);

        return view('admin.dashboard.dashboard')->withPage('dashboard')
                    ->with('sub_page','')
                    ->with('user_count' , $user_count)
                    ->with('video_count' , $video_count)
                    ->with('channel_count' , $channel_count)
                    ->with('get_registers' , $get_registers)
                    ->with('view' , $view)
                    ->with('total_revenue' , $total_revenue)
                    ->with('recent_users' , $recent_users)
                    ->with('recent_videos' , $recent_videos);
    }

    /**
     * Function Name : users_list
     *
     * To Load all the users 
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param --
     * 
     * @return response of users list
     *
     */
    public function users_list() {

        $users = User::orderBy('created_at','desc')
                    ->withCount('getChannel')
                    ->withCount('getChannelVideos')
                    ->get();

        return view('admin.users.list')->withPage('users')
                        ->with('users' , $users)
                        ->with('sub_page','view-user');
    
    }

    /**
     * Function Name : users_type_list
     *
     * To get the based on type users list in ios , andriod , web
     *
     * @created By - Maheswari
     *
     * @updated - -
     *
     * @param - device type
     * 
     * @return response of users list
     *
     */
    public function users_type_list($type) {

        $user_type_list= User::where('device_type',$type)
                            ->orderBy('created_at','desc')
                            ->withCount('getChannel')
                            ->withCount('getChannelVideos')
                            ->get();

        if($user_type_list){

            return view('admin.users.list')->withPage('users')
                ->with('users' , $user_type_list)
                ->with('sub_page','view-user');
        } else{

            return back()->with('flash_error',tr('user_not_found'));
        }
        
    
    }


    /**
     * Function Name : users_create
     *
     * To create a new user
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param --
     * 
     * @return response of new User object
     *
     */
    public function users_create() {

        $user = new User;

        return view('admin.users.create')
                ->with('page' , 'users')
                ->with('sub_page','add-user')
                ->with('user', $user);

    }

    /**
     * Function Name : users_edit
     *
     * To edit a user based on their id
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param Integer $request - User id
     * 
     * @return response of new User object
     *
     */
    public function users_edit(Request $request) {

        try {
          
            $user_details = User::find($request->user_id);

            if( count($user_details) == 0 ) {

                throw new Exception( tr('admin_user_not_found'), 101);

            } else {

                $user_details->dob = ($user_details->dob) ? date('d-m-Y', strtotime($user_details->dob)) : '';

                return view('new_admin.users.edit')
                        ->with('page' , 'users')
                        ->with('sub_page','users-view')
                        ->with('user_details',$user_details);
            }

        } catch( Exception $e) {
            
            $error = $e->getMessage();

            return redirect()->route('admin.users.index')->with('flash_error',$error);
        }    
    }

    /**
     * Function Name : users_save
     *
     * To save the details based on user or to create a new user
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param object $request - User object details
     * 
     * @return response of success/failure reponse details
     *
     */
    public function users_save(Request $request) {
       
        $validator = Validator::make( $request->all(), array(
                'id'=>'exists:users,id',
                'name' => 'required|max:255',
                'email' => $request->id ? 'required|email|max:255|unique:users,email,'.$request->id.',id' : 'required|email|max:255|unique:users,email,NULL,id',
                'mobile' => 'digits_between:6,13',
                'password' => $request->id ? '' :'required|min:6|confirmed',
                'dob'=>'required',
                'description'=>'max:255',
                'picture'=>'mimes:jpg,png,jpeg',
            )
        );
        
        if($validator->fails()) {

            $error_messages = implode(',', $validator->messages()->all());

            return back()->with('flash_errors', $error_messages)->withInput();

        } else {

            $user = $request->id ? User::find($request->id) : new User;

            $new_user = NEW_USER;

            if($user->id) {

                $new_user = EXISTING_USER;

                $message = tr('admin_not_user');

            } else {

                $user->password = ($request->password) ? \Hash::make($request->password) : null;

                $message = tr('admin_add_user');

                $user->login_by = 'manual';

                $user->device_type = 'web';

                $user->picture = asset('placeholder.png');

                $user->chat_picture = asset('placeholder.png');

                $user->timezone = $request->has('timezone') ? $request->timezone : '';

            }
            
            $user->name = $request->has('name') ? $request->name : '';

            $user->email = $request->has('email') ? $request->email: '';

            $user->mobile = $request->has('mobile') ? $request->mobile : '';

            $user->description = $request->has('description') ? $request->description : '';
            
            $user->token = Helper::generate_token();

            $user->token_expiry = Helper::generate_token_expiry();

            $user->dob = $request->dob ? date('Y-m-d', strtotime($request->dob)) : $user->dob;

            if ($user->dob) {

                $from = new \DateTime($user->dob);

                $to   = new \DateTime('today');

                $user->age_limit = $from->diff($to)->y;

            }

            if ($user->age_limit < 10) {

               return back()->with('flash_error', tr('min_age_error'))->withInput();

            }

            if($new_user) {

                $email_data['name'] = $user->name;

                $email_data['password'] = $request->password;

                $email_data['email'] = $user->email;

                $subject = tr('user_welcome_title' , Setting::get('site_name'));

                $page = "emails.admin_user_welcome";

                $email = $user->email;

                $user->is_verified = USER_EMAIL_VERIFIED;

                Helper::send_email($page,$subject,$email,$email_data);

                register_mobile('web');
            }

            // Upload picture

            if ($request->hasFile('picture') != "") {

                if ($request->id) {

                    Helper::delete_picture($user->picture, "/uploads/images/"); // Delete the old pic

                    Helper::delete_picture($user->chat_picture, "/uploads/user_chat_img/");
                }
               
                $user->picture = Helper::normal_upload_picture($request->file('picture'), "/uploads/images/", $user);
                
            }


            $user->save();

            // Check the default subscription and save the user type 

            if ($request->id == '') {
                
                user_type_check($user->id);
            }

            if($user) {

               // $user->token = AppJwt::create(['id' => $user->id, 'email' => $user->email, 'role' => "model"]);

                // $user->save();
                
                return redirect(route('admin.users.view', $user->id))->with('flash_success', $message);

            } else {

                return back()->with('flash_error', tr('admin_not_error')->withInput());

            }

        }
    
    }

    /**
     * Function Name : users_view
     *
     * To view the usre details based on user id
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param integer $id - User Id
     * 
     * @return response of user details
     *
     */
    public function users_view($id) {

        $user = User::where('id', $id)->withCount('getChannel')
                    ->withCount('getChannelVideos')
                    ->withCount('userWishlist')
                    ->withCount('userHistory')
                    ->withCount('userRating')
                    ->withCount('userFlag')
                    ->first();

        if($user) {

            $channels = Channel::where('user_id', $id)
                    ->orderBy('created_at', 'desc')
                    ->withCount('getVideoTape')
                    ->withCount('getChannelSubscribers')
                    ->paginate(12);

            $channel_datas = [];

            foreach ($channels as $key => $value) {

                $earnings = 0;

                if ($value->getVideoTape) {

                    foreach ($value->getVideoTape as $key => $video) {

                        $earnings += $video->user_ppv_amountl;
                    }
                }
                
                $channel_datas[] = [

                    'channel_id'=>$value->id,

                    'channel_name'=>$value->name,

                    'picture'=>$value->picture,

                    'cover'=>$value->cover,

                    'subscribers'=>$value->get_channel_subscribers_count,

                    'videos'=>$value->get_video_tape_count,

                    'earnings'=>$earnings,

                    'currency'=>Setting::get('currency')

                ];

            }

            // Without below condition the output of $channel_datas will be array f index value

            $channel_datas = json_encode($channel_datas);

            $channel_datas = json_decode($channel_datas);

            $videos = $user->getChannelVideos;

            $wishlist = Wishlist::select('wishlists.*', 'video_tapes.title as title')
                    ->where('wishlists.user_id', $id)
                    ->leftJoin('video_tapes', 'video_tapes.id', '=', 'wishlists.video_tape_id')
                    ->orderBy('wishlists.created_at', 'desc')
                    ->paginate(12);

            $history = UserHistory::select('user_histories.*', 'video_tapes.title as title')
                    ->where('user_histories.user_id', $id)
                    ->leftJoin('video_tapes', 'video_tapes.id', '=', 'user_histories.video_tape_id')
                    ->orderBy('user_histories.created_at', 'desc')
                    ->paginate(12);

            $spam_reports = Flag::select('flags.*', 'video_tapes.title as title')
                    ->where('flags.user_id', $id)
                    ->leftJoin('video_tapes', 'video_tapes.id', '=', 'flags.video_tape_id')
                    ->orderBy('flags.created_at', 'desc')
                    ->paginate(12);

            $user_ratings = UserRating::select('user_ratings.*', 'video_tapes.title as title')
                    ->where('user_ratings.user_id', $id)
                    ->leftJoin('video_tapes', 'video_tapes.id', '=', 'user_ratings.video_tape_id')
                    ->orderBy('user_ratings.created_at', 'desc')
                    ->paginate(12);

            return view('admin.users.view')
                        ->with('user' , $user)
                        ->withPage('users')
                        ->with('sub_page','users')
                        ->with('channels', $channel_datas)
                        ->with('videos', $videos)
                        ->with('wishlists', $wishlist)
                        ->with('histories', $history)
                        ->with('spam_reports', $spam_reports)
                        ->with('user_ratings', $user_ratings);
        } else {

            return back()->with('flash_error',tr('user_not_found'));

        }
    
    }


    /**
     * Function Name : users_delete
     *
     * To delete the user details based on user id
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param integer $request - User id
     * 
     * @return response of user details
     *
     */
    public function users_delete(Request $request) {
       
        if($user = User::where('id',$request->id)->first()) {

            // Check User Exists or not

            if ($user) {

                Helper::delete_picture($user->picture, "/uploads/images/"); // Delete the old pic

                if ($user->device_type) {

                    // Load Mobile Registers

                    subtract_count($user->device_type);
                
                }

                // After reduce the count from mobile register model delete the user
                if ($user->delete()) {
                    return back()->with('flash_success',tr('admin_not_user_del'));   
                }
            }
        }

        return back()->with('flash_error',tr('admin_not_error'));
    
    }

   /**
    * Function Name : user_status_change()
    * 
    * @uses Change the user status in approve and decline 
    *
    * @created Maheswari
    *
    * @updated vithya
    *
    * @param Request in user id
    *
    * @return success message in approve/decline
    */
    public function users_status_change(Request $request){

        $users_status = User::find($request->id);

        if($users_status){

            $users_status->status = $request->status;

            $users_status->save();

            if($request->status==DEFAULT_FALSE){

                Channel::where('user_id', $users_status->id)->update(['is_approved'=>ADMIN_CHANNEL_DECLINED]);

                VideoTape::where('user_id', $users_status->id)->update(['is_approved'=>ADMIN_VIDEO_DECLINED_STATUS]);

                return back()->with('flash_success',tr('user_decline_success'));

            } else{

                return back()->with('flash_success',tr('user_approved_success'));

            }

        } else {

            return back()->with('flash_error',tr('user_id_not_found'));
        }

    }


    /**
     * Function Name : users_verify_status
     *
     * To verify the user based on the details of id
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param integer $request - User id
     * 
     * @return response of user details
     *
     */
    public function users_verify_status($id) {

        if($data = User::find($id)) {

            $data->is_verified  = $data->is_verified ? 0 : 1;

            $data->save();

            return back()->with('flash_success' , $data->status ? tr('user_verify_success') : tr('user_unverify_success'));

        } else {

            return back()->with('flash_error',tr('admin_not_error'));
            
        }
    
    }

    /**
     * Function Name : users_history
     *
     * To list down all the videos based on hstory
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param integer $requesr - User id
     * 
     * @return - Response of channel creation page
     *
     */
    public function users_history($id) {

        if($user_details = User::find($id)) {

            $user_history = UserHistory::where('user_histories.user_id' , $id)
                            ->leftJoin('users' , 'user_histories.user_id' , '=' , 'users.id')
                            ->leftJoin('video_tapes' , 'user_histories.video_tape_id' , '=' , 'video_tapes.id')
                            ->select(
                                'users.name as username' , 
                                'users.id as user_id' , 
                                'user_histories.video_tape_id',
                                'user_histories.id as user_history_id',
                                'video_tapes.title',
                                'user_histories.created_at as date'
                                )
                            ->get();

            return view('admin.users.history')
                        ->with('user_details' , $user_details)
                        ->with('data' , $user_history)
                        ->withPage('users')
                        ->with('sub_page','users');

        } else {

            return back()->with('flash_error',tr('admin_not_error'));
        }
    
    }

    /**
     * Function Name : users_history_delete
     *
     * To delete the history based on history id
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param integer $request - Integer id
     * 
     * @return - Response of success/failure message
     *
     */
    public function users_history_delete($id) {

        if($user_history = UserHistory::find($id)) {

            $user_history->delete();

            return back()->with('flash_success',tr('admin_not_history_del'));

        } else {

            return back()->with('flash_error',tr('admin_not_error'));

        }
    
    }


    /**
     * Function Name : users_wishlist
     *
     * To list out all the wishlist details based on user
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param integer $request - User id
     * 
     * @return - Response of wishlist based on id
     *
     */
    public function users_wishlist($id) {

        if($user = User::find($id)) {

            $user_wishlist = Wishlist::where('wishlists.user_id' , $id)
                            ->leftJoin('users' , 'wishlists.user_id' , '=' , 'users.id')
                            ->leftJoin('video_tapes' , 'wishlists.video_tape_id' , '=' , 'video_tapes.id')
                            ->select(
                                'users.name as username' , 
                                'users.id as user_id' , 
                                'wishlists.video_tape_id',
                                'wishlists.id as wishlist_id',
                                'video_tapes.title',
                                'wishlists.created_at as date'
                                )
                            ->get();

            return view('admin.users.user-wishlist')
                        ->with('data' , $user_wishlist)
                        ->with('user_details' , $user)
                        ->withPage('users')
                        ->with('sub_page','users');

        } else {

            return back()->with('flash_error',tr('admin_not_error'));
        }    
    }

    /**
     * Function Name : users_wishlist_delete
     *
     * To delete the wishlist based on wishlist id
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param integer $request - User id
     * 
     * @return - Response of success/failure message
     *
     */
    public function users_wishlist_delete($id) {

        if($user_wishlist = Wishlist::find($id)) {

            $user_wishlist->delete();

            return back()->with('flash_success',tr('user_wishlist_delete_success'));

        } else {

            return back()->with('flash_error',tr('admin_not_error'));

        }
    
    }
  

    /**
     * Function Name : users_subscriptions
     *
     * To subscribe a new plans based on users
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param integer $id - User id (Optional)
     * 
     * @return - response of array of subscription details
     *
     */
    public function users_subscriptions($id) {

        $data = Subscription::orderBy('created_at','desc')->get();

        $payments = []; 

        if($id) {

            $payments = UserPayment::select('user_payments.*', 'subscriptions.title')
                        ->leftjoin('subscriptions', 'subscriptions.id', '=', 'user_payments.subscription_id')
                        ->orderBy('user_payments.created_at' , 'desc')
                        ->where('user_payments.user_id' , $id)->get();

        }

        return view('admin.subscriptions.user_plans')->withPage('users')
                        ->with('subscriptions' , $data)
                        ->with('id', $id)
                        ->with('sub_page','users')->with('payments', $payments);        

    }

    /**
     * Function Name : users_subscription_save
     *
     * To save subscription details based on user id
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param integer $s_id - Subscription id, $u_id - User id
     * 
     * @return - response of array of subscription details
     *
     */
    public function users_subscription_save($s_id, $u_id) {

        $response = CommonRepo::save_subscription($s_id, $u_id)->getData();

        if($response->success) {

            return back()->with('flash_success', $response->message);

        } else {

            return back()->with('flash_error', $response->message);

        }

    }


    /**
     * Function Name : channels_create
     *
     * To create a new channel
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param --
     * 
     * @return - Response of channel creation page
     *
     */
    public function channels_create() {

        // Check the create channel option is enabled from admin settings

        if(Setting::get('create_channel_by_user') == CREATE_CHANNEL_BY_USER_ENABLED) {

            $users = User::where('is_verified', DEFAULT_TRUE)
                    ->where('status', DEFAULT_TRUE)
                    ->where('user_type', SUBSCRIBED_USER)
                    ->orderBy('created_at', 'desc')
                    ->get();

        } else {

            // Load master user

            $users = User::where('is_verified', DEFAULT_TRUE)
                        ->where('is_master_user' , 1)
                        ->where('status', DEFAULT_TRUE)
                        ->orderBy('created_at', 'desc')
                        ->get();

        }

        $channel = new Channel;
         
        return view('admin.channels.create')
                ->with('users', $users)
                ->with('channel', $channel)
                ->with('page' ,'channels')
                ->with('sub_page' ,'add-channel');
    }

    /**
     * Function Name : channels_edit
     *
     * To edit the channel based on the channel id
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param integer $id - Channel id
     * 
     * @return response of channel edit
     *
     */
    public function channels_edit($id) {

        $channel = Channel::find($id);

        if ($channel) {

            $users = User::where('is_verified', DEFAULT_TRUE)
                    ->where('status', DEFAULT_TRUE)
                    ->where('user_type', SUBSCRIBED_USER)
                    ->get();

            return view('admin.channels.edit')
                    ->with('channel' , $channel)
                    ->with('page' ,'channels')
                    ->with('sub_page' ,'edit-channel')
                    ->with('users', $users);

        } else {

            return back()->with('flash_error', tr('channel_not_found'));

        }
    
    }


    /**
     * Function Name : channels_view
     *
     * To view the channel based on the channel id
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param integer $id - Channel id
     * 
     * @return response of channel edit
     *
     */
    public function channels_view($id) {

        $channel = Channel::select('channels.*', 'users.name as user_name', 'users.picture as user_picture')
                    ->leftjoin('users', 'users.id', '=', 'channels.user_id')
                    ->withCount('getVideoTape')
                    ->withCount('getChannelSubscribers')
                    ->where('channels.id', $id)
                    ->first();
        if ($channel) {

            // Load videos and subscribrs based on the channel

            $channel_earnings = getAmountBasedChannel($channel->id);

            $videos = VideoTape::select('video_tapes.title', 'video_tapes.default_image', 'video_tapes.id', 'video_tapes.description', 'video_tapes.created_at')
                        ->where('channel_id', $channel->id)
                        ->paginate(12);

            $subscribers = ChannelSubscription::select('users.name as user_name', 'users.id as user_id', 'users.picture as user_picture', 'users.description', 'users.created_at', 'users.email')->where('channel_id', $channel->id)
                        ->leftjoin('users', 'users.id', '=', 'channel_subscriptions.user_id')
                        ->paginate(12);

            return view('admin.channels.view')
                    ->with('channel' , $channel)
                    ->with('channel_earnings', $channel_earnings)
                    ->with('videos', $videos)
                    ->with('page' ,'channels')
                    ->with('subscribers', $subscribers)
                    ->with('sub_page' ,'edit-channel');
        } else {

            return back()->with('flash_error', tr('channel_not_found'));

        }
    
    }

    /**
     * Function Name : channels_save
     *
     * To save the channel video object details
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param integer $id - Channel id
     * 
     * @return response of channel edit
     *
     */
    public function channels_save(Request $request) {

        $response = CommonRepo::channel_save($request)->getData();
        
        if($response->success) {

            return back()->with('flash_success', $response->message);

        } else {
            
            return back()->with('flash_error', $response->error_messages);
        }
        
    }

    /**
     * Function Name : channels_delete
     *
     * To delete the channel based on channel id
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param integer $request->id - Channel id
     * 
     * @return response of channel edit
     *
     */
    public function channels_delete(Request $request) {
        
        $channel = Channel::where('id' , $request->channel_id)->first();

        if($channel) {       

            $channel->delete();

            return back()->with('flash_success',tr('channel_delete_success'));

        } else {

            return back()->with('flash_error',tr('something_error'));

        }
    
    }

    /**
     * Function Name : channels_status_change
     *
     * To change the channel status of approve and decline 
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param integer $request->id - Channel id
     * 
     * @return response of channel edit
     *
     */

    public function channels_status_change(Request $request) {

        $channel = Channel::find($request->id);

        if ($channel) {

            $channel->is_approved = $request->status;

            $channel->save();

            if ($request->status == ADMIN_CHANNEL_DECLINED) {

                VideoTape::where('channel_id', $channel->id)
                            ->update(['is_approved'=>ADMIN_CHANNEL_DECLINED]);
            
            }

            $message = tr('channel_decline_success');

            if($channel->is_approved == DEFAULT_TRUE){

                $message = tr('channel_approve_success');
            }

            return back()->with('flash_success', $message);

        } else {

            return back()->with('flash_error', tr('channel_not_found'));

        }
    
    }


    /**
     * Function Name : users_channels
     *
     * To list out all the channels based on users id
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param integer $user_id - User id
     * 
     * @return response of user channel details
     *
     */
    public function users_channels($user_id) {

        $user = User::find($user_id);

        if($user){

            $channels = Channel::orderBy('channels.created_at', 'desc')
                            ->where('user_id' , $user_id)
                            ->distinct('channels.id')
                            ->get();

            return view('admin.channels.list')
                    ->with('channels' , $channels)
                    ->withPage('channels')
                    ->with('sub_page','view-channels')
                    ->with('user' , $user);
        } else {

            return back()->with('flash_error' , tr('user_not_found'));

        }
    
    }

    /**
     * Function Name : channels
     *
     * To list out all the channels
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param --
     * 
     * @return response of user channel details
     *
     */
    public function channels() {

        $channels = Channel::orderBy('channels.created_at', 'desc')
                        ->distinct('channels.id')
                        ->withCount('getChannelSubscribers')
                        ->withCount('getVideoTape')
                        ->get();

        return view('admin.channels.list')
            ->with('channels' , $channels)
            ->withPage('channels')
            ->with('sub_page','view-channels');
    
    }

    /**
     * Function Name : channels_videos
     *
     * To list out particular channel videos based on channel id
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param integer $channel_id - Channel Id
     * 
     * @return response of user channel details
     *
     */
    public function channels_videos($channel_id) {

        $channel = Channel::find($channel_id);

        if ($channel) {

            $videos = VideoTape::leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                        ->where('channel_id' , $channel_id)
                        ->videoResponse()
                        ->orderBy('video_tapes.created_at' , 'desc')
                        ->get();

            return view('admin.videos.videos')->with('videos' , $videos)
                        ->withPage('videos')
                        ->with('channel' , $channel)
                        ->with('sub_page','view-videos');
        } else {

            return back()->with('flash_error', tr('channel_not_found'));

        }
   
    }


    /**
     * Function Name : channels_subscribers
     *
     * To list out particular channel subscribers based on channel id
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param integer $request->id - Channel Id (optional)
     * 
     * @return response of subscribers of channel details
     *
     */
    public function channels_subscribers(Request $request) {

        if ($request->id) {

            $subscribers = ChannelSubscription::where('channel_id', $request->id)->orderBy('created_at', 'desc')->get();

        } else {

            $subscribers = ChannelSubscription::orderBy('created_at', 'desc')->get();

        }

        return view('admin.channels.subscribers')
                ->with('subscribers' , $subscribers)
                ->withPage('channels')
                ->with('sub_page','subscribers');
    
    }

    /**
     * Function Name : videos_list
     *
     * List of videos displayed and also based on user it will list out
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param --
     * 
     * @return response of videos details
     *
     */
    public function videos_list(Request $request) {

        try {

            $query = VideoTape::leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                        ->videoResponse()
                        ->orderBy('video_tapes.created_at' , 'desc');

            if($request->has('tag_id')) {

                $tag_details = Tag::find($request->tag_id);

                if (count($tag_details) == 0) {

                    throw new Exception(tr('admin_tag_not_found'), 101);
                }

                $query->leftjoin('video_tape_tags', 'video_tape_tags.video_tape_id', '=', 'video_tapes.id')
                        ->where('video_tape_tags.tag_id', $request->tag_id)
                        ->orderBy('video_tapes.created_at' , 'desc')
                        ->groupBy('video_tape_tags.video_tape_id');
            }

            if ($request->id) {

                $query->where('video_tapes.user_id', $request->id);
            }

            $videos = $query->get();

            return view('admin.videos.videos')
                        ->with('videos' , $videos)
                        ->withPage('videos')
                        ->with('sub_page','view-videos');
   
            
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return redirect()->route('admin.videos.list')->with('flash_error',$error);
        }
    }

    /**
     * Function Name : videos_view
     *
     * Load video based on id 
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param --
     * 
     * @return response of videos details
     *
     */
    public function videos_view(Request $request) {
    
        $validator = Validator::make($request->all() , [
                'id' => 'required|exists:video_tapes,id'
            ]);

        if($validator->fails()) {
            $error_messages = implode(',', $validator->messages()->all());
            return back()->with('flash_errors', $error_messages);
        } else {
            $video = VideoTape::where('video_tapes.id' , $request->id)
                    ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                    ->videoResponse()
                    ->orderBy('video_tapes.created_at' , 'desc')
                    ->first();

            $video_tags = VideoTapeTag::where('video_tape_tags.video_tape_id' , $request->id)
                    ->leftjoin('tags','tags.id' , '=' , 'video_tape_tags.tag_id')
                    ->get();

            $videoPath = $video_pixels = $videoStreamUrl = '';
            if ($video->video_type == VIDEO_TYPE_UPLOAD) {

                if (\Setting::get('streaming_url')) {
                    $videoStreamUrl = \Setting::get('streaming_url').get_video_end($video->video);
                    if ($video->is_approved == 1) {
                        if ($video->video_resolutions) {
                            $videoStreamUrl = Helper::web_url().'/uploads/smil/'.get_video_end_smil($video->video).'.smil';
                        }
                    }
                } else {

                    $videoPath = $video->video_resize_path ? $videos->video.','.$video->video_resize_path : $video->video;
                    $video_pixels = $video->video_resolutions ? 'original,'.$video->video_resolutions : 'original';
                    

                }
            } else {
                $videoStreamUrl = $video->video;
            }
        
        $admin_video_images = $video->getScopeVideoTapeImages;

        // Spam Videos Reports

        $spam_reports = Flag::select('users.name as user_name', 'flags.*')
                ->leftjoin('users', 'users.id', '=', 'flags.user_id')
                ->where('video_tape_id', $request->id)->paginate(12);

        // User Reviews 

        $reviews = UserRating::select('users.name as user_name', 'user_ratings.*')
                ->leftjoin('users', 'users.id', '=', 'user_ratings.user_id')
                ->where('video_tape_id', $request->id)->paginate(12);

        // Wishlists

        $wishlists = Wishlist::select( 'wishlists.*','users.name as user_name')
                ->leftjoin('users', 'users.id', '=', 'wishlists.user_id')
                ->where('video_tape_id', $request->id)->get(12);


        $page = 'videos';
        $sub_page = 'add-video';

        if($video->is_banner == 1) {
            $page = 'banner-videos';
            $sub_page = 'banner-videos';
        }

        return view('admin.videos.view-video')->with('video' , $video)
                    ->with('video_images' , $admin_video_images)
                    ->withPage($page)
                    ->with('sub_page','view-videos')
                    ->with('videoPath', $videoPath)
                    ->with('video_pixels', $video_pixels)
                    ->with('videoStreamUrl', $videoStreamUrl)
                    ->with('spam_reports', $spam_reports)
                    ->with('reviews', $reviews)
                    ->with('wishlists', $wishlists)
                    ->with('video_tags', $video_tags);
        }
    
    }


    /**
     * Function Name : videos_wishlist
     *
     * To list out all the wishlist details based on user
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param integer $request - Video id
     * 
     * @return - Response of wishlist based on id
     *
     */
    public function videos_wishlist(Request $request) {

        $wishlists = Wishlist::where('wishlists.video_tape_id' , $request->id)
                        ->leftJoin('users' , 'wishlists.user_id' , '=' , 'users.id')
                        ->leftJoin('video_tapes' , 'wishlists.video_tape_id' , '=' , 'video_tapes.id')
                        ->select(
                            'users.name as username' , 
                            'users.id as user_id' , 
                            'wishlists.video_tape_id',
                            'wishlists.id as wishlist_id',
                            'video_tapes.title',
                            'wishlists.created_at'
                            )
                        ->get();

        return view('admin.videos.wishlists')
                    ->with('data' , $wishlists)
                    ->withPage('videos')
                    ->with('sub_page','view-videos');

       
    }

    /**
     * Function Name : videos_create
     *
     * To create new video 
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param --
     * 
     * @return response of videos form
     *
     */
    public function videos_create(Request $request) {

        $channels = getChannels();

        $categories_list = Category::select('id as category_id', 'name as category_name')->where('status', CATEGORY_APPROVE_STATUS)->orderBy('created_at', 'desc')
                ->get();

        $tags = Tag::select('tags.id as tag_id', 'name as tag_name', 'search_count as count')
                    ->where('status', TAG_APPROVE_STATUS)
                    ->orderBy('created_at', 'desc')->get();

        return view('admin.videos.video_upload')
                ->with('channels' , $channels)
                ->with('page' ,'videos')
                ->with('sub_page' ,'add-video')
                ->with('categories', $categories_list)
                ->with('tags', $tags);

    }

    /**
     * Function Name : videos_edit
     *
     * To Edit a video based on video id
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param Integer $request - Video Id
     * 
     * @return response of videos form
     *
     */
    public function videos_edit(Request $request) {


        $video = VideoTape::where('video_tapes.id' , $request->id)
                    ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                    ->videoResponse()
                    ->orderBy('video_tapes.created_at' , 'desc')
                    ->first();

        if ($video) {

            $page = 'videos';
            $sub_page = 'add-video';


            if($video->is_banner == 1) {
                $page = 'banner-videos';
                $sub_page = 'banner-videos';
            }

            $channels = getChannels();

            $categories_list = Category::select('id as category_id', 'name as category_name')->where('status', CATEGORY_APPROVE_STATUS)->orderBy('created_at', 'desc')
                ->get();


            $tags = Tag::select('tags.id as tag_id', 'name as tag_name', 'search_count as count')
                    ->where('status', TAG_APPROVE_STATUS)
                    ->orderBy('created_at', 'desc')->get();

            $video->tag_id = VideoTapeTag::where('video_tape_id', $request->id)->where('status', TAG_APPROVE_STATUS)->get()->pluck('tag_id')->toArray();

            return view('admin.videos.edit-video')
                    ->with('channels' , $channels)
                    ->with('video' ,$video)
                    ->with('page' ,$page)
                    ->with('sub_page' ,$sub_page)
                    ->with('tags', $tags)
                    ->with('categories', $categories_list);
        } else {

            return back()->with('flash_error', tr('video_not_found'));

        }
    
    }

    /**
     * Function Name : videos_save()
     *
     * To Save video based on new /edit video details
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param Object $request - Video Object Details
     * 
     * @return response of success/failure 
     *
     */
    public function videos_save(Request $request) {
        
        $response = CommonRepo::video_save($request)->getData();

        if ($response->success) {

            $view = '';

            if ($response->data->video_type == VIDEO_TYPE_UPLOAD) {

                $tape_images = VideoTapeImage::where('video_tape_id', $response->data->id)->get();

                $view = \View::make('admin.videos.select_image')->with('model', $response)->with('tape_images', $tape_images)->render();

            }

            return response()->json(['success'=>true, 'path'=>$view, 'data'=>$response->data], 200);

        } else {

            return response()->json($response);

        }

    }  

    /**
     * Function Name : videos_images()
     *
     * To get images which is uploaded in Video Based on id
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param Integer $request - Video Id
     * 
     * @return response of success/failure 
     *
     */
    public function videos_images($id) {

        $response = CommonRepo::get_video_tape_images($id)->getData();

        $tape_images = VideoTapeImage::where('video_tape_id', $id)->get();

        $view = \View::make('admin.videos.select_image')->with('model', $response)->with('tape_images', $tape_images)->render();

        return response()->json(['path'=>$view, 'data'=>$response->data]);

    } 

    /**
     * Function Name : videos_save_default_img()
     *
     * To set the default image based on object details
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param Integer $request - Video Id
     * 
     * @return response of success/failure message
     *
     */
    public function videos_save_default_img(Request $request) {

        $response = CommonRepo::set_default_image($request)->getData();

        return response()->json($response);

    }

    /**
     * Function Name : videos_upload_image()
     *
     * To save the image based on object details
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param Integer $request - Video Id, Video details
     * 
     * @return response of success/failure message
     *
     */
    public function videos_upload_image(Request $request) {

        $response = CommonRepo::upload_video_image($request)->getData();

        return response()->json($response);

    }

    /**
     * Function Name : videos_status()
     *
     * To change the status of approve/decline video
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param Integer $request - Video Id, Video details
     * 
     * @return response of success/failure message
     *
     */
    public function videos_status(Request $request) {

        $video = VideoTape::find($request->video_tape_id);

        $video->is_approved = $video->is_approved ? DEFAULT_FALSE : DEFAULT_TRUE;

        $video->save();

        if($video->is_approved == DEFAULT_TRUE) {

            $message = tr('admin_not_video_approve');

        } else {

            $message = tr('admin_not_video_decline');

        }

        return back()->with('flash_success', $message);
    
    }

    /**
     * Function Name : videos_publish()
     *
     * To publish the video based on changing the status of the video
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param Integer $id - Video Id
     * 
     * @return response of success/failure message
     *
     */
    public function videos_publish($id) {

        // Load video based on Auto increment id
        $video = VideoTape::find($id);

        // Check the video present or not
        if ($video) {

            $video->status = DEFAULT_TRUE;

            $video->publish_time = date('Y-m-d H:i:s');

            // Save the values in DB
            if ($video->save()) {

                return back()->with('flash_success', tr('admin_published_video_success'));

            }

        }

        return back()->with('flash_error', tr('admin_published_video_failure'));
    }


    /**
     * Function Name : videos_delete()
     *
     * To delete a video based on video id
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param Integer $id - Video Id
     * 
     * @return response of success/failure message
     *
     */
    public function videos_delete($id) {

        if($video = VideoTape::where('id' , $id)->first())  {

            $video->delete();

            return back()->with('flash_success', tr('video_delete_success'));
        }

        return back()->with('flash_error', tr('video_not_found'));
    
    }

    /**
     * Function Name : videos_set_ppv
     *
     * Brief : To save the payment details
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param integer $id Video Id
     *
     * @param object  $request Object (Post Attributes)
     *
     * @return flash message
     */
    public function videos_set_ppv($id, Request $request) {
        
        if($request->ppv_amount > 0){

            // Load Video Model
            $model = VideoTape::find($id);

            // Get post attribute values and save the values
            if ($model) {

                 $request->request->add([ 
                    'ppv_created_by'=> 0 ,
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

        } else {

            return back()->with('flash_error',tr('add_ppv_amount'));
        }
    
    }


    /**
     * Function Name : videos_remove_ppv
     *
     * Brief : To remove PPV Details
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param integer $id Video Id
     *     
     * @return flash message
     */
    public function videos_remove_ppv($id) {
        
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
     * Function Name : banner_videos_set()
     *
     * To set a video as banner based on video id
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param Integer $id - Video Id
     * 
     * @return response of success/failure message
     *
     */
    public function banner_videos_set($id) {

        $video = VideoTape::where('is_home_slider' , 1 )->update(['is_home_slider' => 0]); 

        $video = VideoTape::where('id' , $id)->update(['is_home_slider' => 1] );

        return back()->with('flash_success', tr('slider_success'));
    
    }

    /**
     * Function Name : banner_videos()
     *
     * To list out all the banner videos 
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param - 
     * 
     * @return response of array of banner videos
     *
     */
    public function banner_videos(Request $request) {

        $videos = VideoTape::leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                    ->where('video_tapes.is_banner' , 1 )
                    ->videoResponse()
                    ->orderBy('video_tapes.created_at' , 'desc')
                    ->get();

        return view('admin.banner_videos.banner-videos')->with('videos' , $videos)
                    ->withPage('banner-videos')
                    ->with('sub_page','view-banner-videos');
   
    }

    /**
     * Function Name : banner_videos_create()
     *
     * To create a banner video based on id
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param Integer $id - Video Id
     * 
     * @return response of create form of banner 
     *
     */
    public function banner_videos_create(Request $request) {

        $channels = getChannels();

        return view('admin.banner_videos.banner-video-upload')
                ->with('channels' , $channels)
                ->with('page' ,'banner-videos')
                ->with('sub_page' ,'add-banner-video');

    }

    /**
     * Function Name : banner_videos_remove()
     *
     * To remove a banner video based on id
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param Integer $id - Video Id
     * 
     * @return response of succes/failure message
     *
     */
    public function banner_videos_remove($id) {

        $video = VideoTape::find($id);

        $video->is_banner = 0 ;

        $video->save();

        $message = tr('change_banner_video_success');
       
        return back()->with('flash_success', $message);
    
    }

    /**
     * Function Name : spam_videos()
     *
     * Load all the videos from flag table
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @return all the spam videos
     */
    public function spam_videos(Request $request) {
        // Load all the videos from flag table
        $model = Flag::groupBy('video_tape_id')->get();
        // Return array of values
        return view('admin.spam_videos.spam_videos')->with('model' , $model)
                        ->with('page' , 'videos')
                        ->with('sub_page' , 'spam_videos');
    }

    /**
     * Function Name : spam_videos_user_reports()
     *
     * Load all the flags based on the video id
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param integer $id Video id
     *
     * @return all the spam videos
     */
    public function spam_videos_user_reports($id) {

        // Load all the users
        $model = Flag::where('video_tape_id', $id)->get();
        // Return array of values
        return view('admin.spam_videos.user_report')->with('model' , $model)
                        ->with('page' , 'videos')
                        ->with('sub_page' , 'spam_videos');   
    }

    /**
     * Function Name : spam_videos_each_user_reports()
     *
     * Load all the flags based on the user id
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param integer $id Video id
     *
     * @return all the spam videos
     */
    public function spam_videos_each_user_reports($id) {
   
        // Load all the users
        $model = Flag::where('user_id', $id)->get();
        // Return array of values
        return view('admin.spam_videos.user_report')->with('model' , $model)
                        ->with('page' , 'videos')
                        ->with('sub_page' , 'spam_videos');   
    }

    /**
     * Function Name : spam_videos_unspam()
     *
     * Unsapm video based on flag id
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param integer $id Flag id
     *
     * @return response of success/failure message
     */
    public function spam_videos_unspam($id) {

        $model = Flag::find($id);

        if ($model) {

            if ($model->delete()) {

                return back()->with('flash_success', tr('unmark_report_video_success_msg'));

            } else {

                return back()->with('flash_error', tr('something_error'));
            }

        } else {

            return back()->with('flash_error', tr('something_error'));
        }
    }

    /**
     * Function Name : user_reviews()
     *
     * list out all the reviews which is leaves by user
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param -
     *
     * @return response of array details
     */
    public function user_reviews(Request $request) {
            
        $query = UserRating::leftJoin('users', 'user_ratings.user_id', '=', 'users.id')
            ->leftJoin('video_tapes', 'video_tapes.id', '=', 'user_ratings.video_tape_id')  
            ->select('user_ratings.id as rating_id', 'user_ratings.rating', 
                     'user_ratings.comment', 
                     'users.name as name', 
                     'video_tapes.title as title',
                     'user_ratings.video_tape_id as video_id',
                     'users.id as user_id', 'user_ratings.created_at')
            ->orderBy('user_ratings.created_at', 'desc');

        if($request->video_tape_id) {

            $query->where('user_ratings.video_tape_id',$request->video_tape_id);

        }

        if($request->user_id) {

            $query->where('user_ratings.user_id',$request->user_id);

        }

        $user_reviews = $query->get();

        return view('admin.reviews.reviews')->with('page' ,'videos')
                ->with('sub_page' ,'reviews')->with('reviews', $user_reviews);
    
    }

    /**
     * Function Name : user_reviews_delete()
     *
     * Delete a user review based on review id
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param integer $request - User review id
     *
     * @return response of array details
     */
    public function user_reviews_delete(Request $request) {

        if($user = UserRating::find($request->id)) {

            $user->delete();
        }

        return back()->with('flash_success', tr('admin_not_ur_del'));
    
    }

    /**
     * Function Name : ads_details_create()
     *
     * To create ad 
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param - 
     *
     * @return response of Ad Details Object
     */
    public function ads_details_create() {

        $model = new AdsDetail;

        return view('admin.ads_details.create')
            ->with('model', $model)
            ->with('page', 'videos_ads')
            ->with('sub_page','create-ad-videos');

    }

    /**
     * Function Name : ads_details_edit()
     *
     * To Edit ad 
     *
     * @created vithya R
     *
     * @updated - Ad Details
     *
     * @param - 
     *
     * @return response of Ad Details Object
     */
    public function ads_details_edit(Request $request) {

        $model = AdsDetail::find($request->id);

        if ($model) {

            return view('admin.ads_details.edit')->with('model', $model)->with('page', '    videos_ads')->with('sub_page','create-ad-videos');

        } else {

            return back()->with('flash_error', tr('ad_not_found'));

        }

    }

    /**
     * Function Name : ads_details_save()
     *
     * To save the ad for new & old object details
     *
     * @created vithya R
     *
     * @updated - Ad Details
     *
     * @param - 
     *
     * @return response of Ad Details Object
     */
    public function ads_details_save(Request $request) {

        $response = AdminRepo::ads_details_save($request)->getData();

        if($response->success) {

            return redirect(route('admin.ads-details.view', ['id'=>$response->data->id]))->with('flash_success', $response->message);

        } else {

            return back()->with('flash_error', $response->message);

        }

    }

    /**
     * Function Name : ads_details_index()
     *
     * To List out all the ads which is created by admin
     *
     * @created vithya R
     *
     * @updated - 
     *
     * @param -
     *
     * @return response of Ad Details array of objects
     */
    public function ads_details_index() {

        $response = AdminRepo::ads_details_index()->getData();

        return view('admin.ads_details.index')->with('model', $response)->with('page', 'videos_ads')->with('sub_page', 'view-ads');        

    }

    /**
     * Function Name : ads_details_view()
     *
     * To Find the object of Ad details based on ad details id
     *
     * @created vithya R
     *
     * @updated - Ad Details
     *
     * @param - 
     *
     * @return response of Ad Details Object
     */
    public function ads_details_view(Request $request) {

        $model = AdsDetail::find($request->id);

        if ($model) {

            return view('admin.ads_details.view')->with('model', $model)->with('page', 'videos_ads')->with('sub_page', 'view-ads');

        } else {

            return back()->with('flash_error', tr('ad_not_found'));

        }

    }

    /**
     * Function Name : ads_details_status()
     *
     * To change the status of approve/decline status
     *
     * @created vithya R
     *
     * @updated - Ad Details
     *
     * @param Integer $request->id : Ads Details Id
     *
     * @return response of Ad Details Object
     */
    public function ads_details_status(Request $request) {

        $model = AdsDetail::find($request->id);

        $model->status = $request->status;

        $model->save();

        $message = tr('ad_status_decline');

        if($model->status == DEFAULT_TRUE){

            $message = tr('ad_status_approve');
            
        }

        // Load Assigned video ads

        $assigned_video_ad = AssignVideoAd::where('ad_id', $model->id)->get();

        foreach ($assigned_video_ad as $key => $value) {
           
            // Load video ad

            $video_ad = VideoAd::find($value->video_ad_id);

            $ad_type = $value->ad_type;

            if($video_ad) {

                $exp_video_ad = explode(',', $video_ad->types_of_ad);

                if (count($exp_video_ad) == 1) {

                    $video_ad->delete();

                } else {

                    $type_of_ad = [];

                    foreach ($exp_video_ad as $key => $exp_ad) {
                            
                        if ($exp_ad == $ad_type) {



                        } else {

                            $type_of_ad[] = $exp_ad;

                        }
 
                    }

                    $video_ad->types_of_ad = is_array($type_of_ad) ? implode(',', $type_of_ad) : '';

                    $video_ad->save();

                }

            }

        }

        return back()->with('flash_success', $message);
    
    }

    /**
     * Function Name : ads_details_delete()
     *
     * To delete the ads based on Ad id
     *
     * @created vithya R
     *
     * @updated - Ad Details
     *
     * @param Integer $request->id : Ads Details Id
     *
     * @return response of Ad Details Object
     */
    public function ads_details_delete(Request $request) {

        $model = AdsDetail::find($request->id);

        if($model) {

            if (count($model->getAssignedVideo) > 0) {

                foreach ($model->getAssignedVideo as $key => $value) {

                    if ($value->videoAd) {

                        $value->videoAd->delete();
                    }

                   $value->delete();    

                }               
             
            }

            if($model->delete()) {

                return back()->with('flash_success', tr('ad_delete_success'));

            }

        } else {

            return back()->with('flash_error', tr('ad_not_found'));

        }

    }

    /**
     * Function Name : ads_details_ad_status_change()
     *
     * To change the status of the ad details (Video Ad enable/disable)
     *
     * @created vithya R
     *
     * @updated - 
     *
     * @param Integer $request->id : Ads Details Id
     *
     * @return response of Ad Details Object
     */
    public function ads_details_ad_status_change(Request $request) {

        if($data = VideoTape::find($request->id)) {

            $data->ad_status  = $data->ad_status ? 0 : 1;

            if($data->save()) {

                $video_ad = VideoAd::where('video_tape_id', $data->id)->first();

                if ($video_ad) {

                    $video_ad->status = $data->ad_status;

                    $video_ad->save();

                }

                if($data->ad_status) {

                    return back()->with('flash_success', tr('ad_status_enable_success'));
                        
                } else {

                    return back()->with('flash_success', tr('ad_status_disable_success'));
                }

            }

            return back()->with('flash_success', tr('ad_status_change_failure'));

        } else {

            return back()->with('flash_error', tr('ad_status_change_failure'));
            
        }
    }


    /**
     * Function Name : video_assign_ad()
     *
     * To assign singl/multiple based on ads with video details
     *
     * @created vithya R
     *
     * @updated - 
     *
     * @param Integer $request->id : Ads Details Id
     *
     * @return response of Ad Details Object
     */
    public function video_assign_ad(Request $request) {

        $model = AdsDetail::find($request->id);

        if (!$model) {

            return back()->with('flash_error', tr('something_error'));

        }

        $videos = VideoTape::where('status', DEFAULT_TRUE)
            ->where('publish_status', DEFAULT_TRUE)
            ->where('is_approved', DEFAULT_TRUE)
            ->where('ad_status',DEFAULT_TRUE)
            ->get();
       
        return view('admin.ads_details.assign_ad')
                ->with('page', 'videos_ads')
                ->with('sub_page', 'view-ads')
                ->with('model', $model)
                ->with('videos', $videos)
                ->with('type', $request->type);
    }


    /**
     * Function Name : video_ads_list()
     *
     * To List out all the videos ads list with videos
     *
     * @created vithya R
     *
     * @updated - 
     *
     * @param Integer $request->id : Ads Details Id
     *
     * @return response of Ad Details Object
     */
    public function video_ads_list() {

        $videos = VideoAd::select('channels.id as channel_id', 'channels.name', 'video_tapes.id as video_tape_id', 'video_tapes.title', 'video_tapes.default_image', 'video_tapes.ad_status',
            'video_ads.*','video_tapes.channel_id')
                    ->leftJoin('video_tapes' , 'video_tapes.id' , '=' , 'video_ads.video_tape_id')
                    ->leftJoin('channels' , 'channels.id' , '=' , 'video_tapes.channel_id')
                    ->orderBy('video_tapes.created_at' , 'asc')
                    ->get();

        return view('admin.video_ads.ad_videos')->with('model' , $videos)
                    ->withPage('videos_ads')
                    ->with('sub_page','ad-videos');
   
    }

    /**
     * Function Name : video_ads_view()
     *
     * To get ads with video (Single video based on id)
     *
     * @created vithya R
     *
     * @updated - 
     *
     * @param Integer $request->id : Video id
     *
     * @return response of Ad Details Object with video details
     */
    public function video_ads_view(Request $request) {
        
        $model = AdminRepo::video_ads_view($request)->getData();

        if(!$model) {

            return back()->with('flash_error', tr('something_error'));

        }

        return view('admin.video_ads.view')->with('ads', $model)
                ->with('page', 'videos_ads')->with('sub_page', 'ad-videos');

    }

    /**
     * Function Name : video_ads_delete()
     *
     * To delete assigned video ads based on video ad
     *
     * @created vithya R
     *
     * @updated - 
     *
     * @param Integer $request->id : Video ad id
     *
     * @return response of succes//failure response of details
     */
    public function video_ads_delete(Request $request) {

        $model = VideoAd::find($request->id);

        if($model) {

            /*if($model->getVideoTape) {

                $model->getVideoTape->ad_status = DEFAULT_FALSE;

                $model->getVideoTape->save();

            } */

            if($model->delete()) {

                return back()->with('flash_success', tr('ad_delete_success'));

            }

        }

        return back()->with('flash_error', tr('something_error'));

    }

    /**
     * Function Name : video_ads_assign_ad()
     *
     * To assign a add based on input details
     *
     * @created vithya R
     *
     * @updated - 
     *
     * @param Integer $request : Video ad id with video ad details
     *
     * @return response of succes//failure response of details
     */
    public function video_ads_assign_ad(Request $request) {

        try {

            DB::beginTransaction();

            if(!$request->ad_type) {

                throw new Exception(tr('select_ad_type_error'));
                
            }

            $video_tape_ids = explode(',', $request->video_tape_id);


            foreach ($video_tape_ids as $key => $video_tape_id) {
               
                $model = VideoAd::where('video_tape_id', $video_tape_id)->first();

                if($model) {

                    $ads_type = [];

                    foreach ($request->ad_type as $key => $value) {

                        $exp_type = explode(',', $model->types_of_ad);

                        if(in_array($value, $exp_type)) {

                            $assign = AssignVideoAd::where('video_ad_id', $model->id)
                                    ->where('ad_type', $value)->first();

                            if(!$assign) {

                                throw new Exception(tr('something_error'));
                            }

                        } else {

                            $assign = new AssignVideoAd;

                            $ads_type[] = $value;

                        }

                        $assign->ad_id = $request->ad_id;

                        $assign->ad_time = $request->ad_time;

                        $assign->ad_type = $value;

                        $assign->video_ad_id = $model->id;

                        if($value == BETWEEN_AD) {

                            $time = $request->video_time ? $request->video_time : "00:00:00";

                            $expTime = explode(':', $time);

                            if (count($expTime) == 3) {

                                $assign->video_time = $time;

                            }

                            if (count($expTime) == 2) {

                                 $assign->video_time = "00:".$expTime[0].":".$expTime[1];

                            }
                        } else if($value == POST_AD){

                            $assign->video_time = $model->getVideoTape ? $model->getVideoTape->duration : "00:00:00";



                        } else {

                            $assign->video_time = "00:00:00";
                        }

                        $assign->status = DEFAULT_TRUE;

                        if ($assign->save()) {

                            
                        } else {

                            throw new Exception(tr('something_error'));
                            
                        }

                    }

                    $model->types_of_ad = ($ads_type) ? $model->types_of_ad.','.implode(',', $ads_type) : $model->types_of_ad;

                    if ($model->save()) {


                    } else {

                         throw new Exception(tr('something_error'));

                    }

                } else {

                    $model = new VideoAd;

                    $model->video_tape_id = $video_tape_id;

                    $model->types_of_ad = implode(',', $request->ad_type);

                    $model->status = DEFAULT_TRUE;

                    if ($model->save()) {

                        foreach ($request->ad_type as $key => $value) {

                            $assign = new AssignVideoAd;

                            $assign->ad_id = $request->ad_id;

                            $assign->ad_type = $value;

                            $assign->video_ad_id = $model->id;

                            $assign->ad_time = $request->ad_time;

                            $time = $request->video_time ? $request->video_time : "00:00:00";


                            if($value == BETWEEN_AD) {

                                $expTime = explode(':', $time);

                                if (count($expTime) == 3) {

                                    $assign->video_time = $time;

                                }

                                if (count($expTime) == 2) {

                                     $assign->video_time = "00:".$expTime[0].":".$expTime[1];
                                }

                            } else if($value == POST_AD){

                                $assign->video_time = $model->getVideoTape ? $model->getVideoTape->duration : "00:00:00";

                            } else {

                                $assign->video_time = "00:00:00";

                            }

                            $assign->status = DEFAULT_TRUE;

                            if ($assign->save()) {


                            } else {

                                throw new Exception(tr('something_error'));
                                
                            }

                        }

                    } else {

                        throw new Exception(tr('something_error'));
                        
                    }

                }

            }     

            DB::commit();

            return back()->with('flash_success', tr('assign_ad_success'));

        } catch (Exception $e) {

            DB::rollBack();

            return back()->with('flash_error', $e->getMessage());

        }

    }

    /**
     * Function Name : video_ads_create()
     *
     * To create a video ads based on video id
     *
     * @created vithya R
     *
     * @updated - 
     *
     * @param Integer $request : Video ad id with video ad details
     *
     * @return response of succes/failure response of details
     */
    public function video_ads_create(Request $request) {

        $vModel = VideoTape::find($request->video_tape_id);

        if ($vModel) {

            $videoPath = '';

            $video_pixels = '';

            $preAd = new AdsDetail;

            $postAd = new AdsDetail;

            $betweenAd = new AdsDetail;

            $model = new VideoAd;

            if ($vModel) {

                $videoPath = $vModel->video_resize_path ? $vModel->video.','.$vModel->video_resize_path : $vModel->video;
                $video_pixels = $vModel->video_resolutions ? 'original,'.$vModel->video_resolutions : 'original';

            }

            $index = 0;

            $ads = AdsDetail::where('status', ADS_ENABLED)->get(); 

            return view('admin.video_ads.create')
                    ->with('vModel', $vModel)
                    ->with('videoPath', $videoPath)
                    ->with('video_pixels', $video_pixels)
                    ->with('page', 'videos')
                    ->with('sub_page', 'videos')
                    ->with('index', $index)
                    ->with('model', $model)
                    ->with('preAd', $preAd)
                    ->with('postAd', $postAd)
                    ->with('betweenAd', $betweenAd)
                    ->with('ads', $ads);

        } else {

            return back()->with('flash_error', tr('video_not_found'));
            
        }
    }


    /**
     * Function Name : video_ads_edit()
     *
     * To edit a assigned ad videos edit details
     *
     * @created vithya R
     *
     * @updated - 
     *
     * @param Integer $request : Video ad id with video ad details
     *
     * @return response of succes//failure response of details
     */
    public function video_ads_edit(Request $request) { 

        $model = VideoAd::find($request->id);

        $preAd = $model->getPreAdDetail ? $model->getPreAdDetail : new AdsDetail;

        $postAd = $model->getPostAdDetail ? $model->getPostAdDetail : new AdsDetail;

        $betweenAd = (count($model->getBetweenAdDetails) > 0) ? $model->getBetweenAdDetails : [];

        $index = 0;

        $vModel = $model->getVideoTape;

        $videoPath = '';

        $video_pixels = '';

        $ads = AdsDetail::where('status', ADS_ENABLED)->get(); 

        if ($vModel) {

            $videoPath = $vModel->video_resize_path ? $vModel->video.','.$vModel->video_resize_path : $vModel->video;
            $video_pixels = $vModel->video_resolutions ? 'original,'.$vModel->video_resolutions : 'original';

        }

        return view('admin.video_ads.edit')->with('vModel', $vModel)->with('videoPath', $videoPath)->with('video_pixels', $video_pixels)->with('page', 'videos_ads')->with('sub_page', 'ad-videos')->with('model', $model)->with('preAd', $preAd)->with('postAd', $postAd)->with('betweenAd', $betweenAd)->with('index', $index)->with('ads', $ads);
    }

    /**
     * Function Name : video_ads_save()
     *
     * To save the video ads when edit by the admin
     *
     * @created vithya R
     *
     * @updated - 
     *
     * @param Integer $request : Video ad id with video ad details
     *
     * @return response of succes/failure response of details
     */
    public function video_ads_save(Request $request) {

        $response = AdminRepo::video_ads_save($request)->getData();

       // dd($response);

        if($response->success) {

            return redirect(route('admin.video-ads.view', ['id'=>$response->data->id]))->with('flash_success', $response->message);

        } else {

            return back()->with('flash_error', $response->message);

        }
        
    }


    /**
     * Function Name : video_ads_inter_ads()
     *
     * To add between ads details based on video details
     *
     * @created vithya R
     *
     * @updated - 
     *
     * @param -
     *
     * @return response of Ad Between Form
     */
    public function video_ads_inter_ads(Request $request) {

        $index = $request->index + 1;

        $b_ad = new AdsDetail;

        $ads = AdsDetail::where('status', ADS_ENABLED)->get(); 
        
        return view('admin.video_ads._sub_form')->with('index' , $index)->with('b_ad', $b_ad)->with('ads', $ads);
    }


   /**
    * Function Name: banner_ads_create()
    *
    * @uses To create a banner Ad
    *
    * @created vithya
    *
    * @updated -
    *
    * @param - 
    *
    * @return HTML view page with new Object Banner Ad
    */
    public function banner_ads_create() {

        $model = new BannerAd;

        $banner = BannerAd::orderBy('position', 'desc')->first();

        $model->position = $banner ? $banner->position + DEFAULT_TRUE : DEFAULT_TRUE;

        return view('admin.banner_ads.create')->with('model', $model)
            ->with('page', 'bannerads_nav')->with('sub_page', 'bannerads-create');
    
    }

   /**
    * Function Name: banner_ads_edit()
    *
    * @uses To edit a banner Ad based on Given Banner Id
    *
    * @created vithya
    *
    * @updated -
    *
    * @param integer $request->id - Banner Id
    *
    * @return HTML view page with  Banner Ad Objectt
    */
    public function banner_ads_edit(Request $request) {

        $model = BannerAd::find($request->id);

        if (!$model) {

            return back()->with('flash_error', tr('something_error'));

        }

        return view('admin.banner_ads.edit')->with('model', $model)
            ->with('page', 'bannerads_nav')->with('sub_page', 'bannerads-index');
    
    }

   /**
    * Function Name: banner_ads_save()
    *
    * @uses To save a banner ad based on new / Old object details
    *
    * @created vithya
    *
    * @updated -
    *
    * @param integer $request->id - Banner Id
    *
    * @return response of success/failure response of banner ad details
    */
    public function banner_ads_save(Request $request) {

        $validator = Validator::make($request->all(),[
                'title' => 'max:255',
                'description' => '',
                'position'=>$request->id ? 'required' :'required|unique:banner_ads',
                'link'=>'required|url',
                'file' => $request->id ? 'mimes:jpeg,png,jpg' : 'required|mimes:jpeg,png,jpg'
        ]);
        
        if($validator->fails()) {

            $error_messages = implode(',', $validator->messages()->all());

            return back()->with('flash_errors', $error_messages);

        } else {

            $model = $request->id ? BannerAd::find($request->id) : new BannerAd;

            $model->title = $request->title ? $request->title : "";

            $model->description = $request->description ? $request->description : "";

            $model->position = $request->position;

            $model->link = $request->link;

            if($request->hasFile('file')) {

                if ($request->id) {

                    Helper::delete_picture($model->file, '/uploads/banners/');

                } 

                $model->file = Helper::normal_upload_picture($request->file('file'), '/uploads/banners/');

            }

            $model->status = DEFAULT_TRUE;

            $model->save();

            if ($model) {

                return redirect(route('admin.banner-ads.view', array('id'=>$model->id)));

            } else {

                return back()->with('flash_error', tr('something_error'));
            }

        }

    }

   /**
    * Function Name: banner_ads_view()
    *
    * @uses To view the banner id based on the Banner Id
    *
    * @created vithya
    *
    * @updated -
    *
    * @param Integer request->id - Banner Id
    *
    * @return HTML view page with new Object Banner Ad
    */
    public function banner_ads_view(Request $request) {

        $model = BannerAd::find($request->id);

        if (!$model) {

            return back()->with('flash_error', tr('something_error'));

        } else {

            return view('admin.banner_ads.view')->with('model', $model)->with('page', 'bannerads_nav')->with('sub_page', 'bannerads-index');

        }

    }

   /**
    * Function Name: banner_ads()
    *
    * @uses Display all the banner ads list 
    *
    * @created vithya
    *
    * @updated -
    *
    * @param - 
    *
    * @return banner ads list
    */
    public function banner_ads(Request $request) {

        $model = BannerAd::orderBy('position' , 'asc')->get();

        return view('admin.banner_ads.index')
                    ->with('model', $model)
                    ->with('page', 'bannerads_nav')
                    ->with('sub_page', 'bannerads-index');
    
    }

   /**
    * Function Name: banner_ads_delete()
    *
    * @uses Delete banner Ad based on banner Id
    *
    * @created vithya
    *
    * @updated -
    *
    * @param Object $request - Banner Id 
    *
    * @return Banner Ad response of success/failure response
    */
    public function banner_ads_delete(Request $request) {

        $model = BannerAd::find($request->id);

        if (!$model) {

            return back()->with('flash_error', tr('something_error'));

        } else {

            // Check the current position 

            $current_position = $model->position;

            $banner = BannerAd::orderBy('position', 'desc')->first();

            $last_position = $banner ? $banner->position : "";

            if($last_position == $current_position) {

                // No need to do anything

            } else if($current_position < $last_position) {

                // Update remaining records positions

                DB::update("UPDATE banner_ads SET position =  position-1 WHERE position > $current_position");

            }

            Helper::delete_picture($model->file, '/uploads/banners/');

            $model->delete();

            return back()->with('flash_success', tr('banner_delete_success'));

        }

    }

   /**
    * Function Name: banner_ads_status()
    *
    * @uses To change the Banner Ad status of Approve/Decline status
    *
    * @created vithya
    *
    * @updated -
    *
    * @param Object $request - Banner Id 
    *
    * @return Banner Ad response of success/failure response
    */
    public function banner_ads_status($id) {

        $model = BannerAd::find($id);

        if (!$model) {

            return back()->with('flash_error', tr('something_error'));

        } else {

            $model->status = $model->status ? DEFAULT_FALSE : DEFAULT_TRUE;

            $model->save();

            return back()->with('flash_success', $model->status ? tr('banner_approve_success') : tr('banner_decline_success'));

        }

    }

   /**
    * Function Name: banner_ads_position()
    *
    * @uses To change the Banner Ad position 
    *
    * @created vithya
    *
    * @updated -
    *
    * @param Object $request - Banner Id 
    *
    * @return Banner Ad response of success/failure response
    */
    public function banner_ads_position(Request $request) {

        $model = BannerAd::find($request->id);

        if (!$model) {

            return back()->with('flash_error', tr('something_error'));

        } else {

            $position = $model->position;

            $current_position = $request->position;

            // Load Current Position Banner

            $banner = BannerAd::where('position', $current_position)->first();

            if (!$banner) {

                return back()->with('flash_error', tr('current_position_banner_ad_not_available'));

            } else {

                $banner->position = $model->position;

                $banner->save();

                if ($banner) {

                    $model->position = $current_position;

                    $model->save();

                    if ($model) {


                    } else {

                        return back()->with('flash_error', tr('something_error'));

                    }

                } else {

                    return back()->with('flash_error', tr('something_error'));
                }

            }

            return back()->with('flash_success', tr('banner_position_success'));

        }
    
    }


   /**
    * Function Name: subscriptions()
    *
    * @uses To list out subscription details
    *
    * @created vithya
    *
    * @updated -
    *
    * @param - 
    *
    * @return HTML view page with subscription details
    */
    public function subscriptions() {

        $data = Subscription::orderBy('created_at','desc')->get();

        return view('admin.subscriptions.index')->withPage('subscriptions')
                        ->with('data' , $data)
                        ->with('sub_page','subscriptions-view');        

    }

   /**
    * Function Name: subscription_create()
    *
    * @uses To create subscription details 
    *
    * @created vithya
    *
    * @updated -
    *
    * @param - 
    *
    * @return HTML view page 
    */
    public function subscription_create() {

        $data = new Subscription;

        return view('admin.subscriptions.create')->with('page' , 'subscriptions')
                    ->with('sub_page','subscriptions-add')
                    ->with('data', $data);
    }

   /**
    * Function Name: subscription_edit()
    *
    * @uses To create subscription details 
    *
    * @created vithya
    *
    * @updated -
    *
    * @param - 
    *
    * @return HTML view page 
    */
    public function subscription_edit($unique_id) {

        $data = Subscription::where('unique_id' ,$unique_id)->first();

        return view('admin.subscriptions.edit')->withData($data)
                    ->with('sub_page','subscriptions-view')
                    ->with('page' , 'subscriptions ');

    }


   /**
    * Function Name: subscription_save()
    *
    * @uses To save the subscription details of new /old object based on details
    *
    * @created vithya
    *
    * @updated -
    *
    * @param object $request - Subscription details
    *
    * @return response of success/failure details
    */
    public function subscription_save(Request $request) {

        $validator = Validator::make($request->all(),[
                'title' => 'required|max:255',
                'plan' => 'required',
                'amount' => 'required',
                'image' => 'mimes:jpeg,png,jpg'
        ]);
        
        if($validator->fails()) {

            $error_messages = implode(',', $validator->messages()->all());

            return back()->with('flash_errors', $error_messages);

        } else {

            if($request->id != '') {

                $model = Subscription::find($request->id);


                if($request->hasFile('image')) {

                    $model->picture ? Helper::delete_picture('uploads/subscriptions' , $model->picture) : "";

                    $picture = Helper::upload_avatar('uploads/subscriptions' , $request->file('image'));
                    
                    $request->request->add(['picture' => $picture , 'image' => '']);

                }

                $model->update($request->all());

            } else {

                if($request->hasFile('image')) {

                    $picture = Helper::upload_avatar('uploads/subscriptions' , $request->file('image'));

                    $request->request->add(['picture' => $picture , 'image'=> '']);
                }

                $model = Subscription::create($request->all());

                $model->status = 1;

                $model->unique_id = $request->title;

                $model->save();
            }
        
            if($model) {
                return redirect(route('admin.subscriptions.view', $model->unique_id))->with('flash_success', $request->id ? tr('subscription_update_success') : tr('subscription_create_success'));

            } else {
                return back()->with('flash_error',tr('admin_not_error'));
            }
        }
    
        
    }

   /**
    * Function Name: subscription_view()
    *
    * @uses to view the subscription details based on subscription id
    *
    * @created vithya
    *
    * @updated -
    *
    * @param integer $request - Unique id of subscription
    *
    * @return response of success/failure details
    */
    public function subscription_view($unique_id) {

        if($data = Subscription::where('unique_id' , $unique_id)->first()) {

            return view('admin.subscriptions.view')
                        ->with('data' , $data)
                        ->withPage('subscriptions')
                        ->with('sub_page','subscriptions-view');

        } else {
            return back()->with('flash_error',tr('admin_not_error'));
        }
   
    }

   /**
    * Function Name: subscription_delete()
    *
    * @uses To delete a particualr subscrption details based on id
    *
    * @created vithya
    *
    * @updated -
    *
    * @param integer $request - Subscription Id
    *
    * @return response of success/failure details
    */
    public function subscription_delete($id) {

        if($data = Subscription::where('id',$id)->first()->delete()) {

            return back()->with('flash_success',tr('subscription_delete_success'));

        } else {
            return back()->with('flash_error',tr('admin_not_error'));
        }
        
    }

   /**
    * Function Name: subscription_status()
    *
    * @uses To change the status of subscription (Approve/decline)
    *
    * @created vithya
    *
    * @updated -
    *
    * @param integer $request - Subscription Unique id
    *
    * @return response of success/failure details
    */
    public function subscription_status($unique_id) {

        if($data = Subscription::where('unique_id' , $unique_id)->first()) {

            $data->status  = $data->status ? 0 : 1;

            $data->save();

            return back()->with('flash_success' , $data->status ? tr('subscription_approve_success') : tr('subscription_decline_success'));

        } else {

            return back()->with('flash_error',tr('admin_not_error'));
            
        }
    }

   /**
    * Function Name: coupon_create()
    *
    * @uses Get the coupon add form fields
    *
    * @created Maheswari
    *
    * @updated Maheswari
    *
    * @param Get the route of add coupon form
    *
    * @return Html form page
    */
    public function coupon_create(){

       return view('admin.coupons.create')
                ->with('page','coupons')
                ->with('sub_page','add-coupons');
    
    }

   /**
    * Function Name: coupon_save()
    *
    * @uses Save/Update the coupon details in database 
    *
    * @created Maheswari
    *
    * @updated Maheswari
    *
    * @param Request to all the coupon details
    *
    * @return add details for success message
    */
    public function coupon_save(Request $request){
        
        $validator = Validator::make($request->all(),[
            'id'=>'exists:coupons,id',
            'title'=>'required',
            'coupon_code'=>$request->id ? 'required|max:10|min:1|unique:coupons,coupon_code,'.$request->id : 'required|unique:coupons,coupon_code|min:1|max:10',
            'amount'=>'required|numeric|min:1|max:5000',
            'amount_type'=>'required',
            'expiry_date'=>'required|date_format:d-m-Y|after:today',
            'no_of_users_limit'=>'required|numeric|min:1|max:1000',
            'per_users_limit'=>'required|numeric|min:1|max:100',
        ]);

        if($validator->fails()){

            $error_messages = implode(',',$validator->messages()->all());

            return back()->with('flash_error',$error_messages);
        }
        if($request->id !=''){
                    
               
                $coupon_detail = Coupon::find($request->id); 

                $message=tr('coupon_update_success');

        } else {

            $coupon_detail = new Coupon;

            $coupon_detail->status = DEFAULT_TRUE;

            $message = tr('coupon_add_success');
        }

        // Check the condition amount type equal zero mean percentage
        if($request->amount_type == PERCENTAGE){

            // Amount type zero must should be amount less than or equal 100 only
            if($request->amount <= 100){

                $coupon_detail->amount_type = $request->has('amount_type') ? $request->amount_type :0;
 
                $coupon_detail->amount = $request->has('amount') ?  $request->amount : '';

            } else{

                return back()->with('flash_error',tr('coupon_amount_lessthan_100'));
            }

        } else{

            // This else condition is absoulte amount 

            // Amount type one must should be amount less than or equal 5000 only
            if($request->amount <= 5000){

                $coupon_detail->amount_type=$request->has('amount_type') ? $request->amount_type : 1;

                $coupon_detail->amount=$request->has('amount') ?  $request->amount : '';

            } else{

                return back()->with('flash_error',tr('coupon_amount_lessthan_5000'));
            }
        }
        $coupon_detail->title=ucfirst($request->title);

        // Remove the string space and special characters
        $coupon_code_format  = preg_replace("/[^A-Za-z0-9\-]+/", "", $request->coupon_code);

        // Replace the string uppercase format
        $coupon_detail->coupon_code = strtoupper($coupon_code_format);

        // Convert date format year,month,date purpose of database storing
        $coupon_detail->expiry_date = date('Y-m-d',strtotime($request->expiry_date));
      
        $coupon_detail->description = $request->has('description')? $request->description : '' ;

        // Based no users limit need to apply coupons
        
        $coupon_detail->no_of_users_limit = $request->no_of_users_limit;

        $coupon_detail->per_users_limit = $request->per_users_limit;

        if($coupon_detail){

            $coupon_detail->save(); 

            return back()->with('flash_success',$message);

        } else {

            return back()->with('flash_error',tr('coupon_not_found_error'));
        }
        
    }

   /**
    * Function Name: coupon_index()
    *
    * @uses Get the coupon details for all 
    *
    * @created Maheswari
    *
    * @updated Maheswari
    *
    * @param Get the coupon list in table
    *
    * @return Html table from coupon list page
    */
    public function coupon_index(){

        $coupon_index = Coupon::orderBy('updated_at','desc')->get();

        if($coupon_index){

            return view('admin.coupons.index')
                ->with('coupon_index',$coupon_index)
                ->with('page','coupons')
                ->with('sub_page','view_coupons');
        } else{

            return back()->with('flash_error',tr('coupon_not_found_error'));
        }
    
    }

   /**
    * Function Name: coupon_edit() 
    *
    * @uses Edit the coupon details and get the coupon edit form for 
    *
    * @created Maheswari
    *
    * @updated Maheswari
    *
    * @param Coupon id
    *
    * @return Get the html form
    */
    public function coupon_edit($id){

        $edit_coupon = Coupon::find($id);

        if($edit_coupon){

            return view('admin.coupons.edit')
                        ->with('edit_coupon',$edit_coupon)
                        ->with('page','coupons')
                        ->with('sub_page','add-coupons');

        } else{

            return back()->with('flash_error',tr('coupon_id_not_found_error'));
        }
    
    }

   /**
    * Function Name: coupon_delete()
    *
    * @uses Delete the particular coupon detail
    *
    * @created Maheswari
    *
    * @updated Maheswari
    *
    * @param Coupon id
    *
    * @return Deleted Success message
    */
    public function coupon_delete($id){

        $delete_coupon = Coupon::find($id);

        if($delete_coupon){

            $delete_coupon->delete();

            return back()->with('flash_success',tr('coupon_delete_success'));
            
        } else{

            return back()->with('flash_error',tr('coupon_id_not_found_error'));
        }
    
    }

   /**
    * Function Name: coupon_status_change()
    * 
    * @uses Coupon status for active and inactive update the status function
    *
    * @created Maheswari
    *
    * @updated Maheswari
    *
    * @param Request the coupon id
    *
    * @return Success message for active/inactive
    */
    public function coupon_status_change(Request $request){

        if($request->id){

            $coupon_status = Coupon::find($request->id);

            if($coupon_status) {

                $coupon_status->status = $request->status;

                $coupon_status->save();

            } else {

                return back()->with('flash_error',tr('coupon_not_found_error'));

            }

            if($request->status==DEFAULT_FALSE){

                $message = tr('coupon_inactive_success');

            } 

            if($request->status==DEFAULT_TRUE){

                $message = tr('coupon_active_success');
            }

            return back()->with('flash_success',$message);

        } else{

            return back()->with('flash_error',tr('coupon_id_not_found_error'));
        }
    
    }

   /**
    * Function Name: coupon_view()
    *
    * @uses Get the particular coupon details for view page content
    *
    * @created Maheswari
    *
    * @updated Maheswaari
    *
    * @param Coupon id
    *
    * @return Html view page with coupon detail
    */
    public function coupon_view($id){

        $view_coupon = Coupon::find($id);

        if($view_coupon){

            return view('admin.coupons.view')
                ->with('view_coupon',$view_coupon)
                ->with('page','coupons')
                ->with('sub_page','view_coupons');
            

        } else {

            return back()->with('flash_error',tr('coupon_id_not_found_error'));

        }
    
    }


   /**
    * Function Name: user_redeem_requests()
    *
    * @uses To list out the all the redeem requests from the users, admin can payout amount
    *
    * @created vithya
    *
    * @updated -
    *
    * @param integer $id - Optional ( Redeem request id)
    *
    * @return Html view page with redeem request details
    */
    public function user_redeem_requests($id = "") {

        $base_query = RedeemRequest::orderBy('status' , 'asc');

        $user = [];

        if($id) {

            $base_query = $base_query->where('user_id' , $id);

            $user = User::find($id);

        }

        $data = $base_query->orderBy('updated_at', 'desc')->get();

        return view('admin.users.redeems')
                ->withPage('redeems')
                ->with('sub_page' , 'redeems')
                ->with('data' , $data)->with('user' , $user);
    
    }


   /**
    * Function Name: user_redeem_pay() NOT USED 
    *
    * @uses Pay the amount to the users.
    *
    * @created vithya
    *
    * @updated -
    *
    * @param amount $request - Amount, Request Id
    *
    * @return response of success/failure response details
    */
    public function user_redeem_pay(Request $request) {

        $validator = Validator::make($request->all() , [
            'redeem_request_id' => 'required|exists:redeem_requests,id',
            'paid_amount' => 'required', 
            ]);

        if($validator->fails()) {

            return back()->with('flash_error' , $validator->messages()->all())->withInput();

        } else {

            $redeem_request_details = RedeemRequest::find($request->redeem_request_id);

            if($redeem_request_details) {

                if($redeem_request_details->status == REDEEM_REQUEST_PAID ) {

                    return back()->with('flash_error' , tr('redeem_request_status_mismatch'));

                }


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

                } else {

                    $message = tr('redeems_request_admin_less_amount');

                    $redeem_amount = 0; // To restrict the redeeem paid amount update

                }

                $redeem_details = Redeem::where('user_id' , $redeem_request_details->user_id)->first();

                if(count($redeem_details) > 0 ) {

                    $redeem_details->paid = $redeem_details->paid + $redeem_amount;

                    $redeem_details->save();
                }

                return back()->with('flash_success' , $message);

            } else {
                return back()->with('flash_error' , tr('something_error'));
            }
        }

    }


   /**
    * Function Name: revenues()
    *
    * @uses To list out the details of the revenue models
    *
    * @created vithya
    *
    * @updated -
    *
    * @param -
    *
    * @return Html view page with revenues detail
    */
    public function revenues() {

        $total  = total_revenue();

        $subscription_total = UserPayment::sum('amount');

        $total_subscribers = UserPayment::where('status' ,  PAID_STATUS)->count();

        $admin_ppv_amount = VideoTape::sum('admin_ppv_amount');

        $user_ppv_amount = VideoTape::sum('user_ppv_amount');

        $total_ppv_amount = $admin_ppv_amount + $user_ppv_amount;


        $admin_live_amount = LiveVideoPayment::sum('admin_amount');

        $user_live_amount = LiveVideoPayment::sum('user_amount');

        $total_live_amount = $admin_live_amount + $user_live_amount;


        
        return view('admin.payments.revenues')
                        ->with('total' , $total)
                        ->with('subscription_total' , $subscription_total)
                        ->with('total_subscribers' , $total_subscribers)
                        ->with('ppv_admin_amount', $admin_ppv_amount)
                        ->with('ppv_user_amount', $user_ppv_amount)
                        ->with('ppv_total', $total_ppv_amount)
                        ->with('admin_live_amount', $admin_live_amount)
                        ->with('user_live_amount', $user_live_amount)
                        ->with('total_live_amount', $total_live_amount)
                        ->withPage('payments')
                        ->with('sub_page' , 'payments-dashboard');
    
    }

   /**
    * Function Name: subscription_payments()
    *
    * @uses Used to display the subscription payments details List or 
    * 
    * Based on the particuler subscriptions details
    *
    * @created vithya
    *
    * @updated -
    *
    * @param -
    *
    * @return Html view page with subscription detail
    */
    public function subscription_payments($id = "") {

        $base_query = UserPayment::orderBy('created_at' , 'desc');

        $subscription = [];

        if($id) {

            $subscription = Subscription::find($id);

            $base_query = $base_query->where('subscription_id' , $id);

        }

        $payments = $base_query->get();

        return view('admin.payments.subscription-payments')
                ->with('data' , $payments)
                ->withPage('payments')
                ->with('sub_page','payments-subscriptions')
                ->with('subscription' , $subscription); 
    
    }

   /**
    * Function Name: ppv_payments()
    *
    * @uses To list out ppv payment details
    *     
    * @created vithya
    *
    * @updated -
    *
    * @param -
    *
    * @return Html view page with ppv details
    */
    public function ppv_payments() {
             $payments = PayPerView::select('pay_per_views.*', 'video_tapes.title', 'users.name as user_name')
            ->leftJoin('video_tapes', 'video_tapes.id', '=', 'pay_per_views.video_id')
            ->leftJoin('users', 'users.id', '=', 'pay_per_views.user_id')
            ->orderBy('pay_per_views.created_at' , 'desc')->get();

        return view('admin.payments.ppv-payments')->with('data' , $payments)->withPage('payments')->with('sub_page','payments-ppv');
    }


   /**
    * Function Name: settings()
    *
    * @uses To display the settings value from settings table
    *
    * @created vithya
    *
    * @updated -
    *
    * @param -
    *
    * @return Html view page with settings detail
    */
    public function settings() {

        $settings = array();

        $result = EnvEditorHelper::getEnvValues();

        return view('admin.settings.settings')->with('settings' , $settings)->with('result', $result)->withPage('settings')->with('sub_page',''); 
    
    }

   /**
    * Function Name: settings_process()
    *
    * @uses To save the settings table values
    *
    * @created vithya
    *
    * @updated -
    *
    * @param object $request - Settings object values
    *
    * @return response of Success / Failure Response details
    */
    public function settings_process(Request $request) {

        $settings = Settings::all();

        $check_streaming_url = "";

        if($settings) {

            foreach ($settings as $setting) {

                $key = $setting->key;
               
                if($setting->key == 'site_name') {

                    if($request->has('site_name')) {
                        
                        $setting->value = $request->site_name;

                        $site_name = preg_replace('/[^A-Za-z0-9\-]/', '', $request->site_name);

                        \Enveditor::set('SITENAME',$site_name);
                    
                    }
                    
                } else if($setting->key == 'site_icon') {

                    if($request->hasFile('site_icon')) {
                        
                        if($setting->value) {
                            Helper::delete_picture($setting->value, "/uploads/images/");
                        }

                        $setting->value = Helper::normal_upload_picture($request->file('site_icon'), "/uploads/images/");
                    
                    }
                    
                } else if($setting->key == 'site_logo') {

                    if($request->hasFile('site_logo')) {

                        if($setting->value) {

                            Helper::delete_picture($setting->value, "/uploads/images/");
                        }

                        $setting->value = Helper::normal_upload_picture($request->file('site_logo'),"/uploads/images/");
                    }

                } else if($setting->key == 'streaming_url') {

                    if(check_nginx_configure()) {

                        $setting->value = $request->streaming_url;

                    } else {

                        $setting->value = "";

                        $check_streaming_url = " !! ====> Please Configure the Nginx Streaming Server.";
                    }

                } else if($setting->key == 'HLS_STREAMING_URL') {

                    if(check_nginx_configure()) {
                        $setting->value = $request->HLS_STREAMING_URL;
                    } else {
                        $check_streaming_url = " !! ====> Please Configure the Nginx Streaming Server.";
                        $setting->value = "";
                    }
                    
                } else if($setting->key == 'multi_channel_status') {

                    $setting->value = ($request->multi_channel_status) ? (($request->multi_channel_status == 'on') ? DEFAULT_TRUE : DEFAULT_FALSE) : DEFAULT_FALSE;

                } else if($setting->key == "admin_ppv_commission") {

                    $setting->value = $request->admin_ppv_commission < 100 ? $request->admin_ppv_commission : 100;

                    $user_ppv_commission = $request->admin_ppv_commission < 100 ? 100 - $request->admin_ppv_commission : 0;

                    $user_ppv_commission_details = Settings::where('key' , 'user_ppv_commission')->first();

                    if(count($user_ppv_commission_details) > 0) {

                        $user_ppv_commission_details->value = $user_ppv_commission;


                        $user_ppv_commission_details->save();
                    }


                } else {

                    if (isset($_REQUEST[$key])) {

                        $setting->value = $request->$key;

                    }

                }

                $setting->save();
            
            }
        }
        
        $message = "Settings Updated Successfully"." ".$check_streaming_url;
        
        // return back()->with('setting', $settings)->with('flash_success', $message);

        $result = EnvEditorHelper::getEnvValues();

        return redirect(route('clear-cache'))->with('result' , $result)->with('flash_success' , $message);    
    
    }

    /**
     * Function Name : save_common_settings
     *
     * Save the values in env file
     *
     * @created vithya
     *
     * @updated -
     *
     * @param object $request Post Attribute values
     * 
     * @return settings values
     */
    public function save_common_settings(Request $request) {

        $admin_id = \Auth::guard('admin')->user()->id;

        foreach ($request->all() as $key => $data) {

            if($request->has($key)) {

                if ($key == 'stripe_publishable_key') {

                    $setting = Settings::where('key', $key)->first();

                    if ($setting) {

                        $setting->value = $data;

                        $setting->save();

                    }

                } else if ($key == 'stripe_secret_key') {

                    $setting = Settings::where('key', $key)->first();

                    if ($setting) {

                        $setting->value = $data;

                        $setting->save();

                    }

                } else {

                    \Enveditor::set($key,$data);

                }      

            }
        }

        $check_streaming_url = "";

        $settings = Settings::all();

        if($settings) {

            foreach ($settings as $setting) {

                $key = $setting->key;

                if($request->$key!='') {

                    if($setting->key == 'streaming_url') {

                        if($request->has('streaming_url') && $request->streaming_url != $setting->value) {

                            if(check_nginx_configure()) {
                                $setting->value = $request->streaming_url;
                            } else {
                                $check_streaming_url = " !! ====> Please Configure the Nginx Streaming Server.";
                            }
                        }  

                    }

                    if($setting->key == 'HLS_STREAMING_URL') {

                        if($request->has('HLS_STREAMING_URL') && $request->HLS_STREAMING_URL != $setting->value) {

                            if(check_nginx_configure()) {
                                $setting->value = $request->HLS_STREAMING_URL;
                            } else {
                                $check_streaming_url = " !! ====> Please Configure the Nginx Streaming Server.";
                            }
                        }  

                    }

                    $setting->value = $request->$key;

                }

                $setting->save();

            }
        
        }


        $result = EnvEditorHelper::getEnvValues();

        $message = tr('common_settings_success')." ".$check_streaming_url;

        return redirect(route('clear-cache'))->with('result' , $result)->with('flash_success' , $message);
    
    }

    /**
    * Function Name: email_settings_process()
    *
    * @uses Email Setting Process
    *
    * @created vithya
    *
    * @updated -
    *
    * @return Html view page with coupon detail
    */
    public function email_settings_process(Request $request) {

        $email_settings = ['MAIL_DRIVER' , 'MAIL_HOST' , 'MAIL_PORT' , 'MAIL_USERNAME' , 'MAIL_PASSWORD' , 'MAIL_ENCRYPTION'];

        $admin_id = \Auth::guard('admin')->user()->id;


        foreach ($email_settings as $key => $data) {

            \Enveditor::set($data,$request->$data);
            
        }

        return redirect(route('clear-cache'))->with('flash_success' , tr('email_settings_success'));

    }

   /**
    * Function Name: custom_push()
    *
    * @uses To display custom message
    *
    * @created vithya
    *
    * @updated -
    *
    * @param - 
    *
    * @return response of HTML page view
    */
    public function custom_push() {

        return view('admin.static.push')->with('title' , "Custom Push")->with('page' , "custom-push");

    }

   /**
    * Function Name: custom_push_process()
    *
    * @uses To send custom push message to mobile
    *
    * @created vithya
    *
    * @updated -
    *
    * @param object $request - message details
    *
    * @return response of flash success/failure message
    */
    public function custom_push_process(Request $request) {

        $validator = Validator::make(
            $request->all(),
            array( 'message' => 'required')
        );

        if($validator->fails()) {

            $error = $validator->messages()->all();

            return back()->with('flash_errors',$error);

        } else {
            // Send notifications to the users
            $title = $content = $request->message;

            // dispatch(new sendPushNotification(PUSH_TO_ALL , $push_message , PUSH_REDIRECT_SINGLE_VIDEO , 29, 0, [] , PUSH_TO_CHANNEL_SUBSCRIBERS ));

            // dispatch(new sendPushNotification(PUSH_TO_ALL,$push_message,PUSH_REDIRECT_HOME,0));

            $push_data = ['type' => PUSH_REDIRECT_HOME];

            dispatch(new sendPushNotification(PUSH_TO_ALL , $title , $content, PUSH_REDIRECT_HOME , 0, 0, $push_data));

            return back()->with('flash_success' , tr('push_send_success'));
        }
    
    }

   /**
    * Function Name: pages()
    *
    * @uses To load all the statc pages which is created by admin
    *
    * @created vithya
    *
    * @updated -
    *
    * @param - 
    *
    * @return Html view page with page detail
    */
    public function pages() {

        $all_pages = Page::all();

        return view('admin.pages.index')
                ->with('page',"viewpages")
                ->with('sub_page','view_pages')
                ->with('data',$all_pages);
    
    }

   /**
    * Function Name: pages_create()
    *
    * @uses To create a new page of staitc page using type
    *
    * @created vithya
    *
    * @updated -
    *
    * @param - 
    *
    * @return Html view page with page detail
    */
    public function pages_create() {

        $all = Page::all();

        return view('admin.pages.create')
                ->with('page' , 'viewpages')->with('sub_page',"add_page")
                ->with('view_pages',$all);
    
    }

   /**
    * Function Name: pages_edit()
    *
    * @uses To edit a existing page of static page using type
    *
    * @created vithya
    *
    * @updated -
    *
    * @param integer $id = Page id
    *
    * @return Html view page with page detail
    */
    public function pages_edit($id) {

        $data = Page::find($id);

        if($data) {
            return view('admin.pages.edit')
                    ->withPage('viewpages')->with('sub_page',"view_pages")
                    ->with('data',$data);
        } else {

            return back()->with('flash_error',tr('something_error'));

        }
    
    }

   /**
    * Function Name: pages_view()
    *
    * @uses To view a existing page of static page 
    *
    * @created vithya
    *
    * @updated -
    *
    * @param integer $id = Page id
    *
    * @return Html view page with page detail
    */
    public function pages_view($id) {

        $data = Page::find($id);

        if($data) {
            return view('admin.pages.view')
                    ->withPage('viewpages')->with('sub_page',"view_pages")
                    ->with('data',$data);
        } else {

            return back()->with('flash_error',tr('something_error'));

        }
    
    }

   /**
    * Function Name: pages_save()
    *
    * @uses To save new page / existing  page post details
    *
    * @created vithya
    *
    * @updated -
    *
    * @param integer $request - Postdetails of static page
    *
    * @return Html view page with page detail
    */
    public function pages_save(Request $request) {

        if($request->has('id')) {
            $validator = Validator::make($request->all() , array(
                'title' => '',
                'heading' => 'required',
                'description' => 'required'
            ));
        } else {
            $validator = Validator::make($request->all() , array(
                'type' => 'required',
                'title' => 'required|max:255|unique:pages',
                'heading' => 'required',
                'description' => 'required'
            ));
        }

        if($validator->fails()) {
            $error = implode('',$validator->messages()->all());
            return back()->with('flash_errors',$error);
        } else {

            if($request->has('id')) {
                $pages = Page::find($request->id);
            } else {
                if(Page::count() <= 5) {
                    if($request->type != 'others') {
                        $check_page_type = Page::where('type',$request->type)->first();
                        if($check_page_type){
                            return back()->with('flash_error',"You have already created $request->type page");
                        }
                    }
                    
                    
                    $pages = new Page;

                    $check_page = Page::where('title',$request->title)->first();
                    
                    if($check_page) {
                        return back()->with('flash_error',tr('page_already_alert'));
                    }
                }else {
                    return back()->with('flash_error',tr('cannot_create_more_pages'));
                }
                
            }
            if($pages) {

                $pages->type = $request->type ? $request->type : $pages->type;
                $pages->title = $request->title ? $request->title : $pages->title;
                $pages->heading = $request->heading ? $request->heading : $pages->heading;
                $pages->description = $request->description ? $request->description : $pages->description;
                $pages->save();
            }
            if($pages) {
                return back()->with('flash_success',tr('page_create_success'));
            } else {
                return back()->with('flash_error',tr('something_error'));
            }
        }
    
    }

   /**
    * Function Name: pages_delete()
    *
    * @uses To delete page based on static page id
    *
    * @created vithya
    *
    * @updated -
    *
    * @param integer $id - page id
    *
    * @return Html view page with page detail
    */
    public function pages_delete($id) {

        $page = Page::where('id',$id)->delete();

        if($page) {

            return back()->with('flash_success',tr('page_delete_success'));

        } else {

            return back()->with('flash_error',tr('something_error'));

        }
    
    }

   /**
    * Function Name: profile()
    *
    * Admin profile page 
    *
    * @created vithya
    *
    * @updated -
    *
    * @param -- 
    *
    * @return Html view page with admin details
    */
    public function profile() {

        $admin = Admin::first();

        return view('admin.account.profile')
                ->with('admin' , $admin)->withPage('profile')->with('sub_page','');
    
    }

   /**
    * Function Name: profile_process()
    *
    * Admin profile page 
    *
    * @created vithya
    *
    * @updated -
    *
    * @param -- 
    *
    * @return Html view page with admin details
    */
    public function profile_process(Request $request) {

        $validator = Validator::make( $request->all(),array(
                'name' => 'max:255',
                'email' => $request->id ? 'email|max:255|unique:admins,email,'.$request->id : 'email|max:255|unique:admins,email,NULL',
                'mobile' => 'digits_between:6,13',
                'address' => 'max:300',
                'id' => 'required|exists:admins,id',
                'picture' => 'mimes:jpeg,jpg,png'
            )
        );
        
        if($validator->fails()) {

            $error_messages = implode(',', $validator->messages()->all());

            return back()->with('flash_errors', $error_messages);
        } else {
            
            $admin = Admin::find($request->id);
            
            $admin->name = $request->has('name') ? $request->name : $admin->name;

            $admin->email = $request->has('email') ? $request->email : $admin->email;

            $admin->mobile = $request->has('mobile') ? $request->mobile : $admin->mobile;

            $admin->gender = $request->has('gender') ? $request->gender : $admin->gender;

            $admin->address = $request->has('address') ? $request->address : $admin->address;

            if($request->hasFile('picture')) {

                Helper::delete_picture($admin->picture, "/uploads/images/");

                $admin->picture = Helper::normal_upload_picture($request->picture, "/uploads/images/");
            }
                
            $admin->remember_token = Helper::generate_token();
            
            $admin->save();

            return back()->with('flash_success', tr('admin_not_profile'));
            
        }
    
    }

   /**
    * Function Name: change_password()
    *
    * Admin  - Change password page, he can change his password
    *
    * @created vithya
    *
    * @updated -
    *
    * @param -- 
    *
    * @return Html view page with admin details
    */
    public function change_password(Request $request) {

        $old_password = $request->old_password;
        $new_password = $request->password;
        $confirm_password = $request->confirm_password;
        
        $validator = Validator::make($request->all(), [              
                'password' => 'required|min:6',
                'old_password' => 'required',
                'confirm_password' => 'required|min:6',
                'id' => 'required|exists:admins,id'
            ]);

        if($validator->fails()) {

            $error_messages = implode(',',$validator->messages()->all());

            return back()->with('flash_errors', $error_messages);

        } else {

            $admin = Admin::find($request->id);

            if(\Hash::check($old_password,$admin->password))
            {
                $admin->password = \Hash::make($new_password);

                $admin->save();

                return back()->with('flash_success', tr('password_change_success'));
                
            } else {

                return back()->with('flash_error', tr('password_mismatch'));

            }
        }

        $response = response()->json($response_array,$response_code);

        return $response;
    
    }


   /**
    * Function Name: help()
    *
    * Help page for admin 
    *
    * @created vithya
    *
    * @updated -
    *
    * @param -- 
    *
    * @return Html view page with help content
    */
    public function help() {

        return view('admin.static.help')->withPage('help')->with('sub_page' , "");

    }


    /**
     * Function Name : categories_create()
     * 
     * To create a category 
     *
     * @param - $request - As of now no attribute
     *
     * @return response of html page with details
     *
     */
    public function categories_create(Request $request) {

        $model = new Category;

        return view('admin.categories.create')->with('page', 'categories')
                    ->with('sub_page', 'create_category')
                    ->with('model', $model);
    }


    /**
     * Function Name : categories_edit()
     * 
     * To edit a category based on id 
     *
     * @param integer $request - Category id
     *
     * @return response of html page with details
     *
     */
    public function categories_edit(Request $request) {

        $model = Category::where('id', $request->id)->first();

        if ($model) {

            return view('admin.categories.edit')
                    ->with('page', 'categories')
                    ->with('sub_page', 'create_category')
                    ->with('model', $model);

        } else {

            return back()->with('flash_error', tr('category_not_found'));

        }
        
    }


    /**
     * Function Name : categories_list()
     *
     * To list out all the categories
     *
     * @param -
     * 
     * @return response of array list
     */
    public function categories_list(Request $request) {

        $datas = Category::orderBy('updated_at', 'desc')->withCount('getVideos')->get();

        return view('admin.categories.index')
                    ->with('page', 'categories')
                    ->with('sub_page', 'categories')
                    ->with('datas', $datas);
    }

    /**
     * Function Name : categories_save()
     *
     * To save the category
     *
     * @param - category object 
     *
     * @return response of success/failure message
     */
    public function categories_save(Request $request) {

        $validator = Validator::make($request->all() , [
            'name' => $request->id ? 'required|unique:categories,name,'.$request->id.',id|max:128|min:2' : 'required|unique:categories,name,NULL,id|max:128|min:2',
            'id' => 'exists:categories,id', 
            'image' => $request->id ? 'mimes:jpeg,jpg,bmp,png' : 'required|mimes:jpeg,jpg,bmp,png',
                'description'=>'required',
            ]);

        if($validator->fails()) {

            return back()->with('flash_error' , implode(',',$validator->messages()->all()));

        } else {

            $model = $request->id ? Category::find($request->id) : new Category;

            $model->name = $request->name;

            $model->unique_id = seoUrl($model->name);

            $model->description = $request->description;

            $model->status = DEFAULT_TRUE;

            if($request->hasFile('image')) {

                if ($request->id) {

                    Helper::delete_avatar('uploads/categories' ,$model->image);

                }

                $model->image = Helper::upload_avatar('uploads/categories',$request->file('image'), 0);
            }

            if ($model->save()) {


                if ($request->id) {

                    return redirect(route('admin.categories.view', ['category_id'=>$model->id]))->with('flash_success',tr('category_update_success'));

                } else {

                    return back()->with('flash_success',tr('category_create_success'));

                }

            } else {

                return back()->with('flash_error', tr('category_not_saved'));
            }

        }

    }

    /**
     * Function Name : categories_delete()
     *
     * To delete the category based on id
     *
     * @param integer $request - category id 
     *
     * @return response of success/failure message
     */
    public function categories_delete(Request $request) {

        $validator = Validator::make($request->all() , [
            'id' => 'required|exists:categories,id', 
        ]);

        if($validator->fails()) {

            return back()->with('flash_error' , $validator->messages()->all())->withInput();

        } else {

            $model = Category::find($request->id);

            $model_img = $model->image;

            if ($model->no_of_uploads > 0) {

                return back()->with('flash_error', tr('category_allocated'));

            }

            if ($model->delete()) {                

                Helper::delete_avatar('uploads/categories' ,$model_img);

                return back()->with('flash_success', tr('category_delete_success'));

            } else {

                return back()->with('flash_error', tr('category_not_deleted'));

            }

        }
    }

    /**
     * Function Name : categories_status()
     *
     * To change the category status approve/decline
     *
     * @param integer $request - category id 
     *
     * @return response of success/failure message
     */
    public function categories_status(Request $request) {

        $validator = Validator::make($request->all() , [
            'id' => 'required|exists:categories,id', 
        ]);

        if($validator->fails()) {

            return back()->with('flash_error' , $validator->messages()->all())->withInput();

        } else {

            $model = Category::find($request->id);

            $model->status = $model->status == CATEGORY_APPROVE_STATUS ? CATEGORY_DECLINE_STATUS : CATEGORY_APPROVE_STATUS;

            
            if ($model->status == CATEGORY_DECLINE_STATUS) {

                VideoTape::where('category_id', $model->id)->update(['is_approved'=>ADMIN_VIDEO_DECLINED_STATUS]);

            }

            if ($model->save()) {

                return back()->with('flash_success', $model->status ? tr('category_approve_success') : tr('category_decline_success'));

           } else {

                return back()->with('flash_error', tr('category_not_saved'));

           }

        }
    }

    /**
     * Function Name : create_tag()
     *
     * To create tag, displayed form
     *
     * @param object $request - As of now no attribute
     *
     * @return response of json
     */
    public function tags(Request $request) {

        $model = $request->id ? Tag::find($request->id) : new Tag;

        if (!$model) {

            $model = new Tag;

        }

        $datas = Tag::orderBy('created_at', 'desc')->get();

        return view('admin.tags.index')
                ->with('model', $model)
                ->with('datas', $datas)
                ->with('page', 'tags')
                ->with('sub_page', '');
    }

    /**
     * Function Name : tags_videos
     *
     * List of videos displayed and also based on tags
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param --
     * 
     * @return response of videos details
     *
     */
    public function tags_videos(Request $request) {

        $tag = Tag::find($request->id);

        if ($tag) {

            $videos = VideoTape::leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                        ->videoResponse()
                        ->leftjoin('video_tape_tags', 'video_tape_tags.video_tape_id', '=', 'video_tapes.id')
                        ->where('video_tape_tags.tag_id', $request->id)
                        ->orderBy('video_tapes.created_at' , 'desc')
                        ->groupBy('video_tape_tags.video_tape_id')
                        ->get();


            return view('admin.tags.videos')
                        ->with('videos' , $videos)
                        ->with('tag', $tag)
                        ->withPage('tags')
                        ->with('sub_page','tags');

        } else {

            return back()->with('flash_error', tr('tag_not_found'));

        }
   
    }

    /**
     * Function Name : save_tag()
     *
     * To save the tag
     *
     * @param - tag object 
     *
     * @return response of success/failure message
     */
    public function save_tag(Request $request) {

        $validator = Validator::make($request->all() , [
            'name' => $request->id ? 'required|max:128|min:2|unique:tags,name,'.$request->id.',id' : 
            'required|max:128|min:2|unique:tags,name,NULL,id',
            'id' => $request->id ? 'exists:tags,id' : '', 
        ]);

        if($validator->fails()) {

            return back()->with('flash_error' , implode(',',$validator->messages()->all()));

        } else {

            $model = $request->id ? Tag::find($request->id) : new Tag;

            $model->name = $request->name;

            $model->status = DEFAULT_TRUE;

            $model->search_count = 0;

            if ($model->save()) {

                if ($request->id) {

                    return redirect(route('admin.tags'))->with('flash_success',tr('tag_update_success'));

                } else {

                    return back()->with('flash_success',tr('tag_create_success'));

                }

            } else {

                return back()->with('flash_error', tr('something_error'));
            }

        }

    }

    /**
     * Function Name : delete_tag()
     *
     * To save the tag
     *
     * @param integer $request - tag id 
     *
     * @return response of success/failure message
     */
    public function delete_tag(Request $request) {

        $validator = Validator::make($request->all() , [
            'id' => 'required|exists:tags,id', 
        ]);

        if($validator->fails()) {

            return back()->with('flash_error' , $validator->messages()->all())->withInput();

        } else {

            $model = Tag::find($request->id);

            $model->delete();

            return back()->with('flash_success', tr('tag_delete_success'));

        }
    }

     /**
     * Function Name : tag_status()
     *
     * To save the tag
     *
     * @param integer $request - tag id 
     *
     * @return response of success/failure message
     */
    public function tag_status(Request $request) {

        $validator = Validator::make($request->all() , [
            'id' => 'required|exists:tags,id', 
        ]);

        if($validator->fails()) {

            return back()->with('flash_error' , $validator->messages()->all())->withInput();

        } else {

            $model = Tag::find($request->id);

            $model->status = $model->status == TAG_APPROVE_STATUS ? TAG_DECLINE_STATUS : TAG_APPROVE_STATUS;

            $model->save();

            if ($model->status == TAG_DECLINE_STATUS) {

                VideoTapeTag::where('tag_id', $model->id)->update(['status'=>TAG_DECLINE_STATUS]);

            } else {

                VideoTapeTag::where('tag_id', $model->id)->update(['status'=>TAG_APPROVE_STATUS]);
                
            }

            return back()->with('flash_success', $model->status ? tr('tag_approve_success') : tr('tag_decline_success'));

        }
    }

    /**
     * Function Name : categories_videos()
     *
     * @created vithya
     *
     * @updated -
     *
     * To display based on category
     *
     * @param object $request - User Details
     *
     * @return Response of videos list
     */
    public function categories_videos(Request $request) {

        $basicValidator = Validator::make(
                $request->all(),
                array(
                    'category_id' => 'required|exists:categories,id'
                )
        );

        if($basicValidator->fails()) {

            $error_messages = implode(',', $basicValidator->messages()->all());

            return back()->with('flash_error', $error_messages);

        } else {

            $videos = VideoTape::leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
            ->videoResponse()
            ->where('category_id', $request->category_id)
            ->get();

            $category = Category::find($request->category_id);

            return view('admin.categories.videos')
                        ->with('videos', $videos)
                        ->with('page', 'categories')
                        ->with('sub_page', 'categories')
                        ->with('category', $category);
                
        }
    
    }

    /**
     * Function Name : categories_channels
     *
     * To list out channels based on category
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param --
     * 
     * @return response of user channel details
     *
     */
    public function categories_channels(Request $request) {

        $basicValidator = Validator::make(
                $request->all(),
                array(
                    'category_id' => 'required|exists:categories,id'
                )
        );

        if($basicValidator->fails()) {

            $error_messages = implode(',', $basicValidator->messages()->all());

            return back()->with('flash_error', $error_messages);                

        } else {

            $category = Category::find($request->category_id);

            $channels_id = Channel::leftJoin('video_tapes', 'video_tapes.channel_id', '=', 'channels.id')
                        ->where('video_tapes.category_id', $request->category_id)->get()
                        ->pluck('channel_id')
                        ->toArray();

            $channels = Channel::orderBy('channels.created_at', 'desc')
                            ->distinct('channels.id')
                            ->withCount('getChannelSubscribers')
                            ->withCount('getVideoTape')
                            ->whereIn('id', $channels_id)
                            ->get();

            return view('admin.categories.channels')
                ->with('channels' , $channels)
                ->withPage('categories')
                ->with('category', $category)
                ->with('sub_page','categories');

        }
    
    }


    /**
     * Function Name ; categories_view()
     *
     * category details based on id
     *
     * @created vithya
     *
     * @updated -
     *
     * @param - 
     * 
     * @return response of json
     */
    public function categories_view(Request $request) {

        $basicValidator = Validator::make(
                $request->all(),
                array(
                    'category_id' => 'required|exists:categories,id'
                )
        );

        if($basicValidator->fails()) {

            $error_messages = implode(',', $basicValidator->messages()->all());

            return back()->with('flash_error', $error_messages);                

        } else {

            $model = Category::where('id', $request->category_id)
                ->withCount('getVideos')
                ->first();

            $no_of_channels = Channel::leftJoin('video_tapes', 'video_tapes.channel_id', '=', 'channels.id')
                    ->where('video_tapes.category_id', $request->category_id)
                    ->groupBy('video_tapes.channel_id')
                    ->get();

            // No of videos count

            $channels_list = Channel::select('channels.*', 'video_tapes.id as video_tape_id', 'video_tapes.is_approved',
                        'video_tapes.status', 'video_tapes.channel_id')
                    ->leftJoin('video_tapes', 'video_tapes.channel_id', '=', 'channels.id')
                    ->where('video_tapes.category_id', $request->category_id)
                    ->groupBy('video_tapes.channel_id')
                    ->skip(0)->take(Setting::get('admin_take_count', 12))->get();


            $channel_lists = [];

            foreach ($channels_list as $key => $value) {

                $channel_lists[] = [
                        'channel_id'=>$value->id, 
                        'user_id'=>$value->user_id,
                        'picture'=> $value->picture, 
                        'title'=>$value->name,
                        'description'=>$value->description, 
                        'created_at'=>$value->created_at->diffForHumans(),
                        'no_of_videos'=>videos_count($value->id),
                        'subscribe_status'=>$request->id ? check_channel_status($request->id, $value->id) : '',
                        'no_of_subscribers'=>$value->getChannelSubscribers()->count(),
                ];

            }

            $channel_lists = json_decode(json_encode($channel_lists));

            $category_videos = VideoTape::leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                            ->leftJoin('categories' , 'video_tapes.category_id' , '=' , 'categories.id')
                            ->videoResponse()
                            ->where('category_id', $request->category_id)
                            ->orderby('video_tapes.updated_at' , 'desc')
                            ->skip(0)->take(Setting::get('admin_take_count', 12))->get();


            $category_earnings = getAmountBasedChannel($model->id);

            return view('admin.categories.view')
                    ->with('category_videos', $category_videos)
                    ->with('channel_lists', $channel_lists)
                    ->with('category', $model)
                    ->with('category_earnings', $category_earnings)
                    ->with('no_of_channels', count($no_of_channels))
                    ->with('page', 'categories')
                    ->with('sub_page', 'categories');

        }

    }

    /**
     *
     * Function name: custom_live_videos()
     *
     * @uses custom live videos
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param
     *
     * @return Live videos details with view page
     */

    public function custom_live_videos() {

        $model = CustomLiveVideo::orderBy('created_at','desc')->get();

        return view('admin.custom_live_videos.index')
                        ->withPage('custom_live_videos')
                        ->with('model' , $model)
                        ->with('sub_page','custom_live_videos_index');
    }

    /**
     *
     * Function name: custom_live_videos_create()
     *
     * @uses custom live videos
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param
     *
     * @return create live video page
     */

    public function custom_live_videos_create() {

        $model = new CustomLiveVideo;

        return view('admin.custom_live_videos.create')->withModel($model)->with('page' , 'custom_live_videos')->with('sub_page','create_live_video');
    }

    /**
     *
     * Function name: custom_live_videos_edit()
     *
     * @uses edit live video page with selected record
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param
     *
     * @return edit live video page with selected record
     */
    public function custom_live_videos_edit(Request $request) {

        $model = CustomLiveVideo::find($request->id);

        if(count($model) == 0) {
            return redirect()->route('admin.custom.live')->with('flash_error' , tr('custom_live_video_not_found'));
        }

        return view('admin.custom_live_videos.edit')->withModel($model)->with('sub_page','custom_live_videos')->with('page' , 'create_live_video');
    }

    /**
     *
     * Function name: custom_live_videos_save()
     *
     * @uses save the form data of the live video
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param
     *
     * @return success/error message 
     */

    public function custom_live_videos_save(Request $request) {

        $response = AdminRepo::save_custom_live_video($request)->getData();

        if($response->success) {

            return redirect(route('admin.custom.live.view', $response->data->id))->with('flash_success', $response->message);

        } else {

            return back()->with('flash_error', $response->message);

        }

    }

    /**
     *
     * Function name: custom_live_videos_change_status()
     *
     * @uses update the status of the live video.
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param
     *
     * @return success/error message 
     */
    public function custom_live_videos_change_status(Request $request) {

        $model = CustomLiveVideo::find($request->id);

        if(count($model) == 0) {

            return redirect()->route('admin.custom.live')->with('flash_error' , tr('custom_live_video_not_found'));
        }

        $model->status = $model->status ?  DEFAULT_FALSE : DEFAULT_TRUE;

        $model->save();

        if($model->status ==1) {

            $message = tr('live_custom_video_approved_success');

        } else {

            $message = tr('live_custom_video_declined_success');

        }

        return back()->with('flash_success', $message);

    }

    /**
     *
     * Function name: custom_live_videos_delete()
     *
     * @uses delete the selected record
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param
     *
     * @return success/error message 
     */

    public function custom_live_videos_delete(Request $request) {
        
        if($model = CustomLiveVideo::where('id',$request->id)->first()) {

            if ($model->delete()) {
                return back()->with('flash_success',tr('live_custom_video_delete_success'));   
            }
        }
        return back()->with('flash_error',tr('something_error'));
    
    }

    /**
     *
     * Function name: custom_live_videos_view()
     *
     * @uses view the selected record
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param
     *
     * @return view page 
     */
    public function custom_live_videos_view($id) {

        if($model = CustomLiveVideo::find($id)) {

            return view('admin.custom_live_videos.view')
                        ->with('video' , $model)
                        ->withPage('custom_live_videos')
                        ->with('sub_page','custom_live_videos');

        } else {

            return back()->with('flash_error',tr('custom_live_video_not_found'));

        }
        
    }

   /**
     * Function Name : user_subscription_pause
     *
     * To prevent automatic subscriptioon, user have option to cancel subscription
     *
     * @param object $request - USer details & payment details
     *
     * @return boolean response with message
     */
    public function user_subscription_pause(Request $request) {

        $user_payment = UserPayment::find($request->id);

        if($user_payment) {

            $user_payment->is_cancelled = AUTORENEWAL_CANCELLED;

            $user_payment->cancel_reason = $request->cancel_reason;

            $user_payment->save();

            return back()->with('flash_success', tr('cancel_subscription_success'));

        } else {

            return back()->with('flash_error', Helper::get_error_message(167));

        }        

    }

    /**
     * Function Name : user_subscription_enable
     *
     * To prevent automatic subscriptioon, user have option to cancel subscription
     *
     * @created vithya
     *
     * @updated
     *
     * @param object $request - USer details & payment details
     *
     * @return boolean response with message
     */
    public function user_subscription_enable(Request $request) {

        $user_payment = UserPayment::where('user_id', $request->id)->where('status', PAID_STATUS)->orderBy('created_at', 'desc')
            ->where('is_cancelled', AUTORENEWAL_CANCELLED)
            ->first();

        if($user_payment) {

            $user_payment->is_cancelled = AUTORENEWAL_ENABLED;

            $user_payment->save();

            return back()->with('flash_success', tr('autorenewal_enable_success'));

        } else {

            return back()->with('flash_error', Helper::get_error_message(167));
        }       

    }  

    /**
     * Function Name : automatic_subscribers
     *
     * To list out automatic subscribers
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param integer $id - User id (Optional)
     * 
     * @return - response of array of automatic subscribers
     *
     */
    public function automatic_subscribers() {

        $datas = UserPayment::select(DB::raw('max(user_payments.id) as user_payment_id'),'user_payments.*')
                        ->leftjoin('subscriptions', 'subscriptions.id','=' ,'subscription_id')
                        ->where('subscriptions.amount', '>', 0)
                        ->where('user_payments.status', PAID_STATUS)
                        //->where('user_payments.is_cancelled', AUTORENEWAL_ENABLED)
                        ->groupBy('user_payments.user_id')
                        ->orderBy('user_payments.created_at' , 'desc')
                        ->get();

        $payments = [];

        $amount = 0;

        foreach ($datas as $key => $value) {
    
            $value = UserPayment::find($value->user_payment_id);

            if ($value->is_cancelled == AUTORENEWAL_ENABLED) {

                if ($value->getSubscription) {

                    $amount += $value->getSubscription ? $value->getSubscription->amount : 0;

                }
                
                $payments[] = [

                    'id'=> $value->id,

                    'user_id'=>$value->user_id,

                    'subscription_id'=>$value->subscription_id,

                    'payment_id'=>$value->payment_id,

                    'amount'=>$value->getSubscription ? $value->getSubscription->amount : '',

                    'payment_mode'=>$value->payment_mode,

                    'expiry_date'=>date('d-m-Y H:i a', strtotime($value->expiry_date)),

                    'user_name' => $value->user ? $value->user->name : '',

                    'subscription_name'=>$value->getSubscription ? $value->getSubscription->title : '',

                    'unique_id'=>$value->getSubscription ? $value->getSubscription->unique_id : '',

                ];

            } else {

                Log::info('Subscription not found');

            }

        }

        $payments =json_decode(json_encode($payments));

        return view('admin.subscriptions.subscribers.automatic')
                        ->withPage('subscriptions')
                        ->with('amount', $amount)
                        ->with('sub_page','automatic')->with('payments', $payments);        

    }

    /**
     * Function Name : cancelled_subscribers
     *
     * To list out cancelled subscribers
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param integer $id - User id (Optional)
     * 
     * @return - response of array of cancelled subscribers
     *
     */
    public function cancelled_subscribers() {

        $datas = UserPayment::select(DB::raw('max(user_payments.id) as user_payment_id'),'user_payments.*')
                        ->where('user_payments.status', PAID_STATUS)
                        ->leftjoin('subscriptions', 'subscriptions.id','=' ,'subscription_id')
                        ->where('user_payments.is_cancelled', AUTORENEWAL_CANCELLED)
                        ->groupBy('user_payments.user_id')
                        ->orderBy('user_payments.created_at' , 'desc')
                        ->get();

        $payments = [];

        foreach ($datas as $key => $value) {

            $value = UserPayment::find($value->user_payment_id);
            
            $payments[] = [

                'id'=> $value->user_payment_id,

                'user_id'=>$value->user_id,

                'subscription_id'=>$value->subscription_id,

                'payment_id'=>$value->payment_id,

                'amount'=>$value->getSubscription ? $value->getSubscription->amount : '',

                'payment_mode'=>$value->payment_mode,

                'expiry_date'=>date('d-m-Y H:i a', strtotime($value->expiry_date)),

                'user_name' => $value->user ? $value->user->name : '',

                'subscription_name'=>$value->getSubscription ? $value->getSubscription->title : '',

                'unique_id'=>$value->getSubscription ? $value->getSubscription->unique_id : '',

                'cancel_reason'=>$value->cancel_reason

            ];

        }

        $payments =json_decode(json_encode($payments));

        return view('admin.subscriptions.subscribers.cancelled')
                        ->withPage('subscriptions')
                        ->with('sub_page','cancelled')->with('payments', $payments);      

    }

    /**
     * Function Name : videos_compression_complete()
     *
     * @uses To complete the compressing videos
     *
     * @param integer video id - Video id
     *
     * @created vithya
     *
     * @updated: -
     *
     * @return response of success/failure message
     */
    public function videos_compression_complete(Request $request) {

        $response = CommonRepo::videos_compression_complete($request)->getData();

        if ($response->success) {

            return back()->with('flash_success', $response->message);

        } else {

            return back()->with('flash_error', $response->error_messages);

        }

    }

    /**
     *
     *
     */

    public function live_videos(Request $request) {

        $live_videos = LiveVideo::where('status', DEFAULT_FALSE)->where('is_streaming', DEFAULT_TRUE)->orderBy('created_at' , 'desc')->get();

        return view('admin.live_videos.index')->with('data' , $live_videos)
            ->with('page','live_videos')->with('sub_page','live_videos_idx'); 
    }


    public function live_videos_history(Request $request) {

        $live_videos = LiveVideo::orderBy('created_at' , 'desc')->get();

        return view('admin.live_videos.index')->with('data' , $live_videos)->with('page','live_videos')->with('sub_page','list_videos'); 
    }


    public function live_videos_view($id,Request $request) {

        $model = LiveVideo::find($id);


        if($model){

            $video_url = "";

            $ios_video_url = "";

            if ($model->unique_id == 'sample') {

                $video_url = $model->video_url;

            } else {

                if ($model->video_url) {            

                    if($model->browser_name == DEVICE_IOS){

                       $video_url = CommonRepo::rtmpUrl($model);

                    }

                    //$video_url = CommonRepo::iosUrl($model);

                    $ios_video_url = CommonRepo::iosUrl($model);

                } else {

                    $video_url = "";

                }

            }

            $model->video_url = $video_url;

            return view('admin.live_videos.view')->with('data' , $model)->with('page','live_videos')->with('sub_page','view_live_videos')->with('ios_video_url', $ios_video_url); 
        } else{

            return back()->with('flash_error',tr('live_videos_not_found'));
        }

    }



    public function user_payout(Request $request) {

        $validator = Validator::make($request->all() , [
            'user_id' => 'required|exists:users,id',
            'amount' => 'required', 
            ]);

        if($validator->fails()) {

            return back()->with('flash_error' , $validator->messages()->all())->withInput();

        } else {

            $model = User::find($request->user_id);

            if($model) {

                if($request->amount <= $model->remaining_amount) {

                    $model->paid_amount = $model->paid_amount + $request->amount;

                    $model->remaining_amount =$model->remaining_amount - $request->amount;

                    $model->save();
    
                    return back()->with('flash_success' , tr('action_success'));

                } else {
                    return back()->with('flash_error' , tr('user_payout_greater_error'));
                }

            } else {

                return back()->with('flash_error' , tr('something_error'));

            }
        }

    }

    /*
    public function remove_payper_view($id) {


        $subscription_total = UserPayment::sum('amount');

        $total_subscribers = UserPayment::where('status' , '!=' , 0)->count();

        $admin_ppv_amount = VideoTape::sum('admin_ppv_amount');

        $user_ppv_amount = VideoTape::sum('user_ppv_amount');

        $total_ppv_amount = $admin_ppv_amount + $user_ppv_amount;


        $admin_live_amount = LiveVideoPayment::sum('admin_amount');

        $user_live_amount = LiveVideoPayment::sum('user_amount');

        $total_live_amount = $admin_live_amount + $user_live_amount;


        
        return view('admin.payments.revenues')
                        ->with('total' , $total)
                    
                        ->with('subscription_total' , $subscription_total)
                        ->with('total_subscribers' , $total_subscribers)
                        ->with('admin_ppv_amount', $admin_ppv_amount)
                        ->with('user_ppv_amount', $user_ppv_amount)
                        ->with('total_ppv_amount', $total_ppv_amount)
                        ->with('admin_live_amount', $admin_live_amount)
                        ->with('user_live_amount', $user_live_amount)
                        ->with('total_live_amount', $total_live_amount)
                        ->withPage('payments')

                        ->with('sub_page' , 'payments-dashboard');



    public function secure(Request $request) {

        $video = VideoTape::where('video_tapes.id' , 49)
                ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                ->videoResponse()
                ->orderBy('video_tapes.created_at' , 'desc')
                ->first();

        $videoPath = $video_pixels = $videoStreamUrl = '';

        $secure_video = Helper::convert_hls_to_secure(get_video_end($video->video) , $video->video);
       
        $admin_video_images = $video->getScopeVideoTapeImages;

        $page = 'videos';
        $sub_page = 'add-video';

        return view('admin.videos.secure')->with('video' , $video)
                    ->with('video_images' , $admin_video_images)
                    ->withPage($page)
                    ->with('sub_page',$sub_page)
                    ->with('videoPath', $videoPath)
                    ->with('secure_video', $secure_video)
                    ->with('video_pixels', $video_pixels)
                    ->with('videoStreamUrl', $videoStreamUrl);
    }
     */

    /**
    * Function Name: live_video_payments
    *
    * @uses Get the live video payment details
    *
    * @created Maheswari
    *
    * @edited Maheswari
    *
    * @param Get the live video payment list in table
    *
    * @return Html table from payment list page
    */
    public function live_video_payments(){

        $live_video_payments = LiveVideoPayment::orderby('created_at','desc')->get();

        if($live_video_payments){

            return view('admin.payments.video-payments')
                ->with('data',$live_video_payments)
                ->with('page','payments')
                ->with('sub_page','video-payments');
        } else {

            return back()->with('flash_error',tr('live_video_payment_not_found'));
        }
    }

    /**
    * Function Name: ios_control()
    *
    * @uses To update the ios payment subscription status
    *
    * @param settings key value
    *
    * @created Maheswari
    *
    * @updated Maheswari
    *
    * @return response of success / failure message.
    */
    public function ios_control(){

        if(Auth::guard('admin')->check()){

            return view('admin.settings.ios-control')->with('page','ios-control');

        } else {

            return back();
        }
    }

    /**
    * Function Name: ios_control()
    *
    * @uses To update the ios settings value
    *
    * @param settings key value
    *
    * @created Maheswari
    *
    * @updated Maheswari
    *
    * @return response of success / failure message.
    */
    public function ios_control_save(Request $request){

        if(Auth::guard('admin')->check()){

            $settings = Settings::get();

            foreach ($settings as $key => $setting_details) {

                # code...

                $current_key = "";

                $current_key = $setting_details->key;
                
                    if($request->has($current_key)) {

                        $setting_details->value = $request->$current_key;
                    }

                $setting_details->save();
            }

            return back()->with('flash_success',tr('settings_success'));

        } else {

            return back();
        }
    }

}
