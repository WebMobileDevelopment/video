@extends('layouts.admin')

@section('title', tr('edit_channel'))

@section('content-header', tr('edit_channel'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li><a href="{{route('admin.channels')}}"><i class="fa fa-suitcase"></i> {{tr('channels')}}</a></li>
    <li class="active">{{tr('edit_channel')}}</li>
@endsection

@section('content')

@include('admin.channels._form')

@endsection