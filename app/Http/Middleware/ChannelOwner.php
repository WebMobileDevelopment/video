<?php

namespace App\Http\Middleware;

use Closure;

use App\Channel;

use App\VideoTape;

use App\Helpers\Helper;

class ChannelOwner
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next) {

	    if($request->channel_id) {

	        $channel_details = Channel::find($request->channel_id);

	        if(!$channel_details) {

	            $error_messages = Helper::get_error_message(50102); $error_code = 50102;
	            
	            $response_array = ['success' => false, 'error_messages' => $error_messages, 'error_code' => $error_code];

	            return response()->json($response_array,200);
	        }

	        if($channel_details->user_id != $request->id) {

	            $error_messages = Helper::get_error_message(50103); $error_code = 50103;
	            
	            $response_array = ['success' => false, 'error_messages' => $error_messages, 'error_code' => $error_code];

	            return response()->json($response_array,200);

	        }


	    }

	    if($request->video_tape_id) {

	        $video_tape_details = VideoTape::find($request->video_tape_id);

	        if(!$video_tape_details) {
	            
	            $error_messages = Helper::get_error_message(1001); $error_code = 1001;

	            $response_array = ['success' => false, 'error_messages' => $error_messages, 'error_code' => $error_code];

	            return response()->json($response_array,200);
	        
	        }

	    }

	    return $next($request);
	
	}
}
