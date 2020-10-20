<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVersion4Migration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bell_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('from_user_id');
            $table->integer('to_user_id');
            $table->string('notification_type');
            $table->text('message');
            $table->integer('channel_id')->default(0);
            $table->integer('video_tape_id')->default(0);
            $table->integer('status')->default(BELL_NOTIFICATION_STATUS_UNREAD);
            $table->timestamps();
        });

        Schema::create('bell_notification_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('unique_id');
            $table->string('type');
            $table->string('title');
            $table->text('message');
            $table->integer('status');
            $table->timestamps();
        });

        Schema::table('admins', function (Blueprint $table) {

            $table->string('role')->default(ADMIN)->after('status');

        });

        Schema::table('channels', function (Blueprint $table) {

            $table->string('youtube_channel_id')->default("")->after('status');

            $table->dateTime('youtube_channel_created_at')->nullable()->after('youtube_channel_id');

            $table->dateTime('youtube_channel_updated_at')->nullable()->after('youtube_channel_id');

        });

        Schema::table('video_tapes', function (Blueprint $table) {

            $table->string('youtube_video_id')->after('status');

            $table->string('youtube_channel_id')->after('youtube_video_id');

            $table->tinyInteger('is_youtube_downloaded')->default(NO)->after('youtube_channel_id');

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('bell_notifications');

        Schema::drop('bell_notification_templates');

        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('youtube_channel_id');
            $table->dropColumn('youtube_channel_created_at');
            $table->dropColumn('youtube_channel_updated_at');
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('is_youtube_downloaded');
        });

    }
}
