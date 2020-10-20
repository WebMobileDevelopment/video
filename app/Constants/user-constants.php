<?php

/*
|--------------------------------------------------------------------------
| Application Constants
|--------------------------------------------------------------------------
|
| 
|
*/

if(!defined('SAMPLE_ID')) define('SAMPLE_ID', 1);

if(!defined('TAKE_COUNT')) define('TAKE_COUNT', 6);

if(!defined('NO')) define('NO', 0);
if(!defined('YES')) define('YES', 1);

if(!defined('PAID')) define('PAID',1);
if(!defined('UNPAID')) define('UNPAID', 0);

if(!defined('DEVICE_ANDROID')) define('DEVICE_ANDROID', 'android');
if(!defined('DEVICE_IOS')) define('DEVICE_IOS', 'ios');
if(!defined('DEVICE_WEB')) define('DEVICE_WEB', 'web');

if(!defined('APPROVED')) define('APPROVED', 1);
if(!defined('DECLINED')) define('DECLINED', 0);

if(!defined('DEFAULT_TRUE')) define('DEFAULT_TRUE', true);
if(!defined('DEFAULT_FALSE')) define('DEFAULT_FALSE', false);

if(!defined('ADMIN')) define('ADMIN', 'admin');
if(!defined('USER')) define('USER', 'user');
if(!defined('PROVIDER')) define('PROVIDER', 'provider');


if(!defined('COD')) define('COD',   'COD');
if(!defined('PAYPAL')) define('PAYPAL', 'PAYPAL');
if(!defined('CARD')) define('CARD',  'CARD');

if(!defined('STRIPE_MODE_LIVE')) define('STRIPE_MODE_LIVE',  'live');
if(!defined('STRIPE_MODE_SANDBOX')) define('STRIPE_MODE_SANDBOX',  'sandbox');

//////// USERS

if(!defined('USER_TYPE_NORMAL')) define('USER_TYPE_NORMAL', 0);
if(!defined('USER_TYPE_PAID')) define('USER_TYPE_PAID', 1);

if(!defined('USER_PENDING')) define('USER_PENDING', 0);
if(!defined('USER_APPROVED')) define('USER_APPROVED', 1);
if(!defined('USER_DECLINED')) define('USER_DECLINED', 2);

if(!defined('USER_EMAIL_NOT_VERIFIED')) define('USER_EMAIL_NOT_VERIFIED', 0);
if(!defined('USER_EMAIL_VERIFIED')) define('USER_EMAIL_VERIFIED', 1);

if(!defined('USER_STEP_WELCOME')) define('USER_STEP_WELCOME', 0);
if(!defined('USER_STEP_COMPLETED')) define('USER_STEP_COMPLETED', 1);

//////// USERS END

/***** ADMIN CONTROLS KEYS ********/

if(!defined('ADMIN_CONTROL_ENABLED')) define("ADMIN_CONTROL_ENABLED", 1);

if(!defined('ADMIN_CONTROL_DISABLED')) define("ADMIN_CONTROL_DISABLED", 0);

if(!defined('NO_DEVICE_TOKEN')) define("NO_DEVICE_TOKEN", "NO_DEVICE_TOKEN");

// Notification settings

if(!defined('EMAIL_NOTIFICATION')) define('EMAIL_NOTIFICATION', 'email');

if(!defined('PUSH_NOTIFICATION')) define('PUSH_NOTIFICATION', 'push');


// Video status

if(!defined('ADMIN_VIDEO_APPROVED')) define('ADMIN_VIDEO_APPROVED', 1);

if(!defined('ADMIN_VIDEO_DECLINED')) define('ADMIN_VIDEO_DECLINED', 0);

// User status

if(!defined('USER_VIDEO_APPROVED')) define('USER_VIDEO_APPROVED', 1);

if(!defined('USER_VIDEO_DECLINED')) define('USER_VIDEO_DECLINED', 0);

// Published Status

if (!defined('VIDEO_PUBLISHED')) define('VIDEO_PUBLISHED', 1);

if (!defined('VIDEO_NOT_YET_PUBLISHED')) define('VIDEO_NOT_YET_PUBLISHED', 0);


// Channel status

if(!defined('ADMIN_CHANNEL_APPROVED')) define('ADMIN_CHANNEL_APPROVED', 1);

if(!defined('ADMIN_CHANNEL_DECLINED')) define('ADMIN_CHANNEL_DECLINED', 0);


if(!defined('USER_CHANNEL_APPROVED')) define('USER_CHANNEL_APPROVED', 1);

if(!defined('USER_CHANNEL_DECLINED')) define('USER_CHANNEL_DECLINED', 0);


// Redeeem Request Status

if(!defined('REDEEM_REQUEST_SENT')) define('REDEEM_REQUEST_SENT', 0);

if(!defined('REDEEM_REQUEST_PROCESSING')) define('REDEEM_REQUEST_PROCESSING', 1);

if(!defined('REDEEM_REQUEST_PAID')) define('REDEEM_REQUEST_PAID', 2);

if(!defined('REDEEM_REQUEST_CANCEL')) define('REDEEM_REQUEST_CANCEL', 3);
