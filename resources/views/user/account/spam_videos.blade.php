    @extends('layouts.user')

@section('content')

<div class="y-content">
    <div class="row content-row">

        @include('layouts.user.nav')

        <div class="history-content page-inner col-sm-9 col-md-10">
            
            @include('notification.notify')

            <div class="new-history">
                <div class="content-head">
                    <div><h4 class="no-margin-top">{{tr('spam_videos')}}</h4></div>              
                </div><!--end of content-head-->

                @if(count($model->items) > 0)

                    <ul class="history-list">
                    
                        @foreach($model->items as $i => $spamvideo)

                        <li class="sub-list row">
                            <div class="main-history">
                                 <div class="history-image">
                                    <img src="{{$spamvideo->video_image}}">

                                    @if($spamvideo->ppv_amount > 0)
                                        @if(!$spamvideo->ppv_status)
                                            <div class="video_amount">

                                            {{tr('pay')}} - {{Setting::get('currency')}}{{$spamvideo->ppv_amount}}

                                            </div>
                                        @endif
                                    @endif
                                    <div class="video_duration">
                                        {{$spamvideo->duration}}
                                    </div>                        
                                </div><!--history-image-->

                                <div class="history-title">
                                    <div class="history-head row">
                                        <div class="cross-title1">
                                            <h5>{{$spamvideo->title}}</h5>
                                            <!-- <p class="duration">{{tr('duration')}}: {{$spamvideo->duration}}</p> -->
                                            <span class="video_views">
                                                <div><a href="{{route('user.channel',$spamvideo->channel_id)}}">{{$spamvideo->channel_name}}</a></div>
                                                <i class="fa fa-eye"></i> {{$spamvideo->watch_count}} {{tr('views')}} 
                                                <b>.</b> 
                                                {{ common_date($spamvideo->created_at) }}
                                            </span>
                                        </div> 
                                        
                                        <div class="cross-mark1">
                                            <a onclick="return confirm(&quot;{{ substr($spamvideo->title, 0 , 15)}}.. {{tr('user_spamvideo_delete_confirm') }}&quot;)"  href="{{route('user.remove.report_video',$spamvideo->video_tape_id)}}"><i class="fa fa-times" aria-hidden="true"></i></a>
                                        </div><!--end of cross-mark-->                       
                                    </div> <!--end of history-head--> 

                                    <div class="description">
                                        <?= $spamvideo->description?>
                                    </div><!--end of description--> 

                                    <span class="stars">
                                        <a><i @if($spamvideo->ratings >= 1) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                        <a><i @if($spamvideo->ratings >= 2) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                        <a><i @if($spamvideo->ratings >= 3) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                        <a><i @if($spamvideo->ratings >= 4) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                        <a><i @if($spamvideo->ratings >= 5) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                    </span>                                               
                                </div><!--end of history-title--> 
                                
                            </div><!--end of main-history-->
                        </li>    

                        @endforeach
                       
                    </ul>

                @else
                   <!--  <p>{{tr('no_spam_found')}}</p> -->
                   <img src="{{asset('images/no-result.jpg')}}" class="img-responsive auto-margin">
                @endif

                @if(count($model->items) > 0)

                    @if($model->items)
                    <div class="row">
                        <div class="col-md-12">
                            <div align="center" id="paglink"><?php echo $model->pagination; ?></div>
                        </div>
                    </div>
                    @endif
                @endif
                
            </div>
        
            <div class="sidebar-back"></div> 
        </div>

    </div>
</div>

@endsection