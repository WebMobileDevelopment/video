<?php

use Illuminate\Database\Seeder;

class AddedMaxsizekeysInSettings extends Seeder
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
		        'key' => 'post_max_size',
		        'value' => "2000M"
		    ],
		    [
		        'key' => 'upload_max_size',
		        'value' => "2000M"
		    ]
		]);
    }
}
