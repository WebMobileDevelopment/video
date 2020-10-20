<?php

use Illuminate\Database\Seeder;

class RegisterAgeLimitSeeder extends Seeder
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
		        'key' => 'max_register_age_limit',
		        'value' => 15
		    ]
		]);
    }
}
