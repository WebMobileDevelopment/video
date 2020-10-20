<?php

use Illuminate\Database\Seeder;

class VideoSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(Schema::hasTable('settings')) {

        	$data = DB::table('settings')->where('key' , "JWPLAYER_KEY")->delete();

         	DB::table('settings')->insert([
	    		[
			        'key' => 'JWPLAYER_KEY',
			        'value' => 'M2NCefPoiiKsaVB8nTttvMBxfb1J3Xl7PDXSaw==',
			        'created_at' => date('Y-m-d H:i:s'),
			        'updated_at' => date('Y-m-d H:i:s')
			    ],
			    [
			    	'key' => 'HLS_STREAMING_URL' ,
			    	'value' => '',
			    	'created_at' => date('Y-m-d H:i:s'),
			        'updated_at' => date('Y-m-d H:i:s')
			    ]
			]);
    	}
    }
}
