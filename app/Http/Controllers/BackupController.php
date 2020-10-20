<?php

namespace App\Http\Controllers;


class BackupController extends Controller {

    /**
     *
     * Route :     Route::get('/user/upgrade/{id}', 'AdminController@user_upgrade')->name('user.upgrade');
     *
     *
     *
     */
    public function user_upgrade($id) {

        if($user = User::find($id)) {

            // Check the user is exists in moderators table

            if(!$moderator = Moderator::where('email' , $user->email)->first()) {

                $moderator_user = new Moderator;

                $moderator_user->name = $user->name;

                $moderator_user->email = $user->email;

                if($user->login_by == "manual") {

                    $moderator_user->password = $user->password; 

                    $new_password = "Please use you user login Pasword.";

                } else {

                    $new_password = time();
                    $new_password .= rand();
                    $new_password = sha1($new_password);
                    $new_password = substr($new_password, 0, 8);
                    $moderator_user->password = \Hash::make($new_password);
                }

                $moderator_user->picture = $user->picture;
                $moderator_user->mobile = $user->mobile;
                $moderator_user->address = $user->address;
                $moderator_user->save();

                $email_data = array();

                $subject = tr('user_welcome_title' , Setting::get('site_name'));
                $page = "emails.moderator_welcome";
                $email = $user->email;
                $email_data['name'] = $moderator_user->name;
                $email_data['email'] = $moderator_user->email;
                $email_data['password'] = \Hash::make($new_password);

                Helper::send_email($page,$subject,$email,$email_data);

                $moderator = $moderator_user;

            }

            if($moderator) {

                $user->is_moderator = 1;
                $user->moderator_id = $moderator->id;
                $user->save();

                $moderator->is_user = 1;
                $moderator->save();

                return back()->with('flash_warning',tr('admin_user_upgrade'));

            } else  {
                return back()->with('flash_error',tr('admin_not_error'));    
            }

        } else {
            return back()->with('flash_error',tr('admin_not_error'));
        }

    }

    /**
     *
     *
     * Route :  Route::any('/upgrade/disable', 'AdminController@user_upgrade_disable')->name('user.upgrade.disable');
     *
     *
     */
    public function user_upgrade_disable(Request $request) {

        if($moderator = Moderator::find($request->moderator_id)) {

            if($user = User::find($request->id)) {
                $user->is_moderator = 0;
                $user->save();
            }

            $moderator->save();

            return back()->with('flash_success',tr('admin_user_upgrade_disable'));

        } else {

            return back()->with('flash_error',tr('admin_not_error'));
        }
    
    }


    public function decline_video($id) {
        
        $video = VideoTape::find($id);

        $video->is_approved = DEFAULT_FALSE;

        $video->save();

        if($video->is_approved == DEFAULT_TRUE){
            $message = tr('admin_not_video_approve');
        } else {
            $message = tr('admin_not_video_decline');
        }

        return back()->with('flash_success', $message);
    
    }

    public function ads_index() {

        $response = AdminRepo::ads_details_index()->getData();

        return view('admin.ads-details.index')->with('model', $response)->with('page', 'videos_ads')->with('sub_page', 'view-ads');        

    }




    // @ads_create - TODO Check

    public function ads_create_duplicate(Request $request) {

        $vModel = VideoTape::find($request->video_tape_id);

        $videoPath = '';

        $video_pixels = '';

        $preAd = new AdsDetail;

        $postAd = new AdsDetail;

        $betweenAd = new AdsDetail;

        $model = new VideoAd;

        if ($vModel) {

            $videoPath = $vModel->video_resize_path ? $vModel->video.','.$vModel->video_resize_path : $vModel->video;
            $video_pixels = $vModel->video_resolutions ? 'original,'.$vModel->video_resolutions : 'original';

        }

        $index = 0;

        $ads = AdsDetail::get(); 

        return view('admin.ads.create')->with('vModel', $vModel)->with('videoPath', $videoPath)->with('video_pixels', $video_pixels)->with('page', 'videos')->with('sub_page', 'videos')->with('index', $index)->with('model', $model)->with('preAd', $preAd)->with('postAd', $postAd)->with('betweenAd', $betweenAd)->with('ads', $ads);
    }



}
