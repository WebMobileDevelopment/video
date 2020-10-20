<?php

use Illuminate\Database\Seeder;

class AddedChatUrlInSettings extends Seeder
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
		        'key' => 'chat_socket_url',
		        'value' => "",
		    ],
		    [
		        'key' => 'chat_url',
		        'value' => "",
		    ],

		]);
    }
}
