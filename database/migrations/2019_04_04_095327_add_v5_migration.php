<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddV5Migration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_referrers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('unique_id');
            $table->integer('user_id');
            $table->string('referral_code');
            $table->integer('total_referrals')->default(0);
            $table->float('total_referrals_earnings')->default(0);
            $table->integer('status')->default(1);
            $table->timestamps();
        });

        Schema::create('referrals', function (Blueprint $table) {
            $table->increments('id');
            $table->string('unique_id');
            $table->integer('user_id');
            $table->integer('parent_user_id');
            $table->integer('user_referrer_id');
            $table->string('referral_code');
            $table->string('source');            
            $table->integer('status')->default(1);
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
        Schema::drop('user_referrers');
        Schema::drop('referrals');
    }
}