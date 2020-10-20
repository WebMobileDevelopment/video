@extends('layouts.admin')

@section('title', tr('edit_category'))

@section('content-header', tr('edit_category'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li><a href="{{route('admin.categories.list')}}"><i class="fa fa-key"></i> {{tr('categories')}}</a></li>
    <li class="active">{{tr('edit_category')}}</li>
@endsection

@section('content')

@include('notification.notify')

@include('admin.categories._form')

@endsection