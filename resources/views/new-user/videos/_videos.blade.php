@if($data->data)

<div class="slide-area">

    <div class="box-head">

        <h3>{{$data->title}}</h3>

        <p>{{$data->description}}</p>

    </div>

    <div class="box video-list-slider">

        @foreach($data->data as $video_tape_details)

        <div class="slide-box">

            <div class="slide-image">

                <a href="{{$video_tape_details->video_tape_id}}">

                    <img src="{{asset('streamtube/images/placeholder.gif')}}" data-src="{{$video_tape_details->video_image}}"class="slide-img1 placeholder" />

                </a>

                @if($video_tape_details->ppv_amount > 0)

                    @if(!$video_tape_details->is_pay_per_view)

                        <div class="video_amount">

                        {{tr('pay')}} - {{Setting::get('currency')}}{{$video_tape_details->ppv_amount}}

                        </div>

                    @endif

                @endif

                <div class="video_mobile_views">
                    {{$video_tape_details->watch_count}} {{tr('views')}}
                </div>

                <div class="video_duration">
                    {{$video_tape_details->duration}}
                </div>

            </div><!--end of slide-image-->

            <div class="video-details">

                <div class="video-head">
                    <a href="{{$video_tape_details->video_tape_id}}">{{$video_tape_details->title}}</a>
                </div>

                <span class="video_views">
                    <div>
                        <a href="{{route('user.channel',$video_tape_details->channel_id)}}">
                            {{$video_tape_details->channel_name}}
                        </a>
                    </div>

                    <div class="hidden-mobile">
                        <i class="fa fa-eye"></i> 
                        {{$video_tape_details->watch_count}} {{tr('views')}} 
                        <b>.</b> 
                        {{$video_tape_details->created_at}}
                    </div>
                </span>
            </div><!--end of video-details-->
        
        </div>
        
        @endforeach

          
    </div><!--end of box--> 

</div>

<div class="section-video-border"></div>

@endif
