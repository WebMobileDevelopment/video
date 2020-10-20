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

</head>

<body class="hold-transition login-page">

    <div class="row">

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            <div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">

            <div class="box box-primary" style="margin-top: 10px">

                <div class="box-header with-border">

                    <img class="adm-log-logo pull-right" style="width: 120px;height: 32px;" src="@if(Setting::get('site_logo')) {{Setting::get('site_logo')}} @else {{asset('logo.png')}} @endif"/>

                    <h2 class="pull-left">@if($model) {{$model->heading}} @else {{tr('model')}} @endif</h2>
                   
                </div>

                <div class="box-body">

                    <div class="col-md-12">

                    @if($model) <?php echo $model->description; ?> @else {{tr('model')}} @endif

                    </div>

                
                </div>

                <div class="box-footer">

                    <a href="{{url('/')}}" class="btn btn-success pull-right"> <i class="fa fa-arrow-left"></i> {{tr('go_home')}} </a>
                
                </div>
                
            </div>

                        
        </div>

    </div>

    <!-- jQuery 2.2.0 -->
    <script src="{{asset('admin-css/plugins/jQuery/jQuery-2.2.0.min.js')}}"></script>
    <!-- Bootstrap 3.3.6 -->
    <script src="{{asset('admin-css/bootstrap/js/bootstrap.min.js')}}"></script>
    <!-- iCheck -->
    <script src="{{asset('admin-css/plugins/iCheck/icheck.min.js')}}"></script>

</body>

</html>

