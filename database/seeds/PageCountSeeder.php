<?php

use Illuminate\Database\Seeder;

class PageCountSeeder extends Seeder
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
		        'key' => 'no_of_static_pages',
		        'value' => 8
		    ],
		]);
    }
}
