@extends('layouts.user')

@section('content')

<div class="y-content">
   
   <div class="row content-row">

        @include('layouts.user.nav')
         
        <div class="history-content page-inner col-sm-9 col-md-10">
        
            @include('notification.notify')

            <div class="slide-area1 col-sm-4 col-md-4">
                
                <div class="new-history">
                
                    <div class="content-head">

                        @if($video_tapes)
                   
                            <a href="{{route('user.single', $video_tapes[0]->video_tape_id)}}">
                                <img src="{{asset('streamtube/images/placeholder.gif')}}" data-src="{{$video_tapes[0]->video_image}}" class="slide-img1 placeholder" />
                            </a>

                        @else

                            <a href="#">
                                <img src="{{asset('streamtube/images/playlist.jpg')}}" data-src="{{asset('streamtube/images/playlist.jpg')}}" class="slide-img1 placeholder" />
                            </a>

                        @endif

                        <h3 class="text-word-wrap">{{$playlist_details->title}}</h3>

                        @if($video_tapes)
                            <div class="pull-right btn btn-danger mb-15">
                                <a href="{{route('user.playlists.play_all', ['playlist_id' => $playlist_details->playlist_id, 'playlist_type' => $playlist_type])}}" style="color: #fff">Play All</a>
                            </div>
                        @endif
                        
                        <p> {{tr('total_videos')}} : {{$playlist_details->total_videos }} </p>

                        <p>{{tr('last_updated')}} : {{ date('d M y', strtotime($playlist_details->updated_at)) }}</p>

                        <hr>

                        @if(Auth::check())
                            <img class="img-circle " width="70px" src="{{$playlist_details->user_picture}}" style="margin-right: 10px">
                            <h4 style="display: inline;">{{ $playlist_details->user_name }}</h4>
                        @endif

                        @if($playlist_details->is_my_channel && $playlist_type == PLAYLIST_TYPE_CHANNEL) 

                        <a class="share-new global_playlist_id pull-right" id="{{ $playlist_details->channel_id, PLAYLIST_TYPE_CHANNEL }}" title="{{ tr('edit') }}"><i class="fa fa-edit"></i><h4>{{ tr('edit') }}</h4></a>

                        @endif

                        @if($playlist_type == PLAYLIST_TYPE_USER) 

                            <a class="share-new global_playlist_id pull-right" id="{{$playlist_details->playlist_id, PLAYLIST_TYPE_USER}}" title="{{tr('edit')}}"><i class="fa fa-edit"></i></a>
                           
                        @endif

                    </div>

                </div>
             
            </div>

            <div class="slide-area1 col-sm-8 col-md-8">
                
                <div class="new-history">
                      
                    <div class="content-head">
                    
                        <div>
                          
                            <h4 class="bold no-margin-top text-word-wrap">
                               {{tr('playlist_videos')}} - {{$playlist_details->title}}
                            </h4>
                 
                        </div>              
                    
                    </div>

                    @if(count($video_tapes) > 0)

                        <ul class="history-list">

                           @include('user.playlists.playlists')

                           <span id="playlists_videos"></span>

                           <div class="clearfix"></div>

                           <div class="row" style="margin-top: 20px">

                               <div id="playlist_video_content_loader" style="display: none;">

                                   <h1 class="text-center"><i class="fa fa-spinner fa-spin" style="color:#ff0000"></i></h1>

                               </div>

                               <div class="clearfix"></div>

                               <button class="pull-right btn btn-info mb-15" onclick="getPlaylistsList()" style="color: #fff">{{tr('view_more')}}</button>

                               <div class="clearfix"></div>

                           </div>
                          
                       </ul>

                    @else
                       
                       <!-- <img src="{{asset('images/no-result.jpg')}}" class="img-responsive auto-margin"> -->

                    @endif
                      
                </div>

                <div class="sidebar-back"></div> 
            
            </div>
    
            <!-- PLAYLIST POPUPSTART -->
            @if($playlist_type == PLAYLIST_TYPE_USER ) 
            <div class="modal fade global_playlist_id_modal" id="global_playlist_id_{{$playlist_details->playlist_id }}" role="dialog"> @elseif ($playlist_details->is_my_channel && $playlist_type == PLAYLIST_TYPE_CHANNEL) 
            <div class="modal fade global_playlist_id_modal" id="global_playlist_id_{{$playlist_details->channel_id}}" role="dialog"> @else 
            <div class="modal fade global_playlist_id_modal">
            @endif
               
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">

                        <!-- if user logged in let create, update playlist -->

                        @if(Auth::check())

                            <div class="modal-header">

                                <button type="button" class="close" data-dismiss="modal">&times;</button>

                                <h4 class="modal-title">{{tr('edit_playlist')}}</h4>

                            </div>

                            <div class="modal-footer">

                                <div class="more-content">

                                    <div onclick="$('#create_playlist_form').toggle()">

                                        <!-- <label><i class="fa fa-plus"></i> {{tr('edit_playlist')}}</label> -->

                                    </div>

                                    <div class="" id="create_playlist_form">

                                        <div class="form-group">

                                            <input type="text" name="playlist_title" id="playlist_title" class="form-control" placeholder="{{tr('playlist_name_placeholder')}}" required value="{{$playlist_details->title}}">

                                        </div>

                                        @if($playlist_type == PLAYLIST_TYPE_CHANNEL) 

                                        <div class="form-group">

                                            <label for="video" class="control-label">{{tr('videos')}}</label>

                                            <div>

                                                <select id="video_tapes_id" name="video_tapes_id[]" class="form-control select2" data-placeholder="{{tr('select_video_tapes')}}" multiple style="width: 100% !important" required>

                                                    @if(count($videos) > 0) 

                                                        @foreach($videos as $video_tapes_details) 

                                                            @if($video_tapes_details->is_approved == YES)

                                                            <option value="{{ $video_tapes_details->video_tape_id}}" @if($video_tapes_details->exist_in_playlists) selected @endif> {{ $video_tapes_details->title }}</option>

                                                            @endif 

                                                        @endforeach 

                                                    @endif

                                                </select>

                                            </div>

                                            <div class="" style="display: none;">

                                                <label for="playlist_privacy">Privacy</label>
                                               
                                                <select id="playlist_privacy" name="playlist_privacy" class="form-control">
                                                    <option value="PUBLIC">PUBLIC</option>
                                                    <option value="PRIVETE">PRIVETE</option>
                                                    <option value="UNLISTED">UNLISTED</option>
                                                </select>
                                            
                                            </div>
                                        
                                        </div>

                                        @else
                                        
                                            <input type="hidden" id="video_tapes_id" name="video_tapes_id[]">
                                        
                                        @endif  

                                        @if(!$playlist_type == PLAYLIST_TYPE_USER) 

                                            <button class="btn btn-primary" onclick='playlist_save({{$playlist_details->channel_id}} ,{{$playlist_details->playlist_id}})'>{{ tr('save')}}
                                            </button>

                                        @else

                                            <button class="btn btn-primary" onclick='user_playlist_save({{$playlist_details->playlist_id}})'>{{ tr('save')}}
                                            </button>

                                        @endif

                                    </div>

                                </div>

                            </div>

                            <!-- if user not logged in ask for login -->

                        @else
                            
                            <!-- 
                            <div class="menu4 top nav-space">

                                <p>{{tr('signid_for_playlist')}}</p>

                                <a href="{{route('user.login.form')}}" class="btn btn-sm btn-primary">{{tr('login')}}</a>

                            </div> -->

                        @endif

                    </div>
                    <!-- modal content ends -->

                </div>

            </div>

        </div>

   </div>

</div>

@endsection

@section('scripts')

<script>

    var stopPageScroll = false;

    var searchDataLength = "{{count($video_tapes)}}";

    function getPlaylistsList() {

        if (searchDataLength > 0) {

            playlists_videos(searchDataLength);

        }

    }

    function playlists_videos(cnt) {

        $.ajax({

            type: "post",
            async: false,
            url: "{{route('user.playlists.view')}}",
            data: {
                skip: cnt,
                playlist_id: "{{$playlist_details->playlist_id}}",
                is_json: 1
            },

            beforeSend: function() {

                $("#playlist_video_content_loader").fadeIn();
            },

            success: function(response) {
                console.log(response);
                $('#playlists_videos').append(response.view);

                if (response.count == 0) {

                    stopPageScroll = true;

                } else {

                    stopPageScroll = false;

                    searchDataLength = parseInt(searchDataLength) + response.count;

                }

            },

            complete: function() {

                $("#playlist_video_content_loader").fadeOut();

            },

            error: function(data) {

            },

        });

    }

    $(document).on('ready', function() {

        $("#copy-embed1").on("click", function() {
        
            $('#popup1').modal('hide');
        
        });

        $('.global_playlist_id').on('click', function(event) {

            event.preventDefault();

            var id = $(this).attr('id');

            $('#global_playlist_id_'+id).modal('show');

        });

    });

    function user_playlist_save(playlist_id) {

        var title = $("#playlist_title").val();

        var privacy = $("#playlist_privacy").val();

        var playlist_type = '{{$playlist_type}}';

        var playlist_id = playlist_id;
        
        if(title == '') { alert("Title for playlist required"); }

        $.ajax({

            url: "{{route('user.channel.playlists.save')}}",

            data: {
                title: title,
                privacy: privacy,
                playlist_id: playlist_id,
                playlist_type: playlist_type
            },

            type: "post",

            success: function(data) {

                if (data.success) {

                    $('#playlist_title').removeAttr('value');

                    $('#global_playlist_id_' + playlist_id).modal('hide');
                   
                    $('#no_playlist').hide();

                    alert(data.message);

                    location.reload();

                } else {

                    alert(data.error_messages);

                }

            },

            error: function(data) {

            },

        })
    }

    function playlist_save(channel_id, playlist_id) {

        var title = $("#playlist_title").val();

        var privacy = $("#playlist_privacy").val();

        var video_tapes_id = $("#video_tapes_id").val();

        var playlist_type = '{{$playlist_type}}';

        var playlist_id = playlist_id;
        
        if(title == '') { alert("Title for playlist required"); }

        if(playlist_type == 'CHANNEL' && video_tapes_id == null) {

            alert("Please Choose videos to create playlist");

        } else {

            $.ajax({

                url: "{{route('user.channel.playlists.save')}}",

                data: {
                    title: title,
                    channel_id: channel_id,
                    privacy: privacy,
                    video_tapes_id: video_tapes_id,
                    playlist_id: playlist_id,
                    playlist_type: playlist_type
                },

                type: "post",

                success: function(data) {

                    if (data.success) {

                        $('#playlist_title').removeAttr('value');

                        $('#video_tapes_id').val(null).trigger('change');

                        $('#global_playlist_id_' + channel_id).modal('hide');
                       
                        $('#no_playlist').hide();

                        $('#new_playlist').append(data.new_playlist_content);

                        alert(data.message);

                        location.reload();

                    } else {

                        alert(data.error_messages);

                    }

                },

                error: function(data) {

                },

            })

        }

    }

</script>

@endsection

