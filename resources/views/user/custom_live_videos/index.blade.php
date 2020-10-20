@extends('layouts.user')

@section('styles')

@endsection

@section('content')


<div class="y-content">
        
        <div class="row content-row">

            @include('layouts.user.nav')

            <div class="page-inner col-sm-9 col-md-10">

                <div class="slide-area recom-area">

                    <div class="box-head recom-head">

                        <h3>{{tr('custom_live_videos')}}</h3>

                    </div>


                    @if(count($data) > 0)

                        <div class="recommend-list row">

                            @foreach($data as $video)

                                <div class="slide-box recom-box">

                                    <div class="slide-image recom-image">

                                        <a href="{{route('user.custom_live_videos.view' , $video->custom_live_video_id)}}">
                                        	<img src="{{$video->image}}" />
                                       	</a>
                                        
                                        <div class="video_duration">
                                            Live
                                        </div>
                                    </div><!--end of slide-image-->

                                    <div class="video-details recom-details">
                                        <div class="video-head">
                                            <a href="{{route('user.custom_live_videos.view' , $video->custom_live_video_id)}}">{{$video->title}}</a>
                                        </div>
                                       

                                        <span class="video_views">                                             
                                            {{$video->created_time}}
                                        </span> 
                                    </div><!--end of video-details-->
                                </div><!--end of slide-box-->
                            @endforeach
                            

                        </div>

                    @else

                         <div class="recommend-list row">
                            <div class="slide-box recom-box"> {{tr('no_result_found')}}</div>
                        </div>

                    @endif

                    <!--end of recommend-list-->

                     @if(count($data) > 0)

                        <div class="row">
                            <div class="col-md-12">
                                <div align="center" id="paglink"><?php //echo $data->pagination; ?></div>
                            </div>
                        </div>

                    @endif
                </div>

                <!--end of slide-area-->

                <div class="sidebar-back"></div> 
            </div>

        </div>
    </div>


@endsection
