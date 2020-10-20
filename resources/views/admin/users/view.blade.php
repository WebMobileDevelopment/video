@extends('layouts.admin')

@section('title', tr('view_user'))

@section('content-header') 

{{tr('view_user')}} 

<!-- <a href="#" id="help-popover" class="btn btn-danger" style="font-size: 14px;font-weight: 600" title="Any Help ?">HELP ?</a>

<div id="help-content" style="display: none">

    <ul class="popover-list">

        <li><b>PayPal - </b> Minimum Accepted Amount - $ 0.01</li>

        <li><b>Stripe - </b> Minimum Accepted Amount - $ 0.50 - <a target="_blank" href="https://stripe.com/docs/currencies">Check References</a></li>

    </ul>
    
</div>
 -->
@endsection

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li><a href="{{route('admin.users')}}"><i class="fa fa-user"></i> {{tr('users')}}</a></li>
    <li class="active"><i class="fa fa-user"></i> {{tr('view_user')}}</li>
@endsection

@section('styles')

<style type="text/css">
	.timeline::before {
	    content: '';
	    position: absolute;
	    top: 0;
	    bottom: 0;
	    width: 0;
	    background: #fff;
	    left: 0px;
	    margin: 0;
	    border-radius: 0px;
	}
	.check-redeem {
		color: #FFF !important;
	}

	.nav > li > a:hover, .nav > li > a:active, .nav > li > a:focus {
		color: black !important;
		background-color: green !important;
	}


	.text-ellipsis {
	  white-space: nowrap;
	  overflow: hidden;
	  text-overflow: ellipsis;
	}

	hr {

		margin-top: 10px;
		margin-bottom: 10px;
	}

	.h4-header {
		background-color:#f7f7f7; font-size: 18px; text-align: center; padding: 7px 10px; margin-top: 0;
	}
</style>

@endsection

@section('content')

	<div class="row">

	    <div class="col-md-3">

	      <!-- Profile Image -->
	      <div class="box box-widget widget-user">

	      	 	<div class="widget-user-header bg-black">
	            	<h3 class="widget-user-username text-capitalize text-ellipsis">{{$user_details->name}}</h3>
	              	<h5 class="widget-user-desc">User 

	              		@if($user_details->user_type)

			      				<span class="text-white"><i class="fa fa-check-circle"></i></span>

			      			@else

			      				<span class="text-white" ><i class="fa fa-times-circle"></i></span>

			      			@endif

	              	</h5>
	            </div>
	            <div class="widget-user-image">
	              <img class="img-circle" src="{{$user_details->picture}}" alt="{{$user_details->name}}" style="height: 90px">
	            </div>
		        <div class="box-body box-profile">

		          <h3 class="profile-username text-center"></h3>

		          <p class="text-muted text-center"></p>

		          <br>

		          <ul class="list-group list-group-unbordered">
		            <li class="list-group-item">
		              <b>{{tr('channels')}}</b> <a target="_blank" class="pull-right" href="{{route('admin.users.channels' , $user_details->id)}}">{{$user_details->get_channel_count}}</a>
		            </li>
		            <li class="list-group-item">
		              <b>{{tr('videos')}}</b> <a class="pull-right" target="_blank" href="{{route('admin.video_tapes.list' , $user_details->id)}}">{{$user_details->get_channel_videos_count}}</a>
		            </li>
		            <li class="list-group-item">
		              <b>{{tr('wishlists')}}</b> <a href="{{route('admin.users.wishlist', $user_details->id)}}" class="pull-right" target="_blank">{{$user_details->user_wishlist_count}}</a>
		            </li>
		            <li class="list-group-item">
		              <b>{{tr('histories')}}</b> <a href="{{route('admin.users.history', $user_details->id)}}" class="pull-right" target="_blank">{{$user_details->user_history_count}}</a>
		            </li>
		            
		             <li class="list-group-item">

		              <b>{{tr('reviews')}}</b> <a href="{{route('admin.users.history', $user_details->id)}}" class="pull-right" target="_blank">{{$user_details->user_rating_count}}</a>
		            </li>
		             <li class="list-group-item">
		              <b>{{tr('spam_reports')}}</b> <a href="{{route('admin.users.history', $user_details->id)}}" class="pull-right" target="_blank">{{$user_details->user_flag_count}}</a>
		            </li>
		            <!-- <li class="list-group-item">
		              <b>{{tr('earnings')}}</b> <a class="pull-right">13,287</a>
		            </li> -->

		            <li class="list-group-item">
		              <b>{{tr('status')}}</b> <a class="pull-right">
		              	@if($user_details->status) 
			      			<span class="label label-success">{{tr('approved')}}</span>
			       		@else 
			       			<span class="label label-warning">{{tr('pending')}}</span>
			       		@endif
		              </a>
		            </li>

		             <li class="list-group-item">
		              <b>{{tr('is_verified')}}</b> <a class="pull-right">
		              	@if(!$user_details->is_verified) 

			      			<a href="{{route('admin.users.verify' , $user_details->id)}}" class="btn btn-xs btn-warning">{{tr('verify')}}</a>

			       		@else 
			       			<span class="label label-success">{{tr('verified')}}</span>
			       		@endif
		              </a>
		            </li>

		          </ul>

		          <a href="{{route('admin.users.edit', array('id' => $user_details->id))}}" class="btn btn-primary btn-block"><b>{{tr('edit_user')}}</b></a>

		        </div>
		        <!-- /.box-body -->
	      </div>
	      <!-- /.box -->

	      <!-- About Me Box -->
	      <div class="box box-primary">
	        <div class="box-header with-border">
	          <h3 class="box-title">{{tr('about_me')}}</h3>
	        </div>
	        <!-- /.box-header -->
	        <div class="box-body">
	         <!--  <strong><i class="fa fa-book margin-r-5"></i> Education</strong> -->

	          <p class="text-muted">

	          		{{$user_details->description}}
	          </p>

	          <hr>

	          <strong><i class="fa fa-map-marker margin-r-5"></i> {{tr('location')}}</strong>

	          <p class="text-muted">{{$user_details->timezone}}</p>

	          <hr>

	         

	          <p><strong><i class="fa fa-bell margin-r-5"></i> {{tr('validity_days')}}</strong></p>


                <p style="color:#cc181e">The Pack will Expiry within <b>{{get_expiry_days($user_details->id)['days']}} days</b></p>
           

            	<p><a target="_blank" href="{{route('admin.users.subscriptions.plans' , $user_details->id)}}" class="btn btn-xs btn-success"><i class="fa fa-hand-pointer-o"></i>&nbsp;{{tr('subscribe')}}</a></p>

	        </div>
	        <!-- /.box-body -->
	      </div>
	      <!-- /.box -->
	    </div>
	    <!-- /.col -->
	    <div class="col-md-9">
	      <div class="nav-tabs-custom">
	        <ul class="nav nav-tabs">
	          <li class="active"><a href="#activity" data-toggle="tab" aria-expanded="true">{{tr('profile')}}</a></li>
	          <li class=""><a href="#channels_id" data-toggle="tab" aria-expanded="false">{{tr('channels')}}</a></li>
	          <li class=""><a href="#wishlist_list" data-toggle="tab" aria-expanded="false">{{tr('favourites')}}</a></li>
	          <li class=""><a href="#history_list" data-toggle="tab" aria-expanded="false">{{tr('history')}}</a></li>
	          <li class=""><a href="#reviews_list" data-toggle="tab" aria-expanded="false">{{tr('reviews')}}</a></li>
	          <li class=""><a href="#spam_reports_list" data-toggle="tab" aria-expanded="false">{{tr('spam_reports')}}</a></li>
	        </ul>
	        <div class="tab-content">
	          <div class="tab-pane active" id="activity">

	          	<h4 class="h4-header">
                    {{tr('personal_info')}}
                </h4>
	            <table class="table table-striped">
	            	<tr>
	            		<th>{{tr('username')}}</th>
	            		<td>{{$user_details->name}}</td>
	            	</tr>
	            	<tr>
	            		<th>{{tr('email')}}</th>
	            		<td>{{$user_details->email}}</td>
	            	</tr>
	            	<tr>
	            		<th>{{tr('dob')}}</th>
	            		<td>{{date('d-m-Y', strtotime($user_details->dob))}}</td>
	            	</tr>
	            	<tr>
	            		<th>{{tr('mobile')}}</th>
	            		<td>{{$user_details->mobile}}</td>
	            	</tr>
	            	<tr>
	            		<th>{{tr('device_type')}}</th>
	            		<td>{{$user_details->device_type}}</td>
	            	</tr>
	            	<tr>
	            		<th>{{tr('register_type')}}</th>
	            		<td>{{$user_details->register_type}}</td>
	            	</tr>
	            	<tr>
	            		<th>{{tr('login_type')}}</th>
	            		<td>{{$user_details->login_type}}</td>
	            	</tr>
	            </table>

	            <h4 class="h4-header">
                    {{tr('redeems')}}
                </h4>
                <table class="table table-striped">
	            	<tr>
	            		<th>{{tr('total_earning')}}</th>
	            		<td>{{Setting::get('currency')}} {{$user_details->userRedeem ? number_format_short($user_details->userRedeem->total) : "0.00"}}</td>
	            	</tr>
	            	<tr>
	            		<th>{{tr('wallet_balance')}}</th>
	            		<td>{{Setting::get('currency')}} {{$user_details->userRedeem ? number_format_short($user_details->userRedeem->remaining) : "0.00"}}</td>
	            	</tr>
	            	<tr>
	            		<th>{{tr('paid_amount')}}</th>
	            		<td>{{Setting::get('currency')}} {{$user_details->userRedeem ? number_format_short($user_details->userRedeem->paid) : "0.00"}}</td>
	            	</tr>
	            	<tr>
	            		<td>
	            			<a target="_blank" href="{{route('admin.users.redeems' , $user_details->id)}}" class="btn btn-success check-redeem" style="background-color: #00a65a !important; color: #fff !important" >	

		                	{{tr('check_redeem_requests')}}

		                	</a>
	            		</td>
	            	</tr>
	            </table>

	          </div>
	          <!-- /.tab-pane -->
	          <div class="tab-pane" id="channels_id">

	          	<blockquote>
	                <p>{{tr('channels_short_notes')}}</p>
	                <small>{{tr('to_view_more')}} <cite><a href="{{route('admin.users.channels', $user_details->id)}}" target="_blank">{{tr('click_here')}}</a></cite></small>
	            </blockquote>

	          		<div class="row">

	          		@if(count($channels) > 0)

	          		@foreach($channels as $channel)

		            <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6">
			          <!-- Widget: user widget style 1 -->
			          <div class="box box-widget widget-user">
			            <!-- Add the bg color to the header using any of the bg-* classes -->
			            <div class="widget-user-header bg-black" style="background: url('{{$channel->cover}}') center center;">
			              <h3 class="widget-user-username">{{$channel->channel_name}}</h3>
			              <h5 class="widget-user-desc">{{tr('channel')}}</h5>
			            </div>
			            <div class="widget-user-image">
			              <img class="img-circle" src="{{$channel->picture}}" alt="{{$channel->channel_name}}" style="height: 90px">
			            </div>
			            <div class="box-footer">
			              <div class="row">
			                <div class="col-sm-4 border-right">
			                  <div class="description-block">
			                    <h5 class="description-header"><a href="{{route('admin.channels.videos', $channel->channel_id)}}">{{$channel->videos}}</a></h5>
			                    <span class="description-text">{{tr('videos')}}</span>
			                  </div>
			                  <!-- /.description-block -->
			                </div>
			                <!-- /.col -->
			                <div class="col-sm-4 border-right">
			                  <div class="description-block">
			                    <h5 class="description-header"><a href="{{route('admin.channels.subscribers', array('id'=> $channel->channel_id))}}"> {{$channel->subscribers}}</a></h5>
			                    <span class="description-text">{{tr('subscribers')}}</span>
			                  </div>
			                  <!-- /.description-block -->
			                </div>
			                <!-- /.col -->
			                <div class="col-sm-4">
			                  <div class="description-block">
			                    <h5 class="description-header">{{$channel->currency}}{{number_format_short($channel->earnings)}}</h5>
			                    <span class="description-text">{{tr('earnings')}}</span>
			                  </div>
			                  <!-- /.description-block -->
			                </div>
			                <!-- /.col -->
			              </div>
			              <!-- /.row -->
			            </div>
			          </div>
			          <!-- /.widget-user -->
			        </div>
			        @endforeach

			        @else

			        	<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">

			        		<p class="text-center">{{tr('no_channels_found')}}</p>

			        	</div>

			        @endif
			     </div>
	          </div> 
	          <!-- /.tab-pane -->

	          <div class="tab-pane" id="wishlist_list">

	          		<blockquote>
		                <p>{{tr('favourites_notes')}}</p>
		                <small>{{tr('to_view_more')}} <cite><a href="{{route('admin.users.wishlist', $user_details->id)}}" target="_blank">{{tr('click_here')}}</a></cite></small>
		            </blockquote>

	           		<table id="datatable-withoutpagination" class="table table-bordered table-striped">
						<thead>
						    <tr>
						      <th>{{tr('id')}}</th>
						      <th>{{tr('video')}}</th>
						      <th>{{tr('date')}}</th>
						      <th>{{tr('action')}}</th>
						    </tr>
						</thead>

						<tbody>

							@foreach($wishlists as $i => $wishlist)
								
							    <tr>
							      	<td>{{$i+1}}</td>
							      	<td>{{$wishlist->title}}</td>
							      	<td>{{$wishlist->created_at->diffForHumans()}}</td>
								    <td>
            							
								        <a class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');" href="{{route('admin.users.wishlist.delete' , $wishlist->id)}}"><i class="fa fa-trash"></i></a>
								              
								    </td>
							    </tr>					

							@endforeach

						</tbody>
					</table>
	          </div>

	          <div class="tab-pane" id="history_list">

	          		<blockquote>
		                <p>{{tr('history_notes')}}</p>
		                <small>{{tr('to_view_more')}} <cite><a target="_blank" href="{{route('admin.reviews', $user_details->id)}}">{{tr('click_here')}}</a></cite></small>
		            </blockquote>

	           		<table id="datatable-withoutpagination1" class="table table-bordered table-striped">
						<thead>
						    <tr>
						      <th>{{tr('id')}}</th>
						      <th>{{tr('video')}}</th>
						      <th>{{tr('date')}}</th>
						      <th>{{tr('action')}}</th>
						    </tr>
						</thead>

						<tbody>


							@foreach($histories as $i => $history)

							    <tr>
							      	<td>{{$i+1}}</td>
							      	<td>{{$history->title}}</td>
							      	<td>{{$history->created_at->diffForHumans()}}</td>
								    <td>
            							
								        <a class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');" href="{{route('admin.users.history.delete' , $history->id)}}"><i class="fa fa-trash"></i></a>
								              
								    </td>
							    </tr>					

							@endforeach

							
						</tbody>
					</table>
	          </div>

	          <div class="tab-pane" id="spam_reports_list">

	          		<blockquote>
		                <p>{{tr('spam_reports_notes')}}</p>
		                <small>{{tr('to_view_more')}} <cite><a href="{{route('admin.spam-videos.per-user-reports', $user_details->id)}}" target="_blank">{{tr('click_here')}}</a></cite></small>
		            </blockquote>

	           		<table id="datatable-withoutpagination" class="table table-bordered table-striped">
						<thead>
						    <tr>
						      <th>{{tr('id')}}</th>
						      <th>{{tr('video')}}</th>
						      <th>{{tr('reason')}}</th>
						      <th>{{tr('date')}}</th>
						      <th>{{tr('action')}}</th>
						    </tr>
						</thead>

						<tbody>

							@foreach($spam_reports as $i => $spam_report)

							    <tr>
							      	<td>{{$i+1}}</td>
							      	<td>{{$spam_report->title}}</td>
							      	<td>{{$spam_report->reason}}</td>
							      	<td>{{$spam_report->created_at->diffForHumans()}}</td>
								    <td>
            							
								        <a class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');" href="{{route('admin.spam-videos.unspam-video' , $spam_report->id)}}"><i class="fa fa-trash"></i></a>
								              
								    </td>
							    </tr>					

							@endforeach

						</tbody>
					</table>
	          </div>

	          <div class="tab-pane" id="reviews_list">

	          		<blockquote>
		                <p>{{tr('reviews_notes_list')}}</p>
		                <small>{{tr('to_view_more')}} <cite><a href="{{route('admin.reviews', array('user_id'=>$user_details->id))}}" target="_blank">{{tr('click_here')}}</a></cite></small>
		            </blockquote>

	           		<table id="datatable-withoutpagination" class="table table-bordered table-striped">
						<thead>
						    <tr>
						      <th>{{tr('id')}}</th>
						      <th>{{tr('video')}}</th>
						      <th>{{tr('comments')}}</th>
						      <th>{{tr('date')}}</th>
						      <th>{{tr('action')}}</th>
						    </tr>
						</thead>

						<tbody>

							@foreach($user_ratings as $i => $user_rating)

							    <tr>
							      	<td>{{$i+1}}</td>
							      	<td>{{$user_rating->title}}</td>
							      	<td>{{$user_rating->comment}}</td>
							      	<td>{{$user_rating->created_at->diffForHumans()}}</td>
								    <td>
            							
								        <a class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');" href="{{route('admin.reviews.delete' , array('id'=>$user_rating->id))}}"><i class="fa fa-trash"></i></a>
								              
								    </td>
							    </tr>					

							@endforeach

						</tbody>
					</table>
	          </div>

	          <!-- /.tab-pane -->
	        </div>
	        <!-- /.tab-content -->
	      </div>
	      <!-- /.nav-tabs-custom -->
	    </div>
	        <!-- /.col -->

    </div>


@endsection


@section('scripts')

<script type="text/javascript">
$("#datatable-withoutpagination").DataTable({
     "paging": false,
     "lengthChange": false,
     "searching": false,
     "language": {
           "info": ""
    }
});
$("#datatable-withoutpagination1").DataTable({
     "paging": false,
     "lengthChange": false,
     "searching": false,
     "language": {
           "info": ""
    }
});
</script>
@endsection