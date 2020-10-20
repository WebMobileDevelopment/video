<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Setting;

class RedeemRequest extends Model
{
    public function user() {

    	return $this->belongsTo('App\User');
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCommonResponse($query) {

    	$currency = Setting::get('currency', '$');

        return $query->select('redeem_requests.id as redeem_request_id', 
        				'redeem_requests.request_amount' , 
        				'redeem_requests.paid_amount',
                     	\DB::raw("'$currency' as currency"),
                     	'status',
                     	\DB::raw('DATE_FORMAT(redeem_requests.created_at , "%e %b %y") as requested_date'),
                     	\DB::raw('DATE_FORMAT(redeem_requests.updated_at , "%e %b %y") as paid_date')
                 	);
    }
}
