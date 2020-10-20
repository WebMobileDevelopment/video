@extends('layouts.user.focused')

@section('content')
    <?php

        if( config('mail.username') &&  config('mail.password')) {

            $disabled = false;

        } else {

            $disabled = true;
        }

    ?>

    
    <div class="login-space">
        <div class="common-form login-common">

            <div class="social-form">
                <div class="signup-head">
                    <h3>{{tr('forgot_password')}}</h3>
                </div><!--end  of signup-head-->        
            </div><!--end of socila-form-->

            @include('notification.notify')

            <div class="sign-up login-page">
                <form class="signup-form login-form" method="post" action="{{ $disabled ? '' : route(
                'user.forgot.password') }}">
                     {!! csrf_field() !!}

                     @if($disabled)

                        <p>{{tr('forgot_password_note')}}</p>    

                     @endif

                    @if($errors->has('email'))
                        <div data-abide-error="" class="alert callout">
                            <p>
                                <i class="fa fa-exclamation-triangle"></i> 
                                <strong> 
                                    @if($errors->has('email')) 
                                        {{ $errors->first('email') }}
                                    @endif
                                </strong>
                            </p>
                        </div>
                    @endif
                    <div class="form-group">
                        <label for="exampleInputEmail1">{{tr('email')}}</label>
                        <input type="email" name="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter Your email" value="{{old('email')}}">
                    </div>

                    <div class="change-pwd">    
                        @if($disabled)
                        <button type="button" class="btn btn-primary signup-submit" disabled>{{tr('submit')}}</button>
                        @else
                        <button type="submit" class="btn btn-primary signup-submit">{{tr('submit')}}</button>
                        @endif
                    </div>          
                    <p>{{tr('already_account')}} <a href="{{route('user.login.form')}}">{{tr('login')}}</a></p>         
                </form>
            </div><!--end of sign-up-->
        </div><!--end of common-form-->     
    </div><!--end of form-background-->

@endsection
