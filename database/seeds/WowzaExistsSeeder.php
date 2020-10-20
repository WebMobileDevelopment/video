<?php

use Illuminate\Database\Seeder;

class WowzaExistsSeeder extends Seeder
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
		        'key' => 'is_wowza_configured',
		        'value' => 0
		    ]
		]);
    }
}

