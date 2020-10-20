<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Helpers\Helper;

use Log; 

use Setting;

use App\User;

use App\Channel;

use App\ChannelSubscription;

use App\VideoTape;

use App\BellNotification;

use App\BellNotificationTemplate;

class BellNotificationJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Check the video is added in spam

        Log::info("BellNotification Job Started");

        $template_details = BellNotificationTemplate::where('type', $this->data->notification_type)->first();

        $message = "new notification";

        if($template_details) {

            Log::info("BellNotification - template_details");

            $channel_details = $this->data->channel_id ? Channel::find($this->data->channel_id): [];

            $video_tape_details = isset($this->data->video_tape_id) ? VideoTape::find($this->data->video_tape_id) : [];

            $user_details = User::find($this->data->from_user_id);

            $channel_name = $channel_details ? $channel_details->name : "";

            $video_title = $video_tape_details ? $video_tape_details->title : "";

            $username = $user_details ? $user_details->name : "";

            $replacers = [
                '{username}' => $username,
                '{channel_name}' => $channel_name,
                '{video_title}' => $video_title
            ];

            $message = strtr($template_details->message, $replacers);

        }

        $user_ids = [$this->data->to_user_id];

        if($this->data->notification_type == BELL_NOTIFICATION_NEW_VIDEO) {

            $user_ids = ChannelSubscription::where('channel_id', $this->data->channel_id)->pluck('user_id');

        }

        foreach ($user_ids as $key => $to_user_id) {
            
            $bell_notification = New BellNotification;

            $bell_notification->from_user_id = $this->data->from_user_id;

            $bell_notification->to_user_id = $to_user_id;

            $bell_notification->notification_type = $this->data->notification_type;

            $bell_notification->message = $message;

            $bell_notification->channel_id = $this->data->channel_id;

            $bell_notification->video_tape_id = isset($this->data->video_tape_id) ? $this->data->video_tape_id : 0;

            $bell_notification->status = BELL_NOTIFICATION_STATUS_UNREAD;

            $bell_notification->save();
        }

        Log::info("BellNotification - END");

    }
}
