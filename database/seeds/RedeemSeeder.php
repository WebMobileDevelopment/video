<?php

use Illuminate\Database\Seeder;

class RedeemSeeder extends Seeder
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
		        'key' => 'minimum_redeem',
		        'value' => 1
		    ],
		    [
		        'key' => 'redeem_control',
		        'value' => 1
		    ]
		]);
    }
}
