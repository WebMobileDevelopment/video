<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCouponFieldsInUserPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_payments', function (Blueprint $table) {
            $table->string('payment_mode', 8)->after('amount');
            $table->tinyInteger('is_coupon_applied')->after('status');
            $table->string('coupon_code', 32)->after('is_coupon_applied');
            $table->double('coupon_amount')->after('coupon_code');
            $table->double('subscription_amount')->after('coupon_amount');
            $table->text('coupon_reason')->after('subscription_amount');
            $table->tinyInteger('is_cancelled')->after('coupon_reason');
            $table->text('cancel_reason')->after('is_cancelled');
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
            $table->dropColumn('payment_mode');
            $table->dropColumn('is_coupon_applied');
            $table->dropColumn('coupon_code');
            $table->dropColumn('coupon_amount');
            $table->dropColumn('subscription_amount');
            $table->dropColumn('coupon_reason');
            $table->dropColumn('is_cancelled');
            $table->dropColumn('cancel_reason');
        });
    }
}
