    
    <form action="{{  Setting::get('admin_delete_control') == YES ? '#' : route('admin.channels.playlists.save') }}" method="POST" enctype="multipart/form-data" role="form">

        <div class="box-body">

            <input type="hidden" name="channel_id" value="{{ $channel_details->id }}">

            <input type="hidden" name="playlist_id" value="{{ $playlist_details->id }}">

            <div class="col-sm-6">
                <div class="form-group">
                    <label for="title" class="control-label">{{ tr('title') }} *</label>
                    <input type="text" required name="title" value="{{$playlist_details->title ? $playlist_details->title : old('title')}}" class="form-control" id="" placeholder="{{tr('title')}} *" title="{{tr('title')}}">
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <label for="video" class="control-label">{{tr('videos')}}</label>
                <div>                      
                    <select id="video_tapes_id" name="video_tapes_id[]" class="form-control select2" data-placeholder="{{tr('select_video_tapes')}}" multiple style="width: 100% !important" required>

                        @foreach($video_tapes as $video_tapes_details)
                              <option value="{{$video_tapes_details->video_tapes_id}}" @if($video_tapes_details->is_selected == 1) selected  @endif> {{$video_tapes_details->video_tape_title}}</option>
                        @endforeach
                        
                    </select>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="form-group">                        
                    <label for="description" class="control-label">{{ tr('description') }} *</label>
                    <textarea required class="form-control" id="description" name="description" placeholder="{{ tr('description') }}" required>{{ old('description') ?: $playlist_details->description }}</textarea> 
                </div>
            </div>

             <div class="col-sm-12">
                <div class="form-group">                        
                    <label for="picture" class="control-label">{{ tr('picture') }} *</label>
                    <input type="file" accept="image/png, image/jpeg" id="picture" name="picture" placeholder="{{ tr('picture') }}" @if(!$playlist_details->id) required @endif>
                    <br>
                    @if($playlist_details->picture)
                        <img style="height: 90px;margin-bottom: 15px; border-radius:2em;" src="{{ $playlist_details->picture }}">
                    @endif
                </div>
            </div>           
        </div>

        <div class="box-footer">
            <a href="" class="btn btn-danger">{{ tr('reset') }}</a>
            <button type="submit" class="btn btn-success pull-right" @if(Setting::get('admin_delete_control') == YES) disabled @endif>{{ tr('submit') }}</button>
        </div>

    </form>