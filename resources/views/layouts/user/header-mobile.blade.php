<div class="col-lg-3 col-md-3 col-sm-4 col-xs-6 visible-xs hidden-sm hidden-md hidden-lg">
            
    @if(Auth::check())
        
        <div class="y-button profile-button" style="position: unset;">
           
           <div class="dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="background-color: transparent;">

                    @if(Auth::user()->picture != "")
                        <img class="profile-image" src="{{Auth::user()->picture}}">
                    @else
                        <img class="profile-image" src="{{asset('placeholder.png')}}">
                    @endif

                </button>

                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                    <a href="{{route('user.profile')}}">
                        <div class="display-inline">
                            <div class="menu-profile-left">
                                <img src="{{Auth::user()->picture}}">
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
                            <a href="/settings" class="menu-link"><i class="fa fa-cog"></i>{{tr('settings')}}</a>
                        </div>
                        <div class="col-xs-6">
                            <a href="{{route('user.logout')}}" class="menu-link"><i class="fa fa-sign-out"></i>{{tr('logout')}}</a>
                        </div>
                    </div>
                
                </ul>

                
            
            </div>

        </div><!--y-button end-->

        <!-- Version 3.1 feature -->

        @if(Setting::get('is_direct_upload_button') == YES)

            <a href="{{userChannelId()}}" class="btn pull-right" style="margin-right: 10px;color: red;background:white;box-shadow: none;" title="{{tr('upload_video')}}">
                <i class="fa fa-upload fa-1x"></i>
            </a>

        @endif

    @endif

    <section id="language-section">

    <ul class="nav navbar-nav pull-right" style="margin: 3.5px 0px">

        @if(Setting::get('admin_language_control'))

            @if(count($languages = getActiveLanguages()) > 1) 
               
                <li  class="dropdown">
            
                    <a href="#" class="dropdown-toggle language-icon" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-globe"></i> <b class="caret"></b></a>

                    <ul class="dropdown-menu languages1">

                        @foreach($languages as $h => $language)

                            <li class="{{(\Session::get('locale') == $language->folder_name) ? 'active' : ''}}" ><a href="{{route('user_session_language', $language->folder_name)}}" style="{{(\Session::get('locale') == $language->folder_name) ? 'background-color: #cc181e' : ''}}">{{$language->folder_name}}</a></li>
                        @endforeach
                        
                    </ul>
                 
                </li>
        
            @endif

        @endif

    </ul>

    </section>

    @if(!Auth::check())

        <div class="y-button pull-right" style="position: unset;">
            <a href="{{route('user.login.form')}}" class="y-signin" style="margin-left: 0px;" title="{tr('login')}}"><i class="fa fa-sign-in"></i></a>
        </div><!--y-button end-->

        @if(Setting::get('is_direct_upload_button') == YES)

            <a href="{{route('user.login.form')}}" class="btn pull-right" style="margin-right: 10px;color: red;background:white;box-shadow: none;" title="{{tr('upload_video')}}">
                <i class="fa fa-upload fa-1x"></i>
            </a>

        @endif

    @endif

    <span class="search-cls pull-right" id="search-btn"><i class="fa fa-search top5"></i></span>
    
    <div class="clearfix"></div>

</div>

<div class="col-xs-12 visible-xs">
    <ul class="mobile-header">
        <li><a href="{{route('user.dashboard')}}" class="mobile-menu">
            <i class="material-icons">home</i> 
            <span class="hidden-xxxs">{{tr('home')}}</span>
        </a></li>
        <li><a href="{{route('user.trending')}}" class="mobile-menu">
            <i class="material-icons">whatshot</i>
            <span class="hidden-xxxs">{{tr('trending')}}</span>
        </a></li>
        <li><a href="{{route('user.channel.list')}}" class="mobile-menu">
            <i class="material-icons">live_tv</i>
            <span class="hidden-xxxs">{{tr('channels')}}</span>
        </a></li>
        <li><a href="{{route('user.channel.list')}}" class="mobile-menu">
            <i class="material-icons">movie</i>
            <span class="hidden-xxxs">{{tr('user_custom_live_videos')}}</span>
        </a></li>
        <li><a href="{{route('user.live_videos')}}" class="mobile-menu">
            <i class="material-icons">videocam</i>
            <span class="hidden-xxxs">Live videos</span>
        </a></li>

        @if(Auth::check())

            @if(Setting::get('create_channel_by_user') == CREATE_CHANNEL_BY_USER_ENABLED || Auth::user()->is_master_user == 1)

                <li><a href="{{route('user.channel.mychannel')}}" class="mobile-menu">
                    <i class="material-icons">subscriptions</i>
                    <span class="hidden-xxxs">{{tr('my_channels')}}</span>
                </a></li>
            @else

                <li>
                    <a href="{{route('user.history')}}" class="mobile-menu">
                        <i class="material-icons">history</i>
                        <span class="hidden-xxxs">{{tr('history')}}</span>
                    </a>
                </li>
            @endif
        @endif

    </ul>
</div>
