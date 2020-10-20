
<!-- Main R4D Video Configuration -->

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

@if(count($videos_lists) > 0)
@foreach($videos_lists as $key=>$videos_list)
    @foreach($videos_list as $key_v=>$video_item)
    <?php
        $video_format = pathinfo($video_item['video'], PATHINFO_EXTENSION);
    ?>
    <video
        id="stream_video_{{$key}}_{{$key_v}}"
        class="video-js col-md-6 vjs-big-play-centered vjs-layout-medium vjs-default-skin hidden"
        controls
        preload="auto"
        poster="{{$video->default_image}}"
        data-setup= '{ "playbackRates": [0.5, 1, 1.5, 2] }'
      >
        <source src="{{$video_item['video']}}" type="video/{{$video_format}}" />
      </video>
    @endforeach
@endforeach
@endif
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

<p>check video</p>
<div id="video_check_div">
</div>
<div class="embed-responsive embed-responsive-16by9" id="flash_error_display" style="display: none;">
   <div style="width: 100%;background: black; color:#fff;height:350px;">

   		 <div style="text-align: center;padding-top:25%">{{tr('flash_missing')}}<a target="_blank" href="http://get.adobe.com/flashplayer/" class="underline">{{tr('adobe')}}</a>.</div>
   </div>
</div>

@section('r4d_video_script')
<script>
var video_check_div = $('#video_check_div')
@if(count($videos_lists) > 0)
var video_id = video_folder_id = []
var player_order = 0
var total_video_org = <?php echo json_encode($videos_lists, JSON_PRETTY_PRINT);?>;
var total_video = total_video_org;

@foreach($videos_lists as $key=>$videos_list)
    @foreach($videos_list as $key_v=>$video_item)
    player_order++;
    var options_{{$key}}_{{$key_v}} = {
        domid: player_order,
        folder_id: player_order,
        responsive: true,
        breakpoints: {
            tiny: 300,
            xsmall: 400,
            small: 500,
            medium: 600,
            large: 700,
            xlarge: 800,
            huge: 900
        }
    };
    var player_{{$key}}_{{$key_v}} = videojs("stream_video_{{$video_item['folder_id']}}_{{$key_v}}", options_{{$key}}_{{$key_v}}, function(){
        var rand_dom_folder_id = {{$key}}
        var rand_dom_id = {{$key_v}}
        if(this.hasClass('vjs-next')) {
            console.log('ok chekc next page')
        }
        this.on('paused', function() {
            this.play();
        })
        this.on('ended', function() {
            this.addClass('vjs-ended');
            // if (this.options_.loop) {
            //     this.currentTime(0);
            //     this.play();
            // } else if (!this.paused()) {
            //     this.pause();
            // }
            videojs.log('ed here!');
            video_check_div.append('_folder_'+(rand_dom_folder_id+1)+'_video_'+(rand_dom_id+1)+'<br/>')
            
            this.addClass('vjs-ended');
            // this.trigger('ended');
                this.addClass('hidden');
                let xx = total_video.length 
                let yy = Number(total_video.length) - 1
                console.log(yy , total_video, "okok", rand_dom_folder_id)
            if(total_video.length-1 > rand_dom_folder_id) {
                console.log(yy , "okok", rand_dom_folder_id)
                
                play_next('end', rand_dom_folder_id, rand_dom_id)
            }else {
                console.log(yy , "nono", rand_dom_folder_id)
                
                video_check_div.append('_sub_folder_end_<br/>')
                play_next('end', 'sub_end', rand_dom_id)
                // this.pause()
                // this.addClass('vjs-next');
                // ready_next('end', rand_dom_folder_id, rand_dom_id)
            }
        })
        this.on('error', function() {
            this.addClass('vjs-error here');
            
            video_check_div.append('_folder_'+(rand_dom_folder_id+1)+'_video_'+(rand_dom_id+1)+'<br/>')
                this.addClass('hidden');
            if(total_video.length-1 > rand_dom_folder_id) {
                play_next('end', rand_dom_folder_id, rand_dom_id)
            }else {
                video_check_div.append('_sub_folder_end_<br/>')
                play_next('end', 'sub_end', rand_dom_id)
                // this.pause()
                // this.addClass('vjs-next');
                // ready_next('end', rand_dom_folder_id, rand_dom_id)
            }
        })
        this.on('playing', function() {
            
            // if({{$key}} == total_video.length-1) {
            //     console.log(" ready next ", rand_dom_folder_id)
            //     this.pause()
            //     ready_next('end', rand_dom_folder_id, rand_dom_id)
            // }
        });
    })
    // player_{{$video_item['id']}}.logo({
    //     image:'my_logo.png',
    //     position:'top-left'
    // })
    
    @endforeach
@endforeach
@endif
$('#stream_video_0_0').removeClass('hidden')
function play_next(status, rand_dom_folder_id, domid) {
    // video_check_div.append(status+'_folder_'+rand_dom_folder_id+'_video_'+domid+'<br/>')
    console.log(status, rand_dom_folder_id, domid,"ok")
    if(rand_dom_folder_id == 'sub_end')
        var next_folder = 0;
    else
        var next_folder = rand_dom_folder_id+1;
            
    console.log(status, rand_dom_folder_id, domid,"ok",next_folder)
            
    var rand_dom_id = get_random_id(next_folder, domid)
    
    console.log(status, rand_dom_id, "rand_dom_id", next_folder, "next_folder",rand_dom_folder_id)
    $('#stream_video_'+next_folder+'_'+rand_dom_id).removeClass('hidden')
    if(next_folder > 0)
        eval('player_'+next_folder+'_'+rand_dom_id).play()
    // if(status == 'end' && domid == 0) {
    //     $('#stream_video_'+video_id[0]).removeClass('hidden')
    //     eval('player_'+video_id[0]).play()
    // }else {
    //     $('#stream_video_'+video_id[domid]).removeClass('hidden')
    //     eval('player_'+video_id[domid]).play()
    // }
    // if(video_id.length == domid) {
    // }
}

function ready_nexts(status, rand_dom_folder_id, domid) {
    $('.video-js').addClass('hidden')
    console.log(status, rand_dom_folder_id, domid,"ready")
    if(rand_dom_folder_id != 0) {
        var pre_folder = rand_dom_folder_id-1;
        if(!$('#stream_video_'+pre_folder+'_'+domid).hasClass('hidden'))
            $('#stream_video_'+pre_folder+'_'+domid).addClass('hidden')
    }else if(rand_dom_folder_id == 0)
    {
        next_folder = 0;
        if(!$('#stream_video_'+next_folder+'_'+domid).hasClass('hidden'))
            $('#stream_video_'+next_folder+'_'+domid).addClass('hidden')
    }
    
    var rand_dom_id = get_random_id(0, domid)
    $('#stream_video_0_'+rand_dom_id).removeClass('hidden')
    eval('player_0_'+rand_dom_id).play()

}

function get_random_id(next_folder, domid) {
    
        // total_video[next_folder][domid]
    
    if(total_video[next_folder].length == 0)
        total_video[next_folder] = total_video_org[next_folder]
    var rand_dom_id = Math.floor(Math.random() * total_video[next_folder].length);

    if(total_video[next_folder][rand_dom_id] != undefined)
        total_video[next_folder].slice(rand_dom_id, 1)
    
    if(total_video[next_folder][rand_dom_id] == undefined)
        get_random_id(next_folder)
    return rand_dom_id;
}
</script>
@endsection
<!-- Trailer Video Configuration END -->