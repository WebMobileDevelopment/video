<?php

use Illuminate\Database\Seeder;

class AddedAgeKeyInSettings extends Seeder
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
		        'key' => 'age_limit',
		        'value' => 18
		    ],
		]);
    }
}
