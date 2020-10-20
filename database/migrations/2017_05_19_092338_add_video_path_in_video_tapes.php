<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVideoPathInVideoTapes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('video_tapes', function (Blueprint $table) {
            $table->text('video_path')->nullable()->after('reviews');
            $table->string('video_resolutions')->nullable()->after('video_path');
            $table->string('publish_status')->default(0)->after('video_resolutions');
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
            $table->dropColumn('video_path');
            $table->dropColumn('video_resolutions');
            $table->dropColumn('publish_status');
        });
    }
}
