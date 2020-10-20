<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddedPpvInVideosTapesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('video_tapes', function (Blueprint $table) {
            $table->integer('type_of_user')->default(0)->after('user_ratings');
            $table->integer('type_of_subscription')->default(0)->after('type_of_user');
            $table->float('ppv_amount')->default(0)->after('type_of_subscription');
            $table->float('admin_ppv_amount')->default(0)->after('ppv_amount');
            $table->float('user_ppv_amount')->default(0)->after('admin_ppv_amount');
            $table->string('ppv_created_by')->nullable()->after('user_ppv_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('video_tapes', function (Blueprint $table) {
            //
        });
    }
}
