<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    //

        /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'live_video_id', 'live_video_viewer_id', 'message' , 'type'
    ];

    /**
     * Load viewers using relation model
     */
    public function getViewUser()
    {
        return $this->belongsTo('App\User', 'live_video_viewer_id');
    }

    /**
     * Load viewers using relation model
     */
    public function getUser()
    {
        return $this->belongsTo('App\User', 'user_id');
    }


}
