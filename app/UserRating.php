<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRating extends Model
{
    public function adminVideo() {
        return $this->belongsTo('App\VideoTape');
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCommonResponse($query) {

        return $query->leftJoin('users' , 'user_ratings.user_id' , '=' , 'users.id')
        	->select(
            'user_ratings.id as user_rating_id',
            'user_ratings.user_id',
            'user_ratings.video_tape_id',
            'users.name as username',
            'users.picture as picture',
            'user_ratings.rating as rating',
            'user_ratings.comment as comment',
			\DB::raw("DATE_FORMAT(user_ratings.created_at, '%M %Y') as created"),           
            'user_ratings.created_at',
            'user_ratings.updated_at'
            );
    
    }

    public function toArray()
    {
        $array = parent::toArray();

        $array['diff_human_time'] = ($this->created_at) ? $this->created_at->diffForHumans() : 0;

        return $array;
    }

}
