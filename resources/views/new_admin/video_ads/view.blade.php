@extends('layouts.admin')

@section('title', tr('view_assigned_ad'))

@section('content-header', tr('view_assigned_ad'))

@section('styles')

<style>
hr {
    margin-bottom: 10px;
    margin-top: 10px;
}
</style>

@endsection

@section('breadcrumb')
    <li><a href="{{route('admin.video_ads.index')}}"><i class="fa fa-bullhorn"></i> {{tr('assigned_ads')}}</a></li>
    <li class="active">{{tr('view_assigned_ad')}}</li>
@endsection 

@section('content')
    
    <div class="col-md-12">
        
        <div class="nav-tabs-custom">

            <ul class="nav nav-tabs">
                
                <li class="active"><a href="#preview_ad" data-toggle="tab" aria-expanded="true">{{tr('preview_ad')}}</a></li>

                @if(Setting::get('admin_delete_control') == YES )
                    <a href="javascript:;" class="btn disabled" style="text-align: left" class="pull-right btn btn-warning" title="{{tr('edit')}}" style="margin: 4px"><b><i class="fa fa-pencil"></i></b>
                    </a>

                    <a href="javascript:;" class="pull-right btn disabled" style="text-align: left" class="pull-right btn btn-danger" title="{{tr('delete')}}" style="margin: 4px"><b><i class="fa fa-trash"></i></b>
                    </a>
                @else
                    <a href="{{route('admin.video_ads.edit' , ['id' => $ads->id] )}}" class="pull-right btn btn-warning" title="{{tr('edit')}}" style="margin: 4px">
                        <b><i class="fa fa-pencil"></i></b>
                    </a>
                     <a href="{{route('admin.video_ads.delete' , ['id' => $ads->id] )}}" onclick="return confirm(&quot;{{ tr('are_you_sure') }}&quot;);" class="pull-right btn btn-danger" title="{{tr('delete')}}" style="margin: 4px">
                        <b><i class="fa fa-trash"></i></b>
                    </a>
                @endif
            </ul>

            <div class="tab-content">

                <div class="tab-pane active" id="preview_ad">

                    <div class="col-lg-6">
                        @include('new_admin.video_tapes.streaming')
                    </div>

                    <div class="col-lg-6">

                        <h4>{{tr('details')}}</h4>

                        <ul class="timeline timeline-inverse">

                        @if($ads->pre_ad)

                            <li class="time-label" title="{{tr('video_time')}}">
                                <span class="bg-red">
                                  {{$ads->pre_ad->video_time}}
                                </span>
                            </li>

                            <li>
                                <i class="fa fa-bullhorn bg-blue"></i>

                                <div class="timeline-item">
                                    <span class="time"><i class="fa fa-clock-o"></i> {{$ads->pre_ad->ad_time}} ({{tr('in_sec')}})</span>
                                    <h3 class="timeline-header">
                                        <a>{{tr('pre_ad')}}</a> {{tr('details')}} (<a href="{{$ads->pre_ad->assigned_ad->ad_url}}" target="_blank">{{tr('click_here_url')}}</a>)
                                    </h3> 

                                    <div class="timeline-body">
                                      
                                        <img src="{{$ads->pre_ad->assigned_ad->file}}" style="width: 100%">

                                    </div>
                                </div>
                            </li>
                        @endif

                        @if($ads->between_ad)
                         
                            @foreach($ads->between_ad as $details)
                                
                                <li class="time-label" title="{{tr('video_time')}}">
                                      <span class="bg-red">
                                        {{$details->video_time}}
                                      </span>
                                </li>

                                <li>
                                    <i class="fa fa-bullhorn bg-blue"></i>

                                    <div class="timeline-item">

                                        <span class="time"><i class="fa fa-clock-o"></i> {{$details->ad_time}} ({{tr('in_sec')}})</span>

                                        <h3 class="timeline-header">

                                        @if($details->ad_type == 1)

                                          <a>{{tr('pre_ad')}}</a> 

                                        @elseif($details->ad_type == 2) 

                                          <a>{{tr('post_ad')}}</a> 

                                        @else

                                          <a>{{tr('between_ad')}}</a> 

                                        @endif

                                        {{tr('details')}} (<a href="{{$details->assigned_ad->ad_url}}" target="_blank">{{tr('click_here_url')}}</a>)</h3> 

                                        <div class="timeline-body">
                                            
                                              <img src="{{$details->assigned_ad->file}}" style="width: 100%">

                                        </div>
                                  
                                    </div>

                                </li>

                            @endforeach

                        @endif

                        @if($ads->post_ad)
                        
                            <li class="time-label" title="{{tr('video_time')}}">
                                <span class="bg-red">
                                  {{$ads->post_ad->video_time}}
                                </span>
                            </li>

                            <li>
                                <i class="fa fa-bullhorn bg-blue"></i>

                                <div class="timeline-item">
                                    <span class="time"><i class="fa fa-clock-o"></i> {{$ads->post_ad->ad_time}} ({{tr('in_sec')}})</span>

                                    <h3 class="timeline-header">
                                        <a>{{tr('post_ad')}}</a> 
                                        {{tr('details')}} (<a href="{{$ads->post_ad->assigned_ad->ad_url}}" target="_blank">{{tr('click_here_url')}}</a>)
                                    </h3> 

                                    <div class="timeline-body">
                                      
                                        <img src="{{$ads->post_ad->assigned_ad->file}}" style="width: 100%">
                                    </div>

                                </div>

                            </li>

                        @endif
                        
                        </ul>

                    </div>

                    <div class="clearfix"></div>

                </div>

            </div>

        </div>

        </div>

    <div class="clearfix"></div>
    @endsection

@section('scripts')

    <script src="{{asset('jwplayer/jwplayer.js')}}"></script>

    <script>jwplayer.key="{{Setting::get('JWPLAYER_KEY')}}";</script>

    <script type="text/javascript">
        
        jQuery(document).ready(function(){  

                // Opera 8.0+
                var isOpera = (!!window.opr && !!opr.addons) || !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
                // Firefox 1.0+
                var isFirefox = typeof InstallTrigger !== 'undefined';
                // At least Safari 3+: "[object HTMLElementConstructor]"
                var isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;
                // Internet Explorer 6-11
                var isIE = /*@cc_on!@*/false || !!document.documentMode;
                // Edge 20+
                var isEdge = !isIE && !!window.StyleMedia;
                // Chrome 1+
                var isChrome = !!window.chrome && !!window.chrome.webstore;
                // Blink engine detection
                var isBlink = (isChrome || isOpera) && !!window.CSS;


            //  jQuery("form[name='watch_main_video']").submit(function(e) {

                //prevent Default functionality
               //  e.preventDefault();

               //  jQuery('#watch_main_video_button').fadeOut();

                    var playerInstance = jwplayer("main-video-player");  

                    @if($ads->get_video_tape->video)

                        if(isOpera || isSafari) {

                            jQuery('#main_video_setup_error').show();
                            jQuery('#trailer_video_setup_error').hide();
                            jQuery('#main-video-player').hide();

                            confirm('The video format is not supported in this browser. Please option some other browser.');

                        } else {

                                playerInstance.setup({
                                    
                                    file: "{{$ads->get_video_tape->video}}",
                                    image: "{{$ads->get_video_tape->default_image}}",
                                    width: "100%",
                                    aspectratio: "16:9",
                                    height: "270px",
                                    primary: "flash",
                                    controls : true,
                                    "controlbar.idlehide" : false,
                                    controlBarMode:'floating',
                                    "controls": {
                                        "enableFullscreen": false,
                                        "enablePlay": false,
                                        "enablePause": false,
                                        "enableMute": true,
                                        "enableVolume": true
                                    },
                                    // autostart : true,
                                    "sharing": {
                                        "sites": ["reddit","facebook","twitter"]
                                      }
                                
                                });

                            playerInstance.on('setupError', function() {

                                jQuery("#main-video-player").css("display", "none");
                               

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

                                jQuery('#main_video_setup_error').css("display", "block");

                                confirm('The video format is not supported in this browser. Please option some other browser.');
                            
                            });
                        }
                    
                    @else
                        jQuery('#main_video_error').show();
                        
                    @endif

                    jQuery("#main-video-player").show();

                    console.log(jwplayer().getPosition());

                    var intervalId;


                    var timings = "{{($ads) ? count($ads->between_ad) : 0}}";

                    // var adtimings = 5;

                    var time = 0;

                    // console.log("Timings " + timings.length);

                    function timer(){

                         intervalId = setInterval(function(){

                            var video_time = Math.round(playerInstance.getPosition());


                            console.log("Video Timing "+video_time);

                            @if($ads)

                                @if(count($ads->between_ad) > 0)

                                    @foreach($ads->between_ad as $i => $obj) {

                                        var video_timing = "{{$obj->video_time}}";

                                        console.log("Video Timing "+video_timing);

                                        var a = video_timing.split(':'); // split it at the colons

                                         if (a.length == 3) {
                                             var seconds = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2]);
                                         } else {
                                             var seconds = parseInt(a[0]) * 60 + parseInt(a[1]);
                                         }

                                         console.log("Seconds "+seconds);

                                        if (video_time == seconds && time != video_time) {

                                             jwplayer().pause();

                                             time = video_time;

                                             stop();

                                             // $('#main-video-player').hide();

                                             $("#main-video-player").css({'visibility':'hidden', 'width' : '0%'});

                                             $('#main_video_ad').show();

                                             $("#ad_image").attr("src","{{$obj->assigned_ad->file}}");


                                             adsPage("{{$obj->ad_time}}");

                                        }
                                    }

                                    @endforeach

                                @endif


                                @if($ads->post_ad)

                                    if (playerInstance.getState() == "complete") {

                                        // $('#main-video-player').hide();

                                        $("#main-video-player").css({'visibility':'hidden', 'width' : '0%'});

                                        $('#main_video_ad').show();

                                        $("#ad_image").attr("src","{{$ads->post_ad->assigned_ad->file}}");

                                        stop();

                                        adsPage("{{$ads->post_ad->ad_time}}");

                                    }
                                @endif
                                
                            @endif


                        }, 1000);

                    }

                    function stop(){
                       clearInterval(intervalId);
                    }

                    var adCount = 0;

                    function adsPage(adtimings){

                         // adtimings = adtimings * 60;

                         intervalId = setInterval(function(){

                            adCount += 1;

                            console.log("Ad Count " +adCount);
 
                            if (adCount == adtimings) {

                                adCount = 0;

                                stop();

                                $('#main_video_ad').hide();

                                // $('#main-video-player').show();

                                $("#main-video-player").css({'visibility':'visible', 'width' : '100%'});

                                if (playerInstance.getState() != "complete") {

                                    jwplayer().play();

                                    timer();

                                }

                            }

                        }, 1000);

                    }


                    jwplayer().on('displayClick', function(e) {

                        console.log("state pos "+jwplayer().getState());

                        if (jwplayer().getState() == 'idle') {

                            @if($ads)

                                @if($ads->pre_ad)

                                     jwplayer().pause();

                                     // $('#main-video-player').hide();

                                     $('#main_video_ad').show();

                                     $("#main-video-player").css({'visibility':'hidden', 'width' : '0%'});

                                     $("#ad_image").attr("src","{{$ads->pre_ad->assigned_ad->file}}");

                                     adsPage("{{$ads->pre_ad->ad_time}}");

                                @endif

                            @endif


                        }

                        @if($ads)
                            @if (((count($ads->between_ad) > 0) || !empty($ads->post_ad)) && empty($ads->pre_ad)) 

                                timer();

                            @endif
                        @endif

                    })                    

            // });

        });

    </script>

@endsection


