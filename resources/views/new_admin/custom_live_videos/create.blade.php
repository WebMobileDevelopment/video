@extends('layouts.admin')

@section('title', tr('create_custom_live_video'))

@section('content-header', tr('create_custom_live_video'))

@section('breadcrumb')
    <li><a href="{{route('admin.custom.live.index')}}"><i class="fa fa-camera-video"></i> {{tr('custom_live_videos')}}</a></li>
    <li class="active"><i class="fa fa-camera-video"></i> {{tr('create_custom_live_video')}}</li>
@endsection

@section('content')

@include('new_admin.custom_live_videos._form')

@endsection
