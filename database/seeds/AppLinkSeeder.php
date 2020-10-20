<?php

use Illuminate\Database\Seeder;

class AppLinkSeeder extends Seeder
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
	            'key' => "appstore",
	            'value' => "",
        	],
        	[
	            'key' => "playstore",
	            'value' => "",
        	]
        ]);
    }
}
