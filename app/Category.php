<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //

    public function getVideos() {

         return $this->hasMany('App\VideoTape', 'category_id', 'id');

    }


  
}
