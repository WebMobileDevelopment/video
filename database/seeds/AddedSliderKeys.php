<?php

use Illuminate\Database\Seeder;

class AddedSliderKeys extends Seeder
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
		    	'key' => 'is_banner_video' ,
		    	'value' => 0,
		    ],
		    [
		    	'key' => 'is_banner_ad' ,
		    	'value' => 1,
		    ],
		]);
	   
    }
}
