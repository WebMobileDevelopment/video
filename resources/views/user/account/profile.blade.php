@extends('layouts.user')

@section('styles')
<style type="text/css">
.history-image{ 
    width: 30% !important;
}
.history-title {
    width: 65% !important;
}
</style>
@endsection
@section('content')

<div class="y-content">
    <div class="row y-content-row">
        @include('layouts.user.nav')

        <div class="page-inner col-sm-9 col-md-10 profile-edit">
            
            <div class="profile-content slide-area1">
                <div class="row no-margin">
                    <!--profile-view end-->
                    <?php /* 
                    <div class="col-md-7 col-lg-7 profile-view">
                        <div class="edit-profile ">
                            <div class="profile-details">
                                <div class="sub-profile">
                                    <h4 class="edit-head">{{tr('profile')}}</h4>

                                    <div class="image-profile">
                                        @if($user->picture)
                                            <img src="{{$user->picture}}">
                                        @else
                                            <img src="{{asset('placeholder.png')}}">
                                        @endif                                
                                    </div><!--end of image-profile-->

                                    <div class="profile-title">
                                        <h3>{{$user->name}}</h3>
                                        
                                        @if($user->login_by == 'manual')
                                            <h4>{{$user->email}}</h4>
                                        @endif

                                       

                                        <p>{{$user->mobile}}</p>  

                                        <p>
                                        <?php 

                                        if (!empty($user->dob) && $user->dob != "0000-00-00") {

                                            $dob = date('d-m-Y', strtotime($user->dob));

                                        } else {

                                            $dob = "00-00-0000";
                                        }

                                        echo $dob;

                                        ?></p>

                                       
                                        <p>{{$user->description}}</p>

                                        

                                    </div><!--end of profile-title-->

                                    @if ($user->id == Auth::user()->id)

                                    <form>
                                    <br>
                                        <div class="change-pwd edit-pwd edit-pro-btn">

                                            <div class="clearfix"></div>

                                             <a href="{{route('user.subscriptions')}}" class="btn btn-warning">{{tr('subscriptions')}}</a>


                                            <a href="{{route('user.update.profile')}}" class="btn btn-primary">{{tr('edit_profile')}}</a>
                                            
                                            @if($user->login_by == 'manual')
                                                <a href="{{route('user.change.password')}}"
                                            class="btn btn-danger">{{tr('change_password')}}</a>

                                            @endif
                                        </div> 
                                    </form>  

                                    @endif                              
                                </div><!--end of sub-profile-->                            
                            </div><!--end of profile-details-->                           
                        </div><!--end of edit-profile-->
                    </div>
                     */ ?>
                    <!--profile-view end--> 

                    <!-- new ui -->
                    <div class="col-md-7 col-lg-7 profile-view">
                        <h4 class="mylist-head">Profile</h4>
                        <div class="new-profile-sec">
                            <div class="display-inline">
                                <div class="new-profile-left">
                                    @if(Auth::user()->picture)
                                        <img src="{{Auth::user()->picture}}">
                                    @else
                                        <img src="{{asset('placeholder.png')}}">
                                    @endif     
                                </div>
                                <div class="new-profile-right">
                                    <div class="profile-title">
                                        <h4><i class="fa fa-user"></i>{{Auth::user()->name}}</h4>
                                        
                                        @if(Auth::user()->login_by == 'manual')
                                            <h4><i class="fa fa-envelope"></i>{{Auth::user()->email}}</h4>
                                        @endif
                                       
                                        <h4><i class="fa fa-phone"></i>{{Auth::user()->mobile}}</h4>  

                                        <h4><i class="fa fa-calendar"></i>
                                        <?php 

                                        if (!empty(Auth::user()->dob) && Auth::user()->dob != "0000-00-00") {

                                            $dob = date('d-m-Y', strtotime(Auth::user()->dob));

                                        } else {

                                            $divob = "00-00-0000";
                                        }

                                        echo $dob;

                                        ?></h4>
                                        <h4 class="text-word-wrap"><i class="fa fa-file"></i>{{(Auth::user()->description)}}</h4>
                                        
                                        <div class="text-right">
                                            <a href="{{route('user.update.profile')}}" class="btn btn-info">{{tr('edit_profile')}}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- new ui -->

                    <?php // $wishlist = wishlist($user->id); ?>

                    @if ($user->id == Auth::user()->id)
                    
                    @if(count($wishlist->items) > 0)
                        
                    <div class="mylist-profile col-md-5 col-lg-5">
                        <h4 class="mylist-head">{{tr('wishlist')}}</h4>

                        <ul class="history-list profile-history">



                            @foreach($wishlist->items as $i => $video)

                                <li class="sub-list row no-margin">
                                    <div class="main-history">
                                        <div class="history-image">
                                            <a href="{{$video->url}}">
                                                <!-- <img src="{{$video->video_image}}"> -->
                                                <img src="{{asset('streamtube/images/placeholder.gif')}}" data-src="{{$video->video_image}}" class="placeholder" />
                                            </a>  
                                            @if($video->ppv_amount > 0)
                                                @if(!$video->ppv_status)
                                                    <div class="video_amount">

                                                    {{tr('pay')}} - {{Setting::get('currency')}}{{$video->ppv_amount}}

                                                    </div>
                                                @endif
                                            @endif
                                             <div class="video_duration">
                                                {{$video->duration}}
                                            </div>                      
                                        </div><!--history-image-->

                                        <div class="history-title">
                                            <div class="history-head row">
                                                <div class="cross-title1">
                                                    <h5><a href="{{$video->url}}">{{$video->title}}</a></h5>
                                                     <span class="video_views">
                                                        <div><a href="{{route('user.channel',$video->channel_id)}}">{{$video->channel_name}}</a></div>
                                                        <i class="fa fa-eye"></i> {{$video->watch_count}} {{tr('views')}} 
                                                        <b>.</b> 
                                                        {{ common_date($video->created_at) }}
                                                    </span>
                                                </div> 
                                                <div class="cross-mark1">
                                                    <a  onclick="return confirm(&quot;{{ substr($video->title, 0 , 15)}}.. {{tr('user_wishlist_delete_confirm') }}&quot;)"
                                                    href="{{route('user.delete.wishlist' , array('video_tape_id' => $video->video_tape_id))}}"><i class="fa fa-times" aria-hidden="true"></i></a>
                                                </div><!--end of cross-mark-->                       
                                            </div> <!--end of history-head--> 

                                        
                                             <span class="stars">
                                                <a><i @if($video->ratings >= 1) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                <a><i @if($video->ratings >= 2) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                <a><i @if($video->ratings >= 3) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                <a><i @if($video->ratings >= 4) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                <a><i @if($video->ratings >= 5) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                            </span>                                                  
                                        </div><!--end of history-title--> 
                                    </div><!--end of main-history-->
                                </li>

                            @endforeach

           
                        </ul>                                
                    
                    </div><!--end of mylist-profile-->

                    @endif

                    @endif

                </div><!--end of profile-content row-->
            
            </div>

            <div class="sidebar-back"></div> 
        </div>

    </div>
</div>

@endsection
