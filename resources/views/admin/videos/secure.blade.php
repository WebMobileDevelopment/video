@extends('layouts.admin')

@section('title', tr('view_video'))

@section('content-header') 

{{tr('view_video')}} 

<a href="#" id="help-popover" class="btn btn-danger" style="font-size: 14px;font-weight: 600" title="{{tr('any_help')}}">{{tr('help')}}</a>


<div id="help-content" style="display: none">

    <ul class="popover-list">
        <li><b>{{tr('ads_view_revenue')}} - </b> {{tr('watch_count_revenue')}} {{tr('ad_status')}} </li>
        <li><b>{{tr('age_limit')}} - </b>{{tr('video_marked_only_18_years')}}</li>
        <li><b>{{tr('ppv_created_by')}} - </b> {{tr('admin_or_user')}} </li>

    </ul>
    
</div>

@endsection

@section('styles')

<style>
hr {
    margin-bottom: 10px;
    margin-top: 10px;
}
</style>

@endsection

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li><a href="{{route('admin.videos')}}"><i class="fa fa-video-camera"></i> {{tr('videos')}}</a></li>
    <li class="active">{{tr('video')}}</li>
@endsection 

@section('content')

    <?php $url = $trailer_url = ""; ?>

    <div class="row">

        @include('notification.notify')
        <div class="col-lg-12">
            <div class="box box-primary">
            <div class="box-header with-border btn-primary">
                <div class='pull-left'>
                    <h3 class="box-title" style="color: white"> <b>{{$video->title}}</b></h3>
                    <br>
                    <span style="margin-left:0px;color: white" class="description">{{tr('created_time')}}- {{$video->video_date}}</span>

                </div>
                <div class='pull-right'>
                    @if ($video->compress_status == 0) <span class="label label-danger">{{tr('compress')}}</span>
                    @else
                    <a href="{{route('admin.edit.video' , array('id' => $video->video_tape_id))}}" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i> {{tr('edit')}}</a>
                    @endif
                </div>
                <div class="clearfix"></div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">

                <section id="revenue-section" >

                    <div class="row">

                        <h3 style="margin-top:0;" class="text-green col-lg-12">{{tr('ppv_revenue')}}</h3>

                        <div class="col-md-4">

                            <p class="ppv-amount-label">
                                <b>{{tr('total')}}: </b>
                                <label class="text-red">{{Setting::get('currency')}} {{$video->admin_amount + $video->user_amount}}</label>
                            </p>

                        </div>

                        <div class="col-md-4">

                            <p class="ppv-amount-label">
                                <b>{{tr('admin_amount')}}: </b>
                                <label class="text-green">{{Setting::get('currency')}} {{$video->admin_ppv_amount}}</label>
                            </p>

                        </div>

                        <div class="col-md-4">

                            <p class="ppv-amount-label">
                                <b>{{tr('user_amount')}}: </b>
                                <label class="text-blue">
                                    {{Setting::get('currency')}} {{$video->user_ppv_amount}}
                                </label>
                            </p>

                        </div>

                    </div>

                    <hr>

                </section>

              <div class="row">
                  <div class="col-lg-12 row">

                    <div class="col-lg-4">

                        <div class="box-body box-profile">

                            <h4>{{tr('details')}}</h4>
                            
                            <ul class="list-group list-group-unbordered">
                                <li class="list-group-item">
                                    <b><i class="fa fa-suitcase margin-r-5"></i>{{tr('channel')}}</b> <a class="pull-right">{{$video->channel_name}}</a>
                                </li>
                              
                                

                                <li class="list-group-item">
                                    <b><i class="fa fa-clock-o margin-r-5"></i>{{tr('duration')}}</b> <a href="#" class="pull-right">{{$video->duration}}</a>
                                </li>

                                <li class="list-group-item">
                                    <b><i class="fa fa-stop margin-r-5"></i>{{tr('age_limit')}}</b> <a class="pull-right">{{$video->age_limit ? '18+' :  'All Users'}}&nbsp;</a>
                                </li> 

                                <li class="list-group-item">
                                    <b><i class="fa fa-bullhorn margin-r-5"></i>{{tr('ad_status')}}</b> <a class="pull-right">
                                        
                                        @if($video->ad_status)
                                            <span class="label label-success">{{tr('yes')}}</span>
                                        @else
                                            <span class="label label-danger">{{tr('no')}}</span>
                                        @endif
                                    </a>
                                </li>

                                <li class="list-group-item">
                                    <b><i class="fa fa-bullhorn margin-r-5"></i>{{tr('publish_type')}}</b> <a class="pull-right">
                                        
                                        @if($video->publish_status)
                                            <span class="label label-success">{{tr('now')}}</span>
                                        @else
                                            <span class="label label-danger">{{tr('later')}}</span>
                                        @endif
                                    </a>
                                </li>

                                @if($video->publish_status != 1)

                                <li class="list-group-item">
                                    <b><i class="fa fa-bullhorn margin-r-5"></i>{{tr('publish_time')}}</b> <a class="pull-right">
                                        
                                        {{$video->publish_time }}
                                    </a>
                                </li>

                                @endif

                                <li class="list-group-item">
                                    <b><i class="fa fa-bullhorn margin-r-5"></i>{{tr('is_ppv')}}</b> <a class="pull-right">
                                        
                                       @if($video->ppv_amount > 0)
                                            <span class="label label-success">{{tr('yes')}}</span>
                                        @else
                                            <span class="label label-danger">{{tr('no')}}</span>
                                        @endif
                                    </a>
                                </li>

                                <li class="list-group-item">
                                    <b><i class="fa fa-star margin-r-5"></i>{{tr('ratings')}}</b> <a class="pull-right">
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
                                  </a>
                                </li>

                                <li class="list-group-item">
                                    <b><i class="fa fa-eye margin-r-5"></i>{{tr('views')}}</b> <a class="pull-right">{{number_format_short($video->watch_count)}}</a>
                                </li>

                                <li class="list-group-item">
                                    <b><i class="fa fa-clock-o margin-r-5"></i>{{tr('ads_view_revenue')}}</b> <a class="pull-right"> {{Setting::get('currency')}} {{$video->amount}}</a>
                                </li>

                                

                                <li class="list-group-item">
                                    <b><i class="fa fa-thumbs-up margin-r-5"></i>{{tr('likes')}}</b> <a class="pull-right">{{number_format_short($video->getScopeLikeCount->count())}}&nbsp;</a>
                                </li> 

                                <li class="list-group-item">
                                    <b><i class="fa fa-thumbs-down margin-r-5"></i>{{tr('dislikes')}}</b> <a class="pull-right">{{number_format_short($video->getScopeDisLikeCount->count())}}&nbsp;</a>
                                </li>                                
                            
                            </ul>

                        </div>

                    </div>

                    <div class="col-lg-8">

                        <div class="box-body box-profile">
                            <h4></h4>
                        </div>

                         <div class="row" style="overflow-x: hidden;overflow-y: scroll; height:20em">
                         
                            <div class="col-lg-12">

                              <strong><i class="fa fa-file-text-o margin-r-5"></i> {{tr('description')}}</strong>

                              <p style="margin-top: 10px;">{{$video->description}}.</p>
                            </div>
                            <div class="col-lg-12">
                                  <strong><i class="fa fa-file-text-o margin-r-5"></i> {{tr('reviews')}}</strong>

                                  <p style="margin-top: 10px;">{{$video->reviews}}.</p>
                            </div>


                            <div class="col-lg-12">
                                  <strong><i class="fa fa-file-text-o margin-r-5"></i> {{tr('user_reviews')}}</strong>

                                  <p style="margin-top: 10px;"><a href="{{route('admin.reviews', array('video_tape_id'=> $video->video_tape_id))}}"> {{tr('no_of_reviews')}} - {{$video->getScopeUserRatings()->count()}}.</a></p>
                            </div>
                           
                         </div>

                    </div>
                    
                  </div>
                </div>

                <hr>

                <section id="ppv-details">

                    @if(Setting::get('is_payper_view'))

                        @if($video->ppv_amount > 0)


                            <h4 style="margin-left: 15px;font-weight: bold;">{{tr('pay_per_view')}}</h4>

                            <div class="row">

                                <div class="col-lg-12">
                                    <div class="col-lg-4">
                                        <strong><i class="fa fa-file-text-o margin-r-5"></i> {{tr('type_of_user')}}</strong>

                                        <p style="margin-top: 10px;">
                                            @if($video->type_of_user == NORMAL_USER)
                                                {{tr('normal_user')}}
                                            @elseif($video->type_of_user == PAID_USER)
                                                {{tr('paid_user')}}
                                            @elseif($video->type_of_user == BOTH_USERS) 
                                                {{tr('both_user')}}
                                            @else
                                                -
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-lg-4">
                                        <strong><i class="fa fa-file-text-o margin-r-5"></i> {{tr('type_of_subscription')}}</strong>

                                        <p style="margin-top: 10px;">
                                            @if($video->type_of_subscription == ONE_TIME_PAYMENT)
                                                {{tr('one_time_payment')}}
                                            @elseif($video->type_of_subscription == RECURRING_PAYMENT)
                                                {{tr('recurring_payment')}}
                                            @else
                                                -
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-lg-4">
                                        <strong><i class="fa fa-file-text-o margin-r-5"></i> {{tr('amount')}}</strong>

                                        <p style="margin-top: 10px;">
                                           {{Setting::get('currency')}} {{$video->ppv_amount}}
                                        </p>
                                    </div>
                                </div>
                            
                            </div>

                            <hr>
                        
                        @endif

                    @endif

                </section>

                <div class="row">

                    <div class="col-lg-12">
                        <div class="col-lg-6">

                            <strong><i class="fa fa-video-camera margin-r-5"></i> {{tr('full_video')}}</strong>

                            <br>

                             <b>{{tr('embed_link')}} : </b> <a href="{{route('embed_video', array('u_id'=>$video->unique_id))}}" target="_blank">{{route('embed_video', array('u_id'=>$video->unique_id))}}</a>

                            <div class="margin-t-10" style="margin-top:10px;">
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

                           <strong><i class="fa fa-file-picture-o margin-r-5"></i> {{tr('images')}}</strong>

                            <div class="row margin-bottom" style="margin-top: 10px;">
                                <!-- /.col -->
                                <div class="col-lg-12">
                                  <div class="row">
                                    <div class="col-lg-6">
                                        <img alt="Photo" src="{{($video->default_image) ? $video->default_image : ''}}" class="img-responsive" style="width:100%;height:130px;">
                                    </div>
                                    @foreach($video_images as $i => $image)
                                    <div class="col-lg-6">
                                      <img alt="Photo" src="{{$image->image}}" class="img-responsive" style="width:100%;height:130px">
                                      <br>
                                    </div>
                                    @endforeach
                                    @if ($video->is_banner) 
                                        <div class="col-lg-6">
                                            <img alt="Photo" src="{{$video->banner_image}}" class="img-responsive" style="width:100%;height:130px" title="Banner Image">
                                        </div>
                                    @endif
                                    <!-- /.col -->
                                  </div>
                                </div>
                                  <!-- /.row -->
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

                    var sample_video = "{{$secure_video}}";
                    secure_video = sample_video.replace(/&amp;/g, '&');

                    var playerInstance = jwplayer("main-video-player");


                    playerInstance.setup({
                        file: secure_video,
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

