<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserHistory extends Model
{
    public function videoTape() {
        return $this->belongsTo('App\VideoTape')
        		->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
        		->videoResponse();
    }

}
