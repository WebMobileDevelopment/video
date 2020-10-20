<?php

use Illuminate\Database\Seeder;

class PaymentTypeSeeder extends Seeder
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
        		'key' => 'payment_type',
        		'value' => 'stripe',
        	]	
        ]);
    }
}
