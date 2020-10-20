<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideoTapesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_tapes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('channel_id');
            $table->string('unique_id');
            $table->string('title');
            $table->text('description');
            $table->string('default_image');
            $table->string('video');
            $table->time('duration')->nullable();
            $table->string('video_publish_type')->comment="1 - publish now , 2 Publish later";
            $table->dateTime('publish_time');
            $table->integer('is_approved')->comment="Admin Approve and UnApprove";
            $table->integer('status')->comment="User Approve and UnApprove";
            $table->integer('watch_count');
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
        Schema::drop('video_tapes');
    }
}
