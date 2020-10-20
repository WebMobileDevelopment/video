<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LiveVideo extends Model
{
    //

    public function setUniqueIdAttribute($value){

		$this->attributes['unique_id'] = uniqid(str_replace(' ', '-', $value));

	}


	public function payments() {

		return $this->hasMany('App\LiveVideoPayment');
		
	}

	public function viewers() {

		return $this->hasMany('App\Viewer');
		
	}

	public function user() {

		return $this->belongsTo('App\User');
		
	}

	public function channel() {

		return $this->belongsTo('App\Channel');
		
	}


	    /**
     * Boot function for using with User Events
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

         //delete your related models here, for example
        static::deleting(function($model)
        {
        	if (count($model->viewers) > 0) {

                foreach($model->viewers as $viewer)
                {
                    $viewer->delete();
                } 

            }

            if (count($model->payments) > 0) {

                foreach($model->payments as $video)
                {
                    $video->delete();
                } 

            }

        });
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVideoResponse($query) {

        return $query->select(
                 'users.id as id',
                 'users.name as name', 
                 'users.email as email',
                 'channels.name as channel_name', 
                 'channels.picture as channel_image',
                 'channels.id as channel_id',
                 'users.picture as user_picture',
                 'users.chat_picture as chat_picture',
                 'live_videos.id as video_id',
                 'live_videos.title as title',
                 'live_videos.unique_id as unique_id',
                 'live_videos.channel_id as channel_id',
                 'live_videos.type as type',
                 'live_videos.description as description',
                 'live_videos.amount as amount',
                 'live_videos.snapshot as snapshot',
                 'live_videos.viewer_cnt as viewers',
                 'live_videos.no_of_minutes as no_of_minutes',
                 'live_videos.payment_status as payment_status',
                 'live_videos.status as video_stopped_status',
                 'live_videos.created_at',
                \DB::raw('DATE_FORMAT(live_videos.created_at , "%e %b %y") as date')
            );
    }

}

