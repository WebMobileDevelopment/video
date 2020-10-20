<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlaylistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('playlists', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('channel_id')->default(0);
            $table->integer('user_id');
            $table->string('title');
            $table->text('description');
            $table->string('picture');
            $table->string('playlist_display_type')->comment="Public, Private";
            $table->string('playlist_type')->comment="User, Channel";
            $table->integer('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('playlists');
    }
}
