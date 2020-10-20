@extends('layouts.user')

@section('content')

<div class="y-content">
    <div class="row content-row">

        @include('layouts.user.nav')

        <div class="history-content page-inner col-sm-9 col-md-10">
            <div class="slide-area1">

                @include('notification.notify')

                <div class="new-history">
                    <div class="content-head">
                        <div><h4 class="bold">{{tr('history')}}</h4></div>

                        @if(count($histories->items) > 0)
                            <div class="clear-button">
                                <form method="get" action="{{route('user.delete.history')}}">
                                    <input type="hidden" name="status" value="1">
                                    <button class="btn btn-danger" onclick="return confirm(&quot; {{tr('user_history_delete_confirm') }}&quot;)" type="submit">{{tr('clear_all')}}</button>
                                </form>

                            </div>  
                        @endif 
                        <div class="clear-fix"></div>             
                    </div>

                    <!--end of content-head-->
                    
                    @if(count($histories->items) > 0)

                        <ul class="history-list">

                            @foreach($histories->items as $i => $history)

                                <li class="sub-list row">
                                    <div class="main-history">
                                         <div class="history-image">
                                            <a href="{{$history->url}}">
                                                <!-- <img src="{{$history->video_image}}"> -->
                                                <img src="{{asset('streamtube/images/placeholder.gif')}}" data-src="{{$history->video_image}}" class="slide-img1 placeholder" />
                                            </a>
                                            @if($history->ppv_amount > 0)
                                                @if(!$history->ppv_status)
                                                    <div class="video_amount">

                                                    {{tr('pay')}} - {{Setting::get('currency')}}{{$history->ppv_amount}}

                                                    </div>
                                                @endif
                                            @endif
                                            <div class="video_duration">
                                                {{$history->duration}}
                                            </div>
                                        </div><!--history-image-->

                                        <div class="history-title">
                                            <div class="history-head row">
                                                <div class="cross-title1">
                                                    <h5><a href="{{$history->url}}">{{$history->title}}</a></h5>
                                                    <span class="video_views">
                                                        <div class="video-views-channel-name">
                                                            <a href="{{route('user.channel',$history->channel_id)}}">{{$history->channel_name}}</a>
                                                        </div>
                                                        <i class="fa fa-eye"></i> {{$history->watch_count}} {{tr('views')}} 
                                                        <b>.</b> 
                                                        {{ common_date($history->created_at) }}
                                                    </span>
                                                </div> 
                                                <div class="cross-mark1">
                                                    <a onclick="return confirm(&quot; {{tr('user_history_delete_confirm') }}&quot;)"  href="{{route('user.delete.history' , array('video_tape_id' => $history->video_tape_id))}}"><i class="fa fa-times" aria-hidden="true"></i></a>
                                                </div><!--end of cross-mark-->                       
                                            </div> <!--end of history-head--> 

                                            <div class="description">
                                                <?= $history->description ?>
                                            </div><!--end of description--> 

                                            <span class="stars">
                                               <a><i @if($history->ratings >= 1) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                               <a><i @if($history->ratings >= 2) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                               <a><i @if($history->ratings >= 3) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                               <a><i @if($history->ratings >= 4) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                               <a><i @if($history->ratings >= 5) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                            </span>      

                                        </div><!--end of history-title--> 
                                        
                                    </div><!--end of main-history-->
                                </li> 
                            @endforeach
                           
                        </ul>

                    @else 

                        <!-- <p>{{tr('no_history_found')}}</p> -->
                        <img src="{{asset('images/no-result.jpg')}}" class="img-responsive auto-margin">

                    @endif

                    @if(count($histories->items) > 0)

                        @if($histories->items)
                        <div class="row">
                            <div class="col-md-12">
                                <div align="center" id="paglink"><?php echo $histories->pagination; ?></div>
                            </div>
                        </div>
                        @endif
                    @endif
                    
                </div>
            
                <div class="sidebar-back"></div>

            </div>
        </div>

    </div>
</div>

@endsection