<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{Setting::get('site_name')}} | @yield('title') </title>  
    <meta name="robots" content="noindex">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="{{asset('admin-css/bootstrap/css/bootstrap.min.css')}}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{asset('admin-css/bootstrap/css/ionicons.min.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('admin-css/dist/css/AdminLTE.min.css')}}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{asset('admin-css/plugins/iCheck/square/blue.css')}}">

    <link rel="shortcut icon" href="@if( Setting::get('site_icon')) {{ Setting::get('site_icon') }} @else {{asset('favicon.png')}} @endif">

    <?php echo Setting::get('header_scripts') ?>

     <style>
     
        .admin-bg-login {
            background-image: url("{{asset('live.jpg')}}");
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: 100%;
            /*background-repeat: no-repeat; /*/
            opacity: 0.7;
            filter: alpha(opacity=80);
        }
    </style>

</head>

<body class="hold-transition login-page">
    
        <div class="admin-bg-login">

    <div class="col-md-6 col-md-offset-3">

        @include('notification.notify')

    </div>

    <div class="login-box">

        @yield('content')

    </div>

</div>

    <!-- jQuery 2.2.0 -->
    <script src="{{asset('admin-css/plugins/jQuery/jQuery-2.2.0.min.js')}}"></script>
    <!-- Bootstrap 3.3.6 -->
    <script src="{{asset('admin-css/bootstrap/js/bootstrap.min.js')}}"></script>
    <!-- iCheck -->
    <script src="{{asset('admin-css/plugins/iCheck/icheck.min.js')}}"></script>

    <script>
        $(function () {
            $('input').iCheck({
              checkboxClass: 'icheckbox_square-blue',
              radioClass: 'iradio_square-blue',
              increaseArea: '20%' // optional
            });
        });
    </script>

    @yield('scripts')

    <?php echo Setting::get('body_scripts') ?>


</body>

</html>

