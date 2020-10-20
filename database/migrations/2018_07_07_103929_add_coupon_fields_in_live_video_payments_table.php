<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCouponFieldsInLiveVideoPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('live_video_payments', function (Blueprint $table) {
            $table->tinyInteger('is_coupon_applied')->after('status');
            $table->string('coupon_code')->after('is_coupon_applied');
            $table->double('coupon_amount')->after('coupon_code');
            $table->double('live_video_amount')->after('coupon_amount')->commit('Live Video Amount');
            $table->text('coupon_reason')->after('live_video_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('live_video_payments', function (Blueprint $table) {
            $table->dropColumn('is_coupon_applied');
            $table->dropColumn('coupon_code');
            $table->dropColumn('coupon_amount');
            $table->dropColumn('live_video_amount');
            $table->dropColumn('coupon_reason');
        });
    }
}
