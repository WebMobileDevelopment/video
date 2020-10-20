<?php

use Illuminate\Database\Seeder;

class AddedLiveVideoKeysInSettings extends Seeder
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
		        'key' => 'SOCKET_URL',
		        'value' => '',
		        'created_at' => date('Y-m-d H:i:s'),
		        'updated_at' => date('Y-m-d H:i:s')
		    ],
		    [
		    	'key' => 'BASE_URL' ,
		    	'value' => '/',
		    	'created_at' => date('Y-m-d H:i:s'),
		        'updated_at' => date('Y-m-d H:i:s')
		    ]
		]);
    }
}
