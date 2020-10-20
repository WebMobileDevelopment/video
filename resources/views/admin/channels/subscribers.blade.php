@extends('layouts.admin')

@section('title', tr('channels'))

@section('content-header')

@if(isset($user)) <span class="text-green"> {{$user->name}} </span>- @endif {{tr('channels')}}

@endsection

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li><a href="{{route('admin.channels')}}"><i class="fa fa-suitcase"></i> {{tr('channels')}}</a></li>
    <li class="active"><i class="fa fa-suitcase"></i> {{tr('subscribers')}}</li>
@endsection

@section('content')

	@include('notification.notify')

	<div class="row">
        <div class="col-xs-12">
          <div class="box box-primary">
          	<div class="box-header label-primary">
                <b style="font-size:18px;">{{tr('subscribers')}}</b>
            </div>
            <div class="box-body">

            	@if(count($subscribers) > 0)

	              	<table id="example1" class="table table-bordered table-striped">

						<thead>
						    <tr>
						      <th>{{tr('id')}}</th>
						      <th>{{tr('channel')}}</th>
						      <th>{{tr('picture')}}</th>
						      <th>{{tr('subscriber_name')}}</th>
						  	  <th>{{tr('action')}}</th>
						    </tr>
						</thead>

						<tbody>
							@foreach($subscribers as $i => $subscriber)

							    <tr>
							      	<td>{{$i+1}}</td>
							      	<td><a target="_blank" href="{{route('admin.channels.view', $subscriber->channel_id)}}">{{$subscriber->getChannel ? $subscriber->getChannel->name : ''}}</a></td>
							      	
							      	<td>
							      		@if($subscriber->getChannel)
	                                	<img style="height: 30px;" src="{{$subscriber->getChannel->picture}}">
	                                	@endif
	                            	</td>	                           

	                            	<td>{{$subscriber->getUser ? $subscriber->getUser->name : ''}}</a></td>

	                            	
							     	<td>	

							     		@if($subscriber->getUser)

							     		<a href="{{route('admin.users.view', $subscriber->getUser ? $subscriber->getUser-> id: '')}}" class="btn btn-success">{{tr('view_user')}}</a>
							     		@else

							     			-

							     		@endif
            							
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
