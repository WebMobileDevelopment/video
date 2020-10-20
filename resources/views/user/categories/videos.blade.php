@foreach($videos as $i => $video)


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
                    <div class="cross-title2">
                        <h5 class="payment_class"><a href="{{$video->url}}">{{$video->title}}</a></h5>
                        
                        <span class="video_views">
                            <i class="fa fa-eye"></i> {{$video->watch_count}} {{tr('views')}} <b>.</b> 
                            {{ common_date($video->created_at) }}
                        </span>
                    </div> 
                    
                                        <!--end of cross-mark-->                       
                </div> <!--end of history-head--> 

                <div class="description">
                    <?php /*<div class="category"><b class="text-capitalize">{{tr('category_name')}} : </b> <a href="{{route('user.categories.view', $video->category_unique_id)}}" target="_blank">{{$video->category_name}}</a></div> */?>
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