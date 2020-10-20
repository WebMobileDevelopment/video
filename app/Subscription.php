<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Setting;

class Subscription extends Model
{

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCommonResponse($query) {

        $currency = Setting::get('currency') ?: "$";

        return $query->select('subscriptions.id as subscription_id', 
                'subscriptions.title' , 
                'subscriptions.description' , 
                'subscriptions.plan' , 
                // 'subscriptions.picture' , 
                \DB::raw("'$currency' as currency"),
                'subscriptions.amount' , 
                // 'subscriptions.plan_type' , 
                'subscriptions.status',
                'subscriptions.created_at',
                'subscriptions.updated_at'
                );
    }

	 /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = array('title', 'description', 'plan' , 'amount', 'picture', 'unique_id', 'total_subscription', 'status');

    /**
	 * Save the unique ID 
	 *
	 *
	 */
    public function setUniqueIdAttribute($value){

		$this->attributes['unique_id'] = uniqid(str_replace(' ', '-', $value));

	}

	public function getUserPayments() {

        return $this->hasMany('App\UserPayment', 'subscription_id', 'id');

    }

}
