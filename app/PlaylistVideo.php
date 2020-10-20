<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlaylistVideo extends Model
{
    public function playlistDetails() {

        return $this->belongsTo('App\Playlist');
    }
}
