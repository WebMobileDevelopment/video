@if(Auth::check())

<li role="tabpanel" class="tab-pane" id="live_videos_billing">

    <div class="slide-area recom-area abt-sec">
        <div class="abt-sec-head">
            
             <div class="new-history1">
                    <div class="content-head">
                        <div><h4 style="color: #000;">{{tr('live_history')}}</h4></div>              
                    </div><!--end of content-head-->

                    @if(count($live_video_history->data) > 0)

                        <ul class="history-list">

                            @foreach($live_video_history->data as $i => $video)

                            <li class="sub-list row">
                                <div class="main-history">
                                     <div class="history-image">
                                        <a><img src="{{$video->video_image}}"></a> 
                                        @if($video->amount > 0)
                                            <div class="video_amount">

                                            {{tr('pay')}} - {{Setting::get('currency')}} {{$video->amount}}

                                            </div>
                                            
                                        @endif
                                        <div class="video_duration">
                                            {{$video->paid_date}}
                                        </div>                          
                                    </div><!--history-image-->

                                    <div class="history-title">
                                        <div class="history-head row">
                                            <div class="cross-title">
                                                <h5 class="payment_class"><a>{{$video->title}}</a></h5>
                                                

                                                <span class="video_views">
                                                     
                                                    {{$video->paid_date}}
                                                </span> 

                                            </div> 
                                                                   
                                        </div> <!--end of history-head--> 

                                        <div class="description">
                                            <p>{{$video->description}}</p>
                                        </div><!--end of description--> 


                                        <div>
                                            @if($video->user_amount > 0)
                                            <span class="label label-success">${{$video->user_amount}}</span>
                                            @endif
                                            
                                        </div>
                                                                                        
                                    </div><!--end of history-title--> 
                                    
                                </div><!--end of main-history-->
                            </li>    

                            @endforeach

                            <span id="live_videos_list"></span>

                            <div id="live_video_loader" style="display: none;">
                                    
                                <h1 class="text-center"><i class="fa fa-spinner fa-spin" style="color:#ff0000"></i></h1>

                            </div>

                            <div class="clearfix"></div>


                            <button class="pull-right st_video_upload_btn subscribe_btn" onclick="getLiveVideos()" style="color: #fff">{{tr('view_more')}}</button>

                            <div class="clearfix"></div>
                           

                        </ul>

                    @else

                        <img src="{{asset('images/no-result.jpg')}}" class="img-responsive auto-margin">

                    @endif


                </div>

        </div>
    </div>

</li>

@endif

@if(Setting::get('broadcast_by_user') == 1 || (Auth::check() ? Auth::user()->is_master_user == 1 : 0))

<li role="tabpanel" class="tab-pane" id="live_videos_section">

    <div class="slide-area recom-area abt-sec">
        <div class="abt-sec-head">
            
             <div class="new-history1">
                    <div class="content-head">
                        <div><h4 style="color: #000;">{{tr('live_videos')}}&nbsp;&nbsp;
                       
                        </h4></div>              
                    </div><!--end of content-head-->

                    @if(count($live_videos) > 0)

                        <ul class="history-list">

                            @foreach($live_videos as $i => $live_video)

                                <?php 


                                    $userId = Auth::check() ? Auth::user()->id : '';

                                    $url = ($live_video->amount > 0) ? route('user.payment_url', array('id'=>$live_video->id, 'user_id'=>$userId)): route('user.live_video.start_broadcasting' , array('id'=>$live_video->unique_id,'c_id'=>$live_video->channel_id));
                                ?>


                                <li class="sub-list row">
                                    <div class="main-history">
                                         <div class="history-image">

                                            <a href="{{$url}}">

                                                <img src="{{$live_video->snapshot}}" /> 

                                            </a>

                                            <div class="video_duration text-uppercase">
                                                 @if($live_video->amount > 0) 

                                                    {{tr('pay')}} - ${{$live_video->amount}} 

                                                @else {{tr('free')}} @endif
                                            </div>                        
                                        </div><!--history-image-->

                                        <div class="history-title">
                                            <div class="history-head row">
                                                <div class="cross-title">
                                                    <h5 class="payment_class">

                                                        @if($live_video->amount > 0) 
                                                            <a data-toggle="modal" data-target="#paypal_{{$live_video->id}}" style="cursor: pointer;">
                                                        @else
                                                    
                                                        <a href="{{$url}}">

                                                        @endif

                                                            {{$live_video->title}}

                                                        </a>


                                                    </h5>
                                                   
                                                    <span class="video_views">
                                                        <i class="fa fa-eye"></i> {{$live_video->viewers_cnt}} {{tr('views')}} <b>.</b> 
                                                        {{$live_video->created_at->diffForHumans()}}
                                                    </span>
                                                </div> 
                                                <!--end of cross-mark-->                       
                                            </div> <!--end of history-head--> 

                                            <div class="description">
                                                <p>{{$live_video->description}}</p>
                                            </div><!--end of description--> 
                                        </div><!--end of history-title--> 
                                        
                                    </div><!--end of main-history-->
                               
                                </li>    

                            @endforeach
                           
                        </ul>

                    @else

                       <p style="color: #000">{{tr('no_video_found')}}</p>

                    @endif



                    @if(count($live_videos) > 0)

                        @if($live_videos)
                        <div class="row">
                            <div class="col-md-12">
                                <div align="center" id="paglink"><?php echo $live_videos->links(); ?></div>
                            </div>
                        </div>
                        @endif
                    @endif
                    
                </div>

            </div>
    </div>

</li>

@endif