<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignVideoAd extends Model
{
    //

    public function getAds() {

         return $this->hasOne('App\AdsDetail', 'id', 'ad_id');

    }

    public function getVideoAds() {

         return $this->hasMany('App\VideoAd', 'id', 'video_ad_id');

    }

    public function videoAd() {

         return $this->hasOne('App\VideoAd', 'id', 'video_ad_id');

    }


    public function toArray()
    {
        $array = parent::toArray();

        $array['assigned_ad'] = $this->getAds;
        
        return $array;
    }

}
