<?php

use Illuminate\Database\Seeder;

class RedeempaypalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // https://www.paypal.com/cgi-bin/webscr - LIVE 
        // https://www.sandbox.paypal.com/cgi-bin/webscr = sandbox
        DB::table('settings')->insert([
    		[
		        'key' => 'redeem_paypal_url',
		        'value' => "https://www.sandbox.paypal.com/cgi-bin/webscr"
		    ]
		]);
    }
}
