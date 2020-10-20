<div class="up-next">

    <h4 class="sugg-head1">{{tr('suggestions')}}</h4>

    <ul class="video-sugg">

        @if(count($suggestions->items) > 0)

            @foreach($suggestions->items as $suggestion)

                <li class="sugg-list row">
                    
                    <div class="main-video">
                        
                        <div class="video-image">
                            
                            <div class="video-image-outer">
                                @if($suggestion->video_type == VIDEO_TYPE_R4D)
                                <a href="{{$suggestion->url}}/?video_type={{VIDEO_TYPE_R4D}}">
                                @else
                                <a href="{{$suggestion->url}}">
                                @endif
                                    <img src="{{asset('streamtube/images/placeholder.gif')}}" data-src="{{$suggestion->video_image}}" class="placeholder" />
                                </a>
                            </div>  
                            
                            @if($suggestion->ppv_amount > 0)
                                @if(!$suggestion->ppv_status)
                                    <div class="video_amount">
                                        {{tr('pay')}} - {{Setting::get('currency')}}{{$suggestion->ppv_amount}}
                                    </div>
                                @endif
                            @endif

                            <div class="video_duration">
                                {{$suggestion->duration}}
                            </div>
                        
                        </div>
                        
                        <!--video-image-->

                        <div class="sugg-head">
                            <div class="suggn-title">
                                <h5>
                                @if($suggestion->video_type == VIDEO_TYPE_R4D)
                                <a href="{{$suggestion->url}}/?video_type={{VIDEO_TYPE_R4D}}">
                                @else
                                <a href="{{$suggestion->url}}">
                                @endif
                                {{$suggestion->title}}</a></h5>
                            </div>
                            <!--end of sugg-title-->

                            <span class="video_views">
                                <div>
                                    <a href="{{route('user.channel',$suggestion->channel_id)}}">{{$suggestion->channel_name}}</a>
                                </div>
                                <i class="fa fa-eye"></i> {{$suggestion->watch_count}} {{tr('views')}} <b>.</b> 
                                {{$suggestion->created_at}} 
                            </span>

                            <br>

                            <span class="stars">
                                <a><i @if($suggestion->ratings >= 1) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                <a><i @if($suggestion->ratings >= 2) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                <a><i @if($suggestion->ratings >= 3) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                <a><i @if($suggestion->ratings >= 4) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                <a><i @if($suggestion->ratings >= 5) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                            </span>                              
                        
                        </div>
                        <!--end of sugg-head-->

                    </div>
                    <!--end of main-video-->
                
                </li>
                <!--end of sugg-list-->
            @endforeach
        @endif
    </ul>
</div>
<!--end of up-next-->