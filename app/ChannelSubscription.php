<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChannelSubscription extends Model
{
    //

    public function getChannel()
    {
        return $this->hasOne('App\Channel', 'id', 'channel_id');
    }

    public function getUser()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
}
