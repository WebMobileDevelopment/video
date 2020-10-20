@if($video)
<?php
  $video_format = 'mp4';
if($video && isset($video->video)) {
  $temp = explode('.', $video->video);
  $video_format = end($temp);
}

?>
<div class="wrapper col-md-12">
  <div class="videocontent">
      <video
        id="my-video"
        class="video-js col-md-6 vjs-big-play-centered vjs-layout-medium vjs-default-skin"
        controls
        preload="auto"
        poster="{{$video->default_image}}"
        data-setup= '{ "playbackRates": [0.5, 1, 1.5, 2] }'
      >
        <source src="{{$video->video}}" type="video/{{$video_format}}" />
        <p class="vjs-no-js">
          <!-- To view this video please enable JavaScript, and consider upgrading to a
          web browser that
          <a href="https://videojs.com/html5-video-support/" target="_blank"
            >supports HTML5 video</a> -->
        </p>
      </video>
      <div id="video_player_overlay">
        <img src="{{Setting::get('site_logo')}}" class="overlay_logo" />
      </div>
  </div>
</div>
@endif