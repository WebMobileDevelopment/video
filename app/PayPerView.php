<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PayPerView extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'video_id', 'payment_id', 'amount', 'expiry_date', 'status'
    ];

    /**
     * Get the video record associated with the flag.
     */
    public function videoTape()
    {
        return $this->hasOne('App\VideoTape', 'id', 'video_id');
    }

    /**
     * Get the video record associated with the flag.
     */
    public function userDetails()
    {
        return $this->belongsTo('App\User','id');
    }


    /**
     * Get the video record associated with the flag.
     */
    public function videoTapeDetails()
    {
        return $this->belongsTo('App\VideoTape', 'video_id');
    }

    /**
     * Get the video record associated with the flag.
     */
    public function videoTapeResponse()
    {
        return $this->hasOne('App\VideoTape', 'id', 'video_id')
            ->leftJoin('channels' ,'video_tapes.channel_id' , '=' , 'channels.id')
            ->videoResponse();
    }

    /**
     * Get the video record associated with the flag.
     */
    public function userVideos()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }


    /**
     * Get the video record associated with the flag.
     */
    public function video()
    {
        return $this->belongsTo('App\VideoTape', 'video_tape_id');
    }
}
