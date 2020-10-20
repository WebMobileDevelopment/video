<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('unique_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('token');
            $table->string('token_expiry');
            $table->integer('user_type');
            $table->string('picture');
            $table->enum('device_type',array('web','android','ios'));
            $table->string('device_token');
            $table->enum('register_type',array('web','android','ios'));
            $table->enum('login_by',array('manual','facebook', 'twitter', 'google','linkedin'));
            $table->string('social_unique_id');
            $table->string('description');
            $table->enum('gender',array('male','female','others'));
            $table->string('mobile');
            $table->double('latitude', 15, 8);
            $table->double('longitude',15,8);
            $table->string('address');
            $table->string('payment_mode');
            $table->integer('card_id'); 
            $table->integer('status')->comment="1 - Approve , 0 - Decline";
            $table->integer('push_status')->comment="Mobile Purpose";
            $table->string('verification_code');
            $table->string('verification_code_expiry');
            $table->integer('is_verified')->default(0)->comment('1 - verified , 0 - No');
            $table->integer('is_moderator');
            $table->integer('moderator_id');
            $table->string('timezone');
            $table->rememberToken();
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
        Schema::drop('users');
    }
}
