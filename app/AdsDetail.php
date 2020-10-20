<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdsDetail extends Model
{
    //

    public function getAssignedVideo() {

         return $this->hasMany('App\AssignVideoAd', 'ad_id', 'id');

    }
}
