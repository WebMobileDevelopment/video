@extends( 'layouts.user' )

@section('styles')

<style type="text/css">
video {
	
	width: 100%;

	height: 100%;	
}
</style>

<link rel="stylesheet" type="text/css" href="{{asset('assets/css/video.css')}}">
@endsection


@section('meta_tags')

<meta property="og:locale" content="en_US" />
<meta property="og:type" content="article" />
<meta property="og:title" content="{{$data->title}}" />
<meta property="og:description" content="{{$data->description}}" />
<meta property="og:url" content="" />
<meta property="og:site_name" content="@if(Setting::get('site_name')) {{Setting::get('site_name') }} @else {{tr('site_name')}} @endif" />
<meta property="og:image" content="{{$data->snapshot}}" />

<meta name="twitter:card" content="summary"/>
<meta name="twitter:description" content="{{$data->description}}"/>
<meta name="twitter:title" content="{{$data->title}}"/>
<meta name="twitter:image:src" content="{{$data->snapshot}}"/>

@endsection

@section('content')

<div class="y-content">

	<div class="row content-row">

		@include('layouts.user.nav')

		<div class="page-inner col-sm-9 col-md-10">

			@include('notification.notify')

			<div class="slide-area recom-area abt-sec" ng-app="liveApp" >

				<div class="row live_video">

					<div class="col-lg-8" ng-controller="streamCtrl">

					<div style="display: none">
                        
                        <input type="text" id="room-id" size=20 value="{{$data->virtual_id}}">

                        <button id="join-room">Join Room</button>
                        <button id="open-or-join-room">Auto Open Or Join Room</button>

                      </div>

                      	<div style="display: none">
                            <video id="videoInput" autoplay></video>
                            <textarea id="rtpSdp">v=0
                              o=- 0 0 IN IP4 {{Setting::get('wowza_ip_address')}}
                              s=Kurento
                              c=IN IP4 {{Setting::get('wowza_ip_address')}}
                              t=0 0
                              m=video {{$data->port_no}} RTP/AVP 100
                              a=rtpmap:100 H264/90000
                            </textarea>
                        </div>
						

						<div class="live_img" id="videos-container" room="{{$data->id}}">
							<!-- <img src="{{asset('images/mobile-camera.jpg')}}" width="100%" height="400px"> -->

							<img src="{{asset('images/preview_img.jpg')}}" width="100%" id="default_image">
							
							<div class="loader_img" id="loader_btn" style="display: none;"><img src="{{asset('images/loader.svg')}}"/></div>

						</div>

						<div class="main_video_error live_img" id="main_video_setup_error" style="display: none;">
                            <img src="{{asset('error.jpg')}}" class="error-image" alt="Error" style="width: 100%;height: 250px;">

                            <div class="flash_display" id="flash_error_display" style="display: none;">
                                <div class="flash_error_div">
                                    <div class="flash_error">{{tr('flash_missing_error')}}<a style="background-color:none;margin-left:1%;display:inline;" target="_blank" href="http://get.adobe.com/flashplayer/" class="underline">{{tr('adobe')}}</a>.</div>
                                </div>
                            </div>
                        </div>

						<hr>

						<div class="pull-left">						

							<button class="btn btn-sm btn-info text-uppercase">
								@if(round($data->amount) > 0) 

                                    {{tr('paid')}} - ${{$data->amount}} 

                                @else 

                                	{{tr('free')}} 

                                @endif
							</button>

							<button class="btn btn-sm btn-danger text-uppercase">
								<i class="fa fa-eye"></i>&nbsp;<span id='viewers_cnt'>{{$data->viewer_cnt}}</span> {{tr('views')}}
							</button>
	
							<a href="http://www.facebook.com/sharer.php?u={{route('user.live_video.start_broadcasting' , array('id'=>$data->unique_id,'c_id'=>$data->channel_id))}}" target="_blank" class="btn btn-sm btn-success text-uppercase" title="{{tr('share_on_fb')}}" style="background: #3b5998;border-color:#3b5998;">
								<i class="fa fa-facebook"></i>
							</a>

							<a href="http://twitter.com/share?text={{$data->title}}...&url={{route('user.live_video.start_broadcasting' , array('id'=>$data->unique_id,'c_id'=>$data->channel_id))}}" target="_blank" class="btn btn-sm btn-success text-uppercase" title="{{tr('share_on_twitter')}}" style="background: #4099ff;border-color:#4099ff;">
								<i class="fa fa-twitter"></i>
							</a>
						</div>

						@if (Auth::check())

							@if($data->user_id == Auth::user()->id)
							<div class="pull-right">

								<?php /*<a href="{{route('user.live_video.stop_streaming',array('id'=>$data->id))}}" class="btn btn-sm btn-danger">{{tr('stop')}}</a> */?>

								<a onclick="return confirm('Are you sure want to stop?')" href="{{route('user.live_video.stop_streaming', array('id'=>$data->id))}}"class="btn btn-sm btn-danger">{{tr('stop')}}</a>

							</div>

							@endif

						@endif

						<div class="clearfix"></div>

						<h4>{{$data->title}}</h4>

						<div class="small" style="color:#777">{{tr('streaming_by')}} {{$data->user ? $data->user->name : ''}} {{tr('from')}} @if (Auth::check()) {{convertTimeToUSERzone($data->created_at, Auth::user()->timezone, 'd-m-Y h:i A')}} @else {{convertTimeToUSERzone($data->created_at, '', 'd-m-Y h:i A')}} @endif</div>

						<br>

						<p>{{$data->description}}</p>



					</div>

					<div class="col-lg-4" ng-controller="chatBarCtrl">

						<div class="chat_box">

							<div class="chat-header">

								<i class="fa fa-comments-o fa-2x"></i>&nbsp;&nbsp; {{tr('group_chat')}}

							</div>

							<div class="chat-content">

								<div id="chat-box" class="chat_box_scroll">

							
									@if(count($comments) > 0)


									@foreach($comments as $comment)

										<?php $img =  ($comment->getUser) ? $comment->getUser->chat_picture : $comment->getViewUser->chat_picture ;

											$name = ($comment->getUser) ? $comment->getUser->name : $comment->getViewUser->name;

											$uid = ($comment->getUser)? $comment->user_id : $comment->live_video_viewer_id;
										?>

										<div class="item chat-msg-sec">


							                <div class="Chat-left" style="padding: 0">

							                	<a target="_blank" href="{{route('user.profile', array('id'=>$uid))}}" title="{{$name}}">  
							                		<img src="{{$img}}" alt="user image" class="chat_img">
							                	</a>

							                </div>
							                <div class="message Chat-right">

							                  <a href="{{route('user.profile', array('id'=>$uid))}}" class="name clearfix" style="text-decoration: none">
							                    <small class="text-muted pull-left">{{$name}}</small>
							                    <?php /*<small class="text-muted pull-right"><i class="fa fa-clock-o"></i> {{$comment->created_at->diffForHumans()}}</small> */ ?>
							                  </a>

							                 	<div>{{$comment->message}}</div>
							                </div>

							                <div class="clearfix"></div>

							            </div>

						            @endforeach

						            @endif
					            

					            </div>

								<div class="chat_footer">

									<div class="input-group">

										@if(Auth::check())
							                <input class="form-control chat_form_input" placeholder="{{tr('comment_here')}}" required id="chat-input">

							                <div class="input-group-btn">
							                  <button type="button" class="btn btn-danger chat_send_btn" id="chat-send"><i class="fa fa-send"></i></button>
							                </div>

						                @else

						                	 <input class="form-control chat_form_input" placeholder="{{tr('comment_here')}}" required readonly>

							                <div class="input-group-btn">
							                  <button type="button" class="btn btn-danger chat_send_btn" disabled><i class="fa fa-send"></i></button>
							                </div>

						                @endif
						            </div>
								</div>

							</div>


						</div>

					</div>

					<div class="clearfix"></div>


					@if(count($videos) > 0)

					<div class="col-lg-12">
							
						<div class="row slide-area recom-area">

		                    <div class="box-head recom-head">
		                        <h3 style="padding: 10px 0px !important;">{{tr('live_videos')}}</h3>
		                    </div>

		                    @if(count($videos) > 0)

		                        <div style="padding: 10px 0px !important;" class="recommend-list row">

		                            @foreach($videos as $video)
		                                <div class="slide-box recom-box">
		                                    <div class="slide-image recom-image">

		                                        <?php 

		                                        $userId = Auth::check() ? Auth::user()->id : '';

		                                        $url = ($video->amount > 0) ? route('user.payment_url', array('id'=>$video->id, 'user_id'=>$userId)): route('user.live_video.start_broadcasting' , array('id'=>$video->unique_id,'c_id'=>$video->channel_id));


		                                        ?>
		<div class="modal fade cus-mod" id="paypal_{{$video->id}}" role="dialog">
                <div class="modal-dialog">
                
                  <!-- Modal content-->
                  <div class="modal-content">

                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title text-center text-uppercase">{{tr('payment_options')}}</h4>
                        </div>


                        <div class="modal-body">
                            <!-- <p>Please Pay to see the full video</p>  -->
                                <div class="col-lg-6">
                                  <!-- small box -->
                                  <div class="small-box bg-green">
                                    <div class="inner">
                                      <h3>{{ Setting::get('currency')}} {{$video->amount}}</h3>
                                      <div class="clearfix"></div>
                                      <p style="float: none;" class="text-left">{{tr('paypal_payment')}}</p>
                                    </div>
                                    <div class="icon">
                                      <i class="fa fa-money"></i>
                                    </div>
                                     <div class="clearfix"></div>
                                    <a href="{{route('user.live_video_paypal', array('id'=>$video->id, 'user_id'=>$userId))}}" class="small-box-footer">{{tr('to_view_video')}} <i class="fa fa-arrow-circle-right"></i></a>
                                  </div>
                                </div>
                           
                                <div class="col-lg-6">
                                  <!-- small box -->
                                  <div class="small-box bg-aqua">
                                    <div class="inner">
                                      <h3>{{ Setting::get('currency')}} {{$video->amount}}</h3>
                                      <div class="clearfix"></div>
                                      <p style="float: none;" class="text-left">{{tr('stripe_payment')}}</p>
                                    </div>
                                    <div class="icon">
                                      <i class="fa fa-money"></i>
                                    </div>
                                     <div class="clearfix"></div>
                                    <a onclick="return confirm('Are you sure want pay through card?')" href="{{route('user.stripe_payment_video', array('id'=>$video->id, 'user_id'=>$userId))}}" class="small-box-footer">{{tr('to_view_video')}} <i class="fa fa-arrow-circle-right"></i></a>
                                  </div>
                                </div>
                            
                            
                            <div class="clearfix"></div>
                            
                        </div>

                        
                  </div>
                  
                </div>
            
            </div>  
                                        @if($video->amount > 0) 
                                        
                                        	@if (isPaidAmount($video->id))
                                                            
                                                <a href="{{$url}}">                                                     
                                            @else
                                                <a data-toggle="modal" data-target="#paypal_{{$video->id}}" style="cursor: pointer;">
                                            @endif
                                        @else
                                    
                                        <a href="{{$url}}">

                                        @endif

                                            <div class="bg_img_video" style="background-image:url({{$video->snapshot}})"></div>

                                        </a>

		                                        <div class="video_duration text-uppercase">
		                                            @if($video->amount > 0) 

		                                                {{tr('paid')}} - ${{$video->amount}} 

		                                            @else {{tr('free')}} @endif
		                                        </div>
		                                    </div><!--end of slide-image-->

		                                    <div class="video-details recom-details">
		                                        <div class="video-head">
		                                            <a href="{{$url}}">

		                                            {{$video->title}}

		                                            </a>
		                                        </div>

		                                        <span class="video_views">
		                                            <i class="fa fa-eye"></i> {{$video->viewer_cnt}} {{tr('views')}} <b>.</b> 
		                                            {{$video->created_at->diffForHumans()}}
		                                        </span> 
		                                    </div><!--end of video-details-->
		                                </div><!--end of slide-box-->
		                            @endforeach
		                            

		                        </div>

		                    @else

		                         <div style="padding: 10px 0px !important;" class="recommend-list row">
		                            <div style="margin: 0.5% 0% !important;" class="slide-box recom-box"> {{tr('no_live_videos')}}</div>
		                        </div>

		                    @endif

		                    <!--end of recommend-list-->

		                     @if(count($videos) > 0)
		                        
		                        <div class="row">
		                            <div class="col-md-12">
		                                <div align="center" id="paglink"><?php echo $videos->links(); ?></div>
		                            </div>
		                        </div>

		                    @endif
		                </div>

					</div>

					@endif
				</div>
				 
			</div>

		</div>

	</div>
</div>

@endsection


@section('scripts')
<script>
    var promise = navigator.mediaDevices.getUserMedia({ audio: true, video: true });
// 	navigator.mediaDevices.getUserMedia(constraints)
//   .then(function(stream) {
//     var videoTracks = stream.getVideoTracks();
//     console.log('Got stream with constraints:', constraints);
//     console.log('Using video device: ' + videoTracks[0].label);
//     stream.onended = function() {
//       console.log('Stream ended');
//     };
//     window.stream = stream; // make variable available to console
//     video.srcObject = stream;
//   })
//   .catch(function(error) {
//     // ...
//   }
// navigator.getMedia = ( navigator.getUserMedia || // use the proper vendor prefix
//                        navigator.webkitGetUserMedia ||
//                        navigator.mozGetUserMedia ||
//                        navigator.msGetUserMedia);

// navigator.getMedia({video: true}, function() {
//   // webcam is available
//   console.log("yes ok")
// }, function() {
// 	var check_camera = confirm("Please enable your camera!");
// 	if(check_camera) {

// 	}else
//   		location.reload()	
//   // webcam is not available
// });
</script>
<script src="{{asset('lib/angular/angular.min.js')}}"></script>
<script src="{{asset('lib/angular-socket-io/socket.min.js')}}"></script>
<script src="{{asset('lib/socketio/socket.io-1.4.5.js')}}"></script>
<script src="{{asset('lib/rtc-multi-connection/RTCMultiConnection.js')}}"></script>

<script src="{{asset('bower_components/adapter.js/adapter.js')}}"></script>
<script src="{{asset('bower_components/demo-console/index.js')}}"></script>
<script src="{{asset('bower_components/ekko-lightbox/dist/ekko-lightbox.min.js')}}"></script>
<script src="{{asset('bower_components/kurento-utils/js/kurento-utils.js')}}"></script>


<script src="{{asset('jwplayer/jwplayer.js')}}"></script>

<script>jwplayer.key="{{Setting::get('JWPLAYER_KEY')}}";</script>

<script src="{{asset('assets/js/getHTMLMediaElement.js')}}"></script>


<script type="text/javascript">

setTimeout( function() { jQuery(".alert-success").fadeOut("slow") },5000);


var jwplayer_key = "{{Setting::get('JWPLAYER_KEY')}}";


var kurento_socket_url = "{{Setting::get('kurento_socket_url')}}";

var wowza_ip_address = "{{Setting::get('wowza_ip_address')}}";

console.log(jwplayer_key);

var video_details = <?= $data; ?>;

var appSettings = <?= $appSettings;?>

var socket_url =  "<?= Setting::get('SOCKET_URL'); ?>";

var chat_socket_url = "<?= Setting::get('chat_socket_url');?>"; 

var stop_streaming_url = "<?= route('user.live_video.stop_streaming', array('id'=>$data->id)) ?>";

$('.chat_box_scroll').scrollTop($('.chat_box_scroll')[0].scrollHeight);

var apiUrl = "<?= url('/');?>";

var live_user_id = "<?= Auth::check() ? Auth::user()->id : '' ?>";

var user_token = "<?= Auth::check() ? Auth::user()->token : '' ?>";

var is_vod = "<?= Setting::get('is_vod')?>";

var routeUrl = "<?= route('user.live_videos') ?>";

var liveAppCtrl = angular.module('liveApp', [
  'btford.socket-io',

], function ($interpolateProvider) {
  $interpolateProvider.startSymbol('<%');
  $interpolateProvider.endSymbol('%>');
})
.constant('socket_url', socket_url)
.constant('stop_streaming_url',stop_streaming_url)
.constant('chat_socket_url', chat_socket_url)
.constant('apiUrl',apiUrl)
.constant('live_user_id',live_user_id)
.constant('routeUrl', routeUrl)
.constant('user_token',user_token)
.constant('appSettings', appSettings);

liveAppCtrl
    .run(['$rootScope',
        '$window',
        '$timeout',
        function ($rootScope,$window, $timeout) {
            

            $rootScope.videoDetails = video_details;

            $rootScope.appSettings = appSettings;
        }
]);

</script>

<script src="{{asset('assets/js/streamController.js')}}"></script>

@endsection