<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\File;

use App\Http\Requests;

use App\Helpers\Helper;

use App\Helpers\EnvEditorHelper;

use App\Jobs\sendPushNotification;

use App\Jobs\NormalPushNotification;

use App\Jobs\CompressVideo;

use App\Repositories\CommonRepository as CommonRepo;

use App\Repositories\AdminRepository as AdminRepo;

use App\Repositories\VideoTapeRepository as VideoRepo;

use Auth;

use DB;

use Exception;

use Log;

use Setting;

use Validator;

use App\User;

use App\UserPayment;

use App\UserHistory;

use App\UserRating;

use App\Wishlist;

use App\Channel;

use App\ChannelSubscription; 

use App\Category;

use App\Tag;

use App\Admin;

use App\AdminVideoImage;

use App\VideoTape;

use App\VideoAd;

use App\VideoTapeTag;

use App\CustomLiveVideo;

use App\Moderator;

use App\Redeem;

use App\Coupon;

use App\Subscription;

use App\Flag;

use App\Page;

use App\Settings;

use App\BannerAd;

use App\AssignVideoAd;

use App\VideoTapeImage;

use App\AdsDetail;

use App\RedeemRequest;

use App\PayPerView;

use App\Playlist;

use App\PlaylistVideo;

use App\Referral;

use App\UserReferrer;

use App\HomeVideo;


class NewAdminController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');  
    } 
      
    public function check_role(Request $request) {
        
        if(Auth::guard('admin')->check()) {
            
            $admin_details = Auth::guard('admin')->user();

            if($admin_details->role == ADMIN) {

                return redirect()->route('admin.dashboard');
            }

            if($admin_details->role == SUBADMIN) {

                return redirect()->route('subadmin.dashboard');
            }

        } else {

            return redirect()->route('admin.login');
        }

    }
   
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

        return view('new_admin.dashboard.dashboard')
        			->withPage('dashboard')
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
     * @method users_index()
     *
     * @uses To list out users object details
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param
     *
     * @return View page
     */
    public function users_index(Request $request) {

        try {

            $base_query = User::orderBy('created_at','desc')
                        ->withCount('getChannel')
                        ->withCount('getChannelVideos');
                    
            if ($request->user_id && $request->user_referrer_id) {
               
                $refered_users = Referral::where('parent_user_id', '=' , $request->user_id)->where('user_referrer_id', '=' , $request->user_referrer_id)->select('user_id')->get();
                
                if(!$refered_users) { 
                    
                    throw new Exception(tr('admin_user_refered_accounts_not_found'), 101);                    
                }

                $refered_users_ids = array_column($refered_users->toArray(), 'user_id');

                $base_query->whereIn('id', $refered_users_ids);
                
            }

            $users = $base_query->get();

            return view('new_admin.users.index')
                        ->withPage('users')
                        ->with('sub_page','users-view')
                        ->with('users' , $users);
           
        } catch (Exception $e) {

            $error = $e->getMessage();

            return redirect()->back()->with('flash_error',$error);            
        }
    }

    /**
     * @method users_create()
     *
     * @uses To create a user object details
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param 
     *
     * @return View page
     */
    public function users_create(Request $request) {

        $user_details = new User;

        return view('new_admin.users.create')
                    ->with('page' , 'users')
                    ->with('sub_page','users-create')
                    ->with('user_details', $user_details);
    }

    /**
     * @method users_edit
     *
     * @uses To edit a user based on their id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - user_id
     * 
     * @return response of new User object
     *`
     */
    public function users_edit(Request $request) {
        
        try {
          
            $user_details = User::find($request->user_id);

            if(!$user_details) {

                throw new Exception( tr('admin_user_not_found'), 101);
            }

            $user_details->dob = ($user_details->dob) ? date('d-m-Y', strtotime($user_details->dob)) : '';

            return view('new_admin.users.edit')
                    ->with('page' , 'users')
                    ->with('sub_page','users-view')
                    ->with('user_details',$user_details);
        

        } catch( Exception $e) {
            
            $error = $e->getMessage();

            return redirect()->route('admin.users.index')->with('flash_error',$error);
        }    
    }

    /**
     * @method users_save
     *
     * @uses To save/update user object based on user id or details
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - user_id, (request) details
     * 
     * @return success/failure message.
     *
     */
    public function users_save(Request $request) {
       
        try {
            
            DB::beginTransaction(); 

            $validator = Validator::make( $request->all(), [
                    'user_id' => 'exists:users,id',
                    'name' => 'required|max:255',
                    'email' => $request->user_id ? 'required|email|max:255|unique:users,email,'.$request->user_id: 'required|email|max:255|unique:users,email,NULL,id',

                    'mobile' => 'digits_between:6,13',
                    'password' => $request->user_id ? '' :'required|min:6|confirmed',
                    'dob' => 'required',
                    'description' => 'max:255',
                    'picture' => 'mimes:jpg,png,jpeg',
                ]
            );
            
            if ($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);
            }

            $user_details = $request->user_id ? User::find($request->user_id) : new User;

            $new_user = NEW_USER;
            
            if ($user_details->id != '') {

                $new_user = NO;

                $message = tr('admin_user_update_success');

            } else {
                
                $new_user = YES;

                $user_details->password = ($request->password) ? \Hash::make($request->password) : null;

                $message = tr('admin_user_create_success');

                $user_details->login_by = 'manual';

                $user_details->device_type = 'web';

                $user_details->picture = asset('placeholder.png');

                $user_details->timezone = $request->has('timezone') ? $request->timezone : '';
            }

            $user_details->name = $request->has('name') ? $request->name : '';

            $user_details->email = $request->has('email') ? $request->email: '';

            $user_details->mobile = $request->has('mobile') ? $request->mobile : '';

            $user_details->description = $request->has('description') ? $request->description : '';
            
            $user_details->token = Helper::generate_token();

            $user_details->token_expiry = Helper::generate_token_expiry();

            $user_details->dob = $request->dob ? date('Y-m-d', strtotime($request->dob)) : $user_details->dob;
            
            $user_details->paypal_email = $request->paypal_email ?: "";

            if ($user_details->dob) {

                $from = new \DateTime($user_details->dob);

                $to = new \DateTime('today');

                $user_details->age_limit = $from->diff($to)->y;
            }

            if ($user_details->age_limit < 10) {

                throw new Exception(tr('admin_user_min_age_error'), 101);
            }

            if ($new_user == YES) {

                $email_data['name'] = $user_details->name;

                $email_data['password'] = $request->password;

                $email_data['email'] = $user_details->email;

                $subject = tr('user_welcome_title' , Setting::get('site_name'));

                $page = "emails.admin_user_welcome";

                $email = $user_details->email;

                $user_details->is_verified = USER_EMAIL_VERIFIED;

                Helper::send_email($page,$subject,$email,$email_data);

                register_mobile('web');
            }

            // Upload picture
            if ($request->hasFile('picture') != "") {

                if ($request->user_id) {

                    Helper::delete_picture($user_details->picture, "/uploads/images/users/"); // Delete the old pic
                }

                $user_details->picture = Helper::normal_upload_picture($request->file('picture'), "/uploads/images/users/");
            }
            
            if ($user_details->save()) {

                // Check the default subscription and save the user type

                if ($request->user_id == '') {
                    
                    user_type_check($user_details->id);
                }
                
                if ($user_details) {
                    
                    DB::commit();

                    return redirect()->route('admin.users.view', ['user_id' => $user_details->id] )->with('flash_success', $message);

                } 

                throw new Exception( tr('admin_user_save_error'), 101);
            } 

            throw new Exception(tr('admin_user_save_error'), 101);             

        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return back()->withInput()->with('flash_error',$error);
        }    
    }

    /**
     * @method users_view
     *
     * @uses To view user details based on user id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - user_id
     * 
     * @return success/failure message.
     *
     */
    public function users_view(Request $request) {
       
        try {
            
            $user_details = User::find($request->user_id) ;
          
            if(!$user_details) {

                throw new Exception(tr('admin_user_not_found'), 101);
            } 

            $user_details = User::where('id', $request->user_id)
                                    ->withCount('getChannel')
                                    ->withCount('getChannelVideos')
                                    ->withCount('userWishlist')
                                    ->withCount('userHistory')
                                    ->withCount('userRating')
                                    ->withCount('userFlag')
                                    ->first();

            if ($user_details) {

                $channels = Channel::where('user_id', $request->user_id)
                            ->orderBy('created_at', 'desc')
                            ->withCount('getVideoTape')
                            ->withCount('getChannelSubscribers')
                            ->paginate(12);

                $channel_datas = [];

                foreach ($channels as $key => $value) {

                    $earnings = 0;

                    if ($value->getVideoTape) {

                        foreach ($value->getVideoTape as $key => $video) {

                            $earnings += $video->user_ppv_amount;
                        }
                    }
                    
                    $channel_datas[] = [

                        'channel_id' => $value->id,

                        'channel_name' => $value->name,

                        'picture' => $value->picture,

                        'cover' => $value->cover,

                        'subscribers' => $value->get_channel_subscribers_count,

                        'videos' => $value->get_video_tape_count,

                        'earnings' => $earnings,

                        'currency' => Setting::get('currency')
                    ];
                }

                // Without below condition the output of $channel_datas will be array f index value
                $channel_datas = json_encode($channel_datas);

                $channel_datas = json_decode($channel_datas);

                $videos = $user_details->getChannelVideos;

                $wishlists = Wishlist::select('wishlists.*', 'video_tapes.title as title')
                        ->where('wishlists.user_id', $request->user_id)
                        ->leftJoin('video_tapes', 'video_tapes.id', '=', 'wishlists.video_tape_id')
                        ->orderBy('wishlists.created_at', 'desc')
                        ->paginate(12);

                $histories = UserHistory::select('user_histories.*', 'video_tapes.title as title')
                        ->where('user_histories.user_id', $request->user_id)
                        ->leftJoin('video_tapes', 'video_tapes.id', '=', 'user_histories.video_tape_id')
                        ->orderBy('user_histories.created_at', 'desc')
                        ->paginate(12);

                $spam_reports = Flag::select('flags.*', 'video_tapes.title as title')
                        ->where('flags.user_id', $request->user_id)
                        ->leftJoin('video_tapes', 'video_tapes.id', '=', 'flags.video_tape_id')
                        ->orderBy('flags.created_at', 'desc')
                        ->paginate(12);

                $user_ratings = UserRating::select('user_ratings.*', 'video_tapes.title as title')
                        ->where('user_ratings.user_id', $request->user_id)
                        ->leftJoin('video_tapes', 'video_tapes.id', '=', 'user_ratings.video_tape_id')
                        ->orderBy('user_ratings.created_at', 'desc')
                        ->paginate(12);
                
                $users_referral_details = UserReferrer::where('user_id', $request->user_id)
                ->withCount('getReferral')
                ->first();

                return view('new_admin.users.view')
                            ->withPage('users')
                            ->with('sub_page','users-view')
                            ->with('user_details' , $user_details)
                            ->with('channels', $channel_datas)
                            ->with('videos', $videos)
                            ->with('wishlists', $wishlists)
                            ->with('histories', $histories)
                            ->with('spam_reports', $spam_reports)
                            ->with('users_referral_details', $users_referral_details)
                            ->with('user_ratings', $user_ratings);
            } 

            throw new Exception(tr('user_not_found'), 101);                
           
        } catch( Exception $e) {
            
            $error = $e->getMessage();

            return redirect()->route('admin.users.index')->with('flash_error',$error);
        }
    
    }


    /**
     * @method users_delete
     *
     * @uses To delete user details based on user id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - user_id
     * 
     * @return success/failure message.
     *
     */
    public function users_delete(Request $request) {

        try {
            
            DB::beginTransaction();

            $users_details = User::find($request->user_id);

            if (count($users_details) == 0 ) {

               throw new Exception(tr('admin_user_not_found'), 101);                
            }

            Helper::delete_picture($users_details->picture, "/uploads/images/users"); 

            if ($users_details->device_type) {

                subtract_count($users_details->device_type);            
            }

            // delete the user After reduce the count from mobile register model 
            if ($users_details->delete()) {
                
                DB::commit();

                return redirect()->route('admin.users.index')->with('flash_success',tr('admin_user_delete_success'));
            }

            throw new Exception(tr('admin_user_delete_error'), 101);       

         } catch( Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return redirect()->route('admin.users.index')->with('flash_error',$error);
        }
    
    }

    /**
     * @method users_status_change
     *
     * @uses To update the user status to APPROVE/DECLINE based on user id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - user_id
     * 
     * @return success/failure message.
     *
     */
    public function users_status_change(Request $request) {

        try {
            
            DB::beginTransaction();

            $users_details = User::find($request->user_id);

            if ( count($users_details) == 0 ) {

               throw new Exception(tr('admin_user_not_found'), 101);                
            }

            $users_details->status =$users_details->status == APPROVED ? DECLINED : APPROVED ;

            if( $users_details->save() ) {
                
                $message = $users_details->status == APPROVED ? tr('admin_user_approve_success') : tr('admin_user_declined_success') ;

                if ($users_details->status == DECLINED) {
                    
                    Channel::where('user_id', $users_details->id)->update(['is_approved'=>ADMIN_CHANNEL_DECLINED]);

                    VideoTape::where('user_id', $users_details->id)->update(['is_approved'=>ADMIN_VIDEO_DECLINED_STATUS]);
                }

                DB::commit();

                return back()->with('flash_success',$message );
            }
            
            throw new Exception(tr('admin_user_status_error'), 101);
            
        } catch (Exception $e) {

            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    }

    /**
     * Function: users_verify_status()
     * 
     * @uses To verify for the user Email 
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer (request) $user_id
     *
     * @return success/error message
     */
    public function users_verify_status(Request $request) {

        try {   

            DB::beginTransaction();
       
            $user_details = User::find($request->user_id);

            if( count( $user_details) == 0) {
                
                throw new Exception(tr('admin_user_not_found'), 101);
            } 
            
            $user_details->is_verified = $user_details->is_verified == USER_EMAIL_VERIFIED ? USER_EMAIL_NOT_VERIFIED : USER_EMAIL_VERIFIED;

            $message = $user_details->is_verified == USER_EMAIL_VERIFIED ? tr('admin_user_verification_success') : tr('admin_user_unverification_success');

            if( $user_details->save() ) {

                DB::commit();

                return back()->with('flash_success',$message);
            }
            
            throw new Exception(tr('admin_user_verification_save_error'), 101);
            
        } catch (Exception $e) {
            
            DB::rollback();
            
            $error = $e->getMessage();

            return redirect()->route('admin.users.index')->with('flash_error',$error);
        }
    }

    /**
     * @method users_wishlist
     *
     * @uses To list out all the wishlist details based on user id
     *
     * @created 
     *
     * @updated 
     *
     * @param integer $request - user id
     * 
     * @return - Response of wishlist based on id
     *
     */
    public function users_wishlist(Request $request) {

        try {
            
            $user_details = User::find($request->user_id);

            if( count( $user_details) == 0) {
                
                throw new Exception(tr('admin_user_not_found'), 101);
            } 

            $user_wishlists = Wishlist::where('wishlists.user_id' , $request->user_id)
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

            return view('new_admin.users.wishlist')
                        ->withPage('users')
                        ->with('sub_page','users')
                        ->with('user_wishlists' , $user_wishlists)
                        ->with('user_details' , $user_details);
          
        } catch (Exception $e) {
           
            $error = $e->getMessage();

            return redirect()->route('admin.users.index')->with('flash_error',$error);
        }
   
    }

    /**
     * @method users_wishlist_delete
     *
     * @uses To delete the user wishlist based on wishlist id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer ($request) wishlist_id
     * 
     * @return success/failure message
     *
     */
    public function users_wishlist_delete(Request $request) {

        try {
            
            DB::beginTransaction();

            $user_wishlist = Wishlist::find($request->wishlist_id);

            if(count($user_wishlist) == 0) {

                throw new Exception(tr('admin_user_wishlist_not_found'), 101);
            }

            if ($user_wishlist->delete()) {

                DB::commit();
                
                return back()->with('flash_success',tr('admin_user_wishlist_delete_success'));
            } 
                
            throw new Exception(tr('admin_user_wishlist_delete_error'), 101);
            
        } catch (Exception $e) {

            DB::rollback();

            $error = $e->getMessage();

            return redirect()->route('admin.users.index')->with('flash_error',$error);
        }
    
    }    

    /**
     * @method users_history_delete
     *
     * @uses To delete the user history based on history id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer ($request) history_id
     * 
     * @return success/failure message
     *
     */
    public function users_history_delete(Request $request) {

        try {
            
            DB::beginTransaction();

            $user_history = UserHistory::find($request->history_id);

            if(count($user_history) == 0) {

                throw new Exception(tr('admin_user_history_not_found'), 101);
            }

            if ($user_history->delete()) {

                DB::commit();
                
                return back()->with('flash_success',tr('admin_user_history_delete_success'));
            }
            
            throw new Exception(tr('admin_user_history_delete_error'), 101);
        
        } catch (Exception $e) {

            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    
    }

    /**
     * @method users_history
     *
     * @uses To list down all the videos based on hstory
     *
     * @created 
     *
     * @updated 
     *
     * @param integer $requesr - User id
     * 
     * @return - Response of channel creation page
     *
     */
    public function users_history(Request $request) {

        try {

            $user_details = User::find($request->user_id);

            if(!$user_details) {

                throw new Exception(tr('admin_user_not_found'), 101);
            }

            $user_histories = UserHistory::where('user_histories.user_id' , $request->user_id)
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

            return view('new_admin.users.history')
                        ->withPage('users')
                        ->with('sub_page','users')
                        ->with('user_details' , $user_details)
                        ->with('user_histories' , $user_histories);

        } catch (Exception $e) {

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    
    }


    /**
     * @method users_subscriptions
     *
     * To subscribe a new plans based on users
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param integer $id - User id (Optional)
     * 
     * @return - response of array of subscription details
     *
     */
    public function users_subscriptions(Request $request) {

        try {

            // Check the user details

            $user_details = User::find($request->user_id);

            if(!$user_details) {

                throw new Exception(tr('admin_user_not_found'), 101);
                
            }

            $subscriptions = Subscription::orderBy('created_at','desc')->get();

            $payments = UserPayment::where('user_payments.user_id' , $request->user_id)
                                ->leftjoin('subscriptions', 'subscriptions.id', '=', 'user_payments.subscription_id')
                                ->select('user_payments.*', 'subscriptions.title')
                                ->orderBy('user_payments.created_at' , 'desc')
                                ->get();

            return view('new_admin.subscriptions.user_plans')
                        ->with('page', 'users')
                        ->with('sub_page','users-view')
                        ->with('user_details', $user_details)
                        ->with('subscriptions' , $subscriptions)
                        ->with('payments', $payments); 
            
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }       

    }


    /**
     * @method users_subscription_save
     *
     * To save subscription details based on user id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer (request) $subscription_id, $user_id
     * 
     * @return success/failure message.
     *
     */
    public function users_subscription_save(Request $request) {

        try {
           
            DB::beginTransaction();

            if(!$subscriptions = Subscription::find($request->subscription_id)) {

                throw new Exception(tr('admin_subscription_not_found'), 101);
            }

            if(!$users_details = User::find($request->user_id)) {

                throw new Exception(tr('admin_user_not_found'), 101);
            }
            
            $response = CommonRepo::save_subscription($request->subscription_id,$request->user_id)->getData();

            if($response->success) {
                
                DB::commit();

                return back()->with('flash_success', $response->message);

            }

            throw new Exception($response->message, 101);  

        } catch (Exception $e) {

            DB::rollback();
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }

    }


    /**
     * @method users_channels
     *
     * To list out all the channels based on users id
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param integer $user_id - User id
     * 
     * @return response of user channel details
     *
     */
    public function users_channels(Request $request) {

        try {

            $user_details = User::find($request->user_id);

            if(!$user_details) {

                throw new Exception(tr('admin_user_not_found'), 101);
            }

            $channels = Channel::orderBy('channels.created_at', 'desc')
                                ->where('user_id' , $request->user_id)
                                ->distinct('channels.id')
                                ->withCount('getChannelSubscribers')
                                ->withCount('getVideoTape')
                                ->get();

            return view('new_admin.channels.index')
                        ->withPage('channels')
                        ->with('sub_page','view-channels')
                        ->with('channels' , $channels)
                        ->with('user_details' , $user_details);
            
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }  
    
    }

    /**
     * @method channels_index()
     *
     * @uses To list out channels object details
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param
     *
     * @return View page
     */
    public function channels_index() {

        $channels = Channel::orderBy('channels.created_at', 'desc')
                        ->distinct('channels.id')
                        ->withCount('getChannelSubscribers')
                        ->withCount('getVideoTape')
                        ->get();

        return view('new_admin.channels.index')
                    ->withPage('channels')
                    ->with('sub_page','channels-view')
                    ->with('channels' , $channels);    
    }

    /**
     * @method channels_create
     *
     * @uses To create a new channel
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param
     * 
     * @return view page
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
                        ->where('is_master_user' , DEFAULT_TRUE)
                        ->where('status', DEFAULT_TRUE)
                        ->orderBy('created_at', 'desc')
                        ->get();
        }

        $channel_details = new Channel;
         
        return view('new_admin.channels.create')
                ->with('page' ,'channels')
                ->with('sub_page' ,'channels-create')
                ->with('users', $users)
                ->with('channel_details', $channel_details);
    }

    /**
     * @method channels_edit
     *
     * @uses To edit the channel based on the channel id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer (request) $channel_id
     * 
     * @return view page
     */
    public function channels_edit(Request $request) {
        
        try {

            $channel_details = Channel::find($request->channel_id);

            if(!$channel_details) {

                throw new Exception(tr('admin_channel_not_found'), 101);
            }

            $users = User::where('is_verified', DEFAULT_TRUE)
                        ->where('status', DEFAULT_TRUE)
                        ->where('user_type', SUBSCRIBED_USER)
                        ->get();

            return view('new_admin.channels.edit')
                        ->with('page' ,'channels')
                        ->with('sub_page' ,'channels-view')
                        ->with('channel_details' , $channel_details)
                        ->with('users', $users);
            
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
   
    }

    /**
     * @method channels_save
     *
     * @uses To save the channel video object details
     *
     * @created 
     *
     * @updated 
     *
     * @param Integer (request) $channel_id
     * 
     * @return view page
     *
     */
    public function channels_save(Request $request) {

        $response = CommonRepo::channel_save($request)->getData();
       
        if($response->success) {

            return redirect()->route('admin.channels.view',['channel_id' => $response->data->id])->with('flash_success', $response->message);

        } else {
            
            return back()->with('flash_error', $response->error_messages);
        }
        
    }


    /**
     * @method channels_view
     *
     * @uses To view the channel based on the channel id
     *
     * @created 
     *
     * @updated 
     *
     * @param Integer (request) $channel_id
     * 
     * @return view page
     *
     */
    public function channels_view(Request $request) {

        try {

            $channel_details = Channel::select('channels.*', 'users.name as user_name', 'users.picture as user_picture')
                        ->leftjoin('users', 'users.id', '=', 'channels.user_id')
                        ->withCount('getVideoTape')
                        ->withCount('getChannelSubscribers')
                        ->withCount('getPlaylist')
                        ->where('channels.id', $request->channel_id)
                        ->first();

            if( !$channel_details ) {

                throw new Exception(tr('admin_channel_not_found'), 101);
            }

            // Load videos and subscribrs based on the channel
            $channel_earnings = getAmountBasedChannel($channel_details->id);

            $videos = VideoTape::select('video_tapes.title', 'video_tapes.default_image', 'video_tapes.id', 'video_tapes.description', 'video_tapes.created_at')
                        ->where('channel_id', $channel_details->id)
                        ->paginate(12);

            $channel_subscriptions = ChannelSubscription::select('users.name as user_name', 'users.id as user_id', 'users.picture as user_picture', 'users.description', 'users.created_at', 'users.email')->where('channel_id', $channel_details->id)
                        ->leftjoin('users', 'users.id', '=', 'channel_subscriptions.user_id')
                        ->paginate(12);

            $channel_playlists = Playlist::where('playlists.channel_id', $request->channel_id)->CommonResponse()->orderBy('playlists.updated_at', 'desc')->get();

            foreach ($channel_playlists as $key => $playlist_details) {
                
                $playlist_details->total_videos = PlaylistVideo::where('playlist_id', $playlist_details->playlist_id)->count();
            }
            
            return view('new_admin.channels.view')
                        ->with('page' ,'channels')
                        ->with('sub_page' ,'channels-view')
                        ->with('channel_details' , $channel_details)
                        ->with('channel_earnings', $channel_earnings)
                        ->with('videos', $videos)
                        ->with('channel_playlists', $channel_playlists)
                        ->with('channel_subscriptions', $channel_subscriptions);
            
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return redirect()->back()->with('flash_error',$error);
        }
    
    }

    /**
     * @method channels_delete
     *
     * @uses To delete the channel based on channel id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param integer (request) $channel_id
     * 
     * @return response of channel edit
     *
     */
    public function channels_delete(Request $request) {

        try {
            
            DB::beginTransaction();

            $channel_details = Channel::find($request->channel_id);

            if(!$channel_details) {

                throw new Exception(tr('admin_channel_not_found'), 101);
            }
            
            if ($channel_details->delete()) {  

                DB::commit();
                
                return redirect()->route('admin.channels.index')->with('flash_success',tr('admin_channel_delete_success'));
            } 

            throw new Exception(tr('admin_channel_delete_success'), 101);
            
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }    
    }

    /**
     * @method channels_status_change
     *
     * @uses To change the channel status of approve and decline 
     *
     * @created 
     *
     * @updated 
     *
     * @param integer (request) $channel_id
     * 
     * @return success/failure message
     */
    public function channels_status_change(Request $request) {
        
        try {

            DB::beginTransaction();

            $channel_details = Channel::find($request->channel_id);

            if ( !$channel_details) {

                throw new Exception(tr('admin_channel_not_found'), 101);
            }

            $channel_details->is_approved = $channel_details->is_approved == APPROVED ? DECLINED : APPROVED;

            $message = $channel_details->is_approved == APPROVED ? tr('admin_channel_approve_success') :  tr('admin_channel_decline_success') ;

            if ($channel_details->save() ) {

                if ( $channel_details->is_approved == ADMIN_CHANNEL_DECLINED) {

                    VideoTape::where('channel_id', $channel_details->id)
                                ->update(['is_approved' => ADMIN_CHANNEL_DECLINED]);                
                }

                DB::commit();

                return back()->with('flash_success', $message);            

            }  

            throw new Exception(tr('admin_channel_status_error'), 101);
            
            
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    
    }

    /**
     * @method channels_videos
     *
     * @uses To list out particular channel videos based on channel id
     *
     * @created 
     *
     * @updated 
     *
     * @param Integer (request) $channel_id
     * 
     * @return view page
     */
    public function channels_videos(Request $request) {

        try {

            $channel_details = Channel::find($request->channel_id);

            if(!$channel_details) {

                throw new Exception(tr('admin_channel_not_found'), 101);
            }

            $video_tapes = VideoTape::leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                        ->where('channel_id' , $request->channel_id)
                        ->videoResponse()
                        ->orderBy('video_tapes.created_at' , 'desc')
                        ->get();

            return view('new_admin.video_tapes.index')
                        ->withPage('video_tapes')
                        ->with('sub_page','video_tapes-view')
                        ->with('video_tapes' , $video_tapes)
                        ->with('channel' , $channel_details);

        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }  
   
    }

    /**
     * @method channels_subscribers
     *
     * @uses To list channel subscribers based on channel id
     *
     * @created 
     *
     * @updated 
     *
     * @param integer (request) $channel_id (optional)
     * 
     * @return view page
     *
     */
    public function channels_subscribers(Request $request) {

        try {
            
            $channel_subscriptions = ChannelSubscription::orderBy('created_at', 'desc')->get();

            $channel_details = '';

            if($request->channel_id) {

                $channel_details = Channel::find($request->channel_id);

                if(!$channel_details) {

                    throw new Exception(tr('admin_channel_not_found'), 101);
                }

                $channel_subscriptions = ChannelSubscription::where('channel_id', $request->channel_id)->orderBy('created_at', 'desc')->get();
            }   
            
            // dd($channel_details); 

            return view('new_admin.channels.subscribers')
                        ->withPage('channels')
                        ->with('sub_page','channels-subscribers')
                        ->with('channel_subscriptions' , $channel_subscriptions)
                        ->with('channel_details' , $channel_details);
            
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    
    }

    /**
     * @method categories_index()
     *
     * @uses To list out categories object details
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param
     *
     * @return View page
     */
    public function categories_index() {

        $categories = Category::orderBy('created_at','desc')
                        ->orderBy('updated_at', 'desc')
                        ->withCount('getVideos')->get();

        return view('new_admin.categories.index')
                    ->withPage('categories')
                    ->with('sub_page','categories-view')
                    ->with('categories' , $categories);
    }

    /**
     * @method categories_create()
     *
     * @uses To create a category object details
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param 
     *
     * @return View page
     */
    public function categories_create(Request $request) {

        $category_details = new Category;

        return view('new_admin.categories.create')
                    ->with('page' , 'categories')
                    ->with('sub_page','categories-create')
                    ->with('category_details', $category_details);
    }

    /**
     * @method categories_edit
     *
     * @uses To edit a category based on their id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - category_id
     * 
     * @return response of new category object
     *
     */
    public function categories_edit(Request $request) {
        
        try {
          
            $category_details = Category::find($request->category_id);

            if( !$category_details ) {

                throw new Exception( tr('admin_category_not_found'), 101);
            } 

            $category_details->dob = ($category_details->dob) ? date('d-m-Y', strtotime($category_details->dob)) : '';

            return view('new_admin.categories.edit')
                    ->with('page' , 'categories')
                    ->with('sub_page','categories-view')
                    ->with('category_details',$category_details);

        } catch( Exception $e) {
            
            $error = $e->getMessage();

            return redirect()->route('admin.categories.index')->with('flash_error',$error);
        }
    
    }


    
    /**
     * @method categories_save
     *
     * @uses To save/update category object based on category id or details
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - category_id, (request) details
     * 
     * @return success/failure message.
     *
     */
    public function categories_save(Request $request) {
        
        try {

            
            DB::beginTransaction();

            $validator = Validator::make($request->all() , [
                'name' => $request->category_id ? 'required|unique:categories,name,'.$request->category_id.',id|max:128|min:2' : 'required|unique:categories,name,NULL,id|max:128|min:2',
                'category_id' => 'exists:categories,id', 
                'image' => $request->category_id ? 'mimes:jpeg,jpg,bmp,png' : 'required|mimes:jpeg,jpg,bmp,png',
                    'description' => 'required',
            ]);

            if($validator->fails()) {

                $error = implode(',',$validator->messages()->all()); 

                throw new Exception($error, 101);
            }

            $category_details = $request->category_id ? Category::find($request->category_id) : new Category;

            $category_details->name = $request->name;

            $category_details->unique_id = seoUrl($category_details->name);

            $category_details->description = $request->description;

            $category_details->status = DEFAULT_TRUE;

            if ($request->hasFile('image')) {

                if ($request->category_id) {

                    Helper::delete_avatar('uploads/categories' , $category_details->image);
                }

                $category_details->image = Helper::upload_avatar('uploads/categories', $request->file('image'), 0); 
            }

            if ($category_details->save()) {

                DB::commit();

                $message = $request->category_id ? tr('admin_category_update_success') : tr('admin_category_update_success');

                return redirect()->route('admin.categories.view', ['category_id' => $category_details->id])->with('flash_success',$message);          
            }

            throw new Exception(tr('admin_category_save_error'), 101);
        
        } catch (Exception $e) {

            DB::rollback();
            
            $error = $e->getMessage();

            return back()->withInput()->with('flash_error',$error);
        }

    }

    /**
     * Function Name ; categories_view()
     *
     * category details based on id
     *
     * @created
     *
     * @updated 
     *
     * @param
     * 
     * @return 
     */
    public function categories_view(Request $request) {

        try {

            $category_details = Category::where('id', $request->category_id)
                                            ->withCount('getVideos')
                                            ->first();
            
            if (!$category_details ) {

                throw new Exception(tr('admin_category_not_found'), 101);
            } 

            // No of videos count
            $no_of_channels = Channel::leftJoin('video_tapes', 'video_tapes.channel_id', '=', 'channels.id')
                    ->where('video_tapes.category_id', $request->category_id)
                    ->groupBy('video_tapes.channel_id')
                    ->get();

            $channels = Channel::select('channels.*', 'video_tapes.id as video_tape_id', 'video_tapes.is_approved',
                            'video_tapes.status', 'video_tapes.channel_id')
                        ->leftJoin('video_tapes', 'video_tapes.channel_id', '=', 'channels.id')
                        ->where('video_tapes.category_id', $request->category_id)
                        ->groupBy('video_tapes.channel_id')
                        ->skip(0)->take(Setting::get('admin_take_count', 12))->get();

            $channel_lists = [];

            foreach ($channels as $key => $value) {

                $channel_lists[] = [
                        'channel_id' => $value->id, 
                        'user_id' => $value->user_id,
                        'picture' =>  $value->picture, 
                        'title' => $value->name,
                        'description' => $value->description, 
                        'created_at' => $value->created_at->diffForHumans(),
                        'no_of_videos' => videos_count($value->id),
                        'subscribe_status' => $request->category_id ? check_channel_status($request->category_id, $value->id) : '',
                        'no_of_subscribers' => $value->getChannelSubscribers()->count(),
                ];

            }

            $channel_lists = json_decode(json_encode($channel_lists));

            $category_videos = VideoTape::leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                            ->leftJoin('categories' , 'video_tapes.category_id' , '=' , 'categories.id')
                            ->videoResponse()
                            ->where('category_id', $request->category_id)
                            ->orderby('video_tapes.updated_at' , 'desc')
                            ->skip(0)->take(Setting::get('admin_take_count', 12))->get();

            $category_earnings = getAmountBasedChannel($category_details->id);

            return view('new_admin.categories.view')
                        ->with('page', 'categories')
                        ->with('sub_page', 'categories-view')
                        ->with('category_videos', $category_videos)
                        ->with('channel_lists', $channel_lists)
                        ->with('category_details', $category_details)
                        ->with('category_earnings', $category_earnings)
                        ->with('no_of_channels', count($no_of_channels));           
            
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return redirect()->route('admin.categories.index')->with('flash_error',$error);
        }

        
    }


    /**
     * @method categories_delete()
     *
     * @uses To delete the category based on category id
     *
     * @param integer $request - category id 
     *
     * @return response of success/failure message
     *
     * @created
     *
     * @updated
     *
     * @param object $request - category id
     *
     * @return success/failure message
     */
    public function categories_delete(Request $request) {

        try {
            
            DB::beginTransaction();

            $validator = Validator::make($request->all() , [
                'category_id' => 'required|exists:categories,id', 
            ]);

            if($validator->fails()) {
                
                $error = $validator->messages()->all();

                throw new Exception($error, 101);
            } 

            $category_details = Category::find($request->category_id);

            $category_details_img = $category_details->image;

            if ($category_details->no_of_uploads > 0) {

                throw new Exception(tr('category_allocated'), 101);
            }

            if ($category_details->delete()) {                
               
                Helper::delete_avatar('uploads/categories' ,$category_details_img);
                
                DB::commit();

                return redirect()->route('admin.categories.index')->with('flash_success',tr('admin_category_delete_success'));
            } 

            throw new Exception(tr('admin_category_delete_error'), 101);
            
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    }

    /**
     * @method categories_videos()
     *
     * @uses To display videos  based on category
     *
     * @created
     *
     * @updated
     *
     * @param object $request - User Details
     *
     * @return view page
     */
    public function categories_videos(Request $request) {

        try {

            $category_details = Category::find($request->category_id);

            if (!$category_details) {

                throw new Exception(tr('admin_category_not_found'), 101);
            }

            $videos = VideoTape::leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                    ->videoResponse()
                    ->where('category_id', $request->category_id)
                    ->get();

            $users = User::where('is_verified', DEFAULT_TRUE)
                            ->where('status', DEFAULT_TRUE)
                            ->where('user_type', SUBSCRIBED_USER)
                            ->get();

            return view('new_admin.categories.videos')
                        ->with('page', 'categories')
                        ->with('sub_page', 'categories-view')
                        ->with('videos', $videos)
                        ->with('category_details', $category_details);
            
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }

    }

    /**
     * @method categories_channels
     *
     * @uses To list out channels based on category
     *
     * @created 
     *
     * @updated 
     *
     * @param
     * 
     * @return response of user channel details
     *
     */
    public function categories_channels(Request $request) {

        try {

            $category_details = Category::find($request->category_id);

            if (!$category_details) {

                throw new Exception(tr('admin_category_not_found'), 101);
            }
            
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

            return view('new_admin.categories.channels')
                        ->withPage('categories')
                        ->with('sub_page','categories-view')
                        ->with('channels' , $channels)
                        ->with('category_details', $category_details);
            
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    
    }


    /**
     * @method categories_status()
     *
     * To change the category status approve/decline
     *
     * @param integer $request - category id 
     *
     * @return response of success/failure message
     */
    public function categories_status(Request $request) {

        try {
            
            DB::beginTransaction();

            $category_details = Category::find($request->category_id);

            if (count( $category_details) == 0 ) {

                throw new Exception(tr('admin_category_not_found'), 101);
            }

            $category_details->status = $category_details->status == APPROVED ? DECLINED: APPROVED;
            
            if ($category_details->status == DECLINED) {

                VideoTape::where('category_id', $category_details->id)->update(['is_approved'=>ADMIN_VIDEO_DECLINED_STATUS]);
            }

            if ($category_details->save()) {
                
                DB::commit();

                $message = $category_details->status == APPROVED ? tr('admin_category_approve_success') : tr('admin_category_decline_success'); 

                return back()->with('flash_success',$message );

            } 

            throw new Exception(tr('admin_category_status_error'), 101);

        } catch (Exception $e) {
            
            DB::commit();

            $error = $e->getMessage();

            return redirect()->route('admin.categories.index')->with('flash_error',$error);
        }
    }

    /**
     * @method tags_index()
     *
     * @uses To list out tags object details
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param Integer (request) tag_id
     *
     * @return View page
     */
    public function tags_index(Request $request) {
        
        try {

            $tag_details = new Tag;
            
            if($request->tag_id) {

                $tag_details = Tag::find($request->tag_id);

                if (!$tag_details) {

                    throw new Exception(tr('admin_tag_not_found'), 101);
                
                }
            }

            $tags = Tag::orderBy('created_at', 'desc')->get();

            return view('new_admin.tags.index')
                        ->with('page', 'tags')
                        ->with('sub_page', '')
                        ->with('tag_details', $tag_details)
                        ->with('tags', $tags);

        } catch (Exception $e) {
            
            DB::commit();

            $error = $e->getMessage();

            return redirect()->route('admin.tags.index')->with('flash_error',$error);
        }
    }
    
    /**
     * @method tags_save
     *
     * @uses To save/update tag object based on tag id or details
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - tag_id, (request) details
     * 
     * @return success/failure message.
     *
     */
    public function tags_save(Request $request) {
        
        try {
            
            DB::beginTransaction();

            $validator = Validator::make($request->all() , [
                'name' => $request->tag_id ? 'required|max:128|min:2|unique:tags,name,'.$request->tag_id.',id' : 'required|max:128|min:2|unique:tags,name,NULL,id',
                'tag_id' => 'exists:tags,id',
            ]);

            if ($validator->fails()) {
                
                $error= implode(',',$validator->messages()->all()); 

                throw new Exception($error, 101);                
            }

            $tag_details = $request->tag_id ? Tag::find($request->tag_id) : new Tag;

            $tag_details->name = $request->name;

            $tag_details->status = DEFAULT_TRUE;

            $tag_details->search_count = 0;

            if ($tag_details->save()) {

                DB::commit();

                $message =  $request->tag_id ? tr('admin_tag_update_success') : tr('admin_tag_create_success'); 

                return redirect()->route('admin.tags.index')->with('flash_success',$message);
            } 

            throw new Exception(tr('admin_tag_save_error'), 101);            
            
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return back()->withInput()->with('flash_error',$error);       
        }

    }

    /**
     * @method tags_delete
     *
     * @uses To delete tag details based on tag id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - tag_id
     * 
     * @return success/failure message.
     *
     */
    public function tags_delete(Request $request) {

        try {
            
            DB::beginTransaction();

            $tag_details = Tag::find($request->tag_id);

            if (!$tag_details) {

                throw new Exception(tr('admin_tag_not_found'), 101);
            }            

            if ($tag_details->delete()) {  

                DB::commit();
                
                return back()->with('flash_success',tr('admin_tag_delete_success'));
            }  

            throw new Exception(tr('admin_tag_delete_error'), 101);
            
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }

    }

    /**
     * @method tags_status_change
     *
     * @uses To change the tag status of approve and decline 
     *
     * @created 
     *
     * @updated 
     *
     * @param integer (request) $tag_id
     * 
     * @return success/failure message
     */
    public function tags_status_change(Request $request) {
        
        try {

            DB::beginTransaction();

            $tag_details = Tag::find($request->tag_id);

            if ( !$tag_details) {

                throw new Exception(tr('admin_tag_not_found'), 101);
            }

            $tag_details->status = $tag_details->status == APPROVED ? DECLINED : APPROVED;

            $message = $tag_details->status == APPROVED ? tr('admin_tag_approved_success') :  tr('admin_tag_declined_success') ;

            if ($tag_details->save() ) {

                DB::commit();

                if ($tag_details->status == DECLINED) {

                    VideoTapeTag::where('tag_id', $tag_details->id)->update(['status' =>DECLINED]);
                } else {

                    VideoTapeTag::where('tag_id', $tag_details->id)->update(['status' =>APPROVED]);
                }
            
                return back()->with('flash_success', $message);   
            }

            throw new Exception(tr('admin_tag_status_error'), 101);
           
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    
    }

    /**
     * @method coupons_index()
     *
     * @uses To list out coupons object details
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param
     *
     * @return View page
     */
    public function coupons_index() {

        $coupons = Coupon::orderBy('created_at','desc')->get();

        return view('new_admin.coupons.index')
                    ->withPage('coupons')
                    ->with('sub_page','coupons-view')
                    ->with('coupons' , $coupons);
    }

    /**
     * @method coupons_create()
     *
     * @uses To create a coupon object details
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param 
     *
     * @return View page
     */
    public function coupons_create(Request $request) {

        $coupon_details = new Coupon;

        $coupon_details->expiry_date = date('Y-m-d');
        
        return view('new_admin.coupons.create')
                    ->with('page' , 'coupons')
                    ->with('sub_page','coupons-create')
                    ->with('coupon_details', $coupon_details);
    }

    /**
     * @method coupons_edit
     *
     * @uses To edit a coupon based on their id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - coupon_id
     * 
     * @return response of new coupon object
     *`
     */
    public function coupons_edit(Request $request) {
        
        try {
          
            $coupon_details = Coupon::find($request->coupon_id);

            if( !$coupon_details ) {

                throw new Exception( tr('admin_coupon_not_found'), 101);
            } 

            $coupon_details->dob = ($coupon_details->dob) ? date('d-m-Y', strtotime($coupon_details->dob)) : '';

            return view('new_admin.coupons.edit')
                    ->with('page' , 'coupons')
                    ->with('sub_page','coupons-view')
                    ->with('coupon_details',$coupon_details);
       

        } catch( Exception $e) {
            
            $error = $e->getMessage();

            return redirect()->route('admin.coupons.index')->with('flash_error',$error);
        }
    
    }

    /**
     * Function Name: coupons_save()
     *
     * @uses save/Update the coupon details
     *
     * @created 
     *
     * @updated 
     *
     * @param Integer (request) coupon_id, (request) details
     *
     * @return success/failure message
     */
    public function coupons_save(Request $request) {

        try {
            
            DB::beginTransaction();

            $validator = Validator::make($request->all(),[
                'coupon_id' => 'exists:coupons,id',
                'title' => 'required',
                'coupon_code' => $request->coupon_id ? 'required|max:10|min:1|unique:coupons,coupon_code,'.$request->coupon_id : 'required|unique:coupons,coupon_code|min:1|max:10',
                'amount' => 'required|numeric|min:1|max:5000',
                'amount_type' => 'required',
                'expiry_date' => 'required|date_format:d-m-Y|after:today',
                'no_of_users_limit' => 'required|numeric|min:1|max:1000',
                'per_users_limit' => 'required|numeric|min:1|max:100',
            ]);

            if($validator->fails()){

                $error = implode(',',$validator->messages()->all());

                throw new Exception( $error, 101);
            }

            if($request->coupon_id != ''){
                                       
                $coupon_detail = Coupon::find($request->coupon_id); 

                $message=tr('admin_coupon_update_success');

            } else {

                $coupon_detail = new Coupon;

                $coupon_detail->status = DEFAULT_TRUE;

                $message = tr('admin_coupon_create_success');
            }

            // Check the condition amount type equal zero mean percentage
            if($request->amount_type == PERCENTAGE) {

                // Amount type zero must should be amount less than or equal 100 only
                if($request->amount <= 100){

                    $coupon_detail->amount_type = $request->has('amount_type') ? $request->amount_type :0;
     
                    $coupon_detail->amount = $request->has('amount') ?  $request->amount : '';

                } else {

                    throw new Exception(tr('admin_coupon_amount_lessthan_100'), 101);
                }

            } else {

                // This else condition is absoulte amount 

                // Amount type one must should be amount less than or equal 5000 only
                if($request->amount <= 5000){

                    $coupon_detail->amount_type=$request->has('amount_type') ? $request->amount_type : 1;

                    $coupon_detail->amount=$request->has('amount') ?  $request->amount : '';

                } else {

                    throw new Exception(tr('admin_coupon_amount_lessthan_5000'), 101);
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

            if($coupon_detail->save()) {

                DB::commit();

                $message = $request->coupon_id ? tr('admin_coupon_create_success'): tr('admin_coupon_update_success');
                               
                return redirect()->route('admin.coupons.view',['coupon_id' =>$coupon_detail->id ])->with('flash_success',$message);
            } 

            throw new Exception(tr('admin_coupon_save_error'), 101);  
            
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return redirect()->back()->withInput()->with('flash_error',$error);
        }
        
    }

    /**
     * @method coupons_view
     *
     * @uses To view the coupon based on the coupon id
     *
     * @created 
     *
     * @updated 
     *
     * @param Integer (request) $coupon_id
     * 
     * @return view page
     *
     */
    public function coupons_view(Request $request) {

        try {

            $coupon_details = Coupon::find($request->coupon_id);

            if (!$coupon_details) {

                throw new Exception(tr('admin_coupon_not_found'), 101);
            }

            return view('new_admin.coupons.view')
                    ->with('page','coupons')
                    ->with('sub_page','coupons-view')
                    ->with('coupon_details',$coupon_details);        
            
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    
    }

    /**
     * @method coupons_delete
     *
     * @uses To delete coupons details based on coupons id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - coupons_id
     * 
     * @return success/failure message.
     *
     */
    public function coupons_delete(Request $request) {

        try {
        
            DB::beginTransaction();

            $coupon_details = Coupon::find($request->coupon_id);

            if (!$coupon_details) {

                throw new Exception(tr('admin_coupon_not_found'), 101);
            }

            if ($coupon_details->delete()) {  

                DB::commit();
                
                return redirect()->route('admin.coupons.index')->with('flash_success',tr('admin_coupon_delete_success'));
            } 

            throw new Exception(tr('admin_coupon_delete_success'), 101);
            
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return redirect()->back()->with('flash_error',$error);
        }

    }

    /**
     * @method coupons_status_change
     *
     * @uses To update the coupon status to APPROVE/DECLINE based on coupon id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - coupon_id
     * 
     * @return success/failure message.
     *
     */
    public function coupons_status_change(Request $request) {

        try {
            
            DB::beginTransaction();

            $coupons_details = Coupon::find($request->coupon_id);

            if ( count($coupons_details) == 0 ) {

               throw new Exception(tr('admin_coupon_not_found'), 101);                
            }

            $coupons_details->status = $coupons_details->status == APPROVED ? DECLINED : APPROVED ;

            if( $coupons_details->save() ) {
                
                $message = $coupons_details->status == APPROVED ? tr('admin_coupon_approved_success') : tr('admin_coupon_declined_success') ;
                
                DB::commit();                

                return back()->with('flash_success',$message );
            } 
                
            throw new Exception(tr('admin_coupon_status_error'), 101);
            
        } catch (Exception $e) {

            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    }

    /**
     * @method ads_details_index()
     *
     * @uses To list out ads_details object details
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param
     *
     * @return View page
     */
    public function ads_details_index() {

        $ads_details = AdminRepo::ads_details_index()->getData();

        return view('new_admin.ads_details.index')
                    ->withPage('videos-ads-details')
                    ->with('sub_page','videos-ads-details-view')
                    ->with('ads_details' , $ads_details);
    }
    public function video_ads_details_index() {

        $ads_details = AdminRepo::ads_details_index()->getData();

        return view('new_admin.video_ads_details.index')
                    ->withPage('videos-ads')
                    ->with('sub_page','view-video-ads')
                    ->with('ads_details' , $ads_details);
    }
    /**
     * @method ads_details_create()
     *
     * @uses To create a ads_detail object details
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param 
     *
     * @return View page
     */
    public function ads_details_create(Request $request) {

        $ads_detail_details = new AdsDetail;

        return view('new_admin.ads_details.create')
                    ->with('page' , 'videos-ads-details')
                    ->with('sub_page','videos-ads-details-create')
                    ->with('ads_detail_details', $ads_detail_details);
    }

    public function video_ads_details_create(Request $request) {

        $ads_detail_details = new AdsDetail;

        return view('new_admin.video_ads_details.create')
                    ->with('page' , 'videos-ads')
                    ->with('sub_page','create-video-ads')
                    ->with('ads_detail_details', $ads_detail_details);
    }
    /**
     * @method ads_details_view
     *
     * @uses To view the ads_detail based on the ads_detail id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer (request) $ads_detail_id
     * 
     * @return view page
     *
     */
    public function ads_details_view(Request $request) {

        try {

            $ads_detail_details = AdsDetail::find($request->ads_detail_id);

            if (!$ads_detail_details) {

                throw new Exception(tr('admin_ads_detail_not_found'), 101);
            }

            return view('new_admin.ads_details.view')
                    ->with('page','videos-ads-details')
                    ->with('sub_page','videos-ads-details-view')
                    ->with('ads_detail_details',$ads_detail_details);        
            
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return redirect()->back()->with('flash_error',$error);
        }
    
    }
   
    public function video_ads_details_view() {

        $video_ads = VideoAd::select('channels.id as channel_id', 'channels.name', 'video_tapes.id as video_tape_id', 'video_tapes.title', 'video_tapes.default_image', 'video_tapes.ad_status',
            'video_ads.*','video_tapes.channel_id')
                    ->leftJoin('video_tapes' , 'video_tapes.id' , '=' , 'video_ads.video_tape_id')
                    ->leftJoin('channels' , 'channels.id' , '=' , 'video_tapes.channel_id')
                    ->orderBy('video_tapes.updated_at' , 'asc')
                    ->get();

        return view('new_admin.video_ads_details.ad_videos')
                    ->with('page', 'videos-ads')
                    ->with('sub_page', 'assigned-videos-ads')
                    ->with('video_ads' , $video_ads);
    }
   
    /**
     * @method ads_details_save
     *
     * @uses To save/update ads_detail object based on ads_detail_id or details
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - ads_detail_id, (request) details
     * 
     * @return success/failure message.
     *
     */
    public function ads_details_save(Request $request) {

        try {

            $response = AdminRepo::ads_details_save($request)->getData();

            if($response->success) {

                return redirect()->route('admin.ads-details.view', ['ads_detail_id' => $response->data->id])->with('flash_success', $response->message);
            } 

            throw new Exception($response->message, 101);  
            
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->withInput()->with('flash_error',$error);
        }

    }
    public function video_ads_details_save(Request $request) {

        try {

            $response = AdminRepo::ads_details_save($request)->getData();

            if($response->success) {

                return redirect()->route('admin.video-ads-details.view', ['ads_detail_id' => $response->data->id])->with('flash_success', $response->message);
            } 

            throw new Exception($response->message, 101);  
            
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->withInput()->with('flash_error',$error);
        }

    }
    /**
     * @method ads_details_edit
     *
     * @uses To edit a ads_detail based on their id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - ads_detail_id
     * 
     * @return response of new ads_detail object
     *`
     */
    public function ads_details_edit(Request $request) {
        
        try {
          
            $ads_detail_details = AdsDetail::find($request->ads_detail_id);

            if( !$ads_detail_details ) {

                throw new Exception( tr('admin_ads_detail_not_found'), 101);
            } 

            $ads_detail_details->dob = ($ads_detail_details->dob) ? date('d-m-Y', strtotime($ads_detail_details->dob)) : '';

            return view('new_admin.ads_details.edit')
                        ->with('page' , 'videos-ads-details')
                        ->with('sub_page','videos-ads-details-view')
                        ->with('ads_detail_details',$ads_detail_details);       

        } catch( Exception $e) {
            
            $error = $e->getMessage();

            return redirect()->route('admin.ads_details.index')->with('flash_error',$error);
        }
    
    }

    public function video_ads_details_edit(Request $request) {
        
        try {
          
            $ads_detail_details = AdsDetail::find($request->ads_detail_id);

            if( !$ads_detail_details ) {

                throw new Exception( tr('admin_ads_detail_not_found'), 101);
            } 

            $ads_detail_details->dob = ($ads_detail_details->dob) ? date('d-m-Y', strtotime($ads_detail_details->dob)) : '';

            return view('new_admin.video_ads_details.edit')
                        ->with('page' , 'videos-ads')
                        ->with('sub_page','videos-ads-details-view')
                        ->with('ads_detail_details',$ads_detail_details);       

        } catch( Exception $e) {
            
            $error = $e->getMessage();

            return redirect()->route('admin.video_ads_details.index')->with('flash_error',$error);
        }
    
    }
    /**
     * @method ads_details_delete
     *
     * @uses To delete the ads_details based on ads_details id
     *
     * @created 
     *
     * @updated 
     *
     * @param integer (request) $ads_details_id
     * 
     * @return response of ads_details edit
     *
     */
    public function ads_details_delete(Request $request) {

        try {
            
            DB::beginTransaction();

            $ads_detail_details = AdsDetail::find($request->ads_detail_id);

            if (!$ads_detail_details) {

                throw new Exception(tr('admin_ads_detail_not_found'), 101);
            }

            foreach ($ads_detail_details->getAssignedVideo as $key => $value) {

                if ($value->videoAd) {

                    if ($value->videoAd->delete()) {  
                        // do nothing
                    } else {

                        throw new Exception(tr('admin_video_ad_delete_error'), 101);
                    }
                }

                if ($value->delete()) {  
                    // do nothing
                } else {

                    throw new Exception(tr('admin_ads_detail_delete_error'), 101);
                } 
            } 
        
            if ($ads_detail_details->delete()) {  

                DB::commit();
                
                return redirect()->route('admin.ads-details.index')->with('flash_success',tr('admin_ads_detail_delete_success'));
            } 

            throw new Exception(tr('admin_ads_detail_delete_error'), 101);
                        
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }    
    }
    public function video_ads_details_delete(Request $request) {

        try {
            
            DB::beginTransaction();

            $ads_detail_details = AdsDetail::find($request->ads_detail_id);

            if (!$ads_detail_details) {

                throw new Exception(tr('admin_ads_detail_not_found'), 101);
            }

            foreach ($ads_detail_details->getAssignedVideo as $key => $value) {

                if ($value->videoAd) {

                    if ($value->videoAd->delete()) {  
                        // do nothing
                    } else {

                        throw new Exception(tr('admin_video_ad_delete_error'), 101);
                    }
                }

                if ($value->delete()) {  
                    // do nothing
                } else {

                    throw new Exception(tr('admin_ads_detail_delete_error'), 101);
                } 
            } 
        
            if ($ads_detail_details->delete()) {  

                DB::commit();
                
                return redirect()->route('admin.ads-details.index')->with('flash_success',tr('admin_ads_detail_delete_success'));
            } 

            throw new Exception(tr('admin_ads_detail_delete_error'), 101);
                        
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }    
    }
    /**
     * @method ads_details_status
     *
     * @uses To delete the ads_details based on ads_details id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param integer (request) $ads_details_id
     * 
     * @return response of ads_details edit
     *
     */
    public function ads_details_status(Request $request) {
        
        try {
            
            DB::beginTransaction();

            $ads_detail_details = AdsDetail::find($request->ads_detail_id);

            if (!$ads_detail_details) {

                throw new Exception(tr('admin_ads_detail_not_found'), 101);
            }

            $ads_detail_details->status = $ads_detail_details->status == DEFAULT_TRUE ? DEFAULT_FALSE : DEFAULT_TRUE ;

            $message = $ads_detail_details->status == DEFAULT_TRUE ?  tr('admin_ads_detail_approved_success') : tr('admin_ads_detail_declined_success') ;

            if( $ads_detail_details->save()) {

                DB::commit();
                
            } else {

                throw new Exception(tr('admin_ad_status_save_error'), 101);                
            }

            // Load Assigned video ads

            $assigned_video_ad = AssignVideoAd::where('ad_id', $ads_detail_details->id)->get();

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

                        DB::commit();
                    }
                }
            }

            return back()->with('flash_success', $message);
            
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    
    }

    /**
     * @method video_ads_index()
     *
     * @uses To list out videos ads list with videos
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param
     *
     * @return View page
     */
    public function video_ads_index() {

        $video_ads = VideoAd::select('channels.id as channel_id', 'channels.name', 'video_tapes.id as video_tape_id', 'video_tapes.title', 'video_tapes.default_image', 'video_tapes.ad_status',
            'video_ads.*','video_tapes.channel_id')
                    ->leftJoin('video_tapes' , 'video_tapes.id' , '=' , 'video_ads.video_tape_id')
                    ->leftJoin('channels' , 'channels.id' , '=' , 'video_tapes.channel_id')
                    ->orderBy('video_tapes.updated_at' , 'asc')
                    ->get();

        return view('new_admin.video_ads.ad_videos')
                    ->with('page', 'videos-ads-details')
                    ->with('sub_page', 'assigned-videos-ads-details')
                    ->with('video_ads' , $video_ads);
    }

    /**
     * @method video_ads_view()
     *
     * @uses To get ads with video (Single video based on id)
     *
     * @created 
     *
     * @updated
     *
     * @param Integer $request->id : Video id
     *
     * @return view page
     */
    public function video_ads_view(Request $request) {
        
        try {

            $video_ad_details = AdminRepo::video_ads_view($request)->getData();

            if(!$video_ad_details) {

                throw new Exception(tr('admin_video_ad_not_found'), 101);            
            }

            return view('new_admin.video_ads.view')
                        ->with('page', 'videos-ads-details')
                        ->with('sub_page', 'assigned-videos-ads-details')
                        ->with('ads', $video_ad_details);
            
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }

    }


    /**
     * @method video_ads_edit()
     *
     * @uses To edit a assigned ad videos edit details
     *
     * @created 
     *
     * @updated 
     *
     * @param Integer $request : Video ad id with video ad details
     *
     * @return response of succes//failure response of details
     */
    public function video_ads_edit(Request $request) {

        try {

            $video_ad_details = VideoAd::find($request->id);

            if (!$video_ad_details) {

                throw new Exception(tr('admin_video_ad_not_found'), 101);
            }

            $preAd = $video_ad_details->getPreAdDetail ? $video_ad_details->getPreAdDetail : new AdsDetail;

            $postAd = $video_ad_details->getPostAdDetail ? $video_ad_details->getPostAdDetail : new AdsDetail;

            $betweenAd = (count($video_ad_details->getBetweenAdDetails) > 0) ? $video_ad_details->getBetweenAdDetails : [];

            $index = 0;

            $video_tape_details = $video_ad_details->getVideoTape;

            $videoPath = $video_pixels = '';

            $ads = AdsDetail::where('status', ADS_ENABLED)->get(); 

            if ($video_tape_details) {

                $videoPath = $video_tape_details->video_resize_path ? $video_tape_details->video.','.$video_tape_details->video_resize_path : $video_tape_details->video;

                $video_pixels = $video_tape_details->video_resolutions ? 'original,'.$video_tape_details->video_resolutions : 'original';
            }

            return view('new_admin.video_ads.edit')
                        ->with('page', 'videos-ads-details')
                        ->with('sub_page', 'assigned-videos-ads-details')
                        ->with('video_tape_details', $video_tape_details)
                        ->with('videoPath', $videoPath)
                        ->with('video_pixels', $video_pixels)
                        ->with('model', $video_ad_details)
                        ->with('preAd', $preAd)
                        ->with('postAd', $postAd)
                        ->with('betweenAd', $betweenAd)
                        ->with('index', $index)
                        ->with('ads', $ads);
            
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        } 
    }

    /**
     * @method video_ads_save()
     *
     * @uses To save the video ads when edit by the admin
     *
     * @created 
     *
     * @updated 
     *
     * @param Integer $request : Video ad id with video ad details
     *
     * @return  succes/failure message
     */
    public function video_ads_save(Request $request) {
      
        try {
            
            $response = AdminRepo::video_ads_save($request)->getData();

            if($response->success) {

                return redirect()->route('admin.video_ads.view', ['id'=>$response->data->id])->with('flash_success', $response->message);
            } 

            throw new Exception($response->message, 101);

        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
        
    }    

    /**
     * @method video_ads_delete()
     *
     * @uses To delete assigned video ads based on video ad
     *
     * @created 
     *
     * @updated  
     *
     * @param Integer $request->id : Video ad id
     *
     * @return response of succes//failure response of details
     */
    public function video_ads_delete(Request $request) {
        
        try {
            
            DB::beginTransaction();

            $video_ad_details = VideoAd::find($request->id);

            if (!$video_ad_details) {

                throw new Exception(tr('admin_video_ad_not_found'), 101);
            }            

            if ($video_ad_details->delete()) {  

                DB::commit();
                
                return redirect()->route('admin.video_ads.index')->with('flash_success',tr('admin_video_ad_delete_success'));
            } 

            throw new Exception(tr('admin_tag_delete_error'), 101);
           
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    }

        /**
     * @method video_assign_ad()
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

        $ad_details = AdsDetail::find($request->id);

        if (!$ad_details) {

            return back()->with('flash_error', tr('something_error'));

        }

        $video_tapes = VideoTape::where('status', DEFAULT_TRUE)
            ->where('publish_status', DEFAULT_TRUE)
            ->where('is_approved', DEFAULT_TRUE)
            ->where('ad_status',DEFAULT_TRUE)
            ->get();
       
        return view('new_admin.ads_details.assign_ad')
                ->with('page', 'videos_ads')
                ->with('sub_page', 'view-ads')
                ->with('ad_details', $ad_details)
                ->with('video_tapes', $video_tapes)
                ->with('type', $request->type);
    }

    /**
     * @method video_ads_inter_ads()
     *
     * To add between ads details based on video details
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param 
     *
     * @return response of Ad Between Form
     *
     * @todo Function Name change 
     */
    public function video_ads_inter_ads(Request $request) {

        $index = $request->index + 1;

        $b_ad = new AdsDetail;

        $ads = AdsDetail::where('status', ADS_ENABLED)->get(); 
        
        return view('new_admin.video_ads._sub_form')
                ->with('page', 'videos-ads-details')
                ->with('sub_page', 'assigned-videos-ads-details')
                ->with('index' , $index)
                ->with('b_ad', $b_ad)
                ->with('ads', $ads);
    }

    /**
    * Function Name: help()
    *
    * @uses To delete the ads_details based on ads_details id
    *
    * @created
    *
    * @updated
    *
    * @param 
    *
    * @return view page
    */
    public function help() {

        return view('new_admin.static_pages.help')
                ->withPage('help')
                ->with('sub_page' , "");

    }

    /**
    * Function Name: profile()
    *
    * @uses To display Admin details 
    *
    * @created
    *
    * @updated
    *
    * @param
    *
    * @return view page
    */
    public function profile() {

        $admin = Admin::first();

        return view('new_admin.account.profile')
                ->withPage('profile')
                ->with('sub_page','')
                ->with('admin' , $admin);
    
    }

    /**
     * Function Name: profile_save()
     *
     * @uses To save admin account datails  
     *
     * @created
     *
     * @updated
     *
     * @param 
     *
     * @return view page
     */
    public function profile_save(Request $request) {
        
        try {

            DB::beginTransaction();

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

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);
                
            }         
            
            $admin_details = Admin::find($request->id);
            
            $admin_details->name = $request->has('name') ? $request->name : $admin_details->name;

            $admin_details->email = $request->has('email') ? $request->email : $admin_details->email;

            $admin_details->mobile = $request->has('mobile') ? $request->mobile : $admin_details->mobile;

            $admin_details->gender = $request->has('gender') ? $request->gender : $admin_details->gender;

            $admin_details->address = $request->has('address') ? $request->address : $admin_details->address;

            if($request->hasFile('picture')) {

                Helper::delete_picture($admin_details->picture, "/uploads/images/");

                $admin_details->picture = Helper::normal_upload_picture($request->picture, "/uploads/images/");
            }
                
            $admin_details->remember_token = Helper::generate_token();            

            if ( $admin_details->save()) {  

                DB::commit();
                
                return back()->with('flash_success', tr('admin_profile_update_success'));
            } 

            throw new Exception(tr('admin_profile_save_error'), 101);
        
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    
    }

    /**
     * Function: change_password()
     * 
     * @uses change the admin password 
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param - 
     *
     * @return redirect with success/ error message
     */
    public function change_password(Request $request) {
       
        try {
       
            DB::beginTransaction();

            $old_password = $request->old_password;
            $new_password = $request->password;
            $confirm_password = $request->confirm_password;
            
            $validator = Validator::make($request->all(), [              
                    'password' => 'required|confirmed|min:6',
                    'old_password' => 'required',
                    'password_confirmation' => 'required|min:6',
                    'id' => 'required|exists:admins,id'
            ]);

            if($validator->fails()) {

                $error = implode(',',$validator->messages()->all());

                throw new Exception($error, 101);
            } 

            $admin_details = Admin::find($request->id);

            if(\Hash::check($old_password,$admin_details->password)) {

                $admin_details->password = \Hash::make($new_password);

                if( $admin_details->save() ) {

                    DB::commit();
                    
                    return back()->with('flash_success', tr('admin_password_change_success'));
                
                } else {
                
                    throw new Exception(tr('admin_password_save_error'), 101);
                }
                
            } else {

                throw new Exception( tr('admin_password_mismatch'), 101);
            }
            
        } catch (Exception $e) {  
            
            DB::rollback();
            
            $error = $e->getMessage();

            return redirect()->back()->with('flash_error',$error);
        }
    
    }

    /**
     * Function: pages_index()
     * 
     * @uses To list the static_pages
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param
     *
     * @return view page
     */
    public function pages_index() {

        $pages = Page::orderBy('created_at' , 'desc')->paginate(10);

        return view('new_admin.pages.index')
                    ->with('page','pages')
                    ->with('sub_page','pages-view')
                    ->with('pages',$pages);
    }

    /**
     * @method pages_create()
     *
     * @uses To list out pages object details
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param
     *
     * @return View page
     */
    public function pages_create() {

        $page_details = new Page;

        return view('new_admin.pages.create')
                    ->with('page' , 'pages')
                    ->with('sub_page',"pages-create")
                    ->with('page_details', $page_details);
    }
      
    /**
     * @method pages_edit()
     *
     * @uses To display and update pages object details based on the pages id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer (request) $static_page_id
     *
     * @return View page
     */
    public function pages_edit(Request $request) {

        try {
          
            $page_details = Page::find($request->page_id);

            if( !$page_details ) {

                throw new Exception( tr('admin_page_not_found'), 101);
            } 

            return view('new_admin.pages.edit')
                    ->with('page' , 'pages')
                    ->with('sub_page','pages-view')
                    ->with('page_details',$page_details);

        } catch( Exception $e) {
            
            $error = $e->getMessage();

            return redirect()->route('admin.pages.index')->with('flash_error',$error);
        }
    }

    /**
     * @method pages_save()
     *
     * @uses To save the page object details of new/existing based on details
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer (request) $page_id , (request) page details
     *
     * @return success/error message
     */
    public function pages_save(Request $request) {

        try {

            DB::beginTransaction();

            $validator = Validator::make($request->all() , array(
                'type' => $request->page_id ? '' : 'required',
                'title' => 'required|max:255',
                'description' => 'required',
            ));

            if( $validator->fails() ) {

                $error = implode(',',$validator->messages()->all());

                throw new Exception($error, 101);                
            } 

            if( $request->has('page_id') ) {

                $page_details = Page::find($request->page_id);

            } else {

                if(Page::count() < Setting::get('no_of_static_pages')) {

                    if( $request->type != 'others' ) {

                        $check_page_type = Page::where('type',$request->type)->first();

                        if($check_page_type){

                            throw new Exception(tr('admin_page_exists').$request->type , 101);
                        }
                    }
                    
                    $page_details = new Page;
                    
                } else {

                    throw new Exception(tr('admin_page_exists').$request->type , 101);
                }                    
            }

            if( $page_details ) {

                $page_details->type = $request->type ? $request->type : $page_details->type;

                $page_details->title = $request->title ? $request->title : $page_details->title;

                $page_details->description = $request->description ? $request->description : $page_details->description;

                if( $page_details->save() ) {

                    DB::commit();

                    return redirect()->route('admin.pages.view', ['page_id' => $page_details->id])->with('flash_success',tr('admin_page_create_success'));
                } 

                throw new Exception(tr('admin_page_save_error'), 101);
            }

        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return redirect()->back()->withInput()->with('flash_error',$error);
        }

    }

    /**
     * Function: pages_view()
     * 
     * @uses To display pages details based on pages id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer (request) $page_id
     *
     * @return view page
     */
    public function pages_view(Request $request) {

        try {

            $page_details = Page::find($request->page_id);
            
            if( !$page_details ) {

                throw new Exception(tr('admin_page_not_found'), 101);
            }

            return view('new_admin.pages.view')
                        ->with('page' ,'pages')
                        ->with('sub_page' ,'pages-view')
                        ->with('page_details' ,$page_details);

        } catch (Exception $e) {

            $error = $e->getMessage();

            return redirect()->route('admin.pages.index')->with('flash_error',$error);
        }
    }

    /**
     * Function: pages_delete()
     * 
     * @uses To delete the page object based on page id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param 
     *
     * @return success/failure message
     */
    public function pages_delete(Request $request) {

        try {

            DB::beginTransaction();
            
            $page_details = Page::where('id' , $request->page_id)->first();

            if( !$page_details ) {  

                throw new Exception(tr('admin_page_not_found'), 101);
            }

            Helper::delete_picture($page_details->picture, "/uploads/images/pages/");
            
            if( $page_details->delete() ) {

                DB::commit();

                return redirect()->route('admin.pages.index')->with('flash_success',tr('admin_page_delete_success'));
            } 
            
            throw new Exception(tr('admin_page_delete_error'), 101);               
            
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    }

    /**
     * @method pages_status()
     *
     * @uses To change the status of the pages, based on page id. only admin can access this option
     * 
     * @created Bhawya N
     *
     * @updated Bhawya N 
     *
     * @param object $request - Page Id
     *
     * @return response of html page with details
     */
    public function pages_status(Request $request) {

        try {

            DB::beginTransaction();
       
            $page_details = Page::find($request->page_id);

            if(!$page_details) {
                
                throw new Exception(tr('admin_page_not_found'), 101);
            } 
            
            $page_details->status = $page_details->status == APPROVED ? DECLINED : APPROVED;

            $message = $page_details->status == APPROVED ? tr('admin_page_approve_success') : tr('admin_page_decline_success');

            if( $page_details->save() ) {

                DB::commit();

                return back()->with('flash_success', $message);
            } 

            throw new Exception(tr('admin_page_status_error'), 101);
            
        } catch( Exception $e) {

            DB::rollback();
            
            $error = $e->getMessage();

            return redirect()->route('admin.pages.index')->with('flash_error',$error);
        }
    }
    /**
     * @method banner_ads_index()
     *
     * @uses To list out banner_ads object details
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param
     *
     * @return View page
     */
    public function banner_ads_index() {

        $banner_ads = BannerAd::orderBy('position' , 'asc')->get();

        return view('new_admin.banner_ads.index')
                    ->with('page', 'banner-ads')
                    ->with('sub_page', 'banner-ads-view')
                    ->with('banner_ads', $banner_ads);
    }

    /**
     * @method banner_ads_create()
     *
     * @uses To create a banner_ads object details
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param 
     *
     * @return View page
     */
    public function banner_ads_create(Request $request) {

        $banner_ad_details = new BannerAd;

        $banner_position = BannerAd::orderBy('position', 'desc')->first();

        $banner_ad_details->position = $banner_position ? $banner_position->position + DEFAULT_TRUE : DEFAULT_TRUE;

        return view('new_admin.banner_ads.create')
                    ->with('page', 'banner-ads')
                    ->with('sub_page', 'banner-ads-create')
                    ->with('banner_ad_details', $banner_ad_details);
    }

    /**
     * @method banner_ads_edit
     *
     * @uses To edit a banner_ads based on their id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - banner_ads_id
     * 
     * @return response of new User object
     *`
     */
    public function banner_ads_edit(Request $request) {
        
        try {
          
            $banner_ad_details = BannerAd::find($request->banner_ad_id);

            if( !$banner_ad_details ) {

                throw new Exception( tr('admin_banner_ad_not_found'), 101);
            } 
            
            return view('new_admin.banner_ads.edit')
                    ->with('page' , 'banner-ads')
                    ->with('sub_page','banner-ads-view')
                    ->with('banner_ad_details',$banner_ad_details);

        } catch( Exception $e) {
            
            $error = $e->getMessage();

            return redirect()->route('admin.banner_ads.index')->with('flash_error',$error);
        }
    
    }
     /**
     * @method banner_ad_save
     *
     * @uses To save/update banner_ad object based on banner_ad id or details
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - banner_ad_id, (request) details
     * 
     * @return success/failure message.
     *
     */
    public function banner_ads_save(Request $request) {

        try {
           
            DB::beginTransaction();

            $validator = Validator::make($request->all(),[
                    'title' => 'required|max:255',
                    'description' => 'required',
                    'position' => $request->banner_ad_id ? 'required' :'required|unique:banner_ads',
                    'link' => 'required|url',
                    'file' => $request->banner_ad_id ? 'mimes:jpeg,png,jpg' : 'required|mimes:jpeg,png,jpg'
            ]);
            
            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                return back()->with('flash_errors', $error);
            } 
            
            $banner_ad_details = $request->banner_ad_id ? BannerAd::find($request->banner_ad_id) : new BannerAd;

            $banner_ad_details->title = $request->title ? $request->title : "";

            $banner_ad_details->description = $request->description ? $request->description : "";

            $banner_ad_details->position = $request->position;

            $banner_ad_details->link = $request->link;

            if($request->hasFile('file')) {

                if ($request->banner_ad_id) {

                    Helper::delete_picture($banner_ad_details->file, '/uploads/banners/');
                } 

                $banner_ad_details->file = Helper::normal_upload_picture($request->file('file'), '/uploads/banners/');
            }

            $banner_ad_details->status = DEFAULT_TRUE;

            if( $banner_ad_details->save()) {
            
                DB::commit();

                $message = $request->banner_ad_id ? tr('admin_banner_ad_update_success') : tr('admin_banner_ad_create_success');

                return redirect()->route('admin.banner_ads.view', ['banner_ad_id' => $banner_ad_details->id])->with('flash_success', $message);
            } 

            throw new Exception(tr('admin_banner_ad_save_error'), 101);
                      
        } catch( Exception $e) {
            
            DB::rollback();
            
            $error = $e->getMessage();

            return redirect()->route('admin.banner_ads.index')->with('flash_error',$error);
        }

    }

    /**
     * @method banner_ads_delete
     *
     * @uses To delete banner_ad details based on banner_ad id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - banner_ad_id
     * 
     * @return success/failure message.
     *
     */
    public function banner_ads_delete(Request $request) {

        try {
            
            DB::beginTransaction();

            $banner_ad_details = BannerAd::find($request->banner_ad_id);

            if (!$banner_ad_details) {

                throw new Exception(tr('admin_banner_ad_not_found'), 101);
            }            
            
            // Check the current position 

            $current_position = $banner_ad_details->position;

            $banner = BannerAd::orderBy('position', 'desc')->first();

            $last_position = $banner ? $banner->position : "";

            if($last_position == $current_position) {

                // Do nothing

            } else if($current_position < $last_position) {

                // Update remaining records positions

                DB::update("UPDATE banner_ads SET position =  position-1 WHERE position > $current_position");

                DB::commit();                
            }

            Helper::delete_picture($banner_ad_details->file, '/uploads/banners/');

            if ( $banner_ad_details->delete()) {  

                DB::commit();
                
                return redirect()->route('admin.banner_ads.index')->with('flash_success', tr('admin_banner_ad_delete_success'));

            }
            
            throw new Exception(tr('admin_banner_ad_delete_error'), 101);
                       
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }

    }

    /**
     * @method  banner_ads_status_change
     *
     * @uses To update the banner_ad status to APPROVE/DECLINE based on banner_ad id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - coupon_id
     * 
     * @return success/failure message.
     *
     */
    public function  banner_ads_status_change(Request $request) {

        try {
            
            DB::beginTransaction();

            $banner_ads_details = BannerAd::find($request->banner_ad_id);

            if ( count($banner_ads_details) == 0 ) {

               throw new Exception(tr('admin_banner_ad_not_found'), 101);
            }

            $banner_ads_details->status = $banner_ads_details->status == DEFAULT_TRUE ? DEFAULT_FALSE : DEFAULT_TRUE ;

            if( $banner_ads_details->save() ) {
                
                $message = $banner_ads_details->status == DEFAULT_TRUE ? tr('admin_banner_ad_approved_success') : tr('admin_banner_ad_declined_success') ;
                
                DB::commit();                

                return back()->with('flash_success',$message );
            } 
                
            throw new Exception(tr('admin_banner_ad_status_error'), 101);
            
        } catch (Exception $e) {

            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    }

        /**
     * @method banner_ads_view
     *
     * @uses To view the banner_ad based on the banner_ad id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer (request) $banner_ad_id
     * 
     * @return view page
     *
     */
    public function banner_ads_view(Request $request) {

        try {

            $banner_ad_details = BannerAd::find($request->banner_ad_id);

            if (!$banner_ad_details) {

                throw new Exception(tr('admin_banner_ad_not_found'), 101);
            }

            return view('new_admin.banner_ads.view')
                        ->with('page','banner-ads')
                        ->with('sub_page','banner-ads-view')
                        ->with('banner_ad_details',$banner_ad_details);        
            
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    
    }

    /**
     * Function Name: banner_ads_position()
     *
     * @uses To change the banner_ad position 
     *
     * @created anjana H
     *
     * @updated Anjana H
     *
     * @param Object $request - banner_ad Id 
     *
     * @return banner_ad response of success/failure response
     */
    public function banner_ads_position(Request $request) {
        
        try {
            
            DB::beginTransaction();

            $banner_ads_details = BannerAd::find($request->banner_ad_id);

            if ( count($banner_ads_details) == 0 ) {

                throw new Exception(tr('admin_banner_ad_not_found'), 101);
            } 

            $position = $banner_ads_details->position;

            $current_position = $request->position;

            // Load Current Position Banner

            $current_position_banner = BannerAd::where('position', $current_position)->first();

            if ( !$current_position_banner ) {

                throw new Exception(tr('current_position_banner_ad_not_available'), 101);
            }

            $current_position_banner->position = $position;

            $current_position_banner->save();

            if ($current_position_banner) {

                $banner_ads_details->position = $current_position;
                
                if( $banner_ads_details->save() ) {
                                                        
                    DB::commit();                

                    return back()->with('flash_success',tr('admin_banner_ad_position_change_success') );

                } else {
                    
                    throw new Exception(tr('admin_banner_ad_position_change_error'), 101);
                }

            } 
            
            throw new Exception(tr('admin_banner_ad_position_change_error'), 101);
                        
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    
    }
    
    /**
     * @method banner_videos_create()
     *
     * To create a banner video based on id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $id - Video Id
     * 
     * @return view Page 
     *
     */
    public function banner_videos_create(Request $request) {

        $channels = getChannels();
        if(HomeVideo::first())
            $model = HomeVideo::first();
        else
            $model = new HomeVideo;

        return view('new_admin.home_videos.create')
                    ->with('page' ,'banner-videos')
                    ->with('sub_page' ,'banner-video-create')
                    ->with('banner_video', $model)
                    ->with('channels' , $channels);

        // return view('new_admin.banner_videos.create')
        //             ->with('page' ,'banner-videos')
        //             ->with('sub_page' ,'banner-video-create')
        //             ->with('home_video_details ' , $home_video_details )
        //             ->with('channels' , $channels);

    }

    public function banner_videos_save(Request $request) {
        $model = new HomeVideo;
        $model->title = $request->name;
        $path = '/uploads/videos/homepage/';
        $video = Helper::normal_upload_picture($request->file('file'), $path);
        $model->video = $video;
        $model->save();
        return back();
    }
    /**
     * @method banner_videos_set()
     *
     * @uses To set a video as banner based on video id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $id - Video Id
     * 
     * @return response of success/failure message
     *
     */
    public function banner_videos_set(Request $request) {

        try {
            
            DB::beginTransaction();

            $video_details = VideoTape::find($request->id);
            
            if(!$video_details) {

                throw new Exception(tr('admin_banner_video_not_found'), 101);
            }

            $current_slider_video = VideoTape::where('is_home_slider' , DEFAULT_TRUE )->update(['is_home_slider' => DEFAULT_FALSE]); 

            $video_details = VideoTape::where('id' , $request->id)->update(['is_home_slider' => DEFAULT_TRUE] );
            
            DB::commit();

            return back()->with('flash_success', tr('admin_slider_video_update_success'));

        } catch (Exception $e) {
            
            DB::rollback();
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    
    }

    /**
     * @method banner_videos()
     *
     * @uses To list out all the banner videos 
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param 
     * 
     * @return View Page
     *
     */
    public function banner_videos_index(Request $request) {

        $video_tapes = VideoTape::leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                    ->where('video_tapes.is_banner' , DEFAULT_TRUE )
                    ->videoResponse()
                    ->orderBy('video_tapes.created_at' , 'desc')
                    ->get();

        return view('new_admin.banner_videos.index')
                    ->withPage('banner-videos')
                    ->with('sub_page','view-banner-videos')
                    ->with('video_tapes' , $video_tapes);   
    }


    /**
     * @method banner_videos_remove()
     *
     * @uses To remove a banner video based on id
     *
     * @created Anjana  H
     *
     * @updated Anjana  H
     *
     * @param Integer $id - Video Id
     * 
     * @return succes/failure message
     *
     */
    public function banner_videos_remove(Request $request) {
        
        try {
            
            DB::beginTransaction();

            $video_details = VideoTape::find($request->id);
            
            if(!$video_details) {

                throw new Exception(tr('admin_banner_video_not_found'), 101);
            }

            $video_details->is_banner = DEFAULT_FALSE ;

            $video_details->save();

            if( $video_details->save() ) {
                                                        
                DB::commit();                

                return back()->with('flash_success',tr('admin_banner_video_change_success') );
            } 
                
            throw new Exception(tr('admin_banner_video_change_error'), 101);
            
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    
    }

        /**
     * @method custom_live_videos_index()
     *
     * @uses To list out custom_live_videos object details
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param
     *
     * @return View page
     */
    public function custom_live_videos_index() {

        $custom_live_videos = CustomLiveVideo::orderBy('created_at','desc')->get();

        return view('new_admin.custom_live_videos.index')
                        ->withPage('custom_live_videos')
                        ->with('sub_page','custom_live_videos-view')
                        ->with('custom_live_videos' , $custom_live_videos);
    }

    /**
     * @method custom_live_videos_create()
     *
     * @uses To create a user object details
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param 
     *
     * @return View page
     */
    public function custom_live_videos_create(Request $request) {

        $custom_live_video_details = new CustomLiveVideo;

        return view('new_admin.custom_live_videos.create')
                    ->with('page' , 'custom_live_videos')
                    ->with('sub_page','custom_live_video-create')
                    ->with('custom_live_video_details', $custom_live_video_details);
    }

    /**
     * @method users_edit
     *
     * @uses To edit a user based on their id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - user_id
     * 
     * @return response of new User object
     *`
     */
    public function custom_live_videos_edit(Request $request) {
        
        try {
          
            $custom_live_video_details = CustomLiveVideo::find($request->custom_live_video_id);

            if( !$custom_live_video_details ) {

                throw new Exception( tr('admin_custom_live_video_not_found'), 101);

            } 

            return view('new_admin.custom_live_videos.edit')
                        ->with('page' , 'custom_live_videos')
                        ->with('sub_page','custom_live_videos-view')
                        ->with('custom_live_video_details',$custom_live_video_details);
        

        } catch( Exception $e) {
            
            $error = $e->getMessage();

            return redirect()->route('admin.users.index')->with('flash_error',$error);
        }    
    }
    

    /**
     * Function : custom_live_videos_save()
     *
     * @uses To edit/save a custom_live_video based on their id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param 
     *     
     * @return Save the form data of the live video
     */
    public function custom_live_videos_save(Request $request) {

        try {
            
            DB::beginTransaction();

            $response = AdminRepo::save_custom_live_video($request)->getData();
            
            if($response->success == DEFAULT_FALSE) {
                
                throw new Exception($response->message, 101);
            }

            DB::commit();
                
            return redirect()->route('admin.custom.live.view', ['custom_live_video_id' =>  $response->data->id])->with('flash_success', $response->message);

        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }

    }

    /**
     * Function: custom_live_videos_view()
     * 
     * @uses To display custom_live_videos details based on custom_live_videos id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer (request) $custom_live_video_id
     *
     * @return view page
     */
    public function custom_live_videos_view(Request $request) {

        try {

            $custom_live_video_details = CustomLiveVideo::find($request->custom_live_video_id);
            
            if( !$custom_live_video_details ) {

                throw new Exception(tr('admin_custom_live_video_not_found'), 101);
            }

            return view('new_admin.custom_live_videos.view')
                        ->with('page' ,'custom_live_videos')
                        ->with('sub_page' ,'custom_live_videos-view')
                        ->with('custom_live_video_details' ,$custom_live_video_details);

        } catch (Exception $e) {

            $error = $e->getMessage();

            return redirect()->route('admin.custom.live.index')->with('flash_error',$error);
        }
    }
    /**
     * @method custom_live_videos_delete
     *
     * @uses To delete the custom_live_video based on custom_live_video id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param integer (request) $custom_live_video_id
     * 
     * @return response of custom_live_video edit
     *
     */
    public function custom_live_videos_delete(Request $request) {

        try {
            
            DB::beginTransaction();

            $custom_live_video_details = CustomLiveVideo::find($request->custom_live_video_id);

            if (!$custom_live_video_details) {

                throw new Exception(tr('admin_custom_live_video_not_found'), 101);
            }
            
            if ($custom_live_video_details->delete()) {  

                DB::commit();
                
                return redirect()->route('admin.custom.live.index')->with('flash_success',tr('admin_custom_live_video_delete_success'));
            } 

            throw new Exception(tr('admin_custom_live_video_delete_success'), 101);
            
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return redirect()->back()->with('flash_error',$error);
        }    
    }
        /**
     * @method custom_live_videos_status_change
     *
     * @uses To change the custom_live_video status of approve and decline 
     *
     * @created 
     *
     * @updated 
     *
     * @param integer (request) $custom_live_video_id
     * 
     * @return success/failure message
     */
    public function custom_live_videos_status_change(Request $request) {
        
        try {

            DB::beginTransaction();

            $custom_live_video_details = CustomLiveVideo::find($request->custom_live_video_id);

            if( !$custom_live_video_details) {

                throw new Exception(tr('admin_custom_live_video_not_found'), 101);
            }

            $custom_live_video_details->status = $custom_live_video_details->status == DEFAULT_TRUE ? DEFAULT_FALSE : DEFAULT_TRUE;

            $message = $custom_live_video_details->status == DEFAULT_TRUE ? tr('admin_custom_live_video_approved_success') :  tr('admin_custom_live_video_declined_success') ;

            if( $custom_live_video_details->save() ) {

                DB::commit();

                return back()->with('flash_success', $message);            
            } 

            throw new Exception(tr('admin_custom_live_video_status_error'), 101);
            
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    
    }

    /**
     * @method subscriptions_index()
     *
     * @uses To list out subscriptions object details
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param
     *
     * @return View page
     */
    public function subscriptions_index() {

        $subscriptions = Subscription::orderBy('created_at','desc')->get();

        return view('new_admin.subscriptions.index')
                    ->withPage('subscriptions')
                    ->with('sub_page','subscriptions-view')
                    ->with('subscriptions' , $subscriptions);
    }

    /**
     * @method subscriptions_create()
     *
     * @uses To create a subscription object details
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param 
     *
     * @return View page
     */
    public function subscriptions_create(Request $request) {

        $subscription_details = new Subscription;

        return view('new_admin.subscriptions.create')
                    ->with('page' , 'subscriptions')
                    ->with('sub_page','subscriptions-create')
                    ->with('subscription_details', $subscription_details);
    }

    /**
     * @method subscriptions_edit
     *
     * @uses To update a subscription details based on their id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - subscription_id
     * 
     * @return response of new subscription object
     *`
     */
    public function subscriptions_edit(Request $request) {
        
        try {
          
            $subscription_details = Subscription::find($request->subscription_id);

            if( !$subscription_details ) {

                throw new Exception( tr('admin_subscription_not_found'), 101);
            } 

            return view('new_admin.subscriptions.edit')
                        ->with('page' , 'subscriptions')
                        ->with('sub_page','subscriptions-view')
                        ->with('subscription_details',$subscription_details);
           
        } catch( Exception $e) {
            
            $error = $e->getMessage();

            return redirect()->route('admin.subscriptions.index')->with('flash_error',$error);
        }    
    }
    
    /**
     * @method subscriptions_save
     *
     * @uses To save/update subscription object based on subscription id or details
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - subscription_id, (request) details
     * 
     * @return success/failure message.
     *
     */
    public function subscriptions_save(Request $request) {

        try {
            
            DB::beginTransaction();

            $validator = Validator::make($request->all(),[
                'title' => 'required|max:255',
                'plan' => 'required|integer|between:1,12',
                'amount' => 'required',
                'picture' => 'mimes:jpeg,png,jpg',
                'description' => 'max:255',
            ]);

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);  
            } 
            
            $subscription_details = $request->subscription_id ? Subscription::find($request->subscription_id) : new Subscription; 
            
            $subscription_details->status = $request->subscription_id != '' ? $subscription_details->status :  APPROVED; 
          
            $subscription_details->title = $request->title; 
            
            $subscription_details->plan = $request->plan;  

            $subscription_details->amount = $request->amount;

            $subscription_details->description = $request->description;
            // options
            $subscription_details->ppv_income   = $request->ppv_income;
            $subscription_details->limit_data   = $request->limit_data;
            $subscription_details->ads_income   = $request->ads_income;
            $subscription_details->ads_us       = $request->ads_us;
            $subscription_details->content_num  = $request->content_num;
            
            if($request->hasFile('picture')) {
                
                if($request->subscription_id != '') {

                    $subscription_details->picture ? Helper::delete_picture('uploads/subscriptions' , $subscription_details->picture) : "";
                }

                $picture = Helper::upload_avatar('uploads/subscriptions' , $request->file('picture'));
            }
            
            if( $subscription_details->save()) {
                
                DB::commit();
                            
                $message = $request->subscription_id ? tr('admin_subscription_update_success') : tr('admin_subscription_create_success') ;

                return redirect()->route('admin.subscriptions.view', ['subscription_id' => $subscription_details->id] )->with('flash_success', $message);
            } 

            throw new Exception(tr('admin_subscription_save_error'), 101);
           
        } catch (Exception $e) {
            
            DB::rollback();
            
            $error = $e->getMessage();

            return back()->withInput()->with('flash_error',$error);
        }    
    }

    /**
     * Function: subscriptions_view()
     * 
     * @uses To display subscription details based on subscription id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer (request) $subscription_id
     *
     * @return view page
     */
    public function subscriptions_view(Request $request) {

        try {

            $subscription_details = Subscription::find($request->subscription_id);
            
            if( !$subscription_details ) {

                throw new Exception(tr('admin_subscription_not_found'), 101);
            }

            return view('new_admin.subscriptions.view')
                    ->with('page' ,'subscriptions')
                    ->with('sub_page' ,'subscriptions-view')
                    ->with('subscription_details' ,$subscription_details);

        } catch (Exception $e) {

            $error = $e->getMessage();

            return redirect()->route('admin.subscriptions.index')->with('flash_error',$error);
        }
    }

    /**
     * @method subscriptions_delete
     *
     * @uses To delete the subscription based on subscription id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param integer (request) $subscription_id
     * 
     * @return response of subscription edit
     *
     */
    public function subscriptions_delete(Request $request) {

        try {

            DB::beginTransaction();
        
            $subscription_details = subscription::find($request->subscription_id);

            if (!$subscription_details) {

                throw new Exception(tr('admin_subscription_not_found'), 101);
            }
            
            if ($subscription_details->delete()) {  

                DB::commit();
                
                return redirect()->route('admin.subscriptions.index')->with('flash_success',tr('admin_subscription_delete_success'));
            } 

            throw new Exception(tr('admin_subscription_delete_success'), 101);
           
            
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }    
    }

    /**
     * @method subscriptions_status_change
     *
     * @uses To update the subscription status to APPROVE/DECLINE based on subscription id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - subscription_id
     * 
     * @return success/failure message.
     *
     */
    public function subscriptions_status_change(Request $request) {

        try {

            DB::beginTransaction();

            $subscriptions_details = Subscription::find($request->subscription_id);

            if ( count($subscriptions_details) == 0 ) {

               throw new Exception(tr('admin_subscription_not_found'), 101);
            }

            $subscriptions_details->status = $subscriptions_details->status == APPROVED ? DECLINED : APPROVED ;

            if( $subscriptions_details->save() ) {
                
                $message = $subscriptions_details->status == APPROVED ? tr('admin_subscription_approved_success') : tr('admin_subscription_declined_success') ;
                
                DB::commit();                

                return back()->with('flash_success',$message );
            } 
                
            throw new Exception(tr('admin_subscription_status_error'), 101);
                        
        } catch (Exception $e) {

            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    }

    /**
     * Function Name: subscription_payments()
     *
     * @uses To display subscription payments list or subscriptions based on subscription_id     
     *
     * @created 
     *
     * @updated
     *
     * @param
     *
     * @return view page
     */
    public function subscription_payments(Request $request) {

        $base_query = UserPayment::orderBy('created_at' , 'desc');

        $subscription_details = [];

        if($request->subscription_id) {

            $subscription_details = Subscription::find($request->subscription_id);

            $base_query = $base_query->where('subscription_id' , $request->subscription_id);
        }

        $payments = $base_query->get();

        return view('new_admin.payments.subscription-payments')
                ->withPage('payments')
                ->with('sub_page','payments-subscriptions')
                ->with('payments' , $payments)
                ->with('subscription_details' , $subscription_details);     
    }

    /**
     * @method auto_renewal_subscribers
     *
     * @uses To list out auto-renewal subscribers
     *
     * @created 
     *
     * @updated 
     *
     * @param integer $id - User id (Optional)
     * 
     * @return - response of array of automatic subscribers
     *
     */
    public function auto_renewal_subscribers() {

        $user_payment_details = UserPayment::select(DB::raw('max(user_payments.id) as user_payment_id'),'user_payments.*')
                        ->leftjoin('subscriptions', 'subscriptions.id','=' ,'subscription_id')
                        ->where('subscriptions.amount', '>', 0)
                        ->where('user_payments.status', PAID_STATUS)
                        //->where('user_payments.is_cancelled', AUTORENEWAL_ENABLED)
                        ->groupBy('user_payments.user_id')
                        ->orderBy('user_payments.created_at' , 'desc')
                        ->get();

        $payments = [];

        $amount = 0;

        foreach ($user_payment_details as $key => $value) {
    
            $value = UserPayment::find($value->user_payment_id);

            if ($value->is_cancelled == AUTORENEWAL_ENABLED) {

                if ($value->getSubscription) {

                    $amount += $value->getSubscription ? $value->getSubscription->amount : 0;
                }

                $payments[] = [

                    'id'=> $value->id,

                    'user_id' => $value->user_id,

                    'subscription_id' => $value->subscription_id,

                    'payment_id' => $value->payment_id,

                    'amount' => $value->getSubscription ? $value->getSubscription->amount : '',

                    'payment_mode' => $value->payment_mode,

                    'expiry_date' => date('d-m-Y H:i a', strtotime($value->expiry_date)),

                    'user_name'  =>  $value->user ? $value->user->name : '',

                    'subscription_name' => $value->getSubscription ? $value->getSubscription->title : '',

                    'unique_id' => $value->getSubscription ? $value->getSubscription->unique_id : '',

                ];

            } else {

                Log::info('Subscription not found');
            }
        }

        $payments = json_decode(json_encode($payments));

        return view('new_admin.subscriptions.subscribers.auto-renewal')
                        ->withPage('subscriptions')                        
                        ->with('sub_page','subscriptions-auto-renewal-subscribers')
                        ->with('amount', $amount)
                        ->with('payments', $payments);        

    }

    /**
     * @method auto_renewal_cancelled_subscribers
     *
     * @uses To list out auto-renewal cancelled subscribers
     *
     * @created Anjana H
     *
     * @updated Anjan H
     *
     * @param integer $id - User id (Optional)
     * 
     * @return - response of array of cancelled subscribers
     *
     */
    public function auto_renewal_cancelled_subscribers() {

        $user_payment_details = UserPayment::select(DB::raw('max(user_payments.id) as user_payment_id'),'user_payments.*')
                        ->where('user_payments.status', PAID_STATUS)
                        ->leftjoin('subscriptions', 'subscriptions.id','=' ,'subscription_id')
                        ->where('user_payments.is_cancelled', AUTORENEWAL_CANCELLED)
                        ->groupBy('user_payments.user_id')
                        ->orderBy('user_payments.created_at' , 'desc')
                        ->get();

        $payments = [];

        foreach ($user_payment_details as $key => $value) {

            $value = UserPayment::find($value->user_payment_id);
            
            $payments[] = [

                'id' => $value->user_payment_id,

                'user_id' => $value->user_id,

                'subscription_id' => $value->subscription_id,

                'payment_id' => $value->payment_id,

                'amount' => $value->getSubscription ? $value->getSubscription->amount : '',

                'payment_mode' => $value->payment_mode,

                'expiry_date' => date('d-m-Y H:i a', strtotime($value->expiry_date)),

                'user_name' => $value->user ? $value->user->name : '',

                'subscription_name' => $value->getSubscription ? $value->getSubscription->title : '',

                'unique_id' => $value->getSubscription ? $value->getSubscription->unique_id : '',

                'cancel_reason' => $value->cancel_reason
            ];
        }

        $payments =json_decode(json_encode($payments));

        return view('new_admin.subscriptions.subscribers.cancelled')
                    ->withPage('subscriptions')
                    ->with('sub_page','subscriptions-cancelled-subscribers')
                    ->with('payments', $payments);      

    }


    /**
     * @method user_subscription_auto_renewal_disable
     *
     * @uses To disable auto-renewal subscription, user can cancel subscription auto-renewal
     *
     * @created Anjana H
     *
     * @updated Anjan H
     *
     * @param object $request - User details & payment details
     *
     * @return success/failure message
     */
    public function user_subscription_auto_renewal_disable(Request $request) {

        try {

            DB::beginTransaction();
        
            $user_payment_details = UserPayment::find($request->id);

            if (!$user_payment_details) {

                throw new Exception(tr('admin_subscription_not_found'), 101);
            }

            $user_payment_details->is_cancelled = AUTORENEWAL_CANCELLED;

            $user_payment_details->cancel_reason = $request->cancel_reason;

            if($user_payment_details->save()) {

                DB::commit();
                
                return back()->with('flash_success', tr('admin_subscription_auto_renewal_disable_success'));
            } 
                
            throw new Exception(tr('admin_subscription_auto_renewal_disable_error'), 101);
            
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }       

    }
   
    /**
     * @method user_subscription_enable
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
    public function user_subscription_auto_renewal_enable(Request $request) {

        try {
            
            DB::beginTransaction();

            $user_payment_details = UserPayment::where('user_id', $request->user_id)->where('status', PAID_STATUS)->orderBy('created_at', 'desc')
                ->where('is_cancelled', AUTORENEWAL_CANCELLED)
                ->first();

            if( !$user_payment_details) {

                throw new Exception(tr('admin_user_payment_details_not_found'), 101);
            }

            $user_payment_details->is_cancelled = AUTORENEWAL_ENABLED;

            if($user_payment_details->save()) {

                DB::commit();
            
                return back()->with('flash_success', tr('autorenewal_enable_success'));
            } 

            throw new Exception(tr('admin_autorenewal_enable_success'), 101);
            

        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }      

    } 

    /**
     * Function Name: user_redeem_requests()
     *
     * @uses To list out redeem requests from users, admin can payout amount
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param integer $id - optional ( Redeem request id)
     *
     * @return view page 
     */
    public function user_redeem_requests(Request $request) {

        try {
            
            $base_query = RedeemRequest::orderBy('status' , 'asc');

            $user_details = [];

            if($request->user_id) {

                $base_query = $base_query->where('user_id' , $request->user_id);

                $user_details = User::find($request->user_id);
            }

            $redeem_requests = $base_query->orderBy('updated_at', 'desc')->get();

            return view('new_admin.users.redeems')
                    ->withPage('redeems')
                    ->with('sub_page' , 'redeems')
                    ->with('redeem_requests' , $redeem_requests)
                    ->with('user_details' , $user_details);
        
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    
    }
    
    /**
     * Function Name: revenues()
     *
     * @uses To list out the revenue models details
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param 
     *
     * @return view page 
     */
    public function revenues() {

        $total  = total_revenue();

        $ppv_total = PayPerView::sum('amount');

        $ppv_admin_amount = PayPerView::sum('admin_ppv_amount');

        $ppv_user_amount = PayPerView::sum('user_ppv_amount');

        $subscription_total = UserPayment::sum('amount');

        $total_subscribers = UserPayment::where('status' , '!=' , 0)->count();
        
        return view('new_admin.payments.revenues')
                    ->withPage('payments')
                    ->with('sub_page' , 'payments-dashboard')
                    ->with('total' , $total)
                    ->with('ppv_total' , $ppv_total)
                    ->with('ppv_admin_amount' , $ppv_admin_amount)
                    ->with('ppv_user_amount' , $ppv_user_amount)
                    ->with('subscription_total' , $subscription_total)
                    ->with('total_subscribers' , $total_subscribers);
    
    }

    /**
    * Function Name: ppv_payments()
    *
    * @uses To list out ppv payment details
    *     
    * @created Anjana  H
    *
    * @updated Anjana  H
    *
    * @param 
    *
    * @returnview page
    */
    public function ppv_payments() {

        $payments = PayPerView::select('pay_per_views.*', 'video_tapes.title', 'users.name as user_name')
            ->leftJoin('video_tapes', 'video_tapes.id', '=', 'pay_per_views.video_id')
            ->leftJoin('users', 'users.id', '=', 'pay_per_views.user_id')
            ->orderBy('pay_per_views.created_at' , 'desc')->get();
    
        return view('new_admin.payments.ppv-payments')
                    ->withPage('payments')
                    ->with('sub_page','payments-ppv')
                    ->with('payments' , $payments);
    }
    /**
     * Function Name: ppv_payments_view()
     *
     * @uses To view ppv payment details based on PayPerView id
     *     
     * @created Anjana  H
     *
     * @updated Anjana  H
     *
     * @param Integer (request) $id
     *
     * @return view page
     */
    public function ppv_payments_view(Request $request) {
        
        try {

            $payment_details = PayPerView::find($request->id);

            if(count($payment_details) == 0 ){

                throw new Exception(tr('admin_ppv_not_found'), 101);
            }

            $payment_details = PayPerView::select('pay_per_views.*', 'video_tapes.title', 'users.name as user_name')
                        ->leftJoin('video_tapes', 'video_tapes.id', '=', 'pay_per_views.video_id')
                        ->leftJoin('users', 'users.id', '=', 'pay_per_views.user_id')
                        ->where('pay_per_views.id', $request->id)
                        ->orderBy('pay_per_views.created_at' , 'desc')->first();

            return view('new_admin.payments.view')
                        ->withPage('payments')
                        ->with('sub_page','payments-ppv')
                        ->with('payment_details' , $payment_details);
            
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    }

    /**
     * @method user_reviews()
     *
     * @uses list out the reviews given by user
     *
     * @created Anjan H
     *
     * @updated Anjan H
     *
     * @param -
     *
     * @return view page
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

        return view('new_admin.reviews.reviews')
                    ->with('page' ,'video_tapes')
                    ->with('sub_page' ,'reviews')
                    ->with('reviews', $user_reviews);
    
    }

    /**
     * Function: settings()
     * 
     * @uses To display settings details
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param - 
     *
     * @return success/failure message
     */   
    public function settings() {

        $settings = array();

        $result = EnvEditorHelper::getEnvValues();

        return view('new_admin.settings.settings')
                    ->withPage('settings')
                    ->with('sub_page','site_settings')
                    ->with('settings' , $settings)
                    ->with('result', $result); 
    }

    /**
     * Function Name: ios_control()
     *
     * @uses To update the ios payment subscription status
     *
     * @param settings key value
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @return success / failure.
     */
    public function ios_control(){

        if(Auth::guard('admin')->check()){

            return view('new_admin.settings.ios-control')->with('page','ios-control');

        } else {

            return back();
        }
    }
    
    /**
     * Function Name: admin_control()
     *
     * @uses To update(enable/disable) admin control details in settings 
     *     
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param settings key value
     *
     * @return viwe page.
     */
    public function admin_control() {

        if (Auth::guard('admin')->check()) {

            return view('new_admin.settings.control')->with('page', tr('admin_control'));

        } else {

            return back();
        }
        
    }

    /**
     * Function: settings_save()
     * 
     * @uses to update settings details
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param (request) setting details
     *
     * @return success/error message
     */
    public function settings_save(Request $request) {
       
        try {
            
            DB::beginTransaction();
        
            foreach( $request->toArray() as $key => $value) {
              
                $check_settings = Settings::where('key' ,'=', $key)->count();

                if( $check_settings == 0 ) {

                    throw new Exception( $key.tr('admin_settings_key_not_found'), 101);
                }

                if($key == "admin_ppv_commission") {

                    $value = $request->admin_ppv_commission < 100 ? $request->admin_ppv_commission : 100;

                    $user_ppv_commission = $request->admin_ppv_commission < 100 ? 100 - $request->admin_ppv_commission : 0;

                    $user_ppv_commission_details = Settings::where('key' , 'user_ppv_commission')->first();

                    if(count($user_ppv_commission_details) > 0) {

                        $user_ppv_commission_details->value = $user_ppv_commission;


                        $user_ppv_commission_details->save();
                    }

                } 

                if ($key == "site_name") {

                    $site_name = preg_replace("/[^A-Za-z0-9]/", "", $value);

                    \Enveditor::set("SITENAME", $site_name);

                }

                $result = Settings::where('key' ,'=', $key)->update(['value' => $value]); 

                if($request->hasFile($key) ) {

                    Helper::delete_picture($key, "/uploads/settings/");

                    $file_path = Helper::normal_upload_picture($request->file($key), "/uploads/settings/");
                    
                    $result = Settings::where('key' ,'=', $key)->update(['value' => $file_path]); 

                }

                if( $result == TRUE ) {
                     
                    DB::commit();
               
                } else {

                    throw new Exception(tr('admin_settings_save_error'), 101);
                } 

            }

            return back()->with('flash_success', tr('admin_settings_key_save_success'));
            
        } catch (Exception $e) {

            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error', $error);
        }
    }

    /**
     * Function Name: custom_push()
     *
     * @uses To display custom message
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param 
     *
     * @return view page 
     */
    public function custom_push() {

        return view('new_admin.static_pages.push') 
                ->with('page' , 'custom-push')
                ->with('sub_page','custom-push')
                ->with('title' , tr('custom_push'));

    }
    /**
     * Function Name: custom_push_process()
     *
     * @uses To send custom push message to mobile
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param object $request - message details
     *
     * @return success/failure message
     */
    public function custom_push_process(Request $request) {
       
        try {

            $validator = Validator::make($request->all(),
                ['message' => 'required'] 
            );

            if($validator->fails()) {

                $error = $validator->messages()->all();

                throw new Exception($error, 101);                
            } 

            // Send notifications to the users
            $content = $request->message;

            $title = Setting::get('site_name');

            // dispatch(new sendPushNotification(PUSH_TO_ALL , $push_message , PUSH_REDIRECT_SINGLE_VIDEO , 29, 0, [] , PUSH_TO_CHANNEL_SUBSCRIBERS ));

            $push_data = ['type' => PUSH_REDIRECT_HOME];

            dispatch(new sendPushNotification(PUSH_TO_ALL , $title , $content, PUSH_REDIRECT_HOME , 0, 0, $push_data));

            return back()->with('flash_success' , tr('push_send_success'));
        
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->with('flash_error', $error);
        }
    
    }
    /**
     * Function Name: email_settings_process()
     * 
     * @uses Email Setting Process
     *
     * @created 
     *
     * @updated 
     *
     * @param 
     *
     * @return Html view page with coupon detail
     */
    public function email_settings_process(Request $request) {

        $email_settings = ['MAIL_DRIVER' , 'MAIL_HOST' , 'MAIL_PORT' , 'MAIL_USERNAME' , 'MAIL_PASSWORD' , 'MAIL_ENCRYPTION'];

        $admin_id = \Auth::guard('admin')->user()->id;

        foreach ($email_settings as $key => $data) {

            \Enveditor::set($data,$request->$data);            
        }

        return redirect()->route('clear-cache')->with('flash_success' , tr('email_settings_success'));

    }

      /**
     * @method ads_details_ad_status_change()
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

        try {

            DB::beginTransaction();

            $video_tape_details = VideoTape::find($request->video_tape_id);

            if(!$video_tape_details) { 

                throw new Exception(tr('admin_video_tape_not_found'), 101);
            }   

            $video_tape_details->ad_status  = $video_tape_details->ad_status == DEFAULT_TRUE ? DEFAULT_FALSE : DEFAULT_TRUE;

            if($video_tape_details->save()) {

                DB::commit();

                $video_ad_details = VideoAd::where('video_tape_id', $video_tape_details->id)->first();

                if ($video_ad_details) {

                    $video_ad_details->status = $data->ad_status;

                    $video_ad_details->save();
                }

                $message = $video_tape_details->ad_status == DEFAULT_TRUE ? tr('ad_status_enable_success') : tr('ad_status_disable_success'); 

                return back()->with('flash_success', $message);
            } 

            throw new Exception(tr('ad_status_change_failure'), 101);
           
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error', $error);
        }
    }

    /**
     * @method user_reviews_delete
     *
     * @uses To delete user_reviews details based on UserRating id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - UserRating_id
     * 
     * @return success/failure message.
     *
     */
    public function user_reviews_delete(Request $request) {

        try {
            
            DB::beginTransaction();
        
            $user_rating_details = UserRating::find($request->user_rating_id);

            if (!$user_rating_details) {

                throw new Exception(tr('admin_user_rating_not_found'), 101);
            }

            if ($user_rating_details->delete()) {  

                DB::commit();
                
                return back()->with('flash_success',tr('admin_user_rating_delete_success'));
            } 
            
            throw new Exception(tr('admin_user_rating_delete_error'), 101);
                       
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }

    }

    /**
     * @method sub_admins_index()
     *
     * @uses To list out subadmins (only admin can access this option)
     * 
     * @created Anjana H
     *
     * @updated Anjana H  
     *
     * @param object $request
     *
     * @return view page
     */
    public function sub_admins_index() {

        $sub_admins = Admin::orderBy('created_at', 'desc')->where('role', SUBADMIN)->get();

        return view('new_admin.sub_admins.index')
                ->with('page', 'sub-admins')
                ->with('sub_page', 'sub-admins-view')
                ->with('sub_admins', $sub_admins);        
    }

    /**
     * @method sub_admins_create()
     *
     * To create a sub admin only admin can access this option
     * 
     * @created Anjana H
     *
     * @updated Anjana H  
     *
     * @param object $request - -
     *
     * @return response of html page with details
     */
    public function sub_admins_create() {

        $sub_admin_details = new Admin();

        return view('new_admin.sub_admins.create')
                ->with('page', 'sub-admins')
                ->with('sub_page', 'sub-admins-create')
                ->with('sub_admin_details', $sub_admin_details);
    }

    /**
     * @method sub_admins_edit()
     *
     * @uses To edit a sub admin based on subadmin id only  admin can access this option
     * 
     * @created
     *
     * @updated 
     *
     * @param object $request - sub Admin Id
     *
     * @return response of html page with details
     */
    public function sub_admins_edit(Request $request) {

       try {
          
            $sub_admin_details = Admin::find($request->sub_admin_id);

            if( !$sub_admin_details ) {

                throw new Exception( tr('admin_sub_admin_not_found'), 101);

            }

            return view('new_admin.sub_admins.edit')
                        ->with('page', 'sub-admins')
                        ->with('sub_page', 'sub-admins-view')
                        ->with('sub_admin_details', $sub_admin_details);

        } catch( Exception $e) {
            
            $error = $e->getMessage();

            return redirect()->route('admin.sub_admins.index')->with('flash_error',$error);
        }
    }

    /**
     * @method sub_admins_view()
     *
     * @uses To view a sub admin based on sub admin id only admin can access this option
     * 
     * @created Anjana H
     *
     * @updated Anjana H  
     *
     * @param object $request - Sub Admin Id
     *
     * @return response of html page with details
     */
    public function sub_admins_view(Request $request) {

        try {
          
            $sub_admin_details = Admin::find($request->sub_admin_id);

            if( !$sub_admin_details ) {

                throw new Exception( tr('admin_sub_admin_not_found'), 101);
            } 

            return view('new_admin.sub_admins.view')
                    ->with('page', 'sub-admins')
                    ->with('sub_page', 'sub-admins-view')
                    ->with('sub_admin_details', $sub_admin_details);
        
           } catch( Exception $e) {
            
            $error = $e->getMessage();

            return redirect()->route('admin.sub_admins.index')->with('flash_error',$error);
        }
    }

    /**
     * @method sub_admins_delete()
     *
     * @uses To delete a sub admin based on sub admin id. only admin can access this option
     * 
     * @created Anjana H
     *
     * @updated Anjana H  
     *
     * @param object $request - Sub Admin Id
     *
     * @return response of html page with details
     */
    public function sub_admins_delete(Request $request) {

        try {

            DB::beginTransaction();
            
            $sub_admin_details = Admin::where('id' , $request->sub_admin_id)->first();

            if(!$sub_admin_details ) {  

                throw new Exception(tr('admin_sub_admin_not_found'), 101);
            }
            
            if( $sub_admin_details->delete() ) {

                DB::commit();

                return redirect()->route('admin.sub_admins.index')->with('flash_success',tr('admin_sub_admin_delete_success'));
            }

            throw new Exception(tr('admin_sub_admin_delete_error'), 101);
            
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    }

    /**
     * @method sub_admins_save()
     *
     * @uses To save the sub admin details
     * 
     * @created Anjana H
     *
     * @updated Anjana H  
     *
     * @param object $request - Sub Admin Id
     *
     * @return response of html page with details
     */
    public function sub_admins_save(Request $request) {

        try {
            
            DB::beginTransaction();

            $validator = Validator::make( $request->all(),array(
                    'name' => 'required|max:100',
                    'email' => $request->sub_admin_id ? 'email|max:255|unique:admins,email,'.$request->sub_admin_id : 'required|email|max:255|unique:admins,email,NULL',
                    'mobile' => 'digits_between:4,16',
                    'address' => 'max:300',
                    'sub_admin_id' => 'exists:admins,id',
                    'picture' => 'mimes:jpeg,jpg,png',
                    'description'=>'required|max:255',
                    'password' => $request->sub_admin_id ? '' : 'required|min:6|confirmed',
                )
            );
            
            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);
            } 

            $sub_admin_details = $request->sub_admin_id ? Admin::find($request->sub_admin_id) : new Admin;

            if (!$sub_admin_details) {

                throw new Exception(tr('sub_admin_not_found'), 101);
            }

            $sub_admin_details->name = $request->name ?: $sub_admin_details->name;

            $sub_admin_details->email = $request->email ? $request->email : $sub_admin_details->email;

            $sub_admin_details->mobile = $request->has('mobile') ? $request->mobile : $sub_admin_details->mobile;

            $sub_admin_details->description = $request->description ? $request->description : '';
            // options
            $sub_admin_details->users       = $request->has('mobile') ? $request->users : $sub_admin_details->users;
            $sub_admin_details->channels    = $request->channels;
            $sub_admin_details->categories  = $request->categories;
            $sub_admin_details->tags    = $request->tags;
            $sub_admin_details->videos  = $request->videos;
            $sub_admin_details->ads     = $request->ads;
            $sub_admin_details->banner_ads_m    = $request->banner_ads_m;
            $sub_admin_details->banner_videos   = $request->banner_videos;
            $sub_admin_details->subscriptions   = $request->subscriptions;
            $sub_admin_details->coupons         = $request->coupons;
            $sub_admin_details->custom_push     = $request->custom_push;

            $sub_admin_details->role = SUBADMIN;
                
            if (!$sub_admin_details->id) {

                $new_password = $request->password;
                
                $sub_admin_details->password = \Hash::make($new_password);

                $sub_admin_details->picture = asset('placeholder.png');

                $sub_admin_details->status = DEFAULT_TRUE;

            }

            if($request->hasFile('picture')) {

                if($request->sub_admin_id) {

                    Helper::delete_picture($sub_admin_details->picture, "/uploads/sub_admins/");
                }

                $sub_admin_details->picture = Helper::normal_upload_picture($request->picture, "/uploads/sub_admins/");
            }

            $sub_admin_details->timezone = $request->timezone;

            $sub_admin_details->token = Helper::generate_token();

            $sub_admin_details->token_expiry = Helper::generate_token_expiry();

            if($sub_admin_details->save()) {

                DB::commit();

                $message = $request->sub_admin_id ? tr('admin_sub_admin_update_success') : tr('admin_sub_admin_create_success');
                
                return redirect()->route('admin.sub_admins.view', ['sub_admin_id' =>$sub_admin_details->id ])->with('flash_success', $message);
            } 

            throw new Exception(tr('admin_sub_admin_save_error'), 101);
           
        } catch (Exception $e) {
            
            DB::rollback();
            
            $error = $e->getMessage();

            return back()->withInput()->with('flash_error',$error);
        }
    
    }

    /**
     * @method sub_admins_status()
     *
     * @uses To change the status of the sub admin, based on sub admin id. only admin can access this option
     * 
     * @created Anjana H
     *
     * @updated Anjana H  
     *
     * @param object $request - SubAdmin Id
     *
     * @return response of html page with details
     */
    public function sub_admins_status(Request $request) {

        try {

            DB::beginTransaction();
       
            $sub_admin_details = Admin::find($request->sub_admin_id);

            if( count( $sub_admin_details) == 0) {
                
                throw new Exception(tr('admin_sub_admin_not_found'), 101);
            } 
            
            $sub_admin_details->status = $sub_admin_details->status == APPROVED ? DECLINED : APPROVED;

            $message = $sub_admin_details->status == APPROVED ? tr('admin_sub_admin_approve_success') : tr('admin_sub_admin_decline_success');

            if( $sub_admin_details->save() ) {

                DB::commit();

                return back()->with('flash_success', $message);
            } 

            throw new Exception(tr('admin_sub_admin_status_error'), 101);
            
        } catch( Exception $e) {

            DB::rollback();
            
            $error = $e->getMessage();

            return redirect()->route('admin.sub_admins.index')->with('flash_error',$error);
        }
    }

    /**
     * @method playlists_index()
     *
     * @uses To list out user playlist
     * 
     * @created Anjana H
     *
     * @updated Anjana H  
     *
     * @param Integer (request) - $user_id
     *
     * @return view page
     */
    public function playlists_index(Request $request) {

       try {
            
            $user_details = User::find($request->user_id);

            if(!$user_details) {

                throw new Exception(tr('admin_user_not_found'), 101);
            } 
            
            $base_query = Playlist::where('playlists.user_id', $request->user_id)
                                ->where('playlists.playlist_type', PLAYLIST_TYPE_USER)
                                ->where('playlists.status', APPROVED)
                                ->orderBy('playlists.updated_at', 'desc');

            if($request->channel_id) {

                $base_query = $base_query->where('playlists.channel_id', $request->channel_id);
            }

            $playlists = $base_query->CommonResponse()->get();

            foreach ($playlists as $key => $playlist_details) {

                $check_video = PlaylistVideo::where('playlist_id', $playlist_details->playlist_id)->where('video_tape_id', $request->video_tape_id)->count();

                $playlist_details->total_videos = PlaylistVideo::where('playlist_id', $playlist_details->playlist_id)->count();
            }

            return view('new_admin.users.playlist_index')
                    ->with('page', 'users')
                    ->with('sub_page', 'users-view')
                    ->with('playlists', $playlists)
                    ->with('user_details', $user_details);

        } catch(Exception $e) {

            $error = $e->getMessage();

            return back()->withInput()->with('flash_error',$error);

        }
    }

    /**
     * @method playlists_delete()
     *
     * @uses To delete user playlist 
     * 
     * @created Anjana H
     *
     * @updated Anjana H  
     *
     * @param Integer (request) - $playlist_id
     *
     * @return view page
     */
    public function playlists_delete(Request $request) {

        try {
            
            DB::beginTransaction();

            $playlist_details = Playlist::find( $request->playlist_id );

            if( !$playlist_details ) {

                throw new Exception( tr('admin_user_playlist_not_found'), 101);
            } 

            if( $playlist_details->delete()){
               
                DB::commit();
                
                return redirect()->back()->with('flash_success', tr('admin_user_playlist_delete_success'));
            }

            throw new Exception(tr('admin_user_playlist_delete_error'), 101);

        } catch(Exception $e) {

            DB::rollback();

            $error = $e->getMessage();

            return back()->withInput()->with('flash_error',$error);

        }
    }

    /**
     * @method playlist_video()
     *
     * @uses To list users playlist videos
     * 
     * @created Anjana H
     *
     * @updated Anjana H  
     *
     * @param Integer (request) - $playlist_id
     *
     * @return view page
     */
    public function playlist_video(Request $request){

        try {

            DB::beginTransaction();

            $playlist_details = Playlist::find($request->playlist_id );

            if( !$playlist_details ) {

                throw new Exception( tr('admin_user_playlist_not_found'), 101);
            } 

            $playlists_videos = PlaylistVideo::where('playlist_id', $request->playlist_id)
                        ->leftjoin('video_tapes','video_tapes.id','=','playlist_videos.video_tape_id')
                        ->leftjoin('channels','channels.id','=','video_tapes.channel_id')
                        ->addSelect('playlist_videos.id as playlist_video_id','playlist_videos.created_at as created_at')
                        ->addSelect('channels.name as channel_name')
                        ->addSelect('video_tapes.title as video_tape_title', 'video_tapes.id as video_tape_id')
                        ->get();

            return view('new_admin.users.playlist_videos')
                    ->with('page', 'users')
                    ->with('sub_page', 'users-view')
                    ->with('playlists_videos', $playlists_videos)
                    ->with('playlist_details', $playlist_details);

        } catch(Exception $e) {

            $error = $e->getMessage();

            return back()->withInput()->with('flash_error',$error);

        }
    }

    /**
     * @method playlists_video_remove()
     *
     * @uses To delete video from user playlist 
     * 
     * @created Anjana H
     *
     * @updated Anjana H  
     *
     * @param Integer (request) - $playlist_video_id
     *
     * @return view page
     */
    public function playlists_video_remove(Request $request) {

        try {
            
            DB::beginTransaction();

            $playlist_video_details = PlaylistVideo::find( $request->playlist_video_id );

            if( !$playlist_video_details ) {

                throw new Exception( tr('admin_user_playlist_video_not_found'), 101);
            } 

            if( $playlist_video_details->delete()){
               
                DB::commit();
                
                return redirect()->back()->with('flash_success', tr('admin_user_playlist_video_delete_success'));
            }

            throw new Exception(tr('admin_user_playlist_video_delete_error'), 101);

        } catch(Exception $e) {

            DB::rollback();

            $error = $e->getMessage();

            return back()->withInput()->with('flash_error',$error);

        }
    }
    
    /**
     * @method video_tapes_index
     *
     * @uses List of videos displayed and also based on user it will list out
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param 
     * 
     * @return response of videos details
     *
     */
    public function video_tapes_index(Request $request) {

        try {

            $base_query = VideoTape::leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                        ->videoResponse()
                        ->orderBy('video_tapes.created_at' , 'desc');
            
            $tag_details = '';

            if($request->has('tag_id')) {

                $tag_details = Tag::find($request->tag_id);

                if (!$tag_details) {

                    throw new Exception(tr('admin_tag_not_found'), 101);
                }

                $base_query->leftjoin('video_tape_tags', 'video_tape_tags.video_tape_id', '=', 'video_tapes.id')
                        ->where('video_tape_tags.tag_id', $request->tag_id)
                        ->orderBy('video_tapes.created_at' , 'desc')
                        ->groupBy('video_tape_tags.video_tape_id');
            }

            if ($request->user_id) {

                $base_query->where('video_tapes.user_id', $request->user_id);
            }

            $video_tapes = $base_query->get();
            
            return view('new_admin.video_tapes.index')
                        ->with('page', 'video_tapes')
                        ->with('sub_page', 'video_tapes-view')
                        ->with('video_tapes' , $video_tapes)
                        ->with('tag_details' , $tag_details); 

        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return redirect()->route('admin.video_tapes.index')->with('flash_error',$error);
        }
    
    }

    /**
     * @method video_tapes_create
     *
     * @uses To create new video 
     *
     * @created Vithya R
     *
     * @updated Anjana H
     *
     * @param 
     * 
     * @return view page
     *
     */
    public function video_tapes_create(Request $request) {

        try {

            $channels = getChannels();

            $categories = Category::select('id as category_id', 'name as category_name')
                                ->where('status', CATEGORY_APPROVE_STATUS)
                                ->orderBy('created_at', 'desc')
                                ->get();

            $tags = Tag::select('tags.id as tag_id', 'name as tag_name', 'search_count as count')
                        ->where('status', TAG_APPROVE_STATUS)
                        ->orderBy('created_at', 'desc')
                        ->get();

            return view('new_admin.video_tapes.create')
                    ->with('page' ,'video_tapes')
                    ->with('sub_page' ,'video_tapes-create')
                    ->with('channels' , $channels)
                    ->with('categories', $categories)
                    ->with('tags', $tags);

        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return redirect()->route('admin.video_tapes.index')->with('flash_error',$error);
        }

    }

    /**
     * @method videos_edit
     *
     * @uses To Edit a video based on video id
     *
     * @created Vithya R
     *
     * @updated Anjana H
     *
     * @param Integer $request video_tape_id
     * 
     * @return view page
     *
     */
    public function video_tapes_edit(Request $request) {
        
        try {

            $video_tape_details = VideoTape::where('video_tapes.id' , $request->video_tape_id)
                        ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                        ->videoResponse()
                        ->orderBy('video_tapes.created_at' , 'desc')
                        ->first();

            if (!$video_tape_details) {

                throw new Exception(tr('video_not_found'), 101);
            }

            $page = 'video_tapes'; $sub_page = 'video_tapes-create';

            if($video_tape_details->is_banner == DEFAULT_TRUE ) {

                $page = 'banner-videos'; $sub_page = 'banner-videos';
            }

            $channels = getChannels();

            $categories = Category::select('id as category_id', 'name as category_name')
                            ->where('status', CATEGORY_APPROVE_STATUS)
                            ->orderBy('created_at', 'desc')
                            ->get();

            $tags = Tag::select('tags.id as tag_id', 'name as tag_name', 'search_count as count')
                            ->where('status', TAG_APPROVE_STATUS)
                            ->orderBy('created_at', 'desc')->get();

            $video_tape_details->tag_id = VideoTapeTag::where('video_tape_id', $request->video_tape_id)->where('status', TAG_APPROVE_STATUS)->get()->pluck('tag_id')->toArray();
            if($video_tape_details->video_type == VIDEO_TYPE_R4D)
                return view('new_admin.video_tapes.create')
                        ->with('page' ,$page)
                        ->with('sub_page' ,$sub_page)
                        ->with('channels' , $channels)
                        ->with('video_tape_details' ,$video_tape_details)
                        ->with('tags', $tags)
                        ->with('categories', $categories);
            else
                return view('new_admin.video_tapes.edit')
                        ->with('page' ,$page)
                        ->with('sub_page' ,$sub_page)
                        ->with('channels' , $channels)
                        ->with('video_tape_details' ,$video_tape_details)
                        ->with('tags', $tags)
                        ->with('categories', $categories);

        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return redirect()->route('admin.video_tapes.index')->with('flash_error',$error);
        }
    
    }

    /**
     * @method video_tapes_save()
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
    public function video_tapes_save(Request $request) {
        
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
     * @method video_tapes_images()
     *
     * @uses To get images which is uploaded based on the video_tape_id
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param Integer $request video_tape_id
     * 
     * @return response of success/failure 
     *
     */
    public function video_tapes_images($id) {

        try {

            $response = CommonRepo::get_video_tape_images($id)->getData();

            $tape_images = VideoTapeImage::where('video_tape_id', $id)->get();

            $view = \View::make('admin.videos.select_image')->with('model', $response)->with('tape_images', $tape_images)->render();

            return response()->json(['path' => $view, 'data'=>$response->data]);

        } catch (Exception $e) {
            
            $error = $e->getMessage();

            $response_array = ['success' => false, 'error' => $error];

            return response()->json($response_array, 200);
        }
    } 



    /**
     * @method video_ads_create()
     *
     * @uses  To create a video ads based on video id
     *
     * @created vithya R
     *
     * @updated Anjana H
     *
     * @param Integer $request : Video ad id with video ad details
     *
     * @return response of succes/failure response of details
     */
    public function video_ads_create(Request $request) {
        $video_tape_details = VideoTape::find($request->video_tape_id);

        if ($video_tape_details) {

            $videoPath = '';

            $video_pixels = '';

            $preAd = new AdsDetail;

            $postAd = new AdsDetail;

            $betweenAd = new AdsDetail;

            $model = new VideoAd;

            if ($video_tape_details) {

                $videoPath = $video_tape_details->video_resize_path ? $video_tape_details->video.','.$video_tape_details->video_resize_path : $video_tape_details->video;
                $video_pixels = $video_tape_details->video_resolutions ? 'original,'.$video_tape_details->video_resolutions : 'original';

            }

            $index = 0;

            $ads = AdsDetail::where('status', ADS_ENABLED)->get(); 

            return view('new_admin.video_ads.create')
                    ->with('video_tape_details', $video_tape_details)
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
     * @method video_tapes_default_image_save()
     *
     * @uses To set the default image based on object details
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param Integer $request video_tape_id
     * 
     * @return JSON Response
     *
     */
    public function video_tapes_default_image_save(Request $request) {

        try {

            $response_array = CommonRepo::set_default_image($request)->getData();

            return response()->json($response_array, 200);

        } catch(Exception $e) {

            $response_array = ['success' => false, 'error' => $e->getMessage()];

            return response()->json($response_array, 200);
        }

    }

    /**
     * @method video_tapes_upload_image()
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
    public function video_tapes_upload_image(Request $request) {

        $response = CommonRepo::upload_video_image($request)->getData();

        return response()->json($response);

    }

    /**
     * @method videos_status()
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
    public function video_tapes_status(Request $request) {

        try {

            $video_tape_details = VideoTape::find($request->video_tape_id);
            
            if (!$video_tape_details) {

                throw new Exception(tr('video_not_found'), 101);
            }

            $video_tape_details->is_approved = $video_tape_details->is_approved ? DEFAULT_FALSE : DEFAULT_TRUE;

            $video_tape_details->save();
            
            $message = $video_tape_details->is_approved == DEFAULT_TRUE ? tr('admin_not_video_approve') : tr('admin_not_video_decline');

            return back()->with('flash_success', $message);
            
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return redirect()->back()->with('flash_error',$error);
        }
    
    }



    /**
     * @method video_tapes_publish()
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
    public function video_tapes_publish($id) {

        // Load video based on Auto increment id
        $video = VideoTape::find($id);

        // Check the video present or not
        if ($video) {

            $video->publish_status = DEFAULT_TRUE;

            $video->publish_time = date('Y-m-d H:i:s');

            // Save the values in DB
            if ($video->save()) {

                return back()->with('flash_success', tr('admin_published_video_success'));

            }

        }

        return back()->with('flash_error', tr('admin_published_video_failure'));
    }


    /**
     * @method video_tapes_view
     *
     * @uses get video details
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param 
     * 
     * @return view page 
     *
     */
    public function video_tapes_view(Request $request) {

        try {

            $validator = Validator::make($request->all() , [
                    'video_tape_id' => 'required|exists:video_tapes,id'
                ]);

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);                
            }
           
            $video_tape_details = VideoTape::where('video_tapes.id' , $request->video_tape_id)
                        ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                        ->videoResponse()
                        ->orderBy('video_tapes.created_at' , 'desc')
                        ->first();

            $video_tape_tags = VideoTapeTag::where('video_tape_tags.video_tape_id' , $request->id)
                    ->leftjoin('tags','tags.id' , '=' , 'video_tape_tags.tag_id')
                    ->get();

            $videoPath = $video_pixels = $videoStreamUrl = '';

            if ($video_tape_details->video_type == VIDEO_TYPE_UPLOAD) {

                if (\Setting::get('streaming_url')) {
                    $videoStreamUrl = \Setting::get('streaming_url').get_video_end($video_tape_details->video);
                    if ($video_tape_details->is_approved == 1) {
                        if ($video_tape_details->video_resolutions) {
                            $videoStreamUrl = Helper::web_url().'/uploads/smil/'.get_video_end_smil($video_tape_details->video).'.smil';
                        }
                    }
                } else {

                    $videoPath = $video_tape_details->video_resize_path ? $videos->video.','.$video_tape_details->video_resize_path : $video_tape_details->video;
                    $video_pixels = $video_tape_details->video_resolutions ? 'original,'.$video_tape_details->video_resolutions : 'original';
                    
                }
           
            } else {

                $videoStreamUrl = $video_tape_details->video;
            }
        
            $admin_video_images = $video_tape_details->getScopeVideoTapeImages;

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


            $page = 'video_tapes'; $sub_page = 'video_tapes-view';

            if($video_tape_details->is_banner == 1) {

                $page = 'banner-videos'; $sub_page = 'banner-videos';
            }
           
            return view('new_admin.video_tapes.view')
                        ->with('video' , $video_tape_details)
                        ->with('video_images' , $admin_video_images)
                        ->with('page', $page)
                        ->with('sub_page', $sub_page)
                        ->with('videoPath', $videoPath)
                        ->with('video_pixels', $video_pixels)
                        ->with('videoStreamUrl', $videoStreamUrl)
                        ->with('spam_reports', $spam_reports)
                        ->with('reviews', $reviews)
                        ->with('wishlists', $wishlists)
                        ->with('video_tags', $video_tape_tags);

        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return redirect()->route('admin.video_tapes.index')->with('flash_error',$error);
        }    
    }


    /**
     * @method videos_delete()
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
    public function video_tapes_delete(Request $request) {

        try {

            DB::beginTransaction();

            $video_tape_details = VideoTape::where('id' , $request->video_tape_id)->first();
            
            if(!$video_tape_details)  {

                throw new Exception(tr('video_not_found'), 101);
            }                

            $path = public_path('uploads/videos/admin/'.get_video_title($video_tape_details->video_title));
            $video_tape_details->delete();
            
            if(File::isDirectory($path))
                File::deleteDirectory($path);
                
            DB::commit();

            return redirect()->back()->with('flash_success', tr('video_delete_success'));
                
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    
    }
    
    /**
     * @method spam_videos()
     *
     * @uses Load all the videos from flag table
     *
     * @created vithya R
     *
     * @updated Anjana H
     *
     * @param integer $id Video id
     *
     * @return all the spam videos
     */
    public function spam_videos(Request $request) {

        // Load all the videos from flag table

        $spam_videos = Flag::groupBy('video_tape_id')->get();
        
        // Return array of values
        return view('new_admin.spam_videos.index')
                        ->with('page' , 'video_tapes')
                        ->with('sub_page' , 'spam_videos')
                        ->with('spam_videos' , $spam_videos);
    }

    /**
     * @method spam_videos_user_reports()
     *
     * @uses Load all the flags based on the video id
     *
     * @created vithya R
     *
     * @updated - -
     *
     * @param integer $id Video id
     *
     * @return all the spam videos
     */
    public function spam_videos_user_reports($video_tape_id) {

        // Load all the users
        $spam_videos = Flag::where('video_tape_id', $video_tape_id)->get();

        // Return array of values

        return view('new_admin.spam_videos.user_reports')
                        ->with('page' , 'videos')
                        ->with('sub_page' , 'spam_videos')
                        ->with('spam_videos' , $spam_videos);   
    }

    /**
     * @method spam_videos_each_user_reports()
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
     * @method spam_videos_unspam()
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
     * @method video_tapes_wishlist
     *
     * @uses To list out all the wishlist details based on user
     *
     * @created vithya R
     *
     * @updated Anjana H
     *
     * @param integer $request - Video id
     * 
     * @return - Response of wishlist based on id
     *
     */
    public function video_tapes_wishlist(Request $request) {

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

        return view('new_admin.video_tapes.wishlists')
                    ->with('wishlists' , $wishlists)
                    ->withPage('videos')
                    ->with('sub_page','view-videos');
     
    }

    /**
     * @method videos_set_ppv
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
    public function video_tapes_set_ppv($id, Request $request) {

        try {
            $validator = Validator::make($request->all() , [
                    'ppv_amount' => 'required|max:6',
                ]);

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);                
            }

            if($request->ppv_amount > 0) {

                // Load Video Model
                $video_tape_details = VideoTape::find($request->id);

                // Get post attribute values and save the values
                if (!$video_tape_details)  {

                    throw new Exception(tr('admin_published_video_failure'), 101);
                }
                
                $request->request->add([ 
                    'ppv_created_by' => 0 ,
                    'is_pay_per_view' => PPV_ENABLED
                ]); 

                if ($data = $request->all()) {

                    // Update the post
                    if (VideoTape::where('id', $request->id)->update($data)) {
                        // Redirect into particular value
                        return back()->with('flash_success', tr('payment_added'));
                    } 
                }
            } 

            throw new Exception(tr('add_ppv_amount'), 101);                
                        
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    
    }

    /**
     * @method video_tapes_remove_ppv
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
    public function video_tapes_remove_ppv($id) {
        
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
     * @method video_tapes_compression_complete()
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
    public function video_tapes_compression_complete(Request $request) {

        $response = CommonRepo::videos_compression_complete($request)->getData();

        if ($response->success) {

            return back()->with('flash_success', $response->message);

        } else {

            return back()->with('flash_error', $response->error_messages);

        }

    }

    /**
     * Function: common_settings_save()
     * 
     * @uses to update .env values
     *
     * @created Anjana H
     *
     * @updated Maheswari
     *
     * @param
     *
     * @return success/error message
     */
    public function common_settings_save(Request $request) {

        try {

            $settings = array();

            $admin_id = \Auth::guard('admin')->user()->id;
           
            foreach( $request->toArray() as $key => $data ) {

                $check_settings = Settings::where('key' ,'=', $key)->count();

                if($check_settings == TRUE) {

                    $result = Settings::where('key' ,'=', $key)->update(['value' => $data]);

                } else{

                    if( \Enveditor::set($key, $data)) { 
                    
                    } else {

                        throw new Exception(tr('admin_settings_save_error'), 101);
                    }

 
                }
            }

            return redirect()->route('clear-cache')->with('flash_success', tr('admin_settings_key_save_success') );
            
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->with('flash_error', $error);
        }
    }

    /**
     * @method channels_playlists_index()
     *
     * @uses To list out channel's  playlist based channel_id
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param Integer (request) channel_id
     *
     * @return View page
     */
    public function channels_playlists_index(Request $request) {

        try {
            
            $channel_details = Channel::find($request->channel_id);

            if(!$channel_details) {

                throw new Exception( tr('admin_channel_not_found'), 101);
            } 
            
            $base_query = Playlist::where('playlists.channel_id', $request->channel_id)
                                ->orderBy('playlists.updated_at', 'desc');

            $playlists = $base_query->CommonResponse()->get();

            foreach ($playlists as $key => $playlist_details) {
                
                $playlist_details->total_videos = PlaylistVideo::where('playlist_id', $playlist_details->playlist_id)->count();
            }

            return view('new_admin.channels.playlist_index')
                    ->with('page', 'channels')
                    ->with('sub_page', 'channels-view')
                    ->with('playlists', $playlists)
                    ->with('channel_details', $channel_details);

        } catch(Exception $e) {

            $error = $e->getMessage();

            return back()->withInput()->with('flash_error',$error);
        }
    
    }

    /**
     * @method channels_playlists_view()
     *
     * @uses to list out playlist details based on playlist_id of a channel
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer (request) playlist_id
     * 
     * @return view page
     */
    public function channels_playlists_view(Request $request) {

        try {

            $playlist_details = Playlist::find($request->playlist_id );

            if(!$playlist_details) {

                throw new Exception( tr('admin_user_playlist_not_found'), 101);
            } 

            $channel_details = Channel::find($request->channel_id);

            if(!$channel_details) {

                throw new Exception( tr('admin_channel_not_found'), 101);
            }

            $playlists_videos = PlaylistVideo::where('playlist_id', $request->playlist_id)
                        ->leftjoin('video_tapes','video_tapes.id','=','playlist_videos.video_tape_id')
                        ->leftjoin('channels','channels.id','=','video_tapes.channel_id')
                        ->addSelect('playlist_videos.id as playlist_video_id','playlist_videos.created_at as created_at')
                        ->addSelect('channels.name as channel_name')
                        ->addSelect('video_tapes.title as video_tape_title', 'video_tapes.id as video_tape_id')
                        ->get();            

            $playlist_details->total_videos = $playlists_videos->count();

            return view('new_admin.channels.playlist_videos')
                    ->with('page', 'channels')
                    ->with('sub_page', 'channels-view')
                    ->with('channel_details', $channel_details)
                    ->with('playlists_videos', $playlists_videos)
                    ->with('playlist_details', $playlist_details);

        } catch(Exception $e) {

            $error = $e->getMessage();

            return back()->withInput()->with('flash_error',$error);

        }
        
    }

    /**
     * @method channels_playlists_delete()
     *
     * @uses To delete the playlist based on playlist_id
     *
     * @created Anjana H
     *
     * @updated Anjana Hdd
     *
     * @param object $request - playlist id
     *
     * @return success/failure message
     */
    public function channels_playlists_delete(Request $request) {

        try {
            
            DB::beginTransaction();

            $playlist_details = Playlist::find( $request->playlist_id );

            if(!$playlist_details) {

                throw new Exception( tr('admin_channel_playlist_not_found'), 101);
            } 

            if( $playlist_details->delete()) {
               
                DB::commit();
                
                return redirect()->back()->with('flash_success', tr('admin_channel_playlist_delete_success'));
            }

            throw new Exception(tr('admin_channel_playlist_delete_error'), 101);

        } catch(Exception $e) {

            DB::rollback();

            $error = $e->getMessage();

            return back()->withInput()->with('flash_error',$error);
        }
    }

    /**
     * @method channels_playlists_video_remove()
     *
     * @uses To delete video from channel playlist's list 
     * 
     * @created Anjana H
     *
     * @updated Anjana H  
     *
     * @param Integer (request) - $playlist_video_id
     *
     * @return view page
     */
    public function channels_playlists_video_remove(Request $request) {

        try {
            
            DB::beginTransaction();

            $playlist_video_details = PlaylistVideo::find( $request->playlist_video_id );

            if(!$playlist_video_details) {

                throw new Exception( tr('admin_channel_playlist_video_not_found'), 101);
            } 

            if( $playlist_video_details->delete()) {
               
                DB::commit();
                
                return redirect()->back()->with('flash_success', tr('admin_channel_playlist_video_delete_success'));
            }

            throw new Exception(tr('admin_channel_playlist_video_delete_error'), 101);

        } catch(Exception $e) {

            $error = $e->getMessage();

            return back()->with('flash_error', $error);

        }

    }
    
    /**
     * @method channels_playlists_create()
     *
     * @uses To create a playlist object details based on channel id
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param Integer (request) channel_id
     *
     * @return View page
     */
    public function channels_playlists_create(Request $request) {

        try {

            $channel_details = Channel::find($request->channel_id);

            if(!$channel_details) {

                throw new Exception(tr('admin_channel_not_found'), 101);                
            }
            
            $video_tapes = VideoTape::where('video_tapes.channel_id' , '=' , $request->channel_id)
                ->select('video_tapes.id as video_tapes_id', 
                    'video_tapes.channel_id as channel_id',
                    'video_tapes.title as video_tape_title')
                ->where('is_approved',APPROVED)
                ->orderBy('video_tapes.created_at' , 'desc')->get();
         
            if(!$video_tapes) {

                throw new Exception(tr('admin_video_tape_not_found'), 101);
            }

            $playlist_details = new Playlist;

            return view('new_admin.channels.playlist_create')
                        ->with('page' , 'channels')
                        ->with('sub_page','channels-view')
                        ->with('channel_details', $channel_details)
                        ->with('video_tapes', $video_tapes)
                        ->with('playlist_details', $playlist_details);
            
        } catch (Exception $e) {

            $error = $e->getMessage();

            return back()->withInput()->with('flash_error',$error);
        }
    }

    /**
     * @method channels_playlists_save
     *
     * @uses To save/update channel object based on channel id or details
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - channel_id, (request) details
     * 
     * @return success/failure message.
     *
     */
    public function channels_playlists_save(Request $request) {

        try {
            
            DB::beginTransaction();

            $validator = Validator::make($request->all() , [
               
                'title' => 'required||max:128|min:2',
                
                'picture' => $request->playlist_id ? 'mimes:jpeg,jpg,bmp,png' : 'required|mimes:jpeg,jpg,bmp,png',
                
                'description' => 'max:225|',
            ]);

            if($validator->fails()) {

                $error = implode(',',$validator->messages()->all()); 

                throw new Exception($error, 101);
            }

            $video_tapes = VideoTape::find($request->video_tapes_id);
          
            if(!$video_tapes){
                
                throw new Exception(tr('video_not_found'), 101);
            } 

            $channel_details = Channel::find($request->channel_id);
          
            if(!$channel_details){
                
                throw new Exception(tr('channel_not_found'), 101);
            }

            if($request->playlist_id) {
                
                $playlist_details = Playlist::find($request->playlist_id);
            
            } else{
                
                $playlist_details = new Playlist;
                
                $playlist_details->status = APPROVED;

                $playlist_details->playlist_type = PLAYLIST_TYPE_CHANNEL;

                $playlist_details->playlist_display_type = PLAYLIST_DISPLAY_PUBLIC;

                $playlist_details->channel_id = $request->channel_id;
               
                $playlist_details->user_id = $channel_details->user_id;

            }

            $playlist_details->title = $request->title;

            $playlist_details->description = $request->description;

            if ($request->hasFile('picture')) {

                if ($request->playlist_id) {

                    Helper::delete_avatar('uploads/channels/playlists' , $playlist_details->picture);
                }

                $playlist_details->picture = Helper::upload_avatar('uploads/channels/playlists', $request->file('picture'), 0); 
            }
            
            if ($playlist_details->save()) {

                $playlist_video_delete = PlaylistVideo::where('playlist_id', $playlist_details->id)
                                ->whereNotIn('video_tape_id', $request->video_tapes_id)->delete();
                
                $playlist_video = PlaylistVideo::where('playlist_id', $playlist_details->id)
                                ->whereIn('video_tape_id', $request->video_tapes_id)
                                ->select('video_tape_id')
                                ->get();

                $playlist_video_ids = array_column($playlist_video->toArray(), 'video_tape_id');
                
                $result = array_diff($request->video_tapes_id,$playlist_video_ids);
                               
                foreach ($result as $key => $video_tape_id) {

                    $playlist_video_details = new PlaylistVideo;

                    $playlist_video_details->playlist_id = $playlist_details->id;

                    $playlist_video_details->video_tape_id = $video_tape_id;

                    $playlist_video_details->status = DEFAULT_TRUE;
                    
                    $playlist_video_details->save();                    
                }

                DB::commit();
                
                $message = $request->playlist_id ? tr('admin_channel_playlist_update_success') : tr('admin_channel_playlist_create_success');

                return redirect()->route('admin.channels.playlists.view', ['playlist_id' => $playlist_details->id, 'channel_id' =>  $request->channel_id])->with('flash_success',$message);
                         
            }

            throw new Exception(tr('admin_playlist_save_error'), 101);
        
        } catch (Exception $e) {

            DB::rollback();
            
            $error = $e->getMessage();

            return back()->withInput()->with('flash_error',$error);
        }

    }

    /**
     * @method channels_playlists_edit
     *
     * @uses To edit a playlist based on playlist_id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - playlist_id
     * 
     * @return viuew page
     *
     */
    public function channels_playlists_edit(Request $request) {
        
        try {

            $playlist_details = $request->playlist_id ? Playlist::find( $request->playlist_id) : new Playlist;

            $channel_details = Channel::find($request->channel_id);

            if(!$channel_details) {

                throw new Exception(tr('admin_channel_not_found'), 101);
            }
            
            $video_tapes = VideoTape::where('video_tapes.channel_id' , '=' , $request->channel_id)
                ->select('video_tapes.id as video_tapes_id', 
                    'video_tapes.channel_id as channel_id',
                    'video_tapes.title as video_tape_title')
                ->orderBy('video_tapes.created_at' , 'desc')->get();
         
            if(!$video_tapes) {

                throw new Exception(tr('admin_video_tape_not_found'), 101);
            }

            foreach ($video_tapes as $key => $video_tape_details) {

                // check already video_tape added 

                $check_playlist_video_exists = PlaylistVideo::where('video_tape_id' , $video_tape_details->video_tapes_id)->where('playlist_id',$request->playlist_id)->where('status' , APPROVED)->count();

                $video_tape_details->is_selected = NO;

                if($check_playlist_video_exists) {
               
                    $video_tape_details->is_selected = YES;
               
                }

            }

            return view('new_admin.channels.playlist_edit')
                        ->with('page' , 'channels')
                        ->with('sub_page','channels-view')
                        ->with('channel_details', $channel_details)
                        ->with('video_tapes', $video_tapes)
                        ->with('playlist_details', $playlist_details);

        } catch( Exception $e) {
            
            $error = $e->getMessage();

            return redirect()->route('admin.categories.index')->with('flash_error',$error);
        }
    }


    /**
     * @method channels_playlists_status
     *
     * @uses To change the channel playlist status to approve or decline 
     *
     * @created 
     *
     * @updated 
     *
     * @param integer (request) $channel_id
     * 
     * @return success/failure message
     */
    public function channels_playlists_status(Request $request) {
        
        try {

            DB::beginTransaction();

            $playlist_details = Playlist::find($request->playlist_id);

            if(!$playlist_details) {

                throw new Exception(tr('admin_channel_playlist_not_found'), 101);
            }

            $playlist_details->status = $playlist_details->status == APPROVED ? DECLINED : APPROVED;

            $message = $playlist_details->status == APPROVED ? tr('admin_channel_playlist_approved_success') :  tr('admin_channel_playlist_declined_success') ;

            if ($playlist_details->save() ) {

                PlaylistVideo::where('playlist_id', $playlist_details->id)
                                ->update(['status' => $playlist_details->status ]);   
                
                DB::commit();

                return back()->with('flash_success', $message); 
            }  

            throw new Exception(tr('admin_channel_playlist_status_error'), 101);
            
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    
    }

}
