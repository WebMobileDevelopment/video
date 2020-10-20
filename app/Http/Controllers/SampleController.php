<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class SampleController extends Controller
{
    public function video_notification(Request $request) {
    	return view('emails.video_notification');
    }

    public function bell_notifications(Request $request) {
        return view('ui.bell_notifications');
    }

    public function upload_video(Request $request) {
    	return view('user.videos.upload_video');
    }

}
