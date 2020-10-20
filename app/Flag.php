<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Flag extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'video_tape_id', 'reason', 'status'
    ];

    /**
     * Get the video record associated with the flag.
     */
    public function videoTape()
    {
        return $this->hasOne('App\VideoTape', 'id', 'video_tape_id')->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')->videoResponse();
    }

    /**
     * Get the video record associated with the flag.
     */
    public function userVideos()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function toArray()
    {
        $array = parent::toArray();

        $array['video_tape'] = $this->videoTape;
        
        return $array;
    }

}
