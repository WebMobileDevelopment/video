<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_ads', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('video_tape_id');
            $table->string('types_of_ad')->nullable()->comment('1 - Pre Ad, 2 - Post Ad, 3 - In Between Ad');
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
        Schema::drop('video_ads');
    }
}
