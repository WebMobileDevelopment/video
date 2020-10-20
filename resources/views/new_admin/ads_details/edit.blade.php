@extends('layouts.admin')

@section('title', tr('edit_ad'))

@section('content-header', tr('edit_ad'))

@section('breadcrumb')
    <li><a href="{{route('admin.ads-details.index')}}"><i class="fa fa-bullhorn"></i>{{tr('video_ads')}}</a></li>
    <li class="active"><i class="fa fa-bullhorn"></i> {{tr('edit_ad')}}</li>
@endsection

@section('content')

	@include('new_admin.ads_details._form')
    
@endsection

