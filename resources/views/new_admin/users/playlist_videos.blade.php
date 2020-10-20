@extends('layouts.admin')

@section('title', tr('playlist'))

@section('content-header') 

{{ $playlist_details->title }} - {{ tr('playlist') }} 

@endsection

@section('breadcrumb')

    <li><a href="{{ route('admin.users.index') }}"><i class="fa fa-user"></i> {{ tr('users') }}</a></li>

    <li class="active"><i class="fa fa-user"></i> {{ tr('playlist') }}</li>

@endsection

@section('content')

	<div class="row">

        <div class="col-xs-12">
			
			@include('notification.notify')

          	<div class="box box-primary">

	          	<div class="box-header label-primary">
	                <b style="font-size:18px;">{{ tr('playlist') }}</b>
	            </div>

	            <div class="box-body table-responsive">

	            	@if(count($playlists_videos) > 0)

		              	<table id="example1" class="table table-bordered table-striped">

							<thead>
							    <tr>
							      	<th>{{ tr('id') }}</th>

							      	<th>{{ tr('channel') }}</th>

							      	<th>{{ tr('title') }}</th>
							      	
							      	<th>{{ tr('added_on') }}</th>
							      
							      	<th>{{ tr('action') }}</th>
							    </tr>
							</thead>

							<tbody>
								@foreach($playlists_videos as $i => $playlists_video_details)

								    <tr>
								      	<td>{{ $i+1 }}</td>

								      	<td>
								      		@if($playlists_video_details->channel_name)
								      			<a href="{{ route('admin.channels.view' ,  ['channel_id' => $playlists_video_details->channel_id] ) }}">{{ $playlists_video_details->channel_name}}</a>
								      		@else
								      			-
								      		@endif
								      	</td>

								      	<td>
								      		<a href="{{ route('admin.video_tapes.view' ,  ['admin_video_id' => $playlists_video_details->id] ) }}">
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

												<a href="{{ route('admin.users.playlist.video.delete', ['playlist_video_id' => $playlists_video_details->playlist_video_id] ) }}" onclick="return confirm(&quot;{{ tr('admin_user_playlist_delete_confirmation', $playlists_video_details->video_tape_title) }}&quot;)" class="btn btn-danger" title="{{tr('delete')}}" ><b><i class="fa fa-trash"></i></b> 
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
