<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB, Log, Hash, Validator, Exception, Setting;

use App\Helpers\Helper, App\Helpers\CommonHelper, App\Helpers\VideoHelper;
use App\Repositories\VideoTapeRepository as VideoRepo;

use App\Repositories\CommonRepository as CommonRepo;

use App\Repositories\PaymentRepository as PaymentRepo;

use App\Repositories\UserRepository as UserRepo;

use App\Repositories\V5Repository as V5Repo;


use App\Jobs\sendPushNotification;

use App\Jobs\BellNotificationJob;


use App\User, App\Card, App\Wishlist;

use App\BellNotification;

use App\Category, App\SubCategory;

use App\Page;

use App\Subscription, App\UserPayment;

use App\VideoTape, App\PayPerView, App\UserHistory;

use App\Coupon;

use App\Redeem, App\RedeemRequest;

use App\Flag, App\UserRating;

use App\Channel, App\ChannelSubscription;

use App\VideoTapeTag;

use App\LikeDislikeVideo;

use App\Playlist, App\PlaylistVideo;

class NewUserApiController extends Controller
{
    protected $skip, $take, $loginUser, $currency;

	public function __construct(Request $request) {
        
        Log::info(url()->current());

        Log::info("Request Info".print_r($request->all(), true));

        $this->loginUser = User::CommonResponse()->find($request->id);

        $this->middleware('ChannelOwner' , ['only' => ['video_tapes_status', 'video_tapes_delete', 'video_tapes_ppv_status','video_tapes_publish_status']]);

        $this->skip = $request->skip ?: 0;
        
        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);
        
        $this->currency = Setting::get('currency', '$');
    }

    /**
     * @method register()
     *
     * @uses Registered user can register through manual or social login
     * 
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param Form data
     *
     * @return Json response with user details
     */
    public function register(Request $request) {

        try {
            DB::beginTransaction();

            // Validate the common and basic fields

            $basic_validator = Validator::make($request->all(),
                [
                    'device_type' => 'required|in:'.DEVICE_ANDROID.','.DEVICE_IOS.','.DEVICE_WEB,
                    'device_token' => 'required',
                    'login_by' => 'required|in:manual,facebook,google',
                ]
            );

            if($basic_validator->fails()) {

                $error = implode(',', $basic_validator->messages()->all());

                throw new Exception($error , 101);

            }

            $allowed_social_logins = ['facebook','google'];

            if(in_array($request->login_by,$allowed_social_logins)) {

                // validate social registration fields

                $social_validator = Validator::make($request->all(),
                            [
                                'social_unique_id' => 'required',
                                'name' => 'required|max:255|min:2',
                                'email' => 'required|email|max:255',
                                'mobile' => 'digits_between:6,13',
                                'picture' => '',
                                'gender' => 'in:male,female,others',
                            ]
                        );

                if($social_validator->fails()) {

                    $error = implode(',', $social_validator->messages()->all());

                    throw new Exception($error , 101);

                }

            } else {

                // Validate manual registration fields

                $manual_validator = Validator::make($request->all(),
                    [
                        'name' => 'required|max:255',
                        'email' => 'required|email|max:255|min:2',
                        'password' => 'required|min:6',
                        'picture' => 'mimes:jpeg,jpg,bmp,png',
                    ]
                );

                // validate email existence

                $email_validator = Validator::make($request->all(),
                    [
                        'email' => 'unique:users,email',
                    ]
                );

                if($manual_validator->fails()) {

                    $error = implode(',', $manual_validator->messages()->all());

                    throw new Exception($error , 101);
                    
                } else if($email_validator->fails()) {

                	$error = implode(',', $email_validator->messages()->all());

                    throw new Exception($error , 101);

                } 

            }

            $user_details = User::where('email' , $request->email)->first();

            $send_email = DEFAULT_FALSE;

            // Creating the user

            if(!$user_details) {

                $user_details = new User;

                register_mobile($request->device_type);

                $send_email = DEFAULT_TRUE;

                $user_details->picture = asset('placeholder.jpg');

                // $user_details->registration_steps = 1;

            } else {

                if(in_array($user_details->status , [USER_PENDING , USER_DECLINED])) {

                    throw new Exception(CommonHelper::error_message(502) , 502);
                
                }

            }

            if($request->has('name')) {

                $user_details->name = $request->name;

            }

            if($request->has('email')) {

                $user_details->email = $request->email;

            }

            if($request->has('mobile')) {

                $user_details->mobile = $request->mobile;

            }

            if($request->has('password')) {

                $user_details->password = Hash::make($request->password ?: "123456");

            }

            if($request->has('dob')) {

                $user_details->dob = date("Y-m-d" , strtotime($request->dob));
            }
            
            if ($user_details->dob) {

                if ($user_details->dob != '0000-00-00') {

                    $from = new \DateTime($user_details->dob);

                    $to   = new \DateTime('today');

                    $user_details->age_limit = $from->diff($to)->y;

                }

            }

            $user_details->gender = $request->gender ?: "male";

            // $user_details->payment_mode = COD;

            $user_details->token = Helper::generate_token();

            $user_details->token_expiry = Helper::generate_token_expiry();

            $check_device_exist = User::where('device_token', $request->device_token)->first();

            if($check_device_exist) {

                $check_device_exist->device_token = "";

                $check_device_exist->save();
            }

            $user_details->device_token = $request->device_token ?: "";

            $user_details->device_type = $request->device_type ?: DEVICE_WEB;

            $user_details->login_by = $request->login_by ?: 'manual';

            $user_details->social_unique_id = $request->social_unique_id ?: '';

            // Upload picture

            if($request->login_by == "manual") {

                if($request->hasFile('picture')) {

                    $user_details->picture = Helper::normal_upload_picture($request->file('picture'), COMMON_FILE_PATH);

                }

            } else {

                $user_details->is_verified = USER_EMAIL_VERIFIED; // Social login

                $user_details->picture = $request->picture ?: $user_details->picture;

            }   

            if($user_details->save()) {

                // Send welcome email to the new user:

                if($send_email) {

                	// Check the default subscription and save the user type 

                    if($request->referral_code) {

                        UserRepo::referral_register($request->referral_code, $user_details);
                    }

                    // Check the user type

                    user_type_check($user_details->id);

                    if($user_details->login_by == 'manual' && Setting::get('email_verify_control')) {

                        $user_details->password = $request->password;

                        $subject = apitr('user_welcome_title').' '.Setting::get('site_name', 'StreamTube');

                        $email_data = $user_details;

                        $page = "emails.welcome";

                        $email = $user_details->email;

                        $email_send_response = CommonHelper::send_email($page,$subject,$email,$email_data);

                        // No need to throw error. For forgot password we need handle the error response

                        if($email_send_response) {

                            if($email_send_response->success) {

                            } else {

                                $error = $email_send_response->error;

                                Log::info("Registered EMAIL Error".print_r($error , true));
                                
                            }

                        }

                    } else{

                        $user_details->is_verified = USER_EMAIL_VERIFIED;

                        $user_details->save();
                    }

                }

                if(in_array($user_details->status , [USER_DECLINED , USER_PENDING])) {
                
                    $response = ['success' => false , 'error' => CommonHelper::error_message(1000) , 'error_code' => 1000];

                    DB::commit();

                    return response()->json($response, 200);
               
                }

                if($user_details->is_verified == USER_EMAIL_VERIFIED) {

                	$data = User::CommonResponse()->find($user_details->id);

                    $data->email_notification_status = (int) $user_details->email_notification_status; // Don't remove int (used ios)

                    $data->push_status = (int) $user_details->push_status; // Don't remove int (used ios)

                    $data->is_appstore_updated = Setting::get('ios_payment_subscription_status', 0);

                    $response_array = ['success' => true, 'data' => $data];

                } else {

                    $response_array = ['success'=>false, 'error' => CommonHelper::error_message(1001), 'error_code'=>1001];
                    
                    DB::commit();

                    return response()->json($response_array, 200);

                }

            } else {

                throw new Exception(CommonHelper::error_message(103), 103);

            }

            DB::commit();

            return response()->json($response_array, 200);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
   
    }

    /**
     * @method login()
     *
     * @uses Registered user can login using their email & password
     * 
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request - User Email & Password
     *
     * @return Json response with user details
     */
    public function login(Request $request) {

        try {

            DB::beginTransaction();

            $basic_validator = Validator::make($request->all(),
                [
                    'device_token' => 'required',
                    'device_type' => 'required|in:'.DEVICE_ANDROID.','.DEVICE_IOS.','.DEVICE_WEB,
                    'login_by' => 'required|in:manual,facebook,google',
                ]
            );

            if($basic_validator->fails()){

                $error = implode(',', $basic_validator->messages()->all());

                throw new Exception($error , 101);

            }

            /** Validate manual login fields */

            $manual_validator = Validator::make($request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required',
                ]
            );

            if($manual_validator->fails()) {

                $error = implode(',', $manual_validator->messages()->all());

            	throw new Exception($error , 101);

            }

            $user_details = User::where('email', '=', $request->email)->first();

            $email_active = DEFAULT_TRUE;

            // Check the user details 

            if(!$user_details) {

            	throw new Exception(CommonHelper::error_message(1002), 1002);

            }

            // check the user approved status

            if($user_details->status != USER_APPROVED) {

            	throw new Exception(CommonHelper::error_message(1000), 1000);

            }

            if(Setting::get('email_verify_control') == YES) {

                if(!$user_details->is_verified) {

                    Helper::check_email_verification("" , $user_details, $error);

                    $email_active = DEFAULT_FALSE;

                }

            }

            if(!$email_active) {

    			throw new Exception(CommonHelper::error_message(1001), 1001);
            }

            if(Hash::check($request->password, $user_details->password)) {

                // Generate new tokens
                
                // $user_details->token = Helper::generate_token();

                $user_details->token_expiry = Helper::generate_token_expiry();
                
                // Save device details

                $check_device_exist = User::where('device_token', $request->device_token)->first();

                if($check_device_exist) {

                    $check_device_exist->device_token = "";
                    
                    $check_device_exist->save();
                }

                $user_details->device_token = $request->device_token ? $request->device_token : $user_details->device_token;

                $user_details->device_type = $request->device_type ? $request->device_type : $user_details->device_type;

                $user_details->login_by = $request->login_by ? $request->login_by : $user_details->login_by;

                $user_details->save();

                $data = User::CommonResponse()->find($user_details->id);

                $data->email_notification_status = (int) $user_details->email_notification_status; // Don't remove int (used ios)

                $data->push_status = (int) $user_details->push_status; // Don't remove int (used ios)

                $data->is_appstore_updated = Setting::get('ios_payment_subscription_status', 0);

                $response_array = ['success' => true, 'message' => CommonHelper::success_message(101) , 'data' => $data];

            } else {

				throw new Exception(CommonHelper::error_message(102), 102);
                
            }

            DB::commit();

            return response()->json($response_array, 200);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }
 
    /**
     * @method forgot_password()
     *
     * @uses If the user forgot his/her password he can hange it over here
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request - Email id
     *
     * @return send mail to the valid user
     */
    
    public function forgot_password(Request $request) {

        try {

            DB::beginTransaction();

            // Check email configuration and email notification enabled by admin

            if(Setting::get('email_notification') != YES || envfile('MAIL_USERNAME') == "" || envfile('MAIL_PASSWORD') == "" ) {

                throw new Exception(CommonHelper::error_message(106), 106);
                
            }
            
            $validator = Validator::make($request->all(),
                [
                    'email' => 'required|email|exists:users,email',
                ],
                [
                    'exists' => 'The :attribute doesn\'t exists',
                ]
            );

            if($validator->fails()) {
                
                $error = implode(',',$validator->messages()->all());
                
                throw new Exception($error , 101);
            
            }

            $user_details = User::where('email' , $request->email)->first();

            if(!$user_details) {

                throw new Exception(CommonHelper::error_message(1002), 1002);
            }

            if($user_details->login_by != "manual") {

                throw new Exception(CommonHelper::error_message(119), 119);
                
            }

            // check email verification

            if($user_details->is_verified == USER_EMAIL_NOT_VERIFIED) {

                throw new Exception(CommonHelper::error_message(120), 120);
            }

            // Check the user approve status

            if(in_array($user_details->status , [USER_DECLINED , USER_PENDING])) {
                throw new Exception(CommonHelper::error_message(121), 121);
            }

            $new_password = Helper::generate_password();

            $user_details->password = Hash::make($new_password);

            $email_data = array();

            $subject = apitr('user_forgot_email_title' , Setting::get('site_name'));

            $email_data['email']  = $user_details->email;

            $email_data['password'] = $new_password;

            $page = "emails.users.forgot-password";

            Log::info(print_r("user_email details".$email_data,true));

            $email_send_response = Helper::send_email($page,$subject,$user_details->email,$email_data);

            if($email_send_response->success) {

                if(!$user_details->save()) {

                    throw new Exception(CommonHelper::error_message(103), 103);

                }

                $response_array = ['success' => true , 'message' => CommonHelper::success_message(102), 'code' => 102];

            } else {

                $error = $email_send_response->message;
                
                throw new Exception($error, $email_send_response->error_code);
            }

            DB::commit();

            return response()->json($response_array, 200);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    
    }

    /**
     * @method change_password()
     *
     * @uses To change the password of the user
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request - Password & confirm Password
     *
     * @return json response of the user
     */
    public function change_password(Request $request) {

        try {

            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                    'password' => 'required|confirmed|min:6',
                    'old_password' => 'required|min:6',
                ]);

            if($validator->fails()) {
                
                $error = implode(',',$validator->messages()->all());
               
                throw new Exception($error , 101);
           
            }

            $user_details = User::find($request->id);

            if(!$user_details) {

                throw new Exception(CommonHelper::error_message(1002), 1002);
            }

            if($user_details->login_by != "manual") {

                throw new Exception(CommonHelper::error_message(121), 121);
                
            }

            if(Hash::check($request->old_password,$user_details->password)) {

                $user_details->password = Hash::make($request->password);
                
                if($user_details->save()) {

                    DB::commit();

                    return $this->sendResponse(CommonHelper::success_message(104), $success_code = 104, $data = []);
                
                } else {

                    throw new Exception(CommonHelper::error_message(103), 103);   
                }

            } else {

                throw new Exception(CommonHelper::error_message(108) , 108);
            }

            

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /** 
     * @method profile()
     *
     * @uses To display the user details based on user  id
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function profile(Request $request) {

        try {

            $user_details = User::where('id' , $request->id)->CommonResponse()->first();

            if(!$user_details) { 

                throw new Exception(CommonHelper::error_message(1002) , 1002);
            }

            $user_details->dob = ($user_details->dob == 0000-00-00) ? '' : $user_details->dob;
            
            $card_last_four_number = "";

            if($user_details->user_card_id) {

                $card = Card::find($user_details->user_card_id);

                if($card) {

                    $card_last_four_number = $card->last_four;

                }

            }

            $data = $user_details->toArray();

            $data['card_last_four_number'] = $card_last_four_number;

            //$overall_rating = ProviderRating::where('user_id', $request->id)->avg('rating');

            // $data['overall_rating'] =   $overall_rating ? intval($overall_rating) : 0;

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }
 
    /**
     * @method update_profile()
     *
     * @uses To update the user details
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param objecct $request : User details
     *
     * @return json response with user details
     */
    public function update_profile(Request $request) {

        try {

            DB::beginTransaction();
            
            $validator = Validator::make($request->all(),
                [
                    'name' => 'required|max:255',
                    'email' => 'email|unique:users,email,'.$request->id.'|max:255',
                    'mobile' => 'digits_between:6,13',
                    'picture' => 'mimes:jpeg,bmp,png',
                    'gender' => 'in:male,female,others',
                    'device_token' => '',
                    'description' => ''
                ]);

            if($validator->fails()) {

                // Error messages added in response for debugging

                $error = implode(',',$validator->messages()->all());
             
                throw new Exception($error , 101);
                
            }

            $user_details = User::find($request->id);

            if(!$user_details) { 

                throw new Exception(CommonHelper::error_message(1002) , 1002);
            }

            $user_details->name = $request->name ? $request->name : $user_details->name;
            
            if($request->has('email')) {

                $user_details->email = $request->email;
            }

            $user_details->mobile = $request->mobile ?: $user_details->mobile;

            $user_details->gender = $request->gender ?: $user_details->gender;

            $user_details->description = $request->description ?: '';

            if($request->dob) {

                $user_details->dob = date('Y-m-d', strtotime($request->dob));

            }

            // Upload picture
            if($request->hasFile('picture') != "") {

                Helper::delete_picture($user_details->picture, COMMON_FILE_PATH); // Delete the old pic

                $user_details->picture = Helper::normal_upload_picture($request->file('picture'), COMMON_FILE_PATH);

            }

            if($user_details->save()) {

            	$data = User::CommonResponse()->find($user_details->id);

                DB::commit();

                return $this->sendResponse($message = apitr('user_profile_update_success'), $success_code = 200, $data);

            } else {    

        		throw new Exception(CommonHelper::error_message(103) , 103);
            }

        } catch (Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
   
    }

    /**
     * @method delete_account()
     * 
     * @uses Delete user account based on user id
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request - Password and user id
     *
     * @return json with boolean output
     */

    public function delete_account(Request $request) {

        try {

            DB::beginTransaction();

            $request->request->add([ 
                'login_by' => $this->loginUser ? $this->loginUser->login_by : "manual",
            ]);

            $validator = Validator::make($request->all(),
                [
                    'password' => 'required_if:login_by,manual',
                ]);

            if($validator->fails()) {

                $error = implode(',',$validator->messages()->all());
             
                throw new Exception($error , 101);
                
            }

            $user_details = User::find($request->id);

            if(!$user_details) {

            	throw new Exception(CommonHelper::error_message(1002), 1002);
                
            }

            // The password is not required when the user is login from social. If manual means the password is required

            if($user_details->login_by == 'manual') {

                if(!Hash::check($request->password, $user_details->password)) {

                    $is_delete_allow = NO ;

                    $error = CommonHelper::error_message(108);
         
                    throw new Exception($error , 108);
                    
                }
            
            }

            if($user_details->delete()) {

                DB::commit();

                // @todo 

                $message = apitr('account_delete_success');

                return $this->sendResponse($message, $success_code = 200, $data = []);

            } else {

                // @todo 

            	throw new Exception("Error Processing Request", 101);
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

	}

    /**
     * @method logout()
     *
     * @uses Logout the user
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param 
     * 
     * @return
     */
    public function logout(Request $request) {

        // @later no logic for logout

        return $this->sendResponse(CommonHelper::success_message(106), 106);

    }

    /**
     * @method cards_list()
     *
     * @uses get the user payment mode and cards list
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param integer id
     * 
     * @return
     */

    public function cards_list(Request $request) {

        try {

            // @todo card_holder_name

            $user_cards = Card::where('user_id' , $request->id)->select('id as user_card_id' , 'customer_id' , 'last_four' ,'card_name', 'card_token' , 'is_default' )->get();

            // $data = $user_cards ? $user_cards : []; 

            $card_payment_mode = $payment_modes = [];

            $card_payment_mode['name'] = "Card";

            $card_payment_mode['payment_mode'] = "card";

            $card_payment_mode['is_default'] = YES;

            array_push($payment_modes , $card_payment_mode);

            $data['payment_modes'] = $payment_modes;   

            $data['cards'] = $user_cards ? $user_cards : []; 

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }
    
    /**
     * @method cards_add()
     *
     * @uses Update the selected payment mode 
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param Form data
     * 
     * @return JSON Response
     */

    public function cards_add(Request $request) {

        try {

            DB::beginTransaction();

            if(Setting::get('stripe_secret_key')) {

                \Stripe\Stripe::setApiKey(Setting::get('stripe_secret_key'));

            } else {

                throw new Exception(CommonHelper::error_message(133), 133);
            }
        
            $validator = Validator::make(
                    $request->all(),
                    [
                        'card_token' => 'required',
                    ]
                );

            if($validator->fails()) {

                $error = implode(',',$validator->messages()->all());
             
                throw new Exception($error , 101);

            } else {

                Log::info("INSIDE CARDS ADD");

                $user_details = User::find($request->id);

                if(!$user_details) {

                    throw new Exception(CommonHelper::error_message(1002), 1002);
                    
                }

                // Get the key from settings table
                
                $customer = \Stripe\Customer::create([
                        "card" => $request->card_token,
                        "email" => $user_details->email,
                        "description" => "Customer for ".Setting::get('site_name'),
                    ]);

                if($customer) {

                    $customer_id = $customer->id;

                    $card_details = new Card;

                    $card_details->user_id = $request->id;

                    $card_details->customer_id = $customer_id;

                    $card_details->card_token = $customer->sources->data ? $customer->sources->data[0]->id : "";

                    $card_details->card_name = $customer->sources->data ? $customer->sources->data[0]->brand : "";

                    $card_details->last_four = $customer->sources->data[0]->last4 ? $customer->sources->data[0]->last4 : "";

                    // Check is any default is available

                    $check_card_details = Card::where('user_id',$request->id)->count();

                    $card_details->is_default = $check_card_details ? 0 : 1;


                    if($card_details->save()) {

                        if($user_details) {

                            $user_details->card_id = $check_card_details ? $user_details->card_id : $card_details->id;

                            $user_details->save();
                        }

                        $data = Card::where('id' , $card_details->id)->select('id as user_card_id' , 'customer_id' , 'last_four' ,'card_name', 'card_token' , 'is_default' )->first();

                        DB::commit();

                        $response_array = ['success' => true , 'message' => CommonHelper::success_message(105) , 'data' => $data];

                    } else {

                        throw new Exception(CommonHelper::error_message(117), 117);
                        
                    }
               
                } else {

                    throw new Exception(CommonHelper::error_message(117) , 117);
                    
                }
            
            }

            

            return response()->json($response_array , 200);

        } catch(Stripe_CardError $e) {

            Log::info("error1");

            $error1 = $e->getMessage();

            $response_array = array('success' => false , 'error' => $error1 ,'error_code' => 903);

            return response()->json($response_array , 200);

        } catch (Stripe_InvalidRequestError $e) {

            // Invalid parameters were supplied to Stripe's API

            Log::info("error2");

            $error2 = $e->getMessage();

            $response_array = array('success' => false , 'error' => $error2 ,'error_code' => 903);

            return response()->json($response_array , 200);

        } catch (Stripe_AuthenticationError $e) {

            Log::info("error3");

            // Authentication with Stripe's API failed
            $error3 = $e->getMessage();

            $response_array = array('success' => false , 'error' => $error3 ,'error_code' => 903);

            return response()->json($response_array , 200);

        } catch (Stripe_ApiConnectionError $e) {
            Log::info("error4");

            // Network communication with Stripe failed
            $error4 = $e->getMessage();

            $response_array = array('success' => false , 'error' => $error4 ,'error_code' => 903);

            return response()->json($response_array , 200);

        } catch (Stripe_Error $e) {
            Log::info("error5");

            // Display a very generic error to the user, and maybe send
            // yourself an email
            $error5 = $e->getMessage();

            $response_array = array('success' => false , 'error' => $error5 ,'error_code' => 903);

            return response()->json($response_array , 200);

        } catch (\Stripe\StripeInvalidRequestError $e) {

            Log::info("error7");

            // Log::info(print_r($e,true));

            $response_array = array('success' => false , 'error' => CommonHelper::error_message(903) ,'error_code' => 903);

            return response()->json($response_array , 200);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
   
    }

    /**
     * @method cards_delete()
     *
     * @uses delete the selected card
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param integer user_card_id
     * 
     * @return JSON Response
     */

    public function cards_delete(Request $request) {

        // Log::info("cards_delete");

        DB::beginTransaction();

        try {
    
            $user_card_id = $request->user_card_id;

            $validator = Validator::make(
                $request->all(),
                array(
                    'user_card_id' => 'required|integer|exists:cards,id,user_id,'.$request->id,
                ),
                array(
                    'exists' => 'The :attribute doesn\'t belong to user:'.$this->loginUser->name
                )
            );

            if($validator->fails()) {

               $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);

            } else {

                $user_details = User::find($request->id);

                // No need to prevent the deafult card delete. We need to allow user to delete the all the cards

                // if($user_details->card_id == $user_card_id) {

                //     throw new Exception(tr('card_default_error'), 101);
                    
                // } else {

                    Card::where('id',$user_card_id)->delete();

                    if($user_details) {

                        if($user_details->payment_mode = CARD) {

                            // Check he added any other card

                            if($check_card = Card::where('user_id' , $request->id)->first()) {

                                $check_card->is_default =  DEFAULT_TRUE;

                                $user_details->card_id = $check_card->id;

                                $check_card->save();

                            } else { 

                                $user_details->payment_mode = COD;

                                $user_details->card_id = DEFAULT_FALSE;
                            
                            }
                       
                        }

                        // Check the deleting card and default card are same

                        if($user_details->card_id == $user_card_id) {

                            $user_details->card_id = DEFAULT_FALSE;

                            $user_details->save();
                        }
                        
                        $user_details->save();
                    
                    }

                    $response_array = ['success' => true , 'message' => CommonHelper::success_message(107) , 'code' => 107];

                // }

            }

            DB::commit();
    
            return response()->json($response_array , 200);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    }

    /**
     * @method cards_default()
     *
     * @uses update the selected card as default
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param integer id
     * 
     * @return JSON Response
     */
    public function cards_default(Request $request) {

        Log::info("cards_default");

        try {

            DB::beginTransaction();

            $validator = Validator::make(
                $request->all(),
                array(
                    'user_card_id' => 'required|integer|exists:cards,id,user_id,'.$request->id,
                ),
                array(
                    'exists' => 'The :attribute doesn\'t belong to user:'.$this->loginUser->name
                )
            );

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);
                   
            }

            $old_default_cards = Card::where('user_id' , $request->id)->where('is_default', DEFAULT_TRUE)->update(array('is_default' => DEFAULT_FALSE));

            $card = Card::where('id' , $request->user_card_id)->update(['is_default' => DEFAULT_TRUE]);

            $user_details = User::find($request->id);

            $user_details->card_id = $request->user_card_id;

            $user_details->save();           

            DB::commit();

            return $this->sendResponse($message = CommonHelper::success_message(108), $success_code = "108", $data = []);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    } 

    /**
     * @method notification_settings()
     *
     * @uses To enable/disable notifications of email / push notification
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param - 
     *
     * @return JSON Response
     */
    public function notification_settings(Request $request) {

        try {

            DB::beginTransaction();

            $validator = Validator::make(
                $request->all(),
                array(
                    'status' => 'required|numeric',
                    'type'=>'required|in:'.EMAIL_NOTIFICATION.','.PUSH_NOTIFICATION
                )
            );

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);

            }
                
            $user_details = User::find($request->id);

            if(!$user_details) {
                throw new Exception(CommonHelper::error_message(1002), 1002);
            }

            if($request->type == EMAIL_NOTIFICATION) {

                $user_details->email_notification_status = $request->status;

            }

            if($request->type == PUSH_NOTIFICATION) {

                $user_details->push_status = $request->status;

            }

            $user_details->save();

            $code = $request->status ? 206 : 207;

            $message = CommonHelper::success_message($code);

            $data = [
                    'id' => $user_details->id , 
                    'token' => $user_details->token, 
                    'email_notification_status' => (int) $user_details->email_notification_status,  // Don't remove int (used ios)
                    'push_status' => (int) $user_details->push_status,    // Don't remove int (used ios)
                ];
                
            DB::commit();

            return $this->sendResponse($message, $code, $data);

        } catch (Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method configurations()
     *
     * @uses used to get the configurations for base products
     *
     * @created Vithya R Chandrasekar
     *
     * @updated - 
     *
     * @param - 
     *
     * @return JSON Response
     */
    public function configurations(Request $request) {

        try {

            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:users,id',
                'token' => 'required',

            ]);

            if($validator->fails()) {

                $error = implode(',',$validator->messages()->all());

                throw new Exception($error, 101);

            }

            $config_data = $data = [];

            $payment_data['is_stripe'] = 1;

            $payment_data['stripe_publishable_key'] = Setting::get('stripe_publishable_key') ?: "";

            $payment_data['stripe_secret_key'] = Setting::get('stripe_secret_key') ?: "";

            $payment_data['stripe_secret_key'] = Setting::get('stripe_secret_key') ?: "";

            $data['payments'] = $payment_data;

            $data['urls']  = [];

            $url_data['base_url'] = envfile("APP_URL") ?: "";

            $url_data['chat_socket_url'] = Setting::get("chat_socket_url") ?: "";

            $data['urls'] = $url_data;

            $notification_data['FCM_SENDER_ID'] = "";

            $notification_data['FCM_SERVER_KEY'] = $notification_data['FCM_API_KEY'] = "";

            $notification_data['FCM_PROTOCOL'] = "";

            $data['notification'] = $notification_data;

            $data['site_name'] = Setting::get('site_name');

            $data['site_logo'] = Setting::get('site_logo');

            $data['currency'] = $this->currency;

            $data['spam_reasons'] = getReportVideoTypes();

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
   
    }

    /**
     * @method static_pages_api()
     *
     * @uses used to get the pages
     *
     * @created Vidhya R 
     *
     * @edited Vidhya R
     *
     * @param - 
     *
     * @return JSON Response
     */

    public function static_pages_api(Request $request) {

        if($request->page_type) {

            $static_page = Page::where('type' , $request->page_type)
                                ->where('status' , APPROVED)
                                ->select('id as page_id' , 'title' , 'description','type as page_type', 'status' , 'created_at' , 'updated_at')
                                ->first();

            $response_array = ['success' => true , 'data' => $static_page];

        } else {

            $static_pages = Page::where('status' , APPROVED)->orderBy('id' , 'asc')
                                ->select('id as page_id' , 'title' , 'description','type as page_type', 'status' , 'created_at' , 'updated_at')
                                ->orderBy('title', 'asc')
                                ->get();

            $response_array = ['success' => true , 'data' => $static_pages ? $static_pages->toArray(): []];

        }

        return response()->json($response_array , 200);

    }

    /**
     * @method coupon_codes_check()
     *
     * @uses check the coupon code status for the video | Subscrition
     *
     * @created vidhya R
     *
     * @updated vidhya R
     *
     * @param object $request - User details, subscription details
     *
     * @return response of coupon details with amount
     *
     */
    public function coupon_codes_check(Request $request) {

        try {

            $validator = Validator::make($request->all(), 
                [
                    'coupon_code' => 'required|exists:coupons,coupon_code',  
                    'subscription_id'=>'exists:subscriptions,id',
                    'video_tape_id' => 'exists:video_tapes,id'        
                ], 
                [
                    'subscription_id' => CommonHelper::error_message(203),
                    'video_tape_id' => CommonHelper::error_message(200),
                    'coupon_code' => CommonHelper::error_message(205)
                ]
            );
            
            if ($validator->fails()) {

                $error = implode(',',$validator->messages()->all());

                throw new Exception($error, 101);
            
            }

            $coupon_details = Coupon::where('coupon_code', $request->coupon_code)->where('status', APPROVED)->first();

            if(!$coupon_details) {

                throw new Exception(CommonHelper::error_message(205), 205);
                
            }

            if(strtotime($coupon_details->expiry_date) < strtotime(date('Y-m-d'))) {

                throw new Exception(CommonHelper::error_message(206), 206);
                
            }

            $user_details = User::find($request->id);

            // Check the coupon code can usable by the user
    
            $coupon_codes_check = PaymentRepo::check_coupon_applicable_to_user($user_details, $coupon_details)->getData();

            if ($coupon_codes_check->success) {

                // Get the amount from corresponding modules

                if($request->subscription_id) {

                    $view_details = Subscription::find($request->subscription_id);

                    if(!$view_details) {

                        throw new Exception(CommonHelper::error_message(203), 203);

                    }

                    $module_amount = $view_details->amount ?: 0.00;

                } else {

                    $view_details = VideoTape::find($request->video_tape_id);

                    if(!$view_details) {

                        throw new Exception(CommonHelper::error_message(200), 200);
                        
                    }

                    $module_amount = $view_details->ppv_amount ?: 0.00;

                }

                // If coupon code is percentage convert the amount

                $coupon_amount = $coupon_details->amount;

                $original_coupon_amount = formatted_amount($coupon_amount); // For response

                if($coupon_details->amount_type == PERCENTAGE) {

                    $coupon_amount = amount_convertion($coupon_details->amount, $module_amount);

                    $original_coupon_amount = $coupon_details->amount."%";

                }

                // Compare and send the amount details as response

                $remaining_amount = 0;

                if($coupon_amount <= 0) {

                    $remaining_amount = $module_amount;

                } elseif($module_amount >= $coupon_amount && $coupon_amount > 0) {

                    $remaining_amount = $module_amount - $coupon_amount;

                }

                $data = [
                        'module_amount' => $module_amount,
                        'remaining_amount' => $remaining_amount, 
                        'coupon_amount' => $coupon_amount,
                        'coupon_code' => $coupon_details->coupon_code,
                        'original_coupon_amount' => $original_coupon_amount,
                        'currency' => $this->currency,
                        ];
                   
                return $this->sendResponse($message = CommonHelper::success_message(220), $code = 220, $data);

            } else {

                throw new Exception($coupon_codes_check->error, $coupon_codes_check->error_code);
            }

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }


    /**
     * @method subscriptions() 
     *
     * @uses used to get the list of subscriptions
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param
     *
     * @return json repsonse
     */     

    public function subscriptions(Request $request) {

        try {

            $base_query = Subscription::where('subscriptions.status', APPROVED)->CommonResponse();

            $subscriptions = $base_query->skip($this->skip)->take($this->take)->orderBy('updated_at', 'desc')->get();

            foreach ($subscriptions as $key => $subscription_details) {

                $subscription_details->is_free_plan = $subscription_details->amount > 0 ? NO : YES;

                $subscription_details->amount_formatted = formatted_amount($subscription_details->amount);
            }

            return $this->sendResponse($message = "", $code = 200, $subscriptions);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method subscriptions_payment_by_stripe() 
     *
     * @uses used to deduct amount for selected subscription
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param
     *
     * @return json repsonse
     */     

    public function subscriptions_payment_by_stripe(Request $request) {

        try {

            $validator = Validator::make($request->all(), [
                'subscription_id' => 'required|exists:subscriptions,id',
                'coupon_code'=>'exists:coupons,coupon_code',
            ],
            [
                'subscription_id' => CommonHelper::error_message(203),
                'coupon_code' => CommonHelper::error_message(205)
            ]
            );

            if ($validator->fails()) {

                // Error messages added in response for debugging

                $error = implode(',',$validator->messages()->all());

                throw new Exception($error, 101);

            }

            DB::beginTransaction();

            // Check Subscriptions

            $subscription_details = Subscription::where('id', $request->subscription_id)->where('status', APPROVED)->first();

            if (!$subscription_details) {

                throw new Exception(CommonHelper::error_message(203), 203);
            }

            $user_details  = User::find($request->id);

            // Initial detault values

            $total = $subscription_details->amount; 

            $coupon_amount = 0.00;
           
            $coupon_reason = ""; 

            $is_coupon_applied = COUPON_NOT_APPLIED;

            // Check the coupon code

            if($request->coupon_code) {
                
                $coupon_code_response = PaymentRepo::check_coupon_code($request, $user_details, $subscription_details->amount);

                $coupon_amount = $coupon_code_response['coupon_amount'];

                $coupon_reason = $coupon_code_response['coupon_reason'];

                $is_coupon_applied = $coupon_code_response['is_coupon_applied'];

                $total = $coupon_code_response['total'];

            }

            // Update the coupon details and total to the request

            $request->coupon_amount = $coupon_amount ?: 0.00;

            $request->coupon_reason = $coupon_reason ?: "";

            $request->is_coupon_applied = $is_coupon_applied;

            $request->total = $total ?: 0.00;

            $request->payment_mode = CARD;

            // If total greater than zero, do the stripe payment

            if($request->total > 0) {

                // Check provider card details

                $card_details = Card::where('user_id', $request->id)->where('is_default', YES)->first();

                if (!$card_details) {

                    throw new Exception(CommonHelper::error_message(111), 111);
                }

                $customer_id = $card_details->customer_id;

                // Check stripe configuration
            
                $stripe_secret_key = Setting::get('stripe_secret_key');

                if(!$stripe_secret_key) {

                    throw new Exception(CommonHelper::error_message(107), 107);

                } 

                try {

                    \Stripe\Stripe::setApiKey($stripe_secret_key);

                    $total = $subscription_details->amount;

                    $currency_code = Setting::get('currency_code', 'USD') ?: "USD";

                    $charge_array = [
                                        "amount" => $total * 100,
                                        "currency" => $currency_code,
                                        "customer" => $customer_id,
                                    ];

                    $stripe_payment_response =  \Stripe\Charge::create($charge_array);

                    $payment_id = $stripe_payment_response->id;

                    $amount = $stripe_payment_response->amount/100;

                    $paid_status = $stripe_payment_response->paid;

                } catch(Stripe_CardError | Stripe_InvalidRequestError | Stripe_AuthenticationError | Stripe_ApiConnectionError | Stripe_Error $e) {

                    $error_message = $e->getMessage();

                    $error_code = $e->getCode();

                    // Payment failure function

                    DB::commit();

                    // @todo changes

                    $response_array = ['success' => false, 'error'=> $error_message , 'error_code' => 205];

                    return response()->json($response_array);

                } 

            }

            $response_array = PaymentRepo::subscriptions_payment_save($request, $subscription_details, $user_details);

            DB::commit();

            return response()->json($response_array, 200);

        } catch(Exception $e) {

            // Something else happened, completely unrelated to Stripe

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method subscriptions_payment_by_paypal() 
     *
     * @uses used to deduct amount for selected subscription
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param
     *
     * @return json repsonse
     */     

    public function subscriptions_payment_by_paypal(Request $request) {

        try {

            $validator = Validator::make($request->all(), [
                'subscription_id' => 'required|exists:subscriptions,id',
                'coupon_code'=>'exists:coupons,coupon_code',
            ],
            [
                'subscription_id' => CommonHelper::error_message(203),
                'coupon_code' => CommonHelper::error_message(205)
            ]
            );

            if ($validator->fails()) {

                // Error messages added in response for debugging

                $error = implode(',',$validator->messages()->all());

                throw new Exception($error, 101);

            }

            DB::beginTransaction();

            // Check Subscriptions

            $subscription_details = Subscription::where('id', $request->subscription_id)->where('status', APPROVED)->first();

            if (!$subscription_details) {

                throw new Exception(CommonHelper::error_message(203), 203);
            }

            $user_details  = User::find($request->id);

            // Initial detault values

            $total = $subscription_details->amount; 

            $coupon_amount = 0.00;
           
            $coupon_reason = ""; 

            $is_coupon_applied = COUPON_NOT_APPLIED;

            // Check the coupon code

            if($request->coupon_code) {
                
                $coupon_code_response = PaymentRepo::check_coupon_code($request, $user_details, $subscription_details->amount);

                $coupon_amount = $coupon_code_response['coupon_amount'];

                $coupon_reason = $coupon_code_response['coupon_reason'];

                $is_coupon_applied = $coupon_code_response['is_coupon_applied'];

                $total = $coupon_code_response['total'];

            }

            // Update the coupon details and total to the request

            $request->coupon_amount = $coupon_amount ?: 0.00;

            $request->coupon_reason = $coupon_reason ?: "";

            $request->is_coupon_applied = $is_coupon_applied;

            $request->total = $total ?: 0.00;

            $request->payment_mode = CARD;

            $request->payment_id = $request->payment_id ?: generate_payment_id($request->id, $subscription_details->id, $total);

            $response_array = PaymentRepo::subscriptions_payment_save($request, $subscription_details, $user_details);

            DB::commit();

            return response()->json($response_array, 200);

        } catch(Exception $e) {

            // Something else happened, completely unrelated to Stripe

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method subscriptions_history() 
     *
     * @uses List of subscription payments
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param
     *
     * @return json repsonse
     */     

    public function subscriptions_history(Request $request) {

        try {

            $base_query = UserPayment::where('user_id', $request->id)->select('user_payments.id as user_payment_id', 'user_payments.*');

            $user_payments = $base_query->skip($this->skip)->take($this->take)->orderBy('user_payments.is_current', 'desc')->get();

            foreach ($user_payments as $key => $payment_details) {

                $payment_details->coupon_amount_formatted = formatted_amount($payment_details->coupon_amount);

                $payment_details->subscription_amount_formatted = formatted_amount($payment_details->subscription_amount);

                $payment_details->amount_formatted = formatted_amount($payment_details->amount);

                $payment_details->title = $payment_details->description = "";

                $payment_details->is_free_plan = $payment_details->is_autorenewal = NO;

                $subscription_details = Subscription::find($payment_details->subscription_id);

                if($subscription_details) {

                    $payment_details->is_free_plan = $subscription_details->amount <= 0 ? YES : NO;

                    $payment_details->title = $subscription_details->title ?: "";

                    $payment_details->description = $subscription_details->description ?: "";
                }

                if($payment_details->is_free_plan == NO) {

                    $payment_details->is_autorenewal = ($payment_details->is_current && strtotime($payment_details->expiry_date) > strtotime(date('Y-m-d H:i:s')) ) ? YES : NO;
                }


                unset($payment_details->id);
            }

            return $this->sendResponse($message = "", $code = 200, $user_payments);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

   /**
     * @method subscriptions_autorenewal_pause
     *
     * @uses To cancel automatic subscription
     *
     * @created Vithya
     *
     * @updated
     *
     * @param object $request - USer details & payment details
     *
     * @return boolean response with message
     */
    
    public function subscriptions_autorenewal_pause(Request $request) {

        try {

            $validator = Validator::make($request->all(),
                    [
                        'cancel_reason' => 'required',
                    ]);

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);
            }

            DB::beginTransaction();

            // Get the current subscription 
            // @todo handle based on the current subscription

            $user_payment = UserPayment::where('user_id', $request->id)->where('status', PAID_STATUS)->orderBy('created_at', 'desc')->first();

            if(!$user_payment) {

                throw new Exception(CommonHelper::error_message(220), 220);
            }

            // Check the subscription is already cancelled

            if($user_payment->is_cancelled == AUTORENEWAL_CANCELLED) {

                throw new Exception(CommonHelper::error_message(221), 221);

            }

            $user_payment->is_cancelled = AUTORENEWAL_CANCELLED;

            $user_payment->cancel_reason = $request->cancel_reason;

            $user_payment->save();

            $data = ['user_payment_id' => $user_payment->id];

            DB::commit();

            return $this->sendResponse($message = CommonHelper::success_message(224), 224, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /**
     * @method subscriptions_autorenewal_enable
     *
     * @uses Enable auto renewal for the current subscription
     *
     * @created Vithya
     *
     * @updated
     *
     * @param object $request - USer details & payment details
     *
     * @return boolean response with message
     */
    
    public function subscriptions_autorenewal_enable(Request $request) {

        try {

            DB::beginTransaction();

            // Get the current subscription 
            // @todo handle based on the current subscription

            $user_payment = UserPayment::where('user_id', $request->id)->where('status', PAID_STATUS)->orderBy('created_at', 'desc')->first();

            if(!$user_payment) {

                throw new Exception(CommonHelper::error_message(220), 220);
            }

            // Check the subscription is already enabled

            if($user_payment->is_cancelled == AUTORENEWAL_ENABLED) {

                throw new Exception(CommonHelper::error_message(222), 222);

            }

            $user_payment->is_cancelled = AUTORENEWAL_ENABLED;
          
            $user_payment->save();

            $data = ['user_payment_id' => $user_payment->id];

            DB::commit();

            return $this->sendResponse($message = CommonHelper::success_message(225), 225, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /**
     * @method wishlist_list()
     *
     * @uses Get the user saved the hosts
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id
     *
     * @return response of details
     */
    public function wishlist_list(Request $request) {

        try {

            $video_tapes = VideoHelper::wishlist_videos($request);

            return $this->sendResponse($message = "", $success_code = "", $video_tapes);

        } catch(Exception  $e) {
            
            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method wishlist_operations()
     *
     * @uses To add/Remove by using this operation favorite
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id, host_id
     *
     * @return response of details
     */
    public function wishlist_operations(Request $request) {

        try {

            DB::beginTransaction();

            $validator = Validator::make($request->all(),
                [
                    'clear_all_status' => 'in:'.YES.','.NO,
                    'video_tape_id' => $request->clear_all_status == NO ? 'required|exists:video_tapes,id,status,'.APPROVED : '', 
                ], 
                [
                    'required' => CommonHelper::error_message(200)
                ]
            );

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);

            }

            if($request->clear_all_status == YES) {

                Wishlist::where('user_id', $request->id)->delete();
                
                DB::commit();

                return $this->sendResponse($message = CommonHelper::success_message(202), $code = 202, $data = []);


            } else {

                $wishlist_details = Wishlist::where('video_tape_id', $request->video_tape_id)->where('user_id', $request->id)->first();

                if($wishlist_details) {

                    if($wishlist_details->delete()) {

                        DB::commit();

                        return $this->sendResponse($message = CommonHelper::success_message(201), $code = 201, $data = []);

                    } else {

                        throw new Exception(CommonHelper::error_message(209), 209);
                        
                    }

                } else {

                    $wishlist_details = new Wishlist;

                    $wishlist_details->user_id = $request->id;

                    $wishlist_details->video_tape_id = $request->video_tape_id;

                    $wishlist_details->status = APPROVED;

                    $wishlist_details->save();

                    DB::commit();
                    
                    $data = ['wishlist_id' => $wishlist_details->id];
               
                    return $this->sendResponse(CommonHelper::success_message(200), 200, $data = ['wishlist_id' => $wishlist_details->id, 'wishlist_status' => $wishlist_details->status]);

                }

            }

        } catch (Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method video_tapes_history()
     *
     * @uses get the logged in user video history details
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id
     *
     * @return response of details
     */
    public function video_tapes_history(Request $request) {

        try {

            // @todo wishlist videos common

            $video_tapes = VideoHelper::history_videos($request);

            return $this->sendResponse($message = "", $success_code = "", $video_tapes);

        } catch(Exception  $e) {
            
            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method video_tapes_history_add()
     *
     * @uses add video to user history and update the PPV details
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id
     *
     * @return response of details
     */
    public function video_tapes_history_add(Request $request) {

        try {

            Log::info("Video History Info".print_r($request->all(), true));

            $validator = Validator::make($request->all(),[
                    'video_tape_id' => 'required|integer|exists:video_tapes,id'
                ]
            );

            if ($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);

            }

            DB::beginTransaction();

            $ppv_details = PayPerView::where('user_id', $request->id)
                                ->where('video_id', $request->video_tape_id)
                                ->where('is_watched', '!=', WATCHED)
                                ->where('status', PAID_STATUS)
                                ->orderBy('ppv_date', 'desc')
                                ->first();

            if ($ppv_details) {

                $ppv_details->is_watched = WATCHED;

                $ppv_details->save();

            }

            $user_history_details = UserHistory::where('user_histories.user_id' , $request->id)->where('video_tape_id' ,$request->video_tape_id)->first();

            if(!$user_history_details) {

                $user_history_details = new UserHistory();

                $user_history_details->user_id = $request->id;

                $user_history_details->video_tape_id = $request->video_tape_id;

                $user_history_details->status = DEFAULT_TRUE;

                $user_history_details->save();

            }

            $video_tape_details = VideoTape::find($request->video_tape_id);

            $navigateback = NO;

            if ($request->id != $video_tape_details->user_id) {

                if ($video_tape_details->type_of_subscription == RECURRING_PAYMENT) {

                    $navigateback = YES;

                }
            }

            DB::commit();

            // navigateback = used to handle the replay in mobile for recurring payments

            $data['navigateback'] = $navigateback;
           
            return $this->sendResponse($message = CommonHelper::success_message(208), $success_code = 208, $data);

        } catch(Exception  $e) {

            DB::rollback();
            
            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method video_tapes_history_remove()
     *
     * @uses remove video from user history and update the PPV details
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id
     *
     * @return response of details
     */
    public function video_tapes_history_remove(Request $request) {

        try {

            $validator = Validator::make($request->all(),[
                    'video_tape_id' =>$request->clear_all_status ? 'integer|exists:video_tapes,id' : 'required|integer|exists:video_tapes,id'
                ]
            );

            if ($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);

            }

            DB::beginTransaction();

            if($request->clear_all_status) {

                $history = UserHistory::where('user_id',$request->id)->delete();

                $message = CommonHelper::success_message(210); $success_code = 210;

            } else {

                $history = UserHistory::where('user_id',$request->id)->where('video_tape_id' , $request->video_tape_id)->delete();

                $message = CommonHelper::success_message(209); $success_code = 209;

            }

            DB::commit();

            return $this->sendResponse($message, $success_code);

        } catch(Exception  $e) {
            
            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method home()
     *
     * @uses home page videos
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id
     *
     * @return response of details
     */
    
    public function home(Request $request) {

        try {
            
            $video_tapes = VideoHelper::mobile_home($request);

            return $this->sendResponse($message = "", $success_code = "", $video_tapes);

        } catch(Exception  $e) {
            
            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method trending()
     *
     * @uses trending videos
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id
     *
     * @return response of details
     */
    
    public function trending(Request $request) {

        try {
            
            $video_tapes = VideoHelper::trending($request);

            return $this->sendResponse($message = "", $success_code = "", $video_tapes);


        } catch(Exception  $e) {
            
            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method tags_based_videos()
     *
     * @uses tags_based_videos videos
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id
     *
     * @return response of details
     */
    
    public function tags_based_videos(Request $request) {

        try {

            $validator = Validator::make($request->all(), [
                'tag_id' => 'required|exists:categories,id',
                'reason' => 'required',
            ]);

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);
            }
            
            $video_tapes = VideoHelper::tags_based_videos($request);

            return $this->sendResponse($message = "", $success_code = "", $video_tapes);


        } catch(Exception  $e) {
            
            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method categories_based_videos()
     *
     * @uses categories_based_videos videos
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id
     *
     * @return response of details
     */
    
    public function categories_based_videos(Request $request) {

        try {

            $validator = Validator::make($request->all(), [
                'category_id' => 'required|exists:categories,id',
            ]);

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);
            }
            
            $video_tapes = VideoHelper::categories_based_videos($request);

            return $this->sendResponse($message = "", $success_code = "", $video_tapes);


        } catch(Exception  $e) {
            
            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method suggestions()
     *
     * @uses suggestions videos
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id
     *
     * @return response of details
     */
    
    public function suggestions(Request $request) {

        try {

            $validator = Validator::make($request->all(), [
                'video_tape_id' => 'exists:video_tapes,id',
            ]);

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);
            }
            
            $video_tapes = VideoHelper::suggestion_videos($request);

            return $this->sendResponse($message = "", $success_code = "", $video_tapes);


        } catch(Exception  $e) {
            
            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method spam_videos()
     *
     * @uses list of videos spammed by logged in user
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id
     *
     * @return response of details
     */

    public function spam_videos(Request $request) {

        try {
            
            $video_tapes = VideoHelper::spam_videos($request);

            return $this->sendResponse($message = "", $success_code = "", $video_tapes);


        } catch(Exception  $e) {
            
            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /**
     * @method spam_videos()
     *
     * @uses add video to spam
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id
     *
     * @return response of details
     */

    public function spam_videos_add(Request $request) {

        try {

            $validator = Validator::make($request->all(), [
                'video_tape_id' => 'required|exists:video_tapes,id',
                'reason' => 'required',
            ]);

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);
            }

            DB::beginTransaction();

            $spam_video = Flag::where('video_tape_id', $request->video_tape_id)->where('user_id', $request->id)->first();

            // If already exists remove

            if(!$spam_video) {

                $spam_video = new Flag;

                $spam_video->user_id = $request->id;

                $spam_video->video_tape_id = $request->video_tape_id;

                $spam_video->reason = $request->reason ?: "";

            }

            $spam_video->status = DEFAULT_TRUE;

            $spam_video->save();

            DB::commit();

            $data['video_tape_id'] = $request->video_tape_id;

            $data['flag_id'] = $spam_video->id;

            $message = CommonHelper::success_message(213);

            return $this->sendResponse($message, $code = 213, $data);

        } catch(Exception  $e) {

            DB::rollback();
            
            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method spam_videos_remove()
     *
     * @uses Remove | clear the video from spams 
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id
     *
     * @return response of details
     */

    public function spam_videos_remove(Request $request) {

        try {

            $validator = Validator::make($request->all(), [
                'video_tape_id' => $request->clear_all_status ? '' : 'required|exists:video_tapes,id',
            ]);

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);
            }

            DB::beginTransaction();

            if($request->clear_all_status) {

                $flag = Flag::where('user_id', $request->id)->delete();

                $message = CommonHelper::success_message(215); $code = 215;

            } else {

                $flag = Flag::where('user_id',$request->id)->where('video_tape_id' , $request->video_tape_id)->delete();

                $message = CommonHelper::success_message(214); $code = 214;

            }

            DB::commit();

            return $this->sendResponse($message, $code);

        } catch(Exception  $e) {

            DB::rollback();
            
            return $this->sendError($e->getMessage(), $e->getCode());
        }
    
    }

    /**
     * @method redeems()
     *
     * @uses redeems details for the loggedin user
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id
     *
     * @return response of details
     */

    public function redeems(Request $request) {

        try {

            // To remove the redeems while redeem requests skip & take

            if($request->skip == 0) {

                $redeem_details = Redeem::where('user_id' , $request->id)->select('total' , 'paid' , 'remaining' , 'status')->first();

                // If no record, create and send the empty details 

                if(!$redeem_details) {

                    $redeem_details = new Redeem;

                    $redeem_details->user_id = $request->id;

                    $redeem_details->total = $redeem_details->paid = $redeem_details->remaining = formatted_amount(0.00);

                    $redeem_details->status = DEFAULT_TRUE;

                    $redeem_details->save();

                }

                $redeem_details->minimum_redeem = intval(Setting::get('minimum_redeem', 1));

                $redeem_details->currency = $this->currency;

                $redeem_details->total_formatted = formatted_amount($redeem_details->total);

                $redeem_details->remaining_formatted = formatted_amount($redeem_details->remaining);
                $redeem_details->paid_formatted = formatted_amount($redeem_details->paid);

                $data['redeems'] = $redeem_details;
            
            }

            $skip = $this->skip ?: 0; $take = $this->take ?: TAKE_COUNT;

            $redeems_history = RedeemRequest::where('user_id' , $request->id)
                                    ->CommonResponse()
                                    ->orderBy('created_at', 'desc')
                                    ->skip($skip)
                                    ->take($take)
                                    ->get();

            foreach ($redeems_history as $key => $details) {

                $details->request_amount_formatted = formatted_amount($details->request_amount);

                $details->paid_amount_formatted = formatted_amount($details->paid_amount);

                $details->status_text = redeem_request_status($details->status);
            }

            $data['redeems_history'] = $redeems_history;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception  $e) {
            

            
            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method redeems_request_send()
     *
     * @uses send redeem request to admin
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id
     *
     * @return response of details
     */
    public function redeems_request_send(Request $request) {

        try {

            // Get admin configured - Minimum Provider Credit

            $minimum_redeem = Setting::get('minimum_redeem' , 1);

            // Get the user redeems details 

            $redeem_details = Redeem::where('user_id' , $request->id)->first();

            if(!$redeem_details) {

                throw new Exception(CommonHelper::error_message(211), 211);
            }

            $remaining = $redeem_details->remaining;

            // check the provider have more than minimum credits

            if($remaining < $minimum_redeem) {
                throw new Exception(CommonHelper::error_message(213), 213);

            }

            $redeem_amount = abs(intval($remaining - $minimum_redeem));

            // Check the redeems is not empty

            if(!$redeem_amount) {

                throw new Exception(CommonHelper::error_message(212), 212);
            }

            DB::beginTransaction();

            // Save Redeem Request

            $redeem_request = new RedeemRequest;

            $redeem_request->user_id = $request->id;

            $redeem_request->request_amount = $redeem_amount;

            $redeem_request->status = DEFAULT_FALSE;

            $redeem_request->save();

            // Update Redeems details 

            $redeem_details->remaining = abs($redeem_details->remaining-$redeem_amount);

            $redeem_details->save();

            $data['redeem_request_id'] = $redeem_request->id;

            DB::commit();

            return $this->sendResponse($message = CommonHelper::success_message(212), 212, $data);
        
        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method redeems_request_cancel()
     *
     * @uses redeems details for the loggedin user
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id
     *
     * @return response of details
     */
    public function redeems_request_cancel(Request $request) {

        try {

            $validator = Validator::make($request->all() , [
                'redeem_request_id' => 'required|exists:redeem_requests,id,user_id,'.$request->id,
                ]);

             if ($validator->fails()) {
                
                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);
            }

            // check the record exists

            $redeem_details = Redeem::where('user_id' , $request->id)->first();

            $redeem_request_details = RedeemRequest::find($request->redeem_request_id);

            if(!$redeem_details || !$redeem_request_details) {

                throw new Exception(CommonHelper::error_message(211), 211);
                
            }

            DB::beginTransaction();

            // Check status to cancel the redeem request

            if(in_array($redeem_request_details->status, [REDEEM_REQUEST_SENT , REDEEM_REQUEST_PROCESSING])) {

                // Update the redeem record

                $redeem_details->remaining = $redeem_details->remaining + abs($redeem_request_details->request_amount);

                $redeem_details->save();

                // Update the redeem request Status

                $redeem_request_details->status = REDEEM_REQUEST_CANCEL;

                $redeem_request_details->save();

                DB::commit();

                $data['redeem_request_id'] = $redeem_request_details->id;

                return $this->sendResponse(CommonHelper::success_message(211), 211, $data);

            } else {

                throw new Exception(CommonHelper::error_message(214), 214);
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method ppv_videos()
     *
     * @uses get the logged in user paid videos
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id
     *
     * @return response of details
     */
    public function ppv_videos(Request $request) {

        try {

            $video_tapes = VideoHelper::ppv_videos($request);

            return $this->sendResponse($message = "", $success_code = "", $video_tapes);

        } catch(Exception  $e) {
            
            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method ppv_payment_by_stripe() 
     *
     * @uses used to deduct amount for selected video
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param @todo not yet completed
     *
     * @return json repsonse
     */

    public function ppv_payment_by_stripe(Request $request) {

        try {

            $validator = Validator::make($request->all(), [
                'video_tape_id'=>'required|exists:video_tapes,id,status,'.USER_VIDEO_APPROVED.',is_approved,'.ADMIN_VIDEO_APPROVED.',publish_status,'.VIDEO_PUBLISHED,
                'payment_id'=>'',
                'coupon_code'=>'exists:coupons,coupon_code',
            ],
            [
                'video_tape_id' => CommonHelper::error_message(200),
                'coupon_code' => CommonHelper::error_message(205)
            ]
            );

            if ($validator->fails()) {

                $error = implode(',',$validator->messages()->all());

                throw new Exception($error, 101);

            }
            
            DB::beginTransaction();

            $video_tape_details = VideoTape::find($request->video_tape_id);

            $user_details = User::find($request->id);

            // Check video record

            if (!$video_tape_details) {

                throw new Exception(CommonHelper::error_message(200), 200);
            }

            // check ppv enabled for this video

            if($video_tape_details->is_pay_per_view == NO) {

                $message = CommonHelper::success_message(216); $code = 216;

                return $this->sendResponse($message, $code);
    
            }

            // Check the logged in user as channel owner

            if($video_tape_details->user_id == $request->id) {

                $message = CommonHelper::success_message(217); $code = 217;

                return $this->sendResponse($message, $code);

            }

            // Check the whether the user needs to pay or directly allow the user to watch video

            $is_user_can_watch_now = PaymentRepo::is_user_can_watch_now($request->id, $video_tape_details);

            if($is_user_can_watch_now == YES) {

                $message = CommonHelper::success_message(218); $code = 218;

                return $this->sendResponse($message, $code);
            }

            // Initial detault values

            $total = $video_tape_details->ppv_amount; 

            $coupon_amount = 0.00;
           
            $coupon_reason = ""; 

            $is_coupon_applied = COUPON_NOT_APPLIED;

            // Check the coupon code

            if($request->coupon_code) {
                
                $coupon_code_response = PaymentRepo::check_coupon_code($request, $user_details, $video_tape_details->ppv_amount);

                $coupon_amount = $coupon_code_response['coupon_amount'];

                $coupon_reason = $coupon_code_response['coupon_reason'];

                $is_coupon_applied = $coupon_code_response['is_coupon_applied'];

                $total = $coupon_code_response['total'];

            }

            // Update the coupon details and total to the request

            $request->coupon_amount = $coupon_amount ?: 0.00;

            $request->coupon_reason = $coupon_reason ?: "";

            $request->is_coupon_applied = $is_coupon_applied;

            $request->total = $total ?: 0.00;

            $request->payment_mode = CARD;

            // If total greater than zero, do the stripe payment

            if($request->total > 0) {

                // Check provider card details

                $card_details = Card::where('user_id', $request->id)->where('is_default', YES)->first();

                if (!$card_details) {

                    throw new Exception(CommonHelper::error_message(111), 111);
                }

                $customer_id = $card_details->customer_id;

                // Check stripe configuration
            
                $stripe_secret_key = Setting::get('stripe_secret_key');

                if(!$stripe_secret_key) {

                    throw new Exception(CommonHelper::error_message(107), 107);

                } 

                try {

                    \Stripe\Stripe::setApiKey($stripe_secret_key);

                    $total = $video_tape_details->ppv_amount;

                    $currency_code = Setting::get('currency_code', 'USD') ?: "USD";

                    $charge_array = [
                                        "amount" => $total * 100,
                                        "currency" => $currency_code,
                                        "customer" => $customer_id,
                                    ];

                    $stripe_payment_response =  \Stripe\Charge::create($charge_array);

                    $payment_id = $stripe_payment_response->id;

                    $amount = $stripe_payment_response->amount/100;

                    $paid_status = $stripe_payment_response->paid;

                    $request->payment_id = $payment_id;

                } catch(Stripe_CardError | Stripe_InvalidRequestError | Stripe_AuthenticationError | Stripe_ApiConnectionError | Stripe_Error $e) {

                    $error = $e->getMessage();

                    $error_code = $e->getCode();

                    // Payment failure function

                    DB::commit();

                    // @todo changes

                    $response_array = ['success' => false, 'error'=> $error , 'error_code' => 205];

                    return response()->json($response_array);

                } 

            }

            $response_array = PaymentRepo::ppv_payment_save($request, $video_tape_details, $user_details);

            DB::commit();

            return response()->json($response_array, 200);

        } catch(Exception $e) {

            // Something else happened, completely unrelated to Stripe

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method ppv_payment_by_paypal() 
     *
     * @uses used to deduct amount for selected video
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param 
     *
     * @return json repsonse
     */     
    public function ppv_payment_by_paypal(Request $request) {

        try {

            $validator = Validator::make($request->all(), [
                'video_tape_id'=>'required|exists:video_tapes,id,status,'.USER_VIDEO_APPROVED.',is_approved,'.ADMIN_VIDEO_APPROVED.',publish_status,'.VIDEO_PUBLISHED,
                'payment_id'=>'required',
                'coupon_code'=>'exists:coupons,coupon_code',
            ],
            [
                'video_tape_id' => CommonHelper::error_message(200),
                'coupon_code' => CommonHelper::error_message(205)
            ]
            );

            if ($validator->fails()) {

                $error = implode(',',$validator->messages()->all());

                throw new Exception($error, 101);

            }

            DB::beginTransaction();

            $video_tape_details = VideoTape::find($request->video_tape_id);

            $user_details = User::find($request->id);

            // Check video record

            if (!$video_tape_details) {

                throw new Exception(CommonHelper::error_message(200), 200);
            }

            // check ppv enabled for this video

            if($video_tape_details->is_pay_per_view == NO) {

                $message = CommonHelper::success_message(216); $code = 216;

                return $this->sendResponse($message, $code);
    
            }

            // Check the logged in user as channel owner

            if($video_tape_details->user_id == $request->id) {

                $message = CommonHelper::success_message(217); $code = 217;

                return $this->sendResponse($message, $code);

            }

            // Check the whether the user needs to pay or directly allow the user to watch video

            $is_user_can_watch_now = PaymentRepo::is_user_can_watch_now($request->id, $video_tape_details);

            if($is_user_can_watch_now == YES) {

                $message = CommonHelper::success_message(218); $code = 218;

                return $this->sendResponse($message, $code);
            }

            // Initial detault values

            $total = $video_tape_details->ppv_amount; 

            $coupon_amount = 0.00;
           
            $coupon_reason = ""; 

            $is_coupon_applied = COUPON_NOT_APPLIED;

            // Check the coupon code

            if($request->coupon_code) {
                
                $coupon_code_response = PaymentRepo::check_coupon_code($request, $user_details, $video_tape_details->ppv_amount);

                $coupon_amount = $coupon_code_response['coupon_amount'];

                $coupon_reason = $coupon_code_response['coupon_reason'];

                $is_coupon_applied = $coupon_code_response['is_coupon_applied'];

                $total = $coupon_code_response['total'];

            }

            // Update the coupon details and total to the request

            $request->coupon_amount = $coupon_amount ?: 0.00;

            $request->coupon_reason = $coupon_reason ?: "";

            $request->is_coupon_applied = $is_coupon_applied;

            $request->total = $total ?: 0.00;

            $request->payment_mode = PAYPAL;

            $response_array = PaymentRepo::ppv_payment_save($request, $video_tape_details, $user_details);

            DB::commit();

            return response()->json($response_array, 200);

        } catch(Exception $e) {

            // Something else happened, completely unrelated to Stripe

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method channels_unsubscribe_subscribe() 
     *
     * @uses used to update the subscribe status
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param
     *
     * @return json repsonse
     */ 
    public function channels_unsubscribe_subscribe(Request $request) {

        try {

            $validator = Validator::make( $request->all(), 
                    [
                        'channel_id' => 'required|exists:channels,id'
                    ]);


            if ($validator->fails()) {

                $error = implode(',',$validator->messages()->all());

                throw new Exception($error, 101);

            }

            DB::beginTransaction();

            $channel_details = Channel::where('status', USER_CHANNEL_APPROVED)
                                    ->where('is_approved', ADMIN_CHANNEL_APPROVED)
                                    ->where('id', $request->channel_id)
                                    ->first();

            // @todo $channel_deyails handle excpetion

            $channel_subscription_details = ChannelSubscription::where('user_id', $request->id)
                        ->where('channel_id',$request->channel_id)
                        ->first();

            $is_user_subscribed_the_channel = NO;

            if($channel_subscription_details) {

                // unsubscribe the details

                $channel_subscription_details->delete();

                $message = CommonHelper::success_message(222); $code = 222;

            } else {

                $channel_subscription_details = new ChannelSubscription;

                $channel_subscription_details->user_id = $request->id;

                $channel_subscription_details->channel_id = $request->channel_id;

                $channel_subscription_details->status = DEFAULT_TRUE;

                $channel_subscription_details->save();

                // Bell Notification

                $notification_data['from_user_id'] = $request->id; 

                $notification_data['to_user_id'] = $channel_details->user_id;

                $notification_data['channel_id'] = $channel_details->id;

                $notification_data['notification_type'] = BELL_NOTIFICATION_NEW_SUBSCRIBER;

                dispatch(new BellNotificationJob(json_decode(json_encode($notification_data))));

                $message = CommonHelper::success_message(221); $code = 221;

                $is_user_subscribed_the_channel = YES;

            }
                
            DB::commit();

            $subscriberscnt = subscriberscnt($request->channel_id);

            $data = ['channel_id' => $request->channel_id, 'is_user_subscribed_the_channel' => $is_user_subscribed_the_channel,'subscription_count' => $subscriberscnt];

            return $this->sendResponse($message, $code, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
   
    }

    /**
     * @method video_tapes_comments()
     *
     * @uses used to update the rating or comment of the video
     * 
     * @created Vithya R
     * 
     * @updated Vithya R
     *
     * @param integer $video_tape_id - Video Tape ID
     *
     * @return response of success/failure message
     */
    public function video_tapes_comments(Request $request) {

        try {

            $validator = Validator::make($request->all(),
                [
                    'video_tape_id' => 'required|integer|exists:video_tapes,id',
                ]);

            if ($validator->fails()) {

                $error = implode(',',$validator->messages()->all());

                throw new Exception($error, 101);
            }

            // Comments Section

            $data = UserRating::where('video_tape_id' , $request->video_tape_id)
                            ->CommonResponse()
                            ->orderby('created_at', 'desc')
                            ->skip($this->skip)
                            ->take($this->take)
                            ->get();

            
            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /**
     * @method video_tapes_comments_save()
     *
     * @uses used to update the rating or comment of the video
     * 
     * @created Vithya R
     * 
     * @updated Vithya R
     *
     * @param integer $video_tape_id - Video Tape ID
     *
     * @return response of success/failure message
     */
    public function video_tapes_comments_save(Request $request) {

        try {

            $validator = Validator::make(
                $request->all(),
                [
                    'video_tape_id' => 'required|integer|exists:video_tapes,id',
                    'rating' => 'integer|in:'.RATINGS,
                    'comment' => 'required',
                ],
                array(
                    'exists' => 'The :attribute doesn\'t exists please provide correct video id',
                    'unique' => 'The :attribute already rated.'
                )
            
            );

            if ($validator->fails()) {

                $error = implode(',',$validator->messages()->all());

                throw new Exception($error, 101);
            }

            DB::beginTransaction();

            // Save Rating

            $user_rating = new UserRating();

            $user_rating->user_id = $request->id;

            $user_rating->video_tape_id = $request->video_tape_id;

            $user_rating->rating = $request->rating ?: 0;

            $user_rating->comment = $request->comment ?: '';

            $user_rating->save();

            // Update video table with avg user rating

            $user_ratings = UserRating::select(
                    'rating', 'video_tape_id',DB::raw('sum(rating) as total_rating'))
                    ->where('video_tape_id', $request->video_tape_id)
                    ->groupBy('video_tape_id')
                    ->avg('rating');

            VideoTape::where('video_tapes.id', $request->video_tape_id)->update(['video_tapes.user_ratings' => $user_ratings]);

            $data = UserRating::where('user_ratings.id', $user_rating->id)->CommonResponse()->first();    

            DB::commit();

            $message = CommonHelper::success_message(223); $code = 223;

            return $this->sendResponse($message, $code, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode() ?: 217);

        }
    
    }

    /**
     * @method video_tapes_user_view
     *
     * @uses video tape details
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param integer video_tape_id
     *
     * @return json response
     */
    
    public function video_tapes_user_view(Request $request) {

        try {

            $validator = Validator::make($request->all(),
                [
                    'video_tape_id' => 'required|integer|exists:video_tapes,id',
                ]);

            if ($validator->fails()) {

                $error = implode(',',$validator->messages()->all());

                throw new Exception($error, 101);
            
            }

            $video_details = VideoTape::VerifiedVideo()->where('video_tapes.id', $request->video_tape_id)->first();

            // check the video record

            if(!$video_details) {

                throw new Exception(CommonHelper::error_message(200), 200);
                
            }

            // Check the video is in flag list

            $flag_details = Flag::where('video_tape_id', $request->video_tape_id)->where('user_id', $request->id)->count();

            if($flag_details) {

                throw new Exception(CommonHelper::error_message(224), 224); 
                
            }

            VideoRepo::watch_count($request->video_tape_id,$request->id,NO);

            $video_tape_details = V5Repo::single_video_response($request->video_tape_id, $request->id);

            $data = $video_tape_details;

            return $this->sendResponse($message = "", $code = 100, $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method categories_view()
     *
     * category details based on id
     *
     * @created Vithya
     *
     * @updated
     *
     * @param
     * 
     * @return response of json
     */
    public function categories_view(Request $request) {

        $basicValidator = Validator::make(
                $request->all(),
                array(
                    'category_id' => 'required|exists:categories,id,status,'.CATEGORY_APPROVE_STATUS,
                )
        );

        if($basicValidator->fails()) {

            $error = implode(',', $basicValidator->messages()->all());

            $response_array = ['success'=>false, 'error'=>$error];              

        } else {

            // For common response

            $category_details = Category::select('id as category_id', 'name', 'image as image', 'image as cover', 'description')->where('status', CATEGORY_APPROVE_STATUS)
                ->where('id', $request->category_id)
                ->first();


            $video_tapes = VideoHelper::categories_based_videos($request);

            $data['details'] = $category_details;

            $data['video_tapes'] = $video_tapes;

            $response_array = ['success' => true, 'data' => $data];

        }

        return response()->json($response_array);
    }

    /**
     * @method create_channel()
     *
     * @uses To create a channel based on the logged in user
     *
     * @created Vidhya R
     *
     * @updated vidhya R
     *
     * @param object $request - User id, token
     *
     * @return success/failure message of boolean 
     */ 
    public function channels_save(Request $request) {

        try {

            // Check the use can create multiple channel

            if(!$request->channel_id) {

                $channels = getChannels($request->id);

                if((count($channels) > 0 && Setting::get('multi_channel_status') == NO)) {

                    throw new Exception(CommonHelper::error_message(225), 225); // @todo 
                    
                }

            } else {

                $channel_details = Channel::where('user_id', $request->id)->where('id', $request->channel_id)->first();

                if(!$channel_details) {

                    throw new Exception(CommonHelper::error_message(223), 223);            

                }
            }

            $user_details = User::find($request->id);

            if(!$user_details) {

                throw new Exception(CommonHelper::error_message(1002), 1002);
                
            }

            DB::beginTransaction();

            $response = CommonRepo::channel_save($request)->getData();

            if($response->success) {

                DB::commit();

                $response_array = ['success'=>true, 'data'=>$response->data, 'message'=>$response->message];
               
                return response()->json($response_array);

            } else {

                // @todo check and replace error_messages

                throw new Exception($response->error_messages, $response->error_code);
                
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }
    /**
     * @method channels_delete()
     *
     * @uses To delete a channel based on logged in user id & channel id (Form Rendering)
     *
     * @created Vithya R
     *
     * @updated vithya R
     * 
     * @param integer $request - Channel Id
     *
     * @return response with flash message
     */
    public function channels_delete(Request $request) {

        try {

            $validator = Validator::make( $request->all(), array(
                            'channel_id' => 'required|exists:channels,id',
                        ));

            if($validator->fails()) {

                    $error = implode(',', $validator->messages()->all());

                    $response_array = ['success'=> false, 'error'=>$error];

                    // return back()->with('flash_errors', $error);

            }

            DB::beginTransaction();

            $channel_details = Channel::where('user_id', $request->id)->where('id', $request->channel_id)->first();

            if(!$channel_details) {

                throw new Exception(CommonHelper::error_message(223), 223);            

            }

            $channel_details->delete();

            DB::commit();

            $response_array = ['success'=>true, 'message'=>tr('channel_delete_success')];

            return response()->json($response_array);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * Like Videos
     *
     * @return JSON Response
     */

    public function video_tapes_like(Request $request) {

        $validator = Validator::make($request->all() , [
            'video_tape_id' => 'required|exists:video_tapes,id',
        ]);

        if ($validator->fails()) {
            
            $error = implode(',', $validator->messages()->all());
            
            $response_array = array('success' => false, 'error' => Helper::get_error_message(101), 
                    'error_code' => 101, 'error'=>$error);

        } else {

            $model = LikeDislikeVideo::where('video_tape_id', $request->video_tape_id)
                    ->where('user_id',$request->id)->first();

            $like_count = LikeDislikeVideo::where('video_tape_id', $request->video_tape_id)
                ->where('like_status', DEFAULT_TRUE)
                ->count();

            $dislike_count = LikeDislikeVideo::where('video_tape_id', $request->video_tape_id)
                ->where('dislike_status', DEFAULT_TRUE)
                ->count();

            $is_liked = YES;

            $message = CommonHelper::success_message(226);

            if (!$model) {

                $model = new LikeDislikeVideo;

                $model->video_tape_id = $request->video_tape_id;

                $model->user_id = $request->id;

                $model->like_status = DEFAULT_TRUE;

                $model->dislike_status = DEFAULT_FALSE;

                $model->save();

                $data['like_count'] = number_format_short($like_count+1);

                $data['dislike_count'] = number_format_short($dislike_count);

                $response_array = ['success'=>true, 'data' => $data];

            } else {

                if($model->dislike_status) {

                    $model->like_status = DEFAULT_TRUE;

                    $model->dislike_status = DEFAULT_FALSE;

                    $model->save();

                    $data['like_count'] = number_format_short($like_count+1);

                    $data['dislike_count'] = number_format_short($dislike_count-1);



                } else {

                    $model->delete();

                    $is_liked = NO;

                    $message = CommonHelper::success_message(227);

                    $data['like_count'] = number_format_short($like_count-1);

                    $data['dislike_count'] = number_format_short($dislike_count);

                    $response_array = ['success' => true, 'data' => $data];

                }

            }

            $data['is_liked'] = $is_liked;

            $response_array = ['success' => true, 'message' => $message,'data' => $data];

        }

        return response()->json($response_array);

    }

    /**
     * Dis Like Videos
     *
     * @return JSON Response
     */

    public function video_tapes_dislike(Request $request) {

        $validator = Validator::make($request->all() , [
            'video_tape_id' => 'required|exists:video_tapes,id',
        ]);

        if ($validator->fails()) {
            
            $error = implode(',', $validator->messages()->all());
            
            $response_array = array('success' => false, 'error' => Helper::get_error_message(101), 
                    'error_code' => 101, 'error'=>$error);

        } else {

            $model = LikeDislikeVideo::where('video_tape_id', $request->video_tape_id)
                    ->where('user_id',$request->id)->first();

            $like_count = LikeDislikeVideo::where('video_tape_id', $request->video_tape_id)
                ->where('like_status', DEFAULT_TRUE)
                ->count();

            $dislike_count = LikeDislikeVideo::where('video_tape_id', $request->video_tape_id)
                ->where('dislike_status', DEFAULT_TRUE)
                ->count();

            $is_disliked = YES; $message = CommonHelper::success_message(228);

            if (!$model) {

                $model = new LikeDislikeVideo;

                $model->video_tape_id = $request->video_tape_id;

                $model->user_id = $request->id;

                $model->like_status = DEFAULT_FALSE;

                $model->dislike_status = DEFAULT_TRUE;

                $model->save();

                $data['like_count'] = number_format_short($like_count);

                $data['dislike_count'] = number_format_short($dislike_count+1);

            } else {

                if($model->like_status) {

                    $model->like_status = DEFAULT_FALSE;

                    $model->dislike_status = DEFAULT_TRUE;

                    $model->save();

                    $data['like_count'] = number_format_short($like_count-1);

                    $data['dislike_count'] = number_format_short($dislike_count+1);

                } else {

                    $model->delete();

                    $is_disliked = NO;

                    $message = CommonHelper::success_message(229);

                    $data['like_count'] = number_format_short($like_count);

                    $data['dislike_count'] = number_format_short($dislike_count-1);

                }

            }

            $response_array = ['success' => true, 'message' => $message, 'data' => $data];

        }

        return response()->json($response_array);

    }

    /**
     * Function Name : categories_channels_list
     *
     * To list out all the channels which is in active status
     *
     * @created Vithya 
     *
     * @updated 
     *
     * @param Object $request - USer Details
     *
     * @return array of channel list @todo
     */
    public function categories_channels_list(Request $request) {

        try {

            $validator = Validator::make($request->all(),
                    [
                        'category_id' => 'required|exists:categories,id,status,'.CATEGORY_APPROVE_STATUS,
                    ]
            );

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);

            }

            $base_query = Channel::BaseResponse()->leftJoin('video_tapes', 'video_tapes.channel_id', '=', 'channels.id')
                    ->where('video_tapes.category_id', $request->category_id)
                    ->groupBy('video_tapes.channel_id');

            $channels = $base_query->skip($this->skip)->take($this->take)->get();

            foreach ($channels as $key => $channel_details) {

                $channel_details->no_of_videos = videos_count($channel_details->channel_id);

                $channel_details->no_of_subscribers = subscriberscnt($channel_details->channel_id);

                // check my channel and subscribe status

                $channel_details->is_my_channel = NO;

                $channel_details->is_user_subscribed_the_channel = CHANNEL_UNSUBSCRIBED;

                if($request->id) {

                    if($channel_details->user_id == $request->id) {

                        $channel_details->is_my_channel = YES;

                        $channel_details->is_user_subscribed_the_channel = CHANNEL_OWNER;

                    } else {

                        $check_channel_subscription = ChannelSubscription::where('user_id', $request->id)->where('channel_id', $channel_details->channel_id)->count();

                        $channel_details->is_user_subscribed_the_channel = $check_channel_subscription ? CHANNEL_SUBSCRIBED : CHANNEL_UNSUBSCRIBED;

                    }

                }

            }
            
            return $this->sendResponse($message = "", $code = 0, $channels);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    // @todo proper structure

    public function video_tapes_search(Request $request) {

        try {

            $validator = Validator::make($request->all(), [
                'key' => ''
            ]);

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);
            }
            
            $video_tapes = VideoHelper::video_tapes_search($request);

            return $this->sendResponse($message = "", $success_code = "", $video_tapes);


        } catch(Exception  $e) {
            
            return $this->sendError($e->getMessage(), $e->getCode());

        }
    }


    /**
     *
     * @method playlists()
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

            $validator = Validator::make($request->all(), [
                'skip' => 'numeric',
                'channel_id' => 'exists:channels,id',
                'view_type' => 'required'
            ]);

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());
                
                throw new Exception($error, 101);
                
            }

            // Guests can access only channel playlists  - Required channel_id

            // Logged in users playlists - required - Required viewer_type 

            // Logged in user access other channel playlist  - required channel_id

            // Logged in user access owned channel playlist - required channel_id

            if(($request->id && $request->view_type == VIEW_TYPE_VIEWER)|| (!$request->id && $request->view_type == VIEW_TYPE_VIEWER)) {

                if(!$request->channel_id) {

                    throw new Exception("Channel ID is required", 101); // @todo
                    

                }
                
            }

            $base_query = Playlist::where('playlists.status', APPROVED)
                                ->orderBy('playlists.updated_at', 'desc');

            // While owner access the users playlists

            if($request->view_type == VIEW_TYPE_OWNER && !$request->channel_id) {

                $base_query = $base_query->where('playlists.user_id', $request->id)->where('channel_id', 0);

            }

            // While owner access the channel playlists

            if($request->view_type == VIEW_TYPE_OWNER && $request->channel_id) {

                $base_query = $base_query->where('playlists.user_id', $request->id);

            }

            if($request->channel_id) {

                $base_query = $base_query->where('playlists.channel_id', $request->channel_id);
            }

            $skip = $this->skip ?: 0;

            $take = $this->take ?: TAKE_COUNT;

            $playlists = $base_query->CommonResponse()->skip($skip)->take($take)->get();

            foreach ($playlists as $key => $playlist_details) {

                $first_video_from_playlist = PlaylistVideo::where('playlist_videos.playlist_id', $playlist_details->playlist_id)
                                            ->leftJoin('video_tapes', 'video_tapes.id', '=', 'playlist_videos.video_tape_id')
                                            ->select('video_tapes.id as video_tape_id', 'video_tapes.default_image as picture')
                                            ->first();

                $playlist_details->picture = $first_video_from_playlist ? $first_video_from_playlist->picture : asset('images/playlist.png');

                $check_video = PlaylistVideo::where('playlist_id', $playlist_details->playlist_id)->where('video_tape_id', $request->video_tape_id)->count();


                $playlist_details->is_selected = $check_video ? YES : NO;

                 // Total Video count start

                $total_video_query = PlaylistVideo::where('playlist_id', $playlist_details->playlist_id);

                if($request->id) {

                    $flag_video_ids = flag_videos($request->id);

                    if($flag_video_ids) {

                        $playlist_details->total_videos = $total_video_query->whereNotIn('playlist_videos.video_tape_id', $flag_video_ids);

                    }

                }

                $playlist_details->total_videos = $total_video_query->count();
               
                // Total Video count end

                $playlist_details->share_link = url('/');
            
            }

            $response_array = ['success' => true, 'data' => $playlists];

            return response()->json($response_array);

        } catch(Exception $e) {

            $error = $e->getMessage();

            $code = $e->getCode();

            $response_array = ['success' => false, 'error' => $error, 'error_code' => $code];

            return response()->json($response_array);

        }
    
    }

    /**
     *
     * @method playlists_save()
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
     *
     */
    public function playlists_save(Request $request) {

        try {

            DB::beginTransaction();

            $validator =Validator::make($request->all(),[
                'title' => 'required|max:255',
                'playlist_id' => 'exists:playlists,id,user_id,'.$request->id,
                'channel_id' => 'exists:channels,id'
            ],
            [
                'exists' => Helper::get_error_message(175)
            ]);

            if($validator->fails()) {

                $error = implode(',',$validator->messages()->all());

                throw new Exception($error, 101);
                
            }

            $playlist_details = Playlist::where('id', $request->playlist_id)->first();

            $message = Helper::get_message(129);

            if(!$playlist_details) {

                $message = Helper::get_message(128);

                $playlist_details = new Playlist;
    
                $playlist_details->status = APPROVED;

                $playlist_details->playlist_display_type = $request->playlist_display_type ?: PLAYLIST_DISPLAY_PRIVATE;

                $playlist_details->playlist_type = $request->playlist_type ?: PLAYLIST_TYPE_USER;

                // $playlist_details->playlist_display_type = PLAYLIST_DISPLAY_PRIVATE;

                // $playlist_details->playlist_type = PLAYLIST_TYPE_USER;

            }

            $playlist_details->user_id = $request->id;

            $playlist_details->channel_id = $request->channel_id ?: "";

            $playlist_details->title = $playlist_details->description = $request->title ?: "";

            if($playlist_details->save()) {

                DB::commit();

                $playlist_details = $playlist_details->where('id', $playlist_details->id)->CommonResponse()->first();

                $response_array = ['success' => true, 'message' => $message, 'data' => $playlist_details];

                return response()->json($response_array, 200);

            } else {

                throw new Exception(Helper::get_error_message(179), 179);

            }

        } catch(Exception $e) {

            DB::rollback();

            $error = $e->getMessage();

            $code = $e->getCode();

            $response_array = ['success' => false, 'error' => $error, 'error_code' => $code];

            return response()->json($response_array);

        }
    
    }

    /**
     *
     * @method playlists_add_video()
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

            $validator = Validator::make($request->all(),[
                'playlist_id' => 'required',
                'video_tape_id' => 'required|exists:video_tapes,id,status,'.APPROVED,
            ]);

            if($validator->fails()) {

                $error = implode(',',$validator->messages()->all());

                throw new Exception($error, 101);
                
            }

            // check the video added in spams (For Viewer)

            if(!$request->channel_id && $request->id) {

                $flagged_videos = getFlagVideos($request->id);

                if(in_array($request->video_tape_id, $flagged_videos)) {

                    throw new Exception(tr('video_in_spam_list'), 101);
                    
                }
            }

            // Spam check end

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

            DB::commit();

            $code = $total_playlists_update > 0 ? 126 : 132;

            $message = Helper::get_message($code);

            $response_array = ['success' => true, 'message' => $message, 'code' => $code];

            return response()->json($response_array);

        } catch(Exception $e) {

            DB::rollback();

            $error = $e->getMessage();

            $error_code = $e->getCode();

            $response_array = ['success' => false, 'error' => $error, 'error_code' => $error_code];

            return response()->json($response_array);

        }
    
    }


    /**
     *
     * @method playlists_view()
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

    public function playlists_view(Request $request) {

        try {

            // Check the playlist record based on the view type

            $playlist_base_query = Playlist::where('playlists.status', APPROVED)
                                ->where('playlists.id', $request->playlist_id);

            // check the playlist belongs to owner

            if($request->view_type == VIEW_TYPE_OWNER) {

                $playlist_base_query = $playlist_base_query->where('playlists.user_id', $request->id);

            }

            $playlist_details = $playlist_base_query->CommonResponse()->first();

            if(!$playlist_details) {

                throw new Exception(Helper::get_error_message(175), 175);
                
            }

            $skip = $this->skip ?: 0; $take = $this->take ?: TAKE_COUNT;

            $video_tape_base_query = PlaylistVideo::where('playlist_videos.playlist_id', $request->playlist_id);


            // Check the flag videos

            if($request->id) {

                // Check any flagged videos are present
                $flagged_videos = getFlagVideos($request->id);

                if($flagged_videos) {

                    $video_tape_base_query->whereNotIn('playlist_videos.video_tape_id', $flagged_videos);

                }

            }

            $video_tape_ids = $video_tape_base_query->skip($skip)
                                ->take($take)
                                ->pluck('playlist_videos.video_tape_id')
                                ->toArray();
            
            $video_tapes = V5Repo::video_list_response($video_tape_ids, $request->id);
           
            $playlist_details->picture = asset('images/playlist.png');

            $playlist_details->share_link = url('/');

            $playlist_details->is_my_channel = NO;

            if($playlist_details->channel_id) {

                if($channel_details = Channel::find($playlist_details->channel_id)) {

                    $playlist_details->is_my_channel = $request->id == $channel_details->user_id ? YES : NO;
                }
            }

            $playlist_details->total_videos = count($video_tapes);

            $playlist_details->video_tapes = $video_tapes;

            $data = $playlist_details;

            $data['video_tapes'] = $video_tapes;

            $response_array = ['success' => true, 'data' => $data];

            return response()->json($response_array);

        } catch(Exception $e) {

            $error = $e->getMessage();

            $error_code = $e->getCode();

            $response_array = ['success' => false, 'error' => $error, 'error_code' => $error_code];

            return response()->json($response_array);

        }
    
    }


    /**
     *
     * @method playlists_video_remove()
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

            DB::beginTransaction();

            $validator = Validator::make($request->all(),[
                    'playlist_id' =>'required|exists:playlists,id',
                    'video_tape_id' => 'required|exists:video_tapes,id',
                ],
                [
                    'exists' => 'The :attribute doesn\'t exists please add to playlist',
                ]
            );

            if ($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);
                
            }

            $playlist_video_details = PlaylistVideo::where('playlist_id',$request->playlist_id)->where('user_id', $request->id)->where('video_tape_id',$request->video_tape_id)->first();

            if(!$playlist_video_details) {

                throw new Exception(Helper::get_error_message(180), 180);

            }

            $playlist_video_details->delete();

            DB::commit();

            $response_array = ['success' => true, 'message' => Helper::get_message(127), 'code' => 127];

            return response()->json($response_array, 200);

        } catch(Exception $e) {

            $error = $e->getMessage();

            $error_code = $e->getCode();

            $response_array = ['success' => false, 'error' => $error, 'error_code' => $error_code];

            return response()->json($response_array);

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

            DB::beginTransaction();

            $validator = Validator::make($request->all(),[
                    'playlist_id' =>'required|exists:playlists,id',
                ],
                [
                    'exists' => 'The :attribute doesn\'t exists please add to playlist',
                ]
            );

            if ($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);
                
            }

            $playlist_details = Playlist::where('id',$request->playlist_id)->where('user_id', $request->id)->first();

            if(!$playlist_details) {

                throw new Exception(Helper::get_error_message(180), 180);

            }

            $playlist_details->delete();

            DB::commit();

            $response_array = ['success' => true, 'message' => Helper::get_message(131), 'code' => 131];

            return response()->json($response_array, 200);

        } catch(Exception $e) {

            DB::rollback();

            $error = $e->getMessage();

            $error_code = $e->getCode();

            $response_array = ['success' => false, 'error' => $error, 'error_code' => $error_code];

            return response()->json($response_array);

        }

    }
}
