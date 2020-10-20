<?php

use Illuminate\Database\Seeder;

use App\Helpers\Helper;

use App\Helpers\AppJwt;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();

    	DB::table('users')->insert([
    		[
		        'name' => 'User',
		        'email' => 'user@tubenow.com',
		        'password' => \Hash::make('123456'),
		        'picture' =>"https://tubenow.streamhash.com/placeholder.png",
                'chat_picture' =>"https://tubenow.streamhash.com/placeholder.png",
                'token'=>Helper::generate_token(),
                'token_expiry'=>Helper::generate_token_expiry(),
                'dob'=>'1992-01-01',
                'age_limit'=>25,
                'is_verified'=>1,
                'status'=>1,
		        'created_at' => date('Y-m-d H:i:s'),
		        'updated_at' => date('Y-m-d H:i:s')
		    ],
		
            [
                'name' => 'Test',
                'email' => 'test@tubenow.com',
                'password' => \Hash::make('123456'),
                'picture' =>"https://tubenow.streamhash.com/placeholder.png",
                'chat_picture' =>"https://tubenow.streamhash.com/placeholder.png",
                'token'=>Helper::generate_token(),
                'token_expiry'=>Helper::generate_token_expiry(),
                'dob'=>'1992-01-01',
                'age_limit'=>25,
                'is_verified'=>1,
                'status'=>1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
            
        ]);
    }
}
