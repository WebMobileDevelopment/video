<?php

use Illuminate\Database\Seeder;

class AddSocialLinksSeeder extends Seeder
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
	            'key' => "facebook_link",
	            'value' => '',
        	],
        	[
	            'key' => "linkedin_link",
	            'value' => '',
        	],
        	[
	            'key' => "twitter_link",
	            'value' => '',
        	],
        	[
	            'key' => "google_plus_link",
	            'value' => '',
        	],
        	[
	            'key' => "pinterest_link",
	            'value' => '',
        	]
        ]);
    }
}
