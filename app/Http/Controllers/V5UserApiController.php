<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Repositories\V5Repository as V5Repo;

use App\Helpers\Helper;

use Exception, DB, Validator, Setting, Log;

use App\User, App\Card, App\Wishlist;

use App\VideoTape, App\VideoTapeTag;

use App\Channel, App\ChannelSubscription;


class V5UserApiController extends Controller
{
    protected $skip, $take;

	public function __construct(Request $request) {

        Log::info(url()->current());   

        Log::info("Request Data".print_r($request->all(), true));
        
        $this->middleware('UserApiVal', ['except' => ['channels_index', 'channels_view', 'channel_based_videos']]);

        $this->middleware('ChannelOwner' , ['only' => ['video_tapes_status', 'video_tapes_delete', 'video_tapes_ppv_status','video_tapes_publish_status']]);

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

    }

    /**
     * @method cards_add()
     *
     * @uses Update the selected payment mode 
     *
     * @created Vidhya R
     *
     * @updated vithya R
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

                throw new Exception(Helper::get_error_message(50108), 50108);
            }
        
            $validator = Validator::make(
                    $request->all(),
                    [
                        'card_token' => 'required',
                    ]
                );

            if ($validator->fails()) {

                $error = implode(',',$validator->messages()->all());
             
                throw new Exception($error , 101);

            } 

            Log::info("INSIDE CARDS ADD");

            $user_details = User::find($request->id);

            if(!$user_details) {

                throw new Exception(Helper::get_error_message(9001), 9001);
                
            }

            // Get the key from settings table
            
            $customer = \Stripe\Customer::create([
                    "card" => $request->card_token,
                    "email" => $user_details->email,
                    "description" => "Customer for ".Setting::get('site_name', 'ST'),
                ]);

            if(!$customer) {
                
                throw new Exception(Helper::get_error_message(117) , 117);

            }

            $customer_id = $customer->id;

            $card_details = new Card;

            $card_details->user_id = $request->id;

            $card_details->customer_id = $customer_id;

            $card_details->card_token = $customer->sources->data ? $customer->sources->data[0]->id : "";

            $card_details->card_name = $customer->sources->data ? $customer->sources->data[0]->brand : "";

            $card_details->last_four = $customer->sources->data? $customer->sources->data[0]->last4 : "";

            $card_details->month = $customer->sources->data ? $customer->sources->data[0]->exp_month : "";

            $card_details->year = $customer->sources->data ? $customer->sources->data[0]->exp_year : "";

            // Check is any default is available

            $check_card_details = Card::where('user_id',$request->id)->count();

            $card_details->is_default = $check_card_details ? 0 : 1;

            if($card_details->save()) {

                if($user_details) {

                    $user_details->card_id = $check_card_details ? $user_details->card_id : $card_details->id;

                    $user_details->save();
                }

                $data = Card::where('id' , $card_details->id)->select('id as card_id' , 'customer_id' , 'last_four' ,'card_name', 'card_token' , 'is_default' )->first();

                DB::commit();

                return $this->sendResponse($message = Helper::get_message(50007), $code = 50007, $data);

            } else {

                throw new Exception(Helper::get_error_message(117), 117);
                
            }
       
        } catch(Stripe_CardError | \Stripe\StripeInvalidRequestError | Stripe_AuthenticationError | Stripe_ApiConnectionError | Stripe_Error $e) {

            Log::info("error1");

            $error1 = $e->getMessage();

            $response_array = ['success' => false , 'error' => $error1 ,'error_code' => 903];

            return response()->json($response_array , 200);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
   
    }

    /**
     * @method channels_list_for_owners()
     *
     * @uses Get the user channels
     *
     * @created Vidhya R
     *
     * @updated vithya R
     *
     * @param Form data
     * 
     * @return JSON Response
     */

    public function channels_index(Request $request) {

        try {

            $base_query = Channel::BaseResponse();

            if($request->view_type == VIEW_TYPE_OWNER) {

                $base_query = $base_query->where('channels.user_id', $request->id);

            }

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

    /**
     * @method channels_view()
     *
     * @uses used to get the channel details
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param integer channel_id
     * 
     * @return json response
     */
    public function channels_view(Request $request) {

        try {

            $validator = Validator::make($request->all(),
                [
                    'channel_id' => 'required|integer|exists:channels,id',
                    'view_type' => 'required'
                ],
                [
                    'exists' => 'The :attribute doesn\'t exists',
                ]
            );

            if ($validator->fails()) {

                $error_messages = implode(',', $validator->messages()->all());

                throw new Exception($error_messages, 101);
                
            }

            $base_query = Channel::BaseResponse()->where('channels.id', $request->channel_id);

            if($request->view_type == VIEW_TYPE_VIEWER) {

                $base_query = $base_query->where('channels.status', USER_CHANNEL_APPROVED)->where('channels.is_approved', ADMIN_CHANNEL_APPROVED);

            }

            $channel_details = $base_query->first();

            if(!$channel_details) {

                throw new Exception(Helper::get_error_message(50102), 50102);
            }

            $data = new \stdClass();

            $channel_details->name = $channel_details->channel_name;

            $channel_details->description = $channel_details->channel_description;
            
            $channel_details->image = $channel_details->channel_image;

            $channel_details->cover = $channel_details->channel_cover;

            $channel_details->no_of_videos = videos_count($request->channel_id);

            $channel_details->no_of_subscribers = subscriberscnt($request->channel_id);

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

            $data->details = $channel_details;

            // Videos with skip and take

            $video_tape_base_query = VideoTape::where('video_tapes.channel_id', $request->channel_id);

            // Check flag videos

            if ($request->id) {

                // Check any flagged videos are present
                $flagged_videos = getFlagVideos($request->id);

                if($flagged_videos) {

                    $video_tape_base_query->whereNotIn('video_tapes.id', $flagged_videos);

                }

            }

            $video_tape_ids = $video_tape_base_query->skip($this->skip)->take($this->take)->pluck('video_tapes.id')->toArray();

            $video_tapes = V5Repo::video_list_response($video_tape_ids, $request->id);

            $data->video_tapes = $video_tapes;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch (Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method channel_based_videos()
     *
     * @uses used to get the videos list based on the selected channel id
     *
     * @created Vithya R
     * 
     * @updated Vithya R
     *
     * @param integer $channel_id
     *
     * @return json response
     */
    
    public function channel_based_videos(Request $request) {

        try {

            $validator = Validator::make($request->all(),
                [
                    'channel_id' => 'required|integer|exists:channels,id',
                ],
                [
                    'exists' => 'The :attribute doesn\'t exists',
                ]
            );

            if ($validator->fails()) {

                $error_messages = implode(',', $validator->messages()->all());

                throw new Exception($error_messages, 101);
                
            }

            $channel_details = Channel::where('channels.id', $request->channel_id)->first();

            if(!$channel_details) {

                throw new Exception(Helper::get_error_message(50102), 50102);
            }

            $data = new \stdClass();

            // Videos with skip and take

            $video_tape_base_query = VideoTape::where('video_tapes.channel_id', $request->channel_id);

            // Check flag videos

            if ($request->id) {

                // Check any flagged videos are present
                $flagged_videos = getFlagVideos($request->id);

                if($flagged_videos) {

                    $video_tape_base_query->whereNotIn('video_tapes.id', $flagged_videos);

                }

            }

            $video_tape_ids = $video_tape_base_query->skip($this->skip)->take($this->take)->pluck('video_tapes.id')->toArray();

            $video_tapes = V5Repo::video_list_response($video_tape_ids, $request->id, $orderby = 'video_tapes.updated_at', $other_select_columns = "", $is_random_order = "", $is_owner = $request->view_type == VIEW_TYPE_OWNER ? YES : NO);

            // $data->video_tapes = $video_tapes;
            $data = $video_tapes;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch (Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /**
     * @method video_tapes_view()
     *
     * @uses used to get the channel details
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param integer video_tape_id
     * 
     * @return json response
     */
    public function video_tapes_view(Request $request) {

        try {

            $validator = Validator::make($request->all(),
                [
                    'video_tape_id' => 'required|integer|exists:video_tapes,id',
                ],
                [
                    'exists' => 'The :attribute doesn\'t exists',
                ]
            );

            if ($validator->fails()) {

                $error_messages = implode(',', $validator->messages()->all());

                throw new Exception($error_messages, 906);
                
            }

            $data = array();

            $video_tape_details = VideoTape::where('id', $request->video_tape_id)
                                    ->where('user_id', $request->id)
                                    ->where('status', APPROVED)
                                    ->select('id as video_tape_id', 'title', 'description', 'default_image', 'age_limit', 'duration', 'video_publish_type', 'publish_status', 'publish_time', 'is_approved as is_admin_approved', 'status as video_status', 'watch_count', 'is_pay_per_view', 'type_of_subscription', 'ppv_amount', 'category_name','video_type', 'channel_id', 'user_ppv_amount as ppv_revenue', 'amount as ads_revenue', 'category_id')
                                    ->first();

            if(!$video_tape_details) {

                throw new Exception(Helper::get_error_message(906), 906);
            }

            $video_tape_details->video_publish_type_text = $video_tape_details->video_publish_type == PUBLISH_NOW ? tr('PUBLISH_NOW') : tr('PUBLISH_LATER');

            $video_types = [VIDEO_TYPE_UPLOAD => tr('VIDEO_TYPE_UPLOAD'), VIDEO_TYPE_LIVE => tr('VIDEO_TYPE_LIVE'), VIDEO_TYPE_YOUTUBE => tr('VIDEO_TYPE_YOUTUBE'), VIDEO_TYPE_OTHERS => tr('VIDEO_TYPE_OTHERS')];

            $video_tape_details->video_type_text = $video_types[$video_tape_details->video_type];

            $video_tape_details->total_revenue = $video_tape_details->ads_revenue + $video_tape_details->ppv_revenue;

            $channel_details = Channel::find($video_tape_details->channel_id);

            $video_tape_details->channel_name = $channel_details ? $channel_details->name: "";

            $video_tape_details->tags = VideoTapeTag::select('tag_id', 'tags.name as tag_name')
                                            ->leftJoin('tags', 'tags.id', '=', 'video_tape_tags.tag_id')
                                            ->where('video_tape_tags.status', TAG_APPROVE_STATUS)
                                            ->where('video_tape_id', $request->video_tape_id)
                                            ->get()->toArray();

            $video_tape_details->wishlist_count = get_wishlist_count($request->video_tape_id);
            
            $response_array = ['success' => true, 'data' => $video_tape_details];
    
            return response()->json($response_array, 200);

        } catch (Exception $e) {

            $error_messages = $e->getMessage(); $error_code = $e->getCode();

            $response_array = ['success' => false, 'error_messages' => $error_messages, 'error_code' => $error_code];

            return response()->json($response_array, 200);
        }

    }

    /**
     * @method subscribed_channels
     *
     * @uses list of channels subscribed by the loggedin user
     * 
     * @created vithya R
     *
     * @updated vithya R
     * 
     * @param Object $request - Subscribed plan Details
     *
     * @return array of channel subscribed plans
     */
    
    public function channels_subscribed(Request $request) {

        try {

            $validator = Validator::make($request->all(),
                [
                    'skip' => 'required',
                ]
            );

            if ($validator->fails()) {

                $error_messages = implode(',', $validator->messages()->all());

                throw new Exception($error_messages, 906);
                
            }


            $subscribed_channel_ids = ChannelSubscription::where('user_id', $request->id)->pluck('channel_id')->toArray();

            $base_query = Channel::whereIn('channels.id', $subscribed_channel_ids)->BaseResponse();

            $channels = $base_query->skip($this->skip)->take($this->take)->get();

            foreach ($channels as $key => $channel_details) {

                $channel_details->no_of_videos = videos_count($channel_details->channel_id);

                $channel_details->no_of_subscribers = $channel_details->getChannelSubscribers()->count();

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

        } catch (Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }
}
