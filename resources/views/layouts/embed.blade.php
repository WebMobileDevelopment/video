<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{Setting::get('site_name')}} | @yield('title') </title>  
    <meta name="robots" content="noindex">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="shortcut icon" href="@if( Setting::get('site_icon')) {{ Setting::get('site_icon') }} @else {{asset('favicon.png')}} @endif">

    <style type="text/css">
        html,body {
            height: 100%;
            width: 100%;
            margin:0;
        }

    </style>

</head>

<body class="hold-transition login-page">
    

        @yield('content')


    <!-- jQuery 2.2.0 -->
    <script src="{{asset('admin-css/plugins/jQuery/jQuery-2.2.0.min.js')}}"></script>
    <!-- Bootstrap 3.3.6 -->
    <script src="{{asset('admin-css/bootstrap/js/bootstrap.min.js')}}"></script>

    @yield('scripts')

</body>

</html>

