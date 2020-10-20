
<meta name="description" content="{{Setting::get('meta_description')}}">

<meta name="author" content="{{Setting::get('meta_author')}}">

<meta name="keywords" content="{{Setting::get('meta_keywords')}}">

<link rel="stylesheet" href="{{asset('streamtube/css/bootstrap.min.css')}}">

<link rel="stylesheet" href="{{asset('assets/bootstrap/css/jquery-ui.css')}}">

<link rel="stylesheet" type="text/css" href="{{asset('streamtube/fonts/font-awesome/css/font-awesome.min.css')}}">

<link href="https://fonts.googleapis.com/icon?family=Material+Icons"
  rel="stylesheet">

<link href='https://fonts.googleapis.com/css?family=Roboto:400,700' rel='stylesheet' type='text/css'> 

<link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/slick.css')}}"/>

<link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/slick-theme.css')}}"/>

<link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/style.css')}}">


<link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/responsive.css')}}">

<link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/responsive1.css')}}">

<link rel="stylesheet" href="{{ asset('admin-css/plugins/select2/select2.min.css')}}">

<link rel="shortcut icon" type="image/png" href="{{Setting::get('site_icon' , asset('img/favicon.png'))}}"/>

<style type="text/css">
    
    .ui-autocomplete{
        z-index: 99999;
    }

</style>

@yield('meta_tags')

@yield('styles')

<?php echo Setting::get('google_analytics') ?: ""; ?>

<?php echo Setting::get('header_scripts') ?: "" ?>