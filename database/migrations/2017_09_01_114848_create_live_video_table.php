<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLiveVideoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('live_videos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('unique_id');
            $table->integer('channel_id');
            $table->integer('user_id');
            $table->string('virtual_id');
            $table->string('type', 64)->nullable()->comment('Public, Private');
            $table->integer('payment_status')->default(0)->commen('0 - No, 1 - Yes');
            $table->string('title')->nullabe();
            $table->text('description')->nullabe();
            $table->float('amount')->default(0);
            $table->integer('is_streaming')->default(0);
            $table->string('snapshot')->nullable();
            $table->text('video_url');
            $table->integer('viewer_cnt')->default(0);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('no_of_minutes')->default(0)->nullable();
            $table->string('port_no');
            $table->integer('status')->default(0);
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
        Schema::drop('live_videos');
    }
}
