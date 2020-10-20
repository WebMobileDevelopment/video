<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Helpers\Helper;

class Channel extends Model
{
    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeBaseResponse($query) {

        return $query->leftJoin('users', 'channels.user_id', '=', 'users.id')
                    ->select('channels.id as channel_id', 
                        'channels.user_id as user_id', 
                        'channels.name as channel_name', 
                        'channels.description as channel_description', 
                        'channels.picture as channel_image', 
                        'channels.cover as channel_cover', 
                        'users.name as channel_owner_name',
                        'users.picture as channel_owner_picture',
                        'channels.is_approved as is_admin_approved', 
                        'channels.status as channel_status', 
                        'channels.created_at', 
                        'channels.updated_at'
                    );

    }

    public function videoTape() {
        return $this->hasMany('App\VideoTape')->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')->videoResponse();
    }

    /**
     * Get the video record associated with the flag.
     */
    public function getUser()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
    
    /**
     * Save the unique ID 
     *
     *
     */
    public function setUniqueIdAttribute($value){

        $this->attributes['unique_id'] = uniqid(str_replace(' ', '-', $value));

    }

    public function getVideos() {
        return $this->hasMany('App\VideoTape')->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')->videoResponse()->where('status', DEFAULT_TRUE)->where('is_approved', DEFAULT_TRUE);
    }

    public function getVideoTape() {
        return $this->hasMany('App\VideoTape');
    }

    public function getChannelSubscribers() {
        return $this->hasMany('App\ChannelSubscription');
    }

    public function getPlaylist() {
        return $this->hasMany('App\Playlist');
    }

    public function getBellNotifications() {

         return $this->hasMany('App\BellNotification', 'channel_id', 'id');

    }

    /**
     * Boot function for using with User Events
     *
     * @return void
     */

    public static function boot()
    {
        //execute the parent's boot method 
        parent::boot();

        //delete your related models here, for example
        static::deleting(function($model)
        {

            if($model->picture) {
                Helper::delete_picture($model->picture, "/uploads/channels/picture/");
            }

            if($model->cover) {
                Helper::delete_picture($model->cover, "/uploads/channels/cover/");
            }

            if (count($model->getVideoTape) > 0) {

                foreach ($model->getVideoTape as $key => $value) {

                    Helper::delete_picture($value->video, "/uploads/videos/");

                    Helper::delete_picture($value->subtitle, "/uploads/subtitles/"); 

                    if ($value->banner_image) {

                        Helper::delete_picture($value->banner_image, "/uploads/images/");
                    }

                    Helper::delete_picture($value->default_image, "/uploads/images/");

                    if ($value->video_path) {

                        $explode = explode(',', $value->video_path);

                        if (count($explode) > 0) {

                            foreach ($explode as $key => $exp) {

                                Helper::delete_picture($exp, "/uploads/videos/");

                            }
                        }
                    }

                   $value->delete();    

                }

            }

            if (count($model->getChannelSubscribers) > 0) {

                foreach ($model->getChannelSubscribers as $key => $value) {

                   $value->delete();    

                }

            }

            if (count($model->getPlaylist) > 0) {

                foreach ($model->getPlaylist as $key => $playlist) {

                   $playlist->delete();    
                }
            }

            if (count($model->getBellNotifications) > 0) {

                foreach ($model->getBellNotifications as $key => $value) {

                   $value->delete();    

                }            

            }


        }); 

    }

}
