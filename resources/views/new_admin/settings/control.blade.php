@extends('layouts.admin')

@section('title', tr('settings'))

@section('content-header', 'Admin Control')

@section('breadcrumb')
    <li class="active"><i class="fa fa-money"></i> Admin Control</li>
@endsection

@section('content')

@include('notification.notify')

    <div class="row">

        <div class="col-md-12">

            <div class="box box-danger">

                <div class="box-header with-border">

                    <h3 class="box-title">Admin Control</h3>

                </div>

                    <form action="{{route('admin.settings.save')}}" method="POST" role="form">

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
                                </label><br><br>
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
                            </div><br><br>
                        </div>

                        <div class="clearfix"></div>
                        
                        <div class="form-group">
                            <div class="col-md-6">
                                <label>{{ tr('is_direct_upload_button') }}</label>
                                <br>
                                <label>
                                    <input required type="radio" name="is_direct_upload_button" value="1" class="flat-red" @if(Setting::get('is_direct_upload_button') == 1) checked @endif>
                                    {{tr('yes')}}
                                </label>

                                <label>
                                    <input required type="radio" name="is_direct_upload_button" class="flat-red"  value="0" @if(Setting::get('is_direct_upload_button') == 0) checked @endif>
                                    {{tr('no')}}
                                </label><br><br>
                            </div>

                            <div class="col-md-6">
                                <label>{{ tr('admin_language_control') }}</label>
                                <br>
                                <label>
                                    <input required type="radio" name="admin_language_control" value="1" class="flat-red" @if(Setting::get('admin_language_control') == 1) checked @endif>
                                    {{tr('yes')}}
                                </label>

                                <label>
                                    <input required type="radio" name="admin_language_control" class="flat-red"  value="0" @if(Setting::get('admin_language_control') == 0) checked @endif>
                                    {{tr('no')}}
                                </label><br><br>
                            </div>
                        </div>
                        <div class="clearfix"></div>

                        <div class="form-group">
                            <div class="col-md-6">
                                <label>{{tr('spam_video_enable')}}</label>
                                <br>
                                <label>
                                    <input required type="radio" name="is_spam" value="1" class="flat-red" @if(Setting::get('is_spam')) checked @endif>
                                    {{tr('yes')}}
                                </label>

                                <label>
                                    <input required type="radio" name="is_spam" class="flat-red"  value="0" @if(!Setting::get('is_spam')) checked @endif>
                                    {{tr('no')}}
                                </label><br><br>
                            </div>

                            <div class="col-md-6">
                                <label>{{tr('default_subscription')}}</label>
                                <br>
                                <label>
                                    <input required type="radio" name="is_subscription" value="1" class="flat-red" @if(Setting::get('is_subscription')) checked @endif>
                                    {{tr('yes')}}
                                </label>

                                <label>
                                    <input required type="radio" name="is_subscription" class="flat-red"  value="0" @if(!Setting::get('is_subscription')) checked @endif>
                                    {{tr('no')}}
                                </label><br><br>
                            </div>
                        </div>
                        <div class="clearfix"></div>

                        <div class="form-group">
                            <div class="col-md-6">
                                <label>{{tr('is_banner_video')}}</label>
                                <br>
                                <label>
                                    <input required type="radio" name="is_banner_video" value="1" class="flat-red" @if(Setting::get('is_banner_video')) checked @endif>
                                    {{tr('yes')}}
                                </label>

                                <label>
                                    <input required type="radio" name="is_banner_video" class="flat-red"  value="0" @if(!Setting::get('is_banner_video')) checked @endif>
                                    {{tr('no')}}
                                </label><br><br>
                            </div>

                            <div class="col-md-6">
                                <label>{{tr('is_banner_ad')}}</label>
                                <br>
                                <label>
                                    <input required type="radio" name="is_banner_ad" value="1" class="flat-red" @if(Setting::get('is_banner_ad')) checked @endif>
                                    {{tr('yes')}}
                                </label>

                                <label>
                                    <input required type="radio" name="is_banner_ad" class="flat-red"  value="0" @if(!Setting::get('is_banner_ad')) checked @endif>
                                    {{tr('no')}}
                                </label><br><br>
                            </div>
                        </div>
                        <div class="clearfix"></div>

                        <div class="form-group">
                            
                            <div class="col-md-6">
                                <label>{{tr('create_channel_by_user')}}</label>
                                <br>
                                <label>
                                    <input required type="radio" name="create_channel_by_user" value="1" class="flat-red" @if(Setting::get('create_channel_by_user')) checked @endif>
                                    {{tr('enabled')}}
                                </label>

                                <label>
                                    <input required type="radio" name="create_channel_by_user" class="flat-red"  value="0" @if(!Setting::get('create_channel_by_user')) checked @endif>
                                    {{tr('disabled')}}
                                </label><br><br>
                            </div>

                            <div class="col-md-6">
                                <label>{{ tr('ffmpeg_installed') }}</label>
                                <br>
                                <label>
                                    <input required type="radio" name="ffmpeg_installed" value="1" class="flat-red" @if(Setting::get('ffmpeg_installed') == 1) checked @endif>
                                    {{tr('yes')}}
                                </label>

                                <label>
                                    <input required type="radio" name="ffmpeg_installed" class="flat-red"  value="0" @if(Setting::get('ffmpeg_installed') == 0) checked @endif>
                                    {{tr('no')}}
                                </label><br><br>
                            </div>
                        </div>
   
                        <div class="clearfix"></div>
                        
                        <div class="form-group">
                            
                            <div class="col-md-6">
                                <label>{{tr('redeems')}}</label>
                                <br>
                                <label>
                                    <input required type="radio" name="redeem_control" value="1" class="flat-red" @if(Setting::get('redeem_control')) checked @endif>
                                    {{tr('yes')}}
                                </label>

                                <label>
                                    <input required type="radio" name="redeem_control" class="flat-red"  value="0" @if(!Setting::get('redeem_control')) checked @endif>
                                    {{tr('no')}}
                                </label><br><br>
                            </div>
                        </div>
                        <div class="clearfix"></div>

                        <div class="box-footer pull-right">
                            <button type="submit" class="btn btn-primary">{{tr('submit')}}</button>
                        </div>

                </form>

            </div>

        </div>

    </div>


@endsection