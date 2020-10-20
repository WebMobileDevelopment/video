@extends('layouts.admin')

@section('title', tr('users'))

@section('content-header') 

{{ tr('users') }} 

<a href="#" id="help-popover" class="btn btn-danger" style="font-size: 14px;font-weight: 600" title="Any Help ?">{{ tr('help') }}</a>

<div id="help-content" style="display: none">

    <ul class="popover-list">
        <li><span class="text-green"><i class="fa fa-check-circle"></i></span>{{ tr('subscribed_user') }}</li>
        <li><span class="text-red"><i class="fa fa-times"></i></span>{{ tr('unsubscribed_user') }}</li>
        <li><b>{{ tr('redeems') }} - </b>{{ tr('current_wallet_amt') }}</li>
        <li><b>{{ tr('validity_days') }} - </b>{{ tr('expiry_date_subscription') }}</li>
    </ul>
    
</div>

@endsection

@section('breadcrumb')
    <li class="active"><i class="fa fa-user"></i> {{ tr('users') }}</li>
@endsection

@section('content')

	<div class="row">

        <div class="col-xs-12">
			
			@include('notification.notify')

          	<div class="box box-primary">

	          	<div class="box-header label-primary">
	                <b style="font-size:18px;">{{ tr('users') }}</b>

	                <a href="{{ route('admin.users.create') }}" class="btn btn-default pull-right">{{ tr('add_user') }}</a>

	                <!-- EXPORT OPTION START -->

						@if(count($users) > 0 )
		                
			                <ul class="admin-action btn btn-default pull-right" style="margin-right: 20px">
			                 	
								<li class="dropdown">
					                
					                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
					                  {{ tr('export') }} <span class="caret"></span>
					                </a>
					               
					                <ul class="dropdown-menu">
					                  	<li role="presentation">
					                  		<a role="menuitem" tabindex="-1" href="{{ route('admin.users.export' , ['format' => 'xlsx']) }}">
					                  			<span class="text-red"><b>{{ tr('excel_sheet') }}</b></span>
					                  		</a>
					                  	</li>

					                  	<li role="presentation">
					                  		<a role="menuitem" tabindex="-1" href="{{ route('admin.users.export' , ['format' => 'csv']) }}">
					                  			<span class="text-blue"><b>{{ tr('csv') }}</b></span>
					                  		</a>
					                  	</li>
					                </ul>
							
								</li>
							
							</ul>

						@endif

		            <!-- EXPORT OPTION END -->
	            </div>

	            <div class="box-body table-responsive">

	            	@if(count($users) > 0)

		              	<table id="example1" class="table table-bordered table-striped">

							<thead>
							    <tr>
							      <th>{{ tr('id') }}</th>
							      <th>{{ tr('username') }}</th>
							      <th>{{ tr('email') }}</th>
							      <th>{{ tr('channels') }}</th>
							      <th>{{ tr('videos') }}</th>
							      <th>{{ tr('plan_text') }}</th>
							      <th>{{ tr('redeems') }}</th>
							      @if(Setting::get('email_verify_control') == YES)
							      <th>{{ tr('user_is_verified') }}</th>
							      @endif
							      <th>{{ tr('status') }}</th>
							      <th>{{ tr('action') }}</th>
							    </tr>
							</thead>

							<tbody>
								
								@foreach($users as $i => $user_details)

								    <tr>
								      	<td>{{ $i+1 }}</td>

								      	<td>
								      		<a href="{{ route('admin.users.view' ,  ['user_id' => $user_details->id] ) }}"> 

								      			{{ $user_details->name }}

								      			@if($user_details->user_type)

								      			<span class="text-green pull-right"><i class="fa fa-check-circle"></i></span>

								      			@else

								      			<span class="text-red pull-right"><i class="fa fa-times"></i></span>

								      			@endif
								      		</a>
								      	</td>

								      	<td>{{ $user_details->email }}</td>
								      	
								      	<td class="text-center"><a target="_blank" href="{{ route('admin.users.channels' , ['user_id' => $user_details->id] ) }}">{{ $user_details->get_channel_count }}</a></td>

								      	<td class="text-center"><a target="_blank" href="{{ route('admin.video_tapes.index' , $user_details->id) }}">{{ $user_details->get_channel_videos_count }}</a></td>

										<td>
											@if($user_details->user_type)
												{{ get_expiry_days($user_details->id)['days'] }} days
											@endif
										</td>

								      	<td>
								      		<b>{{ formatted_amount($user_details->userRedeem ? $user_details->userRedeem->remaining : 0) }}</b>
								     	</td>

								      	@if(Setting::get('email_verify_control') == USER_EMAIL_VERIFIED)

									      	<td>
										      	@if(!$user_details->is_verified)
										      		<a href="{{ route('admin.users.verify' ,['user_id' => $user_details->id] ) }}" class="btn btn-xs btn-success">{{ tr('verify') }}</a>

										      	@else
										      		<span>{{ tr('verified') }}</span>
										      	@endif									      	
									      	</td>

								      	@endif

								      	<td>								      		
								      		@if($user_details->status == USER_APPROVED)
								      			<span class="label label-success">{{ tr('approved') }}</span>
								       		@else
								       			<span class="label label-warning">{{ tr('pending') }}</span>
								       		@endif
								      	</td>
								 
								      	<td>

	            							<ul class="admin-action btn btn-default">

	            								<li class="dropdown">									               
									                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
									                  {{ tr('action') }} <span class="caret"></span>
									                </a>

									                <ul class="dropdown-menu dropdown-menu-right">

									                <li role="presentation"></li>

									                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="{{ route('admin.users.view' , ['user_id' => $user_details->id] ) }}">{{ tr('view') }}</a></li>
									                  
									                  	@if(Setting::get('admin_delete_control') == YES )

									               			@if(get_expiry_days($user_details->id) > 0)
									                  	 		<li role="presentation"><a role="menuitem" tabindex="-1" onclick="return confirm(&quot;{{tr('admin_user_delete_with_expiry_days_confirmation') }}&quot;);" href="javascript:;" > {{ tr('delete') }} </a></li>	
									                  		@else 
									                  			<li role="presentation">
									                  	 			<a role="menuitem" tabindex="-1" onclick="return confirm(&quot;{{ tr('admin_premium_user_delete_confirmation', $user_details->name ) }}&quot;);" href="javascript:;">{{ tr('delete') }}
									                  			</a></li>
									                  	 	@endif

									               		@else

									               			<li role="presentation"><a role="menuitem" tabindex="-1" href="{{ route('admin.users.edit' , ['user_id' => $user_details->id] ) }}">{{ tr('edit') }}</a></li>		           		
									                  	 	@if(get_expiry_days($user_details->id) > 0)
									                  	 		
									                  	 		<li role="presentation"><a role="menuitem" tabindex="-1" onclick="return confirm(&quot;{{ tr('admin_user_delete_with_expiry_days_confirmation' ) }}&quot;);" href="{{ route('admin.users.delete', ['user_id' => $user_details->id] ) }}"> {{ tr('delete') }} </a></li>	
									                  		@else 
									                  			<li role="presentation">
									                  	 			<a role="menuitem" tabindex="-1" onclick="return confirm(&quot;{{ tr('admin_premium_user_delete_confirmation', $user_details->name ) }}&quot;);" href="{{ route('admin.users.delete', ['user_id' => $user_details->id] ) }}">{{ tr('delete') }}
									                  			</a></li>
									                  	 	@endif

									                  	@endif

									                  	<li class="divider" role="presentation"></li>

									                  	@if($user_details->status == USER_APPROVED )
									                  		
									                  		<li role="presentation">

									                  			<a role="menuitem" tabindex="-1" href="{{ route('admin.users.status', ['user_id' => $user_details->id] ) }}" onclick="return confirm(&quot;{{ tr('admin_user_decline_confirmation', $user_details->name) }}&quot;)">
									                  				<span class="text-danger">{{ tr('decline') }}</span>
									                  			</a>
									                  		</li>
									                  	
									                  	@else
									                  	
									                  		<li role="presentation"><a role="menuitem"  tabindex="-1" href="{{ route('admin.users.status',['user_id' => $user_details->id] ) }}" onclick="return confirm(&quot;{{ tr('admin_user_approve_confirmation', $user_details->name) }}&quot;)" >
									                  			<span class="text-success">{{ tr('approve') }}</span></a></li>
									                  	@endif

									                  	<li class="divider" role="presentation"></li>

									                  	<li role="presentation">
									                  		<a role="menuitem" tabindex="-1" href="{{route('admin.users.subscriptions.plans' ,['user_id' => $user_details->id])}}">
									                  			{{ tr('plans') }}
									                  		</a>
									                  	</li>

									                  	<li role="presentation">
									                  		<a role="menuitem" tabindex="-1" href="{{ route('admin.users.playlist.index' , ['user_id' => $user_details->id] ) }}">
									                  			{{ tr('playlists') }}
									                  		</a>
									                  	</li>	
									                  	
									                </ul>

	              								</li>

	            							</ul>

								      	</td>

								    </tr>

								@endforeach

							</tbody>

						</table>

					@else
						<h3 class="no-result">{{ tr('no_user_found') }}</h3>
					@endif

	            </div>

          	</div>

        </div>

    </div>

@endsection
