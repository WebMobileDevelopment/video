@extends('layouts.admin')

@section('title', tr('create_category'))

@section('content-header', tr('create_category'))

@section('breadcrumb')
    <li><a href="{{route('admin.categories.index')}}"><i class="fa fa-list"></i> {{tr('categories')}}</a></li>
    <li class="active">{{tr('create_category')}}</li>
@endsection

@section('content')

@include('notification.notify')

@include('new_admin.categories._form')

@endsection