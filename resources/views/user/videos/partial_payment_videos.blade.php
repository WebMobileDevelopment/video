 @foreach($payment_videos->data as $i => $video)


<li class="sub-list row">
    <div class="main-history">
         <div class="history-image">
            <a href="{{$video->url}}"><img src="{{$video->video_image}}"></a> 
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
                    <h5 class="payment_class"><a href="{{$video->url}}">{{$video->title}}</a></h5>
                
                    <span class="video_views">
                        <i class="fa fa-eye"></i> {{$video->watch_count}} {{tr('views')}} <b>.</b> 
                        {{$video->created_at}}
                    </span> 

                </div> 
                <div class="cross-mark">
                    <a onclick="return confirm(&quot;{{tr('user_video_delete_confirm') }}&quot;)" href="{{route('user.delete.video' , array('id' => $video->video_tape_id))}}"><i class="fa fa-times" aria-hidden="true"></i></a>
                </div><!--end of cross-mark-->                       
            </div> <!--end of history-head--> 

            <div class="description">
                <?= $video->description ?>
            </div><!--end of description--> 


            <div>
                @if($video->amount > 0)
                <span class="label label-success">{{tr('ad_amount')}} - ${{$video->amount}}</span>
                @endif
                @if($video->total_ppv_amount > 0)
                <span class="label label-primary">{{tr('ppv_amount')}} - ${{$video->total_ppv_amount}}</span>
                @endif
            </div>
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