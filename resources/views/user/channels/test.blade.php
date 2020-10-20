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

</style>

@endsection 

@section('content')

<div class="y-content">

<div class="row content-row">

@include('layouts.user.nav')

<div class="page-inner col-sm-9 col-md-10">

<div class="slide-area1">
@include('notification.notify')


<div class="branded-page-v2-top-row">

<div class="branded-page-v2-header channel-header yt-card">
<div id="gh-banner">

<div id="c4-header-bg-container" class="c4-visible-on-hover-container  has-custom-banner">
<div class="hd-banner">
<div class="hd-banner-image"></div>
</div>

<!-- <a class="channel-header-profile-image spf-link" href="">
<img class="channel-header-profile-image" src="{{$channel->picture}}" title="{{$channel->name}}" alt="{{$channel->name}}">
</a> -->
</div>

</div>

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
<a class="st_video_upload_btn" href="{{route('user.video_upload', ['id'=>$channel->id])}}"><i class="fa fa-plus-circle"></i> {{tr('upload_video')}}</a>

@if(Setting::get('broadcast_by_user') == 1 || Auth::user()->is_master_user == 1)

	<button class="st_video_upload_btn text-uppercase" data-toggle="modal" data-target="#start_broadcast">
		<i class="fa fa-video-camera"></i> 
		{{tr('go_live')}}
	</button>

@endif
<a class="st_video_upload_btn" href="{{route('user.channel_edit', $channel->id)}}"><i class="fa fa-pencil"></i> {{tr('edit_channel')}}</a>
<a class="st_video_upload_btn" onclick="return confirm('Are you sure?');" href="{{route('user.delete.channel', ['id'=>$channel->id])}}"><i class="fa fa-trash"></i> {{tr('delete_channel')}}</a>

@if(Setting::get('broadcast_by_user') == 1 || (Auth::check() ? Auth::user()->is_master_user == 1 : 0))

		<div id="start_broadcast" class="modal fade" role="dialog">
		    <div class="modal-dialog">

		    <!-- Modal content-->
		    <div class="modal-content">
		        <div class="modal-header start_brocadcast_form">
		            <button type="button" class="close" data-dismiss="modal">&times;</button>
		        	<h4 class="modal-title text-uppercase text-center">{{tr('start_broadcast')}}</h4>
		        </div>

		        <div class="modal-body body-modal">

		      	<form method="post" action="{{route('user.live_video.broadcast')}}">

			      	<input type="hidden" name="channel_id" value="{{$channel->id}}">

			      	<input type="hidden" name="user_id" value="{{$channel->user_id}}">

					<!-- Text input-->

                    <div class="form-group form-group1">
                    	<input type="text" class="form-control signup-form1" placeholder="{{tr('enter_title')}}" id="title" name="title" required="">
                   	</div>


					<div class="form-group radio-btn text-left">

						<label class="control-label col-xs-4 col-sm-3 zero-padding" for="optradio">{{tr('payment')}}</label>

						<div class="col-xs-8 col-sm-8">

						    <label class="radio-inline width-100" for="reqType-1">
								<input type="radio" id="reqType-1" checked="checked" class="option-input radio" name="payment_status" onchange="return $('#price').hide();" value="0">{{tr('free')}}
							</label>
							<label class="radio-inline">
								<input type="radio" id="reqType-0" class="option-input radio" name="payment_status" onchange="return $('#price').show()" value="1">{{tr('paid')}}
						    </label>
				      	</div>
					
					</div>

                  	<div class="clearfix"></div>

                 	<!-- ======amount===== -->
                    <div class="form-group form-group1" style="display: none" id="price">
                        <input id="Amount" name="amount" type="number" placeholder="Amount" pattern="[0-9]{0,}" class="form-control signup-form1">
                    </div>
                 

                    <div class="form-group form-group1">
					    <textarea id="description" name="description" class="form-control signup-form1" rows="5" id="comment" placeholder="{{tr('description')}}"></textarea>
					</div>

                @if(Setting::get('broadcast_by_user') == 1 || Auth::user()->is_master_user == 1) 
                  	<button class="btn btn-danger" type="submit" id="submitButton" name="submitButton">{{tr('broadcast')}}</button>
                @else

                  	<button class="btn btn-danger" type="button" onclick="return alert('Broadcast option is disabled by admin.');">{{tr('broadcast')}}</button>

                @endif

		      </form>

		      </div>

		      <div class="clearfix"></div>
		    </div>

		  </div>
		
		</div>

@endif

@endif

@endif

@if($channel->user_id != Auth::user()->id)

@if (!$subscribe_status)

<a class="st_video_upload_btn subscribe_btn" href="{{route('user.subscribe.channel', array('user_id'=>Auth::user()->id, 'channel_id'=>$channel->id))}}" style="color: #fff !important">{{tr('subscribe')}} &nbsp; {{$subscriberscnt}} </a>

@else 

<a class="st_video_upload_btn" href="{{route('user.unsubscribe.channel', array('subscribe_id'=>$subscribe_status))}}" onclick="return confirm('Are you sure want to Unsubscribe the channel?')">{{tr('un_subscribe')}} &nbsp; {{$subscriberscnt}}</a>

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
<a href="#about" class="yt-uix-button  spf-link  yt-uix-sessionlink yt-uix-button-epic-nav-item yt-uix-button-size-default" aria-controls="about" role="tab" data-toggle="tab">
<span class="yt-uix-button-content hidden-xs">{{tr('about_video')}}</span> 
<span class="visible-xs"><i class="fa fa-info channel-tab-icon"></i></span>
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


<li role="presentation" id="live_videos_billing_sec">
<a href="#live_videos_billing" class="yt-uix-button  spf-link  yt-uix-sessionlink yt-uix-button-epic-nav-item yt-uix-button-size-default" aria-controls="live_videos_billing" role="tab" data-toggle="tab">

<span class="yt-uix-button-content hidden-xs">{{tr('live_videos_billing')}}</span> 
<span class="visible-xs"><i class="fa fa-video-camera channel-tab-icon"></i> &nbsp;</span>
</a>
</li>
@endif
@endif
</ul>
</div>
</div>
</div>
</div>

<ul class="tab-content">

<li role="tabpanel" class="tab-pane active" id="home1">
<div class="feed-item-dismissable">
<div class="feed-item-main feed-item-no-author">
<div class="feed-item-main-content">
<div class="shelf-wrapper clearfix">
<div class="big-section-main new-history1">

<!-- <h4 class="content-head con-head">
	{{tr('watch_to_next')}}
</h4> -->

<div class="content-head">
	<h4 style="color: #000;">{{tr('watch_to_next')}}</h4>
</div>

<div class="lohp-shelf-content row">

<div class="lohp-medium-shelves-container col-md-12">
<div class="row">
@if(count($trending_videos) > 0)

@foreach($trending_videos as $index => $trending_video)

<div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">

<div class="slide-box recom-box big-box-slide mt-0 mb-15">
	<div class="slide-image">
		<a href="{{$trending_video->url}}"><img src="{{$trending_video->video_image}}"></a>
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
		<div class="video-head">
			<a href="{{$trending_video->url}}">{{$trending_video->title}}</a>
		</div>
		
        <span class="video_views">
            <i class="fa fa-eye"></i> {{$trending_video->watch_count}} {{tr('views')}} <b>.</b> 
            {{$trending_video->created_at}}
        </span>
	</div>
	
</div>
</div>

@endforeach

@else

<img src="{{asset('images/no-result.jpg')}}" class="img-responsive auto-margin">

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

<li role="tabpanel" class="tab-pane" id="videos">

<div class="recom-area abt-sec">
<div class="abt-sec-head">

<div class="new-history1">
<div class="content-head">
<div><h4 style="color: #000;">{{tr('videos')}}&nbsp;&nbsp;
@if(Auth::check())

<!-- @if(Auth::user()->id == $channel->user_id)
<small style="font-size: 12px">({{tr('note')}}:{{tr('ad_note')}} )</small>

@endif -->

@endif
</h4></div>              
</div><!--end of content-head-->

@if(count($videos) > 0)

<ul class="history-list">

@foreach($videos as $i => $video)


<li class="sub-list row border-0">
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
            <h5 class="payment_class unset-height">
            	@if(Auth::check())
	            	@if($channel->user_id == Auth::user()->id)
		            	@if($video->is_approved == YES)
		            		<span class="text-green" title="Admin Approved"><i class="fa fa-check-circle"></i></span>
		            	@else

		            		<span class="text-red" title="Admin Declined"><i class="fa fa-times"></i></span>

		            	@endif
		            @endif
	            @endif

            	<a href="{{$video->url}}">{{$video->title}}</a>
            </h5>
           	
            <span class="video_views">
                <i class="fa fa-eye"></i> {{$video->watch_count}} {{tr('views')}} <b>.</b> 
                {{$video->created_at}}
            </span>
        </div> 
		@if(Auth::check())
		@if($channel->user_id == Auth::user()->id)

		@if($video->status)
		<div class="cross-mark2">
	        
            <!-- <label style="float:none; margin-top: 6px;" class="switch hidden-xs" title="{{$video->ad_status ? tr('disable_ad') : tr('enable_ad')}}">
                <input id="change_adstatus_id" type="checkbox" @if($video->ad_status) checked @endif onchange="change_adstatus(this.value, {{$video->video_tape_id}})">
                <div class="slider round"></div>
            </label> -->

            <div class="btn-group show-on-hover">
	          	<button type="button" class="video-menu dropdown-toggle" data-toggle="dropdown">

		            <!-- <span class="hidden-xs">{{tr('action')}}</span>
		            <span class="caret"></span> -->
		            <i class="fa fa-ellipsis-v"></i>
	          	</button>

	          	<?php $total_amount = $video->amount + ppv_amount($video->video_tape_id); ?>

	          	<ul class="dropdown-menu dropdown-menu-right" role="menu">

		          	@if(Setting::get('is_payper_view') == 1)
		            <li><a data-toggle="modal" data-target="#pay-perview_{{$video->video_tape_id}}">{{tr('pay_per_view')}}</a></li>
		            @endif

		            @if($total_amount > 0) 
		            <li><a data-toggle="modal" data-target="#earning_{{$video->video_tape_id}}">{{tr('total_earning')}}</a></li>
		            <!-- <li><a data-toggle="modal" data-target="#earning">{{tr('total_earning')}}</a></li> -->
		            <li class="divider"></li>
		            @endif
		            
		            <li><a title="edit" href="{{route('user.edit.video', $video->video_tape_id)}}">{{tr('edit_video')}}</a></li>
		            <li><a title="delete" onclick="return confirm('Are you sure?');" href="{{route('user.delete.video' , array('id' => $video->video_tape_id))}}"> {{tr('delete_video')}}</a></li>
		            <li>
            			<a onclick="change_adstatus({{$video->ad_status}}, {{$video->video_tape_id}})" style="cursor: pointer;" id="ad_status_{{$video->video_tape_id}}">@if($video->ad_status) {{tr('disable_ad')}} @else {{tr('enable_ad')}} @endif</a>

            		</li>
	          	</ul>



	          	@if($total_amount > 0) 

					<div class="modal fade modal-top" id="earning_{{$video->video_tape_id}}" role="dialog">
					<!-- <div class="modal fade modal-top" id="earning" role="dialog"> -->
						<div class="modal-dialog bg-img modal-sm" style="background-image: url({{asset('images/popup-back.jpg')}});">

							<div class="modal-content earning-content">
								<div class="modal-header text-center">
							      	<button type="button" class="close" data-dismiss="modal">&times;</button>
							      	<h3 class="modal-title no-margin">{{tr('total_earnings')}}</h3>
							    </div>
							    <div class="modal-body text-center">
							    	<div class="amount-circle">
							    		<h3 class="no-margin">${{$total_amount}}</h3>
							   		</div>
							      	<p>{{tr('total_views')}} - {{$video->watch_count}}</p>
							      	<a href="{{route('user.redeems')}}">
							      		<button class="btn btn-danger top">{{tr('view_redeem')}}</button>
							      	</a>
							    </div>
							</div>
						</div>
					</div>

					@endif

						<!-- ========modal pay per view======= -->
					<div id="pay-perview_{{$video->video_tape_id}}" class="modal fade" role="dialog">
							<div class="modal-dialog">
								<div class="modal-content">
									<form  action="{{route('user.save.video-payment', $video->video_tape_id)}}" method="POST">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal">&times;</button>
											<h4 class="modal-title text-left" style="color: #000;">{{tr('pay_per_view')}}</h4>
										</div>
										<div class="modal-body pay-perview">
										   
										    	<h4 class="black-clr text-left">{{tr('type_of_user')}}</h4>
										    	<div>
													<label class="radio1">  <input id="radio1" type="radio" name="type_of_user"  value="{{NORMAL_USER}}" {{($video->type_of_user > 0) ? (($video->type_of_user == NORMAL_USER) ? 'checked' : '') : 'checked'}} required>
														<span class="outer"><span class="inner"></span></span>{{tr('normal_user')}}
													</label>
												</div>
												<div>
												    <label class="radio1">   <input id="radio2" type="radio" name="type_of_user" value="{{PAID_USER}}" {{($video->type_of_user == PAID_USER) ? 'checked' : ''}} required>
												    <span class="outer"><span class="inner"></span></span>{{tr('paid_user')}}
												</label>
											</div>
											<div>
											    <label class="radio1">
												    <input id="radio2" type="radio" name="type_of_user" value="{{BOTH_USERS}}" {{($video->type_of_user == BOTH_USERS) ? 'checked' : ''}} required>
												    <span class="outer"><span class="inner"></span></span>{{tr('both_user')}}
												</label>
											</div>

											<div class="clearfix"></div>

											<h4 class="black-clr text-left">{{tr('type_of_subscription')}}</h4>
											<div>

											    <label class="radio1">
												    <input id="radio2" type="radio" name="type_of_subscription" value="{{ONE_TIME_PAYMENT}}" {{($video->type_of_subscription > 0) ? (($video->type_of_subscription == ONE_TIME_PAYMENT) ? 'checked' : '') : 'checked'}} required>
												    <span class="outer"><span class="inner"></span></span>{{tr('one_time_payment')}}
												</label>
											</div>
											<div>

												{{{$video->type_of_subscription}}}
											    <label class="radio1">
												    <input id="radio2" type="radio" name="type_of_subscription" value="{{RECURRING_PAYMENT}}" {{($video->type_of_subscription == RECURRING_PAYMENT) ? 'checked' : ''}} required>
												    <span class="outer"><span class="inner"></span></span>{{tr('recurring_payment')}}
												</label>
											</div>

											<div class="clearfix"></div>

											<h4 class="black-clr text-left">{{tr('amount')}}</h4>
											<div>
						                       <input type="number" required value="{{$video->ppv_amount}}" name="ppv_amount" class="form-control" id="amount" placeholder="{{tr('amount')}}" step="any" maxlength="6">
						                  <!-- /input-group -->
						                
								            </div>

												
											
										<div class="clearfix"></div>
									</div>

									 <div class="modal-footer border-0">
								      	<div class="pull-left">
								      		@if($video->ppv_amount > 0)
								       			<a class="btn btn-danger" href="{{route('user.remove_pay_per_view', $video->video_tape_id)}}">{{tr('remove_pay_per_view')}}</a>
								       		@endif
								       	</div>
								        <div class="pull-right">
									        <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
									        <button type="submit" class="btn btn-info">Submit</button>
									    </div>
									    <div class="clearfix"></div>
								      </div>
							      </form>
							</div>
						</div>
					</div>	
					<!-- ========modal ends======= -->	

	        </div>                   
            
           
        </div>
        @else
        	<button class="btn btn-warning btn-small">{{tr('video_compressing')}}</button>
        @endif
        @endif
        @endif

                            <!--end of cross-mark-->                       
    </div> <!--end of history-head--> 
    
    <?php /*<div class="category"><b class="text-capitalize">{{tr('category_name')}} : </b> <a href="{{route('user.categories.view', $video->category_unique_id)}}" target="_blank">{{$video->category_name}}</a></div> */?>
    
    <!-- Live Video Section Start -->

    <div class="description">
        <?= $video->description?>
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


<span id="videos_list"></span>

<div id="video_loader" style="display: none;">

<h1 class="text-center"><i class="fa fa-spinner fa-spin" style="color:#ff0000"></i></h1>

</div>

<div class="clearfix"></div>


<button class="pull-right btn btn-info mb-15" onclick="getVideos()" style="color: #fff">{{tr('view_more')}}</button>

<div class="clearfix"></div>

</ul>

@else

<!-- <p style="color: #000">{{tr('no_video_found')}}</p> -->
<img src="{{asset('images/no-result.jpg')}}" class="img-responsive auto-margin">

@endif

</div>

</div>
</div>


</li>

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

<li role="tabpanel" class="tab-pane" id="about">

<div class="recom-area abt-sec">
<div class="abt-sec-head">
<h5><?= $channel->description?></h5>
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
	</div><!--end of content-head-->

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
    <a href="{{$video->url}}"><img src="{{$video->video_image}}"></a> 
    @if($video->ppv_amount > 0)
        @if(!$video->ppv_status)
            <div class="video_amount">

            {{tr('pay')}} - {{Setting::get('currency')}} {{$video->ppv_amount}}

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
            <h5 class="payment_class unset-height"><a href="{{$video->url}}">{{$video->title}}</a></h5>
            

            <span class="video_views">
                <i class="fa fa-eye"></i> {{$video->watch_count}} {{tr('views')}} <b>.</b> 
                {{$video->created_at}}
            </span> 

        </div> 
        <div class="cross-mark">
            <a onclick="return confirm('Are you sure?');" href="{{route('user.delete.video' , array('id' => $video->video_tape_id))}}"><i class="fa fa-times" aria-hidden="true"></i></a>
        </div><!--end of cross-mark-->                       
    </div> <!--end of history-head--> 

    <div class="description">
        <?= $video->description?>
    </div><!--end of description--> 


    <div class="label-sec">
    	@if($video->amount > 0)
    	<span class="label label-success">{{tr('ad_amount')}} - ${{$video->amount}}</span>
    	@endif
    	@if($video->user_ppv_amount > 0)
    	<span class="label label-info">{{tr('ppv_revenue')}} - ${{$video->user_ppv_amount}}</span>
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

<span id="payment_videos_list"></span>

<div id="payment_video_loader" style="display: none;">

<h1 class="text-center"><i class="fa fa-spinner fa-spin" style="color:#ff0000"></i></h1>

</div>

<div class="clearfix"></div>


<button class="pull-right btn btn-info mb-15" onclick="getPaymentVideos()" style="color: #fff">{{tr('view_more')}}</button>

<div class="clearfix"></div>


</ul>

@else

<img src="{{asset('images/no-result.jpg')}}" class="img-responsive auto-margin">

@endif

</div>

</div>
</div>

</li>

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

</ul>

<div class="sidebar-back"></div> 
</div>
</div>

</div>

</div>

@endsection

@section('scripts')

<script>

    function change_adstatus(val, id) {

        var url = "{{route('user.ad_request')}}";

        $.ajax({
            url : url,
            method : "POST",
            data : {id : id , status : val},
            success : function(result) {
  
                if (result.success == true) {

                	if (result.status == 1) {

                		$("#ad_status_"+id).html("{{tr('disable_ad')}}");

                	} else {

                		$("#ad_status_"+id).html("{{tr('enable_ad')}}");
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
	
	var stopLiveScroll = false;

	var searchLiveLength = @if(Auth::check()) "{{count($live_video_history->data)}}" @else 0 @endif;

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



	function getLiveVideos() {

		if (searchLiveLength > 0) {

			live_videos_history(searchLiveLength);

		}
	}

	function videos(cnt) {



    	channel_id = "{{$channel->id}}";

    	$.ajax({

    		type : "post",

    		url : "{{route('user.video.get_videos')}}",

    		beforeSend : function () {

				$("#video_loader").fadeIn();
			},

			data : {skip : cnt, channel_id : channel_id},

			async : false,

			success : function (data) {

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

			complete : function() {

				$("#video_loader").fadeOut();

			},

			error : function (data) {


			},

    	});

    }


	function payment_videos(cnt) {

    	channel_id = "{{$channel->id}}";

    	$.ajax({

    		type : "post",

    		url : "{{route('user.video.payment_mgmt_videos')}}",

    		beforeSend : function () {

				$("#payment_video_loader").fadeIn();
			},

			data : {skip : cnt, channel_id : channel_id},

			async : false,

			success : function (data) {

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

			complete : function() {

				$("#payment_video_loader").fadeOut();

			},

			error : function (data) {


			},

    	});

    }

    @if(Auth::check())

    function live_videos_history(cnt) {

    	channel_id = "{{$channel->id}}";

    	$.ajax({

    		type : "post",

    		url : "{{route('user.live.video.mgmt')}}",

    		beforeSend : function () {

				$("#live_video_loader").fadeIn();
			},

			data : {skip : cnt, channel_id : channel_id, id : "{{Auth::user()->id}}", token : "{{Auth::user()->token}}"},

			async : false,

			success : function (data) {

				$("#live_videos_list").append(data.view);

				if (data.length == 0) {

					stopLiveScroll = true;

				} else {

					stopLiveScroll = false;

					// console.log(searchLength);

					// console.log(data.length);

					searchLiveLength = parseInt(searchLiveLength) + data.length;

					// console.log("searchLength" +searchLength);

				}

			}, 

			complete : function() {

				$("#live_video_loader").fadeOut();

			},

			error : function (data) {


			},

    	});

    }

    @endif
</script>
@endsection