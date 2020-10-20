<?php

use Illuminate\Database\Seeder;

class WowzwIPaddress extends Seeder
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
			        'key' => 'wowza_ip_address',
			        'value' => '',
			        'created_at' => date('Y-m-d H:i:s'),
			        'updated_at' => date('Y-m-d H:i:s')
			    ],

			]);
    }
}
