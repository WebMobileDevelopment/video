@extends('layouts.admin')

@section('title', tr('sub_admin_edit'))

@section('content-header', tr('sub_admin_edit'))

@section('breadcrumb')
    <li><a href="{{route('admin.sub_admins.index')}}"><i class="fa fa-user"></i> {{tr('sub_admins')}}</a></li>
    <li class="active"><i class="fa fa-user-plus"></i> {{tr('sub_admin_edit')}}</li>
@endsection

@section('content')

@include('new_admin.sub_admins._form')

@endsection

@section('scripts')

<script src="{{asset('assets/js/jstz.min.js')}}"></script>
<script>
    
    $(document).ready(function() {

        var dMin = new Date().getTimezoneOffset();
        var dtz = -(dMin/60);
        // alert(dtz);
        $("#userTimezone").val(jstz.determine().name());
    });

</script>

<script src="{{asset('admin-css/plugins/iCheck/icheck.min.js')}}"></script>

@endsection