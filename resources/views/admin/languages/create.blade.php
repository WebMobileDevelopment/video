@extends('layouts.admin')

@section('title', tr('create_language'))

@section('content-header', tr('create_language'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li><a href="{{route('admin.languages.index')}}"><i class="fa fa-globe"></i>{{tr('languages')}}</a></li>
    <li class="active"><i class="fa fa-globe"></i>&nbsp; {{tr('create_language')}}</li>
@endsection

@section('content')

    @include('notification.notify')

  	<div class="row">

	    <div class="col-md-10">

	        <div class="box box-primary">

	            <div class="box-header label-primary">
	                <b>{{tr('create_language')}}</b>
	                <a href="{{route('admin.languages.index')}}" style="float:right" class="btn btn-default">{{tr('languages')}}</a>
	            </div>

	            <form  action="{{(Setting::get('admin_delete_control')) ? '' :route('admin.languages.save')}}" method="POST" enctype="multipart/form-data" role="form">

	                <div class="box-body">


	                    <div class="form-group">
	                        <label for="name">{{tr('short_name')}}</label>
	                        <input type="text" class="form-control" name="folder_name" id="name" placeholder="{{tr('example_language')}}" required maxlength="4">

	                    </div>

	                    <div class="form-group">
	                        <label for="name">{{tr('language')}}</label>
	                        <div>
		                        <input type="text" class="form-control" name="language" id="name" placeholder="{{tr('example_language2')}}" required maxlength="64">
	                        </div>
	                    </div>

	                    <div class="form-group">
	                        <label for="name">{{tr('auth_file')}}</label>
	                        <input type="file" id="auth_file" name="auth_file" placeholder="{{tr('picture')}}" required>
	                    </div>

	                    <div class="form-group">
	                        <label for="name">{{tr('messages_file')}}</label>
	                        <input type="file" id="messages_file" name="messages_file" placeholder="{{tr('picture')}}" required>
	                    </div>

	                    <div class="form-group">
	                        <label for="name">{{tr('pagination_file')}}</label>
	                        <input type="file" id="pagination_file" name="pagination_file" placeholder="{{tr('picture')}}" required>
	                    </div>

	                    <div class="form-group">
	                        <label for="name">{{tr('passwords_file')}}</label>
	                        <input type="file" id="passwords_file" name="passwords_file" placeholder="{{tr('picture')}}" required>
	                    </div>

	                    <div class="form-group">
	                        <label for="name">{{tr('validation_file')}}</label>
	                        <input type="file" id="validation_file" name="validation_file" placeholder="{{tr('picture')}}" required>
	                    </div>
	                     
	                </div>

	              <div class="box-footer">
	                    <button type="reset" class="btn btn-danger">{{tr('cancel')}}</button>
	                    @if(Setting::get('admin_delete_control'))
	                    <button type="button" class="btn btn-success pull-right" disabled>{{tr('submit')}}</button>
	                    @else
	                    <button type="submit" class="btn btn-success pull-right">{{tr('submit')}}</button>
	                    @endif
	              </div>

	            </form>
	        
	        </div>

	    </div>

	</div>
   
@endsection

