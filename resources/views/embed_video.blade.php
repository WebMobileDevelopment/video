@extends('layouts.embed')

@section('content')

<div style="height: 100% !important">

<div style="width: 100% !important;height: 100% !important">

<div id="video-player">
	
</div>


<div class="embed-responsive embed-responsive-16by9" id="flash_error_display" style="display: none;">
   <div style="width: 100%;background: black; color:#fff;">
         <div style="text-align: center;align-items: center;">{{tr('flash_missing')}} <a target="_blank" href="http://get.adobe.com/flashplayer/" class="underline">{{tr('adobe')}}</a>.</div>
   </div>
</div>

</div>

</div>

@endsection

@section('scripts')
<script src="{{asset('jwplayer/jwplayer.js')}}"></script>

<script>jwplayer.key="{{Setting::get('JWPLAYER_KEY')}}";</script>

<script type="text/javascript">
        
jQuery(document).ready(function(){

var playerInstance = jwplayer("video-player");


playerInstance.setup({
    file: "{{$model->video}}",
    image: "{{$model->default_image}}",
    width: "100%",
    height: "100%",
    primary: "flash",
    autostart : true,
});


playerInstance.on('setupError', function() {

           // jQuery("#video-player").css("display", "none");
           // jQuery('#trailer_video_setup_error').hide();
           

            var hasFlash = false;
            try {
                var fo = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
                if (fo) {
                    hasFlash = true;
                }
            } catch (e) {
                if (navigator.mimeTypes
                        && navigator.mimeTypes['application/x-shockwave-flash'] != undefined
                        && navigator.mimeTypes['application/x-shockwave-flash'].enabledPlugin) {
                    hasFlash = true;
                }
            }

            if (hasFlash == false) {
                jQuery('#flash_error_display').show();
                return false;
            }

            // jQuery('#main_video_setup_error').css("display", "block");

            confirm('The video format is not supported in this browser. Please option some other browser.');
        
        });

});
</script>
@endsection