<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddedEnuValuesPages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pages', function (Blueprint $table) {
            DB::statement("ALTER TABLE `pages` CHANGE `type` `type` ENUM( 'about', 'privacy', 'terms', 'help', 'others', 'contact' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pages', function (Blueprint $table) {
            //
        });
    }
}
