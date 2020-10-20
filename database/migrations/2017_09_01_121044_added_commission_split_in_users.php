<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddedCommissionSplitInUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //

            DB::statement('ALTER TABLE `users` CHANGE `token` `token` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;');

            $table->string('role', 64)->nullable()->after('status');

            $table->float('amount_paid')->defined(0);
            $table->dateTime('expiry_date')->nullable();
            $table->integer('no_of_days')->defined(0);
            $table->string('paypal_email')->after('user_type');
            $table->float('total_amount')->after('status');
            $table->float('total_admin_amount')->after('total_amount');
            $table->float('total_user_amount')->after('total_admin_amount');
            $table->float('paid_amount')->after('total_user_amount');
            $table->float('remaining_amount')->after('paid_amount');
            $table->text('chat_picture')->nullable()->after('picture');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
