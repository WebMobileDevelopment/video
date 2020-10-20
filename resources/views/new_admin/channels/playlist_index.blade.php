@extends('layouts.admin')

@section('title', tr('playlist'))

@section('content-header') 

{{ tr('channel') }} - <a href="{{ route('admin.channels.view',['channel_id' => $channel_details])}}" >{{ $channel_details->name }} </a>

@endsection

@section('breadcrumb')
     
	<li ><i class="fa fa-suitcase"></i> <a href="{{ route('admin.channels.index')}}" > {{ tr('channels') }} </a></li>
    <li class="active"><i class="fa fa-list"></i> {{ tr('playlist') }}</li>

@endsection

@section('content')

	<div class="row">

        <div class="col-xs-12">
			
			@include('notification.notify')

          	<div class="box box-primary">

	          	<div class="box-header label-primary">
	                
	                <b style="font-size:18px;">{{ tr('playlist') }}</b>

	                <a href="{{route('admin.channels.playlists.create', ['channel_id' => $channel_details] )}}" class="btn btn-default pull-right" title="{{tr('add')}}" ><b><i class="fa fa-plus"> {{tr('add_playlist')}}</i></b> 
	          		</a>

	            </div>

	            <div class="box-body table-responsive">

	            	@if(count($playlists) > 0)

		              	<table id="example1" class="table table-bordered table-striped">

							<thead>

							    <tr>

							      	<th>{{ tr('id') }}</th>

							      	<th>{{ tr('playlist') }}</th>

							      	<th>{{ tr('total_videos') }}</th>
							      
							      	<th>{{ tr('added_on') }}</th>

							      	<th>{{ tr('action') }}</th>

							    </tr>

							</thead>

							<tbody>

								@foreach($playlists as $i => $playlist_details)

								    <tr>
								      	<td>{{ $i+1 }}</td>
								      	
								      	<td>
								      	<a href="{{ route('admin.channels.playlists.view' ,  ['playlist_id' => $playlist_details->playlist_id, 'channel_id' => $channel_details] ) }}">{{ $playlist_details->title }}</a></td>

								 		<td><a href="{{ route('admin.channels.playlists.view' ,  ['playlist_id' => $playlist_details->playlist_id, 'channel_id' => $channel_details] ) }}"> {{ $playlist_details->total_videos }}</a>
								 		</td>								      	
								      	

								      	<td> {{ $playlist_details->created_at  }} </td>
								      	
								      	<td>

											@if(Setting::get('admin_delete_control') == YES )

	          									<a href="javascript:;" onclick="return confirm(&quot;{{ tr('admin_channel_playlist_delete_confirmation',$playlist_details->title) }}&quot;)" class="btn btn-danger" title="{{tr('delete')}}" ><b><i class="fa fa-trash"></i></b> 
	          									</a>

	          									<a href="javascript:;" class="btn btn-warning" title="{{tr('edit')}}" ><b><i class="fa fa-edit"></i></b> 
	          									</a>	

	          								@else

												<a href="{{ route('admin.channels.playlists.delete', ['playlist_id' => $playlist_details->playlist_id] ) }}" onclick="return confirm(&quot;{{ tr('admin_channel_playlist_delete_confirmation',$playlist_details->title) }}&quot;)" class="btn btn-danger" title="{{tr('delete')}}" ><b><i class="fa fa-trash"></i></b> 
	          									</a>

	          									<a href="{{ route('admin.channels.playlists.edit', [ 'channel_id' => $channel_details , 'playlist_id' => $playlist_details->playlist_id] ) }}" class="btn btn-warning" title="{{tr('edit')}}" ><b><i class="fa fa-edit"></i></b> 
	          									</a>	

	          								@endif
											
											@if($playlist_details->status == APPROVED)
	      	
	          									<a href="{{ route('admin.channels.playlists.status.change', ['playlist_id' => $playlist_details->playlist_id] ) }}" class="btn btn-danger" title="{{tr('decline')}}" onclick="return confirm(&quot;{{ tr('admin_channel_playlist_decline_notes', $playlist_details->title) }}&quot;)" ><b><i class="fa fa-close"></i></b> 
	          									</a>

	          								@else 
												
												<a href="{{ route('admin.channels.playlists.status.change', ['playlist_id' => $playlist_details->playlist_id] ) }}" class="btn btn-success" title="{{tr('approve')}}" ><b><i class="fa fa-check"></i></b> 
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
