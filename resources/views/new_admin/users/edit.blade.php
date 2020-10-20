@extends('layouts.admin')

@section('title', tr('edit_user'))

@section('content-header', tr('edit_user'))

@section('breadcrumb')
    <li><a href="{{route('admin.users.index')}}"><i class="fa fa-user"></i> {{tr('users')}}</a></li>
    <li class="active">{{tr('edit_user')}}</li>
@endsection

@section('styles')

<link rel="stylesheet" href="{{asset('admin-css/plugins/datepicker/datepicker3.css')}}">

@endsection

@section('content')

    @include('new_admin.users._form')

@endsection

@section('scripts')
<script src="{{asset('admin-css/plugins/datepicker/bootstrap-datepicker.js')}}"></script> 

<script src="{{asset('assets/js/jstz.min.js')}}"></script>
<script>
    
    $(document).ready(function() {

        var dMin = new Date().getTimezoneOffset();
        var dtz = -(dMin/60);
        // alert(dtz);
        $("#userTimezone").val(jstz.determine().name());
    });

var max_age_limit = "{{ Setting::get('max_register_age_limit' , 18) }}";

max_age_limit = max_age_limit ? "-"+max_age_limit+"y" : "-15y";

$('#dob').datepicker({
    autoclose:true,
    format : 'dd-mm-yyyy',
    endDate: max_age_limit,
});

function loadFile(event, id){
    // alert(event.files[0]);
    var reader = new FileReader();
    reader.onload = function(){
      var output = document.getElementById(id);
      // alert(output);
      output.src = reader.result;
      //$("#c4-header-bg-container .hd-banner-image").css("background-image", "url("+this.result+")");
    };
    reader.readAsDataURL(event.files[0]);
}
</script>

<script src="https://cdn.ckeditor.com/4.5.5/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace( 'description' );
</script>

@endsection