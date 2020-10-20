@extends('layouts.user')

@section('content')
<div class="y-content">

    <div class="row y-content-row">

        @include('layouts.user.nav')

        <div class="page-inner col-sm-9 col-md-10 profile-edit">

            <div class="form-background p-50">
               
                <div class="common-form login-common">

                    @include('notification.notify')

                    <div class="social-form">
                        <div class="signup-head">
                            <h3>{{tr('change_password')}}</h3>
                        </div><!--end  of signup-head-->        
                    </div><!--end of socila-form-->

                    <div class="sign-up login-page"> 
                        @if(Setting::get('admin_delete_control') == 1)

                        <form class="signup-form login-form" method="post" action="#">

                        @else

                        <form class="signup-form login-form" method="post" action="{{ route('user.profile.password') }}">

                        @endif

                            <div class="form-group">
                                <label for="old_password">{{tr('old_password')}}</label>
                                <input type="password" required name="old_password" class="form-control" id="old_password" placeholder="{{tr('old_password')}}" value="{{ old('old_password') }}">
                            </div>

                            <div class="form-group">
                                <label for="new_password">{{tr('new_password')}}</label>
                                <input type="password" required name="password" class="form-control" id="new_password" placeholder="{{tr('new_password')}}" value="{{ old('password') }}">
                            </div>

                            <div class="form-group">
                                <label for="confirm_password">{{tr('confirm_password')}}</label>
                                <input type="password" required name="password_confirmation" class="form-control" id="confirm_password" placeholder="{{tr('confirm_password')}}">
                            </div>

                            <div class="change-pwd">

                                @if(Setting::get('admin_delete_control') == 1)
                                    <button type="button" disabled class="btn btn-primary" style="cursor: disabled" title="{{tr('admin_disabled')}}">{{tr('submit')}}</button>
                                    <button type="reset" class="btn btn-primary signup-submit">{{tr('reset')}}</button>
                                @else

                                    <button type="submit" class="btn btn-info">{{tr('submit')}}</button>
                                    <button type="reset" class="btn btn-primary signup-submit">{{tr('reset')}}</button>
                                @endif

                            </div>  
                                        
                        </form>
                    </div><!--end of sign-up-->

                </div><!--end of common-form-->     
            </div><!--end of form-background-->

            <div class="sidebar-back"></div> 
        </div>
    </div>
</div>

@endsection
