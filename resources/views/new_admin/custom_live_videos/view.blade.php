@extends('layouts.admin')

@section('title', tr('view_custom_live_video'))

@section('content-header', tr('view_custom_live_video'))

@section('styles')

<style>
hr {
    margin-bottom: 10px;
    margin-top: 10px;
}
</style>

@endsection

@section('breadcrumb')
    <li><a href="{{route('admin.custom.live.index')}}"><i class="fa fa-video-camera"></i> {{tr('custom_live_videos')}}</a></li>
    <li class="active">{{tr('view_custom_live_video')}}</li>
@endsection 

@section('content')

    <div class="row">

        <div class="col-lg-12">

            @include('notification.notify')

            <div class="box box-primary">

                <div class="box-header with-border">

                    <div class='pull-left'>
                        <h3 class="box-title"> <b>{{$custom_live_video_details->title}}</b></h3>
                        <br>
                        <span style="margin-left:0px" class="description">Created Time - {{$custom_live_video_details->created_at->diffForHumans()}}</span>
                    </div>

                    <div class='pull-right'>
                       
                        @if(Setting::get('admin_delete_control') == YES )
                            
                            <a class="btn btn-sm btn-warning" href="javascript:;" class="btn disabled" style="text-align: left" title="{{ tr('edit') }}"><i class="fa fa-pencil"></i></a>

                            <a class="btn btn-sm btn-danger" href="javascript:;" class="btn disabled" style="text-align: left" title="{{ tr('delete') }}"><i class="fa fa-trash"></i></a>

                        @else

                             <a class="btn btn-sm btn-warning" href="{{ route('admin.custom.live.edit' , ['custom_live_video_id' => $custom_live_video_details->id] ) }}" title="{{ tr('edit') }}"><i class="fa fa-pencil"></i></a>

                             <a class="btn btn-sm btn-danger" href="{{ route('admin.custom.live.delete' , ['custom_live_video_id' => $custom_live_video_details->id] ) }}" onclick="return confirm(&quot;{{ tr('admin_custom_live_video_delete_confirmation', $custom_live_video_details->title ) }}&quot;);"  title="{{ tr('delete') }}"><i class="fa fa-trash"></i></a>

                        @endif

                        @if($custom_live_video_details->status == YES)
                            
                            <a class="btn btn-sm btn-danger" href="{{ route('admin.custom.live.status', ['custom_live_video_id' => $custom_live_video_details->id] ) }}" onclick="return confirm(&quot;{{ tr('admin_custom_live_video_decline_confirmation', $custom_live_video_details->title) }}&quot;)"  title="{{ tr('decline') }}"><i class="fa fa-close"></i></a>
                            
                        @else
                            
                            <a class="btn btn-sm btn-success" href="{{ route('admin.custom.live.status', ['custom_live_video_id' => $custom_live_video_details->id]) }}" onclick="return confirm(&quot;{{ tr('admin_custom_live_video_approve_confirmation', $custom_live_video_details->title) }}&quot;)" title="{{ tr('approve') }}"><i class="fa fa-check"></i></a>
                            
                        @endif
                                            
                    </div>

                    <div class="clearfix"></div>
                </div>

                <div class="box-body">

                    <div class="row">
                        
                        <div class="col-lg-12">
                                                           
                            <h4><b>{{ tr('details') }}</b></h4>

                            <div class="col-lg-4"> 
                                <b><i class="fa fa-suitcase margin-r-5"></i>{{tr('title')}}</b>
                                <br>  
                                <br>  
                                <p>{{$custom_live_video_details->title}}</p>
                            </div> 
                            
                            <div class="col-lg-4" style="word-wrap: break-word;"> 
                                <b><i class="fa fa-video-camera margin-r-5"></i>{{tr('hls_video_url')}}</b> 
                                <br>  
                                <br>                   
                                <a href="{{ $custom_live_video_details->hls_video_url }}">{{$custom_live_video_details->hls_video_url}}</a>
                            </div> 

                            <div class="col-lg-4" style="word-wrap: break-word;">
                                <span><b><i class="fa fa-video-camera margin-r-5"></i>
                                {{tr('rtmp_video_url')}}</b> </span>
                  
                                <br>  
                                <br>  
                                <a href="{{ $custom_live_video_details->rtmp_video_url }}" >{{$custom_live_video_details->rtmp_video_url}}</a>
                            </div>

                            <div class="col-lg-12">
                            <hr>
                                <b><i class="fa fa-book margin-r-5"></i>{{tr('description')}}</b>   
                                <br>   
                                <br>   
                                <p style="word-wrap: break-word;">{{$custom_live_video_details->description}}</p>
                            </div>
                       
                        </div>

                    </div>

                    <hr>
                
                    <div class="row">

                        <div class="col-lg-12">
                            
                            <div class="col-lg-6">

                                <strong><i class="fa fa-video-camera margin-r-5"></i> {{tr('video')}}</strong>
                                <br>
                                <br>
                        
                                @if(check_valid_url($custom_live_video_details->rtmp_video_url))

                                    <?php $url = $custom_live_video_details->rtmp_video_url; ?>

                                    <div id="main-video-player"></div>

                                @else
                                    <div class="image">
                                        <img src="{{asset('error.jpg')}}" alt="{{Setting::get('site_name')}}">
                                    </div>
                                @endif
                            </div>

                            <div class="col-lg-6">
                                <strong><i class="fa fa-file-picture-o margin-r-5"></i> {{tr('images')}}</strong>
                                <br>
                                <br>
                                <img alt="Photo" src="{{isset($custom_live_video_details->image) ? $custom_live_video_details->image : ''}}" class="img-responsive" style="width:100%;height:250px;">
                            </div>
                            
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection

@section('scripts')
    
    <script src="{{asset('jwplayer/jwplayer.js')}}"></script>

    <script>jwplayer.key="{{Setting::get('JWPLAYER_KEY')}}";</script>

    <script type="text/javascript">

        function getBrowser() {

            // Opera 8.0+
            var isOpera = (!!window.opr && !!opr.addons) || !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;

            // Firefox 1.0+
            var isFirefox = typeof InstallTrigger !== 'undefined';

            // Safari 3.0+ "[object HTMLElementConstructor]" 
            var isSafari = /constructor/i.test(window.HTMLElement) || (function (p) { return p.toString() === "[object SafariRemoteNotification]"; })(!window['safari'] || safari.pushNotification);

            // Internet Explorer 6-11
            var isIE = /*@cc_on!@*/false || !!document.documentMode;

            // Edge 20+
            var isEdge = !isIE && !!window.StyleMedia;

            // Chrome 1+
            var isChrome = (!!window.chrome && !!window.chrome.webstore) || navigator.userAgent.indexOf("Chrome") !== -1;

            // Blink engine detection
            var isBlink = (isChrome || isOpera) && !!window.CSS;

            var b_n = '';

            switch(true) {

                case isFirefox :

                        b_n = "Firefox";

                        break;
                case isChrome :

                        b_n = "Chrome";

                        break;

                case isSafari :

                        b_n = "Safari";

                        break;
                case isOpera :

                        b_n = "Opera";

                        break;

                case isIE :

                        b_n = "IE";

                        break;

                case isEdge : 

                        b_n = "Edge";

                        break;

                case isBlink : 

                        b_n = "Blink";

                        break;

                default :

                        b_n = "Unknown";

                        break;

            }

            return b_n;

        }

        var mobile_type = "";

        function getMobileOperatingSystem() {

          var userAgent = navigator.userAgent || navigator.vendor || window.opera;

          if( userAgent.match( /iPad/i ) || userAgent.match( /iPhone/i ) || userAgent.match( /iPod/i ) )
          {
            mobile_type =  'ios';

          }
          else if( userAgent.match( /Android/i ) )
          {

            mobile_type =  'andriod';
          }
          else
          {
            mobile_type =  'unknown'; 
          }

          return mobile_type;
        
        }

        var browser = getBrowser();

        var m_type = getMobileOperatingSystem();

        
        jQuery(document).ready(function(){


                console.log('Inside Video');
                    
                console.log('Inside Video Player');

                console.log("{{$custom_live_video_details->rtmp_video_url}}");
                console.log("{{$custom_live_video_details->hls_video_url}}");

                var playerInstance = jwplayer("main-video-player");

                if(m_type != "unknown") {

                    playerInstance.setup({

                        file: mobile_type == 'ios' ? "{{$custom_live_video_details->hls_video_url}}" : "{{$custom_live_video_details->rtmp_video_url}}",
                        image: "{{$custom_live_video_details->image}}",
                        width: "100%",
                        aspectratio: "16:9",
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

                } else {

                    playerInstance.setup({
                        sources: [
                    {
                        file: "{{$custom_live_video_details->rtmp_video_url}}"
                    }, 
                    {
                        file : "{{$custom_live_video_details->hls_video_url}}"
                    }
                    ],
                        image: "{{$custom_live_video_details->image}}",
                        width: "100%",
                        aspectratio: "16:9",
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

                }
                    
                
        });

    </script>

@endsection

