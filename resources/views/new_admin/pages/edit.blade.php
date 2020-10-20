@extends('layouts.admin')

@section('title', tr('edit_page'))

@section('content-header', tr('edit_page'))

@section('breadcrumb')
    <li><a href="{{route('admin.pages.index')}}"><i class="fa fa-book"></i> {{tr('pages')}}</a></li>
    <li class="active"> {{tr('edit_page')}}</li>
@endsection

@section('content')


<div class="row">

    <div class="col-md-12">
        
        @include('notification.notify')

        <div class="box box-info">

            <div class="box-header">
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
