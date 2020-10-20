@extends('layouts.admin')

@section('title', tr('banner_videos'))

@section('content-header', tr('banner_videos'))

@section('breadcrumb')
    
    <li class="active"><i class="fa fa-university"></i> {{tr('banner_videos')}}</li>
@endsection

@section('content')

    @include('notification.notify')

	<div class="row">
        
        <div class="col-xs-12">

        	<p class="text-justify" style="color:brown;"><b>{{tr('note')}} : </b>{{tr('banner_video_content')}}</p>
          	
          	<div class="box box-primary">

	          	<div class="box-header label-primary">
	                <b style="font-size:18px;">{{tr('banner_videos')}}</b>
	                <a href="{{route('admin.banner.videos.create')}}" class="btn btn-default pull-right">{{tr('add_banner_videos')}}</a>
	            </div>

	            <div class="box-body">

	            	@if(count($video_tapes) > 0)

		              	<table id="example1" class="table table-bordered table-striped">

							<thead>
							    <tr>
							      <th>{{tr('id')}}</th>
							      <th>{{tr('channel')}}</th>
							      <th>{{tr('title')}}</th>
							      <th>{{tr('description')}}</th>
							      @if(Setting::get('theme') == 'default')
							      	<th>{{tr('slider_video')}}</th>
							      @endif
							      <th>{{tr('status')}}</th>
							      <th>{{tr('change')}}</th>
							      <th>{{tr('action')}}</th>
							    </tr>
							</thead>

							<tbody>
								@foreach($video_tapes as $i => $video_tape_details)

								    <tr>
								      	<td>{{$i+1}}</td>
								      	
								      	<td>{{$video_tape_details->channel_name}}</td>
								      	
								      	<td>{{substr($video_tape_details->title , 0,25)}}...</td>
								      	
								      	<td>{{substr($video_tape_details->description , 0, 25)}}...</td>

								      	@if(Setting::get('theme') == 'default')
								      	
									      	<td>
									      		@if($video_tape_details->is_home_slider == 0 && $video_tape_details->is_approved && $video_tape_details->status)
									      			<a href="{{route('admin.slider.video' , $video_tape_details->video_tape_id)}}"><span class="label label-danger">{{tr('set_slider')}}</span></a>
									      		@elseif($video_tape_details->is_home_slider)
									      			<span class="label label-success">{{tr('slider')}}</span>
									      		@else
									      			-
									      		@endif
									      	</td>

								      	@endif
								      	<td>
								      		@if($video_tape_details->is_approved == YES)
								      			<span class="label label-success">{{tr('approved')}}</span>
								       		@else
								       			<span class="label label-warning">{{tr('pending')}}</span>
								       		@endif
								      	</td>

								      	<td>
								      		@if(Setting::get('admin_delete_control') == YES) 
								      			<button class="btn btn-primary btn-xs" disabled>{{tr('remove_banner')}}</button>
								      		@else
								      			<a class="btn btn-primary btn-xs" href="{{route('admin.change.video' ,$video_tape_details->video_tape_id )}}" >{{tr('remove_banner')}}</a>
								      		@endif
								      	</td>

									    <td>
	            							<ul class="admin-action btn btn-default">
	            								
	            								<li class="dropup">
									                
									                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
									                  {{tr('action')}} <span class="caret"></span>
									                </a>
									                
									                <ul class="dropdown-menu">

								                  		<li role="presentation"><a role="menuitem" tabindex="-1" target="_blank" href="{{route('admin.videos.view' , ['video_tape_id' => $video_tape_details->video_tape_id] )}}">{{tr('view')}}</a></li>
								                  	
	                                                    @if(Setting::get('admin_delete_control') ==YES )
	                                                        <li role="presentation">
	                                                        	<a role="button" href="javascript:;" class="btn disabled" style="text-align: left">{{tr('edit')}}</a>
	                                                        </li>

	                                                        <li role="presentation">
	                                                        	<a role="button" href="javascript:;" class="btn disabled" style="text-align: left">{{tr('delete')}}</a>
	                                                        </li>
	                                                    @else
	                                                        <li role="presentation">
	                                                        	<a role="menuitem" tabindex="-1" href="{{route('admin.videos.edit' , array('id' => $video_tape_details->video_tape_id))}}">{{tr('edit')}}</a>
	                                                        </li>

	                                                        <li role="presentation">
	                                                        	<a role="menuitem" tabindex="-1" onclick="return confirm('Are you sure?')" href="{{route('admin.videos.delete' , array('id' => $video_tape_details->video_tape_id))}}">{{tr('delete')}}</a>
	                                                        </li>
	                                                    @endif
									                  	
									                  	<li class="divider" role="presentation"></li>

									                  	@if($video_tape_details->is_approved == YES )
									                		<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.videos.status', ['video_tape_id' => $video_tape_details->video_tape_id ] )}}">{{tr('decline')}}</a></li>
									                	@else
									                  		<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.videos.status', ['video_tape_id' => $video_tape_details->video_tape_id ] ) }}">{{tr('approve')}}</a></li>
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
						<h3 class="no-result">{{tr('no_video_found')}}</h3>
					@endif
	            </div>

          	</div>

        </div>

    </div>

@endsection
