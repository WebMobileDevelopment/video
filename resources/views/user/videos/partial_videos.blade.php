@foreach($videos as $i => $video)

        <li class="sub-list row">
            <div class="main-history">
                <div class="history-image">
                    <a href="{{$video->url}}"><img src="{{$video->video_image}}"></a>
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
                        <div class="cross-title2">
                            <h5 class="payment_class"><a href="{{$video->url}}">{{$video->title}}</a></h5>
                           
                            <span class="video_views">
                                <i class="fa fa-eye"></i> {{$video->watch_count}} {{tr('views')}} <b>.</b> 
                                {{ common_date($video->created_at) }}
                            </span>
                        </div> 
                        @if(Auth::check())
                        @if($channel->user_id == Auth::user()->id)

                        @if($video->status)
                        <div class="cross-mark2">
                            
                            <label style="float:none; margin-top: 6px;" class="switch hidden-xs" title="{{$video->ad_status ? tr('disable_ad') : tr('enable_ad')}}">
                                <input id="change_adstatus_id" type="checkbox" @if($video->ad_status) checked @endif onchange="change_adstatus(this.value, {{$video->video_tape_id}})">
                                <div class="slider round"></div>
                            </label>

                            <div class="btn-group show-on-hover">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
    
                                    <span class="hidden-xs">{{tr('action')}}</span>
                                    <span class="caret"></span>

                                </button>

                                <ul class="dropdown-menu dropdown-menu-right" role="menu">

                                    @if(Setting::get('is_payper_view') == 1)
                                    <li><a data-toggle="modal" data-target="#pay-perview_{{$video->video_tape_id}}">{{tr('pay_per_view')}}</a></li>
                                    @endif

                                    @if($video->amount > 0) 
                                    <li><a data-toggle="modal" data-target="#earning_{{$video->video_tape_id}}">{{tr('total_earning')}}</a></li>
                                    <!-- <li><a data-toggle="modal" data-target="#earning">{{tr('total_earning')}}</a></li> -->
                                    <li class="divider"></li>
                                    @endif
                                    
                                    <li><a title="edit" href="{{route('user.edit.video', $video->video_tape_id)}}">{{tr('edit_video')}}</a></li>
                                    <li><a title="delete" onclick="return confirm(&quot;{{tr('user_video_delete_confirm') }}&quot;)" href="{{route('user.delete.video' , array('id' => $video->video_tape_id))}}"> {{tr('delete_video')}}</a></li>
                                    <li class="visible-xs">
                                        <a onclick="change_adstatus({{$video->ad_status}}, {{$video->video_tape_id}})" style="cursor: pointer;" id="ad_status_{{$video->video_tape_id}}">@if($video->ad_status) {{tr('disable_ad')}} @else {{tr('enable_ad')}} @endif</a>
         
                                    </li>
                                </ul>


                                @if($video->amount > 0) 

                                    <div class="modal fade modal-top" id="earning_{{$video->video_tape_id}}" role="dialog">
                                    <!-- <div class="modal fade modal-top" id="earning" role="dialog"> -->
                                        <div class="modal-dialog bg-img modal-sm" style="background-image: url({{asset('images/popup-back.jpg')}});">

                                            <div class="modal-content earning-content">
                                                <div class="modal-header text-center">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    <h3 class="modal-title no-margin">{{tr('total_earnings')}}</h3>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <div class="amount-circle">
                                                        <h3 class="no-margin">${{$video->amount + ppv_amount($video->video_tape_id)}}</h3>
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
                                                    <form  action="{{route('user.save.video-payment', $video->video_tape_id)}}" method="POST">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            <h4 class="modal-title text-left" style="color: #000;">{{tr('pay_per_view')}}</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                           
                                                                <h4 class="black-clr text-left">{{tr('type_of_user')}}</h4>
                                                                <div>
                                                                    <label class="radio1">
                                                                        <input id="radio1" type="radio" name="type_of_user"  value="{{NORMAL_USER}}" {{($video->type_of_user > 0) ? (($video->type_of_user == NORMAL_USER) ? 'checked' : '') : 'checked'}} required>
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
                                                                    <input id="radio2" type="radio" name="type_of_user" {{($video->type_of_user == BOTH_USERS) ? 'checked' : ''}} required>
                                                                    <span class="outer"><span class="inner"></span></span>{{tr('both_user')}}
                                                                </label>
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

                                                                {{{$video->type_of_subscription}}} ergergerg
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

                                                     <div class="modal-footer">
                                                        <div class="pull-left">
                                                            @if($video->ppv_amount > 0)
                                                                <a class="btn btn-danger" href="{{route('user.remove_pay_per_view', $video->video_tape_id)}}">{{tr('remove_pay_per_view')}}</a>
                                                            @endif
                                                        </div>
                                                        <div class="pull-right">
                                                            <button type="button" class="btn btn-default" data-dismiss="modal">{{tr('close')}}</button>

                                                            <button type="submit" class="btn btn-primary">{{tr('submit')}}</button>
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
                            <button class="btn btn-warning btn-small">{{tr('video_compressing')}}</button>
                        @endif
                        @endif
                        @endif

                                            <!--end of cross-mark-->                       
                    </div> <!--end of history-head--> 

                    <div class="description">
                        <?= $video->description ?>
                    </div><!--end of description--> 

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