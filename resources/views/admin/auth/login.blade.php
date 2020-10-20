@extends('layouts.admin.focused')

@section('title', tr('login'))

@section('content')

    <div class="login-box-body" style="height:275px">

        <div class="login-logo">
             <a href="{{route('admin.login')}}"><b> 
                <img class="adm-log-logo" style="width:50%;height:auto" src="@if(Setting::get('site_logo')) {{Setting::get('site_logo')}} @else {{asset('logo.png')}} @endif" /></b></a>
        </div>


        <form class="form-layout" role="form" method="POST" action="{{ url('/admin/login') }}">
            {{ csrf_field() }}

            <div class="login-logo">
               <!-- <a href="{{route('admin.login')}}"><b>{{Setting::get('site_name')}}</b></a> -->
               <input type="hidden" name="timezone" value="" id="userTimezone">
            </div>

            <p class="text-center mb30"></p>

            <div class="form-inputs">
                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">

                    <input type="email" class="form-control input-lg" pattern="[a-z0-9]+@[a-z0-9]+\.[a-z]{2,6}$"  name="email" placeholder="{{tr('email')}}" value="{{Setting::get('admin_login')}}">

                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif

                </div>

                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">

                    <input type="password" class="form-control input-lg" name="password" placeholder="{{tr('password')}}" value="{{Setting::get('admin_password')}}">

                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif

                </div>


            </div>

            <div class="col-md-6 col-md-offset-3">
                <button class="btn btn-success btn-block mb15" type="submit">
                    <h5><span><i class="fa fa-btn fa-sign-in"></i> {{tr('login')}}</span></h5>
                </button>
            </div>

            <?php /*<div class="form-group">
                    <a style="margin-left:100px" class="btn btn-link" href="{{ url('/admin/password/reset') }}">{{tr('reset_password')}}</a>
            </div> */?>

            

        </form>

    </div>

@endsection

@section('scripts')

<script src="{{asset('assets/js/jstz.min.js')}}"></script>
<script>
    
    $(document).ready(function() {

        var dMin = new Date().getTimezoneOffset();
        var dtz = -(dMin/60);
        // alert(dtz);
        $("#userTimezone").val(jstz.determine().name());
    });

</script>

@endsection

