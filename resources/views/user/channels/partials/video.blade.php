<li role="tabpanel" class="tab-pane" id="videos">

                            <div class="recom-area abt-sec">

                                <div class="abt-sec-head">

                                    <div class="new-history1">

                                        <div class="content-head">

                                            <div>
                                                <h4 style="color: #000;">{{tr('videos')}}&nbsp;&nbsp;
                                                @if(Auth::check())

                                                <!-- @if(Auth::user()->id == $channel->user_id)
                                                <small style="font-size: 12px">({{tr('note')}}:{{tr('ad_note')}} )</small>

                                                @endif -->

                                                @endif
                                                </h4>
                                            </div>

                                        </div>
                                        <!--end of content-head-->

                                        @if(count($videos) > 0)

                                        <ul class="history-list">

                                            @foreach($videos as $i => $video)
                                            @if($video->video_type != VIDEO_TYPE_R4D)
                                            <li class="sub-list row border-0">

                                                <div class="main-history">

                                                    <div class="history-image">

                                                        <a href="{{$video->url}}">
                                                            <!-- <img src="{{$video->video_image}}"> -->
                                                            <img src="{{asset('streamtube/images/placeholder.gif')}}" data-src="{{$video->video_image}}" class="slide-img1 placeholder" />
                                                        </a> 
                                                        
                                                        @if($video->ppv_amount > 0) 

                                                            @if($video->ppv_status)
                                                                <div class="video_amount">
                                                                    {{tr('pay')}} - {{Setting::get('currency')}}{{$video->ppv_amount}}

                                                                </div>
                                                            @endif 

                                                        @endif

                                                        <div class="video_duration">
                                                            {{$video->duration}}
                                                        </div>

                                                    </div>
                                                    <!--history-image-->

                                                    <div class="history-title">

                                                        <div class="history-head row">
                                                           
                                                            <div class="cross-title2">
                                                                <h5 class="payment_class unset-height">
                                                                    @if(Auth::check())

                                                                        @if($channel->user_id == Auth::user()->id)

                                                                            @if($video->is_approved == YES)
                                                                                <span class="text-green" title="Admin Approved"><i class="fa fa-check-circle"></i></span>
                                                                            @else

                                                                                <span class="text-red" title="Admin Declined"><i class="fa fa-times"></i></span>

                                                                            @endif

                                                                        @endif

                                                                    @endif

                                                                    <a href="{{$video->url}}">{{$video->title}}</a>
                                                                </h5>

                                                                <span class="video_views">
                                                                    <i class="fa fa-eye"></i> {{$video->watch_count}} {{tr('views')}} <b>.</b> 
                                                                     {{ common_date($video->created_at) }}
                                                                </span>
                                                            </div>
            @if(Auth::check()) 

                @if($channel->user_id == Auth::user()->id) 

                    @if($video->status)
                
                    <div class="cross-mark2">

                        <div class="btn-group show-on-hover">
                            <button type="button" class="video-menu dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-ellipsis-v"></i>
                            </button>

                            <?php $total_amount = $video->amount + ppv_amount($video->video_tape_id); ?>

                            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                
                                @if(Setting::get('is_payper_view') == 1 && $cur_sub->ppv_income == 1)
                                <li><a data-toggle="modal" data-target="#pay-perview_{{$video->video_tape_id}}">{{tr('pay_per_view')}}</a></li>
                                @endif 

                                @if($total_amount > 0)
                                <li><a data-toggle="modal" data-target="#earning_{{$video->video_tape_id}}">{{tr('total_earning')}}</a></li>
                                <li class="divider"></li>
                                @endif

                                <li><a title="edit" href="{{route('user.edit.video', $video->video_tape_id)}}">{{tr('edit_video')}}</a></li>
                                
                                <li><a title="delete" onclick="return confirm(&quot;{{ substr($video->title, 0 , 15)}}.. {{tr('user_video_delete_confirm') }}&quot;)" href="{{route('user.delete.video' , array('id' => $video->video_tape_id))}}"> {{tr('delete_video')}}</a></li>
                            
                                @if($cur_sub->ads_us == 1)
                                <li><a onclick="change_adstatus({{$video->ad_status}}, {{$video->video_tape_id}})" style="cursor: pointer;" id="ad_status_{{$video->video_tape_id}}">@if($video->ad_status) {{tr('disable_ad')}} @else {{tr('enable_ad')}} @endif</a>
                                </li>
                                @endif

                            </ul>

                            @if($total_amount > 0)

                            <div class="modal fade modal-top" id="earning_{{$video->video_tape_id}}" role="dialog">
                                <div class="modal-dialog bg-img modal-sm" style="background-image: url({{asset('images/popup-back.jpg')}});">

                                    <div class="modal-content earning-content">
                                        <div class="modal-header text-center">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h3 class="modal-title no-margin">{{tr('total_earnings')}}</h3>
                                        </div>
                                        <div class="modal-body text-center">
                                            <div class="amount-circle">
                                                <h3 class="no-margin">${{$total_amount}}</h3>
                                            </div>
                                            <p>{{tr('total_views')}} - {{$video->watch_count}}</p>
                                            <a href="{{route('user.redeems')}}">
                                                <button class="btn btn-danger top">{{tr('view_redeem')}}</button>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            @endif

                            <!-- ========modal pay per view======= -->

                            <div id="pay-perview_{{$video->video_tape_id}}" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{route('user.save.video-payment', $video->video_tape_id)}}" method="POST">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title text-left" style="color: #000;">{{tr('pay_per_view')}}</h4>
                                            </div>
                                            <div class="modal-body pay-perview">
                                                <div style="display: none">
                                                    <h4 class="black-clr text-left">{{tr('type_of_user')}}</h4>
                                                    <div>
                                                        <label class="radio1">
                                                            <input id="radio1" type="radio" name="type_of_user" value="{{NORMAL_USER}}" {{($video->type_of_user > 0) ? (($video->type_of_user == NORMAL_USER) ? 'checked' : '') : 'checked'}} required>
                                                            <span class="outer"><span class="inner"></span></span>{{tr('normal_user')}}
                                                        </label>
                                                    </div>
                                                    <div>
                                                        <label class="radio1">
                                                            <input id="radio2" type="radio" name="type_of_user" value="{{PAID_USER}}" {{($video->type_of_user == PAID_USER) ? 'checked' : ''}} required>
                                                            <span class="outer"><span class="inner"></span></span>{{tr('paid_user')}}
                                                        </label>
                                                    </div>
                                                    <div>
                                                        <label class="radio1">
                                                            <input id="radio2" type="radio" name="type_of_user" value="{{BOTH_USERS}}" {{($video->type_of_user == BOTH_USERS) ? 'checked' : ''}} required>
                                                            <span class="outer"><span class="inner"></span></span>{{tr('both_user')}}
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>

                                                <h4 class="black-clr text-left">{{tr('type_of_subscription')}}</h4>
                                                <div>

                                                    <label class="radio1">
                                                        <input id="radio2" type="radio" name="type_of_subscription" value="{{ONE_TIME_PAYMENT}}" {{($video->type_of_subscription > 0) ? (($video->type_of_subscription == ONE_TIME_PAYMENT) ? 'checked' : '') : 'checked'}} required>
                                                        <span class="outer"><span class="inner"></span></span>{{tr('one_time_payment')}}
                                                    </label>
                                                </div>
                                                <div>

                                                    {{{$video->type_of_subscription}}}
                                                    <label class="radio1">
                                                        <input id="radio2" type="radio" name="type_of_subscription" value="{{RECURRING_PAYMENT}}" {{($video->type_of_subscription == RECURRING_PAYMENT) ? 'checked' : ''}} required>
                                                        <span class="outer"><span class="inner"></span></span>{{tr('recurring_payment')}}
                                                    </label>
                                                </div>

                                                <div class="clearfix"></div>

                                                <h4 class="black-clr text-left">{{tr('amount')}}</h4>
                                                <div>
                                                    <input type="number" required value="{{$video->ppv_amount}}" name="ppv_amount" class="form-control" id="amount" placeholder="{{tr('amount')}}" step="any" maxlength="6">
                                                    <!-- /input-group -->

                                                </div>

                                                <div class="clearfix"></div>
                                            </div>

                                            <div class="modal-footer border-0">
                                                <div class="pull-left">
                                                    @if($video->ppv_amount > 0)
                                                    <a class="btn btn-danger" href="{{route('user.remove_pay_per_view', $video->video_tape_id)}}">{{tr('remove_pay_per_view')}}</a> @endif
                                                </div>
                                                <div class="pull-right">
                                                    <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
                                                    <button type="submit" class="btn btn-info">Submit</button>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- ========modal ends======= -->

                        </div>
                    </div>

                    @else
                        <div>
                            <button class="btn btn-warning btn-small">{{tr('video_compressing')}}</button>

                            <!--end of cross-mark-->
                        </div>

                    @endif 

                @endif 

            @endif
        <!--end of history-head-->

        <?php /*<div class="category"><b class="text-capitalize">{{tr('category_name')}} : </b> <a href="{{route('user.categories.view', $video->category_unique_id)}}" target="_blank">{{$video->category_name}}</a></div> */?>

                                                            <div class="description">
                                                                <?= $video->description?>
                                                            </div>
                                                            <!--end of description-->

                                                            <span class="stars">
                                                                <a><i @if($video->ratings >= 1) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                                <a><i @if($video->ratings >= 2) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                                <a><i @if($video->ratings >= 3) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                                <a><i @if($video->ratings >= 4) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                                <a><i @if($video->ratings >= 5) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                            </span>
                                                      
                                                        </div>
                                                        <!--end of history-title-->

                                                    </div>
                                                    <!--end of main-history-->
                                               
                                                </div>

                                            </li>
                                            @endif
                                            @endforeach

                                            <span id="videos_list"></span>

                                            <div id="video_loader" style="display: none;">

                                                <h1 class="text-center"><i class="fa fa-spinner fa-spin" style="color:#ff0000"></i></h1>

                                            </div>

                                            <div class="clearfix"></div>

                                            <button class="pull-right btn btn-info mb-15" onclick="getVideos()" style="color: #fff">{{tr('view_more')}}</button>

                                            <div class="clearfix"></div>

                                        </ul>

                                        @else

                                        <!-- <p style="color: #000">{{tr('no_video_found')}}</p> -->
                                        <img src="{{asset('images/no-result.jpg')}}" class="img-responsive auto-margin"> @endif

                                    </div>

                                </div>
                                
                            </div>

                        </li>