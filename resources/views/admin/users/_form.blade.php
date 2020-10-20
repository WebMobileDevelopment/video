@include('notification.notify')

<div class="row">

    <div class="col-md-10">

        <div class="box box-primary">

            <div class="box-header label-primary">
                <b style="font-size:18px;">@yield('title')</b>
                <a href="{{route('admin.users')}}" class="btn btn-default pull-right">{{tr('view_users')}}</a>
            </div>

            <form class="form-horizontal" action="{{route('admin.users.save')}}" method="POST" enctype="multipart/form-data" role="form">

                <div class="box-body">

                    <input type="hidden" name="id" value="{{$user->id}}">

                    <div class="row">

                        <div class="col-lg-3 text-center">
                            
                            <input type="file" name="picture" id="picture" onchange="loadFile(this, 'picture_preview')" style="width: 200px;display: none" accept="image/jpeg, image/png" />

                            <img id="picture_preview" style="width: 150px;height: 150px;cursor: pointer;" src="{{$user->picture ? $user->picture : asset('placeholder.png')}}" onclick="return $('#picture').click()" />

                        </div>

                        <div class="col-lg-9">
                            
                            <div class="form-group">

                                <div class="col-lg-6">

                                    <input type="text" required name="name" value="{{$user->name ? $user->name : old('name')}}" class="form-control" id="username" placeholder="{{tr('name')}} *" pattern="[a-zA-Z0-9\s]+" title="{{tr('username_notes')}}">

                                </div>

                                 <div class="col-lg-6">

                                    <input type="email" required class="form-control" value="{{$user->email ? $user->email : old('email')}}" id="email" name="email" placeholder="{{tr('email')}} *" maxlength="255">

                                </div>

                            </div>

                            <div class="form-group">

                                <div class="col-lg-6">

                                    <input type="text" name="dob" class="form-control" placeholder="{{tr('enter_dob')}} *" id="dob" required autocomplete="off" value="{{$user->dob ? $user->dob : old('dob')}}" readonly>

                                </div>

                                 <div class="col-lg-6">

                                    <input type="text" name="mobile" class="form-control" id="mobile" placeholder="{{tr('mobile')}}" minlength="6" maxlength="13" pattern="[0-9]{6,}" value="{{$user->mobile ? $user->mobile : old('mobile')}}"">

                                    <small style="color:brown">{{tr('mobile_note')}}</small>

                                </div>

                            </div>

                            @if(!$user->id)

                            <div class="form-group">

                                <div class="col-lg-6">

                                    <input type="password" required name="password" class="form-control" id="password" placeholder="{{tr('password')}} *" minlength="6" title="Enter Minimum 6 Characters">

                                </div>

                                 <div class="col-lg-6">

                                   <input type="password" required name="password_confirmation" class="form-control" id="confirm-password" placeholder="{{tr('confirm_password')}} *" minlength="6" title="Enter Minimum 6 Characters">

                                </div>

                            </div>

                            @endif


                        </div>

                    </div>

                    <div class="clearfix"></div>

                    <br>

                    <div class="form-group">

                        <div class="col-lg-12">

                            <textarea type="text" name="description" class="form-control" id="description" placeholder="{{tr('description')}}" maxlength="255">{{$user->description ? $user->description  :old('description')}}</textarea>

                        </div>

                    </div>


                </div>

                <div class="box-footer">
                    <a href="" class="btn btn-danger">{{tr('reset')}}</a>
                    <button type="submit" class="btn btn-success pull-right">{{tr('submit')}}</button>
                </div>
                <input type="hidden" name="timezone" value="" id="userTimezone">
            </form>
        
        </div>

    </div>

</div>
