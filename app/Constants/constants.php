<?php

if(!defined('SAMPLE_ID')) define('SAMPLE_ID', 1);

if(!defined('TAKE_COUNT')) define('TAKE_COUNT', 6);


if(!defined('DEFAULT_TRUE')) define('DEFAULT_TRUE', 1);

if(!defined('DEFAULT_FALSE')) define('DEFAULT_FALSE', 0);


if(!defined('WEB')) define('WEB' , 1);

if(!defined('DEVICE_WEB')) define('DEVICE_WEB', 'web');

if(!defined('DEVICE_ANDROID')) define('DEVICE_ANDROID', 'android');

if(!defined('DEVICE_IOS')) define('DEVICE_IOS', 'ios');

if(!defined('NO_INSTALL')) define('NO_INSTALL' , 0);



if(!defined('SYSTEM_CHECK')) define('SYSTEM_CHECK' , 1);

if(!defined('INSTALL_COMPLETE')) define('INSTALL_COMPLETE' , 2);


// FFMPEG Status

if(!defined('FFMPEG_INSTALLED')) define('FFMPEG_INSTALLED', 1);

if(!defined('FFMPEG_NOT_INSTALLED')) define('FFMPEG_NOT_INSTALLED', 0);


// Payment Constants

if(!defined('COD')) define('COD',   'cod');

if(!defined('PAYPAL')) define('PAYPAL', 'paypal');

if(!defined('CARD')) define('CARD',  'card');


if(!defined('RATINGS')) define('RATINGS', '0,1,2,3,4,5');


if(!defined('USER')) define('USER', 0);

if(!defined('NONE')) define('NONE', 0);

if(!defined('ADMIN')) define('ADMIN', 'admin');

if(!defined('SUBADMIN')) define('SUBADMIN', 'sub_admin');


// ON OFF STATUS

if(!defined('YES')) define('YES', 1);

if(!defined('NO')) define('NO', 0);

// ON OFF STATUS

if(!defined('APPROVED')) define('APPROVED', 1);

if(!defined('DECLINED')) define('DECLINED', 0);


if (!defined('USER_PENDING')) define('USER_PENDING',2);

if (!defined('USER_APPROVED')) define('USER_APPROVED',1);

if (!defined('USER_DECLINED')) define('USER_DECLINED',0);


if (!defined('USER_EMAIL_VERIFIED')) define('USER_EMAIL_VERIFIED',1);

if (!defined('USER_EMAIL_NOT_VERIFIED')) define('USER_EMAIL_NOT_VERIFIED',0);



if(!defined('PUSH_TO_ALL')) define('PUSH_TO_ALL', 0);

if(!defined('PUSH_TO_CHANNEL_SUBSCRIBERS')) define('PUSH_TO_CHANNEL_SUBSCRIBERS', 1);

if(!defined('PUSH_REDIRECT_HOME')) define('PUSH_REDIRECT_HOME', 1);

if(!defined('PUSH_REDIRECT_CHANNEL')) define('PUSH_REDIRECT_CHANNEL', 2);

if(!defined('PUSH_REDIRECT_SINGLE_VIDEO')) define('PUSH_REDIRECT_SINGLE_VIDEO', 3);


if(!defined('DEVICE_ANDROID')) define('DEVICE_ANDROID', 'android');

if(!defined('DEVICE_IOS')) define('DEVICE_IOS', 'ios');

if(!defined('DEVICE_WEB')) define('DEVICE_WEB', 'web');

// Channel settings 

if(!defined('CREATE_CHANNEL_BY_USER_ENABLED')) define('CREATE_CHANNEL_BY_USER_ENABLED' , 1);

if(!defined('CREATE_CHANNEL_BY_USER_DISENABLED')) define('CREATE_CHANNEL_BY_USER_DISENABLED' , 0);

// REDEEMS

if(!defined('REDEEM_OPTION_ENABLED')) define('REDEEM_OPTION_ENABLED', 1);

if(!defined('REDEEM_OPTION_DISABLED')) define('REDEEM_OPTION_DISABLED', 0);

// Redeeem Request Status

if(!defined('REDEEM_REQUEST_SENT')) define('REDEEM_REQUEST_SENT', 0);

if(!defined('REDEEM_REQUEST_PROCESSING')) define('REDEEM_REQUEST_PROCESSING', 1);

if(!defined('REDEEM_REQUEST_PAID')) define('REDEEM_REQUEST_PAID', 2);

if(!defined('REDEEM_REQUEST_CANCEL')) define('REDEEM_REQUEST_CANCEL', 3);

// Video Types

if(!defined('TYPE_PUBLIC')) define('TYPE_PUBLIC', 'public');

if(!defined('TYPE_PRIVATE')) define('TYPE_PRIVATE', 'private');

// Ad Types

if(!defined('PRE_AD')) define('PRE_AD', 1);

if(!defined('POST_AD')) define('POST_AD', 2);

if(!defined('BETWEEN_AD')) define('BETWEEN_AD', 3);

if(!defined('REPORT_VIDEO_KEY')) define('REPORT_VIDEO_KEY', 'REPORT_VIDEO');

if (!defined('IMAGE_RESOLUTIONS_KEY')) define('IMAGE_RESOLUTIONS_KEY', 'IMAGE_RESOLUTIONS');

if (!defined('VIDEO_RESOLUTIONS_KEY')) define('VIDEO_RESOLUTIONS_KEY', 'VIDEO_RESOLUTIONS');

// Ads status

if(!defined('ADS_ENABLED')) define('ADS_ENABLED', 1);

if(!defined('ADS_DISABLED')) define('ADS_DISABLED', 0);



// Subscription Type

if(!defined('ONE_TIME_PAYMENT')) define('ONE_TIME_PAYMENT', 1);

if(!defined('RECURRING_PAYMENT')) define('RECURRING_PAYMENT', 2);

// PPV - User Types

if(!defined('NORMAL_USER')) define('NORMAL_USER', 1);

if(!defined('PAID_USER')) define('PAID_USER', 2);

if(!defined('BOTH_USERS')) define('BOTH_USERS', 3);

// REQUEST STATE

if(!defined('REQUEST_STEP_PRE_1')) define('REQUEST_STEP_PRE_1', 5);

if(!defined('REQUEST_STEP_1')) define('REQUEST_STEP_1', 1);

if(!defined('REQUEST_STEP_2')) define('REQUEST_STEP_2', 2);

if(!defined('REQUEST_STEP_3')) define('REQUEST_STEP_3', 3);

if(!defined('REQUEST_STEP_FINAL')) define('REQUEST_STEP_FINAL', 4);


if(!defined('MAIN_VIDEO')) define('MAIN_VIDEO', 1);

if(!defined('TRAILER_VIDEO')) define('TRAILER_VIDEO', 2);

// VIDEO UPLOAD TYPES

if(!defined('VIDEO_TYPE_R4D')) define('VIDEO_TYPE_R4D', 5);

if(!defined('VIDEO_TYPE_UPLOAD')) define('VIDEO_TYPE_UPLOAD', 1);

if(!defined('VIDEO_TYPE_LIVE')) define('VIDEO_TYPE_LIVE', 2);

if(!defined('VIDEO_TYPE_YOUTUBE')) define('VIDEO_TYPE_YOUTUBE', 3);

if(!defined('VIDEO_TYPE_OTHERS')) define('VIDEO_TYPE_OTHERS', 4);


if(!defined('VIDEO_UPLOAD_TYPE_s3')) define('VIDEO_UPLOAD_TYPE_s3', 1);

if(!defined('VIDEO_UPLOAD_TYPE_DIRECT')) define('VIDEO_UPLOAD_TYPE_DIRECT', 2);


if(!defined('PUBLISH_NOW')) define('PUBLISH_NOW', 1);

if(!defined('PUBLISH_LATER')) define('PUBLISH_LATER', 2);



if(!defined('WISHLIST_EMPTY')) define('WISHLIST_EMPTY' , 0);

if(!defined('WISHLIST_ADDED')) define('WISHLIST_ADDED' , 1);

if(!defined('WISHLIST_REMOVED')) define('WISHLIST_REMOVED' , 2);


if(!defined('RECENTLY_ADDED')) define('RECENTLY_ADDED' , 'recent');

if(!defined('TRENDING')) define('TRENDING' , 'trending');

if(!defined('SUGGESTIONS')) define('SUGGESTIONS' , 'suggestion');

if(!defined('WISHLIST')) define('WISHLIST' , 'wishlist');

if(!defined('WATCHLIST')) define('WATCHLIST' , 'watchlist');

if(!defined('BANNER')) define('BANNER' , 'banner');

if(!defined('ALL_VIDEOS')) define('ALL_VIDEOS', 'All Videos');

if(!defined('JWT_SECRET')) define('JWT_SECRET', '12345');


if(!defined('PERCENTAGE')) define('PERCENTAGE',0);

if(!defined('ABSOULTE')) define('ABSOULTE',1);



// User status
if(!defined('NEW_USER')) define('NEW_USER', 0);
if(!defined('EXISTING_USER')) define('EXISTING_USER', 1);

// Subscription user tpe
if(!defined('SUBSCRIBED_USER')) define('SUBSCRIBED_USER', 1);
if(!defined('NON_SUBSCRIBED_USER')) define('NON_SUBSCRIBED_USER', 0);




// Admin status

// Video status

if(!defined('ADMIN_VIDEO_APPROVED_STATUS')) define('ADMIN_VIDEO_APPROVED_STATUS', 1);
if(!defined('ADMIN_VIDEO_DECLINED_STATUS')) define('ADMIN_VIDEO_DECLINED_STATUS', 0);

// Channel status

if(!defined('ADMIN_CHANNEL_APPROVED')) define('ADMIN_CHANNEL_APPROVED', 1);
if(!defined('ADMIN_CHANNEL_DECLINED')) define('ADMIN_CHANNEL_DECLINED', 0);

if(!defined('USER_CHANNEL_APPROVED')) define('USER_CHANNEL_APPROVED', 1);
if(!defined('USER_CHANNEL_DECLINED')) define('USER_CHANNEL_DECLINED', 0);

// User status

if(!defined('USER_VIDEO_APPROVED_STATUS')) define('USER_VIDEO_APPROVED_STATUS', 1);
if(!defined('USER_VIDEO_DECLINED_STATUS')) define('USER_VIDEO_DECLINED_STATUS', 0);




if(!defined('MY_CHANNEL')) define('MY_CHANNEL', 1);
if(!defined('OTHERS_CHANNEL')) define('OTHERS_CHANNEL', 0);


// Category Status

if(!defined('CATEGORY_APPROVE_STATUS')) define('CATEGORY_APPROVE_STATUS', 1);

if(!defined('CATEGORY_DECLINE_STATUS')) define('CATEGORY_DECLINE_STATUS', 0);

// Tag Status

if(!defined('TAG_APPROVE_STATUS')) define('TAG_APPROVE_STATUS', 1);

if(!defined('TAG_DECLINE_STATUS')) define('TAG_DECLINE_STATUS', 0);


// AUTORENEWAL STATUS

if(!defined('AUTORENEWAL_ENABLED')) define('AUTORENEWAL_ENABLED',0);

if(!defined('AUTORENEWAL_CANCELLED')) define('AUTORENEWAL_CANCELLED',1);


// Active plan

if(!defined('ACTIVE_PLAN')) define('ACTIVE_PLAN', 1);

if(!defined('NOT_ACTIVE_PLAN')) define('NOT_ACTIVE_PLAN',0);


// Paid status

if(!defined('PAID_STATUS')) define('PAID_STATUS', 1);

// Published Status

if (!defined('VIDEO_PUBLISHED')) define('VIDEO_PUBLISHED', 1);

if (!defined('VIDEO_NOT_YET_PUBLISHED')) define('VIDEO_NOT_YET_PUBLISHED', 0);


// Coupons applied status

if(!defined('COUPON_APPLIED')) define('COUPON_APPLIED',1);

if(!defined('COUPON_NOT_APPLIED')) define('COUPON_NOT_APPLIED', 0);

// Coupons status

if(!defined('COUPON_ACTIVE')) define('COUPON_ACTIVE',1);

if(!defined('COUPON_INACTIVE')) define('COUPON_INACTIVE', 0);


// watched status

if(!defined('NOT_YET_WATCHED')) define('NOT_YET_WATCHED', 0);

if(!defined('WATCHED')) define('WATCHED', 1);


// PPV Status

if(!defined('PPV_ENABLED')) define('PPV_ENABLED', 1);

if(!defined('PPV_DISABLED')) define('PPV_DISABLED', 0);


// Subscription Status

if(!defined('ACTIVE_PLANS')) define('ACTIVE_PLANS', 1);

if(!defined('INACTIVE_PLANS')) define('INACTIVE_PLANS', 0);



if(!defined('PLAYLIST_DISPLAY_PUBLIC')) define('PLAYLIST_DISPLAY_PUBLIC', "PUBLIC");

if(!defined('PLAYLIST_DISPLAY_PRIVATE')) define('PLAYLIST_DISPLAY_PRIVATE', "PRIVATE");

// VIDEO STATUS

if (!defined('VIDEO_STREAMING_STOPPED')) define('VIDEO_STREAMING_STOPPED' , 1);

if (!defined('VIDEO_STREAMING_ONGOING')) define('VIDEO_STREAMING_ONGOING' , 0);


// BROWSERS

if (!defined('WEB_SAFARI')) define('WEB_SAFARI', 'Safari');

if (!defined('WEB_OPERA')) define('WEB_OPERA', 'Opera');

if (!defined('WEB_FIREFOX')) define('WEB_FIREFOX', 'Firefox');

if (!defined('WEB_CHROME')) define('WEB_CHROME', 'Chrome');

if (!defined('WEB_IE')) define('WEB_IE', 'IE');

if (!defined('WEB_EDGE')) define('WEB_EDGE', 'Edge');

if (!defined('WEB_BLINK')) define('WEB_BLINK', 'Blink');

if (!defined('UNKNOWN')) define('UNKNOWN', 'Unknown');

if (!defined('ANDROID_BROWSER')) define('ANDROID_BROWSER', 'andriod');

if (!defined('IOS_BROWSER')) define('IOS_BROWSER', 'ios');


// ON OFF STATUS


if(!defined('PLAYLIST_TYPE_USER')) define('PLAYLIST_TYPE_USER', "USER");

if(!defined('PLAYLIST_TYPE_CHANNEL')) define('PLAYLIST_TYPE_CHANNEL', "CHANNEL");

// Bell notification
if(!defined('BELL_NOTIFICATION_NEW_VIDEO')) define('BELL_NOTIFICATION_NEW_VIDEO', "NEW_VIDEO");

if(!defined('BELL_NOTIFICATION_NEW_SUBSCRIBER')) define('BELL_NOTIFICATION_NEW_SUBSCRIBER', "NEW_SUBSCRIBER");

// Bell notification status
if(!defined('BELL_NOTIFICATION_STATUS_UNREAD')) define('BELL_NOTIFICATION_STATUS_UNREAD', 1);

if(!defined('BELL_NOTIFICATION_STATUS_READ')) define('BELL_NOTIFICATION_STATUS_READ', 2);


if(!defined('NO')) define('NO', 0);

if (!defined('APPROVED')) define('APPROVED',1);

if (!defined('DECLINED')) define('DECLINED',0);


// ========================new branch v4.0 admmin-coderevamp =================

if (!defined('WISHLIST_DELETE_ALL')) define('WISHLIST_DELETE_ALL', 1);

// These constants are used identify the home page api types http://prntscr.com/mahza1

if(!defined('API_PAGE_TYPE_HOME')) define('API_PAGE_TYPE_HOME', 'HOME');

if(!defined('API_PAGE_TYPE_SERIES')) define('API_PAGE_TYPE_SERIES', "SERIES");

if(!defined('API_PAGE_TYPE_FLIMS')) define('API_PAGE_TYPE_FLIMS', "FLIMS");

if(!defined('API_PAGE_TYPE_KIDS')) define('API_PAGE_TYPE_KIDS', "KIDS");

if(!defined('API_PAGE_TYPE_CATEGORY')) define('API_PAGE_TYPE_CATEGORY', "CATEGORY");

if(!defined('API_PAGE_TYPE_SUB_CATEGORY')) define('API_PAGE_TYPE_SUB_CATEGORY', "SUB_CATEGORY");

if(!defined('API_PAGE_TYPE_GENRE')) define('API_PAGE_TYPE_GENRE', "GENRE");



if(!defined('URL_TYPE_WISHLIST')) define('URL_TYPE_WISHLIST', 'URL_TYPE_WISHLIST');

if(!defined('URL_TYPE_NEW_RELEASE')) define('URL_TYPE_NEW_RELEASE', 'URL_TYPE_NEW_RELEASE');

if(!defined('URL_TYPE_TRENDING')) define('URL_TYPE_TRENDING', 'URL_TYPE_TRENDING');

if(!defined('URL_TYPE_SUGGESTION')) define('URL_TYPE_SUGGESTION', 'URL_TYPE_SUGGESTION');

if(!defined('URL_TYPE_ORIGINAL')) define('URL_TYPE_ORIGINAL', 'URL_TYPE_ORIGINAL');

if(!defined('URL_TYPE_CATEGORY')) define('URL_TYPE_CATEGORY', 'URL_TYPE_CATEGORY');

if(!defined('URL_TYPE_SUB_CATEGORY')) define('URL_TYPE_SUB_CATEGORY', 'URL_TYPE_SUB_CATEGORY');

if(!defined('URL_TYPE_GENRE')) define('URL_TYPE_GENRE', 'URL_TYPE_GENRE');

if(!defined('URL_TYPE_CAST_CREW')) define('URL_TYPE_CAST_CREW', 'URL_TYPE_CAST_CREW');


if(!defined('CHANNEL_SUBSCRIBED')) define('CHANNEL_SUBSCRIBED', 1);

if(!defined('CHANNEL_UNSUBSCRIBED')) define('CHANNEL_UNSUBSCRIBED', 0);

if(!defined('CHANNEL_OWNER')) define('CHANNEL_OWNER', 2);

if(!defined('VIEW_TYPE_OWNER')) define('VIEW_TYPE_OWNER', 'owner');

if(!defined('VIEW_TYPE_VIEWER')) define('VIEW_TYPE_VIEWER', 'viewer');


if(!defined('PAY_WATCH_VIDEO')) define('PAY_WATCH_VIDEO', 1);

if(!defined('FREE_VIDEO')) define('FREE_VIDEO', 0);


