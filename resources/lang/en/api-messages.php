<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Messages Language Keys
	|--------------------------------------------------------------------------
	|
	|	This is the master keyword file. Any new keyword added to the website
	|	should go here first.
	|
	|
	*/


	/*********** COMMON ERRORS *******/

	'token_expiry' => 'Token Expired',
	'invalid_token' => 'Invalid Token ',
	'invalid_input' => 'Invalid Input',
	'without_id_token_user_accessing_request' => 'The requested action needs login.',

	'are_you_sure' => 'Are you sure?',
	'unknown_error_occured' => 'Unknown error occured',
	'something_went_wrong' => 'Sorry, Something went wrong, while functioning the current request!!',
	'no_result_found' => 'No Result Found',
	
	'invalid_email_address' => 'The email address is invalid!!',

	'stripe_not_configured' => 'The Stripe Payment is not configured Properly!!!',

	'login_success' => 'Successfully loggedin!!',
	'logout_success' => 'Successfully loggedout!!',

	'mail_send_failure' => 'The mail send process is failed!!!',
	'mail_not_configured' => 'The mail configuration failed!!!',
	'mail_sent_success' => 'Mail sent successfully',

	'forgot_password_email_verification_error' => 'The email verification not yet done Please check you inbox.',
	'forgot_password_decline_error' => 'The requested email is disabled by admin.',
	
	'password_not_correct' => 'Sorry, the password is not matched.',
	'password_mismatch' => 'The password doesn\'t match with existing record. Please try again!!',
	'password_change_success' => 'Password changed successfully!!',

	'account_delete_success' => 'Account deleted successfully!!!',

	'user_details_not_found' => 'The selected user not exists.',
	'provider_details_not_found' => 'The selected provider not exists.',

	/*********** COMMON ERRORS *******/

	// = = = = = = = = = USERS = = = = = = = = = = 

	'user_forgot_password_deny_for_social_login' => 'The forgot password only available for manual login.',

	'user_change_password_deny_for_social_login' => 'The change password only available for manual login.',

	'user_details_not_save' => 'User details not saved',

	'user_profile_update_success' => 'The Profile updated',

	'username_password_not_match' => 'Sorry, the username or password you entered do not match. Please try again',

	'user_login_decline' => 'Sorry, Your account has been disabled.',
	'user_not_verified' => 'Please verify your email address!!',
	'user_no_payment_mode' => 'Update the payment mode in account and try again!!!',

	'card_added_success' => 'Card Added successfully!!',
	'card_deleted_success' => 'Card Deleted successfully!!',
	'card_default_success' => 'Selected Card has been changed into Default Card', 
	'user_payment_mode_update_success' => 'Payment Mode updated successfully..!!!',

	'no_default_card' => 'Please add card and try again!!',

	'notification_enable' => 'Notification has been successfully enabled',
	'notification_disable' => 'Notification has been successfully disabled',

	// Wishlist

	'wishlist_delete_error' => 'The wishlist remove error',
	'wishlist_add_success' => 'The video added to wishlist',
	'wishlist_delete_success' => 'The wishlist video removed',
	'wishlist_clear_success' => 'Wishlist songs has been cleared successfully',

	// Subscriptions

	'subscription_not_found' => 'The subscription is not available now.',
	'subscription_payment_success' => 'Payment success..!!',
	'subscription_payment_error' => 'The subscription payment failed..!!',

	'subscription_autorenewal_pause_failed' => 'Auto-renewal pause failed.!!',
	'subscription_autorenewal_enable_failed' => 'Auto-renewal enable failed.!!',

	'subscription_autorenewal_paused' => 'Auto-renewal paused.',
	'subscription_autorenewal_enabled' => 'Auto-renewal enabled. Enjoy the videos without any interruption',

	'subscription_autorenewal_already_paused' => 'Auto-renewal already paused.',
	'subscription_autorenewal_already_enabled' => 'Auto-renewal already enabled.',


	'subscription_payment_details_not_found' => 'Subscription payment not found.!!',

	// Coupon codes

	'coupon_code_not_found' => 'The coupon code is not valid',
	'coupon_code_declined' => 'The coupon is invalid',
	'coupon_code_limit_exceeds' => 'Coupon Limit Reached..!, You can`t use the coupon code.',

	'create_a_new_coupon_row' => 'Create a new User coupon Details',
	'total_no_of_users_maximum_limit_reached' => 'Coupon Limit Reached..!, You can`t use the coupon code.',
	'coupon_code_per_user_limit_exceeds' => 'Your maxiumum limit is over..!',
	'add_no_of_times_used_coupon' => 'Already coupon row added, increase no of times used the coupon',
	'coupon_code_is_invaild' => 'The coupon code is invalid',
	'coupon_code_expired' => 'The coupon code is expired',

	'coupon_code_appiled' => 'The coupon code applied.',

	// History management

	'history_video_added' => 'The video added to history',
	'history_video_tape_removed' => 'The video removed from history',
	'history_cleared' => 'The history cleared',
	'history_failed' => 'The action failed',

	// Redeems Management

	'REDEEM_REQUEST_SENT' => 'Sent to Admin.',
	'REDEEM_REQUEST_PROCESSING' => 'On Progress',
	'REDEEM_REQUEST_PAID' => 'Paid',
	'REDEEM_REQUEST_CANCEL' => 'Cancelled',

	'redeem_disabled_by_admin' => 'Redeems is disabled by admin',
	'redeem_not_found' => 'Redeem record not found.',
	'redeem_wallet_empty' => 'Redeem wallet is empty',
	'redeem_minimum_limit_failed' => 'Earn the minimum limit and try again.!',
	'redeem_request_status_mismatch' => 'Redeem request status mismatched',

	'redeem_request_cancelled_success' => 'The request cancelled and credited the redeems to your wallet.',
	'redeem_request_send_success' => 'Your Redeem Request Sent to Admin.',

	// Spam videos

	'spam_video_add_failed' => 'Spam video add failed',
	'spam_video_remove_failed' => 'Spam video remove failed',

	'spam_video_added' => 'The video added to spam list',
	'spam_video_removed' => 'The video removed from spams',
	'spam_video_cleared' => 'The spam videos are cleared',

	// PPV module

	'ppv_is_not_enabled' => 'PPV is not applied for the video. You can watch now.',
	'ppv_channel_owner_no_need_to_pay' => 'You\'re the owner of channel',
	'ppv_already_paid' => 'You already paid for this video',

	'ppv_payment_success' => 'Payment success..!!',
	'ppv_payment_error' => 'The payment failed. Please try again',

	// Video Tapes

	'video_tape_not_found' => 'The video details is not found.',
	'video_tape_in_spam_list' => 'The video in spam list.',

	'video_tape_liked' => 'The video liked',
	'video_tape_like_removed' => 'The like removed',
	'video_tape_disliked' => 'The video disliked',
	'video_tape_dislike_removed' => 'The dislike removed',

	// Channels

	'channel_subscribed' => 'Subscription Added',
	'channel_unsubscribed' => 'Subscription Removed',

	'channel_not_found' => 'The channel is not available or not found.!',

	'channel_create_purchase_subscription_error' => 'Purchase a plan to create channel.',

	// Comments 

	'video_comment_success' => 'Comment added',
	'video_comment_failed' => 'Comment add failed',

	// Videos management

	'VIDEO_TYPE_UPLOAD' => 'Direct Upload',
	'VIDEO_TYPE_LIVE' => 'Live TV',
	'VIDEO_TYPE_YOUTUBE' => 'YouTube',
	'VIDEO_TYPE_OTHERS' => 'Others',
	
	'referral_code_share_message' => 'Join me on :otherkey! You\'ll get <%referral_commission%> off when you sign up using my code: <%referral_code%>. Signup now: ',


);
