@extends('layouts.admin')

@section('title', tr('view_history'))

@section('content-header')

{{tr('view_history')}} - 

<a href="{{route('admin.users.view' ,['user_id' => $user_details->id] )}}">{{$user_details->name}}</a>

@endsection

@section('breadcrumb')
    <li><a href="{{route('admin.users.index')}}"><i class="fa fa-user"></i> {{tr('users')}}</a></li>
    <li class="active"> {{tr('view_history')}}</li>
@endsection

@section('content')

	<div class="row">
        
        <div class="col-xs-12">

        @include('notification.notify')

          	<div class="box">

	            <div class="box-body">

	            	@if(count($user_histories) > 0)

		              	<table id="example1" class="table table-bordered table-striped">

							<thead>
							    <tr>
							      <th>{{tr('id')}}</th>
							      <th>{{tr('username')}}</th>
							      <th>{{tr('video')}}</th>
							      <th>{{tr('date')}}</th>
							      <th>{{tr('action')}}</th>
							    </tr>
							</thead>

							<tbody>

								@foreach($user_histories as $i => $user_history_details)

								    <tr>
								      	<td>{{$i+1}}</td>

								      	<td>
								      		<a href="{{route('admin.users.view', ['user_id' => $user_history_details->user_id])}}" >{{$user_history_details->username}}</a>
								      	</td>
								      	
								      	<td>
								      		<a href="{{route('admin.video_tapes.view', ['video_tape_id' => $user_history_details->video_tape_id]) }}" >{{$user_history_details->title}}</a>
								      	</td>
								      	
								      	<td>{{$user_history_details->date}}</td>
									    
									    <td>
	            						
								            <a href="{{route('admin.users.history.delete' , ['history_id' => $user_history_details->user_history_id] )}}" onclick="return confirm(&quot;{{ tr('admin_user_history_delete_confirm', $user_history_details->title) }}&quot;)" class="btn btn-danger" title="{{tr('delete')}}" ><b><i class="fa fa-trash"></i></b> 
          									</a>
									    </td>
									    
								    </tr>					

								@endforeach
							</tbody>

						</table>

					@else
						<h3 class="no-result">{{tr('no_history_found')}}</h3>
					@endif

	            </div>


          	</div>

        </div>
    
    </div>

@endsection


