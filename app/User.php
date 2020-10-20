<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Helpers\Helper;

use App\Helpers\AppJwt;

use Setting;

class User extends Authenticatable
{

    public $expiry_date;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','user_type','device_type','login_by',
        'picture','is_activated', 'timezone', 'verification_code' , 
        'verification_code_expiry','is_verified','age_limit', 'dob','chat_picture'
    ];

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCommonResponse($query) {

        return $query->select(
            'users.id as user_id',
            'users.id as id',
            'users.name',
            'users.email as email',
            'users.picture as picture',
            'users.description as description',
            'users.mobile as mobile',
            'users.dob as dob',
            'users.token as token',
            'users.token_expiry as token_expiry',
            'users.social_unique_id as social_unique_id',
            'users.login_by as login_by',
            'users.payment_mode',
            'users.card_id as user_card_id',
            'users.status as user_status',
            // 'users.email_notification_status',
            // 'users.push_notification_status',
            'users.is_verified',
            'users.user_type',
            'users.created_at',
            'users.updated_at'
            );
    
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function userHistory()
    {
        return $this->hasMany('App\UserHistory');
    }

    public function userRating()
    {
        return $this->hasMany('App\UserRating');
    }

    public function userWishlist()
    {
        return $this->hasMany('App\Wishlist');
    }

    public function userPayment()
    {
        return $this->hasMany('App\UserPayment');
    }

    public function ppvDetails()
    {
        return $this->hasMany('App\PayPerView');
    }

    /**
     * Get the flag record associated with the user.
     */
    public function userFlag()
    {
        return $this->hasMany('App\Flag', 'user_id', 'id');
    }  

    /**
     * Get the referrar user record associated with the user.
     */
    public function userReferrar()
    {
        return $this->has('App\userReferrar', 'user_id', 'id');
    }

    /**
     * Get the referral user record associated with the user.
     */
    public function userReferral()
    {
        return $this->hasMany('App\Referral', 'user_id', 'id');
    } 

    /**
     * Get the referral parent user record associated with the user.
     */
    public function userReferralParent()
    {
        return $this->hasMany('App\Referral', 'parent_user_id', 'id');
    } 

    /**
     * Get the flag record associated with the user.
     */
    public function getChannel()
    {
        return $this->hasMany('App\Channel', 'user_id', 'id');
    }

    /**
     * Get the flag record associated with the user.
     */
    public function getChannelSubscription()
    {
        return $this->hasMany('App\ChannelSubscription', 'user_id', 'id');
    }

     /**
     * Get the flag record associated with the user.
     */
    public function getChannelVideos()
    {
        return $this->hasMany('App\VideoTape', 'user_id', 'id');
    }


    /**
     * Get the Redeems
     */

    public function userRedeem() {
        return $this->hasOne('App\Redeem' , 'user_id' , 'id');
    }

    /**
     * Get the Redeems
     */
    
    public function userRedeemRequests() {
        return $this->hasMany('App\RedeemRequest')->orderBy('status' , 'asc');
    }

     /**
     * Get the Redeems
     */
    
    public function userLiveVideoPayments() {
        return $this->hasMany('App\LiveVideoPayment','user_id');
    }

    /**
     * Save the unique ID 
     *
     *
     */
    public function setUniqueIdAttribute($value){

        $this->attributes['unique_id'] = uniqid(str_replace(' ', '-', $value));

    }

    /**
     * Get the Playlist record associated with the user.
     */
    public function getPlaylist() {
        return $this->hasMany('App\Playlist');
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

        static::creating(function ($model) {
           
            $model->generateToken($model);

            $model->generateEmailCode();

            $model->defaultPaidCheck();
        });

        //delete your related models here, for example
        static::deleting(function($user)
        {
            if (count($user->userHistory) > 0) {

                foreach($user->userHistory as $history)
                {
                    $history->delete();
                } 

            }

            if (count($user->ppvDetails) > 0) {

                foreach($user->ppvDetails as $ppv)
                {
                    $ppv->delete();
                } 

            }

            if (count($user->userRating) > 0) {

                foreach($user->userRating as $rating)
                {
                    $rating->delete();
                } 

            }

            if (count($user->getChannelSubscription) > 0) {

                foreach($user->getChannelSubscription as $subscripton_user)
                {
                    $subscripton_user->delete();
                } 

            }

            if (count($user->userWishlist) > 0) {

                foreach($user->userWishlist as $wishlist)
                {
                    $wishlist->delete();
                } 

            }

            if (count($user->userFlag) > 0) {

                foreach($user->userFlag as $flag)
                {
                    $flag->delete();
                }

            }


            if (count($user->userPayment) > 0) {

                foreach($user->userPayment as $payment)
                {
                    $payment->delete();
                } 
            }
        

            if (count($user->getChannel) > 0) {

                foreach($user->getChannel as $channel) {

                    $channel->delete();

                } 

            }
            if (count($user->userLiveVideoPayments) > 0) {

                foreach($user->userLiveVideoPayments as $payment) {

                    $payment->delete();

                } 

            }

            if (count($user->getChannelVideos) > 0) {

                foreach($user->getChannelVideos as $video) {
 
                    Helper::delete_picture($video->video, "/uploads/videos/");

                    Helper::delete_picture($video->subtitle, "/uploads/subtitles/"); 

                    if ($video->banner_image) {

                        Helper::delete_picture($video->banner_image, "/uploads/images/");
                    }

                    Helper::delete_picture($video->default_image, "/uploads/images/");

                    if ($video->video_path) {

                        $explode = explode(',', $video->video_path);

                        if (count($explode) > 0) {

                            foreach ($explode as $key => $exp) {

                                Helper::delete_picture($exp, "/uploads/videos/");
                            }

                        }                

                    }

                    $video->delete();
                    
                } 

            }

            if (count($user->userRedeem) > 0) {

                $user->userRedeem()->delete();

            }

            if (count($user->userRedeemRequests) > 0) {

                $user->userRedeemRequests()->delete();

            }

            if (count($user->getPlaylist) > 0) {

                foreach($user->getPlaylist as $playlist) {

                    $playlist->delete();

                } 

            }

        }); 
    }


    protected function defaultPaidCheck() {

        if(Setting::get('is_default_paid_user') == 1) {

            $this->attributes['user_type'] = 1;

        }
    }


    /**
     * Generates Token and Token Expiry
     * 
     * @return bool returns true if successful. false on failure.
     */

    protected function generateEmailCode() {

        $this->attributes['verification_code'] = Helper::generate_email_code();

        $this->attributes['verification_code_expiry'] = Helper::generate_email_expiry();

        // $this->attributes['is_verified'] = 0;

        $this->attributes['status'] = DEFAULT_TRUE;

        return true;
    }

    /**
     * Generates Token and Token Expiry
     * 
     * @return bool returns true if successful. false on failure.
     */

    protected function generateToken($model) {

        $this->attributes['token'] = Helper::generate_token();

        // $this->attributes['token'] = AppJwt::create(['id' => $model->id, 'email' => $model->email, 'role' => "model"]);

        $this->attributes['token_expiry'] = Helper::generate_token_expiry();

        return true;
    }

}
