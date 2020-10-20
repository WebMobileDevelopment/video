<!Doctype html>
<html class="no-js" lang="">
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>  
    <meta name="robots" content="noindex">
    
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <meta name="description" content="">

    <link rel="shortcut icon" href=" @if(Setting::get('site_icon')) {{ Setting::get('site_icon') }} @else {{asset('favicon.png') }} @endif">

    <link rel="stylesheet" href="{{ asset('admin-css/bootstrap/css/bootstrap.min.css')}}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">

    <!-- Ionicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">

    <link rel="stylesheet" href="{{ asset('admin-css/plugins/jvectormap/jquery-jvectormap-1.2.2.css')}}">

    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('admin-css/plugins/datatables/dataTables.bootstrap.css')}}">

    @yield('mid-styles')

    <link rel="stylesheet" href="{{ asset('admin-css/plugins/select2/select2.min.css')}}">

      <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('admin-css/dist/css/AdminLTE.min.css') }}">

    <link rel="stylesheet" href="{{ asset('admin-css/dist/css/skins/_all-skins.min.css')}}">

    <link rel="stylesheet" href="{{ asset('admin-css/dist/css/custom.css')}}">
    
    <link rel="stylesheet" href="{{ asset('admin-css/plugins/datepicker/datepicker3.css')}}">

    @yield('styles')

    <style>
       
        /*

        .skin-blue .main-header .navbar {
            background: linear-gradient(to bottom right, rgb(86, 202, 193), #0e5c73);
        }
        
        .skin-blue .main-header .logo {
            background: linear-gradient(to bottom right, rgb(86, 202, 193), #0e5c73);
        }

        .skin-blue .main-sidebar{
            background: linear-gradient(to bottom right, rgb(42, 49, 53), #39a1bf);
        }*/

        .popover {
            max-width: 500px;
        }

        .popover-content {
            padding: 10px 5px;

        }

        .popover-list li{

            margin-bottom: 5px;

        }
    </style>

    <?php echo Setting::get('header_scripts') ?>

    <style>
        .example-note {
            color: gray;
            margin: 5px 0px;

        }
    </style>

</head>
<body class="hold-transition skin-red sidebar-mini">

    <div class="wrapper">      

        @if(Auth::guard('admin')->user()->role == ADMIN)

            @include('layouts.admin.header')

            @include('layouts.admin.nav')

        @else 

            @include('layouts.admin.subadmin-header')

            @include('layouts.admin.subadmin-nav')

        @endif

        <div class="content-wrapper">

            <section class="content-header">

                <h1>@yield('content-header')<small>@yield('content-sub-header')</small></h1>

                <ol class="breadcrumb">

                    <li>
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="fa fa-dashboard"></i>{{ tr('home') }}
                        </a>
                    </li>

                    @yield('breadcrumb')

                </ol>
                
            </section>

            <!-- Main content -->
            <section class="content">
                @yield('content')
            </section>

        </div>

        <!-- include('layouts.admin.footer') -->

        <!-- include('layouts.admin.left-side-bar') -->

    </div>


    <!-- jQuery 2.2.0 -->
    <script src="{{asset('admin-css/plugins/jQuery/jQuery-2.2.0.min.js')}}"></script>
    <!-- Bootstrap 3.3.6 -->
    <script src="{{asset('admin-css/bootstrap/js/bootstrap.min.js')}}"></script>

    <script src="{{asset('admin-css/plugins/datatables/jquery.dataTables.min.js')}}"></script>

    <script src="{{asset('admin-css/plugins/datatables/dataTables.bootstrap.min.js')}}"></script>

    <!-- Select2 -->
    <script src="{{asset('admin-css/plugins/select2/select2.full.min.js')}}"></script>
    <!-- InputMask -->
    <script src="{{asset('admin-css/plugins/input-mask/jquery.inputmask.js')}}"></script>
    <script src="{{asset('admin-css/plugins/input-mask/jquery.inputmask.date.extensions.js')}}"></script>

    <script src="{{asset('admin-css/plugins/input-mask/jquery.inputmask.extensions.js')}}"></script>

    <!-- SlimScroll -->
    <script src="{{asset('admin-css/plugins/slimScroll/jquery.slimscroll.min.js')}}"></script>
    <!-- FastClick -->
    <script src="{{asset('admin-css/plugins/fastclick/fastclick.js')}}"></script>
    <!-- AdminLTE App -->
    <script src="{{asset('admin-css/dist/js/app.min.js')}}"></script>

    <!-- jvectormap -->
    <script src="{{asset('admin-css/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js')}}"></script>

    <script src="{{asset('admin-css/plugins/jvectormap/jquery-jvectormap-world-mill-en.js')}}"></script>

    <script src="{{asset('admin-css/plugins/chartjs/Chart.min.js')}}"></script>

    <script src = "{{asset('admin-css/plugins/datepicker/bootstrap-datepicker.js')}}"></script>

    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <!-- <script src="{{asset('admin-css/dist/js/pages/dashboard2.js')}}"></script> -->

    <script src="{{asset('admin-css/dist/js/demo.js')}}"></script>

    <!-- page script -->
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(document).ready(function(){
            $('#help-popover').popover({
                html : true, 
                content: function() {
                    return $('#help-content').html();
                } 
            });  
        });
        
        $(function () {
            $("#example1").DataTable();
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });
        });
    </script>

    @yield('scripts')


    <script type="text/javascript">
        $("#{{$page}}").addClass("active");
        @if(isset($sub_page)) $("#{{$sub_page}}").addClass("active"); @endif
    </script>


    <script type="text/javascript">
        
        $(document).ready(function() {
        $('#expiry_date').datepicker({
            autoclose:true,
            format : 'dd-mm-yyyy',
            startDate: 'today',
        });

        
    });

    </script>

    <script type="text/javascript">
        
        $(function () {
            //Initialize Select2 Elements
            $(".select2").select2();

            //Datemask dd/mm/yyyy
            $("#datemask").inputmask("dd:mm:yyyy", {"placeholder": "hh:mm:ss"});
            //Datemask2 mm/dd/yyyy
            // $("#datemask2").inputmask("hh:mm:ss", {"placeholder": "hh:mm:ss"});
            //Money Euro
            $("[data-mask]").inputmask();
        });
    </script>

    <?php echo Setting::get('body_scripts') ?>

</body>

</html>
