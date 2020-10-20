@extends('layouts.admin')

@section('title', tr('user_reports'))

@section('content-header', tr('user_reports'))

@section('breadcrumb')

    <li><a href="{{route('admin.spam-videos')}}"><i class="fa fa-flag"></i>{{tr('spam_videos')}}</a></li>
    
    <li class="active"><i class="fa fa-suitcase"></i> {{tr('user_reports')}}</li>
@endsection

@section('content')

	@include('notification.notify')

	<div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-body">

            	@if(count($spam_videos) > 0)

	              	<table id="example1" class="table table-bordered table-striped">

						<thead>
						    <tr>
						      <th>{{tr('id')}}</th>
						      <th>{{tr('username')}}</th>
						      <th>{{tr('reason')}}</th>
						      <th>{{tr('action')}}</th>
						    </tr>
						</thead>

						<tbody>
							@foreach($spam_videos as $i => $reason)
							    <tr>
							      	<td>{{$i+1}}</td>
							      	<td>{{$reason->userVideos->name}}</td>
							      	<td>{{$reason->reason}}</td>
							      	<td><a class="btn btn-sm btn-danger" href="{{route('admin.spam-videos.unspam-video' , $reason->id)}}"><i class="fa fa-trash"></i></a></td>
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
