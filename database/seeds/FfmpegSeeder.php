<?php

use Illuminate\Database\Seeder;

class FfmpegSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            'key' => "ffmpeg_installed",
            'value' => 0,
        ]);
    }
}
