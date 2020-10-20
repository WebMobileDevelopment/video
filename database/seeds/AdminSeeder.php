<?php

use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->delete();
    	DB::table('admins')->insert([
    		[
		        'name' => 'Admin',
		        'email' => 'admin@tubenow.com',
		        'password' => \Hash::make('123456'),
		        'picture' => "https://tubenow.streamhash.com/placeholder.png",
		        'created_at' => date('Y-m-d H:i:s'),
		        'updated_at' => date('Y-m-d H:i:s')
		    ],

            [
                'name' => 'Test',
                'email' => 'test@streamtube.com',
                'password' => \Hash::make('123456'),
                'picture' => "",
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]

        ]);
    }
}
