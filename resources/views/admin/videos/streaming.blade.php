<script type="text/javascript">
	$('.progress-bar-fill').delay(1000).queue(function () {
	    $(this).css('width', '100%')
	});
	
</script>
<style type="text/css">


.progress-bar {
    width: calc(100% - 6px);
    height: 5px;
    background: #e0e0e0;
    padding: 3px;
    border-radius: 3px;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, .2);
}

.progress-bar-fill {
    display: block;
    height: 5px;
    background: #659cef;
    border-radius: 3px;
    /*transition: width 250ms ease-in-out;*/
    transition: width 5s ease-in-out;
}

</style>

<!-- Main Video Configuration -->

<div class="embed-responsive embed-responsive-16by9" id="main_video_setup_error" style="display: none;width: 100%;">
    <img src="{{asset('error.jpg')}}" class="error-image" alt="{{Setting::get('site_name')}} - Main Video" style="width: 100%;">
</div>

<div class="embed-responsive embed-responsive-16by9" id="main_video_ad" style="display: none;width: 100%;">
    <img src="" class="error-image" alt="{{Setting::get('site_name')}} - Main Video Ad" id="ad_image" style="width: 100%;">
    <!-- <div class="progress-bar">
	    <span class="progress-bar-fill" style="width: 30%"></span>
	</div> -->
</div>

<div id="main-video-player"></div>

@if(!check_valid_url($ads->get_video_tape->video))
    <div class="embed-responsive embed-responsive-16by9" style="display:none" id="main_video_error">
        <img src="{{asset('error.jpg')}}" class="error-image" alt="{{Setting::get('site_name')}} - Main Video">
    </div>
@endif


<div class="embed-responsive embed-responsive-16by9" id="flash_error_display" style="display: none;width: 100%;">
   <div style="width: 100%;background: black; color:#fff;height:350px;">
   		 <div style="text-align: center;padding-top:25%">{{tr('flash_missing')}} <a target="_blank" href="http://get.adobe.com/flashplayer/" class="underline">{{'adobe'}}</a>.</div>

   </div>
</div>