@extends('layouts.admin')

@section('title', tr('add_channel'))

@section('content-header', tr('add_channel'))

@section('breadcrumb')
    <li><a href="{{route('admin.channels.index')}}"><i class="fa fa-suitcase"></i> {{tr('channels')}}</a></li>
    <li class="active">{{tr('add_channel')}}</li>
@endsection

@section('content')

<div class="row">

    <div class="col-md-12">

        @include('notification.notify')

        <div class="box box-primary">

            <div class="box-header label-primary">
                <b style="font-size:18px;">{{ tr('add_channel') }}</b>
                <a href="{{ route('admin.channels.index') }}" class="btn btn-default pull-right">{{ tr('channels') }}</a>
            </div>

			@include('new_admin.channels._form')

        </div>

    </div>

</div>



@endsection

@section('scripts')
    <script src="https://cdn.ckeditor.com/4.5.5/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace( 'description' );
    </script>
@endsection