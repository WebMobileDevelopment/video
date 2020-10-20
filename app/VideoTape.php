<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Auth;

use App\Helpers\Helper;

use App\Category;

use Setting;

class VideoTape extends Model
{

    public $channel_details;
    
    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeVideoResponse($query) {

        return $query->select(
            'video_tapes.id as video_tape_id' ,
            'channels.id as channel_id' ,
            'channels.user_id as channel_created_by',
            'channels.name as channel_name',
            'channels.picture as channel_picture',
            'channels.status as channel_status',
            'channels.is_approved as channel_approved_status',
            'channels.status as channel_status',
            'video_tapes.title',
            'video_tapes.description',
            'video_tapes.default_image',
            'video_tapes.created_at',
            'video_tapes.video',
            'video_tapes.is_approved',
            'video_tapes.status',
            'video_tapes.watch_count',
            'video_tapes.unique_id',
            'video_tapes.duration',
            'video_tapes.video_publish_type',
            'video_tapes.publish_status',
            'video_tapes.publish_time',
            'video_tapes.compress_status',
            'video_tapes.ad_status',
            'video_tapes.reviews',
            'video_tapes.amount',
            'video_tapes.is_banner',
            'video_tapes.banner_image',
            'video_tapes.redeem_count',
            'video_tapes.video_resolutions',
            'video_tapes.video_path',
            'video_tapes.created_at as video_created_time',
            'video_tapes.subtitle',
            'video_tapes.age_limit',
            'video_tapes.user_ratings',
            'video_tapes.video_type',
            'video_tapes.type_of_user',
            'video_tapes.type_of_subscription',
            'video_tapes.ppv_amount',
            'video_tapes.admin_ppv_amount',
            'video_tapes.user_ppv_amount',
            'video_tapes.category_id',
            'video_tapes.category_name',
            'video_tapes.is_pay_per_view',
            'video_tapes.video_type',
            'video_tapes.user_id',
            \DB::raw('DATE_FORMAT(video_tapes.created_at , "%e %b %y") as video_date'),
            \DB::raw('(CASE WHEN (user_ratings = 0) THEN ratings ELSE user_ratings END) as ratings')
        );
    
    }

    public function scopeVerifiedVideo($query) {

         return $query
                ->where('video_tapes.status' , USER_VIDEO_APPROVED)
                ->where('video_tapes.publish_status' , VIDEO_PUBLISHED)
                ->where('video_tapes.is_approved' , ADMIN_VIDEO_APPROVED);

    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeShortVideoResponse($query) {

        return $query
            ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
            ->where('video_tapes.status' , USER_VIDEO_APPROVED_STATUS)
            ->where('video_tapes.publish_status' , VIDEO_PUBLISHED)
            ->where('video_tapes.is_approved' , ADMIN_VIDEO_APPROVED_STATUS)
            ->select(
                'video_tapes.id as video_tape_id' ,
                'video_tapes.title',
                'video_tapes.video',
                'video_tapes.description',
                'video_tapes.default_image as video_image',
                'video_tapes.watch_count',
                'video_tapes.duration',
                'channels.id as channel_id' ,
                'channels.name as channel_name',
                'channels.status as channel_status',
                'channels.picture as channel_image',
                'video_tapes.category_id',
                'video_tapes.age_limit',
                'video_tapes.type_of_user',
                'video_tapes.type_of_subscription',
                'video_tapes.is_pay_per_view',
                'video_tapes.ppv_amount',
                'video_tapes.is_approved as is_admin_approved',
                'video_tapes.status as video_status',
                'video_publish_type',
                'publish_status',
                'publish_time',
                'video_tapes.video_type',
                \DB::raw('DATE_FORMAT(video_tapes.created_at , "%e %b %y") as created'),
                \DB::raw('DATE_FORMAT(video_tapes.updated_at , "%e %b %y") as updated'),
                \DB::raw('(CASE WHEN (user_ratings = 0) THEN ratings ELSE user_ratings END) as ratings')
            
            );
    
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeOwnerShortVideoResponse($query) {

        return $query
            ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
            // ->where('video_tapes.status' , USER_VIDEO_APPROVED_STATUS)
            // ->where('video_tapes.publish_status' , VIDEO_PUBLISHED)
            // ->where('video_tapes.is_approved' , ADMIN_VIDEO_APPROVED_STATUS)
            ->select(
                'video_tapes.id as video_tape_id' ,
                'video_tapes.title',
                'video_tapes.video',
                'video_tapes.description',
                'video_tapes.default_image as video_image',
                'video_tapes.watch_count',
                'video_tapes.duration',
                'channels.id as channel_id' ,
                'channels.name as channel_name',
                'channels.status as channel_status',
                'channels.picture as channel_image',
                'video_tapes.age_limit',
                'video_tapes.type_of_user',
                'video_tapes.type_of_subscription',
                'video_tapes.is_pay_per_view',
                'video_tapes.ppv_amount',
                'video_tapes.is_approved as is_admin_approved',
                'video_tapes.status as video_status',
                'video_tapes.publish_time',
                'video_publish_type',
                'publish_status',
                \DB::raw('DATE_FORMAT(video_tapes.created_at , "%e %b %y") as created'),
                \DB::raw('DATE_FORMAT(video_tapes.updated_at , "%e %b %y") as updated'),
                \DB::raw('(CASE WHEN (user_ratings = 0) THEN ratings ELSE user_ratings END) as ratings')
            
            );
    
    }

    public function setUniqueIdAttribute($value){

        $this->attributes['unique_id'] = uniqid(str_replace(' ', '-', $value));

    }

    public function userHistory()
    {
        return $this->hasMany('App\UserHistory', 'video_tape_id', 'id');
    }

    public function userWishlist()
    {
        return $this->hasMany('App\Wishlist', 'video_tape_id', 'id');
    }

    public function getUserRatings() {

         return $this->hasMany('App\UserRating', 'video_tape_id', 'id');

    }

    public function getScopeUserRatings() {

         return $this->hasMany('App\UserRating', 'video_tape_id', 'video_tape_id')->count();

    }

     public function getUserWishlist() {

         return $this->hasMany('App\Wishlist', 'video_tape_id', 'video_tape_id')->count();

    }

    public function getScopeVideoAds() {

         return $this->hasOne('App\VideoAd', 'video_tape_id', 'video_tape_id');

    }

    public function getVideoAds() {

         return $this->hasOne('App\VideoAd', 'video_tape_id', 'id');

    }

    public function getChannel() {

         return $this->hasOne('App\Channel', 'id', 'channel_id');

    }

    public function getVideoTapeImages() {

         return $this->hasMany('App\VideoTapeImage', 'video_tape_id', 'id');

    }

    public function getVideoTags() {

         return $this->hasMany('App\VideoTapeTag', 'video_tape_id', 'id');

    }

    public function getScopeVideoTags() {

         return $this->hasMany('App\VideoTapeTag', 'video_tape_id', 'video_tape_id')->where('status', TAG_APPROVE_STATUS);

    }


    public function getScopeVideoTapeImages() {

         return $this->hasMany('App\VideoTapeImage', 'video_tape_id', 'video_tape_id');

    }


    public function getScopeLikeCount() {

        return $this->hasMany('App\LikeDislikeVideo', 'video_tape_id', 'video_tape_id')->where('like_status', DEFAULT_TRUE)->count();

    }

    public function getScopeDisLikeCount() {

        return $this->hasMany('App\LikeDislikeVideo', 'video_tape_id', 'video_tape_id')->where('dislike_status', DEFAULT_TRUE)->count();

    }

    public function getLikeCount() {

        return $this->hasMany('App\LikeDislikeVideo', 'video_tape_id', 'id')->where('like_status', DEFAULT_TRUE);

    }

    public function getCategory() {

        return $this->hasOne('App\Category', 'id', 'category_id');

    }

    public function getDisLikeCount() {

        return $this->hasMany('App\LikeDislikeVideo', 'video_tape_id', 'id')->where('dislike_status', DEFAULT_TRUE);

    }
    
     public function getUserFlags() {

         return $this->hasMany('App\Flag', 'video_tape_id', 'id');


    }


    public function getScopeUserFlags() {

         return $this->hasMany('App\Flag', 'video_tape_id', 'video_tape_id')->count();

    }

    public function getBellNotifications() {

         return $this->hasMany('App\BellNotification', 'video_tape_id', 'id');

    }

    public function toArray()
    {
        $array = parent::toArray();

        /*$array['tape_images'] = $this->getVideoTapeImages;

        $array['channel_details'] = $this->getChannel;

        $array['user_details'] = ($this->getChannel) ? $this->getChannel->getUser : [];*/

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

        static::created(function($model) {

            // If the user uploaded video means check the whether admin needs to approve or direct approval

            if( $model->uploaded_by == "user" && Setting::get('is_admin_needs_to_approve_channel_video') == YES) {

                $model->is_approved = DEFAULT_FALSE;

                $model->save();

            }

            $model->attributes['unique_id'] = routefreestring($model->attributes['title']).'-'.$model->attributes['id'];

        });

        //delete your related models here, for example
        static::deleting(function($model)
        {

            if ($model) {

                Helper::delete_picture($model->video, "/uploads/videos/");

                Helper::delete_picture($model->subtitle, "/uploads/subtitles/"); 

                if ($model->banner_image) {

                    Helper::delete_picture($model->banner_image, "/uploads/images/");
                }

                Helper::delete_picture($model->default_image, "/uploads/images/");

                if ($model->video_path) {

                    $explode = explode(',', $model->video_path);

                    if (count($explode) > 0) {


                        foreach ($explode as $key => $exp) {


                            Helper::delete_picture($exp, "/uploads/videos/");

                        }

                    }
        
                }

                if ($model->category_id) {

                    $category = Category::find($model->category_id);

                    if ($category) {

                        $category->no_of_uploads = $category->no_of_uploads > 0 ? $category->no_of_uploads - 1 : 0;

                        $category->save();

                    }

                }

            }

            if (count($model->getVideoTapeImages) > 0) {

                foreach ($model->getVideoTapeImages as $key => $value) {

                    if ($value->image) {

                        Helper::delete_picture($value->image, "/uploads/images/");

                    }

                   $value->delete();    

                }               

            }

            if (count($model->getVideoAds) > 0) {

                if(!is_null($model->getVideoAds)) {

                    $model->getVideoAds->delete();   

                }             

            }

            if (count($model->getBellNotifications) > 0) {

                foreach ($model->getBellNotifications as $key => $value) {

                   $value->delete();    

                }            

            }

            if (count($model->getUserFlags) > 0) {

                foreach ($model->getUserFlags as $key => $value) {

                   $value->delete();    

                }               
            

            }

            if (count($model->getUserRatings) > 0) {

                foreach ($model->getUserRatings as $key => $value) {

                   $value->delete();    

                }               

            }

            if (count($model->userHistory) > 0) {

                foreach ($model->userHistory as $key => $value) {

                   $value->delete();    

                }               

            }

            if (count($model->userWishlist) > 0) {

                foreach ($model->userWishlist as $key => $value) {

                   $value->delete();    

                }               

            }


        }); 

        static::updating(function($model) {

            $model->attributes['unique_id'] = routefreestring($model->attributes['title']).'-'.$model->attributes['id'];

        });

    }

}
