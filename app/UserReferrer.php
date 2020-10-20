<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserReferrer extends Model
{
    public function userDetails() {

        return $this->belongsTo('App\User', 'user_id');
    }
    
    /**
     * Get the flag record associated with the user.
     */
    public function getReferral()
    {
        return $this->hasMany('App\Referral', 'user_referrer_id', 'id');
    }
    
}
