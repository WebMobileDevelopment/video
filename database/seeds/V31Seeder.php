<?php

use Illuminate\Database\Seeder;

// Version 3.1 seeder

class V31Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
    		[
		        'key' => 'is_admin_needs_to_approve_channel_video',
		        'value' => NO
		    ],
		    [
		        'key' => 'is_direct_upload_button',
		        'value' => NO
		    ],
		]);
    }
}
