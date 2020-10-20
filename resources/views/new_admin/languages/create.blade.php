@extends('layouts.admin')

@section('title', tr('create_language'))

@section('content-header', tr('create_language'))

@section('breadcrumb')
    <li><a href="{{route('admin.languages.index')}}"><i class="fa fa-globe"></i>{{tr('languages')}}</a></li>
    <li class="active"><i class="fa fa-globe"></i>&nbsp; {{tr('create_language')}}</li>
@endsection

@section('content')

  	<div class="row">

	    <div class="col-md-10">
   			
   			@include('notification.notify')

	        <div class="box box-primary">

	            <div class="box-header label-primary">
	                <b>{{tr('create_language')}}</b>
	                <a href="{{route('admin.languages.index')}}" style="float:right" class="btn btn-default">{{tr('languages')}}</a>
	            </div>

	            @include('new_admin.languages._form')

	        </div>

	    </div>

	</div>
   
@endsection

