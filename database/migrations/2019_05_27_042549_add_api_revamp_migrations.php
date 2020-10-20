<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddApiRevampMigrations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_payments', function (Blueprint $table) {
            $table->integer('is_current')->default(0)->after('status');
            $table->string('currency')->default(Setting::get('currency', '$'))->after('amount');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer('email_notification_status')->default(1)->after('push_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_payments', function (Blueprint $table) {
            $table->dropColumn('is_current');
            $table->dropColumn('currency');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('email_notification_status');
        });
    }
}
