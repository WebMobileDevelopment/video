@extends('layouts.admin.focused')

@section('title', tr('reset_password'))

@section('content')

    <div class="login-box-body" style="height:300px">

        <form class="form-layout" role="form" method="POST" action="{{ url('/admin/password/email') }}">
            {{ csrf_field() }}

            <div class="login-logo">
               <a href="{{route('admin.login')}}"><b>{{Setting::get('site_name')}}</b></a>
            </div>

            <p class="text-center mb25">{{tr('enter_your_address_reset_pass')}}</p></br>

            <div class="form-inputs">
                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="{{tr('email_add')}}">


                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-3">
                        <button class="pull-right btn btn-warning btn-block mb15" type="submit">
                            <i class="fa fa-btn fa-envelope"></i>{{tr('reset')}}
                        </button>

                         <a href="{{route('admin.dashboard')}}" class="pull-left btn btn-info btn-block mb15">

                            <i class="fa fa-btn fa-user"></i> {{tr('login')}}
                        </a>
                    </div>
                </div>

            </div>
        </form>

    </div>

@endsection
