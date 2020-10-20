@extends('layouts.user')

@section('content')

    <div class="y-content">
        <div class="row content-row">

            @include('layouts.user.nav')
    
            <div class=" page-inner col-sm-9 col-md-10">
                <div class="new-history">
                    <div class="content-head search-head">
                        <div><h4>{{tr('search_result')}} "{{$key}}"</h4></div>               
                    </div><!--end of content-head-->

                    <ul class="history-list">

                        @if (count($channels) > 0)

                            @foreach($channels as $channel)

                            <li class="sub-list search-list row">
                                <div class="main-history">
                                    <div class="history-image text-center">
                                        <a href="#" class="">
                                            <img src="{{$channel->picture}}" class="channelsearch-img">
                                        </a> 
                                    </div>
                                    <div class="history-title mt-15">
                                        <div class="history-head row">
                                            <div class="cross-title">
                                                <h5 class="mb-5"><a href="{{route('user.channel',$channel->channel_id)}}">{{$channel->title}}</a></h5>
                                                <span class="video_views">
                                                    <div>
                                                        <i class="fa fa-eye"></i>&nbsp;{{$channel->no_of_subscribers}} Subscribers&nbsp;<b>.</b>&nbsp;{{$channel->no_of_videos}} videos
                                                    </div>
                                                </span> 
                                            </div> 
                                            @if(Auth::check())
                                            <div class="pull-right upload_a">

                                                @if (!$channel->subscribe_status)

                                                    <a class="st_video_upload_btn subscribe_btn" href="{{route('user.subscribe.channel', array('user_id'=>Auth::user()->id, 'channel_id'=>$channel->channel_id))}}" style="color: #fff !important;text-decoration: none">
                                                    <i class="fa fa-users"></i>&nbsp;{{tr('subscribe')}}&nbsp;{{$channel->no_of_subscribers}}</a>

                                                @else 

                                                    <a class="st_video_upload_btn " href="{{route('user.unsubscribe.channel', array('subscribe_id'=>$channel->subscribe_status))}}" onclick="return confirm(&quot;{{tr('user_unsubscribe_confirm') }}&quot;)"> <i class="fa fa-times"></i>&nbsp;{{tr('un_subscribe')}} &nbsp; {{$channel->no_of_subscribers}}</a>

                                                @endif
                                            </div>
                                            @endif
                                        </div> <!--end of history-head--> 

                                        <div class="description">
                                            <div>
                                                <?= $channel->description?>
                                            </div>
                                        </div><!--end of description-->                                                     
                                    </div>
                                </div>
                            </li>
                            @endforeach

                        @endif
                    </ul>

                    <ul class="history-list">

                        @if(count($videos->items) > 0)

                            @foreach($videos->items as $v => $video)

                                <li class="sub-list search-list row">
                                    <div class="main-history">
                                         <div class="history-image">
                                            <a href="{{$video->url}}">
                                                <img src="{{$video->video_image}}">
                                            </a>        
                                            @if($video->ppv_amount > 0)
                                                @if(!$video->ppv_status)
                                                    <div class="video_amount">

                                                    {{tr('pay')}} - {{Setting::get('currency')}}{{$video->ppv_amount}}

                                                    </div>
                                                @endif
                                            @endif
                                            <div class="video_duration">
                                                {{$video->duration}}
                                            </div>                 
                                        </div><!--history-image-->

                                        <div class="history-title">
                                            <div class="history-head row">
                                                <div class="cross-title">
                                                    <h5>
                                                        <a href="{{$video->url}}">{{$video->title}}</a></h5>
                                                    <span class="video_views">
                                                         <div><a href="{{route('user.channel',$video->channel_id)}}">{{$video->channel_name}}</a></div>
                                                        <div>
                                                            <i class="fa fa-eye"></i> {{$video->watch_count}} {{tr('views')}}<b>.</b> 
                                                            {{common_date($video->created_at) }}
                                                        </div>
                                                    </span> 
                                                </div> 
                                                                      
                                            </div> <!--end of history-head--> 

                                            <div class="description">
                                                <div><?= $video->description?></div>
                                            </div><!--end of description--> 

                                            <span class="stars">
                                               <a><i @if($video->ratings >= 1) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                               <a><i @if($video->ratings >= 2) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                               <a><i @if($video->ratings >= 3) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                               <a><i @if($video->ratings >= 4) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                               <a><i @if($video->ratings >= 5) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                            </span>                                                       
                                        </div><!--end of history-title--> 
                                        
                                    </div><!--end of main-history-->
                                </li>

                            @endforeach

                        @else

                            <!-- <p class="no-result">{{tr('no_search_result')}}</p> -->
                            <img src="{{asset('images/no-result.jpg')}}" class="img-responsive auto-margin">

                        @endif
                       
                    </ul>

                    @if(count($videos->items) > 16)
                        <div class="row">
                            <div class="col-md-12">
                                <div align="right" id="paglink">
                                     <a href="{{route('user.trending')}}" class="btn btn-sm btn-danger text-uppercase">{{tr('see_all')}}</a>

                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    @endif

                    <hr>


                    <ul class="history-list">

                        @if(count($live_videos) > 0)

                            <div><h5 class="text-uppercase" style="font-weight: bold;">{{tr('live_video_title')}} "{{$key}}"</h5></div>

                            @foreach($live_videos as $v => $live_video)

                                <?php 

                                    $userId = Auth::check() ? Auth::user()->id : '';

                                    $url = ($live_video->amount > 0) ? route('user.payment_url', array('id'=>$live_video->id, 'user_id'=>$userId)): route('user.live_video.start_broadcasting' , array('id'=>$live_video->unique_id,'c_id'=>$live_video->channel_id));

                                    ?>

                                <li class="sub-list search-list row">
                                    <div class="main-history">
                                         <div class="history-image">
                                            <a href="{{$url}}">
                                                <img src="{{$live_video->snapshot}}">
                                            </a>        
                                            <div class="video_duration text-uppercase">
                                                @if($live_video->amount > 0) 

                                                {{tr('paid')}} - ${{$live_video->amount}} 

                                                @else {{tr('free')}} @endif
                                            </div>                 
                                        </div><!--history-image-->

                                        <div class="history-title">
                                            <div class="history-head row">
                                                <div class="cross-title">
                                                    <h5>
                                                        <a href="{{$url}}">{{$live_video->title}}</a></h5>
                                                    <span class="video_views">
                                                        <i class="fa fa-eye"></i> {{$live_video->viewer_cnt}} {{tr('views')}}<b>.</b> 
                                                        {{$live_video->created_at->diffForHumans()}}
                                                    </span> 
                                                </div> 
                                                                      
                                            </div> <!--end of history-head--> 

                                            <div class="description">
                                                <p>{{$live_video->description}}</p>
                                            </div><!--end of description--> 

                                                                                                  
                                        </div><!--end of history-title--> 
                                        
                                    </div><!--end of main-history-->
                                </li>

                            @endforeach

                        @else

                            <p class="no-result">{{tr('no_search_result')}}</p>

                        @endif
                       
                    </ul>

                    @if(count($live_videos) > 16)
                        <div class="row">
                            <div class="col-md-12">
                                <div align="right" id="paglink">

                                    <?php //echo $live_videos->links(); ?>

                                    <a href="{{route('user.live_videos')}}" class="btn btn-sm btn-danger text-uppercase">{{tr('see_all')}}</a>
                                        
                                </div>
                            </div>
                        </div>
                    @endif
                    
                </div>
                <div class="sidebar-back"></div> 
            </div>
        </div>
    </div>

@endsection