<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategoryFieldsInVideoTapes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('video_tapes', function (Blueprint $table) {
            $table->integer('category_id')->after('user_id');
            $table->string('category_name')->after('category_id');
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
            $table->dropColumn('category_id');
            $table->dropColumn('category_name');
        });
    }
}
