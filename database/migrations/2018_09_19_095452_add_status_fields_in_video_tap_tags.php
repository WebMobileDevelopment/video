<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusFieldsInVideoTapTags extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('video_tape_tags', function (Blueprint $table) {
            $table->tinyInteger('status')->after('tag_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('video_tape_tags', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
