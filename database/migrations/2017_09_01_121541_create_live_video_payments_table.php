<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLiveVideoPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('live_video_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('unique_id');
            $table->integer('live_video_id');
            $table->integer('user_id');
            $table->integer('live_video_viewer_id');
            $table->string('payment_id');
            $table->string('payment_mode');
            $table->float('amount');
            $table->float('admin_amount');
            $table->float('user_amount');
            $table->string('currency');
            $table->integer('status');
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
        Schema::drop('live_video_payments');
    }
}
