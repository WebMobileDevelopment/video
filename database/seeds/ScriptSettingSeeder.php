<?php

use Illuminate\Database\Seeder;

class ScriptSettingSeeder extends Seeder
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
		        'key' => 'header_scripts',
		        'value' => ""
		    ],
		    [
		        'key' => 'body_scripts',
		        'value' => ""
		    ]
		]);
    }
}
