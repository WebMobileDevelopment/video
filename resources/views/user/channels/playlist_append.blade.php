    
    <div class="slide-box recom-box">

        <div class="slide-image">

            <a href="{{route('user.playlists.view', ['playlist_id' => $channel_playlist_details->playlist_id])}}">  

                <img src="{{ $channel_playlist_details->picture ?: asset('streamtube/images/placeholder.gif')}}" data-src="{{ $channel_playlist_details->picture }}" class="slide-img1 placeholder" />
            </a> 

            @if(Auth::check())

            <div class="video_amount">

                <a href="{{route('user.playlists.delete', ['playlist_id' => $channel_playlist_details->playlist_id])}}" onclick="return confirm(&quot;{{ substr($channel_playlist_details->title, 0 , 15)}} - {{tr('user_playlist_delete_confirm') }}&quot;)" class="playlist-delete"><i class="fa fa-trash"></i></a>

            </div>

            @endif

            <div class="video_duration">
                {{$channel_playlist_details->total_videos}} {{tr('videos')}}
            </div>

        </div>

        <div class="video-details recom-details">

            <div class="video-head">
                <a href="{{route('user.playlists.view', ['playlist_id' => $channel_playlist_details->playlist_id])}}">{{$channel_playlist_details->title}}</a>
            </div>

            <span class="video_views">
                <div>

                </div>
                {{ common_date($channel_playlist_details->created_at) }}
            </span>

        </div>
        <!--end of video-details-->

    </div>