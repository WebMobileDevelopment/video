<?php

use Illuminate\Database\Seeder;

class AddedLanguageControlInSettings extends Seeder
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
		        'key' => 'admin_language_control',
		        'value' => 1
		    ],
		]);
    }
}

