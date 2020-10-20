<?php

namespace App\Jobs;

use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Jobs\Job;
use App\Helpers\Helper;
use App\User;
use App\ChannelSubscription;
use Setting;
use Log;

use App\Repositories\PushNotificationRepository as PushRepo;

class sendPushNotification extends Job implements ShouldQueue {

    use InteractsWithQueue, SerializesModels;

    protected $user_id;
    protected $title;
    protected $content;
    protected $push_redirect_type;
    protected $video_tape_id;
    protected $channel_id;
    protected $push_data;
    protected $push_all_type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_id = PUSH_TO_ALL , $title, $content, $push_redirect_type = PUSH_REDIRECT_HOME , $video_tape_id = 0 , $channel_id = 0 , $push_data = [] , $push_all_type = PUSH_TO_ALL) {

        $this->user_id = $user_id;
        $this->title = $title;
        $this->content = $content;
        $this->push_redirect_type = $push_redirect_type;
        $this->video_tape_id = $video_tape_id;
        $this->channel_id = $channel_id;
        $this->push_data = $push_data;
        $this->push_all_type = $push_all_type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        if($this->user_id == PUSH_TO_ALL) {
            
            if($this->push_all_type == PUSH_TO_CHANNEL_SUBSCRIBERS) {

                $channel_subscriptions = ChannelSubscription::leftJoin('users', 'users.id', '=', 'channel_subscriptions.user_id')
                    ->where('channel_subscriptions.channel_id', $this->channel_id)
                    ->where('users.push_status',YES)
                    ->where('users.device_token','!=' ,'')
                    ->get();
                
                foreach ($channel_subscriptions as $key => $subscriber) {

                    if($subscriber->getUser) {

                        $user_details = $subscriber->getUser;

                        PushRepo::push_notification($user_details->device_token, $this->title, $this->content, $this->push_data, $user_details->device_type);

                    }
       
                }
            } else {

                $android_register_ids = User::where('status' , APPROVED)->where('device_token' , '!=' , "")->where('device_type' , DEVICE_ANDROID)->where('push_status' , YES)->pluck('device_token')->toArray();

                PushRepo::push_notification_andriod($android_register_ids , $this->title, $this->content, $this->push_data);

                $ios_register_ids = User::where('status' , APPROVED)->where('device_type' , DEVICE_IOS)->where('push_status' , YES)->pluck('device_token')->toArray();

                PushRepo::push_notification_ios($ios_register_ids , $this->title, $this->content, $this->push_data);

            }

        } else {

            $user_details = User::where('id',$this->user_id)
                    ->where('push_status' , YES)
                    ->where('device_token' , '!=' , "")
                    ->first();

            if($user_details) { 

                PushRepo::push_notification($user_details->device_token, $this->title, $this->content, $this->push_data, $user_details->device_type);

            } else {
              
                Log::info('Push notifictaion is not enabled. Please contact admin');
            }
           
        }
    }
}
