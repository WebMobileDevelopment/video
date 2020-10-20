<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AdminSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(MobileRegisterSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(RedeemSeeder::class);
        $this->call(ScriptSettingSeeder::class);
        $this->call(MultiChannelSeeder::class);
        $this->call(AdminDemoLoginSeeder::class);
        $this->call(PageCountSeeder::class);
        $this->call(VideoSettingsSeeder::class);
        $this->call(AddedMaxsizekeysInSettings::class);
        $this->call(AddedLanguageControlInSettings::class);
        $this->call(AddedStripeKeyInSettings::class);
        $this->call(AddedAgeKeyInSettings::class);
        $this->call(AddedLiveVideoKeysInSettings::class);
        $this->call(AddedKurentoUrlInSettings::class);
        $this->call(CommissionSplitSeeder::class);
        $this->call(RegisterAgeLimitSeeder::class);
        $this->call(AddedChatUrlInSettings::class);
        $this->call(AddedSliderKeys::class);
        $this->call(VODKeyInSettings::class);
        $this->call(WowzwIPaddress::class);
        $this->call(ChannelSettingsSeeder::class);
        $this->call(DeleteVideoHourSettings::class);
        $this->call(PayperviewCommissionSplit::class);
        $this->call(PayperViewInSetings::class);
        $this->call(AddSocialLinksSeeder::class);
        $this->call(AppLinkSeeder::class);
        $this->call(LanguageSeeder::class);
        $this->call(PushNotificationSeeder::class);
        $this->call(SecureVideoSeeder::class);
        $this->call(MailGunSeeder::class);
        $this->call(IosPaymentStatusSeeder::class);
        $this->call(PaymentTypeSeeder::class);
        $this->call(FfmpegSeeder::class);
        $this->call(WowzaDetailsSeeder::class);
        $this->call(WowzaExistsSeeder::class);
        $this->call(V3Seeder::class);
        $this->call(V31Seeder::class);
        $this->call(LiveurlSeeder::class);
        $this->call(V4Seeder::class);
        $this->call(V5Seeder::class);
        $this->call(FcmSettingsSeeder::class);
        $this->call(RedeempaypalSeeder::class);
        $this->call(V5_1_Seeder::class);
    }
}
