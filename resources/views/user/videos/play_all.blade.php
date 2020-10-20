@extends('layouts.user')
@section('meta_tags')
   <meta property="og:locale" content="en_US" />
   <meta property="og:type" content="article" />
   <meta property="og:title" content="{{$video->title}}" />
   <meta property="og:description" content="<?= $video->title ?>" />
   <meta property="og:url" content="" />
   <meta property="og:site_name" content="{{Setting::get('site_name') ?: tr('site_name')}}" />
   <meta property="og:image" content="{{$video->default_image}}" />
   <meta name="twitter:card" content="summary"/>
   <meta name="twitter:description" content="<?= $video->title ?>"/>
   <meta name="twitter:title" content="{{$video->title}}"/>
   <meta name="twitter:image:src" content="{{$video->default_image}}"/>
@endsection

@section('styles')
   <link rel="stylesheet" href="{{asset('assets/css/star-rating.css')}}">
   <link rel="stylesheet" href="{{asset('assets/css/toast.style.css')}}">
 
   <style type="text/css">
      
      .sub-comhead .rating-md {
         font-size: 11px;
      }
      .thumb-class {
         cursor:pointer;
         text-decoration:none;
      }
      .common-streamtube {
         min-height: 0px !important;
      }
      textarea[name=comments] {
         resize: none;
      }
      #timings {
         padding: 5px;
      }
      .ad_progress {
         position: absolute;
         bottom: 0px;
         width: 100%;
         opacity: 0.8;
         background: #000;
         color: #fff;
         font-size: 12px;
      }
      .progress-bar-div {
         width: 100%;
         height: 5px;
         background: #e0e0e0;
         /*padding: 3px;*/
         border-radius: 3px;
         box-shadow: inset 0 1px 3px rgba(0, 0, 0, .2);
      }
      .progress-bar-fill-div {
         display: block;
         height: 5px;
         background: #cc181e;
         border-radius: 3px;
         /*transition: width 250ms ease-in-out;*/
         /*transition : width 10s ease-in-out;*/
      }
      th {
         border-top: none;
      }

      [id='toggle-heart'] {
        position: absolute;
        left: -100vw;
      }


   </style>
@endsection

@section('content')

<div class="y-content">

   <div class="row y-content-row">
      
      @include('layouts.user.nav')
         
         <div class="page-inner col-sm-9 col-md-10 profile-edit">
            
            <div class="profile-content mar-0">
            
               @include('notification.notify')

               <div class="row no-margin">
                    
                  <div class="col-sm-12 col-md-8 play-video">
                     
                     <div class="single-video-sec">
                         @include('user.videos.streaming')
                     </div>
                     
                     <div class="main-content">

                        <div class="video-content">
                             
                           <div class="details">
                                 
                              <div class="video-title">
                                    
                                 <div class="title row">
                                         
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-lg-12 zero-padding">
                                       <h3>{{$video->title}}</h3>

                                       <div class="views pull-left">
                                          {{number_format_short($video->watch_count)}} {{tr('views')}}
                                       </div>
                     
                                       <div class="pull-right relative">
                                          @if (Auth::check())
                                          <a class="thumb-class" onclick="likeVideo({{$video->video_tape_id}})"><i id="like" class="material-icons like @if($like_status > 0)like_color @endif">thumb_up</i>&nbsp;<span id="like_count">{{number_format_short($like_count)}}</span></a>&nbsp;&nbsp;&nbsp;
                                          <a class="thumb-class" onclick="dislikeVideo({{$video->video_tape_id}})"><i id="dislike" class="material-icons ali-midd-20 dislike  @if($dislike_status > 0) dislike_color @endif">thumb_down</i>&nbsp;<span id="dislike_count">{{number_format_short($dislike_count)}}</span></a>
                                          @else 
                                          <a class="thumb-class" data-toggle="modal" data-target="#login_error"><i class="material-icons">thumb_up</i>&nbsp;<span>{{number_format_short($like_count)}}</span></a>&nbsp;&nbsp;&nbsp;
                                          <a class="thumb-class" data-toggle="modal" data-target="#login_error"><i class="material-icons ali-midd-20">thumb_down</i>&nbsp;<span>{{number_format_short($dislike_count)}}</span></a>
                                          @endif

                                          <a  class="share-new" data-toggle="modal" data-target="#popup1">
                                             <i class="material-icons">share</i>&nbsp;{{tr('share')}}
                                             <!--  <p class="hidden-xs">share</p> -->
                                          </a>                                            
                                          <a class="share-new global_playlist_id" id="{{$video->video_tape_id}}" href="#">
                                             <i class="material-icons">playlist_add</i>&nbsp;{{tr('save')}}&nbsp;&nbsp;
                                          </a>
                                          
                                          @if(Auth::check())

                                             <a class="thumb-class" onclick="wishlist_operations({{Auth::user()->id}},{{$video->video_tape_id}})"><i id="icon_color" class="fa fa-heart icon_color @if(count($wishlist_status) == 1 && $wishlist_status) wishlist-add @endif"></i>&nbsp;</a>

                                          @endif
                                          <!-- <form name="add_to_wishlist" method="post" id="add_to_wishlist" action="{{route('user.add.wishlist')}}" class="add-wishlist">
                                             @if(Auth::check())
                                             
                                                <input type="hidden" value="{{$video->video_tape_id}}" name="video_tape_id">
                                                
                                                @if(count($wishlist_status) == 1 && $wishlist_status)
                                                
                                                <input type="hidden" id="status" value="0" name="status">
                                                
                                                <input type="hidden" id="wishlist_id" value="{{$wishlist_status->id}}" name="wishlist_id">
                                                
                                                @if($flaggedVideo == '')
                                                   <div class="mylist">
                                                   <button  type="submit" id="added_wishlist" data-toggle="tooltip" title="{{tr('added_wishlist')}}">
                                                   <div class="added_to_wishlist" id="check_id">
                                                   <i class="fa fa-heart" style="color: #b31217"></i>
                                                   </div>
                                                   
                                                   <span class="wishlist_heart_remove">
                                                   <i class="fa fa-heart"></i>
                                                   </span>
                                                   </button> 
                                                   </div>
                                                @endif
                                                
                                                @else
                                                
                                                   <input type="hidden" id="status" value="1" name="status">
                                                   
                                                   <input type="hidden" id="wishlist_id" value="" name="wishlist_id">

                                                   @if($flaggedVideo == '')
                                                      <div class="mylist">
                                                      <button type="submit" id="added_wishlist" data-toggle="tooltip" title="{{tr('add_to_wishlist')}}">
                                                      <div class="add_to_wishlist" id="check_id">
                                                      <i class="fa fa-heart"></i>
                                                      </div>
                                                      
                                                      <span class="wishlist_heart">
                                                      <i class="fa fa-heart"></i>
                                                      </span>
                                                      </button> 
                                                      </div>
                                                   @endif

                                                @endif
                                             
                                             @endif
                                             
                                          </form> -->
                                       

                                       </div>
                                       <!--  <h3>Channel Name</h3> -->
                                       <div class="clearfix"></div>
                                       <!-- <h4 class="video-desc">{{$video->description}}</h4> -->
                                       <hr>
                                    </div>

                                    <div class="col-lg-12 col-md-12 col-sm-12 col-lg-12 top zero-padding ">
                                       
                                       <div class="row1">
                                           
                                          <div class="col-xs-12 col-md-7 col-sm-7 col-lg-7">
                                           
                                            <div class="username">
                                                <a href="{{route('user.channel',$video->channel_id)}}">
                                                    {{$video->channel_name}}
                                                </a>
                                            </div>

                                            <h5 class="rating no-margin mt-5">
                                                <span class="rating1"><i @if($video->ratings >= 1) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></span>
                                                <span class="rating1"><i @if($video->ratings >= 2) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></span>
                                                <span class="rating1"><i @if($video->ratings >= 3) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></span>
                                                <span class="rating1"><i @if($video->ratings >= 4) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></span>
                                                <span class="rating1"><i @if($video->ratings >= 5) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></span>
                                            </h5>

                                          </div>
                                          
                                          <div class="col-xs-12 col-md-5 col-sm-5 col-lg-5">
                                                     
                                             <div class="pull-right">
                                            
                                                @if(Auth::check())
                                                    
                                                   @if(Setting::get('is_spam') && Auth::user()->id != $video->channel_created_by)
                                                   
                                                      @if($flaggedVideo == '')

                                                            <button  type="button" class="btn btn-danger report-button bottom-space" title="{{tr('report')}}" data-toggle="modal" data-target="#report-form">
                                                                <i class="fa fa-flag"></i> 
                                                            </button>
                                                        
                                                        @else 
                                                            <a href="{{route('user.remove.report_video', $flaggedVideo->video_tape_id)}}" class="btn btn-info unmark bottom-space" title="{{tr('remove_report')}}">
                                                                <i class="fa fa-flag"></i> 
                                                            </a>
                                                        
                                                        @endif

                                                     @endif

                                                @endif
                                             </div>
                                             
                                             <div class="pull-right">
                                                
                                                @if(Auth::check())
                                                   
                                                   @if($video->get_channel->user_id != Auth::user()->id)
                                                      <a id="subscription" class="btn btn-sm bottom-space btn-info text-uppercase subscription @if($subscribe_status) subscription_button @endif" onclick="channels_unsubscribe_subscribe({{Auth::user()->id}},{{$video->channel_id}})"><span id="subscription_text">
                                                      @if($subscribe_status) {{tr('un_subscribe')}} @else {{tr('subscribe')}} @endif &nbsp; </span><span id="subscriberscnt">{{$subscriberscnt}}</span></a>
                                                      
                                                      <!-- @if(!$subscribe_status)
                                                         
                                                         <a class="btn btn-sm bottom-space btn-info text-uppercase" onclick="subscribe({{Auth::user()->id}},{{$video->channel_id}})">{{tr('subscribe')}} &nbsp; {{$subscriberscnt}}</a>
                                                      
                                                      @else 
                                                         
                                                         <a class="btn btn-sm bottom-space btn-danger text-uppercase" onclick="un_subscribe({{$subscribe_status}})" style="background: rgb(229, 45, 39) !important">{{tr('un_subscribe')}} &nbsp; {{$subscriberscnt}}</a>

                                                      @endif -->

                                                   @else
                                                      
                                                      <a class="btn btn-sm bottom-space btn-danger text-uppercase" href="{{route('user.channel.subscribers', array('channel_id'=>$video->channel_id))}}" style="background: rgb(229, 45, 39) !important"><i class="fa fa-users"></i>&nbsp; {{tr('subscribers')}} - {{$subscriberscnt}}</a>

                                                   @endif
                                                
                                                @endif
                                            
                                            </div>

                                          </div>

                                       </div>

                                       <div class="clearfix"></div>

                                    </div>

                                    <div class="clearfix"></div>

                                    <div>

                                       <h4 class="video-desc"><?= $video->description?></h4>
                                   
                                       <div class="tag-and-category">
                                           
                                           <div class="row m-0">
                                             <div class="col-lg-3 col-md-3 col-sm-4 col-xs-4 p-0 mt-10">
                                                <p class="category-name" style="float: none !important;font-size: 15px !important;">category</p>
                                             </div>
                                             <div class="col-lg-9 col-md-9 col-sm-8 col-xs-8 p-0 mt-10">
                                                <a href="{{route('user.categories.view', $video->category_unique_id)}}" target="_blank" class="category-name blue-link">{{$video->category_name}}</a>
                                             </div>
                                           </div>
                                           
                                           @if(count($tags) > 0)
                                                <div class="row m-0">
                                                   <div class="col-lg-3 col-md-3 col-sm-4 col-xs-4 p-0 mt-10">
                                                      <p class="category-name" style="float: none !important;font-size: 15px !important;">{{tr('tags')}}</p>
                                                   </div>
                                                   <div class="col-lg-9 col-md-9 col-sm-8 col-xs-8 p-0 mt-10">
                                                   <?php 
                                                       $tags_list = [];
                                                       
                                                       foreach($tags as $i => $tag) {
                                                       
                                                         $tags_list[] = '<a href="'.route('user.tags.videos', array('id'=>$tag->tag_id)).'" target="_blank" class="category-name blue-link">'.$tag->tag_name.'</a>';
                                                       
                                                       }
                                                       
                                                   ?>
                                                   <?= $tags_list ? implode(', ', $tags_list) : '' ?>
                                                   </div>
                                               </div>
                                           
                                           @endif
                                       </div>
                                   
                                    </div>

                                    <div class="clearfix"></div>

                                    @if(Setting::get('is_spam'))

                                       @if (!$flaggedVideo)
                                          
                                          <div class="more-content" style="display: none;" id="report_video_form">
                                             <form name="report_video" method="post" id="report_video" action="{{route('user.add.spam_video')}}">
                                                <b>{{tr('report_this_video')}}</b>
                                                <br>
                                               
                                                @foreach($report_video as $report) 
                                                <div class="report_list">
                                                  <label class="radio1">
                                                     <input id="radio1" type="radio" name="reason" checked="" value="{{$report->value}}" required>
                                                     <span class="outer"><span class="inner"></span></span>{{$report->value}}
                                                  </label>
                                                </div>
                                                <!-- <div class="clearfix"></div> -->
                                                @endforeach

                                                <input type="hidden" name="video_tape_id" value="{{$video->video_tape_id}}" />
                                                <p class="help-block"><small>If you report this video, you won't see again the same video in anywhere in your account except "Spam Videos". If you want to continue to report this video as same. Click continue and proceed the same.</small></p>
                                                <div class="pull-right">
                                                   <button class="btn btn-info btn-sm">{{tr('submit')}}</button>
                                                </div>
                                                <div class="clearfix"></div>
                                          
                                             </form>
                                           
                                          </div>
                                       
                                       @endif

                                    @endif

                                 </div>

                                 <div class="hr-class"><hr></div>

                                 <div class="clearfix"></div>

                              </div>

                              <!--end of video-title-->                                                             
                           </div>

                           <!--end of details-->

                           @include('user.videos._comments')

                        </div>
               
                     </div>

                     <!--end of main-content-->

                  </div>
               
                  <!--end of col-sm-8 and play-video-->
                  
                  <div class="col-sm-12 col-md-4 side-video custom-side">

                     <div class="up-next pt-0">

                        <h4 class="sugg-head1">{{tr('playlists')}}</h4>

                        <ul class="video-sugg">
                        
                          @if(count($play_all->video_tapes) > 0)

                              @include('user.videos._playlist')

                              <span id="playlists_videos"></span>

                              <div class="clearfix"></div>

                              <div class="row" style="margin-top: 20px">

                              <div id="playlist_video_content_loader" style="display: none;">

                              <h1 class="text-center"><i class="fa fa-spinner fa-spin" style="color:#ff0000"></i></h1>

                                  </div>

                                  <div class="clearfix"></div>

                                  <button class="pull-right btn btn-info mb-15" onclick="getPlaylistsList()" style="color: #fff">{{tr('view_more')}}</button>

                                  <div class="clearfix"></div>

                             </div>
                          @endif
                        </ul>
                     </div>
                      
                  </div>

                  <!--end of col-sm-4-->
               </div>
            
            </div>
         
         <div class="sidebar-back"></div>
      
      </div>
   
   </div>
   <!--y-content-row-->
</div>

<?php
   $ads_timing = $video_timings = [];
   
   if(count($ads) > 0 && $ads != null) {
   
       foreach ($ads->between_ad as $key => $obj) {
   
           $video_timings[] = $obj->video_time;
   
           $ads_timing[] = $obj->ad_time;
   
       }
   }
   
   ?>

<!-- MODALS SECTION -->

@include('user.videos._modals')

<!-- MODALS SECTION -->

@endsection

@section('scripts')

<script type="text/javascript" src="{{asset('assets/js/star-rating.js')}}"></script>

<script type="text/javascript" src="{{asset('assets/js/toast.script.js')}}"></script>

<script src="{{asset('jwplayer/jwplayer.js')}}"></script>

<script type="text/javascript">

   // <!-- wishlist animation -->

   $(document).ready(function() {
          $(".heart").on('click touchstart', function(){
              $(this).toggleClass('is_animating');
          });
   
         $(".heart").on('animationend', function(){
             $(this).toggleClass('is_animating');
         });
   });

   // <!-- wishlist animation -->

   jwplayer.key="{{Setting::get('JWPLAYER_KEY')}}";
   
   $(document).ready(function(){
      
      $('.video-y-menu').addClass('hidden');
   }); 
   
   function showReportForm() {
   
       var divId = document.getElementById('report_video_form').style.display;
   
       if (divId == 'none') {
   
           $('#report_video_form').show(500);
   
       } else {
   
           $('#report_video_form').hide(500);
   
       }
   
   }
   
   $('.view_rating').rating({disabled: true, showClear: false});
   
   $('.comment_rating').rating({showClear: false});
   
   $(document).on('ready', function() {

      $("#copy-embed1").on( "click", function() {
           $('#popup1').modal('hide'); 
       });

      $('.global_playlist_id').on('click', function(event){

         event.preventDefault();

         var video_tape_id = $(this).attr('id');

         $('#global_playlist_id_'+video_tape_id).modal('show'); 

      });

   });
   
   jQuery(document).ready(function(){ 

      
       // Opera 8.0+
       var isOpera = (!!window.opr && !!opr.addons) || !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
       // Firefox 1.0+
       var isFirefox = typeof InstallTrigger !== 'undefined';
       // At least Safari 3+: "[object HTMLElementConstructor]"
       var isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;
       // Internet Explorer 6-11
       var isIE = /*@cc_on!@*/false || !!document.documentMode;
       // Edge 20+
       var isEdge = !isIE && !!window.StyleMedia;
       // Chrome 1+
       var isChrome = !!window.chrome && !!window.chrome.webstore;
       // Blink engine detection
       var isBlink = (isChrome || isOpera) && !!window.CSS;
   
   
       //hang on event of form with id=myform
       jQuery("form[name='add_to_wishlist']").submit(function(e) {
   
           //prevent Default functionality
           e.preventDefault();
   
           //get the action-url of the form
           var actionurl = e.currentTarget.action;
   
           //do your own request an handle the results
           jQuery.ajax({
               url: actionurl,
               type: 'post',
               dataType: 'json',
               data: jQuery("#add_to_wishlist").serialize(),
               success: function(data) {
                   if(data.success == true) {
   
                       jQuery("#added_wishlist").html("");
   
                       /*var display_style = document.getElementById('check_id').style.display;
   
                       alert(display_style);*/
   
                       if(data.status == 1) {
   
                           jQuery('#status').val("0");
   
                           jQuery('#wishlist_id').val(data.wishlist_id); 
                           jQuery("#added_wishlist").css({'font-family':'arial','background-color':'transparent','color' : '#b31217'});
   
                           if (jQuery(window).width() > 640) {
                           var append = '<i class="fa fa-heart">';
                           // var append = '<i class="fa fa-times-circle">&nbsp;&nbsp;{{tr('wishlist')}}';
                           } else {
                           var append = '<i class="fa fa-heart">';
                           }
                           jQuery("#added_wishlist").append(append);
   
                       } else {
   
                           jQuery('#status').val("1");
                           jQuery('#wishlist_id').val("");
                           jQuery("#added_wishlist").css({'font-family':'arial','background':'','color' : ''});
                           if (jQuery(window).width() > 640) {
                           var append = '<i class="fa fa-heart">';
                           // var append = '<i class="fa fa-plus-circle">&nbsp;&nbsp;{{tr('wishlist')}}';
                           } else {
                           var append = '<i class="fa fa-heart">';
                           }
   
                           jQuery("#added_wishlist").append(append);
   
                       }
   
                  } else {
   
                       // console.log('Wrong...!');
   
                  }
               }
           });
   
       });
   
       $('#comment').keydown(function(event) {
           if (event.keyCode == 13) {
               $(this.form).submit()
               return false;
           }
       }).focus(function(){
           if(this.value == "Write your comment here..."){
               this.value = "";
           }
       }).blur(function(){
           if(this.value==""){
               this.value = "";
           }
       });
   
       jQuery("form[name='comment_sent']").submit(function(e) {
   
           //prevent Default functionality
           e.preventDefault();
   
   
           //get the action-url of the form
           var actionurl = e.currentTarget.action;
   
           var form_data = $.trim(jQuery("#comment").val());
   
           if(form_data) {
   
               $("#comment_btn").html("Sending...");
   
               $("#comment_btn").attr('disabled', true);
   
   
               //do your own request an handle the results
               jQuery.ajax({
                   url: actionurl,
                   type: 'post',
                   dataType: 'json',
                   data: jQuery("#comment_sent").serialize(),
                   success: function(data) {
   
                       $("#comment_btn").html("Comment");
   
                       $("#comment_btn").attr('disabled', false);
   
                       if(data.success == true) {
   
                           @if(Auth::check())
                               jQuery('#comment').val("");
                               jQuery('#no_comment').hide();
                               var comment_count = 0;
                               var count = 0;
                               comment_count = jQuery('#comment_count').text();
                               var count = parseInt(comment_count) + 1;
                               jQuery('#comment_count').text(count);
                               jQuery('#video_comment_count').text(count);
   
                               // var stars = 0;
   
                               var first_star = data.comment.rating >= 1 ? "color:#ff0000" : "";
   
                               var second_star = data.comment.rating >= 2 ? "color:#ff0000" : "";
   
                               var third_star = data.comment.rating >= 3 ? "color:#ff0000" : "";
   
                               var fourth_star = data.comment.rating >= 4 ? "color:#ff0000" : "";
   
                               var fifth_star = data.comment.rating >= 5 ? "color:#ff0000" : "";
   
                               var stars = '<span class="stars">'+
                               '<a><i style="'+first_star+'" class="fa fa-star-o comment-stars" aria-hidden="true"></i></a>'+
                               '<a><i style="'+second_star+'" class="fa fa-star-o comment-stars" aria-hidden="true"></i></a>'+
                               '<a><i style="'+third_star+'" class="fa fa-star-o comment-stars" aria-hidden="true"></i></a>'+
                               '<a><i style="'+fourth_star+'" class="fa fa-star-o comment-stars" aria-hidden="true"></i></a>'+
                               '<a><i style="'+fifth_star+'" class="fa fa-star-o comment-stars" aria-hidden="true"></i></a></span>';   
   
                               /**
                               <p><input id="view_rating" name="rating" type="number" class="rating view_rating" min="1" max="5" step="1" value="'+data.comment.rating+'"></p>
                               **/
   
                               if (data.comment.rating > 1) {
   
                               $('.comment_rating').rating('clear');
   
                               window.location.reload();
   
                               }
   
                               jQuery('#new-comment').prepend('<div class="display-com"><div class="com-image"><img style="width:48px;height:48px;  border-radius:24px;" src="{{Auth::user()->picture}}"></div><div class="display-comhead"><span class="sub-comhead"><a><h5 style="float:left">{{Auth::user()->name}}</h5></a><a><p>'+data.date+'</p></a><p>'+stars+'</p><p class="com-para">'+data.comment.comment+'</p></span></div></div>');
                           @endif
                       } else {
                           // console.log('Wrong...!');
                       }
                   }
               });
           } else {
   
               alert("Please fill the comment field");
   
               return false;
   
           }
   
       });
   
       var playerInstance = jwplayer("main-video-player");  
   
   
       var path = [];
   
       @if($videoStreamUrl) 
   
           path.push({file : "{{$videoStreamUrl}}", label : "Original"});
   
           path.push({file : "{{$video->video}}", label : "Original"});
   
       @else
   
           if(jQuery.browser.mobile) {
   
               $('#mainVideo').show();
   
               // console.log('You are using a mobile device!');
   
               path.push({file : "{{$hls_video}}", label : "Original"});
   
           } else {
   
            @if(count($videoPath) > 0 && $videoPath != '')
   
               @foreach($videoPath as $path)
   
                   path.push({file : "{{$path->file}}", label : "{{$path->label}}"});
   
               @endforeach
   
               @endif
   
           }
   
       @endif
   
       var pre_ad_status = 1;
   
       var post_ad_status = 1;
   
       var between_ad_status = 0;
   
       var OnPlayStatus = 0;
   
       playerInstance.setup({
   
           sources: path,
           image: "{{$video->default_image}}",
           width: "100%",
           aspectratio: "16:9",
           primary: "flash",
           controls : true,
           "controlbar.idlehide" : false,
           controlBarMode:'floating',
           "controls": {
           "enableFullscreen": false,
           "enablePlay": false,
           "enablePause": false,
           "enableMute": true,
           "enableVolume": true
           },
           autostart : true,
           "sharing": {
            "sites": ["facebook","twitter"]
           },
           events : {
   
               onReady : function(event) {
   
                   console.log("onready");
   
               },
   
               onTime:function(event) {
   
                   // Between Ad Play
   
                   var video_time = Math.round(playerInstance.getPosition());
   
                   @if($ads)
   
                       @if(count($ads->between_ad) > 0)
   
                           @foreach($ads->between_ad as $i => $obj) 
   
                               var video_timing = "{{$obj->video_time}}";
   
                               // console.log("Video Timing "+video_timing);
   
                               var a = video_timing.split(':'); // split it at the colons
   
                               if (a.length == 3) {
                                   var seconds = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2]);
                               } else {
                                   var seconds = parseInt(a[0]) * 60 + parseInt(a[1]);
                               }


                               // If the user again clicked in between seconds, it wil check whether ad is present or not. if it is enable the ad
                               if (video_time < seconds) {

                                  between_ad_status = 0;

                               }
   
                               // console.log("Seconds "+seconds);
   
                               if (video_time == seconds && between_ad_status != video_time) {
   
                                   between_ad_status = video_time;
   
                                   jwplayer().pause();
   
                                   stop();
   
                                   $("#ad_image").attr("src","{{$obj->assigned_ad->file}}");
   
                                   $("#main-video-player").css({'visibility':'hidden', 'width' : '0%'});
   
                                   $('#main_video_ad').show();
   
                                   @if($obj->assigned_ad->ad_url)
   
                                       $('.click_here_ad').html("<a target='_blank' href='{{$obj->assigned_ad->ad_url}}'>{{tr('click_here_url')}}</a>");
   
                                       $('.click_here_ad').show();
   
                                   @endif
   
   
                                   adsPage("{{$obj->ad_time}}");
   
                               }


                           @endforeach
   
                       @endif
                
   
                   @endif
               },
   
               onBeforePlay : function(event) {
   
   
               },
               onPlay : function(event) {
   
                   // between_ad_status = 0;
   
               },
   
               onComplete : function(event) {
   
                   console.log("onComplete Fn");

                   between_ad_status = 0;
   
                   @if(Auth::check())
   
                       jQuery.ajax({
                           url: "{{route('user.add.history')}}",
                           type: 'post',
                           data: {'video_tape_id' : "{{$video->video_tape_id}}"},
                           success: function(data) {
                               if(data.success == true) {
   
                                   if (data.navigateback) {
   
                                       window.location.reload(true);
   
                                   }

                                    var url = "{{route('user.playlists.play_all' , ['playlist_id'=>$play_all->playlist_id,'playlist_type'=>$play_all->playlist_type,'play_next' => $play_next])}}";

                                    var urlString = url.replace(/&amp;/g, '&');
                 
                                    window.location.assign(urlString);
   
                               } else {
                                      
                               }
                           }
                       });
                       
                   @endif
   
                   // For post ad, once video completed the ad will execute
   
                   if (post_ad_status) {
   
                       @if($ads)
   
                       @if($ads->post_ad)
   
                           $("#ad_image").attr("src","{{$ads->post_ad->assigned_ad->file}}");
   
                           $("#main-video-player").css({'visibility':'hidden', 'width' : '0%'});
   
                           $('#main_video_ad').show();
   
                           @if($ads->post_ad->assigned_ad->ad_url)
   
                               $('.click_here_ad').html("<a target='_blank' href='{{$ads->post_ad->assigned_ad->ad_url}}'>{{tr('click_here_url')}}</a>");
   
                               $('.click_here_ad').show();
   
                           @endif
   
                           stop();
   
                           post_ad_status = 0;
   
                           adsPage("{{$ads->post_ad->ad_time}}");
                           
                       @endif
   
                       @endif
   
                   }
   
               }
   
           },
   
           tracks : [{
               file : "{{$video->subtitle ? $video->subtitle : ''}}",
               kind : "captions",
               default : true,
           }],
   
       });
   
       // For Pre Ad , Every first frame the ad will execute
   
       playerInstance.on('firstFrame', function() {
   
           console.log("firstFrame");
   
           post_ad_status = 1;
   
          // OnPlayStatus += 1;
   
           // if (pre_ad_status) {
   
               @if($ads)
   
                   @if($ads->pre_ad)
   
                       $("#ad_image").attr("src","{{$ads->pre_ad->assigned_ad->file}}");
   
                       $("#main-video-player").css({'visibility':'hidden', 'width' : '0%'});
   
                       $('#main_video_ad').show();
   
                       @if($ads->pre_ad->assigned_ad->ad_url)
   
                           $('.click_here_ad').html("<a target='_blank' href='{{$ads->pre_ad->assigned_ad->ad_url}}'>{{tr('click_here_url')}}</a>");
   
                           $(".click_here_ad").show();
   
                       @endif
   
                       jwplayer().pause();
   
                       pre_ad_status = 0;
   
                       adsPage("{{$ads->pre_ad->ad_time}}");
   
                   @endif
   
               @endif
   
           // }
   
       });
   
       playerInstance.on('setupError', function() {
   
           jQuery("#main-video-player").css("display", "none");
           jQuery('#trailer_video_setup_error').hide();
   
   
           var hasFlash = false;
           try {
               var fo = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
               if (fo) {
                   hasFlash = true;
               }
           } catch (e) {
               if (navigator.mimeTypes
               && navigator.mimeTypes['application/x-shockwave-flash'] != undefined
               && navigator.mimeTypes['application/x-shockwave-flash'].enabledPlugin) {
                   hasFlash = true;
               }
           }
   
           if (hasFlash == false) {
               jQuery('#flash_error_display').show();
               return false;
           }
   
           jQuery('#main_video_setup_error').css("display", "block");
   
           confirm('The video format is not supported in this browser. Please option some other browser.');
   
       });
   
   
       jQuery("#main-video-player").show();
   
       // console.log(jwplayer().getPosition());
   
       var intervalId;
   
       var timings = "{{($ads) ? count($ads->between_ad) : 0}}";
   
       var time = 0;
   
       function timer(){
   
           intervalId = setInterval(function(){
   
               //
   
           }, 1000);
   
       }
   
       function stop(){
           clearInterval(intervalId);
       }
   
   
       var adCount = 0;
   
       function adsPage(adtimings){
   
           // alert("timings..!");
   
           $(".seconds").html(adtimings+" sec");
   
           $("#progress").html('<div class="progress-bar-div">'+
           '<span class="progress-bar-fill-div" style="width: 0%"></span>'+
           '</div>');
   
           $(".progress-bar-fill-div").css('transition', 'width '+adtimings+'s ease-in-out');
   
           $('.progress-bar-fill-div').delay(1000).queue(function () {
   
               // console.log("playig");
   
               $(this).css('width', '100%');
   
           });
   
   
           intervalId = setInterval(function(){
   
               adCount += 1;
   
               $(".seconds").html((adtimings - adCount) +" sec");
   
               // console.log("Ad Count " +adCount);
   
               // console.log("Ad Timings "+adtimings);
   
               if (adCount == adtimings) {
   
                   $(this).css('width', '100%')
   
                   adCount = 0;
   
                   stop();
   
                   $(".click_here_ad").hide();
   
                   $("#ad_image").attr("src", "");
   
                   $('#main_video_ad').hide();
   
                   $("#main-video-player").css({'visibility':'visible', 'width' : '100%'});
   
                   if (playerInstance.getState() != "complete") {
   
                       jwplayer().play();
   
                      // timer();
   
                   }
   
               }
   
           }, 1000);
   
       }
   
   });
   
   function copyTextToClipboard() {
   
      $("#embed_link_url").select();
   
      try {
   
         var successful = document.execCommand( 'copy' );
   
         var msg = successful ? 'successful' : 'unsuccessful';
   
         // console.log('Copying text command was ' + msg);
   
         addToast();
         // alert('Copied Embedded Link');
      } catch (err) {
           // console.log('Oops, unable to copy');
      }
   }
   
   // To add/remove video from playlist
   function playlist_video_update(video_tape_id, playlist_id,playlist_checkbox) {
      // id of clicked playlist   
      var playlist_checkbox_id = playlist_checkbox.id;
     
      var playlist_ids = $("input[name='playlist_ids[]']").val();
     
      // var playlist_ids = document.getElementsByName("playlist_ids[]").value();
      // alert(playlist_ids);
      // alert(JSON.stringify(playlist_ids));
     
      // playlist_ids.push("playlist_id");

      var status;

      if($('#'+playlist_checkbox_id).prop("checked") == true) {

         var status = 1;
      } 
 
      else if($('#'+playlist_checkbox_id).prop("checked") == false) {

         var status = 0;
      }

      $.ajax({
         url : "{{route('user.playlist.video.update')}}",
         data : {video_tape_id : video_tape_id, playlist_id : playlist_id, status : status, playlist_ids : playlist_ids},
         type: "POST",
         success : function(data) {
            if (data.success) {
               
               alert(data.message);
            } else {

               console.log(data.error_messages);
            }

         },

         error : function(data) {
   
         },

      })
   } 

   function playlist_save_video_add(video_tape_id) {
      
      var title = $("#playlist_title" ).val();

      var privacy = $("#playlist_privacy" ).val();

      if(title == '') { 

         alert("Title for playlist required");

      } else {

         $.ajax({
               
               url : "{{route('user.playlist.save.video_add')}}",
               data : {title : title , video_tape_id : video_tape_id, privacy : privacy, },
               type: "post",
               success : function(data) {
               
                  if (data.success) {

                     $('#playlist_title').removeAttr('value');  

                     $('#create_playlist_form').hide();

                     alert(data.message);

                     var labal = '<label class="playlist-container">'+data.title+'<input type="checkbox" onclick="playlist_video_update('+video_tape_id+ ', '+data.playlist_id+ ',this)" id="playlist_'+data.playlist_id+'" checked><span class="playlist-checkmark"></span></label>';

                     $('#user_playlists').append(labal);

                  } else {
                     
                     alert(data.error_messages);
                  }
                  
               },
      
               error : function(data) {
               },
         })
      }
   }  

   /**
     * @function channels_unsubscribe_subscribe() 
     *
     * @uses used to update the subscribe status
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     */ 
   function channels_unsubscribe_subscribe(user_id,channel_id) {
    
      $.ajax({
           url : "{{route('user.channels_unsubscribe_subscribe_ajax.channel')}}",
           data : {id : user_id, channel_id : channel_id},
           type: "get",

           success: function(data) {
               
               if(data.is_user_subscribed_the_channel) {
                  $(".subscription").addClass("subscription_button");
                  $("#subscription_text").html('Un Subscribe&nbsp;');
                  $("#subscriberscnt").html(data.subscription_count);
                  
               } else {
                  $(".subscription").removeClass("subscription_button");
                  $("#subscription_text").html('Subscribe&nbsp;');
                  $("#subscriberscnt").html(data.subscription_count);

               }
           },
   
           error : function(data) {
           },
       })
   }

   /**
     * @function wishlist_operations() 
     *
     * @uses Add / Remove  Wishlist
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     */
   function wishlist_operations(user_id,video_tape_id) {
    
      $.ajax({
           url : "{{route('user.wishlist_operations_ajax')}}",
           data : {id : user_id,video_tape_id : video_tape_id},
           type: "get",

           success: function(data) {

               if(data.data && data.data.wishlist_status) {
                 
                 $(".icon_color").addClass("wishlist-add");
                  
               } else {
                  
                  $(".icon_color").removeClass("wishlist-add");

               }
           },
   
           error : function(data) {
           },
       })
   }

   /**
     * @function likeVideo() 
     *
     * @uses Videos Like and count based on the Likes
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     */
   function likeVideo(video_id) {
      $(".dislike").removeClass("dislike_color");
      $.ajax({
           url : "{{route('user.video.like')}}",
           data : {video_tape_id : video_id},
           type: "post",
           success : function(data) {
               
               if(data.success && data.like_status) {
               
                  $(".like").removeClass("like_color");
                  
               } else {

                  $(".like").addClass("like_color"); 

               }

               if (data.success) {
   
                  $("#like_count").html(data.like_count);
   
                  $("#dislike_count").html(data.dislike_count);
   
               } else {
   
                   // console.log(data.error_messages);
   
               }
           },
   
           error : function(data) {
           },
       })
   }
   
    /**
     * @function dislikeVideo() 
     *
     * @uses Videos disLike and count based on the disLikes
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     */   
     function dislikeVideo(video_id) {
      $(".like").removeClass("like_color");
       $.ajax({
           url : "{{route('user.video.disLike')}}",
           type: "post",
           data : {video_tape_id : video_id},
           success : function(data) {
               if(data.success && data.dislike_status) {

                  $(".dislike").removeClass("dislike_color");

               } else {

                  $(".dislike").addClass("dislike_color"); 

               }

               if(data.success) {
   
                   $("#like_count").html(data.like_count);
         
                   $("#dislike_count").html(data.dislike_count);

               } else {
   
                   // console.log(data.error_messages);
   
               }
           },
   
           error : function(data) {
           },
       })
   }
   
   function addToast() {
       $.Toast("Embedded Link", "Link Copied Successfully.", "success", {
           has_icon:false,
           has_close_btn:true,
           stack: false,
           fullscreen:true,
           timeout:1000,
           sticky:false,
           has_progress:true,
           rtl:false,
       });
   }


   var stopPageScroll = false;

   var searchDataLength = "{{count($play_all->video_tapes)}}";

   function getPlaylistsList() {

     if (searchDataLength > 0) {

         playlists_videos(searchDataLength);

     }

   }

   function playlists_videos(cnt) {

        $.ajax({

            type: "post",
            async: false,
            url: "{{route('user.playlists.play_all')}}",
            data: {
                skip: cnt,
                playlist_id: "{{$play_all->playlist_id}}",
                playlist_type: "{{$play_all->playlist_type}}",
                play_next: "{{$play_next}}",
                is_json: 1
            },
            
            beforeSend: function() {

                $("#playlist_video_content_loader").fadeIn();
            },

            success: function(response) {
               
               $('#playlists_videos').append(response.view);

                if (response.count == 0) {

                    stopPageScroll = true;

                } else {

                    stopPageScroll = false;

                    searchDataLength = parseInt(searchDataLength) + response.count;

                }

            },

            complete: function() {
               console.log('complete');
                $("#playlist_video_content_loader").fadeOut();

            },

            error: function(data) {

            },

        });

    }
</script>
@endsection