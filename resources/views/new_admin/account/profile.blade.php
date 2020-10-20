@extends('layouts.admin')

@section('title', tr('profile'))

@section('content-header', tr('profile'))

@section('breadcrumb')
    <li class="active"><i class="fa fa-diamond"></i> {{tr('account')}}</li>
@endsection

@section('content')

@include('notification.notify')

    <div class="row">

        <div class="col-md-4">

            <div class="box box-primary">

                <div class="box-body box-profile">

                    <img class="profile-user-img img-responsive img-circle" src="@if(Auth::guard('admin')->user()->picture) {{Auth::guard('admin')->user()->picture}} @else {{asset('placeholder.png')}} @endif" alt="User profile picture">

                    <h3 class="profile-username text-center">{{Auth::guard('admin')->user()->name}}</h3>

                    <p class="text-muted text-center">{{tr('admin')}}</p>

                    <ul class="list-group list-group-unbordered">
                        <li class="list-group-item">
                            <b>{{tr('username')}}</b> <a class="pull-right">{{Auth::guard('admin')->user()->name}}</a>
                        </li>
                        <li class="list-group-item">
                            <b>{{tr('email')}}</b> <a class="pull-right">{{Auth::guard('admin')->user()->email}}</a>
                        </li>

                        <li class="list-group-item">
                            <b>{{tr('mobile')}}</b> <a class="pull-right">{{Auth::guard('admin')->user()->mobile}}</a>
                        </li>

                        <li class="list-group-item">
                            <b>{{tr('address')}}</b> 
                        </li>
                        <div  class="col-md-8 text-word-wrap pull-left"><a>{{Auth::guard('admin')->user()->address}}</a></div>
                        
                    </ul>
                
                </div>

            </div>

        </div>

         <div class="col-md-8">
            
            <div class="nav-tabs-custom">

                <ul class="nav nav-tabs">
                    <li class="active"><a href="#adminprofile" data-toggle="tab">{{tr('update_profile')}}</a></li>
                    <li><a href="#image" data-toggle="tab">{{tr('upload_image')}}</a></li>
                    <li><a href="#password_div" data-toggle="tab">{{tr('change_password')}}</a></li>
                </ul>
               
                <div class="tab-content">
                   
                    <div class="active tab-pane" id="adminprofile">

                        <form class="form-horizontal" action="{{(Setting::get('admin_delete_control') == YES) ? '#' : route('admin.profile.save')}}" method="POST" enctype="multipart/form-data" role="form">

                            <input type="hidden" name="id" value="{{Auth::guard('admin')->user()->id}}">

                            <div class="form-group">
                                <label for="name" required class="col-sm-2 control-label">{{tr('username')}}</label>

                                <div class="col-sm-10">
                                  <input type="text" class="form-control" id="name" name="name" value="{{old('name') ?: Auth::guard('admin')->user()->name}}" placeholder="{{tr('username')}}" required title="{{tr('only_for_alpha_values')}}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email" class="col-sm-2 control-label">{{tr('email')}}</label>

                                <div class="col-sm-10">
                                  <input type="email" required value="{{old('email') ?: Auth::guard('admin')->user()->email}}" name="email" class="form-control" id="email" placeholder="{{tr('email')}}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="mobile" class="col-sm-2 control-label">{{tr('mobile')}}</label>

                                <div class="col-sm-10">
                                  <input type="text" value="{{old('mobile') ?: Auth::guard('admin')->user()->mobile}}" name="mobile" class="form-control" id="mobile" placeholder="{{tr('mobile')}}" pattern="[0-9]{4,16}">
                                  <small style="color:brown">{{tr('mobile_note')}}</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="address" class="col-sm-2 control-label">{{tr('address')}}</label>

                                <div class="col-sm-10">
                                  <input type="text" value="{{old('address') ?: Auth::guard('admin')->user()->address}}" name="address" class="form-control" id="address" placeholder="{{tr('address')}}">
                                </div>
                            </div>

                            <div class="form-group">
                               
                                <div class="col-sm-offset-2 col-sm-10">
                                    
                                    <button type="submit" @if(Setting::get('admin_dele te_control') == 1) disabled @endif class="btn btn-danger">{{tr('submit')}}</button>

                                    <a href="{{route('master.login')}}" class="btn btn-success">{{tr('login_as_user')}}</a>
                                    
                                </div>
                            
                            </div>

                        </form>
                    </div>

                    <div class="tab-pane" id="image">

                        <form class="form-horizontal" action="{{(Setting::get('admin_delete_control') == YES) ? '#' : route('admin.profile.save')}}" method="POST" enctype="multipart/form-data" role="form">

                            <input type="hidden" name="id" value="{{Auth::guard('admin')->user()->id}}">

                            @if(Auth::guard('admin')->user()->picture)
                                <img style="height: 90px; margin-bottom: 15px; border-radius:2em;" src="{{Auth::guard('admin')->user()->picture}}" id="image_preview">
                            @else
                                <img style="margin-left: 15px;margin-bottom: 10px" class="profile-user-img img-responsive img-circle"  src="{{asset('placeholder.png')}}" id="image_preview">
                            @endif
                            <div class="form-group">
                                <div class="col-sm-10">
                                    <label for="picture" class="control-label">{{tr('picture')}}</label>

                                    <input type="file" required class="" name="picture" id="picture" accept="image/png, image/jpeg">
                                    <br>
                                    <button type="submit" class="btn btn-danger" @if(Setting::get('admin_delete_control')) disabled @endif>Submit</button>
                                </div>
                            </div>

                        </form>
                    </div>

                    <div class="tab-pane" id="password_div">

                        <form class="form-horizontal" action="{{ (Setting::get('admin_delete_control') == YES) ? '#' : route('admin.change.password')}}" method="POST" enctype="multipart/form-data" role="form">

                            <input type="hidden" name="id" value="{{Auth::guard('admin')->user()->id}}">

                            <div class="form-group">
                                <label for="old_password" class="col-sm-3 control-label">{{tr('old_password')}}</label>

                                <div class="col-sm-8">
                                  <input required type="password" class="form-control" name="old_password" id="old_password" placeholder="{{tr('old_password')}}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password" class="col-sm-3 control-label">{{tr('new_password')}}</label>

                                <div class="col-sm-8">
                                  <input required type="password" class="form-control" name="password" id="password" placeholder="{{tr('new_password')}}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="confirm_password" class="col-sm-3 control-label">{{tr('confirm_password')}}</label>

                                <div class="col-sm-8">
                                  <input required type="password" class="form-control" name="password_confirmation" id="confirm_password" placeholder="{{tr('confirm_password')}}">
                                </div>
                            </div>

                            <div class="form-group">
                                
                                <div class="col-sm-offset-2 col-sm-10">
                                   
                                    <button type="submit" @if(Setting::get('admin_dele te_control') == 1) disabled @endif class="btn btn-danger">{{tr('submit')}}</button>

                                </div>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection

@section('scripts')

    <script type="text/javascript">

        function loadFile(event,id){

            $('#'+id).show();

            var reader = new FileReader();

            reader.onload = function(){

                var output = document.getElementById(id);

                output.src = reader.result;

            };

            reader.readAsDataURL(event.files[0]);
        }

    </script>
@endsection