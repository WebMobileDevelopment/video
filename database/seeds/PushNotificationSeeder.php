<?php

use Illuminate\Database\Seeder;

class PushNotificationSeeder extends Seeder
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
		        'key' => 'push_notification',
		        'value' => 1
		    ],
		]);
    }
}
