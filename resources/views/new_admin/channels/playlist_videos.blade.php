@extends('layouts.admin')

@section('title', tr('playlist'))

@section('content-header') 

	{{ tr('playlist') }}

@endsection

@section('breadcrumb')
    <li ><i class="fa fa-suitcase"></i> <a href="{{ route('admin.channels.index')}}" > {{ tr('channels') }} </a></li>
	
	<li ><i class="fa fa-suitcase"></i> <a href="{{ route('admin.channels.view',['channel_id' => $channel_details])}}" >{{ $channel_details->name }}'s </a></li>
    
    <li><i class="fa fa-list"></i> <a href="{{ route('admin.channels.playlists.index', ['channel_id' =>  $channel_details->id]) }}">{{ tr('playlist') }}</a> </li>
    
    <li class="active"><i class="fa fa-video-camera"></i> {{ tr('playlist_videos') }}</li>
    
@endsection

@section('content')
	
	<div class="row">

        <div class="col-xs-12">
			
			@include('notification.notify')

          	<div class="box box-primary">

	          	<div class="box-header label-primary">
	                <b style="font-size:18px;">{{ tr('playlist') }}</b>
	            </div>
			<div class="col-xs-12 col-sm-12">
	            <div class="col-xs-4 col-sm-4 col-md-4 box-body">
	             	<b style="font-size:18px;">{{ tr('playlist') }} - <a href="{{route('admin.channels.playlists.view', ['playlist_id' => $playlist_details->id, 'channel_id' =>  $channel_details->id]) }}">{{ $playlist_details->title }}</a></b>
	            </div>  

	            <div class="col-xs-4 col-sm-4 col-md-4 box-body">
	             	<b style="font-size:18px;">{{ tr('channel') }} - <a href="#">{{ $channel_details->name }}</a></b>
	            </div>  

	            <div class="box-body col-xs-4 col-sm-4 col-md-4">
	             	<b style="font-size:18px;">{{ tr('total_videos') }} - <a href="#">{{ $playlist_details->total_videos }}</a></b>
	            </div>
	        </div>
	        <br>
	        <br>
	            <hr>

	            <div class="box-body table-responsive">

	            	@if(count($playlists_videos) > 0)

		              	<table id="example1" class="table table-bordered table-striped">

							<thead>
							    <tr>
							      	<th>{{ tr('id') }}</th>

							      	<th>{{ tr('videos') }}</th>
							      	
							      	<th>{{ tr('added_on') }}</th>
							      
							      	<th>{{ tr('action') }}</th>
							    </tr>
							</thead>

							<tbody>
								@foreach($playlists_videos as $i => $playlists_video_details)

								    <tr>
								      	<td>{{ $i+1 }}</td>
							      	
								      	<td>
								      		<a href="{{ route('admin.video_tapes.view' ,  ['video_tape_id' => $playlists_video_details->video_tape_id] ) }}">
								      		{{ $playlists_video_details->video_tape_title }}
								      		</a>
									    </td>
									    <td>
									    	{{$playlists_video_details->created_at}}
									    </td>

									    <td>

										    @if(Setting::get('admin_delete_control') == YES )

	          									<a href="javascript:;" onclick="return confirm(&quot;{{ tr('admin_user_playlist_delete_confirmation',$playlist_details->title) }}&quot;)" class="btn btn-danger" title="{{tr('delete')}}" ><b><i class="fa fa-trash"></i></b> 
	          									</a>	

	          								@else

												<a href="{{ route('admin.channels.playlist.video.delete', ['playlist_video_id' => $playlists_video_details->playlist_video_id] ) }}" onclick="return confirm(&quot;{{ tr('admin_channel_playlist_video_delete_confirmation', $playlists_video_details->video_tape_title) }}&quot;)" class="btn btn-danger" title="{{tr('delete')}}" ><b><i class="fa fa-trash"></i></b> 
	          									</a>

	          								@endif

								      	</td>

								    </tr>
								@endforeach

							</tbody>

						</table>

					@else
						<h3 class="no-result">{{ tr('no_playlist_found') }}</h3>
					@endif

	            </div>

          	</div>

        </div>

    </div>

@endsection
