@if (!Auth::check()) 

<style type="text/css">
    
.mobile-header li {

    width: 19% !important;

}

</style>


@endif
<style type="text/css">
.left-group{
    float:left;
}
.btn-logo-img {
    width: 65px;
}
#go_live_global_btn {
    
}
</style>

<div class="header-search" id="search-section">
    <form method="post" action="{{route('search-all')}}" id="userSearch_min">
        <div class="form-group no-margin pull-left width-95">
            <input type="text" id="auto_complete_search_min" name="key" class="auto_complete_search search-query form-control" required placeholder="Search">
        </div>
    </form>

    <a href="#" id="close-btn"><i class="fa fa-close"></i></a>   

    <div class="clear-both"></div>
</div>

<div class="streamtube-nav" id="header-section">

    <div class="row">

        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">

            <a href="#" class="hidden-xs"><img src="{{asset('images/menu.png')}}" class="toggle-icon"></a>

            <a href="#" class="hidden-lg hidden-md hidden-sm"><img src="{{asset('images/menu_white.png')}}" class="toggle-icon"></a>

            <a href="{{route('user.dashboard')}}"> 
                @if(Setting::get('site_logo'))
                    <img src="{{Setting::get('site_logo')}}" class="logo-img">
                @else
                    <img src="{{asset('logo.png')}}" class="logo-img">
                @endif
            </a>

        </div>

        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 hidden-xs">

            <div id="custom-search-input" class="left-group">
                <form method="post" action="{{route('search-all')}}" id="userSearch">
                    <div class="input-group search-input">
                    
                        <input type="text" id="auto_complete_search" name="key" class="search-query form-control" required placeholder="{{tr('search')}}" />
                        <div class="input-group-btn">
                            <button class="btn btn-danger" id="search_submit_btn" type="submit">
                            <i class=" glyphicon glyphicon-search"></i>
                            </button>
                        </div>
                    <!--BYME-->
					
					<!--ENDME-->
                    </div>
                </form>
            </div><!--custom-search-input end-->
            <div class="">
                <a href="https://r4d-ondemand.com/">
                    <button class="btn btn-md btn-primary" style="float:left;">
                        <img src="{{Setting::get('site_icon')}}" class="btn-logo-img" />Video On Demand
                    </button>
                <!-- <input type="button" class="btn btn-md btn-primary" style="float:left;" value="R4D&copy; Video On Demand"> -->
                </a>
                <a href="https://www.r4d4us.com/page_view/5" id="">
                    <button class="btn btn-md btn-warning" style="float:left;margin-left: 5px;">
                        What is<img src="{{Setting::get('site_icon')}}" class="btn-logo-img" />or Real 4D
                    </button>
                </a>
                <a href="https://www.r4d4us.com/mychannels/list" id="go_live_global_btn">
                    <button class="btn btn-success pull-right btn_go_live">
                        <i class="fa fa-video-camera"></i>
                        <span>{{tr('go_live')}}</span>
                    </button>
                </a>
                <!--@if(Auth::check()) -->
                <!--<a href="{{route('user.directupload')}}"><input type="button" class="btn btn-md btn-primary" style="margin-top: -29px;" value="Upload My Video"></a>-->
                <!--@endif-->
                <a href="{{route('user.directupload')}}">
                    <button class="btn btn-md btn-danger" style="float:right;" id="update_video">
                        Upload My Video
                    </button>
                </a>
                
                <a href="{{route('user.directupload.r4d')}}">
                    <button class="btn btn-md btn-secondary" style="float: right; margin-right:5px;">
                        Upload My <img src="{{Setting::get('site_icon')}}" class="btn-logo-img" />Video
                    </button>
                </a>
            </div>
        </div>

        <!-- ========RESPONSIVE  HEADER VISIBLE IN MOBAILE VIEW====== -->
         <!--div class="col-lg-2"></div-->
        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12 hidden-xs visible-sm visible-md visible-lg">

            @if(Auth::check())

                <div class="y-button profile-button">

                   <div class="dropdown pull-right">

                        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">

                            <img class="profile-image" src="{{Auth::user()->picture ?: asset('placeholder.png')}}">
                        </button>
                        
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">

                            <a href="{{route('user.profile')}}">
                                <div class="display-inline">
                                    <div class="menu-profile-left">
                                        <img src="{{Auth::user()->picture ?: asset('placeholder.png')}}">
                                    </div>
                                    <div class="menu-profile-right">
                                        <h4>{{Auth::user()->name}}</h4>
                                        <p>{{Auth::user()->email}}</p>
                                    </div>
                                </div>
                            </a>

                            <li role="separator" class="divider"></li>

                            <div class="row">

                                <div class="col-xs-6">
                                    <a href="/settings" class="menu-link">
                                        <i class="fa fa-cog"></i>
                                        {{tr('settings')}}
                                    </a>
                                </div>

                                <div class="col-xs-6">
                                    <a href="{{route('user.logout')}}" onclick="return confirm(&quot;{{tr('logout_confirmation')}}&quot;)" class="menu-link">
                                        <i class="fa fa-sign-out"></i>
                                        {{tr('logout')}}
                                    </a>
                                </div>
                            </div>
                           

                        </ul>
                
                    </div>

                </div>

                <ul class="nav navbar-nav pull-right">

                    <li  class="dropdown">
                        <a class="nav-link text-light notification-link" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" onclick="return notificationsStatusUpdate();">
                            <i class="fa fa-bell"></i>
                        </a>

                        <ul class="dropdown-menu-notification dropdown-menu">

                            <li class="notification-head text-light bg-dark">
                                <div class="row">
                                    <div class="col-lg-12 col-sm-12 col-12">
                                        <span>
                                            {{tr('notifications')}} 
                                            (<span id="global-notifications-count">0</span>)
                                        </span>
                                        <!-- <a href="" class="float-right text-light">Mark all as read</a> -->
                                    </div>
                                </div>
                            </li>

                            <span id="global-notifications-box"></span>
                            
                            <li class="notification-footer bg-dark text-center" id="viewAll">
                                <a href="{{route('user.bell_notifications.index')}}" class="text-light">
                                    {{tr('view_all')}}
                                </a>
                            </li>
                        </ul>

                    </li>
                 
                </ul>
                @if(Setting::get('is_direct_upload_button') == YES)

                <a href="{{userChannelId()}}" class="btn pull-right user-upload-btn" title="{{tr('upload_video')}}">
                     {{tr('upload')}} 
                    <i class="fa fa-upload fa-1x"></i>
                </a>

                @endif

            @else
                <div class="y-button">
                    <a href="{{route('user.login.form')}}" class="y-signin" style="padding-left: 14px;padding-right: 15px;">{{tr('login')}}</a>
                </div>

                @if(Setting::get('is_direct_upload_button') == YES)

                    <a href="{{route('user.login.form')}}" class="btn pull-right user-upload-btn" title="{{tr('upload_video')}}"> 
                        {{tr('upload')}} 
                        <i class="fa fa-upload fa-1x"></i>
                    </a>

                @endif

            @endif

            <ul class="nav navbar-nav pull-right" style="margin: 3.5px 0px">

                @if(Setting::get('admin_language_control'))
                    
                    @if(count($languages = getActiveLanguages()) > 1) 
                       
                        <li  class="dropdown">
                    
                            <a href="#" class="dropdown-toggle language-icon" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-globe"></i> <b class="caret"></b></a>

                            <ul class="dropdown-menu languages">

                                @foreach($languages as $h => $language)

                                    <li class="{{(\Session::get('locale') == $language->folder_name) ? 'active' : ''}}" ><a href="{{route('user_session_language', $language->folder_name)}}" style="{{(\Session::get('locale') == $language->folder_name) ? 'background-color: #cc181e' : ''}}">{{$language->folder_name}}</a></li>
                                @endforeach
                                
                            </ul>
                         
                        </li>
                
                    @endif

                @endif

            </ul>

        </div>

        <!-- ======== RESPONSIVE HEADER VISIBLE IN MOBAILE VIEW====== -->

        @include('layouts.user.header-mobile')

        <!-- ======== RESPONSIVE HEADER VISIBLE IN MOBAILE VIEW====== -->

    </div><!--end of row-->

</div><!--end of streamtube-nav-->
