
<!-- Main Video Configuration -->

<div class="embed-responsive embed-responsive-16by9" id="main_video_setup_error" style="display: none;">
    <img src="{{asset('error.jpg')}}" class="error-image" alt="{{Setting::get('site_name')}} - Main Video">
</div>

<div class="embed-responsive embed-responsive-16by9" id="main_video_ad" style="display: none">
    <img src="" class="error-image" alt="{{Setting::get('site_name')}} - Main Video Ad" id="ad_image">

    <div class="click_here_ad" style="display: none">

    </div>

    <div class="ad_progress">
   		<div id="timings">{{tr('ad') }} : <span class="seconds"></span></div>
    	<div class="clearfix"></div>
	    <div id="progress">
        
      </div>
	</div>
</div>

    <?php
    $video_format = 'mp4';
    if($video && isset($video->video)) {
        $temp = explode('.', $video->video);
        $video_format = end($temp);
    }

    ?>

    <video
        id="my-video"
        class="video-js col-md-6 vjs-big-play-centered vjs-layout-medium vjs-default-skin"
        controls
        preload="auto"
        poster="{{$video->default_image}}"
        data-setup= '{ "playbackRates": [0.5, 1, 1.5, 2] }'
      >
        <source src="{{$video->video}}" type="video/{{$video_format}}" />
        <p class="vjs-no-js"></p>
      </video>
      <div id="video_player_overlay">
        <img src="{{Setting::get('site_logo')}}" class="overlay_logo" />
      </div>

 <div class="embed-responsive embed-responsive-16by9" id="flash_error_display" style="display: none;">
         <div style="width: 100%;background: black; color:#fff;height: 100%;">
               <div style="text-align: center;align-items: center;">{{tr('flash_missing')}}<a target="_blank" href="http://get.adobe.com/flashplayer/" class="underline">{{tr('adobe')}}</a>.</div>
         </div>
  </div>


@if(!check_valid_url($video->video))
    <div class="embed-responsive embed-responsive-16by9" style="display:none" id="main_video_error">
        <img src="{{asset('error.jpg')}}" class="error-image" alt="{{Setting::get('site_name')}} - Main Video">
    </div>
@endif


<div class="embed-responsive embed-responsive-16by9" id="flash_error_display" style="display: none;">
   <div style="width: 100%;background: black; color:#fff;height:350px;">

   		 <div style="text-align: center;padding-top:25%">{{tr('flash_missing')}}<a target="_blank" href="http://get.adobe.com/flashplayer/" class="underline">{{tr('adobe')}}</a>.</div>
   </div>
</div>


<!-- Trailer Video Configuration END -->