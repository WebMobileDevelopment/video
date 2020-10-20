@extends('layouts.user')

@section('content')

    <div class="y-content">
        
        <div class="row content-row">

            @include('layouts.user.nav')

            <div class="page-inner col-sm-9 col-md-10">

                <div class="slide-area recom-area">

                    @include('notification.notify')

                    <div class="box-head recom-head">
                        <h3>{{tr('live_videos')}}</h3>
                    </div>

                    @if(count($videos) > 0)

                        <div class="recommend-list row">

                            @foreach($videos as $video)
                                <div class="slide-box recom-box">
                                    <div class="slide-image recom-image">

                                        <?php 

                                        $userId = Auth::check() ? Auth::user()->id : '';

                                        $url = ($video->amount > 0) ? route('user.payment_url', array('id'=>$video->id, 'user_id'=>$userId)): route('user.live_video.start_broadcasting' , array('id'=>$video->unique_id,'c_id'=>$video->channel_id));


                                        ?>


          <?php /*  <div class="modal fade cus-mod" id="paypal_{{$video->id}}" role="dialog">
                <div class="modal-dialog">
                
                  <!-- Modal content-->
                  <div class="modal-content">

                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title text-center text-uppercase">{{tr('payment_options')}}</h4>
                        </div>


                        <div class="modal-body">
                            <!-- <p>Please Pay to see the full video</p>  -->
                                <div class="col-lg-6">
                                  <!-- small box -->
                                  <div class="small-box bg-green">
                                    <div class="inner">
                                      <h3>{{ Setting::get('currency')}} {{$video->amount}}</h3>
                                      <div class="clearfix"></div>
                                      <p style="float: none;" class="text-left">{{tr('paypal_payment')}}</p>
                                    </div>
                                    <div class="icon">
                                      <i class="fa fa-money"></i>
                                    </div>
                                     <div class="clearfix"></div>
                                    <a href="{{route('user.live_video_paypal', array('id'=>$video->id, 'user_id'=>$userId))}}" class="small-box-footer">{{tr('to_view_video')}} <i class="fa fa-arrow-circle-right"></i></a>
                                  </div>
                                </div>
                           
                                <div class="col-lg-6">
                                  <!-- small box -->
                                  <div class="small-box bg-aqua">
                                    <div class="inner">
                                      <h3>{{ Setting::get('currency')}} {{$video->amount}}</h3>
                                      <div class="clearfix"></div>
                                      <p style="float: none;" class="text-left">{{tr('stripe_payment')}}</p>
                                    </div>
                                    <div class="icon">
                                      <i class="fa fa-money"></i>
                                    </div>
                                     <div class="clearfix"></div>
                                    <a onclick="return confirm('Are you sure want pay through card?')" href="{{route('user.stripe_payment_video', array('id'=>$video->id, 'user_id'=>$userId))}}" class="small-box-footer">{{tr('to_view_video')}} <i class="fa fa-arrow-circle-right"></i></a>
                                  </div>
                                </div>
                            
                            
                            <div class="clearfix"></div>
                            
                        </div>

                        
                  </div>
                  
                </div>
             
            </div>   */?>
                                                            
                                        <a href="{{$url}}">                                                     
                                       
                                            <div class="bg_img_video" style="background-image:url({{$video->snapshot}})"></div>

                                        </a>

                                        <div class="video_duration text-uppercase">
                                            @if($video->amount > 0) 

                                                {{tr('pay')}} - ${{$video->amount}} 

                                            @else {{tr('free')}} @endif
                                        </div>
                                    </div><!--end of slide-image-->

                                    <div class="video-details recom-details">
                                        <div class="video-head">
                                            <a>

                                            {{$video->title}}

                                            </a>
                                        </div>

                                        <span class="video_views">
                                            <i class="fa fa-eye"></i> {{$video->viewer_cnt}} {{tr('views')}} <b>.</b> 
                                            {{$video->created_at->diffForHumans()}}
                                        </span> 
                                    </div><!--end of video-details-->
                                </div><!--end of slide-box-->
                            @endforeach
                            
                        </div>

                    @else

                        <div class="recommend-list row">
                            <div class="slide-box recom-box"> {{tr('no_live_videos')}}</div>
                        </div>

                    @endif

                    <!--end of recommend-list-->

                     @if(count($videos) > 0)
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div align="center" id="paglink"><?php echo $videos->links(); ?></div>
                            </div>
                        </div>

                    @endif
                </div>

                <!--end of slide-area-->

                
            </div>

        </div>
    </div>

@endsection