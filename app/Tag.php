<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    //
    public function videoTapeTags() {

        return $this->hasMany('App\VideoTapeTag');

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

        	if (count($model->videoTapeTags) > 0) {

                foreach ($model->videoTapeTags as $key => $value) {

                   $value->delete();    

                }               

            }

        });

    }

}
