<?php

use Illuminate\Database\Seeder;

class AdminDemoLoginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(Schema::hasTable('settings')) {
            
    		$login_data = DB::table('settings')->where('key' ,'admin_login')->first();
    		$password_data = DB::table('settings')->where('key' ,'admin_password')->first();

    		if(!$login_data &&  !$password_data) {

	         	DB::table('settings')->insert([

	         		[
				        'key' => 'admin_login',
				        'value' => 'admin@tubenow.com',
				        'created_at' => date('Y-m-d H:i:s'),
				        'updated_at' => date('Y-m-d H:i:s')
				    ],

				    [
				        'key' => 'admin_password',
				        'value' => 123456,
				        'created_at' => date('Y-m-d H:i:s'),
				        'updated_at' => date('Y-m-d H:i:s')
				    ],
		    		
				]);
			}
		}
    }
}
