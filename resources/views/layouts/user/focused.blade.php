<!DOCTYPE html>
<html>

<head>
    <title>@if(Setting::get('site_name')) {{Setting::get('site_name') }} @else {{tr('site_name')}} @endif</title>  
    <meta name="robots" content="noindex">
    
    <meta name="viewport" content="width=device-width,  initial-scale=1">
    <link rel="stylesheet" href="{{asset('streamtube/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('streamtube/css/jquery-ui.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('streamtube/fonts/font-awesome/css/font-awesome.min.css')}}">
    <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,700' rel='stylesheet' type='text/css'> 
    <link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/slick.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/slick-theme.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/style.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/responsive.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/responsive1.css')}}">

    <link rel="shortcut icon" type="image/png" href="@if(Setting::get('site_icon')) {{Setting::get('site_icon')}} @else {{asset('img/favicon.png')}} @endif"/>

    <style type="text/css">
        .ui-autocomplete{
          z-index: 99999;
        }
    </style>

    @yield('styles')

    <?php echo Setting::get('header_scripts') ?>

</head>

<body>

    <div class="wrap">

        @include('layouts.user.focused-nav')

        @yield('content')

        <!-- <div class="bottom"></div> -->
        
        @include('layouts.user.footer')      

    </div> 
    
    <script src="{{asset('streamtube/js/jquery.min.js')}}"></script>
    <script src="{{asset('streamtube/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('streamtube/js/jquery-ui.js')}}"></script>
    <script type="text/javascript" src="{{asset('streamtube/js/jquery-migrate-1.2.1.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('streamtube/js/slick.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('streamtube/js/script.js')}}"></script>

    <script type="text/javascript">

        jQuery(document).ready( function () {
            //autocomplete
            jQuery("#auto_complete_search").autocomplete({
                source: "{{route('search')}}",
                minLength: 1,
                select: function(event, ui){

                    // set the value of the currently focused text box to the correct value

                    if (event.type == "autocompleteselect"){
                        
                        // console.log( "logged correctly: " + ui.item.value );

                        var username = ui.item.value;

                        if(ui.item.value == 'View All') {

                            // console.log('View AALLLLLLLLL');

                            window.location.href = "{{route('search', array('q' => 'all'))}}";

                        } else {
                            // console.log("User Submit");

                            jQuery('#auto_complete_search').val(ui.item.value);

                            jQuery('#userSearch').submit();
                        }

                    }                        
                }      // select

            }); 

        });

    </script>

    @yield('scripts')

    <?php echo Setting::get('body_scripts') ?>

</body>

</html>