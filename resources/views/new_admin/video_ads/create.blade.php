@extends('layouts.admin')

@section('title', tr('create_ad'))

@section('content-header', tr('create_ad'))

@section('breadcrumb')
    <li><a href="{{route('admin.ads-details.index')}}"><i class="fa fa-bullhorn"></i>{{tr('video_ads')}}</a></li>
    <li class="active"><i class="fa fa-bullhorn"></i> {{tr('create_ad')}}</li>
@endsection

@section('content')

@include('new_admin.video_ads._form')
    
@endsection




