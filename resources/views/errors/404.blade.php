<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{Setting::get('site_name' , tr('site_name'))}}</title>  
    <meta name="robots" content="noindex">
    <link rel="stylesheet" href="{{asset('assets/css/app.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/theme.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/font-awesome.min.css')}}">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800' rel='stylesheet' type='text/css'>

    <link rel="stylesheet" href="{{asset('assets/css/responsive.css')}}">

    <link rel="shortcut icon" type="image/png" href="{{Setting::get('site_icon' , asset('img/favicon.png'))}}"/>


</head>
<body>
<div class="off-canvas-wrapper">
    <div class="off-canvas-wrapper-inner" data-off-canvas-wrapper>

        <div class="off-canvas-content" data-off-canvas-content>

            <section class="error-page">
                <div class="row secBg">
                    <div class="large-8 large-centered columns">
                        <div class="error-page-content text-center" style="padding:0 !important">
                            <div class="error-img text-center">
                                <img src="{{asset('images/404-error.png')}}" alt="404 page">
                                <div class="spark">
                                    <img class="flash" src="{{asset('images/spark.png')}}" alt="spark">
                                </div>
                            </div>
                            <h1>{{tr('page_not_found')}}</h1>
                            <p>We can't find the page you're looking for</p>
                            <a href="{{route('user.dashboard')}}" class="button">{{tr('go_back_home')}}</a>
                        </div>
                    </div>
                </div>
            </section>

        </div>

        <!--end off canvas content-->
    </div><!--end off canvas wrapper inner-->
</div><!--end off canvas wrapper-->
<!-- script files -->
</body>

</html>
