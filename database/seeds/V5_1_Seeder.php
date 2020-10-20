<?php

use Illuminate\Database\Seeder;

class V5_1_Seeder extends Seeder
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
		        'key' => 'is_appstore_upload',
		        'value' => 0 // No
		    ],
		]);
    }
}
