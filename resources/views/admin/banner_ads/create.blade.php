@extends('layouts.admin')

@section('title', tr('create_banner_ad'))

@section('content-header')

{{tr('create_banner_ad')}}

<br>

@endsection

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li><a href="{{route('admin.banner-ads.list')}}"><i class="fa fa-bullhorn"></i>{{tr('banner_ads')}}</a></li>
    <li class="active"><i class="fa fa-bullhorn"></i> {{tr('create_banner_ad')}}</li>
@endsection

@section('content')

@include('admin.banner_ads._form')
    
@endsection




