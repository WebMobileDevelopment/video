@extends('layouts.admin')

@section('title', tr('channels'))

@section('content-header')

@if(isset($user)) <span class="text-green"> {{$user->name}} </span>- @endif {{tr('channels')}}

@endsection

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li class="active"><i class="fa fa-suitcase"></i> {{tr('channels')}}</li>
@endsection

@section('content')

	@include('notification.notify')

	<div class="row">
        <div class="col-xs-12">
          <div class="box box-primary">
          	<div class="box-header label-primary">
                <b style="font-size:18px;">{{tr('channels')}}</b>
                <a href="{{route('admin.channels.create')}}" class="btn btn-default pull-right">{{tr('add_channel')}}</a>

                <!-- EXPORT OPTION START -->

	          	@if(count($channels) > 0 )
	                  
	            <ul class="admin-action btn btn-default pull-right" style="margin-right: 20px">        
	            	<li class="dropdown">
	                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
	                      {{tr('export')}} <span class="caret"></span>
	                    </a>

	                    <ul class="dropdown-menu">
	                        <li role="presentation">
	                          <a role="menuitem" tabindex="-1" href="{{route('admin.channels.export' , ['format' => 'xls'])}}">
	                            <span class="text-red"><b>{{tr('excel_sheet')}}</b></span>
	                          </a>
	                        </li>

	                        <li role="presentation">
	                          <a role="menuitem" tabindex="-1" href="{{route('admin.channels.export' , ['format' => 'csv'])}}">
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

            	@if(count($channels) > 0)

	              	<table id="example1" class="table table-bordered table-striped">

						<thead>
						    <tr>
						      <th>{{tr('id')}}</th>
						      <th>{{tr('channel')}}</th>
						      <th>{{tr('user_name')}}</th>
						      <th>{{tr('no_of_videos')}}</th>
						      <th>{{tr('subscribers')}}</th>
						      <th>{{tr('amount')}}</th>
						      <th>{{tr('status')}}</th>
						      <th>{{tr('action')}}</th>
						    </tr>
						</thead>

						<tbody>
							@foreach($channels as $i => $channel)
							 
							    <tr>
							      	<td>{{$i+1}}</td>
							      	
							      	<td><a target="_blank" href="{{route('admin.channels.view', $channel->id)}}">{{$channel->name}}</a></td>
							      
							      	<td><a target="_blank" href="{{route('admin.users.view', $channel->user_id)}}">{{$channel->getUser ? $channel->getUser->name : ''}}</a></td>
							      	
							      	<td><a target="_blank" href="{{route('admin.channels.videos', array('id'=> $channel->id))}}">{{$channel->get_video_tape_count}}</a></td>
	                            	
	                            	<td><a target="_blank" href="{{route('admin.channels.subscribers', array('id'=> $channel->id))}}">{{$channel->get_channel_subscribers_count}}</a></td>

	                            	<td>{{ formatted_amount(getAmountBasedChannel($channel->id))}}</td>

								    <td>
								      		@if($channel->is_approved)
								      			<span class="label label-success">{{tr('approved')}}</span>
								       		@else
								       			<span class="label label-warning">{{tr('pending')}}</span>
								       		@endif
								    </td>
							     	<td>
            							<ul class="admin-action btn btn-default">
            								
            								<li class="{{$i <=2 ? 'dropdown' : 'dropup'}}">
            								
								                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
								                  {{tr('action')}} <span class="caret"></span>
								                </a>
								                <ul class="dropdown-menu">
								                  	<li role="presentation">
                                                        @if(Setting::get('admin_delete_control'))
                                                            <a role="button" href="javascript:;" class="btn disabled" style="text-align: left">{{tr('edit')}}</a>
                                                        @else
                                                            <a role="menuitem" tabindex="-1" href="{{route('admin.channels.edit' , array('id' => $channel->id))}}">{{tr('edit')}}</a>
                                                        @endif
                                                    </li>		
                                                    
													<li class="divider" role="presentation"></li>

								                  	<li role="presentation">

									                  	@if(Setting::get('admin_delete_control'))

										                  	<a role="button" href="javascript:;" class="btn disabled" style="text-align: left">{{tr('delete')}}</a>

										                @else

								                  			<a role="menuitem" tabindex="-1" onclick="return confirm('Are you sure?')" href="{{route('admin.channels.delete' , array('channel_id' => $channel->id))}}">{{tr('delete')}}</a>
								                  		@endif
								                  	</li>

													<li class="divider" role="presentation"></li>

													<?php 

								                  		$channel_approve_notes = tr('channel_approve_notes');

								                  		$channel_decline_notes = tr('channel_decline_notes');

								                  	?>

								                  	@if($channel->is_approved)
								                  		<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.channel.approve' , array('id' => $channel->id , 'status' =>0))}}" onclick='return confirm("{{$channel_decline_notes}}")'>{{tr('decline')}}</a></li>
								                  	@else
								                  		<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.channel.approve' , array('id' => $channel->id , 'status' => 1))}}" onclick='return confirm("{{$channel_approve_notes}}")'>{{tr('approve')}}</a></li>
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
					<h3 class="no-result">{{tr('no_result_found')}}</h3>
				@endif
            </div>
          </div>
        </div>
    </div>

@endsection
