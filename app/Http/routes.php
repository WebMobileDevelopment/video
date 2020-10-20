<?php  

use Illuminate\Support\Facades\Redis;


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


Route::get('/clear-cache', function() {

    $exitCode = Artisan::call('config:cache');

    return back();

})->name('clear-cache');


// Generral configuration routes 

Route::post('project/configurations' , 'ApplicationController@configuration_site');

Route::get('pages/list' , 'ApplicationController@static_pages_api');

Route::get('video_tapes_auto_clear_cron' , 'ApplicationController@video_tapes_auto_clear_cron');

Route::get('demo_credential_cron' , 'ApplicationController@demo_credential_cron');

// UI

Route::get('/bell_notifications' , 'SampleController@bell_notifications');

Route::get('/video_notification' , 'SampleController@video_notification');

Route::get('/upload_videoUI' , 'SampleController@upload_video');

// Unused Sample Routes

Route::get('/addIndex', 'ApplicationController@addIndex')->name('addIndex');

Route::get('/addAll', 'ApplicationController@addAllVideoToEs')->name('addAll');

Route::post('select/sub_category' , 'ApplicationController@select_sub_category')->name('select.sub_category');

Route::post('select/genre' , 'ApplicationController@select_genre')->name('select.genre');


// Chat Message save


Route::get('message/save' , 'ApplicationController@message_save')->name('message.save');


// Cron jobs

Route::get('/automatic/renewal', 'ApplicationController@automatic_renewal')->name('automatic.renewal');

// Application Routes

Route::get('/generate/index' , 'ApplicationController@generate_index');

Route::get('/payment/failure' , 'ApplicationController@payment_failure')->name('payment.failure');

Route::get('/email/verification' , 'ApplicationController@email_verify')->name('email.verify');

// Installation

Route::get('/install/configure', 'InstallationController@install')->name('installTheme');

Route::get('/system/check', 'InstallationController@system_check_process')->name('system-check');

Route::post('/install/theme', 'InstallationController@theme_check_process')->name('install.theme');

Route::post('/install/settings', 'InstallationController@settings_process')->name('install.settings');

Route::get('/user_session_language/{lang}', 'ApplicationController@set_session_language')->name('user_session_language');

Route::get('/user/searchall' , 'ApplicationController@search_video')->name('search');

Route::any('/user/search' , 'ApplicationController@search_all')->name('search-all');

// Social Login

Route::post('/social', array('as' => 'SocialLogin' , 'uses' => 'SocialAuthController@redirect'));

Route::get('/callback/{provider}', 'SocialAuthController@callback');

// Referral link generate

Route::get('/rc/{referral_code}', 'UserController@referrals_signup')->name('referrals_signup');

// Embed Links

Route::get('/embed', 'ApplicationController@embed_video')->name('embed_video');

// Admin to users login

Route::get('/master/login', 'UserController@master_login')->name('master.login');

// CRON

Route::get('/publish/video', 'ApplicationController@cron_publish_video')->name('publish');

Route::get('/notification/payment', 'ApplicationController@send_notification_user_payment')->name('notification.user.payment');

Route::get('/payment/expiry', 'ApplicationController@user_payment_expiry')->name('user.payment.expiry');

Route::get('cron_delete_video', 'ApplicationController@cron_delete_video');
    
// Static Pages

Route::get('/privacy', 'UserApiController@privacy')->name('user.privacy');

Route::get('/help', 'UserApiController@help')->name('user.help');

Route::get('/terms_condition', 'UserApiController@terms')->name('user.terms');

Route::get('/about', 'ApplicationController@about')->name('user.about');

Route::get('/privacy_policy', 'ApplicationController@privacy')->name('user.privacy_policy');

Route::get('/terms', 'ApplicationController@terms')->name('user.terms-condition');

Route::get('page_view/{id}', 'UserController@page_view')->name('page_view');

Route::get('/admin/check_role', 'NewAdminController@check_role');

Route::group(['prefix' => 'admin' , 'as' => 'admin.'], function() {

    Route::get('login', 'Auth\AdminAuthController@showLoginForm')->name('login');

    Route::post('login', 'Auth\AdminAuthController@login')->name('login.post');

    Route::get('logout', 'Auth\AdminAuthController@logout')->name('logout');

    Route::get('secure', 'AdminController@secure')->name('secure');

    // Registration Routes...

    Route::get('register', 'Auth\AdminAuthController@showRegistrationForm');

    Route::post('register', 'Auth\AdminAuthController@register');

    // Password Reset Routes...
    Route::get('password/reset/{token?}', 'Auth\AdminPasswordController@showResetForm');

    Route::post('password/email', 'Auth\AdminPasswordController@sendResetLinkEmail');

    Route::post('password/reset', 'Auth\AdminPasswordController@reset');

    Route::get('/payment/failure' , 'ApplicationController@payment_failure')->name('payment.failure');

    // Videos

    Route::get('/live_videos', 'AdminController@live_videos')->name('live-videos.index');

    Route::get('/live-videos/list', 'AdminController@live_videos_history')->name('live-videos.history');

    Route::get('/live-videos/view/{id}', 'AdminController@live_videos_view')->name('live-videos.view');

    Route::get('/live-videos/payments', 'AdminController@live_video_payments')->name('live-videos.payments');


    // Exports tables

    Route::get('/users/export/', 'AdminExportController@users_export')->name('users.export');

    Route::get('/channels/export/', 'AdminExportController@channels_export')->name('channels.export');

    Route::get('/videos/export/', 'AdminExportController@videos_export')->name('video_tapes.export');

    Route::get('/subscription/payment/export/', 'AdminExportController@subscription_export')->name('subscription.export');

    Route::get('/payperview/payment/export/', 'AdminExportController@payperview_export')->name('payperview.export');

    Route::get('/live-videos/export/', 'AdminExportController@livevideos_export')->name('live-videos.export');

    Route::get('/live-video/payment/export/', 'AdminExportController@livevideo_payment_export')->name('livevideo-payment.export');

    // Languages

    Route::get('/languages/index', 'LanguageController@languages_index')->name('languages.index'); 

    Route::get('/languages/download/', 'LanguageController@languages_download')->name('languages.download'); 

    Route::get('/languages/create', 'LanguageController@languages_create')->name('languages.create');
    
    Route::get('/languages/edit', 'LanguageController@languages_edit')->name('languages.edit');

    Route::get('/languages/status', 'LanguageController@languages_status_change')->name('languages.status');   

    Route::post('/languages/save', 'LanguageController@languages_save')->name('languages.save');

    Route::get('/languages/delete', 'LanguageController@languages_delete')->name('languages.delete');

    Route::get('/languages/set_default', 'LanguageController@languages_set_default')->name('languages.set_default');


    // ============= branch v4.0-admin-coderevamp ================

    Route::get('/', 'NewAdminController@dashboard')->name('dashboard');

    // New Admin user methods

    Route::get('/users/index', 'NewAdminController@users_index')->name('users.index');

    Route::get('/users/create', 'NewAdminController@users_create')->name('users.create');

    Route::get('/users/edit', 'NewAdminController@users_edit')->name('users.edit');
    
    Route::post('/users/save', 'NewAdminController@users_save')->name('users.save');

    Route::get('/users/view', 'NewAdminController@users_view')->name('users.view');

    Route::get('/users/delete', 'NewAdminController@users_delete')->name('users.delete');

    Route::get('/users/status','NewAdminController@users_status_change')->name('users.status');

    Route::get('/users/verify/', 'NewAdminController@users_verify_status')->name('users.verify');

    Route::get('/users/channels/', 'NewAdminController@users_channels')->name('users.channels');

    Route::get('/users/history/', 'NewAdminController@users_history')->name('users.history');

    Route::get('users/type/','NewAdminController@users_type_list')->name('users.type');

    Route::get('/users/history/delete/', 'NewAdminController@users_history_delete')->name('users.history.delete');

    Route::get('/users/wishlist/', 'NewAdminController@users_wishlist')->name('users.wishlist');

    Route::get('/users/wishlist/delete/', 'NewAdminController@users_wishlist_delete')->name('users.wishlist.delete');    

    Route::get('/users/playlist/index', 'NewAdminController@playlists_index')->name('users.playlist.index');
    
    Route::get('/users/playlist/delete', 'NewAdminController@playlists_delete')->name('users.playlist.delete');

    Route::get('/users/playlist/view', 'NewAdminController@playlist_video')->name('users.playlist.view'); 

    Route::get('/users/playlist/video/remove', 'NewAdminController@playlists_video_remove')->name('users.playlist.video.delete');
    
    Route::get('/users/referral/index', 'NewAdminController@users_index')->name('users.referral.index');

    //User Subscriptions

    Route::get('/users/subscriptions/', 'NewAdminController@users_subscriptions')->name('users.subscriptions.plans');

    Route::get('/users/subscriptions/save', 'NewAdminController@users_subscription_save')->name('users.subscription.save');

    // New Admin Channeles methods begins

    Route::get('/channels', 'NewAdminController@channels_index')->name('channels.index');

    Route::get('/channels/create', 'NewAdminController@channels_create')->name('channels.create');

    Route::get('/channels/edit', 'NewAdminController@channels_edit')->name('channels.edit');    
    Route::post('/channels/save', 'NewAdminController@channels_save')->name('channels.save');

    Route::get('/channels/videos', 'NewAdminController@channels_videos')->name('channels.videos');

    Route::get('/channels/delete', 'NewAdminController@channels_delete')->name('channels.delete');

    Route::get('/channels/view', 'NewAdminController@channels_view')->name('channels.view');

    Route::get('/channels/status/change', 'NewAdminController@channels_status_change')->name('channels.status');

    Route::get('/channels/subscribers', 'NewAdminController@channels_subscribers')->name('channels.subscribers');

    Route::get('/channel/playlists/index', 'NewAdminController@channels_playlists_index')->name('channels.playlists.index');

    Route::get('/channel/playlists/create', 'NewAdminController@channels_playlists_create')->name('channels.playlists.create');

    Route::get('/channel/playlists/edit', 'NewAdminController@channels_playlists_edit')->name('channels.playlists.edit');

    Route::post('/channel/playlists/save', 'NewAdminController@channels_playlists_save')->name('channels.playlists.save');

    Route::get('/channel/playlists/view', 'NewAdminController@channels_playlists_view')->name('channels.playlists.view');

    Route::get('/channel/playlists/delete', 'NewAdminController@channels_playlists_delete')->name('channels.playlists.delete');

    Route::get('/channel/playlists/status', 'NewAdminController@channels_playlists_status')->name('channels.playlists.status.change');

    Route::get('/channel/playlist/video/remove', 'NewAdminController@channels_playlists_video_remove')->name('channels.playlist.video.delete');// 

    Route::get('playlist/videos', 'NewAdminController@playlist_videos')->name('playlists.videos');

    // Route::get('playlist/channels', 'NewAdminController@playlist_channels')->name('playlist.channels');

    // New Admin Channeles methods ends

    // New Admin Categories methods ends
    
    Route::get('/categories/index', 'NewAdminController@categories_index')->name('categories.index');

    Route::get('/categories/create', 'NewAdminController@categories_create')->name('categories.create');

    Route::get('/categories/edit', 'NewAdminController@categories_edit')->name('categories.edit');

    Route::post('/categories/save', 'NewAdminController@categories_save')->name('categories.save');

    Route::get('/categories/view', 'NewAdminController@categories_view')->name('categories.view');

    Route::get('/categories/delete', 'NewAdminController@categories_delete')->name('categories.delete');

    Route::get('/categories/status', 'NewAdminController@categories_status')->name('categories.status');

    Route::get('categories/videos', 'NewAdminController@categories_videos')->name('categories.videos');

    Route::get('categories/channels', 'NewAdminController@categories_channels')->name('categories.channels');

    // New Admin categories methods ends

    // New Admin tags methods begins

    Route::get('/tags', 'NewAdminController@tags_index')->name('tags.index');

    Route::post('/tags/save', 'NewAdminController@tags_save')->name('tags.save');

    Route::get('/tags/delete', 'NewAdminController@tags_delete')->name('tags.delete');

    Route::get('/tags/status', 'NewAdminController@tags_status_change')->name('tags.status');

    // Route::get('/tags/videos', 'NewAdminController@tags_videos')->name('tags.videos');
    
    Route::get('/tags/videos', 'NewAdminController@video_tapes_index')->name('tags.videos');

    // New Admin tags methods ends

    // New Admin coupons methods    

    Route::get('/coupons/index', 'NewAdminController@coupons_index')->name('coupons.index');

    Route::get('/coupons/create','NewAdminController@coupons_create')->name('coupons.create');

    Route::get('/coupons/edit','NewAdminController@coupons_edit')->name('coupons.edit');

    Route::post('/coupons/save','NewAdminController@coupons_save')->name('coupons.save');

    Route::get('/coupons/view', 'NewAdminController@coupons_view')->name('coupons.view');

    Route::get('/coupons/delete','NewAdminController@coupons_delete')->name('coupons.delete');

    Route::get('/coupon/status', 'NewAdminController@coupons_status_change')->name('coupons.status');

    // New Admin Coupons methods ends

    // New Admin AdsDetail methods begins

    Route::get('ads-details/index','NewAdminController@ads_details_index')->name('ads-details.index');
    
    Route::get('ads-details/create','NewAdminController@ads_details_create')->name('ads-details.create');

    Route::get('ads-details/edit','NewAdminController@ads_details_edit')->name('ads-details.edit');

    Route::post('ads-details/save','NewAdminController@ads_details_save')->name('ads-details.save');

    Route::get('ads-details/view','NewAdminController@ads_details_view')->name('ads-details.view');

    Route::get('ads-details/delete','NewAdminController@ads_details_delete')->name('ads-details.delete');

    Route::get('ads-details/status','NewAdminController@ads_details_status')->name('ads-details.status');

    
    Route::get('video-ads-details/index','NewAdminController@video_ads_details_index')->name('video-ads-details.index');
    
    Route::get('video-ads-details/create','NewAdminController@video_ads_details_create')->name('video-ads-details.create');

    Route::get('video-ads-details/edit','NewAdminController@video_ads_details_edit')->name('video-ads-details.edit');

    Route::post('video-ads-details/save','NewAdminController@video_ads_details_save')->name('video-ads-details.save');

    Route::get('video-ads-details/view','NewAdminController@video_ads_details_view')->name('video-ads-details.view');

    Route::get('video-ads-details/delete','NewAdminController@video_ads_details_delete')->name('video-ads-details.delete');

    Route::get('video-ads-details/status','NewAdminController@video_ads_details_status')->name('video-ads-details.status');

    // New Admin coupons methods ends
    
    // New Admin Account methods begins

    Route::get('help' , 'NewAdminController@help')->name('help');

    Route::get('/profile', 'NewAdminController@profile')->name('profile');

    Route::post('/profile/save', 'NewAdminController@profile_save')->name('profile.save');

    Route::post('/change/password', 'NewAdminController@change_password')->name('change.password');

    // New Admin Account methods ends

    // New Admin Pages methods begins

    Route::get('/pages', 'NewAdminController@pages_index')->name('pages.index');

    Route::get('/pages/edit/', 'NewAdminController@pages_edit')->name('pages.edit');

    Route::get('/pages/view/', 'NewAdminController@pages_view')->name('pages.view');

    Route::get('/pages/create', 'NewAdminController@pages_create')->name('pages.create');

    Route::post('/pages/create', 'NewAdminController@pages_save')->name('pages.save');

    Route::get('/pages/delete/', 'NewAdminController@pages_delete')->name('pages.delete');

    Route::get('/pages/status', 'NewAdminController@pages_status')->name('pages.status');

    // New Admin Pages methods ends


    // New Video CRUD start

    Route::get('/videos/create', 'NewAdminController@video_tapes_create')->name('video_tapes.create');

    Route::get('/videos/edit', 'NewAdminController@video_tapes_edit')->name('video_tapes.edit');

    Route::post('/videos/save', 'NewAdminController@video_tapes_save')->name('video_tapes.save');

    Route::get('/videos/images/{id}', 'NewAdminController@video_tapes_images')->name('video_tapes.images');

    Route::post('/videos/upload/image', 'NewAdminController@video_tapes_upload_image')->name('video_tapes.upload_image');

    Route::post('videos/save/default_img', 'NewAdminController@video_tapes_default_image_save')->name('video_tapes.save.default_img');

    Route::get('/videos/list', 'NewAdminController@video_tapes_index')->name('video_tapes.index');

    Route::get('/videos/view', 'NewAdminController@video_tapes_view')->name('video_tapes.view');

    Route::post('/videos/set-ppv/{id}', 'NewAdminController@video_tapes_set_ppv')->name('video_tapes.set-ppv');

    Route::get('/videos/delete', 'NewAdminController@video_tapes_delete')->name('video_tapes.delete');

    Route::get('/videos/status', 'NewAdminController@video_tapes_status')->name('video_tapes.status');

    Route::get('/videos/publish/{id}', 'NewAdminController@video_tapes_publish')->name('video_tapes.publish');

    Route::get('/videos/remove-ppv/{id}', 'NewAdminController@video_tapes_remove_ppv')->name('video_tapes.remove-ppv');

    Route::get('/videos/wishlist/{id}', 'NewAdminController@video_tapes_wishlist')->name('video_tapes.wishlist');

    Route::get('/videos/compression/complete','NewAdminController@video_tapes_compression_complete')->name('compress.status');

    // Spam Videos

    Route::get('/spam-videos', 'NewAdminController@spam_videos')->name('spam-videos');

    Route::get('/spam-videos/user-reports/{id}', 'NewAdminController@spam_videos_user_reports')->name('spam-videos.user-reports');

    Route::get('/spam/per-user-reports/{id}', 'AdminController@spam_videos_each_user_reports')->name('spam-videos.per-user-reports');

    Route::get('/unspam-video/{id}', 'AdminController@spam_videos_unspam')->name('spam-videos.unspam-video');

    // New Video CRUD end

    // New Admin Banner Ads methods begins   

    Route::get('banner-ads/index','NewAdminController@banner_ads_index')->name('banner_ads.index');

    Route::get('banner-ads/create','NewAdminController@banner_ads_create')->name('banner_ads.create');

    Route::get('banner-ads/edit','NewAdminController@banner_ads_edit')->name('banner_ads.edit');

    Route::post('banner-ads/save','NewAdminController@banner_ads_save')->name('banner_ads.save');

    Route::get('banner-ads/view','NewAdminController@banner_ads_view')->name('banner_ads.view');

    Route::get('banner-ads/delete','NewAdminController@banner_ads_delete')->name('banner_ads.delete');

    Route::get('banner-ads/status','NewAdminController@banner_ads_status_change')->name('banner_ads.status');

    Route::post('banner-ads/position','NewAdminController@banner_ads_position')->name('banner_ads.position');

    // New Admin Banner Ads methods ends

    // New Admin Banner Videos methods begins

    Route::get('/banner/videos', 'NewAdminController@banner_videos_index')->name('banner.videos.index');

    Route::get('/banner/videos/create', 'NewAdminController@banner_videos_create')->name('banner.videos.create');

    Route::get('/banner/videos/set', 'NewAdminController@banner_videos_set')->name('banner.videos.set');

    Route::get('/banner/videos/remove', 'NewAdminController@banner_videos_remove')->name('banner.videos.remove');

    Route::post('/banner/videos/save', 'NewAdminController@banner_videos_save')->name('banner.videos.save');

    // New Admin Banner Videos methods ends

    // New Admin Custom Live Videos methods begins

    Route::get('custom/live/videos', 'NewAdminController@custom_live_videos_index')->name('custom.live.index');

    Route::get('custom/live/create', 'NewAdminController@custom_live_videos_create')->name('custom.live.create');

    Route::get('custom/live/edit', 'NewAdminController@custom_live_videos_edit')->name('custom.live.edit');

    Route::post('custom/live/save', 'NewAdminController@custom_live_videos_save')->name('custom.live.save');

    Route::get('custom/live/delete', 'NewAdminController@custom_live_videos_delete')->name('custom.live.delete');

    Route::get('custom/live/view', 'NewAdminController@custom_live_videos_view')->name('custom.live.view');

    Route::get('custom/live/status', 'NewAdminController@custom_live_videos_status_change')->name('custom.live.status');

    // New Admin Custom Live Videos methods ends

    // YouTube Grapper 

    Route::get('youtube/{youtube_channel_id}' , 'version4AdminController@video_tapes_youtube_grapper_save')->name("youtube_downloader.videos_update");

    // Redeems  payouts (Direct | PayPal)
    Route::any('/redeems/payout/invoice', 'version4AdminController@redeems_payout_invoice')->name('users.payout.invoice');

    Route::post('redeems/payout/direct', 'version4AdminController@redeems_payout_direct')->name('users.payout.direct');

    Route::any('/redeems/payout/response', 'version4AdminController@redeems_payout_response')->name('users.payout.response');

    //  New Admin Subscriptions methods begins
 
    Route::get('/subscriptions/index', 'NewAdminController@subscriptions_index')->name('subscriptions.index');

    Route::get('/subscriptions/create', 'NewAdminController@subscriptions_create')->name('subscriptions.create');

    Route::get('/subscriptions/edit', 'NewAdminController@subscriptions_edit')->name('subscriptions.edit');

    Route::post('/subscriptions/create', 'NewAdminController@subscriptions_save')->name('subscriptions.save');

    Route::get('/subscriptions/delete', 'NewAdminController@subscriptions_delete')->name('subscriptions.delete');

    Route::get('/subscriptions/view', 'NewAdminController@subscriptions_view')->name('subscriptions.view');

    Route::get('/subscriptions/status', 'NewAdminController@subscriptions_status_change')->name('subscriptions.status');
    
    //  New Admin Subscriptions methods ends

    Route::get('/revenues/subscription/payments/{id?}' , 'NewAdminController@subscription_payments')->name('revenues.subscription-payments');

    Route::get('auto-renewa/subscribers', 'NewAdminController@auto_renewal_subscribers')->name('auto-renewal.subscribers');

    Route::get('auto-renewal/cancelled/subscribers', 'NewAdminController@auto_renewal_cancelled_subscribers')->name('auto-renewal.cancelled.subscribers');

    Route::post('/user/subscription/auto-renewal/disable', 'NewAdminController@user_subscription_auto_renewal_disable')->name('subscription.auto-renewal.disable');

    Route::get('/user/subscription/enable', 'NewAdminController@user_subscription_auto_renewal_enable')->name('subscription.auto-renewal.enable');

    // Redeems

    Route::get('/redeems', 'NewAdminController@user_redeem_requests')->name('users.redeems');

    // Payment details

    Route::get('revenues/dashboard' , 'NewAdminController@revenues')->name('revenues.dashboard');
    
    Route::get('revenues/ppv-payments' , 'NewAdminController@ppv_payments')->name('revenues.ppv_payments');

    Route::get('revenues/ppv-payments/view' , 'NewAdminController@ppv_payments_view')->name('revenues.ppv_payments.view');


    // Settings

    Route::get('settings' , 'NewAdminController@settings')->name('settings');

    Route::post('settings' , 'NewAdminController@settings_save')->name('settings.save');

    Route::post('settings/email' , 'NewAdminController@email_settings_process')->name('email.settings.save');
    
    Route::post('common-settings_save' , 'NewAdminController@common_settings_save')->name('common-settings.save');
     
    // Get ios control page
    Route::get('/ios-control','NewAdminController@ios_control')->name('ios_control');
    
    // Save the Admin control status 
    Route::get('/admin-control', 'NewAdminController@admin_control')->name('admin-control');
    Route::post('admin-control', 'ApplicationController@save_admin_control')->name('save.control');

    // Custom Push

    Route::get('/custom/push', 'NewAdminController@custom_push')->name('push');

    Route::post('/custom/push', 'NewAdminController@custom_push_process')->name('send.push');

    // Reviews

    Route::get('/reviews', 'NewAdminController@user_reviews')->name('reviews');

    Route::get('/reviews/delete', 'NewAdminController@user_reviews_delete')->name('reviews.delete');

    Route::get('ads-details/ad-status/', 'NewAdminController@ads_details_ad_status_change')->name('ads-details.ad-status-change');

     // Video Ads

    Route::get('/video-ads/index', 'NewAdminController@video_ads_index')->name('video_ads.index');

    Route::get('video-ads/create','NewAdminController@video_ads_create')->name('video_ads.create');

    Route::get('video-ads/edit','NewAdminController@video_ads_edit')->name('video_ads.edit');

    Route::post('video-ads/save','NewAdminController@video_ads_save')->name('video_ads.save');

    Route::get('video-ads/view','NewAdminController@video_ads_view')->name('video_ads.view');

    Route::get('video-ads/delete','NewAdminController@video_ads_delete')->name('video_ads.delete');

    Route::post('video-ads/inter-ads', 'NewAdminController@video_ads_inter_ads')->name('video_ads.inter-ads');
   
    Route::get('videos/assign-ad', 'NewAdminController@video_assign_ad')->name('videos.assign_ad');

    Route::post('video-ads/assign/ads', 'AdminController@video_ads_assign_ad')->name('video-ads.assign.ads');


    // Sub Admins CRUD Operations

    Route::get('sub_admins/index', 'NewAdminController@sub_admins_index')->name('sub_admins.index');

    Route::get('sub_admins/create', 'NewAdminController@sub_admins_create')->name('sub_admins.create');

    Route::get('sub_admins/edit', 'NewAdminController@sub_admins_edit')->name('sub_admins.edit');

    Route::get('sub_admins/view', 'NewAdminController@sub_admins_view')->name('sub_admins.view');

    Route::get('sub_admins/status', 'NewAdminController@sub_admins_status')->name('sub_admins.status');

    Route::get('sub_admins/delete', 'NewAdminController@sub_admins_delete')->name('sub_admins.delete');

    Route::post('sub_admins/save', 'NewAdminController@sub_admins_save')->name('sub_admins.save');

});

Route::group(['middleware' => ['SubAdminMiddleware', 'admin'], 'prefix' => 'subadmin', 'as' => 'subadmin.'], function () {

    Route::get('/', 'SubAdminController@dashboard')->name('dashboard');

    Route::get('subadmin/profile', 'SubAdminController@profile')->name('profile');

});

Route::group(['as' => 'user.'], function(){

//---------------------------CODE-BY-G
	Route::get('directupload', 'UserController@getUID')->name('directupload');
	Route::get('directupload/r4d', 'UserController@getR4D')->name('directupload.r4d');
	// Route::get('fin', 'UserController@fin')->name('fin');
//----------------------------
	
    Route::get('/', 'UserController@index')->name('dashboard');

    Route::get('/trending', 'UserController@trending')->name('trending');

    Route::get('channels', 'UserController@channels')->name('channel.list');
    
    Route::get('playlists', 'UserController@playlists_index')->name('playlists.index');


    Route::get('history', 'UserController@history')->name('history');

    Route::get('wishlist', 'UserController@wishlist')->name('wishlist');

    Route::get('channel/{id}', 'UserController@channel_view')->name('channel');

    Route::get('video/{id}', 'UserController@video_view')->name('single');

    // Wishlist

    Route::post('addWishlist', 'UserController@wishlist_create')->name('add.wishlist');

    Route::get('deleteWishlist', 'UserController@wishlist_delete')->name('delete.wishlist');


    // Comments

    Route::post('addComment', 'UserController@add_comment')->name('add.comment');


    Route::get('deleteHistory', 'UserController@delete_history')->name('delete.history');

    Route::post('addHistory', 'UserController@add_history')->name('add.history');


    Route::get('delete-video/{id}/{user_id}', 'UserController@delete_video')->name('delete_video');

    Route::get('ppv-video/{id}/{coupon_code?}','PaypalController@videoSubscriptionPay')->name('ppv-video-payment');

    Route::get('user/payment/video-status','PaypalController@getVideoPaymentStatus')->name('paypalstatus');


    Route::get('paypal_video','PaypalController@payPerVideo')->name('live_video_paypal');

    Route::get('user/payment/livevideo-status','PaypalController@getLiveVideoPaymentStatus')->name('live-paypalstatus');


    Route::get('login', 'Auth\AuthController@showLoginForm')->name('login.form');

    Route::post('login', 'Auth\AuthController@login')->name('login.post');

    Route::get('logout', 'Auth\AuthController@logout')->name('logout');

    // Registration Routes...
    Route::get('register', 'Auth\AuthController@showRegistrationForm')->name('register.form');

    Route::post('register', 'Auth\AuthController@register')->name('register.post');

    // Password Reset Routes...
    Route::get('password/reset/{token?}', 'Auth\PasswordController@showResetForm');

    Route::post('password/email', 'Auth\PasswordController@sendResetLinkEmail');

    Route::post('password/reset', 'Auth\PasswordController@reset');

    // Subscribe

    Route::get('/subscribe_channel', 'UserController@subscribe_channel')->name('subscribe.channel');

    Route::get('/unsubscribe_channel', 'UserController@unsubscribe_channel')->name('unsubscribe.channel');
    

    Route::get('/subscribers', 'UserController@channel_subscribers')->name('channel.subscribers');

    Route::post('take_snapshot/{rid}', 'UserController@setCaptureImage')->name('setCaptureImage');
    

    Route::get('profile', 'UserController@profile')->name('profile');

    Route::get('update/profile', 'UserController@update_profile')->name('update.profile');

    Route::post('update/profile', 'UserController@profile_save')->name('profile.save');
    
    Route::post('timezone_save', 'UserController@timezone_save')->name('timezone.save');

    Route::get('/profile/password', 'UserController@profile_change_password')->name('change.password');

    Route::post('/profile/password', 'UserController@profile_save_password')->name('profile.password');

    Route::post('/update/paypal_email', 'UserController@update_paypal_email')->name('update.paypal_email');

    // Delete Account

    Route::get('/delete/account', 'UserController@delete_account')->name('delete.account');

    Route::post('/delete/account', 'UserController@delete_account_process')->name('delete.account.process');


    // Channels

    Route::get('channel_create', 'UserController@channel_create')->name('create_channel');

    Route::post('save_channel', 'UserController@save_channel')->name('save_channel');

    Route::get('channel_edit/{id}', 'UserController@channel_edit')->name('channel_edit');

    Route::get('delete_channel', 'UserController@channel_delete')->name('delete.channel');



    // Report Spam Video

    Route::post('markSpamVideo', 'UserController@save_report_video')->name('add.spam_video');

    Route::get('unMarkSpamVideo/{id}', 'UserController@remove_report_video')->name('remove.report_video');

    Route::get('spamVideos', 'UserController@spam_videos')->name('spam-videos');

    Route::get('payment-video', 'UserController@payment_url')->name('payment_url');

    Route::post('live-videos/payment', 'UserController@live_videos_payment_url')->name('live-videos.payment-url');

    Route::get('stripe-payment-video', 'UserController@stripe_payment_video')->name('stripe_payment_video');
    

    Route::post('/save_video_payment/{id}', 'UserController@save_video_payment')->name('save.video-payment');

    Route::get('/remove_payper_view/{id}', 'UserController@remove_payper_view')->name('remove_pay_per_view');


    // Paypal Payment
    Route::get('/paypal/{id}/{coupon_code?}','PaypalController@pay')->name('paypal');

    Route::get('/user/payment/status','PaypalController@getPaymentStatus')->name('paypalstatus');

    Route::get('/live_videos', 'UserController@live_videos')->name('live_videos');

    Route::get('/subscriptions', 'UserController@subscriptions')->name('subscriptions');

    Route::get('/subscription/save/{s_id}/u_id/{u_id}', 'UserController@user_subscription_save')->name('subscription.save');
    

    // Video Upload

    Route::post('directory_create', 'UserController@directory_create')->name('directory_create');
    Route::post('directory_delete', 'UserController@directory_delete')->name('directory_delete');
    Route::post('directory_get', 'UserController@directory_get')->name('directory_get');
    Route::post('directory_files', 'UserController@directory_files')->name('directory_files');
    Route::post('move_files', 'UserController@move_files')->name('move_files');
    Route::post('delete_r4d_files', 'UserController@delete_r4d_files')->name('delete_r4d_files');
    
    Route::get('video-upload', 'UserController@video_upload')->name('video_upload');

    Route::get('upload_video', 'UserController@video_upload')->name('video_upload_old');

    Route::post('video_save', 'UserController@video_save')->name('video_save');

    Route::post('save_default_img', 'UserController@save_default_img')->name('save_default_img');

    Route::post('upload_video_image', 'UserController@upload_video_image')->name('upload_video_image');

    Route::post('ad_request', 'UserController@ad_request')->name('ad_request');

    Route::get('/delete/video/{id}', 'UserController@video_delete')->name('delete.video');
    Route::get('/delete/r4d_video/{id}', 'UserController@r4d_video_delete')->name('delete.video.r4d');
    Route::post('/delete/r4d_o_video', 'UserController@r4d_one_video_delete')->name('delete.video.o_r4d');

    Route::get('/edit_video/{id}', 'UserController@video_edit')->name('edit.video');
    Route::get('/edit_video/r4d/{id}', 'UserController@r4d_video_edit')->name('edit.r4d_video');

    Route::get('get_images/{id}', 'UserController@get_images')->name('get_images');

    Route::post('/like_video', 'UserController@likeVideo')->name('video.like');

    Route::post('/dis_like_video', 'UserController@disLikeVideo')->name('video.disLike');

    // Redeems

    Route::get('redeems/', 'UserController@redeems')->name('redeems');

    Route::get('send/redeem', 'UserController@send_redeem_request')->name('redeems.send.request');

    Route::get('redeem/request/cancel/{id?}', 'UserController@redeem_request_cancel')->name('redeems.request.cancel');


    Route::get('/card', 'UserController@card_details')->name('card.card_details');

    Route::post('/add-card', 'UserController@cards_add')->name('card.add_card');


    Route::post('/pay_tour','UserController@pay_tour')->name('card.card');

    Route::patch('/payment', 'UserController@payment_card_default')->name('card.default');

    Route::delete('/payment', 'UserController@payment_card_delete')->name('card.delete');

    Route::get('/stripe_payment', 'UserController@stripe_payment')->name('card.stripe_payment');

    Route::get('/ppv-stripe-payment', 'UserController@ppv_stripe_payment')->name('card.ppv-stripe-payment');

    Route::get('/subscribed-channels', 'UserController@subscribed_channels')->name('channels.subscribed');


    // Live videos

    Route::post('broadcast', 'UserController@broadcast')->name('live_video.broadcast');

    Route::get('broadcasting', 'UserController@broadcasting')->name('live_video.start_broadcasting');

    Route::get('stop-streaming', 'UserController@stop_streaming')->name('live_video.stop_streaming');

    Route::post('get_viewer_cnt','UserController@get_viewer_cnt')->name('live_video.get_viewer_cnt');

    Route::post('add/watch_count', 'UserController@watch_count')->name('add.watch_count');


    Route::post('/partialVideos', 'UserController@partialVideos')->name('video.get_videos');

    Route::post('/payment_mgmt_videos', 'UserController@payment_mgmt_videos')->name('video.payment_mgmt_videos');

    Route::get('invoice', 'UserController@invoice')->name('subscription.invoice');

    Route::get('ppv-invoice/{id}', 'UserController@ppv_invoice')->name('subscription.ppv_invoice');

    Route::get('subscription-type/{id}', 'UserController@pay_per_view')->name('subscription.pay_per_view');

    Route::get('pay-per-videos', 'UserController@payper_videos')->name('pay-per-videos');

    Route::post('payment-type/{id}', 'UserController@payment_type')->name('payment-type');

    Route::post('subscription/payment', 'UserController@subscription_payment')->name('subscription.payment');

    Route::get('subscription-success', 'UserController@payment_success')->name('subscription.success');

    Route::get('video-success/{id}', 'UserController@video_success')->name('video.success');

    Route::get('mychannels/list', 'UserController@my_channels')->name('channel.mychannel');

    Route::post('/forgot/password', 'UserController@forgot_password')->name('forgot.password');

    Route::get('subscription/history', 'UserController@subscription_history')->name('subscription.history');

    Route::get('ppv/history', 'UserController@ppv_history')->name('ppv.history');

    Route::get('/tags/list', 'UserController@tags_videos')->name('tags.videos');

    Route::post('/subscriptions/enable', 'UserController@subscriptions_autorenewal_enable')->name('subscriptions.enable-subscription');

    Route::post('/subscriptions/pause', 'UserController@subscriptions_autorenewal_pause')->name('subscriptions.pause-subscription');

    // Category view

    Route::get('category/{id}', 'UserController@categories_view')->name('categories.view');

    Route::post('/categories/videos', 'UserController@categories_videos')->name('categories.videos');

    Route::post('/categories/channels', 'UserController@categories_channels')->name('categories.channels');


    // Live Streaming video

    Route::get('/livetv' , 'UserController@custom_live_videos')->name('custom_live_videos.index');

    Route::get('/livetv/{id?}' , 'UserController@custom_live_videos_view')->name('custom_live_videos.view');

    Route::get('live/history', 'UserController@live_history')->name('live.history');

    Route::post('/live/videos/mgmt', 'UserController@live_mgmt_videos')->name('live.video.mgmt');

    Route::get('android/video', 'UserController@android_web_page')->name('android.video');


    // Settings page

    Route::get('/settings' , 'UserController@settings');

    Route::get('/live/video/invoice', 'UserController@live_videos_invoice')->name('live-video.invoice');

    // Notifications

    Route::any('notifications/', 'UserController@bell_notifications')->name('bell_notifications.index');

    Route::any('notifications/update', 'UserController@bell_notifications_update')->name('bell_notifications.update');

    Route::any('notifications/count', 'UserController@bell_notifications_count')->name('bell_notifications.count');

    // User Playlists

    // =============== v5.0 ==================

    Route::any('/playlists/', 'UserController@playlists')->name('playlists.index');

    Route::any('/playlists/save', 'UserController@playlists_save')->name('playlists.save');    

    Route::any('/playlists/delete', 'UserController@playlists_delete')->name('playlists.delete');
   
    Route::any('/playlists/view', 'UserController@playlists_view')->name('playlists.view');

    Route::any('/playlists/video_status', 'UserController@playlists_video_status')->name('playlists.video_status');

    Route::any('/playlists/video_remove', 'UserController@playlists_video_remove')->name('playlists.video_remove');

    Route::any('/playlists/play_all', 'UserController@playlists_play_all')->name('playlists.play_all');

    Route::get('/referrals', 'UserController@referrals')->name('referrals');

    Route::get('/referrals/view', 'UserController@referrals_view')->name('referrals.view');

    Route::post('/playlist_video_update', 'UserController@playlist_video_update')->name('playlist.video.update');

    Route::post('/playlist/save/video_add', 'UserController@playlist_save_video_add')->name('playlist.save.video_add');
    
    Route::any('/channel/playlists/save', 'UserController@channel_playlists_save')->name('channel.playlists.save');

    Route::any('/playlists/delete', 'UserController@playlists_delete')->name('playlists.delete');

    // Used for Ajax function
    Route::get('/channels_unsubscribe_subscribe', 'UserController@channels_unsubscribe_subscribe')->name('channels_unsubscribe_subscribe_ajax.channel');
    Route::get('/wishlist_operations', 'UserController@wishlist_operations')->name('wishlist_operations_ajax');

    Route::post('/update/paypal_email', 'UserController@update_paypal_email')->name('update.paypal_email');

    Route::get('check_user_live_video', 'UserController@check_user_live_video')->name('check_user_live_video');

    Route::get('erase_old_live_videos', 'UserController@erase_old_live_videos')->name('erase_old_live_videos');

});

Route::group(['prefix' => 'userApi'], function() {

        // Live Urls
    
    Route::post('get_live_url', 'UserApiController@get_live_url');

    Route::post('save_vod', 'UserApiController@save_vod');

    Route::post('live_videos', 'UserApiController@live_videos');

    Route::post('save_live_video', 'UserApiController@save_live_video');

    Route::post('/live_video', 'UserApiController@live_video');

    Route::post('save_chat', 'UserApiController@save_chat');

    Route::post('video_subscription', 'UserApiController@video_subscription');

    Route::post('get_viewers', 'UserApiController@get_viewers');

    Route::post('peerProfile', 'UserApiController@peerProfile');

    Route::get('checkVideoStreaming', 'UserApiController@checkVideoStreaming');

    Route::post('close_streaming', 'UserApiController@close_streaming');

    Route::post('stripe/live/ppv', 'UserApiController@stripe_live_ppv');

    Route::post('ppv_paypal', 'UserApiController@ppv_paypal');

    

    Route::post('/watch_count', 'UserController@watch_count');

    Route::post('/register','UserApiController@register');
    
    Route::post('/login','UserApiController@login');

    Route::get('/userDetails','UserApiController@user_details');

    Route::get('/userDetails','UserApiController@user_details');

    Route::post('/updateProfile', 'UserApiController@update_profile');

    Route::post('/forgotpassword', 'UserApiController@forgot_password');

    Route::post('/changePassword', 'UserApiController@change_password');

    Route::post('/deleteAccount', 'UserApiController@delete_account');

    Route::post('/settings', 'UserApiController@settings');

    // Videos and home

    Route::post('/home' , 'UserApiController@home');

    Route::post('/trending' , 'UserApiController@trending');
    
   // Route::post('/common' , 'UserApiController@common');

    Route::post('/single_video' , 'UserApiController@single_video');
    
   // Route::post('/singleVideo' , 'UserApiController@getSingleVideo');

    Route::post('/searchVideo' , 'UserApiController@search_video')->name('search-video');

    Route::post('/channel_videos', 'UserApiController@get_channel_videos');


    // Rating and Reviews

    Route::post('/userRating', 'UserApiController@user_rating');

    // Wish List

    Route::post('/addWishlist', 'UserApiController@wishlist_create');

    Route::post('/getWishlist', 'UserApiController@get_wishlist');

    Route::post('/deleteWishlist', 'UserApiController@wishlist_delete');

    // History

    Route::post('/addHistory', 'UserApiController@add_history');

    Route::post('getHistory', 'UserApiController@get_history');

    Route::post('/deleteHistory', 'UserApiController@delete_history');

    Route::get('/clearHistory', 'UserApiController@clear_history');

    // Index

    Route::post('/index', 'UserApiController@index');

    //Route::post('/redeems/list', 'UserApiController@redeems');

    // Route::post('/send_redeem_request', 'UserApiController@send_redeem_request');


    Route::post('/like_video', 'UserApiController@likeVideo');

    Route::post('/dis_like_video', 'UserApiController@disLikeVideo');

    // Cards 

    Route::post('card_details', 'UserApiController@card_details');

    Route::post('cards_add', 'UserApiController@cards_add');
    
    Route::post('payment_card_add', 'UserApiController@payment_card_add');

    Route::post('default_card', 'UserApiController@default_card');

    Route::post('delete_card', 'UserApiController@delete_card');

    Route::post('/stripe_payment', 'UserApiController@stripe_payment');
    

    // SubScriptions 

    Route::post('subscription_plans', 'UserApiController@subscription_plans');

    Route::post('subscribedPlans', 'UserApiController@subscribedPlans');

    Route::post('pay_now', 'UserApiController@pay_now');

    Route::post('/my_channels', 'UserApiController@my_channels');

    Route::post('/mychannel/list', 'UserApiController@user_channel_list');

    Route::post('subscribe_channel', 'UserApiController@subscribe_channel');

    Route::post('unsubscribe_channel', 'UserApiController@unsubscribe_channel');

    Route::post('subscribed_channels', 'UserApiController@subscribed_channels');

    Route::post('/add_spam', 'UserApiController@add_spam');

    Route::get('/spam-reasons', 'UserApiController@reasons');

    Route::post('remove_spam', 'UserApiController@remove_spam');

    Route::post('spam_videos', 'UserApiController@spam_videos_list');


    Route::post('channel/create', 'UserApiController@create_channel');

    Route::post('ppv_list', 'UserApiController@ppv_list');

    Route::post('/redeems/list', 'UserApiController@redeems');

    Route::post('redeem/request/list', 'UserApiController@redeem_request_list');

    Route::post('redeems/request', 'UserApiController@send_redeem_request');

    Route::post('redeem/request/cancel', 'UserApiController@redeem_request_cancel');

    Route::post('paypal_ppv', 'UserApiController@paypal_ppv');

    Route::post('stripe_ppv', 'UserApiController@stripe_ppv');

    Route::post('channel/edit', 'UserApiController@channel_edit');

    Route::post('channel/delete', 'UserApiController@channel_delete');

    Route::post('/video/erase-streaming', 'UserApiController@erase_streaming');

    //categories

    Route::post('categories/list', 'UserApiController@categories_list');

    Route::post('categories/view', 'UserApiController@categories_view');

    Route::post('categories/videos', 'UserApiController@categories_videos');

    Route::post('categories/channels/list', 'UserApiController@categories_channels_list');

    //Tags

    Route::post('tags/list', 'UserApiController@tags_list');

    Route::post('tags/view', 'UserApiController@tags_view');

    Route::post('tags/videos', 'UserApiController@tags_videos');


    // Automatic subscription with cancel

    Route::post('/cancel/subscription', 'UserApiController@autorenewal_cancel');

    Route::post('/autorenewal/enable', 'UserApiController@autorenewal_enable');

    // Coupons

    Route::post('/apply/coupon/subscription', 'UserApiController@apply_coupon_subscription');

    Route::post('apply/coupon/videos', 'UserApiController@apply_coupon_video_tapes');

    Route::post('apply/coupon/live-videos', 'UserApiController@apply_coupon_live_videos');   
     
    // User Playlists

    Route::post('/playlists/', 'UserApiController@playlists');

    Route::post('/playlists/save', 'UserApiController@playlists_save');

    Route::post('/playlists/delete', 'UserApiController@playlists_delete');

    Route::post('/playlists/view', 'UserApiController@playlists_view');

    Route::post('/playlists/video_status', 'UserApiController@playlists_video_status');

    Route::post('/playlists/video_remove', 'UserApiController@playlists_video_remove');


    // Notification

    Route::post('bell_notifications/', 'UserApiController@bell_notifications');

    Route::post('bell_notifications/update', 'UserApiController@bell_notifications_update');

    Route::post('bell_notifications/count', 'UserApiController@bell_notifications_count');

    Route::any('youtube-downloader' , 'UserApiController@video_tapes_youtube_grapper_save');

    // Referrals 

    Route::post('/referrals', 'UserApiController@referrals')->name('referrals');

    Route::post('/referrals_check', 'UserApiController@referrals_check');

    // Videos management

    Route::post('/video_tapes_revenues', 'UserApiController@video_tapes_revenues');

    Route::post('/video_tapes_status', 'UserApiController@video_tapes_status');

    Route::post('/video_tapes_ppv_status', 'UserApiController@video_tapes_ppv_status');

    Route::post('/video_tapes_delete', 'UserApiController@video_tapes_delete');

    Route::post('/video_tapes_view', 'V5UserApiController@video_tapes_view');

    Route::post('/playlists/video_save', 'UserApiController@playlists_video_save');

});


Route::get('/v5/index', 'V5UserController@index')->name('v5.index');

// 
// 
Route::group(['prefix' => 'userApi'], function(){

    Route::post('cards_add', 'V5UserApiController@cards_add');

    Route::post('v5/channels_index', 'V5UserApiController@channels_index');

    Route::post('v5/channels_view', 'V5UserApiController@channels_view');

    Route::post('v5/channel_based_videos', 'V5UserApiController@channel_based_videos');
    
    Route::post('v5/channels_subscribed', 'V5UserApiController@channels_subscribed');

});


// NEW COLLECTIONS:


Route::group(['prefix' => 'api/user'], function() {

    Route::post('project/configurations' , 'ApplicationController@configuration_site');

    Route::post('spam_reasons' , 'UserApiController@reasons'); // @todo change USERAPI

    /***
     *
     * User Account releated routs
     *
     */

    Route::post('/register','NewUserApiController@register');
    
    Route::post('/login','NewUserApiController@login');

    Route::post('/forgot_password', 'NewUserApiController@forgot_password');

    Route::group(['middleware' => 'NewUserApiVal'] , function() {

        Route::post('/profile','NewUserApiController@profile'); // 1

        Route::post('/update_profile', 'NewUserApiController@update_profile'); // 2

        Route::post('/change_password', 'NewUserApiController@change_password'); // 3

        Route::post('/delete_account', 'NewUserApiController@delete_account'); // 4

        Route::post('/push_notification_update', 'NewUserApiController@push_notification_status_change');  // 5

        Route::post('/email_notification_update', 'NewUserApiController@email_notification_status_change'); // 6

        Route::post('/logout', 'NewUserApiController@logout'); // 7

        // CARDS curd Operations

        Route::post('cards_add', 'NewUserApiController@cards_add'); // 15

        Route::post('cards_list', 'NewUserApiController@cards_list'); // 16

        Route::post('cards_delete', 'NewUserApiController@cards_delete'); // 17

        Route::post('cards_default', 'NewUserApiController@cards_default'); // 18

    });

    //configurations

    Route::post('notification/settings', 'NewUserApiController@notification_settings'); // 22

    // Static pages

    Route::get('pages/list' , 'NewUserApiController@static_pages_api');

    // Core api's

    Route::post('home', 'NewUserApiController@home');

    Route::post('trending', 'NewUserApiController@trending');

    Route::post('tags_based_videos', 'NewUserApiController@tags_based_videos');

    Route::post('categories_based_videos', 'NewUserApiController@categories_based_videos');

    Route::post('suggestions', 'NewUserApiController@suggestions');

    Route::post('video_tapes_search', 'NewUserApiController@video_tapes_search');

    Route::group(['middleware' => 'NewUserApiVal'] , function() {

        // Redeems management 

        Route::post('redeems', 'NewUserApiController@redeems');

        Route::post('redeems_request_send', 'NewUserApiController@redeems_request_send');

        Route::post('redeems_request_cancel', 'NewUserApiController@redeems_request_cancel');


        // Subscriptions management

        Route::post('subscriptions', 'NewUserApiController@subscriptions');

        Route::post('subscriptions_history', 'NewUserApiController@subscriptions_history');

        Route::post('subscriptions_payment_by_stripe', 'NewUserApiController@subscriptions_payment_by_stripe');

        Route::post('subscriptions_payment_by_paypal', 'NewUserApiController@subscriptions_payment_by_paypal');

        // Automatic subscription with cancel @todo change to newuserAPi

        Route::post('subscriptions_autorenewal_pause', 'NewUserApiController@subscriptions_autorenewal_pause');

        Route::post('subscriptions_autorenewal_enable', 'NewUserApiController@subscriptions_autorenewal_enable');

        // Coupon codes

        Route::post('coupon_codes_check', 'NewUserApiController@coupon_codes_check');

        // PPV Management

        Route::post('ppv_videos', 'NewUserApiController@ppv_videos');

        Route::post('ppv_payment_by_stripe', 'NewUserApiController@ppv_payment_by_stripe');

        Route::post('ppv_payment_by_paypal', 'NewUserApiController@ppv_payment_by_paypal');

        // Wishlist

        Route::post('wishlist' , 'NewUserApiController@wishlist_list');

        Route::post('wishlist_operations' , 'NewUserApiController@wishlist_operations');


        // Like & Dislikes

        Route::post('video_tapes_like' , 'NewUserApiController@video_tapes_like');

        Route::post('video_tapes_dislike' , 'NewUserApiController@video_tapes_dislike');

        // History Management

        Route::post('/video_tapes_history', 'NewUserApiController@video_tapes_history');

        Route::post('/video_tapes_history_add', 'NewUserApiController@video_tapes_history_add');

        Route::post('/video_tapes_history_remove', 'NewUserApiController@video_tapes_history_remove');

        // Spam Management

        Route::post('/spam_videos', 'NewUserApiController@spam_videos');

        Route::post('/spam_videos_add', 'NewUserApiController@spam_videos_add');

        Route::post('/spam_videos_remove', 'NewUserApiController@spam_videos_remove');

        // Bell notifications @todo change to new userapi

        Route::post('bell_notifications/', 'UserApiController@bell_notifications');

        Route::post('bell_notifications_update', 'UserApiController@bell_notifications_update');

        Route::post('bell_notifications_count', 'UserApiController@bell_notifications_count');
        
        // Channels @todo change to new userapi

        Route::post('channels_subscribed', 'V5UserApiController@channels_subscribed');

        Route::post('channels_unsubscribe_subscribe', 'NewUserApiController@channels_unsubscribe_subscribe');

        Route::post('channels_save', 'NewUserApiController@channels_save');

        Route::post('channels_delete', 'UserApiController@channel_delete');

        Route::post('channels_edit', 'UserApiController@channel_edit');

        // Referrals 

        Route::post('/referrals', 'UserApiController@referrals')->name('referrals');

        // Videos management @todo change to New user api

        Route::post('video_tapes_revenues', 'UserApiController@video_tapes_revenues');

        Route::post('video_tapes_status', 'UserApiController@video_tapes_status');

        Route::post('video_tapes_ppv_status', 'UserApiController@video_tapes_ppv_status');

        Route::post('video_tapes_delete', 'UserApiController@video_tapes_delete');

        Route::post('video_tapes_view', 'V5UserApiController@video_tapes_view');

        // Route::post('video_tapes_user_view', 'V5UserApiController@video_tapes_user_view');

        // Comments management

        Route::post('video_tapes_comments_save', 'NewUserApiController@video_tapes_comments_save');
        

        // User Playlists @todo change to New user api

        Route::post('playlists_save', 'NewUserApiController@playlists_save');

        Route::post('playlists_delete', 'NewUserApiController@playlists_delete');

        Route::post('playlists_video_status', 'NewUserApiController@playlists_video_status');

        Route::post('playlists_video_remove', 'NewUserApiController@playlists_video_remove');

    });

    Route::post('playlists/', 'NewUserApiController@playlists');

    Route::post('playlists_view', 'NewUserApiController@playlists_view');


    // Channels @todo change to new userapi

    Route::post('channels_index', 'V5UserApiController@channels_index');

    Route::post('channels_view', 'V5UserApiController@channels_view');

    Route::post('channel_based_videos', 'V5UserApiController@channel_based_videos');

    // Single page

    Route::post('video_tapes_user_view', 'NewUserApiController@video_tapes_user_view');

    Route::post('video_tapes_comments', 'NewUserApiController@video_tapes_comments');

    //categories @todo change to newuserAPi

    Route::post('categories_list', 'UserApiController@categories_list');

    Route::post('categories_view', 'NewUserApiController@categories_view');

    Route::post('categories_videos', 'UserApiController@categories_videos');

    Route::post('categories_channels_list', 'NewUserApiController@categories_channels_list');

    // Tags @todo change to newuserAPi

    Route::post('tags_list', 'UserApiController@tags_list');

    Route::post('tags_view', 'UserApiController@tags_view');

    Route::post('tags_videos', 'UserApiController@tags_videos');

    // Referrals 

    Route::post('/referrals_check', 'UserApiController@referrals_check');

});

Route::any('api_revamp_upgrade', 'ApplicationController@api_revamp_upgrade');
