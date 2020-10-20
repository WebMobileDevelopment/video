<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddedAmountFieldsInUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
           /* $table->float('total_amount')->after('ads_status');
            $table->float('total_admin_amount')->after('total_amount');
            $table->float('total_user_amount')->after('total_admin_amount');
            $table->float('paid_amount')->after('total_user_amount');
            $table->float('remaining_amount')->after('paid_amount');*/

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     $table->float('total_admin_amount')->after('ads_status');
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
