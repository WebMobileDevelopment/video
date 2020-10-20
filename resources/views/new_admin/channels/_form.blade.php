
    <form action="{{  Setting::get('admin_delete_control') == YES ? '#' : route('admin.channels.save') }}" method="POST" enctype="multipart/form-data" role="form">

        <div class="box-body">

            <input type="hidden" name="channel_id" value="{{ $channel_details->id }}">

            <input type="hidden" name="user_id" value="{{ $channel_details->user_id }}">

            <input type="hidden" name="device_type" value="{{ DEVICE_WEB }}">

            <div class="col-sm-6">

                <div class="form-group"> 

                    <label for="name" class="control-label ">{{ tr('user_name') }} *</label>

                    @if(!$channel_details->id)
                    
                        <select id="user_id" name="user_id" class="form-control select2" required data-placeholder="{{ tr('select_user') }}*">
                            <option value="">{{ tr('select_user_name') }}</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>

                    @else

                        @foreach($users as $user)

                            @if($user->id == $channel_details->user_id)

                                <input type="text" required class="form-control"  value="{{ $user->name }}" disabled="">
                            @endif

                        @endforeach

                    @endif     

                </div>

            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <label for="name" class="control-label">{{ tr('name') }} *</label>
                    <input type="text" required class="form-control" id="name" name="name" placeholder="{{ tr('channel_name') }}" minlength="6" title="Min length must be an 6 character" value="{{old('name') ?: $channel_details->name}}">
                </div>
            </div>

            <div class="col-sm-12">
                <div class="form-group">                        
                    <label for="description" class="control-label">{{ tr('description') }} *</label>
                    <textarea required class="form-control" id="description" name="description" placeholder="{{ tr('description') }}" required>{{ old('description') ?: $channel_details->description }}</textarea> 
                </div>
            </div>
            
            <div class="col-sm-6">
                <div class="form-group">                        
                    <label for="picture" class="control-label">{{ tr('picture') }} *</label>
                    <input type="file" accept="image/png, image/jpeg" id="picture" name="picture" placeholder="{{ tr('picture') }}" @if(!$channel_details->id) required @endif>
                    <p class="help-block">{{ tr('image_validate') }} {{ tr('image_square') }}</p>

                    @if($channel_details->picture)
                        <img style="height: 90px;margin-bottom: 15px; border-radius:2em;" src="{{ $channel_details->picture }}">
                    @endif
                </div>
            </div>
            
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="cover" class="control-label">{{ tr('cover') }} *</label>
                    <input type="file" accept="image/png, image/jpeg" id="cover" name="cover" placeholder="{{ tr('cover') }}" @if(!$channel_details->id) required @endif>
                    <p class="help-block">{{ tr('image_validate') }} {{ tr('rectangle_image') }}</p>
                    
                    @if($channel_details->cover)
                        <img style="height: 90px;margin-bottom: 15px; border-radius:2em;" src="{{ $channel_details->cover }}">
                    @endif
                </div>
            </div>

        </div>

        <div class="box-footer">
            <a href="" class="btn btn-danger">{{ tr('reset') }}</a>
            <button type="submit" class="btn btn-success pull-right" @if(Setting::get('admin_delete_control') == YES) disabled @endif>{{ tr('submit') }}</button>
        </div>

    </form>