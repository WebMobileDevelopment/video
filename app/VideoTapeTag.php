<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VideoTapeTag extends Model
{
    //

    public function getTag() {

        return $this->hasOne('App\Tag', 'id', 'tag_id');

    }
}
