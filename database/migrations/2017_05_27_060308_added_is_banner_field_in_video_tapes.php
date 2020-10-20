<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddedIsBannerFieldInVideoTapes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('video_tapes', function (Blueprint $table) {
            $table->integer('is_banner')->default(0)->after('status');
            $table->string('banner_image')->nullable()->after('is_banner');
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
            $table->dropColumn('is_banner');
            $table->dropColumn('banner_image');
        });
    }
}
