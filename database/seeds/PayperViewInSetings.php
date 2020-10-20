<?php

use Illuminate\Database\Seeder;

class PayperViewInSetings extends Seeder
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
		        'key' => 'is_payper_view',
		        'value' => 1
		    ]
		]);
    }
}
