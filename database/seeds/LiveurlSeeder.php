<?php

use Illuminate\Database\Seeder;

class LiveurlSeeder extends Seeder
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
		        'key' => 'live_url',
		        'value' => "https://tubenow.bytecollar.com/"
		    ],
		]);
    }
}
