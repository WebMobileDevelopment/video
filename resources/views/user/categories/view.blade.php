@extends( 'layouts.user' )

@section( 'styles' )

<link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/custom-style.css')}}"> 

<style>
	#c4-header-bg-container {
		background: #000;
	}
	
	@media screen and (-webkit-min-device-pixel-ratio: 1.5),
	screen and (min-resolution: 1.5dppx) {
		#c4-header-bg-container {
			background: #000;
		}
	}
	
	#c4-header-bg-container .hd-banner-image {
		background: #000;
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
<!-- <div id="gh-banner">

<div id="c4-header-bg-container" class="c4-visible-on-hover-container  has-custom-banner">
<div class="hd-banner">
<div class="hd-banner-image"></div>
</div>

</div>

</div> -->

<div class="channel-content-spacing">
	<div class="branded-page-v2-header channel-header yt-card"> 
		<div id="gh-banner">
			<div id="c4-header-bg-container" class="c4-visible-on-hover-container  has-custom-banner" style="background-image: url('{{asset('images/category1.jpg')}}');background-position: bottom;">
				<!-- <div class="hd-banner">
					<div class="hd-banner-image" ></div>
				</div> -->
			</div>
		</div>
	</div>


<div>
<div class="pull-left">
<a class="channel-header-profile-image spf-link" href="">
<div style="background-image:url({{$category->category_image}});" class="channel-header-profile-image1"></div>
</a>
</div>
<div class="pull-left">
<h1 class="st_channel_heading text-uppercase mt-35">{{$category->category_name}}</h1>
<?php /*<p class="subscriber-count">{{$subscriberscnt}} Subscribers</p> */?>
</div>

<div class="clearfix"></div>

</div>

<div id="channel-subheader" class="clearfix branded-page-gutter-padding appbar-content-trigger">
<ul id="channel-navigation-menu" class="clearfix nav nav-tabs" role="tablist">
<li role="presentation" class="active">
<a href="#about_home" class="yt-uix-button  spf-link  yt-uix-sessionlink yt-uix-button-epic-nav-item yt-uix-button-size-default" aria-controls="home" role="tab" data-toggle="tab">
<span class="yt-uix-button-content hidden-xs">{{tr('about_category')}}</span>
<span class="visible-xs"><i class="fa fa-home channel-tab-icon"></i></span>
</a>
</li>
<li role="presentation" id="videos_sec">
<a href="#videos" class="yt-uix-button  spf-link  yt-uix-sessionlink yt-uix-button-epic-nav-item yt-uix-button-size-default" aria-controls="videos" role="tab" data-toggle="tab">
<span class="yt-uix-button-content hidden-xs">{{tr('videos')}}</span> 
<span class="visible-xs"><i class="fa fa-video-camera channel-tab-icon"></i></span>
</a>
</li>
<li role="presentation">
<a href="#channels_category" class="yt-uix-button  spf-link  yt-uix-sessionlink yt-uix-button-epic-nav-item yt-uix-button-size-default" aria-controls="about" role="tab" data-toggle="tab">
<span class="yt-uix-button-content hidden-xs">{{tr('channels')}}</span> 
<span class="visible-xs"><i class="fa fa-info channel-tab-icon"></i></span>
</a>
</li>
</ul>
</div>
</div>
</div>
</div>

<ul class="tab-content">

	<li role="tabpanel" class="tab-pane active" id="about_home">
		<div class="recom-area abt-sec">
			<div class="abt-sec-head">
			<h5><?= $category->description ?></h5>
			</div>
		</div>
	</li>

	<li role="tabpanel" class="tab-pane" id="videos">

		<div class="recom-area abt-sec">
		<div class="abt-sec-head">

		<div class="new-history1">
		<div class="content-head">
		<?php /*<div><h4 style="color: #000;">{{tr('videos')}}&nbsp;&nbsp;
		@if(Auth::check())

		@if(Auth::user()->id == $channel->user_id)
		<small style="font-size: 12px">({{tr('note')}}:{{tr('ad_note')}} )</small>

		@endif

		@endif
		</h4></div>   */?>            
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
		            <h5 class="payment_class unset-height"><a href="{{$video->url}}">{{$video->title}}</a></h5>
		           	
		            <span class="video_views">
		                <i class="fa fa-eye"></i> {{$video->watch_count}} {{tr('views')}} <b>.</b> 
		                {{ common_date($video->created_at) }}
		            </span>
		        </div> 
				<?php /*@if(Auth::check())
				@if($channel->user_id == Auth::user()->id)

				@if($video->status)
				<div class="cross-mark2">
			        
		            <label style="float:none; margin-top: 6px;" class="switch hidden-xs" title="{{$video->ad_status ? tr('disable_ad') : tr('enable_ad')}}">
		                <input id="change_adstatus_id" type="checkbox" @if($video->ad_status) checked @endif onchange="change_adstatus(this.value, {{$video->video_tape_id}})">
		                <div class="slider round"></div>
		            </label>

		            <div class="btn-group show-on-hover">
			          	<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">

				            <span class="hidden-xs">{{tr('action')}}</span>
				            <span class="caret"></span>

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
				            <li><a title="delete" onclick="return confirm(&quot;{{tr('user_video_delete_confirm') }}&quot;)" href="{{route('user.delete.video' , array('id' => $video->video_tape_id))}}"> {{tr('delete_video')}}</a></li>
				            <li class="visible-xs">
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
												<div class="modal-body">
												   
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

											 <div class="modal-footer">
										      	<div class="pull-left">
										      		@if($video->ppv_amount > 0)
										       			<a class="btn btn-danger" href="{{route('user.remove_pay_per_view', $video->video_tape_id)}}">{{tr('remove_pay_per_view')}}</a>
										       		@endif
										       	</div>
										        <div class="pull-right">
											        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
											        <button type="submit" class="btn btn-primary">Submit</button>
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
		        @endif */?>

		                            <!--end of cross-mark-->                       
		    </div> <!--end of history-head--> 

		    <div class="description">
		    	<?php /*<div class="category"><b class="text-capitalize">{{tr('category_name')}} : </b> <a href="{{route('user.categories.view',$video->category_id)}}" target="_blank">{{$video->category_name}}</a></div> */?>
		        <div><?= $video->description ?></div>
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

	<li role="tabpanel" class="tab-pane" id="channels_category">

		<div class="recom-area abt-sec">
		<div class="abt-sec-head">

		<div class="new-history1">
		<div class="content-head">
		<div><h4 style="color: #000;">{{tr('channels')}}&nbsp;&nbsp;
		
		</h4></div>            
		</div><!--end of content-head-->

		@if(count($channels) > 0)

		<ul class="history-list">

		@foreach($channels as $i => $channel)


		<li class="sub-list row border-0">
		<div class="main-history">
		<div class="history-image">
		    <a href="{{route('user.channel', $channel->channel_id)}}" target="_blank"><img src="{{$channel->picture}}"></a>
		   
		       
		    <div class="video_duration">
		        {{$channel->no_of_videos ? $channel->no_of_videos : 0}} {{tr('videos')}}
		    </div>                        
		</div><!--history-image-->

		<div class="history-title">
		    <div class="history-head row">
		        <div class="cross-title2">
		            <h5 class="payment_class unset-height"><a href="{{route('user.channel', $channel->channel_id)}}" target="_blank">{{$channel->title}}</a></h5>
		           	
		            <span class="video_views">
		                <i class="fa fa-eye"></i> {{$channel->no_of_subscribers}} {{tr('subscribers')}} <b>.</b> 
		                {{ common_date($channel->created_at) }}
		            </span>
		        </div> 

		                            <!--end of cross-mark-->                       
		    </div> <!--end of history-head--> 

		    <div class="description">
		        <?= $channel->description ?>
		    </div><!--end of description--> 

		                                               
		</div><!--end of history-title--> 

		</div><!--end of main-history-->
		</li>    

		@endforeach


		<span id="category_channels_list"></span>

		<div id="channels_loader" style="display: none;">

		<h1 class="text-center"><i class="fa fa-spinner fa-spin" style="color:#ff0000"></i></h1>

		</div>

		<div class="clearfix"></div>


		<button class="pull-right btn btn-info mb-15" onclick="getChannels()" style="color: #fff">{{tr('view_more')}}</button>

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

</ul>

<div class="sidebar-back"></div> 
</div>
</div>

</div>

</div>

@endsection

@section('scripts')

<script>


    var stopScroll = false;

	var searchLength = "{{count($videos)}}";

	var stopPaymentScroll = false;

	var searchChannelsLength = "{{count($channels)}}";


	function getVideos() {
		
		if (searchLength > 0) {

			videos(searchLength);

		}
	}

	function getChannels() {

		if (searchChannelsLength > 0) {

			channels(searchChannelsLength);

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

					if (searchChannelsLength > 0) {

						payment_videos(searchChannelsLength);

					}

				}
			}

		}

	});*/


	function videos(cnt) {


    	category_id = "{{$category->category_id}}";

    	$.ajax({

    		type : "post",

    		url : "{{route('user.categories.videos')}}",

    		beforeSend : function () {

				$("#video_loader").fadeIn();
			},

			data : {skip : cnt, category_id : category_id},

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


	function channels(cnt) {

    	category_id = "{{$category->category_id}}";

    	$.ajax({

    		type : "post",

    		url : "{{route('user.categories.channels')}}",

    		beforeSend : function () {

				$("#channels_loader").fadeIn();
			},

			data : {skip : cnt, category_id : category_id},

			async : false,

			success : function (data) {

				$("#category_channels_list").append(data.view);

				if (data.length == 0) {

					stopPaymentScroll = true;

				} else {

					stopPaymentScroll = false;

					// console.log(searchLength);

					// console.log(data.length);

					searchChannelsLength = parseInt(searchChannelsLength) + data.length;

					// console.log("searchLength" +searchLength);

				}

			}, 

			complete : function() {

				$("#channels_loader").fadeOut();

			},

			error : function (data) {


			},

    	});

    }
</script>
@endsection