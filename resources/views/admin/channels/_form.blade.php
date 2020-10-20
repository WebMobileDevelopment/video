@include('notification.notify')

<div class="row">

    <div class="col-md-10">

        <div class="box box-primary">

            <div class="box-header label-primary">
                <b style="font-size:18px;">{{tr('add_channel')}}</b>
                <a href="{{route('admin.channels')}}" class="btn btn-default pull-right">{{tr('channels')}}</a>
            </div>

            <form class="form-horizontal" action="{{route('admin.channels.save')}}" method="POST" enctype="multipart/form-data" role="form">

                <div class="box-body">

                    <input type="hidden" name="channel_id" value="{{$channel->id}}">

                    <input type="hidden" name="user_id" value="{{$channel->user_id}}" >

                    <input type="hidden" name="device_type" value="{{DEVICE_WEB}}" >

                   
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">{{tr('user_name')}} *</label>
                        <div class="col-sm-10">
                            
                            @if(!$channel->id)
                            <select id="user_id" name="user_id" class="form-control select2" required data-placeholder="{{tr('select_user')}}*">
                                <option value="">{{tr('select_user_name')}}</option>
                                @foreach($users as $user)
                                    <option value="{{$user->id}}">{{$user->name}}</option>
                                @endforeach
                            </select>
                            @else

                                @foreach($users as $user)

                                    @if($user->id == $channel->user_id)

                                    <div class="col-sm-10"><span class="badge badge-succes" >{{$user->name}}</span></div>

                                    @endif

                                @endforeach

                            @endif
                           
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">{{tr('name')}} *</label>
                        <div class="col-sm-10">
                            <input type="text" required class="form-control" id="name" name="name" placeholder="{{tr('channel_name')}}" minlength="6" title="Min length must be an 6 character" value="{{$channel->name}}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description" class="col-sm-2 control-label">{{tr('description')}} *</label>
                        <div class="col-sm-10">
                            <textarea required class="form-control" id="description" name="description" placeholder="{{tr('description')}}" required>{{$channel->description}}</textarea> 
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="picture" class="col-sm-2 control-label">{{tr('picture')}} *</label>
                        <div class="col-sm-10">

                            <input type="file" accept="image/png, image/jpeg" id="picture" name="picture" placeholder="{{tr('picture')}}" @if(!$channel->id) required @endif>
                            <p class="help-block">{{tr('image_validate')}} {{tr('image_square')}}</p>
                            @if($channel->picture)
                                <img style="height: 90px;margin-bottom: 15px; border-radius:2em;" src="{{$channel->picture}}">
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cover" class="col-sm-2 control-label">{{tr('cover')}} *</label>
                        <div class="col-sm-10">
                            <input type="file" accept="image/png, image/jpeg" id="cover" name="cover" placeholder="{{tr('cover')}}" @if(!$channel->id) required @endif>
                            <p class="help-block">{{tr('image_validate')}} {{tr('rectangle_image')}}</p>
                            @if($channel->cover)
                                <img style="height: 90px;margin-bottom: 15px; border-radius:2em;" src="{{$channel->cover}}">
                            @endif
                        </div>
                    </div>

                </div>

                <div class="box-footer">
                    <a href="" class="btn btn-danger">{{tr('reset')}}</a>
                    <button type="submit" class="btn btn-success pull-right">{{tr('submit')}}</button>
                </div>
            </form>
        
        </div>

    </div>

</div>


@section('scripts')
    <script src="https://cdn.ckeditor.com/4.5.5/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace( 'description' );
    </script>
@endsection
