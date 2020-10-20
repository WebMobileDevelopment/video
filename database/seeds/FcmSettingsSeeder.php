<?php

use Illuminate\Database\Seeder;

class FcmSettingsSeeder extends Seeder
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
                'key' => 'user_fcm_sender_id',
                'value' => '865212328189',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
              'key' => 'user_fcm_server_key' ,
              'value' => 'AAAASJFloB0:APA91bHBe54g5RP63U3EMTRClOVIXV3R8dwQ0xdwGTimGIWuKklipnpn3a7ASHDmEIuZ_OHTUDpWPYIzsXLTXXPE_UEJOz0BR1GgZ7s_gF41DKZjmJVsO3qfUOpZT2SqVMInOcL1Z55e',
              'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
          ]);
        }
    }
}

