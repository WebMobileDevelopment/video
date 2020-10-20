@if($wishlists)

    @if(count($wishlists->items) > 0)

        <div class="slide-area">
            
            <div class="box-head">
                <h3>{{tr('wishlist')}}</h3>
            </div>

            <div class="box">

                @foreach($wishlists->items as $wishlist)

                    <div class="slide-box">
                        
                        <div class="slide-image">
                            
                            <a href="{{$wishlist->url}}">

                                <img src="{{asset('streamtube/images/placeholder.gif')}}" data-src="{{$wishlist->video_image}}"class="slide-img1 placeholder" />

                            </a>

                            @if($wishlist->ppv_amount > 0)
                                @if(!$wishlist->ppv_status)
                                    <div class="video_amount">

                                    {{tr('pay')}} - {{Setting::get('currency')}}{{$wishlist->ppv_amount}}

                                    </div>
                                @endif
                            @endif

                            <div class="video_mobile_views">
                                {{$wishlist->watch_count}} {{tr('views')}}
                            </div>

                            <div class="video_duration">
                                {{$wishlist->duration}}
                            </div>
                        </div>
                        <!--end of slide-image-->

                        <div class="video-details">

                            <div class="video-head">
                                <a href="{{$wishlist->url}}">{{$wishlist->title}}</a>
                            </div>

                            <span class="video_views">
                                <div>
                                    <a href="{{route('user.channel',$wishlist->channel_id)}}">
                                        {{$wishlist->channel_name}}
                                    </a>
                                </div>
                                <div class="hidden-mobile">
                                    <i class="fa fa-eye"></i> 
                                    {{$wishlist->watch_count}} {{tr('views')}} 
                                    <b>.</b> 
                                    {{$wishlist->publish_time}}
                                </div>
                            </span>
                        
                        </div>

                        <!--end of video-details-->
                    
                    </div><!--end of slide-box-->
                
                @endforeach
       
            </div><!--end of box--> 
        </div><!--end of slide-area-->

    @endif

@endif