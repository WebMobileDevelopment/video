@extends('layouts.user')

@section('styles')

@endsection

@section('content')

<div class="y-content">
        
    <div class="row content-row">

        @include('layouts.user.nav')

		<div class="page-inner col-sm-9 col-md-10">

			@include('notification.notify')
            
			<div class="row col-md-12">

				<div class="slide-area1 recom-area">
                   
                    <div class="box-head recom-head">
						<h3 class="no-margin text-left">{{ tr('playlists') }} ({{ count($playlists) }})</h3>
                    </div>

                    @if(count($playlists) > 0)

                        <div class="recommend-list row">

                            @foreach($playlists as $playlist_details)

                                <div class="slide-box recom-box">

                                    <div class="slide-image">

                                        <a href="{{route('user.playlists.view', ['playlist_id' => $playlist_details->playlist_id, 'playlist_type' => $playlist_type])}}">
                                            
                                            <img src="{{asset('streamtube/images/placeholder.gif')}}" data-src="{{$playlist_details->picture}}" class="slide-img1 placeholder" />
                                        </a>

                                        <div class="video_amount">

                                            <a href="{{route('user.playlists.delete', ['playlist_id' => $playlist_details->playlist_id])}}" onclick="return confirm(&quot;{{ substr($playlist_details->title, 0 , 15)}}.. {{tr('user_playlist_delete_confirm') }}&quot;)" class="playlist-delete"><i class="fa fa-trash"></i></a>

                                        </div>

                                        <div class="video_duration">
                                            {{ $playlist_details->total_videos }} {{ tr('videos') }}
                                        </div>

                                    </div><!--end of slide-image-->

                                    <div class="video-details recom-details">
                                        <div class="video-head">
                                            <a href="{{route('user.playlists.view', ['playlist_id' => $playlist_details->playlist_id])}}">{{ $playlist_details->title }}</a>
                                        </div>
                                       
                                        <span class="video_views">
                                            <div>

                                            </div>
                                            {{ common_date($playlist_details->created_at) }}
                                        </span> 
                                  
                                    </div><!--end of video-details-->
                                
                                </div>

                            @endforeach

                            <!--end of slide-area-->

			                <div class="sidebar-back"></div> 

					        <span id="playlists_list"></span>

					        <div class="clearfix"></div>

					        <div class="row" style="margin-top: 20px">

					            <div id="playlist_content_loader" style="display: none;">

					                <h1 class="text-center"><i class="fa fa-spinner fa-spin" style="color:#ff0000"></i></h1>

					            </div>

					            <div class="clearfix"></div>

					            <button class="pull-right btn btn-info mb-15" onclick="getPlaylistsList()" style="color: #fff">{{tr('view_more')}}</button>

					            <div class="clearfix"></div>

				            </div>
                            
                        </div>

                    @else

				    	<img src="{{asset('images/no-result.jpg')}}" class="img-responsive auto-margin">

				    @endif

                </div>

	        </div>

		</div>

   	</div>

</div>

@endsection

@section('scripts')

<script>

    var stopPageScroll = false;

    var searchDataLength = "{{count($playlists)}}";

	function getPlaylistsList() {

        if (searchDataLength > 0) {

            playlists_list(searchDataLength);

        }
    }

	function playlists_list(cnt) {

        $.ajax({

            type: "post",
            async: false,
            url: "{{route('user.playlists.index')}}",
            data: {
                skip: cnt,
                is_json: 1
            },

            beforeSend: function() {

                $("#playlist_content_loader").fadeIn();
            },

            success: function(response) {

            	$.each(response.data, function(key,playlistDetails) { 

	                // console.log(JSON.stringify(playlistDetails));

	                var playlist_view_url = "/playlists/view?playlist_id="+playlistDetails.playlist_id;

	                var messageTemplate = '';

	                messageTemplate = '<div class="slide-box recom-box">';

	                messageTemplate += '<div class="slide-image">';

	                messageTemplate += '<a href="'+playlist_view_url+'">';

	                messageTemplate += '<img src="'+playlistDetails.picture+'" data-src="'+playlistDetails.picture+'" class="slide-img1 placeholder" />';

	                messageTemplate += '</a>';

	                messageTemplate += '<div class="video_duration">';
                                    
	                messageTemplate += playlistDetails.total_videos+' videos';
                                            
                    messageTemplate += '</div>';

                    messageTemplate += '</div>';

                    messageTemplate += '<div class="video-details recom-details">';

                    messageTemplate += '<div class="video-head">';

	                messageTemplate += '<a href="'+playlist_view_url+'">'+playlistDetails.title+'</a>';
                     
                    messageTemplate += '</div>';

                    messageTemplate += '<span class="video_views">'+playlistDetails.created_at+'</span>';

                    messageTemplate += '</div>';

                    messageTemplate += '</div>';
	                
	                $('#playlists_list').append(messageTemplate);

	            });

                if (response.data.length == 0) {

                    stopPageScroll = true;

                } else {

                    stopPageScroll = false;

                    searchDataLength = parseInt(searchDataLength) + response.data.length;

                }

            },

            complete: function() {

                $("#playlist_content_loader").fadeOut();

            },

            error: function(data) {

            },

        });

    }

</script>

@endsection