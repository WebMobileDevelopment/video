<?php

use Illuminate\Database\Seeder;

class DeleteVideoHourSettings extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(Schema::hasTable('settings')) {
    		DB::table('settings')->where('key' , 'delete_video_hour')->delete();
    	}

    	DB::table('settings')->insert([
    		[
		        'key' => 'delete_video_hour',
		        'value' => 2,
		    ]
		]);
    }
}
