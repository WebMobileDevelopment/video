@extends('layouts.admin')

@section('title', tr('edit_video_ad'))

@section('content-header', tr('edit_video_ad'))

@section('breadcrumb')
    <li><a href="{{route('admin.ads-details.index')}}"><i class="fa fa-bullhorn"></i>{{tr('assigned_ads')}}</a></li>
    <li class="active"><i class="fa fa-bullhorn"></i> {{tr('edit_video_ad')}}</li>
@endsection

@section('content')

@include('new_admin.video_ads._form')
    
@endsection




