<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Subscription;

class UserPayment extends Model
{
    public function adminVideo() {
        return $this->belongsTo('App\VideoTape');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function getSubscription() {
        return $this->hasOne('App\Subscription', 'id', 'subscription_id');
    }
    
    public static function getCurSubscr($user_id) {
        $subscr_plan = UserPayment::where('user_id', $user_id)->first();
        $subscr = new Subscription;
        if($subscr_plan) {
            $subscr = Subscription::find($subscr_plan->subscription_id);
            return $subscr;
        }
        return $subscr;
    }
}
