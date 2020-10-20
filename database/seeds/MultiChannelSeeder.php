<?php

use Illuminate\Database\Seeder;

class MultiChannelSeeder extends Seeder
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
		        'key' => 'multi_channel_status',
		        'value' => 0
		    ]
		]);
    }
}
