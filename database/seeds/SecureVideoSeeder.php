<?php

use Illuminate\Database\Seeder;

class SecureVideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

    	if(Schema::hasTable('settings')) {

            $check_setting_rtmp = DB::table('settings')->where('key' , 'RTMP_SECURE_VIDEO_URL')->count();

            if(!$check_setting_rtmp) {

                $rtmp_settings = DB::table('settings')->insert([
                    [
                        'key' => 'RTMP_SECURE_VIDEO_URL',
                        'value' => '',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ],
                ]);

            }

            $check_setting_hls = DB::table('settings')->where('key' , 'HLS_SECURE_VIDEO_URL')->count();

            if(!$check_setting_hls) {

                $hls_settings = DB::table('settings')->insert([
                    [
                        'key' => 'HLS_SECURE_VIDEO_URL',
                        'value' => '',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ],
                ]);

            }

            $check_setting_smil = DB::table('settings')->where('key' , 'VIDEO_SMIL_URL')->count();

            if(!$check_setting_smil) {

                $hls_settings = DB::table('settings')->insert([
                    [
                        'key' => 'VIDEO_SMIL_URL',
                        'value' => '',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ],
                ]);

            }

        }
    }
}
