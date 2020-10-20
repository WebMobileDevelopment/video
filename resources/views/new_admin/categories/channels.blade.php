@extends('layouts.admin')

@section('title', tr('channels'))

@section('content-header')

	<a href="{{  route('admin.categories.view',['category_id' => $category_details->id ] )  }}">{{  $category_details->name  }}</a> - {{ tr('category') }} {{ tr('channels') }}

@endsection

@section('breadcrumb')
     
    <li><a href="{{ route('admin.categories.index') }}"><i class="fa fa-list"></i> {{ tr('category') }} </a></li>
    <li class="active"><i class="fa fa-suitcase"></i> {{ tr('channels') }}</li>
@endsection

@section('content')

	@include('notification.notify')

	<div class="row">

        <div class="col-xs-12">

          	<div class="box box-primary">

	          	<div class="box-header label-primary">
	                <b style="font-size:18px;">"{{ $category_details->name }}" {{ tr('category') }} {{ tr('channels') }}</b>

	                <a href="{{ route('admin.channels.create') }}" class="btn btn-default pull-right">{{ tr('add_channel') }}</a>
	            </div>

	            <div class="box-body">

	            	@if(count($channels) > 0)

		              	<table id="example1" class="table table-bordered table-striped">

							<thead>
							    <tr>
							      <th>{{ tr('id') }}</th>
							      <th>{{ tr('channel') }}</th>
							      <th>{{ tr('user_name') }}</th>
							      <th>{{ tr('no_of_videos') }}</th>
							      <th>{{ tr('subscribers') }}</th>
							      <th>{{ tr('amount') }}</th>
							      <th>{{ tr('status') }}</th>
							      <th>{{ tr('action') }}</th>
							    </tr>
							</thead>

							<tbody>

								@foreach($channels as $i => $channel_details)

								    <tr>

								      	<td>{{ $i+1 }}</td>

								      	<td><a target="_blank" href="{{ route('admin.channels.view', ['channel_id' => $channel_details->id] ) }}">{{ $channel_details->name }}</a></td>

								      	<td><a target="_blank" href="{{ route('admin.users.view', ['user_id' => $channel_details->id] ) }}">{{ $channel_details->getUser ? $channel_details->getUser->name : '' }}</a></td>

								      	<td><a target="_blank" href="{{ route('admin.channels.videos', ['channel_id'=> $channel_details->id] ) }}">{{ $channel_details->get_video_tape_count }}</a></td>

		                            	<td><a target="_blank" href="{{ route('admin.channels.subscribers',['channel_id'=> $channel_details->id] ) }}">{{ $channel_details->get_channel_subscribers_count }}</a></td>

		                            	<td>{{ formatted_amount(getAmountBasedChannel($channel_details->id)) }}</td>

									    <td>
								      		@if($channel_details->is_approved == YES)
								      			<span class="label label-success">{{ tr('approved') }}</span>
								       		@else
								       			<span class="label label-warning">{{ tr('pending') }}</span>
								       		@endif
									    </td>

								     	<td>
	            							<ul class="admin-action btn btn-default">
	            								
	            								<li class="dropup">
	            								
									                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
									                  {{ tr('action') }} <span class="caret"></span>
									                </a>

									                <ul class="dropdown-menu">
                                                        
                                                        @if(Setting::get('admin_delete_control') == YES)
                                                            <li role="presentation"><a role="button" href="javascript:;" class="btn disabled" style="text-align: left">{{ tr('edit') }}</a></li>

                                                            <li role="presentation"><a role="button" href="javascript:;" class="btn disabled" style="text-align: left">{{ tr('delete') }}</a></li>

                                                        @else
                                                            <li role="presentation"><a role="menuitem" tabindex="-1" href="{{ route('admin.channels.edit' , ['channel_id' => $channel_details->id] ) }}">{{ tr('edit') }}</a></li>

                                                            <li role="presentation"><a role="menuitem" tabindex="-1" onclick="return confirm('Are you sure?')" href="{{ route('admin.channels.delete' , ['channel_id' => $channel_details->id] ) }}">{{ tr('delete') }}</a></li>
                                                        @endif
	                                                   	
	                                               
														<li class="divider" role="presentation"></li>

									                  	@if($channel_details->is_approved == YES)
									                  		<li role="presentation"><a role="menuitem" tabindex="-1" href="{{ route('admin.channels.status' , ['channel_id' => $channel_details->id] ) }}" onclick="return confirm(&quot;{{ tr('admin_category_decline_confirmation',$category_details->name ) }}&quot;)">{{ tr('decline') }}</a></li>
									                  	@else
									                  		<li role="presentation"><a role="menuitem" tabindex="-1" href="{{ route('admin.channels.status' , ['channel_id' => $channel_details->id ] ) }}" onclick="return confirm(&quot;{{ tr('category_approve_notes') }}&quot;)">{{ tr('approve') }}</a></li>
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

						<h3 class="no-result">{{ tr('no_result_found') }}</h3>

					@endif

	            </div>

          	</div>

        </div>

    </div>

@endsection
