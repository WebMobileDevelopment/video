<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Validator;

use App\Http\Controllers\Controller;

use Illuminate\Foundation\Auth\ThrottlesLogins;

use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

use App\Helpers\Helper;

use Auth;

use App\Admin;

use App\User;

use App\Channel;

use App\VideoTape;

use App\Repositories\VideoTapeRepository as VideoRepo;

class SubAdminController extends Controller
{
    /**
     * Function: dashboard()
     * 
     * @uses used to display analytics of the website
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *`
     * @param 
     *
     * @return view page
     */
    public function dashboard() {

        $admin_id = Auth::guard('admin')->user()->id;

        $admin_detals = Admin::find($admin_id);
        
        $admin_detals->token = Helper::generate_token();

        $admin_detals->token_expiry = Helper::generate_token_expiry();

        $admin_detals->save();
        
        $user_count = User::count();

        $channel_count = Channel::count();

        $video_count = VideoTape::count();
 
        $recent_videos = VideoRepo::admin_recently_added();

        $get_registers = get_register_count();

        $recent_users = get_recent_users();

        $total_revenue = total_revenue();

        $view = last_days(10);

        return view('new_admin.dashboard.dashboard')
        			->withPage('dashboard')
                    ->with('sub_page','')
                    ->with('user_count' , $user_count)
                    ->with('video_count' , $video_count)
                    ->with('channel_count' , $channel_count)
                    ->with('get_registers' , $get_registers)
                    ->with('view' , $view)
                    ->with('total_revenue' , $total_revenue)
                    ->with('recent_users' , $recent_users)
                    ->with('recent_videos' , $recent_videos);
    }
}
