<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\User;

use App\subscription;

use App\UserPayment;

use App\VideoTape;

use App\LiveVideo;

use App\LiveVideoPayment;

use App\Channel;

use App\PayPerView;

use Excel;

use Exception;

use Setting;

class AdminExportController extends Controller
{
    //

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');  
    }
    
    /**
	 * Function Name: users_export()
	 *
	 * @uses used export the users details into the selected format
	 *
	 * @created Maheswari S
	 *
	 * @edited Maheswari S
	 *
	 * @param string format (xls, csv or pdf)
	 *
	 * @return redirect users page with success or error message 
	 */
    public function users_export(Request $request) {

    	try {

    		// Get the admin selected format for download

    		$format = $request->format ? $request->format : 'xls';

	    	$download_filename = routefreestring(Setting::get('site_name'))."-".date('Y-m-d-h-i-s')."-".uniqid();

	    	$result = User::orderBy('created_at' , 'desc')->get();

	    	// Check the result is not empty

	    	if(count($result) == 0) {
            	
            	return redirect()->route('admin.users')->with('flash_error' , tr('no_user_found'));

	    	}

	    	Excel::create($download_filename, function($excel) use($result)
		    {

		        $excel->sheet('USERS', function($sheet) use($result) 
		        {

	 				$sheet->row(1, function($first_row) {

	                    $first_row->setAlignment('center');

	                });

	                $sheet->setHeight(50);

					$sheet->setAutoSize(true);

					$sheet->setAllBorders('thin');
			        
			        $sheet->setFontFamily('Comic Sans MS');

					$sheet->setFontSize(15);
				
					// Set height for a single row

		    		$sheet->setAutoFilter();

		    		$title = tr('users_management');
		    		
			        $sheet->loadView('exports.users')->with('data' , $result)->with('title' , $title);

			    });
		    
		    })->export($format);

            return redirect()->route('admin.users')->with('flash_success' , tr('export_success'));

		} catch(\Exception $e) {

            $error = $e->getMessage();

            return redirect()->route('admin.users')->with('flash_error' , $error);

        }

    }

    /**
	 * Function Name: channels_export()
	 *
	 * @uses used export the channels details into the selected format
	 *
	 * @created Maheswari S
	 *
	 * @edited Maheswari S
	 *
	 * @param string format (xls, csv or pdf)
	 *
	 * @return redirect users page with success or error message 
	 */
    public function channels_export(Request $request) {

    	try {

    		// Get the admin selected format for download

    		$format = $request->format ? $request->format : 'xls';

	    	$download_filename = routefreestring(Setting::get('site_name'))."-".date('Y-m-d-h-i-s')."-".uniqid();

	    	$result = Channel::orderBy('created_at' , 'desc')->get();

	    	// Check the result is not empty

	    	if(count($result) == 0) {
            	
            	return redirect()->route('admin.channels')->with('flash_error' , tr('channel_not_found'));

	    	}

	    	Excel::create($download_filename, function($excel) use($result)
		    {
		        $excel->sheet('CHANNEL', function($sheet) use($result) 
		        {

	 				$sheet->row(1, function($first_row) {
	                    $first_row->setAlignment('center');

	                });

	                $sheet->setHeight(50);

					$sheet->setAutoSize(true);

					$sheet->setAllBorders('thin');
			        
			        $sheet->setFontFamily('Comic Sans MS');

					$sheet->setFontSize(15);
				
					// Set height for a single row

		    		$sheet->setAutoFilter();

		    		$title = tr('channel_management');

			        $sheet->loadView('exports.channels')->with('data' , $result)->with('title' , $title);

			    });
		    
		    })->export($format);

            return redirect()->route('admin.channels')->with('flash_success' ,tr('export_success'));

		} catch(\Exception $e) {

            $error = $e->getMessage();

            return redirect()->route('admin.channels')->with('flash_error' , $error);

        }

    }

    /**
	 * Function Name: videos_export()
	 *
	 * @uses used export the videos details into the selected format
	 *
	 * @created Maheswari S
	 *
	 * @edited Maheswari S
	 *
	 * @param string format (xls, csv or pdf)
	 *
	 * @return redirect users page with success or error message 
	 */
    public function videos_export(Request $request) {

    	try {

    		// Get the admin selected format for download

    		$format = $request->format ? $request->format : 'xls';

	    	$download_filename = routefreestring(Setting::get('site_name'))."-".date('Y-m-d-h-i-s')."-".uniqid();

	    	$result = VideoTape::orderBy('created_at' , 'desc')->get();

	    	// Check the result is not empty

	    	if(count($result) == 0) {
            	
            	return redirect()->route('admin.videos.index')->with('flash_error' , tr('video_not_found_error'));

	    	}

	    	Excel::create($download_filename, function($excel) use($result)
		    {
		        $excel->sheet('VIDEO', function($sheet) use($result) 
		        {

	 				$sheet->row(1, function($first_row) {
	                    $first_row->setAlignment('center');

	                });

	                $sheet->setHeight(50);

					$sheet->setAutoSize(true);

					$sheet->setAllBorders('thin');
			        
			        $sheet->setFontFamily('Comic Sans MS');

					$sheet->setFontSize(15);
				
					// Set height for a single row

		    		$sheet->setAutoFilter();

		    		$title = tr('video_management');

			        $sheet->loadView('exports.videos')->with('data' , $result)->with('title' , $title);

			    });
		    
		    })->export($format);

            return redirect()->route('admin.videos.index')->with('flash_success' ,tr('export_success'));

		} catch(\Exception $e) {

            $error = $e->getMessage();

            return redirect()->route('admin.videos.index')->with('flash_error' , $error);

        }

    }

    /**
	 * Function Name: subscription_export()
	 *
	 * @uses used export the subscription details into the selected format
	 *
	 * @created Maheswari S
	 *
	 * @edited Maheswari S
	 *
	 * @param string format (xls, csv or pdf)
	 *
	 * @return redirect users page with success or error message 
	 */
    public function subscription_export(Request $request) {

    	try {

    		// Get the admin selected format for download

    		$format = $request->format ? $request->format : 'xls';

	    	$download_filename = routefreestring(Setting::get('site_name'))."-".date('Y-m-d-h-i-s')."-".uniqid();

	    	$result = UserPayment::orderBy('created_at' , 'desc')->get();

	    	// Check the result is not empty

	    	if(count($result) == 0) {
            	
            	return redirect()->route('admin.revenues.subscription-payments')->with('flash_error' , tr('subscription_payment_not_found'));

	    	}

	    	Excel::create($download_filename, function($excel) use($result)
		    {
		        $excel->sheet('SUBSCRIPTION', function($sheet) use($result) 
		        {

	 				$sheet->row(1, function($first_row) {
	                    $first_row->setAlignment('center');

	                });

	                $sheet->setHeight(50);

					$sheet->setAutoSize(true);

					$sheet->setAllBorders('thin');
			        
			        $sheet->setFontFamily('Comic Sans MS');

					$sheet->setFontSize(15);
				
					// Set height for a single row

		    		$sheet->setAutoFilter();

		    		$title = tr('subscription_management');

			        $sheet->loadView('exports.subscription')->with('data' , $result)->with('title' , $title);

			    });
		    
		    })->export($format);

            return redirect()->route('admin.revenues.subscription-payments')->with('flash_success' ,tr('export_success'));

		} catch(\Exception $e) {

            $error = $e->getMessage();

            return redirect()->route('admin.revenues.subscription-payments')->with('flash_error' , $error);

        }

    }

    /**
	 * Function Name: payperview_export()
	 *
	 * @uses used export the video payperview details into the selected format
	 *
	 * @created Maheswari S
	 *
	 * @edited Maheswari S
	 *
	 * @param string format (xls, csv or pdf)
	 *
	 * @return redirect users page with success or error message 
	 */
    public function payperview_export(Request $request) {

    	try {

    		// Get the admin selected format for download

    		$format = $request->format ? $request->format : 'xls';

	    	$download_filename = routefreestring(Setting::get('site_name'))."-".date('Y-m-d-h-i-s')."-".uniqid();

	    	$result = PayPerView::orderBy('created_at' , 'desc')->get();

	    	// Check the result is not empty

	    	if(count($result) == 0) {
            	
            	return redirect()->route('admin.revenues.ppv_payments')->with('flash_error' , tr('ppv_payment_not_found'));

	    	}

	    	Excel::create($download_filename, function($excel) use($result)
		    {
		        $excel->sheet('PAYPERVIEW', function($sheet) use($result) 
		        {

	 				$sheet->row(1, function($first_row) {
	                    $first_row->setAlignment('center');

	                });

	                $sheet->setHeight(50);

					$sheet->setAutoSize(true);

					$sheet->setAllBorders('thin');
			        
			        $sheet->setFontFamily('Comic Sans MS');

					$sheet->setFontSize(15);
				
					// Set height for a single row

		    		$sheet->setAutoFilter();

		    		$title = tr('payperview_management');

			        $sheet->loadView('exports.payperview')->with('data' , $result)->with('title' , $title);

			    });
		    
		    })->export($format);

            return redirect()->route('admin.revenues.ppv_payments')->with('flash_success',tr('export_success'));

		} catch(\Exception $e) {

            $error = $e->getMessage();

            return redirect()->route('admin.revenues.ppv_payments')->with('flash_error' , $error);

        }

    }

    /**
	 * Function Name: livevideos_export()
	 *
	 * @usage used export the live  video details into the selected format
	 *
	 * @created Maheswari
	 *
	 * @edited Maheswari
	 *
	 * @param string format (xls, csv or pdf)
	 *
	 * @return redirect users page with success or error message 
	 */
    public function livevideos_export(Request $request) {

    	try {

    		// Get the admin selected format for download

    		$format = $request->format ? $request->format : 'xls';

	    	$download_filename = routefreestring(Setting::get('site_name'))."-".date('Y-m-d-h-i-s')."-".uniqid();

	    	$result = LiveVideo::orderBy('created_at' , 'desc')->get();

	    	// Check the result is not empty

	    	if(count($result) == 0) {
            	
            	return redirect()->route('admin.live-videos.history')->with('flash_error' , tr('no_results_found'));

	    	}

	    	Excel::create($download_filename, function($excel) use($result)
		    {
		        $excel->sheet('LIVEVIDEO', function($sheet) use($result) 
		        {

	 				$sheet->row(1, function($first_row) {
	                    $first_row->setAlignment('center');

	                });

	                $sheet->setHeight(50);

					$sheet->setAutoSize(true);

					$sheet->setAllBorders('thin');
			        
			        $sheet->setFontFamily('Comic Sans MS');

					$sheet->setFontSize(15);
				
					// Set height for a single row

		    		$sheet->setAutoFilter();

		    		$title = tr('live_videos_management');

			        $sheet->loadView('exports.live-videos')->with('data' , $result)->with('title' , $title);

			    });
		    
		    })->export($format);

            return redirect()->route('admin.live-videos.history')->with('flash_success' , tr('export_success'));

		} catch(\Exception $e) {

            $error = $e->getMessage();

            return redirect()->route('admin.live-videos.history')->with('flash_error' , $error);

        }

    }

    /**
	 * Function Name: livevideo_payment_export()
	 *
	 * @usage used export the live video payment details into the selected format
	 *
	 * @created Maheswari
	 *
	 * @edited Maheswari
	 *
	 * @param string format (xls, csv or pdf)
	 *
	 * @return redirect users page with success or error message 
	 */
    public function livevideo_payment_export(Request $request) {

    	try {

    		// Get the admin selected format for download

    		$format = $request->format ? $request->format : 'xls';

	    	$download_filename = routefreestring(Setting::get('site_name'))."-".date('Y-m-d-h-i-s')."-".uniqid();

	    	$result = LiveVideoPayment::orderBy('created_at' , 'desc')->get();

	    	// Check the result is not empty

	    	if(count($result) == 0) {
            	
            	return redirect()->route('admin.live.videos.payments')->with('flash_error' , tr('no_results_found'));

	    	}

	    	Excel::create($download_filename, function($excel) use($result)
		    {
		        $excel->sheet('LIVEVIDEO', function($sheet) use($result) 
		        {

	 				$sheet->row(1, function($first_row) {
	                    $first_row->setAlignment('center');

	                });

	                $sheet->setHeight(50);

					$sheet->setAutoSize(true);

					$sheet->setAllBorders('thin');
			        
			        $sheet->setFontFamily('Comic Sans MS');

					$sheet->setFontSize(15);
				
					// Set height for a single row

		    		$sheet->setAutoFilter();

		    		$title = tr('live_videos_management');

			        $sheet->loadView('exports.livevideo-payments')->with('data' , $result)->with('title' , $title);

			    });
		    
		    })->export($format);

            return redirect()->route('admin.live.videos.payments')->with('flash_success' , tr('export_success'));

		} catch(\Exception $e) {

            $error = $e->getMessage();

            return redirect()->route('admin.live.videos.payments')->with('flash_error' , $error);

        }

    }
}
