@extends('layouts.admin')

@section('title', tr('custom_live_videos'))

@section('content-header', tr('custom_live_videos'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li class="active"><i class="fa fa-video-camera"></i> {{tr('custom_live_videos')}}</li>
@endsection

@section('content')

    @include('notification.notify')
	<div class="row">
        <div class="col-xs-12">
          <div class="box box-primary">

          	<div class="box-header label-primary">
                <b style="font-size:18px;">{{tr('custom_live_videos')}}</b>
                <a href="{{route('admin.custom.live.create')}}" class="btn btn-default pull-right">{{tr('create_custom_live_video')}}</a>
            </div>

            <div class="box-body">

            	@if(count($model) > 0)

	              	<table id="example1" class="table table-bordered table-striped">

						<thead>
						    <tr>
						      <th>{{tr('id')}}</th>
						      <th>{{tr('title')}}</th>
						      <th>{{tr('description')}}</th>
						      <th>{{tr('image')}}</th>
						      <th>{{tr('status')}}</th>
						      <th>{{tr('action')}}</th>
						    </tr>
						</thead>

						<tbody>
							@foreach($model as $i => $video)

							    <tr>
							      	<td>{{$i+1}}</td>
							      	<td>{{substr($video->title , 0,25)}}...</td>
							      	<td>{{substr($video->description , 0,25)}}...</td>
							      	<td><img src="{{$video->image}}" style="width: 75px;height: 50px;"></td>
							      	<td>
							      		
							      		@if($video->status)
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
                                                            <a role="menuitem" tabindex="-1" href="{{route('admin.custom.live.edit' , array('id' => $video->id))}}">{{tr('edit')}}</a>
                                                        @endif
                                                    </li>
                                                    
								                  	<li role="presentation"><a role="menuitem" tabindex="-1" target="_blank" href="{{route('admin.custom.live.view' , array('id' => $video->id))}}">{{tr('view')}}</a></li>

								                  	@if($video->status)
								                		<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.custom.live.change_status', array('id'=>$video->id))}}">{{tr('decline')}}</a></li>
								                	@else
								                		
								                  		<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.custom.live.change_status', array('id'=>$video->id))}}">{{tr('approve')}}</a></li>
								                  		
								                  	@endif
								                  
								                  	<li class="divider" role="presentation"></li>

								                  	<li role="presentation">
								                  		@if(Setting::get('admin_delete_control'))

									                  	 	<a role="button" href="javascript:;" class="btn disabled" style="text-align: left">{{tr('delete')}}</a>

									                  	@else
								                  			<a role="menuitem" tabindex="-1" onclick="return confirm('Are you sure?')" href="{{route('admin.custom.live.delete' , array('id' => $video->id))}}">{{tr('delete')}}</a>
								                  		@endif
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
					<h3 class="no-result">{{tr('no_video_found')}}</h3>
				@endif
            </div>
          </div>
        </div>
    </div>

@endsection
