@extends('layouts.admin')

@section('title', tr('view_video'))

@section('content-header') 

{{tr('view_video')}} 

<a href="#" id="help-popover" class="btn btn-danger" style="font-size: 14px;font-weight: 600" title="{{tr('any_help')}}">{{tr('help')}}</a>


<div id="help-content" style="display: none">

    <ul class="popover-list">
        <li><b>{{tr('ads_view_revenue')}} - </b> {{tr('watch_revenue_count')}}&nbsp;{{tr('ad_status')}} </li>
        <li><b>{{tr('age_limit')}} - </b>{{tr('video_marked_age')}}</li>
        <li><b>{{tr('ppv_created_by')}} - </b>{{tr('admin_user')}}</li>
    </ul>
    
</div>

@endsection

@section('styles')

<style>
hr {
    margin-bottom: 10px;
    margin-top: 10px;
}

.text-ellipsis {

    white-space:nowrap;overflow: hidden;text-overflow: ellipsis;

}

.h4-header {
    background-color:#f7f7f7; font-size: 18px; text-align: center; padding: 7px 10px; margin-top: 0;
}
</style>

@endsection

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li><a href="{{route('admin.videos.list')}}"><i class="fa fa-video-camera"></i> {{tr('videos')}}</a></li>
    <li class="active">{{tr('video')}}</li>
@endsection 

@section('content')

@include('notification.notify')

<div class="row">
    <div class="col-md-4">
          <!-- Widget: user widget style 1 -->
          <div class="box box-widget widget-user">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header bg-yellow">
              <h3 class="widget-user-username text-ellipsis text-capitalize">{{$video->title}}</h3>
              
              <h5 class="widget-user-desc">{{$video->video_date}}</h5>
            </div>
            <div class="widget-user-image">
              <img class="img-circle" src="{{$video->default_image}}" alt="{{$video->title}}" style="height: 90px">  
            </div>
            <div class="box-footer">
              <div class="row">
                <div class="col-sm-4 border-right">
                  <div class="description-block">
                    <h5 class="description-header">{{number_format_short($video->getScopeUserFlags())}}</h5>
                    <span class="description-text">{{tr('spam_count')}}</span>
                  </div>
                  <!-- /.description-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-4 border-right">
                  <div class="description-block">
                    <h5 class="description-header">

                    {{number_format_short($video->getScopeUserRatings())}}</h5>
                    <span class="description-text">{{tr('reviews')}}</span>
                  </div>
                  <!-- /.description-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-4">
                  <div class="description-block">
                    <h5 class="description-header">{{Setting::get('currency')}}{{number_format_short($video->admin_amount + $video->user_amount+$video->amount)}}</h5>
                    <span class="description-text">{{tr('revenue')}}</span>
                  </div>
                  <!-- /.description-block -->
                </div>
                <!-- /.col -->
              </div>
              <hr>
              <ul class="nav nav-stacked">
                <li>
                    <a href="#">{{tr('is_ppv')}} 
                            <span class="pull-right badge bg-blue">
                                {{$video->ppv_amount > 0 ? tr('yes') : tr('no')}}
                            </span>
                    </a>
                </li>
                <li>
                    <a href="#">{{tr('publish_type')}} 
                        <span class="pull-right badge bg-aqua"> 
                            {{$video->publish_status ? tr('now') : tr('later')}}
                        </span>
                    </a>
                </li>
                <li>
                    <a href="#">{{tr('ad_status')}} 
                        <span class="pull-right badge bg-green">
                            {{($video->ad_status) ?  tr('yes') : tr('no') }}
                        </span>
                    </a>
                </li>
                <li><a href="#">{{tr('user_video_status')}} 
                    @if ($video->compress_status == 0) 

                        <span class="pull-right badge bg-red">{{tr('compress')}}</span> 
                    @else 

                        @if($video->status)

                            <span class="pull-right badge bg-green">{{tr('approved')}}</span>

                        @else

                            <span class="pull-right badge bg-red">{{tr('pending')}}</span>

                        @endif

                    @endif</a></li>
                    <li><a href="#">{{tr('admin_video_status')}} 
                        @if ($video->compress_status == 0) 

                            <span class="pull-right badge bg-red">{{tr('compress')}}</span> 
                        @else 

                            @if($video->is_approved)

                                <span class="pull-right badge bg-green">{{tr('approved')}}</span>

                            @else

                                <span class="pull-right badge bg-red">{{tr('pending')}}</span>

                            @endif

                        @endif</a></li>

                <li><a href="#">{{tr('views')}} <span class="pull-right badge bg-danger">{{number_format_short($video->watch_count)}}</span></a></li>

                <li><a href="#">{{tr('wishlist')}} <span class="pull-right badge bg-red">{{number_format_short($video->getUserWishlist())}}</span></a></li>

                
              </ul>

              @if ($video->compress_status) 

                <a href="{{route('admin.videos.edit' , array('id' => $video->video_tape_id))}}" class="btn btn-primary btn-block"><b>{{tr('edit')}}</b></a>

              @endif
              <!-- /.row -->
            </div>

          </div>
          <!-- /.widget-user -->

          <!-- Widget: user widget style 1 -->
          <div class="box box-widget widget-user-2">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header bg-red" style="padding: 2px 5px">
              <!-- /.widget-user-image -->
              <h4 class="">{{tr('ppv_revenue')}}</h4>
            </div>
            <div class="box-footer no-padding">
              <ul class="nav nav-stacked">
                <li><a href="#">{{tr('total')}} <span class="pull-right">{{Setting::get('currency')}} {{$video->admin_amount + $video->user_amount}}</span></a></li>
                <li><a href="#">{{tr('admin_amount')}} <span class="pull-right">{{Setting::get('currency')}} {{$video->admin_ppv_amount}}</span></a></li>
                <li><a href="#">{{tr('user_amount')}} <span class="pull-right">{{Setting::get('currency')}} {{$video->user_ppv_amount}}</span></a></li>
                
              </ul>
            </div>
          </div>
          <!-- /.widget-user -->
    </div>
    <div class="col-md-8">
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#video_details" data-toggle="tab">{{tr('video_details')}}</a></li>
          <li><a href="#spam_videos_list" data-toggle="tab">{{tr('spam_reports')}}</a></li>
          <li><a href="#reviews_list" data-toggle="tab">{{tr('reviews')}}</a></li>
          <li><a href="#wishlist_details" data-toggle="tab">{{tr('wishlists')}}</a></li>
        </ul>
        <div class="tab-content">
          <div class="active tab-pane" id="video_details">
                <h4 class="h4-header">
                    {{tr('video_details')}}
                </h4>
                <table class="table table-striped">
                    <tr>
                        <th>{{tr('title')}}</th>
                        <td>{{$video->title}}</td>
                    </tr>
                    <tr>
                        <th>{{tr('channel')}}</th>
                        <td>{{$video->channel_name}}</td>
                    </tr>
                    <tr>
                        <th>{{tr('category')}}</th>
                        <td>{{$video->category_name}}</td>
                    </tr>

                    <tr>
                        <th>{{tr('video_type')}}</th>
                        <td>
                            @if($video->video_type == VIDEO_TYPE_UPLOAD) 
                                        
                                {{tr('manual_upload')}}

                            @elseif($video->video_type == VIDEO_TYPE_YOUTUBE)

                                {{tr('youtube_links')}}

                            @else

                                {{tr('other_links')}}

                            @endif

                        </td>
                    </tr>

                    <tr>
                        <th>{{tr('tags')}}</th>
                        
                        <td>
                            @if($video_tags)
                                @foreach($video_tags as $k => $video_tag_details)

                                    @if($video_tag_details->name)

                                    <span class="label label-success"> {{$video_tag_details->name}} </span>&nbsp;

                                    @endif

                                @endforeach
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{tr('duration')}}</th>
                        <td>{{$video->duration}}</td>
                    </tr>
                    <tr>
                        <th>{{tr('age_limit')}}</th>
                        <td>{{$video->age_limit ? '18+' :  'All Users'}}</td>
                    </tr>
                    @if($video->publish_status != 1)
                    <tr>
                        <th>{{tr('publish_time')}}</th>
                        <td>{{$video->publish_time }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>{{tr('ratings')}}</th>
                        <td>
                            <span class="starRating-view">
                                <input id="rating5" type="radio" name="ratings" value="5" @if($video->ratings == 5) checked @endif>
                                <label for="rating5">5</label>

                                <input id="rating4" type="radio" name="ratings" value="4" @if($video->ratings == 4) checked @endif>
                                <label for="rating4">4</label>

                                <input id="rating3" type="radio" name="ratings" value="3" @if($video->ratings == 3) checked @endif>
                                <label for="rating3">3</label>

                                <input id="rating2" type="radio" name="ratings" value="2" @if($video->ratings == 2) checked @endif>
                                <label for="rating2">2</label>

                                <input id="rating1" type="radio" name="ratings" value="1" @if($video->ratings == 1) checked @endif>
                                <label for="rating1">1</label>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>{{tr('ads_view_revenue')}}</th>
                        <td>{{Setting::get('currency')}} {{number_format_short($video->amount)}}</td>
                    </tr>

                    <tr>
                        <th>{{tr('likes')}}</th>

                        <td>{{number_format_short($video->getScopeLikeCount())}}</td>
                    </tr>
                    <tr>
                        <th>{{tr('dislikes')}}</th>
                        <td>{{number_format_short($video->getScopeDisLikeCount())}}</td>
                    </tr>

                </table>
                <h4 class="h4-header">
                    {{tr('description')}}
                </h4>
                <p><?= $video->description ?></p>
                <h4 class="h4-header">
                    {{tr('reviews')}}
                </h4>
                <p><?= $video->reviews ?></p>
                @if(Setting::get('is_payper_view'))

                    @if($video->ppv_amount > 0)
                        <h4 class="h4-header">
                            {{tr('ppv_details')}}
                        </h4>
                        <table class="table table-striped">
                            <tr>
                                <th>{{tr('type_of_user')}}</th>
                                <td>
                                    @if($video->type_of_user == NORMAL_USER)
                                        {{tr('normal_user')}}
                                    @elseif($video->type_of_user == PAID_USER)
                                        {{tr('paid_user')}}
                                    @elseif($video->type_of_user == BOTH_USERS) 
                                        {{tr('both_user')}}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{tr('type_of_subscription')}}</th>
                                <td>
                                    @if($video->type_of_subscription == ONE_TIME_PAYMENT)
                                        {{tr('one_time_payment')}}
                                    @elseif($video->type_of_subscription == RECURRING_PAYMENT)
                                        {{tr('recurring_payment')}}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{tr('duration')}}</th>
                                <td>{{$video->duration}}</td>
                            </tr>
                            <tr>
                                <th>{{tr('amount')}}</th>
                                <td>{{Setting::get('currency')}} {{$video->ppv_amount}}</td>
                            </tr>
                        </table>
                    @endif
                @endif
          </div>
          <!-- /.tab-pane -->
          <div class="tab-pane" id="spam_videos_list">
                <blockquote>
                    <p>{{tr('spam_videos_notes')}}</p>
                    <small>{{tr('to_view_more')}} <cite><a href="{{route('admin.spam-videos.user-reports', $video->video_tape_id)}}" target="_blank">{{tr('click_here')}}</a></cite></small>
                </blockquote>

                @if($video->getScopeUserFlags())
                <table id="datatable-withoutpagination" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                          <th>{{tr('id')}}</th>
                          <th>{{tr('user_name')}}</th>
                          <th>{{tr('reason')}}</th>
                          <th>{{tr('date')}}</th>
                          <th>{{tr('action')}}</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($spam_reports as $i => $spam_report)

                            <tr>
                                <td>{{$i+1}}</td>
                                <td>{{$spam_report->user_name}}</td>
                                <td>{{$spam_report->reason}}</td>
                                <td>{{$spam_report->created_at->diffForHumans()}}</td>
                                <td>
                                    
                                    <a class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');" href="{{route('admin.spam-videos.unspam-video' , $spam_report->id)}}" title="Unspam Video"><i class="fa fa-trash"></i></a>
                                          
                                </td>
                            </tr>                   

                        @endforeach

                    </tbody>
                </table>  
                @endif
          </div>
          <!-- /.tab-pane -->

          <div class="tab-pane" id="reviews_list">
                <blockquote>
                    <p>{{tr('reviews_notes_list')}}</p>
                    <small>{{tr('to_view_more')}} <cite><a href="{{route('admin.reviews', array('video_tape_id'=>$video->video_tape_id))}}" target="_blank">{{tr('click_here')}}</a></cite></small>
                </blockquote>

                @if($video->getScopeUserRatings())

                <table id="datatable-withoutpagination" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                          <th>{{tr('id')}}</th>
                          <th>{{tr('user_name')}}</th>
                          <th>{{tr('comments')}}</th>
                          <th>{{tr('date')}}</th>
                          <th>{{tr('action')}}</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($reviews as $i => $review)

                            <tr>
                                <td>{{$i+1}}</td>
                                <td>{{$review->user_name}}</td>
                                <td>{{$review->comment}}</td>
                                <td>{{$review->created_at->diffForHumans()}}</td>
                                <td>
                                    
                                    <a class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');" href="{{route('admin.reviews.delete' ,['id'=>$review->id])}}" title="Unspam Video"><i class="fa fa-trash"></i></a>
                                          
                                </td>
                            </tr>                   

                        @endforeach

                    </tbody>
                </table>  

                @endif
          </div>
          <div class="tab-pane" id="wishlist_details">
                <blockquote>
                    <p>{{tr('wishlist_notes')}}</p>
                    <small>{{tr('to_view_more')}} <cite><a href="{{route('admin.videos.wishlist', array('video_tape_id'=>$video->video_tape_id))}}" target="_blank">{{tr('click_here')}}</a></cite></small>
                </blockquote>

                @if($video->getUserWishlist())

                <table id="datatable-withoutpagination" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                          <th>{{tr('id')}}</th>
                          <th>{{tr('user_name')}}</th>
                          <th>{{tr('date')}}</th>
                          <th>{{tr('action')}}</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($wishlists as $i => $wishlist)

                            <tr>
                                <td>{{$i+1}}</td>
                                <td>{{$wishlist->user_name}}</td>
                                <td>{{$wishlist->created_at->diffForHumans()}}</td>
                                <td>
                                    
                                    <a class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');" href="{{route('admin.users.wishlist.delete' , $wishlist->id)}}" title="Unspam Video"><i class="fa fa-trash"></i></a>
                                          
                                </td>
                            </tr>                   

                        @endforeach

                    </tbody>
                </table>  

                @endif
          </div>
          <!-- /.tab-pane -->
        </div>
        <!-- /.tab-content -->
      </div>
      <!-- /.nav-tabs-custom -->
    </div>
</div>

<?php $url = $trailer_url = ""; ?>

<div class="row">
    <div class="col-lg-12">
        <div class="box box-primary">
            <div class="box-header with-border btn-primary">
               
                <h3 class="box-title" style="color: white"> <b>{{tr('video_info')}}</b></h3>
                    
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              
                <div class="row">

                    <div class="col-lg-12">
                        <div class="col-lg-6">

                            <strong><i class="fa fa-video-camera margin-r-5"></i> {{tr('full_video')}}</strong>

                            <div class="clearfix"></div>

                            <br>

                            <!--  <b>{{tr('embed_link')}} : </b> <a href="{{route('embed_video', array('u_id'=>$video->unique_id))}}" target="_blank">{{route('embed_video', array('u_id'=>$video->unique_id))}}</a> -->

                            <div class="margin-t-10">
                                @if($video->video_upload_type == 1)
                                    <?php $url = $video->video; ?>
                                    <div id="main-video-player"></div>
                                @else
                                    @if(check_valid_url($video->video))

                                        <?php $url = (Setting::get('streaming_url')) ? Setting::get('streaming_url').get_video_end($video->video) : $video->video; ?>
                                        <div id="main-video-player"></div>
                                    @else
                                        <div class="image">
                                            <img src="{{asset('error.jpg')}}" alt="{{Setting::get('site_name')}}">
                                        </div>
                                    @endif

                                @endif
                            </div>
                            <div class="embed-responsive embed-responsive-16by9" id="flash_error_display" style="display: none;background: black;">
                                   <div style="width: 100%;color:#fff;height: 100%;padding-top: 25%;">
                                         <div style="text-align: center;align-items: center;">{{tr('flash_missing')}}<a target="_blank" href="http://get.adobe.com/flashplayer/" class="underline">{{tr('adobe')}}</a>.</div>
                                   </div>
                            </div>
                        </div>

                       <div class="col-lg-6">

                            <strong><i class="fa fa-picture-o margin-r-5"></i> {{tr('images')}}</strong>

                            <div class="clearfix"></div>

                            <br>

                            <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                                <ol class="carousel-indicators">
                                  <li data-target="#carousel-example-generic" data-slide-to="0" class=""></li>
                                  <li data-target="#carousel-example-generic" data-slide-to="1" class="active"></li>
                                  <li data-target="#carousel-example-generic" data-slide-to="2" class=""></li>
                                </ol>
                                <div class="carousel-inner">

                                    <div class="item active">
                                        <img src="{{($video->default_image) ? $video->default_image : ''}}" alt="Default Image">

                                        <!-- <div class="carousel-caption">
                                          Second Slide
                                        </div> -->
                                    </div>

                                    @foreach($video_images as $i => $image)
                                    <div class="item">
                                        <img src="{{$image->image}}" alt="Photo">
                                    </div>
                                    @endforeach
                                    
                                    @if ($video->is_banner)
                                    <div class="item">
                                        <img src="{{$video->banner_image}}" alt="Banner Image">

                                        <div class="carousel-caption">
                                          {{tr('banner_image')}}
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
                                  <span class="fa fa-angle-left"></span>
                                </a>
                                <a class="right carousel-control" href="#carousel-example-generic" data-slide="next">
                                  <span class="fa fa-angle-right"></span>
                                </a>
                              </div>

                        </div>
                        
                    </div>

                </div>

            <!-- /.box-body -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    
     <script src="{{asset('jwplayer/jwplayer.js')}}"></script>

    <script>jwplayer.key="{{Setting::get('JWPLAYER_KEY')}}";</script>

    <script type="text/javascript">
        
        jQuery(document).ready(function(){

                console.log('Inside Video');
                    
                console.log('Inside Video Player');

                @if($url)

                    var playerInstance = jwplayer("main-video-player");


                    @if($videoStreamUrl) 

                        playerInstance.setup({
                            file: "{{$videoStreamUrl}}",
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
                            // autostart : true,
                            "sharing": {
                                "sites": ["reddit","facebook","twitter"]
                              },

                           tracks : [{
                              file : "{{$video->subtitle}}",
                              kind : "captions",
                              default : true,
                            }]
                        });
                    @else 
                        var videoPath = "{{$videoPath}}";
                        var videoPixels = "{{$video_pixels}}";

                        var path = [];

                        var splitVideo = videoPath.split(',');

                        var splitVideoPixel = videoPixels.split(',');


                        for (var i = 0 ; i < splitVideo.length; i++) {
                            path.push({file : splitVideo[i], label : splitVideoPixel[i]});
                        }
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
                            // autostart : true,
                            "sharing": {
                                "sites": ["reddit","facebook","twitter"]
                              },

                               tracks : [{
                              file : "{{$video->subtitle}}",
                              kind : "captions",
                              default : true,
                            }]
                        });

                        


                    
                    @endif

                    playerInstance.on('setupError', function() {

                                jQuery("#main-video-player").css("display", "none");
                                
                               

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

                @endif

               
        });

    </script>

@endsection

