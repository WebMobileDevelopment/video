<?php

use Illuminate\Database\Seeder;

class MailGunSeeder extends Seeder
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
		        'key' => 'MAILGUN_PUBLIC_KEY',
		        'value' => "pubkey-7dc021cf4689a81a4afb340d1a055021"
		    ],
		    [
		        'key' => 'MAILGUN_PRIVATE_KEY',
		        'value' => ""
		    ]
		]);
    }
}
