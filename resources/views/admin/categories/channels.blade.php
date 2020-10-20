@extends('layouts.admin')

@section('title', tr('channels'))

@section('content-header')

"{{$category->name}}" {{tr('category')}} {{tr('channels')}}

@endsection

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li><a href="{{route('admin.categories.list')}}"><i class="fa fa-list"></i> {{tr('category')}} </a></li>
    <li class="active"><i class="fa fa-suitcase"></i> {{tr('channels')}}</li>
@endsection

@section('content')

	@include('notification.notify')

	<div class="row">
    
        <div class="col-xs-12">
    
          	<div class="box box-primary">
          	
	          	<div class="box-header label-primary">
	                <b style="font-size:18px;">"{{$category->name}}" {{tr('category')}} {{tr('channels')}}</b>

	                <a href="{{route('admin.channels.create')}}" class="btn btn-default pull-right">{{tr('add_channel')}}</a>
	            </div>
	            
	            <div class="box-body">

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

		                            	<td> {{ formatted_amount(getAmountBasedChannel($channel->id)) }}</td>

									    <td>
								      		@if($channel->is_approved)
								      			<span class="label label-success">{{tr('approved')}}</span>
								       		@else
								       			<span class="label label-warning">{{tr('pending')}}</span>
								       		@endif
									    </td>

								     	<td>
	            							<ul class="admin-action btn btn-default">
	            								
	            								<li class="dropup">
	            								
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

									                  	@if($channel->is_approved)
									                  		<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.channel.approve' , array('id' => $channel->id , 'status' =>0))}}">{{tr('decline')}}</a></li>
									                  	@else
									                  		<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.channel.approve' , array('id' => $channel->id , 'status' => 1))}}">{{tr('approve')}}</a></li>
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
