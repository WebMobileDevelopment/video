<?php

use Illuminate\Database\Seeder;

class AddedStripeKeyInSettings extends Seeder
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
		    	'key' => 'stripe_publishable_key' ,
		    	'value' => "pk_test_uDYrTXzzAuGRwDYtu7dkhaF3",
		    ],
		    [
		    	'key' => 'stripe_secret_key' ,
		    	'value' => "sk_test_lRUbYflDyRP3L2UbnsehTUHW",
		    ],

		]);
	   
        
    }
}
