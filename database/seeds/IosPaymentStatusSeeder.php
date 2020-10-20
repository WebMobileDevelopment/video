<?php

use Illuminate\Database\Seeder;

class IosPaymentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            'key' => "ios_payment_subscription_status",
            'value' => 0,
        ]);
    }
}
