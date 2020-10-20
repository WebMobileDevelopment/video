<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssignAdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assign_video_ads', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('video_ad_id')->nullable();
            $table->integer('ad_id')->nullable();
            $table->integer('ad_type')->default(0);
            $table->integer('ad_time')->default(0)->comment('In Sec');
            $table->time('video_time')->nullable();
            $table->integer('status')->default(0);
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
        
        Schema::drop('assign_video_ads'); 
    }
}
