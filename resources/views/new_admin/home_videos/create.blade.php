@extends('layouts.admin')

@section('title', tr('add_banner_videos'))

@section('content-header', tr('add_banner_videos'))

@section('styles')

    <link rel="stylesheet" href="{{asset('assets/css/wizard.css')}}">

    <link rel="stylesheet" href="{{asset('admin-css/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css')}}">

    <link rel="stylesheet" href="{{asset('admin-css/plugins/iCheck/all.css')}}">
@endsection

@section('breadcrumb')
    <li><a href="{{route('admin.banner.videos.index')}}"><i class="fa fa-university"></i>{{tr('banner_videos')}}</a></li>
    <li class="active"><i class="fa fa-university"></i> {{tr('add_banner_videos')}}</li>
@endsection 

@section('content')

@include('notification.notify')


@if (envfile('QUEUE_DRIVER') != 'redis') 

 <div class="alert alert-warning">
    <button type="button" class="close" data-dismiss="alert">×</button>
        {{tr('warning_error_queue')}}
</div>

@endif
<div class="row">

    <div class="col-md-12">

        @include('notification.notify')

        <div class="box box-primary">

            <div class="box-header label-primary">
                <b style="font-size:18px;">@yield('title')</b>
                <!-- <a href="{{route('admin.ads-details.index')}}" class="btn btn-default pull-right">{{tr('video_ads')}}</a> -->
            </div>

            <form  action="{{ Setting::get('admin_delete_control') == YES ? '#' : route('admin.banner.videos.save')}}" method="POST" enctype="multipart/form-data" role="form">

                <div class="box-body">

                    <div class="col-md-12 form-group" style="margin-top: 10px;">

                    	<div class="row">

                            <div class="col-md-9">

                                <div class="row">

                                    <div class="col-md-6">

                                        <label>{{tr('name')}} *</label>

                                        <input type="text" name="name" id="name" class="form-control" value="{{$banner_video->title ? $banner_video->title:''}}" required>

                                    </div>

                                    <div class="col-md-3">

                                        <label>Video *</label>

                                        <input type="file" name="file" id="file" accept="">

                                        <br>
                                        <!-- {{$banner_video->video ? $banner_video->video:''}} -->
                                    </div>

                                    <div class="clearfix"></div>

                                </div>

                                <br>

                            </div>

                    	</div>
                        
                    </div>
                
                </div>

                <div class="box-footer">
                    <a href="" class="btn btn-danger">{{tr('cancel')}}</a>
                    <button type="submit" class="btn btn-success pull-right">{{tr('submit')}}</button>
                </div>

            </form>
        
        </div>

    </div>

</div>

@endsection

@section('scripts')
    <script src="{{asset('admin-css/plugins/bootstrap-datetimepicker/js/moment.min.js')}}"></script> 

    <script src="{{asset('admin-css/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js')}}"></script> 

    <script src="{{asset('admin-css/plugins/iCheck/icheck.min.js')}}"></script>

    <script src="{{asset('streamtube/js/jquery-form.js')}}"></script>

 
@endsection