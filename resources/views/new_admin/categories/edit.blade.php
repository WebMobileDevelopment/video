@extends('layouts.admin')

@section('title', tr('edit_category'))

@section('content-header', tr('edit_category'))

@section('breadcrumb')
    <li><a href="{{route('admin.categories.index')}}"><i class="fa fa-key"></i> {{tr('categories')}}</a></li>
    <li class="active">{{tr('edit_category')}}</li>
@endsection

@section('content')

@include('notification.notify')

@include('new_admin.categories._form')

@endsection