<?php

use Illuminate\Database\Seeder;

class WowzaDetailsSeeder extends Seeder
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
		        'key' => 'wowza_port_number',
		        'value' => '1935'
		    ],
		    [
		        'key' => 'wowza_app_name',
		        'value' => 'live'
		    ],
		    [
		        'key' => 'wowza_username',
		        'value' => 'streamnow'
		    ],
		    [
		        'key' => 'wowza_password',
		        'value' => 'streamnow'
		    ],
		    [
		    	'key'=>'wowza_license_key',
		    	'value'=>'GOSK-8F45-010C-C962-ABB0-264A'
		    ]
		]);
    }
}
