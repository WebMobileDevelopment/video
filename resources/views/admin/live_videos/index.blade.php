@extends('layouts.admin')

@section('title', tr('videos'))

@section('content-header')

@if(isset($subscription)) {{$subscription->title}} - @endif {{ tr('videos') }} 

@endsection

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li class="active"><i class="fa fa-video-camera"></i> {{tr('videos')}}</li>
@endsection

@section('content')

	@include('notification.notify')

	<div class="row">
        <div class="col-xs-12">
          <div class="box box-primary">

          	<div class="box-header label-primary">
                <b>{{tr('videos')}}</b>

                <!-- EXPORT OPTION START -->

				@if(count($data) > 0 )
                
	                <ul class="admin-action btn btn-default pull-right" style="margin-right: 20px">
	                 	
						<li class="dropdown">
			                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
			                  {{tr('export')}} <span class="caret"></span>
			                </a>
			                <ul class="dropdown-menu">
			                  	<li role="presentation">
			                  		<a role="menuitem" tabindex="-1" href="{{route('admin.live-videos.export' , ['format' => 'xls'])}}">
			                  			<span class="text-red"><b>{{tr('excel_sheet')}}</b></span>
			                  		</a>
			                  	</li>

			                  	<li role="presentation">
			                  		<a role="menuitem" tabindex="-1" href="{{route('admin.live-videos.export' , ['format' => 'csv'])}}">
			                  			<span class="text-blue"><b>{{tr('csv')}}</b></span>
			                  		</a>
			                  	</li>
			                </ul>
						</li>
					</ul>

				@endif

                <!-- EXPORT OPTION END -->
                
            </div>
            
            <div class="box-body">

              	<table id="example1" class="table table-bordered table-striped">

					<thead>					    
						<tr>
					      	<th>{{tr('id')}}</th>
					      	<th>{{tr('name')}}</th>
					      	<th>{{tr('title')}}</th>
					      	<th>{{tr('video_type')}}</th>
					      	<th>{{tr('payment')}}</th>
					      	<th>{{tr('streaming_status')}}</th>
					      	<th>{{tr('streamed_at')}}</th>
					      	<th>{{tr('action')}}</th>
					    </tr>
					</thead>

					<tbody>

						@foreach($data as $i => $video)

						    <tr>
						      	<td>{{$i+1}}</td>

						      	<td>

						      		<a href="{{$video->user ? route('admin.users.view' , ['user_id' => $video->user_id]) : '#'}}"> {{$video->user ? $video->user->name : "-"}}</a>

						      	</td>

						      	<td>{{$video->title}}</td>

						      	<td>
						      			
						      		@if($video->type == TYPE_PUBLIC)

						      			<label class="text-green"><b>{{TYPE_PUBLIC}}</b></label>

						      		@else
						      			<label class="text-navyblue"><b>{{TYPE_PRIVATE}}</b></label>
						      		@endif

						      	</td>

						      	<td>
						      			
						      		@if($video->payment_status)

						      			<label class="text-red">{{tr('payment')}}</label>

						      		@else
						      			<label class="text-yellow">{{tr('free')}}</label>
						      		@endif

						      	</td>
						      	
						      	<td>
						      		@if($video->is_streaming && !$video->status)

						      			<label class="text-green">{{tr('yes')}}</label>

						      		@else
						      			<label class="text-red">{{tr('no')}}</label>
						      		@endif
						      	</td>

						      	<td>{{convertTimeToUSERzone($video->created_at, Auth::guard('admin')->user()->timezone, 'd-m-Y h:i A')}}</td>

						      	<td><a href="{{route('admin.live-videos.view' , $video->id)}}" class="btn btn-success"><b>{{tr('view')}}</b></a></td>
						      

						    </tr>
						@endforeach
						
					</tbody>
				
				</table>
			
            </div>
          </div>
        </div>
    </div>

@endsection