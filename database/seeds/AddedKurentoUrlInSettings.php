<?php

use Illuminate\Database\Seeder;

class AddedKurentoUrlInSettings extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        DB::table('settings')->insert([
    		[
		        'key' => 'cross_platform_url',
		        'value' => "",
		    ],
		    [
		        'key' => 'mobile_rtsp',
		        'value' => "",
		    ],
		    [
		        'key' => 'wowza_server_url',
		        'value' => "",
		    ],
		    [
		        'key' => 'kurento_socket_url',
		        'value' => "",
		    ],
		]);
    }
}
