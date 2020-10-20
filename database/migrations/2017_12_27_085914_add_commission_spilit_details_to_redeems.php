<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommissionSpilitDetailsToRedeems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('redeems', function (Blueprint $table) {
            $table->float('total_admin_amount')->after('total');
            $table->float('total_user_amount')->after('total_admin_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('redeems', function (Blueprint $table) {
            $table->dropColumn('total_admin_amount');
            $table->dropColumn('total_user_amount');
        });
    }
}
