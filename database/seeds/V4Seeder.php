<?php

use Illuminate\Database\Seeder;

class V4Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

    	if(Schema::hasTable('bell_notification_templates')) {

         	DB::table('bell_notification_templates')->insert([
	    		[
			        'unique_id' => uniqid(),
			        'type' => BELL_NOTIFICATION_NEW_VIDEO,
			        'title' => tr('BELL_NOTIFICATION_NEW_VIDEO'),
			        'message' => '{channel_name} uploaded: {video_title}',
			        'status' => APPROVED,
			        'created_at' => date('Y-m-d H:i:s'),
			        'updated_at' => date('Y-m-d H:i:s')
			    ],
			    [
			        'unique_id' => uniqid(),
			        'type' => BELL_NOTIFICATION_NEW_SUBSCRIBER,
			        'title' => tr('BELL_NOTIFICATION_NEW_SUBSCRIBER'),
			        'message' => 'The {username} subscribed your channel: {channel_name}',
			        'status' => APPROVED,
			        'created_at' => date('Y-m-d H:i:s'),
			        'updated_at' => date('Y-m-d H:i:s')
			    ]
			
			]);
    	}

    	if(Schema::hasTable('settings')) {

	        DB::table('settings')->insert([
	    		[
			        'key' => 'meta_title',
			        'value' => "STREAMTUBE",
			        'created_at' => date('Y-m-d H:i:s'),
			        'updated_at' => date('Y-m-d H:i:s')
			    ],
			    [
			        'key' => 'meta_description',
			        'value' => "STREAMTUBE",
			        'created_at' => date('Y-m-d H:i:s'),
			        'updated_at' => date('Y-m-d H:i:s')
			    ],
			    [
			        'key' => 'meta_author',
			        'value' => "STREAMTUBE",
			        'created_at' => date('Y-m-d H:i:s'),
			        'updated_at' => date('Y-m-d H:i:s')
			    ],
			    [
			        'key' => 'meta_keywords',
			        'value' => "STREAMTUBE",
			        'created_at' => date('Y-m-d H:i:s'),
			        'updated_at' => date('Y-m-d H:i:s')
			    ],
			]);
    	}
        
    }
}
