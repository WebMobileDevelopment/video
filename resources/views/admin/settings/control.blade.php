@extends('layouts.admin')

@section('title', tr('settings'))

@section('content-header', tr('settings'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li class="active"><i class="fa fa-money"></i> {{tr('settings')}}</li>
@endsection

@section('content')

@include('notification.notify')

    <div class="row">

        <div class="col-md-6">
            <div class="box box-danger">
                <div class="box-header with-border">

                    <h3 class="box-title">{{tr('settings')}}</h3>

                </div>

                <form action="{{route('admin.save.control')}}" method="POST" role="form">
                
                    <div class="box-body">

                        <div class="form-group">

                            <div class="col-md-6">            
                                <label>{{ tr('admin_delete_control') }}</label>
                                <br>
                                <label>
                                    <input required type="radio" name="admin_delete_control" value="1" class="flat-red" @if(Setting::get('admin_delete_control') == 1) checked @endif>
                                    {{tr('yes')}}
                                </label>

                                <label>
                                    <input required type="radio" name="admin_delete_control" class="flat-red"  value="0" @if(Setting::get('admin_delete_control') == 0) checked @endif>
                                    {{tr('no')}}
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label>{{ tr('is_admin_needs_to_approve_channel_video') }}</label>
                                <br>
                                <label>
                                    <input required type="radio" name="is_admin_needs_to_approve_channel_video" value="1" class="flat-red" @if(Setting::get('is_admin_needs_to_approve_channel_video') == 1) checked @endif>
                                    {{tr('yes')}}
                                </label>

                                <label>
                                    <input required type="radio" name="is_admin_needs_to_approve_channel_video" class="flat-red"  value="0" @if(Setting::get('is_admin_needs_to_approve_channel_video') == 0) checked @endif>
                                    {{tr('no')}}
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            
                        </div>
                        
                        <div class="form-group">
                            <label>{{ tr('is_direct_upload_button') }}</label>
                            <br>
                            <label>
                                <input required type="radio" name="is_direct_upload_button" value="1" class="flat-red" @if(Setting::get('is_direct_upload_button') == 1) checked @endif>
                                {{tr('yes')}}
                            </label>

                            <label>
                                <input required type="radio" name="is_direct_upload_button" class="flat-red"  value="0" @if(Setting::get('is_direct_upload_button') == 0) checked @endif>
                                {{tr('no')}}
                            </label>
                        </div>

                         <div class="form-group">
                            <label>{{ tr('admin_language_control') }}</label>
                            <br>
                            <label>
                                <input required type="radio" name="admin_language_control" value="1" class="flat-red" @if(Setting::get('admin_language_control') == 1) checked @endif>
                                {{tr('yes')}}
                            </label>

                            <label>
                                <input required type="radio" name="admin_language_control" class="flat-red"  value="0" @if(Setting::get('admin_language_control') == 0) checked @endif>
                                {{tr('no')}}
                            </label>
                        </div>

                        <div class="form-group">
                            <label>{{tr('spam_video_enable')}}</label>
                            <br>
                            <label>
                                <input required type="radio" name="is_spam" value="1" class="flat-red" @if(Setting::get('is_spam')) checked @endif>
                                {{tr('yes')}}
                            </label>

                            <label>
                                <input required type="radio" name="is_spam" class="flat-red"  value="0" @if(!Setting::get('is_spam')) checked @endif>
                                {{tr('no')}}
                            </label>
                        </div>

                        <div class="form-group">
                            <label>{{tr('default_subscription')}}</label>
                            <br>
                            <label>
                                <input required type="radio" name="is_subscription" value="1" class="flat-red" @if(Setting::get('is_subscription')) checked @endif>
                                {{tr('yes')}}
                            </label>

                            <label>
                                <input required type="radio" name="is_subscription" class="flat-red"  value="0" @if(!Setting::get('is_subscription')) checked @endif>
                                {{tr('no')}}
                            </label>
                        </div>

                        <div class="form-group">
                            <label>{{tr('email_verify_control')}}</label>
                            <br>
                            <label>
                                <input required type="radio" name="email_verify_control" value="1" class="flat-red" @if(Setting::get('email_verify_control')) checked @endif>
                                {{tr('yes')}}
                            </label>

                            <label>
                                <input required type="radio" name="email_verify_control" class="flat-red"  value="0" @if(!Setting::get('email_verify_control')) checked @endif>
                                {{tr('no')}}
                            </label>
                        </div>

                        <div class="form-group">
                            <label>{{tr('redeems')}}</label>
                            <br>
                            <label>
                                <input required type="radio" name="redeem_control" value="1" class="flat-red" @if(Setting::get('redeem_control')) checked @endif>
                                {{tr('yes')}}
                            </label>

                            <label>
                                <input required type="radio" name="redeem_control" class="flat-red"  value="0" @if(!Setting::get('redeem_control')) checked @endif>
                                {{tr('no')}}
                            </label>
                        </div>

                         <div class="form-group">
                            <label>{{tr('is_banner_video')}}</label>
                            <br>
                            <label>
                                <input required type="radio" name="is_banner_video" value="1" class="flat-red" @if(Setting::get('is_banner_video')) checked @endif>
                                {{tr('yes')}}
                            </label>

                            <label>
                                <input required type="radio" name="is_banner_video" class="flat-red"  value="0" @if(!Setting::get('is_banner_video')) checked @endif>
                                {{tr('no')}}
                            </label>
                        </div>

                        <div class="form-group">
                            <label>{{tr('is_banner_ad')}}</label>
                            <br>
                            <label>
                                <input required type="radio" name="is_banner_ad" value="1" class="flat-red" @if(Setting::get('is_banner_ad')) checked @endif>
                                {{tr('yes')}}
                            </label>

                            <label>
                                <input required type="radio" name="is_banner_ad" class="flat-red"  value="0" @if(!Setting::get('is_banner_ad')) checked @endif>
                                {{tr('no')}}
                            </label>
                        </div>

                        <div class="form-group">
                            <label>{{tr('is_vod')}}</label>
                            <br>
                            <label>
                                <input required type="radio" name="is_vod" value="1" class="flat-red" @if(Setting::get('is_vod')) checked @endif>
                                {{tr('yes')}}
                            </label>

                            <label>
                                <input required type="radio" name="is_vod" class="flat-red"  value="0" @if(!Setting::get('is_vod')) checked @endif>
                                {{tr('no')}}

                            </label>

                        </div>

                        <div class="form-group">

                            <label>{{tr('create_channel_by_user')}}</label>
                            <br>
                            <label>
                                <input required type="radio" name="create_channel_by_user" value="1" class="flat-red" @if(Setting::get('create_channel_by_user')) checked @endif>
                                {{tr('enabled')}}
                            </label>

                            <label>
                                <input required type="radio" name="create_channel_by_user" class="flat-red"  value="0" @if(!Setting::get('create_channel_by_user')) checked @endif>
                                {{tr('disabled')}}
                            </label>
                        </div>

                        <div class="form-group">
                            
                            <label>{{tr('broadcast_by_user')}}</label>
                            <br>
                            <label>
                                <input required type="radio" name="broadcast_by_user" value="1" class="flat-red" @if(Setting::get('broadcast_by_user')) checked @endif>
                                {{tr('enabled')}}
                            </label>

                            <label>
                                <input required type="radio" name="broadcast_by_user" class="flat-red"  value="0" @if(!Setting::get('broadcast_by_user')) checked @endif>
                                {{tr('disabled')}}
                            </label>
                        </div>

                        <div class="form-group">

                            <label>{{ tr('ffmpeg_installed') }}</label>
                            <br>
                            <label>
                                <input required type="radio" name="ffmpeg_installed" value="1" class="flat-red" @if(Setting::get('ffmpeg_installed') == 1) checked @endif>
                                {{tr('yes')}}
                            </label>

                            <label>
                                <input required type="radio" name="ffmpeg_installed" class="flat-red"  value="0" @if(Setting::get('ffmpeg_installed') == 0) checked @endif>
                                {{tr('no')}}
                            </label>
                        </div>
                        
                    
                    </div>

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">{{tr('submit')}}</button>
                    </div>

                </form>

            </div>

        </div>

    </div>


@endsection