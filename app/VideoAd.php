<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VideoAd extends Model
{
    //

    public function getVideoTape() {

        return $this->hasOne('App\VideoTape', 'id', 'video_tape_id');

    }

    public function getAd() {

        return $this->hasOne('App\AdsDetail', 'id', 'ad_id');

    }


    public function getAssignAds() {

        return $this->hasMany('App\AssignVideoAd', 'video_ad_id', 'id')->orderBy('video_time', 'asc');

    }



    public function getPreAdDetail() {

        return $this->hasOne('App\AssignVideoAd', 'video_ad_id', 'id')->where('ad_type', PRE_AD);

    }

    public function getPostAdDetail() {

        return $this->hasOne('App\AssignVideoAd', 'video_ad_id', 'id')->where('ad_type', POST_AD);

    }

    public function getBetweenAdDetails() {

        return $this->hasMany('App\AssignVideoAd', 'video_ad_id', 'id')->where('ad_type', BETWEEN_AD);

    }

    public function toArray()
    {
        $array = parent::toArray();

        $array['ad_details'] = $this->getAssignAds;

        $array['post_ad'] = $this->getPostAdDetail;

        $array['pre_ad'] = $this->getPreAdDetail;

        $array['between_ad'] = $this->getBetweenAdDetails;

        $array['ads_types'] = getTypeOfAds($this->types_of_ad);

        return $array;
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
            if (count($model->getAssignAds) > 0) {

                foreach ($model->getAssignAds as $key => $value) {

                   $value->delete();    

                }               
             
            }

        }); 

    }

}
