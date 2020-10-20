<?php

use Illuminate\Database\Seeder;

class V3Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(Schema::hasTable('settings')) {

         	DB::table('settings')->insert([
	    		[
			        'key' => 'email_notification',
			        'value' => YES,
			        'created_at' => date('Y-m-d H:i:s'),
			        'updated_at' => date('Y-m-d H:i:s')
			    ]
			]);
    	}
    }
}
