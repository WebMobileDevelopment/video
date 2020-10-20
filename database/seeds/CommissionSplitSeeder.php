<?php

use Illuminate\Database\Seeder;

class CommissionSplitSeeder extends Seeder
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
		        'key' => 'admin_commission',
		        'value' => 10
		    ],

		    [
		        'key' => 'user_commission',
		        'value' => 90
		    ],
		]);
    }
}
