<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('unique_id');
            $table->integer('live_video_id');
            $table->integer('user_id');
            $table->integer('live_video_viewer_id');
            $table->text('message');
            $table->enum('type',array('uv','vu'))->comment('uv - User To Viewer , pu - Viewer to User');
            $table->boolean('delivered');
            $table->softDeletes();
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
        Schema::drop('chat_messages');
    }
}
