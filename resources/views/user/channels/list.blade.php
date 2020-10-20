@extends( 'layouts.user' )

@section( 'styles' )

<link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/custom-style.css')}}"> 
<style>
	.btn_area * {
		color: white;
	}
	.creat_panel_btn {
		background: #337ab7;
	}
</style>	
@endsection

@section('content')

<div class="y-content">

	<div class="row content-row">

		@include('layouts.user.nav')

		<div class="page-inner col-sm-9 col-md-10">

			@include('notification.notify')

				<div class="slide-area1 recom-area abt-sec">
					
					<div class="abt-sec-head">
						
						<div class="new-history">
				                
			                <div class="content-head">
			                    
			                    <div class="pull-left"><h4 class="bold" style="color: #000;">{{tr('channels')}}&nbsp;&nbsp;			  
			                    </h4></div>      

			                    @if(Auth::check())    	

				                    @if(Setting::get('create_channel_by_user') == CREATE_CHANNEL_BY_USER_ENABLED)

					                    @if((count($response->channels) == 0) || Setting::get('multi_channel_status'))  

										<div class="text-center" style="margin-bottom: 10px;">

											<a class="st_video_upload_btn creat_panel_btn btn_area" href="{{route('user.create_channel')}}"><i class="fa fa-tv"></i>
											<span>{{tr('create_channel')}}</span></a>

										</div>

										@endif

				                    @endif

			                    @endif

			                    <div class="clearfix"></div>    
			               
			                </div><!--end of content-head-->

			                @if(count($response->channels) > 0)

			                    <ul class="history-list">

			                        @foreach($response->channels as $i => $channel)

			                        <li class="sub-list row">
			                           
			                            <div class="main-history">
			                           
			                                 <div class="history-image">
			                           
			                                    <a href="{{route('user.channel',$channel->channel_id)}}">
			                                    	
			                                    	<img src="{{asset('streamtube/images/placeholder.gif')}}" data-src="{{$channel->picture}}" class="slide-img1 placeholder" />
			                                    </a>
			                                    <div class="video_duration">
			                                        {{$channel->no_of_videos}} {{tr('videos')}}
			                                    </div>                        
			                                </div><!--history-image-->

			                                <div class="history-title">
			                                    
			                                    <div class="history-head row">
			                                    
			                                        <div class="cross-title">
			                                            
			                                            <h5 class="payment_class mb-3" style="padding: 0px;"><a href="{{route('user.channel',$channel->channel_id)}}">{{$channel->title}}</a></h5>
			                                            
			                                            <span class="video_views">
			                                            	 <i class="fa fa-eye"></i> {{$channel->no_of_subscribers}} {{tr('subscribers')}} <b>.</b> 
					                                        {{ common_date($channel->created_at) }}
					                                    </span>
			                                        </div> 
			                                        
			                                        @if(Auth::check())

			                                        	@if($channel->user_id != Auth::user()->id)

															<div class="pull-right upload_a">

					                                            @if (!$channel->subscribe_status)

																	<a class="st_video_upload_btn subscribe_btn " href="{{route('user.subscribe.channel', array('user_id'=>Auth::user()->id, 'channel_id'=>$channel->channel_id))}}" style="color: #fff !important"><i class="fa fa-envelope"></i>&nbsp;{{tr('subscribe')}} &nbsp; {{$channel->no_of_subscribers}}</a>

																@else 

																	<a class="st_video_upload_btn " href="{{route('user.unsubscribe.channel', array('subscribe_id'=>$channel->subscribe_status))}}" onclick="return confirm(&quot;{{ $channel->title }} -  {{tr('user_channel_unsubscribe_confirm') }}&quot;)" ><i class="fa fa-times"></i>&nbsp;{{tr('un_subscribe')}} &nbsp; {{$channel->no_of_subscribers}}</a>
																@endif
					                                        </div>

				                                        @else
					                                        <div class="pull-right upload_a">
																@if($channel->no_of_subscribers > 0)
																<a class="st_video_upload_btn subscribe_btn " href="{{route('user.channel.subscribers', array('channel_id'=>$channel->channel_id))}}" style="color: #fff !important;text-decoration: none"><i class="fa fa-users"></i>&nbsp;{{tr('subscribers')}} &nbsp; {{$channel->no_of_subscribers}} 
																</a>
							                                    <div class="description hidden-xs">
							                                    </div><!--end of description--> 

																@endif
															</div>
														@endif
			                                        
			                                        @endif

			                                    </div> <!--end of history-head--> 

			                                    <div class="description hidden-xs">
			                                        <!-- <?= $channel->description?> -->
			                                    </div><!--end of description-->                  
			                                </div><!--end of history-title--> 
			                                
			                            </div><!--end of main-history-->

			                            <div class="clearfix"></div>
			                        
			                        </li>    

			                        @endforeach
			                       
			                    </ul>

			                @else

			                @if(Auth::user())
				                @if(!Auth::user()->user_type)

				                	<!-- <p><small><b>Please create channel</b></small></p> -->
									<!-- ('notes_for_channel') -->
									
				                @endif
				            @endif

			                   <img src="{{asset('images/no-result.jpg')}}" class="img-responsive auto-margin">

			                @endif

			                @if(count($response->channels) > 0)

			                    @if($response->channels)
			                    <div class="row">
			                        <div class="col-md-12">
			                            <div align="center" id="paglink"><?php echo $response->pagination; ?></div>
			                        </div>
			                    </div>
			                    @endif

			                @endif
				                
				        </div>

					</div>

				</div>

			<div class="sidebar-back"></div> 

		</div>

	</div>

</div>

@endsection