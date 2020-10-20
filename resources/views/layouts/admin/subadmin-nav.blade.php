<aside class="main-sidebar">
    <?php 
        $sub_admin = Auth::guard('admin')->user();
    ?>
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="@if(Auth::guard('admin')->user()->picture) {{Auth::guard('admin')->user()->picture}} @else {{asset('placeholder.png')}} @endif" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{Auth::guard('admin')->user()->name}}</p>
                <a href="{{route('admin.profile')}}">{{ tr('admin') }}</a>
            </div>
            <div class="clearfix" style="height: 10px;clear:both"></div>
        </div>

        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">

            <li id="dashboard">
                <a href="{{route('admin.dashboard')}}">
                    <i class="fa fa-dashboard"></i> <span>{{tr('dashboard')}}</span>
                </a>
              
            </li>
            @if($sub_admin->users)
            <li class="treeview" id="users">
                <a href="#">
                    <i class="fa fa-user"></i> <span>{{tr('users')}}</span> <i class="fa fa-angle-left pull-right"></i>
                </a>

                <ul class="treeview-menu">
                    <li id="users-create"><a href="{{route('admin.users.create')}}"><i class="fa fa-circle-o"></i>{{tr('add_user')}}</a></li>
                    <li id="users-view"><a href="{{route('admin.users.index')}}"><i class="fa fa-circle-o"></i>{{tr('view_users')}}</a></li>
                </ul>
            </li>
            @endif
            <li class="header text-uppercase sidebar-header">{{tr('videos_management')}}</li>

            @if($sub_admin->channels)
            <li class="treeview" id="channels">
                <a href="{{route('admin.channels.index')}}">
                    <i class="fa fa-tv"></i> <span>{{tr('channels')}}</span> <i class="fa fa-angle-left pull-right"></i>
                </a>

                <ul class="treeview-menu">
                    
                    <li id="channels-create"><a href="{{route('admin.channels.create')}}"><i class="fa fa-circle-o"></i>{{tr('add_channel')}}</a></li>
                    
                    <li id="channels-view"><a href="{{route('admin.channels.index')}}"><i class="fa fa-circle-o"></i>{{tr('view_channels')}}</a></li>
                    
                    <li id="channels-subscribers"><a href="{{route('admin.channels.subscribers')}}"><i class="fa fa-circle-o"></i>{{tr('channel_subscribers')}}</a></li>
                
                </ul>
            </li>
            @endif

            @if($sub_admin->categories)
            <li id="categories">
                <a href="{{route('admin.categories.index')}}">
                    <i class="fa fa-list"></i> <span>{{tr('categories')}}</span><i class="fa fa-angle-left pull-right"></i> 
                </a>
                <ul class="treeview-menu">
                    
                    <li id="categories-create"><a href="{{route('admin.categories.create')}}"><i class="fa fa-circle-o"></i>{{tr('add_category')}}</a></li>
                    
                    <li id="categories-view"><a href="{{route('admin.categories.index')}}"><i class="fa fa-circle-o"></i>{{tr('view_categories')}}</a></li>

                </ul>
            </li>
            @endif

            @if($sub_admin->tags)
            <li id="tags">
                <a href="{{route('admin.tags.index')}}">
                    <i class="fa fa-tag"></i> <span>{{tr('tags')}}</span> 
                </a>
            </li>
            @endif

            @if($sub_admin->videos)
            <li class="treeview" id="videos">
                
                <a href="{{route('admin.video_tapes.index')}}">
                    <i class="fa fa-video-camera"></i> <span>{{tr('videos')}}</span> <i class="fa fa-angle-left pull-right"></i>
                </a>

                <ul class="treeview-menu">

                    <li id="add-video">
                        <a href="{{route('admin.video_tapes.create')}}">
                            <i class="fa fa-circle-o"></i>{{tr('add_video')}}
                        </a>
                    </li>

                    <li id="view-videos">
                        <a href="{{route('admin.video_tapes.index')}}">
                            <i class="fa fa-circle-o"></i>{{tr('view_videos')}}
                        </a>
                    </li>

                    @if(Setting::get('is_spam')== YES)

                        <li id="spam_videos">
                            <a href="{{route('admin.spam-videos')}}">
                                <i class="fa fa-flag"></i>{{tr('spam_videos')}}
                            </a>
                        </li>

                    @endif

                    <li id="reviews">
                        <a href="{{route('admin.reviews')}}">
                            <i class="fa fa-star"></i>{{tr('reviews')}}
                        </a>
                    </li>
                    
                </ul>

            </li>
            @endif

            <li class="treeview" id="custom_live_videos" style="display: none;">
                <a href="{{route('admin.custom.live.index')}}">
                    <i class="fa fa-wifi"></i> <span>{{tr('custom_live_videos')}}</span> <i class="fa fa-angle-left pull-right"></i>
                </a>

                <ul class="treeview-menu">

                    <li id="custom_live_video-create">
                        <a href="{{route('admin.custom.live.create')}}">
                            <i class="fa fa-circle-o"></i>{{tr('create_custom_live_video')}}
                        </a>
                    </li>

                    <li id="custom_live_videos-view">
                        <a href="{{route('admin.custom.live.index')}}">
                            <i class="fa fa-circle-o"></i>{{tr('custom_live_videos')}}
                        </a>
                    </li>
                </ul>
            </li>  
          
            <li class="header text-uppercase sidebar-header">{{tr('revenue_management')}}</li>

            @if($sub_admin->ads)
            <li class="treeview" id="videos-ads-details">

                <a href="{{route('admin.ads-details.index')}}">
                    <i class="fa fa-bullhorn"></i> <span>{{tr('ads')}}</span> <i class="fa fa-angle-left pull-right"></i>
                </a>

                <ul class="treeview-menu">
                    <li id="videos-ads-details-create"><a href="{{route('admin.ads-details.create')}}"><i class="fa fa-circle-o"></i>{{tr('create_ad')}}</a></li>

                    <li id="videos-ads-details-view"><a href="{{route('admin.ads-details.index')}}"><i class="fa fa-circle-o"></i>{{tr('view_and_assign_ad')}}</a></li>
                    
                    <li id="videos-ads-details-index"><a href="{{route('admin.video_ads.index')}}"><i class="fa fa-circle-o"></i>{{tr('assigned_ads')}}</a></li>
                </ul>

            </li>
            @endif

            @if(Setting::get('is_banner_ad')== YES)
                @if($sub_admin->banner_ads)
                <li class="treeview" id="banner-ads">
                    <a href="{{route('admin.banner_ads.index')}}">
                        <i class="fa fa-university"></i> <span>{{tr('banner_ads')}}</span> <i class="fa fa-angle-left pull-right"></i>
                    </a>

                    <ul class="treeview-menu">
                       
                        <li id="banner-ads-create"><a href="{{route('admin.banner_ads.create')}}"><i class="fa fa-circle-o"></i>{{tr('create_banner_ad')}}</a></li>
                    
                        <li id="banner-ads-view"><a href="{{route('admin.banner_ads.index')}}"><i class="fa fa-circle-o"></i>{{tr('banner_ads')}}</a></li>

                    </ul>

                </li>
                @endif
            @endif

            @if(Setting::get('is_banner_video') == YES)
            @if($sub_admin->banner_videos)
            <li class="treeview" id="banner-videos">
                <a href="{{route('admin.banner.videos.index')}}">
                    <i class="fa fa-university"></i> <span>{{tr('banner_videos')}}</span> <i class="fa fa-angle-left pull-right"></i>
                </a>

                <ul class="treeview-menu">
                    @if(get_banner_count() < 6)
                        <li id="add-banner-video"><a href="{{route('admin.banner.videos.create')}}"><i class="fa fa-circle-o"></i>{{tr('add_video')}}</a></li>
                    @endif
                    <li id="view-banner-videos"><a href="{{route('admin.banner.videos.index')}}"><i class="fa fa-circle-o"></i>{{tr('view_videos')}}</a></li>
                </ul>

            </li>
            @endif
            @endif

            @if($sub_admin->subscriptions)
            <li class="treeview" id="subscriptions">

                <a href="#">
                    <i class="fa fa-key"></i> <span>{{tr('subscriptions')}}</span> <i class="fa fa-angle-left pull-right"></i>
                </a>

                <ul class="treeview-menu">

                    <li id="subscriptions-create"><a href="{{route('admin.subscriptions.create')}}"><i class="fa fa-circle-o"></i>{{tr('add_subscription')}}</a></li>

                    <li id="subscriptions-view"><a href="{{route('admin.subscriptions.index')}}"><i class="fa fa-circle-o"></i>{{tr('view_subscriptions')}}</a></li>

                    <li id="subscriptions-auto-renewal-subscribers"><a href="{{route('admin.auto-renewal.subscribers')}}"><i class="fa fa-circle-o"></i>{{tr('automatic_subscribers')}}</a></li>

                    <li id="subscriptions-cancelled-subscribers"><a href="{{route('admin.auto-renewal.cancelled.subscribers')}}"><i class="fa fa-circle-o"></i>{{tr('cancelled_subscribers')}}</a></li>
                
                </ul>

            </li>
            @endif

            @if($sub_admin->coupons)
            <!-- Coupon Section-->
            <li class="treeview" id="coupons">
                <a href="#">
                    <i class="fa fa-gift"></i><span>{{tr('coupons')}}</span><i class="fa fa-angle-left pull-right"></i>
                </a>

                <ul class="treeview-menu">
                    <li id="coupons-create"><a href="{{route('admin.coupons.create')}}"><i class="fa fa-circle-o"></i>{{tr('add_coupon')}}</a></li>
                    <li id = "coupons-view"><a href="{{route('admin.coupons.index')}}"><i class="fa fa-circle-o"></i>{{tr('view_coupon')}}</a></li>
                </ul>
            </li>
            @endif

            @if($sub_admin->coupons)
            <li id="custom-push">
                <a href="{{route('admin.push')}}">
                    <i class="fa fa-send"></i> <span>{{tr('custom_push')}}</span>
                </a>
            </li>
            @endif

            <li>
                <a href="{{route('admin.logout')}}">
                    <i class="fa fa-sign-out"></i> <span>{{tr('sign_out')}}</span>
                </a>
            </li>

        </ul>

    </section>

    <!-- /.sidebar -->

</aside>