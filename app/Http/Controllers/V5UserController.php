<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Helpers\Helper;

use Auth, DB, Validator, Setting, Exception, Log;

use App\Jobs\BellNotificationJob;


use App\Repositories\CommonRepository as CommonRepo;

use App\Repositories\VideoTapeRepository as VideoRepo;

use App\Repositories\V5Repository as V5Repo;


use App\User, App\Admin;

use App\Card, App\PayPerView;

use App\Subscription, App\UserPayment,  App\ChannelSubscription;

use App\Wishlist, App\Flag;

use App\Channel, App\Category, App\Tag, App\BannerAd;

use App\VideoTape, App\VideoTapeImage, App\VideoTapeTag;

use App\Playlist, App\PlaylistVideo;

use App\Page;


class V5UserController extends Controller {

    protected $UserApi;

    protected $skip, $take;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserApiController $api, Request $request) {

        $this->UserApi = $api;

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: 12);
    }

    /**
     * @method master_login()
     *
     * @uses To Activate Super user by admin
     *
     * @created Vithya R
     *
     * @updated 
     *
     * @param Object $request - User Details
     *
     * @return with Success/Failure Message
     */

    public function index(Request $request) {

        try {

            $request->request->add([ 
                'id'=> 146,
            ]);


            $data = V5Repo::home_first_section($request);

            dd($data);

            return view('new-user.index')->with('data', $data);


        } catch(Exception $e) {

            dd($e->getMessage());

            $error_messages = $e->getMessage();

            return back()->with('flash_error', $error_messages);
        }
    }


}