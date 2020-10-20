@extends('layouts.admin')

@section('title', tr('pages'))

@section('content-header', tr('pages'))

@section('breadcrumb')
    <li><a href="{{route('admin.pages.index')}}"><i class="fa fa-book"></i> {{tr('pages')}}</a></li>
    <li class="active"> {{tr('add_page')}}</li>
@endsection

@section('content')

  	<div class="row">

	    <div class="col-md-12">
    		
    		@include('notification.notify')

	        <div class="box box-primary">

	            <div class="box-header label-primary">
	                <b>{{tr('add_page')}}</b>
	                <a href="{{route('admin.pages.index')}}" style="float:right" class="btn btn-default">{{tr('pages')}}</a>
	            </div>

	            @include('new_admin.pages._form')

	        </div>

	    </div>

	</div>
   
@endsection

@section('scripts')
    <script src="https://cdn.ckeditor.com/4.5.5/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace( 'ckeditor' );
    </script>
@endsection


