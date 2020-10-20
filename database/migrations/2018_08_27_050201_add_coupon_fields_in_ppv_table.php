<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCouponFieldsInPpvTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pay_per_views', function (Blueprint $table) {
            $table->string('payment_mode', 8)->after('amount');
            $table->tinyInteger('is_watched')->after('reason');
            $table->tinyInteger('is_coupon_applied')->after('status');
            $table->string('coupon_code', 32)->after('is_coupon_applied');
            $table->double('coupon_amount')->after('coupon_code');
            $table->double('ppv_amount')->after('coupon_amount');
            $table->text('coupon_reason')->after('ppv_amount');
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
            $table->dropColumn('payment_mode');
            $table->dropColumn('is_watched');
            $table->dropColumn('is_coupon_applied');
            $table->dropColumn('coupon_code');
            $table->dropColumn('coupon_amount');
            $table->dropColumn('ppv_amount');
            $table->dropColumn('coupon_reason');
        });
    }
}
