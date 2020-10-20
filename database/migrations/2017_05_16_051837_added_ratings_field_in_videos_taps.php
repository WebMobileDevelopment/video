<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddedRatingsFieldInVideosTaps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('video_tapes', function (Blueprint $table) {
            $table->text('reviews')->nullable()->after('watch_count');
            $table->integer('ratings')->default(0)->after('reviews');
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
            $table->dropColumn('ratings');
        });
    }
}
