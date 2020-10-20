<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommissionFieldsToPayPerViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pay_per_views', function (Blueprint $table) {
            $table->string('currency')->before('amount');
            $table->float('admin_ppv_amount')->after('amount');
            $table->float('user_ppv_amount')->after('admin_ppv_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pay_per_views', function (Blueprint $table) {
            $table->dropColumn('currency');
            $table->dropColumn('admin_ppv_amount');
            $table->dropColumn('user_ppv_amount');
        });
    }
}
