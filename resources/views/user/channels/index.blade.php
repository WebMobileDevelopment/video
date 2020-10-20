@extends( 'layouts.user' ) 

@section( 'styles' )

    <link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/custom-style.css')}}">

    <style>
        #c4-header-bg-container {
            background-image: url({{$channel->cover}});
        }
        
        @media screen and (-webkit-min-device-pixel-ratio: 1.5),
        screen and (min-resolution: 1.5dppx) {
            #c4-header-bg-container {
                background-image: url({{$channel->cover}});
            }
        }
        
        #c4-header-bg-container .hd-banner-image {
            background-image: url({{$channel->cover}});
        }
        
        .payment_class {
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            max-height: 38px;
            line-height: 19px;
            padding: 0 !important;
            font-weight: bolder !important;
        }
        
        .switch {
            display: inline-block;
            height: 23px;
            position: relative;
            width: 45px;
            vertical-align: middle;
        }
        
        .switch input {
            display: none;
        }
        
        .slider {
            background-color: #ccc;
            bottom: 0;
            cursor: pointer;
            left: 0;
            position: absolute;
            right: 0;
            top: 0;
            transition: all 0.4s ease 0s;
        }
        
        .slider::before {
            background-color: white;
            bottom: 4px;
            content: "";
            height: 16px;
            left: 0px;
            position: absolute;
            transition: all 0.4s ease 0s;
            width: 16px;
        }
        
        input:checked + .slider {
            background-color: #51af33;
        }
        
        input:focus + .slider {
            box-shadow: 0 0 1px #2196f3;
        }
        
        input:checked + .slider::before {
            transform: translateX(26px);
        }
        
        .slider.round {
            border-radius: 34px;
        }
        
        .slider.round::before {
            border-radius: 50%;
        }
        .btn_go_live {
            background: green;
        }
        .st_video_upload_btn * {
            color: white;
        }
        #video_upload_btn {
            background: #337ab7;
        }
        #channel_edit_btn {
            background: #f0ad4e;
        }
        #channel_del_btn {
            background: #d43f3a;
        }
        .r4d_video_mark {
            width: 5%;
        }
        .r4d_video_mark_tab {
            width: 40px;
        }
    </style>

@endsection 

@section('content')
<?php $cur_sub = App\UserPayment::getCurSubscr(auth()->user()->id); ?>
    <div class="y-content">

        <div class="row content-row">

            @include('layouts.user.nav')

            <div class="page-inner col-sm-9 col-md-10">

                <div class="slide-area1">

                    <div class="branded-page-v2-top-row">

                        <div class="branded-page-v2-header channel-header yt-card">

                            <div id="gh-banner">

                                <div id="c4-header-bg-container" class="c4-visible-on-hover-container has-custom-banner">

                                    <div class="hd-banner">
                                        <div class="hd-banner-image"></div>
                                    </div>

                                    <!-- <a class="channel-header-profile-image spf-link" href="">
                                    <img class="channel-header-profile-image" src="{{$channel->picture}}" title="{{$channel->name}}" alt="{{$channel->name}}">
                                    </a> -->

                                </div>

                            </div>

                            @include('notification.notify')

                            <div class="channel-content-spacing display-inline">

                                <div>

                                    <div class="pull-left">
                                        <a class="channel-header-profile-image spf-link" href="">
                                            <div style="background-image:url({{$channel->picture}});" class="channel-header-profile-image1"></div>
                                        </a>
                                    </div>

                                    <div class="pull-left width-40">
                                        <h1 class="st_channel_heading text-uppercase">{{$channel->name}}</h1>
                                        <p class="subscriber-count">{{$subscriberscnt}} Subscribers</p>
                                        <?php /*<p class="subscriber-count">{{$subscriberscnt}} Subscribers</p> */?>
                                    </div>

                                    <div class="pull-right upload_a btn-space width-60 text-right">
                                        
                                        @if(Auth::check()) 

                                            @if($channel->user_id == Auth::user()->id) 

                                                @if(Auth::user()->user_type)

                                                    <a class="st_video_upload_btn" id="video_upload_btn" href="{{route('user.video_upload', ['id'=>$channel->id])}}"><i class="fa fa-upload"></i><span>{{tr('upload')}}</span></a> 

                                                    @if(Setting::get('broadcast_by_user') == YES || Auth::user()->is_master_user == YES)
                                                        <button class="st_video_upload_btn btn_go_live" onclick="live_video()">
                                                            <i class="fa fa-video-camera"></i>
                                                            <span>{{tr('go_live')}}</span>
                                                        </button>
                                                    @endif

                                                @endif

                                                <a style="display: none;" class="st_video_upload_btn" href="{{route('user.video_upload', ['id'=>$channel->id])}}"><i class="fa fa-download"></i> <span>{{tr('download_from_youtube')}}</span></a>

                                                <a class="st_video_upload_btn" id="channel_edit_btn" href="{{route('user.channel_edit', $channel->id)}}"><i class="fa fa-pencil"></i><span>{{tr('edit')}}</span></a>

                                                <a class="st_video_upload_btn" id="channel_del_btn" onclick="return confirm(&quot;{{ $channel->name }} -  {{tr('user_channel_delete_confirm') }}&quot;)" href="{{route('user.delete.channel', ['id'=>$channel->id])}}"><i class="fa fa-trash"></i><span> {{tr('delete')}}</span></a> 

                                            @endif 

                                            @if($channel->user_id != Auth::user()->id) 

                                                @if (!$subscribe_status)

                                                    <a class="st_video_upload_btn subscribe_btn" href="{{route('user.subscribe.channel', array('user_id'=>Auth::user()->id, 'channel_id'=>$channel->id))}}" style="color: #fff !important">{{tr('subscribe')}} &nbsp; {{$subscriberscnt}} </a> 

                                                @else

                                                    <a class="st_video_upload_btn" href="{{route('user.unsubscribe.channel', array('subscribe_id'=>$subscribe_status))}}" onclick="return confirm(&quot;{{ $channel->name }} -  {{tr('user_channel_unsubscribe_confirm') }}&quot;)">{{tr('un_subscribe')}} &nbsp; {{$subscriberscnt}}</a> 

                                                @endif 

                                            @else 

                                                @if($subscriberscnt > 0)

                                                    <a class="st_video_upload_btn subscribe_btn" href="{{route('user.channel.subscribers', array('channel_id'=>$channel->id))}}" style="color: #fff !important"><i class="fa fa-users"></i>&nbsp;{{tr('subscribers')}} &nbsp; {{$subscriberscnt}}</a> 

                                                @endif 

                                            @endif 

                                        @endif

                                    </div>

                                    <div class="clearfix"></div>

                                </div>

                                <div id="channel-subheader" class="clearfix branded-page-gutter-padding appbar-content-trigger">

                                    <ul id="channel-navigation-menu" class="clearfix nav nav-tabs" role="tablist">

                                        <li role="presentation" class="active">
                                            <a href="#home1" class="yt-uix-button  spf-link  yt-uix-sessionlink yt-uix-button-epic-nav-item yt-uix-button-size-default" aria-controls="home" role="tab" data-toggle="tab">
                                                <span class="yt-uix-button-content hidden-xs">{{tr('home')}}</span>
                                                <span class="visible-xs"><i class="fa fa-home channel-tab-icon"></i></span>
                                            </a>
                                        </li>
                                        <li role="presentation" id="r4d_videos_sec">
                                            <a href="#r4d_videos" class="yt-uix-button  spf-link  yt-uix-sessionlink yt-uix-button-epic-nav-item yt-uix-button-size-default" aria-controls="home" role="tab" data-toggle="tab">
                                                <span class="yt-uix-button-content hidden-xs"><img src="{{Setting::get('site_icon')}}" class="r4d_video_mark_tab" /> {{tr('videos')}}</span>
                                                <span class="visible-xs"><i class="fa fa-home channel-tab-icon"></i></span>
                                            </a>
                                        </li>
                                        <li role="presentation" id="videos_sec">
                                            <a href="#videos" class="yt-uix-button  spf-link  yt-uix-sessionlink yt-uix-button-epic-nav-item yt-uix-button-size-default" aria-controls="videos" role="tab" data-toggle="tab">
                                                <span class="yt-uix-button-content hidden-xs">{{tr('videos')}}</span>
                                                <span class="visible-xs"><i class="fa fa-video-camera channel-tab-icon"></i></span>
                                            </a>
                                        </li>
                                        @if(Setting::get('broadcast_by_user') == 1 || (Auth::check() ? Auth::user()->is_master_user == 1 : 0))

                                            <li role="presentation">

                                                <a href="#live_videos_section" class="yt-uix-button  spf-link  yt-uix-sessionlink yt-uix-button-epic-nav-item yt-uix-button-size-default" aria-controls="live_videos_section" role="tab" data-toggle="tab">
                                                    <span class="yt-uix-button-content">{{tr('live_videos')}}</span> 
                                                </a>

                                            </li>

                                        @endif
                                        <li role="presentation">
                                            <a href="#about" class="yt-uix-button spf-link  yt-uix-sessionlink yt-uix-button-epic-nav-item yt-uix-button-size-default" aria-controls="about" role="tab" data-toggle="tab">
                                                <span class="yt-uix-button-content hidden-xs">{{tr('about_video')}}</span>
                                                <span class="visible-xs"><i class="fa fa-info channel-tab-icon"></i></span>
                                            </a>
                                        </li>

                                        <li role="presentation">
                                            <a href="#playlist" class="yt-uix-button spf-link yt-uix-sessionlink yt-uix-button-epic-nav-item yt-uix-button-size-default" aria-controls="playlist" role="tab" data-toggle="tab">
                                                <span class="yt-uix-button-content hidden-xs">{{tr('playlist')}}</span>
                                                <span class="visible-xs"><i class="fa fa-list channel-tab-icon"></i></span>
                                            </a>
                                        </li>

                                        @if(Auth::check()) 

                                            @if($channel->user_id == Auth::user()->id)

                                                <li role="presentation" id="payment_managment_sec">
                                                    <a href="#payment_managment" class="yt-uix-button  spf-link  yt-uix-sessionlink yt-uix-button-epic-nav-item yt-uix-button-size-default" aria-controls="payment_managment" role="tab" data-toggle="tab">
                                                        <span class="yt-uix-button-content hidden-xs">{{tr('payment_managment')}} ({{Setting::get('currency')}} {{getAmountBasedChannel($channel->id)}})</span>
                                                        <span class="visible-xs"><i class="fa fa-suitcase channel-tab-icon"></i> &nbsp;({{Setting::get('currency')}} {{getAmountBasedChannel($channel->id)}})</span>
                                                    </a>
                                                </li>

                                            @endif 

                                            <li role="presentation" id="live_videos_billing_sec">
                                                <a href="#live_videos_billing" class="yt-uix-button  spf-link  yt-uix-sessionlink yt-uix-button-epic-nav-item yt-uix-button-size-default" aria-controls="live_videos_billing" role="tab" data-toggle="tab">
                                                    <span class="yt-uix-button-content hidden-xs">{{tr('live_videos_billing')}}</span> 
                                                    <span class="visible-xs"><i class="fa fa-video-camera channel-tab-icon"></i> &nbsp;</span>
                                                </a>
                                            </li>

                                        @endif
                                 
                                    </ul>
                               
                                </div>

                            </div>

                        </div>

                    </div>

                    <ul class="tab-content"  style="padding-left: 0px;">

                        <li role="tabpanel" class="tab-pane active" id="home1">

                            <div class="feed-item-dismissable">

                                <div class="feed-item-main feed-item-no-author">

                                    <div class="feed-item-main-content">

                                        <div class="shelf-wrapper clearfix">

                                            <div class="big-section-main new-history1">

                                                <div class="content-head">
                                                    <h4 style="color: #000;">{{tr('watch_to_next')}}</h4>
                                                </div>
                                                <?php /*@if(count($trending_videos) == 0)

                                                <img src="{{asset('images/no-result.jpg')}}" class="img-responsive auto-margin">

                                                @endif */?>
                                                
                                                <div class="lohp-shelf-content row">
                                                    
                                                    <!-- <div class="lohp-large-shelf-container col-md-6">

                                                    @if(count($trending_videos) > 0)
                                                    <div class="slide-box recom-box big-box-slide">
                                                        <div class="slide-image recom-image hbb">
                                                            <a href="{{$trending_videos[0]->url}}">
                                                                <img src="{{$trending_videos[0]->video_image}}">
                                                            </a>
                                                            @if($trending_videos[0]->ppv_amount > 0)
                                                                @if(!$trending_videos[0]->ppv_status)
                                                                    <div class="video_amount">

                                                                    {{tr('pay')}} - {{Setting::get('currency')}}{{$trending_videos[0]->ppv_amount}}

                                                                    </div>
                                                                @endif
                                                            @endif
                                                            <div class="video_duration">
                                                                {{$trending_videos[0]->duration}}
                                                            </div>
                                                        </div>
                                                        <div class="video-details recom-details">
                                                            <div class="video-head">
                                                                <a href="{{$trending_videos[0]->url}}"> {{$trending_videos[0]->title}}</a>
                                                            </div>

                                                             <span class="video_views">
                                                                <i class="fa fa-eye"></i> {{$trending_videos[0]->watch_count}} {{tr('views')}} <b>.</b> 
                                                                {{ common_date($trending_videos[0]->created_at) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    </div> -->
                                                   
                                                    <div class="lohp-medium-shelves-container col-md-12">                      
                                                    <div class="row">
                                                        
                                                        @if(count($trending_videos) > 0) 

                                                            @foreach($trending_videos as $index => $trending_video)

                                                                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6 channel-view">

                                                                    <div class="slide-box recom-box big-box-slide mt-0 mb-15">

                                                                        <div class="slide-image">
                                                                            @if($trending_video->video_type == VIDEO_TYPE_R4D)
                                                                            <a href="{{$trending_video->url}}/?video_type={{VIDEO_TYPE_R4D}}">
                                                                            @else
                                                                            <a href="{{$trending_video->url}}">
                                                                            @endif
                                                                            
                                                                            <img src="{{asset('streamtube/images/placeholder.gif')}}" data-src="{{$trending_video->video_image}}" class="slide-img1 placeholder" /></a>

                                                                            @if($trending_video->ppv_amount > 0) 
                                                                                @if(!$trending_video->ppv_status)

                                                                                    <div class="video_amount">
                                                                                        {{tr('pay')}} - {{Setting::get('currency')}}{{$trending_video->ppv_amount}}
                                                                                    </div>

                                                                                @endif 

                                                                            @endif

                                                                            <div class="video_duration">
                                                                                {{$trending_video->duration}}
                                                                            </div>

                                                                        </div>

                                                                        <div class="video-details">

                                                                        </div>

                                                                        <span class="video_views">

                                                                          <div class="video-head">

                                                                            <!-- <img src="{{$trending_video->video_image}}"> -->

                                                                            <a href="{{$trending_video->url}}">{{$trending_video->title}}</a>

                                                                            <i class="fa fa-eye"></i> {{$trending_video->watch_count}} {{tr('views')}}
                                                                            {{ common_date($trending_video->created_at) }}

                                                                          </div>
                                                                          
                                                                        </span>

                                                                    </div>

                                                                </div>

                                                                @endforeach 

                                                            @else

                                                                <center><img src="{{asset('images/no-result.jpg')}}" class="img-responsive aonuto-margin"> </center>
                                                           
                                                            @endif
                                                
                                                        </div>
                                                
                                                    </div>
                                                
                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </li>
                        <!-- end home -->
                        <!-- start r4d video -->
                        @include('user.channels.partials.r4d_video')
                        <!-- end r4d video -->
                        @include('user.channels.partials.video')
                        <!-- end videos section -->
                        <li role="tabpanel" class="tab-pane" id="about">

                            <div class="recom-area abt-sec">
                                <div class="abt-sec-head">
                                    <h5><?= $channel->description?></h5>
                                </div>
                            </div>

                        </li>

                        <li role="tabpanel" class="tab-pane" id="playlist">

                            <div class="recom-area abt-sec">

                                <div class="abt-sec-head">

                                    <div class="new-history1">

                                        <div class="content-head">

                                            <div>

                                                <h4 style="color: #000; display: inline;">
                                                {{tr('playlists')}}&nbsp;&nbsp;
                                                </h4>
                                                
                                                @if(Auth::check())
                                                    
                                                    @if(\Auth::user()->id == $channel->user_id)
                                                    
                                                        <button class="share-new global_playlist_id pull-right btn btn-info" style="color: #fff" id="{{ $channel->id }}"><i class="material-icons">playlist_add</i>{{ tr('playlist') }}
                                                        </button>
                                                
                                                    @endif
                                                
                                                @endif

                                            </div>

                                        </div>

                                        <div class="recommend-list row">

                                            @if(count($channel_playlists) > 0) 

                                                @foreach($channel_playlists as $channel_playlist_details)

                                                    <div class="slide-box recom-box">
                                                        
                                                        <div class="slide-image">

                                                            <a href="{{route('user.playlists.view', ['playlist_id' => $channel_playlist_details->playlist_id, 'playlist_type' => PLAYLIST_TYPE_CHANNEL ])}}">
                                                                <img src="{{asset('streamtube/images/placeholder.gif')}}" data-src="{{$channel_playlist_details->picture}}" class="slide-img1 placeholder" />
                                                            </a> 

                                                            @if(Auth::check())
                                                              
                                                                @if(\Auth::user()->id == $channel->user_id)
                                                                    <div class="video_amount">

                                                                        <a href="{{route('user.playlists.delete', ['playlist_id' => $channel_playlist_details->playlist_id])}}" onclick="return confirm(&quot;{{ substr($channel_playlist_details->title, 0 , 15)}} - {{tr('user_playlist_delete_confirm') }}&quot;)" class="playlist-delete"><i class="fa fa-trash"></i></a>

                                                                    </div>

                                                                @endif
                                                            
                                                            @endif

                                                            <div class="video_duration">
                                                                {{$channel_playlist_details->total_videos}} {{tr('videos')}}
                                                            </div>

                                                        </div>

                                                        <div class="video-details recom-details">

                                                            <div class="video-head">
                                                                <a href="{{route('user.playlists.view', ['playlist_id' => $channel_playlist_details->playlist_id])}}">{{$channel_playlist_details->title}}</a>
                                                            </div>

                                                            <span class="video_views">
                                                                <div>

                                                                </div>
                                                                {{ common_date($channel_playlist_details->created_at) }}
                                                            </span>

                                                        </div>
                                                        <!--end of video-details-->

                                                    </div>

                                                    <div id="new_playlist">

                                                    </div>

                                                @endforeach 

                                            @else

                                                <div id="new_playlist">

                                                </div>

                                                <img src="{{asset('images/no-result.jpg')}}" class="img-responsive auto-margin" id="no_playlist"> 

                                            @endif

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </li>

                        <li role="tabpanel" class="tab-pane" id="payment_managment">
                            
                            <div class="recom-area abt-sec">

                                <div class="abt-sec-head">

                                    <div class="new-history1">

                                        <div class="content-head">
                                            <div>
                                                <h4 style="color: #000;">{{tr('payment_videos')}}</h4>
                                            </div>
                                        </div>
                                        <!--end of content-head-->

                                        <!-- dashboard -->
                                        <div class="row">

                                            <!-- <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                <div class="ppv-dashboard">
                                                    <div class="ppv-dashboard-left">
                                                        <img src="{{asset('images/video-camera.png')}}">
                                                    </div>
                                                    <div class="ppv-dashboard-right">
                                                        <p>Total videos</p>
                                                        <h2 class="">150</h2>
                                                    </div>
                                                </div>
                                            </div> -->
                                            <!-- <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                <div class="ppv-dashboard">
                                                    <div class="ppv-dashboard-left">
                                                        <img src="{{asset('images/video-cash.png')}}">
                                                    </div>
                                                    <div class="ppv-dashboard-right">
                                                        <p>paid videos</p>
                                                        <h2 class="">100</h2>
                                                    </div>
                                                </div>
                                            </div> -->

                                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                           
                                                <div class="ppv-dashboard">
                                           
                                                    <div class="ppv-dashboard-left">
                                                        <img src="{{asset('images/dollar.png')}}">
                                                    </div>
                                           
                                                    <div class="ppv-dashboard-right">
                                                        <p>{{tr('revenue')}}</p>
                                                        <h2 class="">{{Setting::get('currency')}} {{getAmountBasedChannel($channel->id)}}</h2>
                                                    </div>
                                           
                                                </div>
                                           
                                            </div>

                                        </div>
                                        <!-- dashboard -->

                                        @if($payment_videos->count > 0)

                                        <ul class="history-list">

                                            @foreach($payment_videos->data as $i => $video)

                                            <li class="sub-list row border-0">
                                               
                                                <div class="main-history">
                                               
                                                    <div class="history-image">
                                               
                                                        <a href="{{$video->url}}"><img src="{{$video->video_image}}"></a> @if($video->ppv_amount > 0) @if(!$video->ppv_status)

                                                        <div class="video_amount">

                                                            {{tr('pay')}} - {{Setting::get('currency')}} {{$video->ppv_amount}}

                                                        </div>

                                                        @endif @endif

                                                        <div class="video_duration">
                                                            {{$video->duration}}
                                                        </div>

                                                    </div>
                                                    <!--history-image-->

                                                    <div class="history-title">
                                               
                                                        <div class="history-head row">
                                               
                                                            <div class="cross-title">
                                               
                                                                <h5 class="payment_class unset-height"><a href="{{$video->url}}">{{$video->title}}</a></h5>

                                                                <span class="video_views">
                                                                    <i class="fa fa-eye"></i> {{$video->watch_count}} {{tr('views')}} <b>.</b> 
                                                                    {{ common_date($video->created_at) }}
                                                                </span>

                                                            </div>
                                               
                                                            <div class="cross-mark">
                                                                <a onclick="return confirm(&quot;{{ substr($video->title, 0 , 15)}}.. {{tr('user_video_delete_confirm') }}&quot;)" href="{{route('user.delete.video' , array('id' => $video->video_tape_id))}}"><i class="fa fa-times" aria-hidden="true"></i></a>
                                                            </div>
                                                            <!--end of cross-mark-->
                                               
                                                        </div>
                                                        <!--end of history-head-->

                                                        <div class="description">
                                                            <?= $video->description?>
                                                        </div>
                                                        <!--end of description-->

                                                        <div class="label-sec">
                                                            @if($video->amount > 0)

                                                            <span class="label label-success">{{tr('ad_amount')}} - ${{$video->amount}}</span> @endif @if($video->user_ppv_amount > 0)
                                                            <span class="label label-info">{{tr('ppv_revenue')}} - ${{$video->user_ppv_amount}}</span> @endif
                                                        </div>
                                                        <span class="stars">
                                                            <a><i @if($video->ratings >= 1) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                            <a><i @if($video->ratings >= 2) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                            <a><i @if($video->ratings >= 3) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                            <a><i @if($video->ratings >= 4) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                            <a><i @if($video->ratings >= 5) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                        </span>
                                                    </div>
                                                    <!--end of history-title-->

                                                </div>
                                                <!--end of main-history-->
                                            </li>

                                            @endforeach

                                            <span id="payment_videos_list"></span>

                                            <div id="payment_video_loader" style="display: none;">

                                                <h1 class="text-center"><i class="fa fa-spinner fa-spin" style="color:#ff0000"></i></h1>

                                            </div>

                                            <div class="clearfix"></div>

                                            <button class="pull-right btn btn-info mb-15" onclick="getPaymentVideos()" style="color: #fff">{{tr('view_more')}}</button>

                                            <div class="clearfix"></div>

                                        </ul>

                                        @else

                                        <img src="{{asset('images/no-result.jpg')}}" class="img-responsive auto-margin"> @endif

                                        <?php /* @if(count($payment_videos) > 0)

                                        @if($payment_videos)
                                        <div class="row">
                                        <div class="col-md-12">
                                        <div align="center" id="paglink"><?php echo $payment_videos->links(); ?></div>
                                        </div>
                                        </div>
                                        @endif 

                                        @endif */ ?>

                                    </div>

                                </div>
                        
                            </div>

                        </li>

                        <!-- DONT REMOVE THIS. LIVE VIDEO LISTS -->

                        @include('user.channels._live_taps') 

                    </ul>

                    <div class="sidebar-back"></div>

                </div>

            </div>

        </div>

    </div>

    <!-- PLAYLIST POPUPSTART -->

    <div class="modal fade global_playlist_id_modal" id="global_playlist_id_{{$channel->id}}" role="dialog">

        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">

                <!-- if user logged in let create, update playlist -->

                @if(Auth::check())

                    <div class="modal-header">

                        <button type="button" class="close" data-dismiss="modal">&times;</button>

                        <h4 class="modal-title">{{tr('save_to')}}</h4>

                    </div>

                    <div class="modal-footer">

                        <div class="more-content">

                            <div onclick="$('#create_playlist_form').toggle()">

                                <label><i class="fa fa-plus"></i> {{tr('create_playlist')}}</label>

                            </div>

                            <div class="" id="create_playlist_form" style="display: none">

                                <div class="form-group">

                                    <input type="text" name="playlist_title" id="playlist_title" class="form-control" placeholder="{{tr('playlist_name_placeholder')}}" required>

                                    <label for="video" class="control-label">{{tr('videos')}}</label>

                                    <div>

                                        <select id="video_tapes_id" name="video_tapes_id[]" class="form-control select2" data-placeholder="{{tr('select_video_tapes')}}" multiple style="width: 100% !important" required>

                                            @if(count($videos) > 0) 

                                                @foreach($videos as $video_tapes_details) 

                                                    @if($video_tapes_details->is_approved == YES)

                                                    <option value="{{$video_tapes_details->video_tape_id}}"> {{ $video_tapes_details->title }}</option>

                                                    @endif 

                                                @endforeach 

                                            @endif

                                        </select>

                                    </div>

                                    <div class="" style="display: none;">

                                        <label for="playlist_privacy">Privacy</label>
                                       
                                        <select id="playlist_privacy" name="playlist_privacy" class="form-control">
                                            <option value="PUBLIC">PUBLIC</option>
                                            <option value="PRIVETE">PRIVETE</option>
                                            <option value="UNLISTED">UNLISTED</option>
                                        </select>
                                    
                                    </div>
                                
                                </div>

                                <button class="btn btn-primary" onclick='playlist_save("{{ $channel->id }}")'>{{ tr('create') }}
                                </button>

                            </div>

                        </div>

                    </div>

                    <!-- if user not logged in ask for login -->

                @else

                    <div class="menu4 top nav-space">

                        <p>{{tr('signid_for_playlist')}}</p>

                        <a href="{{route('user.login.form')}}" class="btn btn-sm btn-primary">{{tr('login')}}</a>

                    </div>

                @endif

            </div>
            <!-- modal content ends -->

        </div>

    </div>

    <!-- PLAYLIST POPUPEND -->

    @include('user.channels._go_live_form')

@endsection 

@section('scripts')

    <script>
        
        function change_adstatus(val, id) {

            var url = "{{route('user.ad_request')}}";

            $.ajax({
                url: url,
                method: "POST",
                data: {
                    id: id,
                    status: val
                },
                success: function(result) {

                    if (result.success == true) {

                        if (result.status == 1) {

                            $("#ad_status_" + id).html("{{tr('disable_ad')}}");

                        } else {

                            $("#ad_status_" + id).html("{{tr('enable_ad')}}");
                        }

                        alert("Ad Status Changed Successfully");
                    }
                }

            });

        }

        var stopScroll = false;

        var searchLength = "{{count($videos)}}";

        var stopPaymentScroll = false;

        var searchPaymentLength = "{{$payment_videos->count}}";

        function getVideos() {

            if (searchLength > 0) {

                videos(searchLength);

            }
        }

        function getPaymentVideos() {

            if (searchPaymentLength > 0) {

                payment_videos(searchPaymentLength);

            }
        }

        /*$(window).scroll(function() {

            if($(window).scrollTop() == $(document).height() - $(window).height()) {

                var value = $('ul#channel-navigation-menu').find('li.active').attr('id');

                //alert(value);

                if (value == 'videos_sec') {

                    if (!stopScroll) {

                        // console.log("New Length " +searchLength);

                        if (searchLength > 0) {

                            videos(searchLength);

                        }

                    }
                }

                if (value == 'payment_managment_sec') {

                    if (!stopPaymentScroll) {

                        // console.log("New Length " +searchLength);

                        if (searchPaymentLength > 0) {

                            payment_videos(searchPaymentLength);

                        }

                    }
                }

            }

        });*/

        function videos(cnt) {

            channel_id = "{{$channel->id}}";

            $.ajax({

                type: "post",

                url: "{{route('user.video.get_videos')}}",

                beforeSend: function() {

                    $("#video_loader").fadeIn();
                },

                data: {
                    skip: cnt,
                    channel_id: channel_id
                },

                async: false,

                success: function(data) {

                    $("#videos_list").append(data.view);

                    if (data.length == 0) {

                        stopScroll = true;

                    } else {

                        stopScroll = false;

                        // console.log(searchLength);

                        // console.log(data.length);

                        searchLength = parseInt(searchLength) + data.length;

                        // console.log("searchLength" +searchLength);

                    }

                },

                complete: function() {

                    $("#video_loader").fadeOut();

                },

                error: function(data) {

                },

            });

        }

        function payment_videos(cnt) {

            channel_id = "{{$channel->id}}";

            $.ajax({

                type: "post",

                url: "{{route('user.video.payment_mgmt_videos')}}",

                beforeSend: function() {

                    $("#payment_video_loader").fadeIn();
                },

                data: {
                    skip: cnt,
                    channel_id: channel_id
                },

                async: false,

                success: function(data) {

                    $("#payment_videos_list").append(data.view);

                    if (data.length == 0) {

                        stopPaymentScroll = true;

                    } else {

                        stopPaymentScroll = false;

                        // console.log(searchLength);

                        // console.log(data.length);

                        searchPaymentLength = parseInt(searchPaymentLength) + data.length;

                        // console.log("searchLength" +searchLength);

                    }

                },

                complete: function() {

                    $("#payment_video_loader").fadeOut();

                },

                error: function(data) {

                },

            });

        }

        $(document).on('ready', function() {

            $("#copy-embed1").on("click", function() {
                $('#popup1').modal('hide');
            });

            $('.global_playlist_id').on('click', function(event) {

                event.preventDefault();

                var channel_id = $(this).attr('id');

                $('#global_playlist_id_' + channel_id).modal('show');

            });

        });

        function playlist_save(channel_id) {

            var title = $("#playlist_title").val();

            var privacy = $("#playlist_privacy").val();

            var video_tapes_id = $("#video_tapes_id").val();
           
            var playlist_type = "<?php echo PLAYLIST_TYPE_CHANNEL ?>";

            if (title == '') {

                alert("Title for playlist required");

            }
            if (video_tapes_id == null) {

                alert("Please Choose videos to create playlist");

            } else {

                $.ajax({

                    url: "{{route('user.channel.playlists.save')}}",
                    data: {
                        title: title,
                        channel_id: channel_id,
                        privacy: privacy,
                        video_tapes_id: video_tapes_id,
                        playlist_type: playlist_type
                    },

                    type: "post",
                    success: function(data) {

                        if (data.success) {

                            $('#playlist_title').removeAttr('value');

                            $('#video_tapes_id').val(null).trigger('change');

                            $('#global_playlist_id_' + channel_id).modal('hide');
                           
                            $('#no_playlist').hide();

                            $('#new_playlist').append(data.new_playlist_content);

                            alert(data.message);

                        } else {

                            alert(data.error_messages);

                        }

                    },
                    error: function(data) {

                    },
                });
            }

        }

        function live_video() {
            $.ajax({
                url : "{{route('user.check_user_live_video')}}",
                type: "get",
                success : function(data) {
                   
                   if(data.success) {
                   
                      $("#start_broadcast").modal('show');
                      
                   } else {

                      if(confirm(data.error_messages)) {

                        $.ajax({
                            url : "{{route('user.erase_old_live_videos')}}",
                            type: "get",
                            success : function(data) {
                            },
                            error : function(data) {
                            },
                        });

                    }

                   }
                },
       
               error : function(data) {
               },
           })
       }

    </script>

@endsection