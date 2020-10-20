<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('unique_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('picture');
            $table->enum('gender',array('male','female','others'));
            $table->string('mobile');
            $table->string('address');
            $table->string('description');
            $table->string('token');
            $table->string('token_expiry');            
            $table->integer('status');
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
        Schema::drop('admins');
    }
}
