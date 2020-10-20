@extends('layouts.admin')

@section('title', tr('users'))

@section('content-header') 

{{tr('users')}} 

<a href="#" id="help-popover" class="btn btn-danger" style="font-size: 14px;font-weight: 600" title="{{tr('any_help')}}">{{tr('help')}}</a>

<div id="help-content" style="display: none">

    <ul class="popover-list">
        <li><span class="text-green"><i class="fa fa-check-circle"></i></span> - {{tr('paid_subscribed_user')}}</li>
        <li><span class="text-red"><i class="fa fa-times"></i></span> - {{tr('unpaid_subscribed_user')}}</li>
        <li><b>{{tr('redeems')}} - </b> {{tr('current_wallet_amount_user')}} </li>
        <li><b>{{tr('validity_days')}} - </b> {{tr('expiry_days_subscription_user')}}</li>

    </ul>
    
</div>

@endsection

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li class="active"><i class="fa fa-user"></i> {{tr('users')}}</li>
@endsection

@section('content')

	@include('notification.notify')

	<div class="row">
        <div class="col-xs-12">
          <div class="box box-primary">
          	<div class="box-header label-primary">
                <b style="font-size:18px;">{{tr('users')}}</b>

                <a href="{{route('admin.users.create')}}" class="btn btn-default pull-right">{{tr('add_user')}}</a>

                <!-- EXPORT OPTION START -->

					@if(count($users) > 0 )
	                
		                <ul class="admin-action btn btn-default pull-right" style="margin-right: 20px">
		                 	
							<li class="dropdown">
				                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
				                  {{tr('export')}} <span class="caret"></span>
				                </a>
				                <ul class="dropdown-menu">
				                  	<li role="presentation">
				                  		<a role="menuitem" tabindex="-1" href="{{route('admin.users.export' , ['format' => 'xls'])}}">
				                  			<span class="text-red"><b>{{tr('excel_sheet')}}</b></span>
				                  		</a>
				                  	</li>

				                  	<li role="presentation">
				                  		<a role="menuitem" tabindex="-1" href="{{route('admin.users.export' , ['format' => 'csv'])}}">
				                  			<span class="text-blue"><b>{{tr('csv')}}</b></span>
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
						      <th>{{tr('id')}}</th>
						      <th>{{tr('username')}}</th>
						      <th>{{tr('email')}}</th>
						      <th>{{tr('no_of_channels')}}</th>
						      <th>{{tr('no_of_videos')}}</th>
						      <th>{{tr('validity_days')}}</th>
						      <th>{{tr('redeems')}}</th>
						      @if(Setting::get('email_verify_control'))
						      <th>{{tr('email_verification')}}</th>
						      @endif
						      <th>{{tr('status')}}</th>
						      <th>{{tr('action')}}</th>
						    </tr>
						</thead>

						<tbody>
							@foreach($users as $i => $user)

							    <tr>
							      	<td>{{$i+1}}</td>
							      	<td>
							      		<a href="{{route('admin.users.view' , $user->id)}}"> 

							      			{{$user->name}}

							      			@if($user->user_type)

							      			<span class="text-green pull-right"><i class="fa fa-check-circle"></i></span>

							      			@else

							      			<span class="text-red pull-right"><i class="fa fa-times"></i></span>

							      			@endif

							      		</a>
							      	</td>
							      	<td>{{$user->email}}</td>
							      	
							      	<td class="text-center"><a target="_blank" href="{{route('admin.users.channels' , $user->id)}}">{{$user->get_channel_count}}</a></td>

							      	<td class="text-center"><a target="_blank" href="{{route('admin.video_tapes.list' , $user->id)}}">{{$user->get_channel_videos_count}}</a></td>

									<td>
										@if($user->user_type)
											{{get_expiry_days($user->id)['days']}} days
										@endif
									</td>

							      	<td>
							      		<b>{{Setting::get('currency')}} {{$user->userRedeem ? $user->userRedeem->remaining : 0}}</b>
							     	</td>

							      @if(Setting::get('email_verify_control'))

							      <td>

							      	@if(!$user->is_verified)

							      		<a href="{{route('admin.users.verify' , $user->id)}}" class="btn btn-xs btn-success">{{tr('verify')}}</a>

							      	@else

							      		<span>{{tr('verified')}}</span>

							      	@endif
							      	
							      </td>

							      @endif

							      <td>
							      		
							      		@if($user->status)
							      			<span class="label label-success">{{tr('approved')}}</span>
							       		@else
							       			<span class="label label-warning">{{tr('pending')}}</span>
							       		@endif

							      </td>
							 
							      <td>
            							<ul class="admin-action btn btn-default">

            								<li class="@if($i < 2) dropdown @else dropup @endif">
								               
								                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
								                  {{tr('action')}} <span class="caret"></span>
								                </a>

								                <ul class="dropdown-menu dropdown-menu-right">
								                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.users.edit' , array('id' => $user->id))}}">{{tr('edit')}}</a></li>

								                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.users.view' , $user->id)}}">{{tr('view')}}</a></li>
								                  	
								                  	<li role="presentation" class="divider"></li>

								                  	
								                  	<li role="presentation">
								                  	 	@if(Setting::get('admin_delete_control'))
								                  	 		<a role="button" href="javascript:;" class="btn disabled" style="text-align: left">{{tr('delete')}}</a>
								                  	 	@elseif(get_expiry_days($user->id) > 0)

								                  	 		<a role="menuitem" tabindex="-1" onclick="return confirm('Are you sure want to delete the premium user?');" href="{{route('admin.users.delete', array('id' => $user->id))}}">{{tr('delete')}}
								                  			</a>
								                  		@else 
								                  			<a role="menuitem" tabindex="-1" onclick="return confirm('Are you sure?');" href="{{route('admin.delete.user', array('id' => $user->id))}}">{{tr('delete')}}
								                  			</a>
								                  	 	@endif

								                  	</li>

								                  	<?php 

								                  		$approve_notes = tr('approve_notes');

								                  		$decline_notes = tr('decline_notes');

								                  	?>
								                  	@if($user->status==0)
								                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.users.status',array('id'=>$user->id,'status'=>1))}}" onclick='return confirm("{{$approve_notes}}")'>{{tr('approve')}}</a></li>
								                  	@else
								                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.users.status',array('id'=>$user->id,'status'=>0))}}"  onclick='return confirm("{{$decline_notes}}")'>{{tr('decline')}}</a></li>
								                  	@endif
								                  	
								                </ul>
              								</li>
            							</ul>
							      </td>
							    </tr>
							@endforeach
						</tbody>
					</table>
				@else
					<h3 class="no-result">{{tr('no_user_found')}}</h3>
				@endif
            </div>
          </div>
        </div>
    </div>

@endsection
