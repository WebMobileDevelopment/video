@if(Auth::check() ? Auth::user()->user_type == 1 : 0)

    @if(Setting::get('broadcast_by_user') == 1 || (Auth::check() ? Auth::user()->is_master_user == 1 : 0))

        <div id="start_broadcast" class="modal fade" role="dialog">
            <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header start_brocadcast_form">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title text-uppercase text-center">{{tr('start_broadcast')}}</h4>
                </div>

                <form method="post" action="{{route('user.live_video.broadcast')}}">

                    <div class="modal-body body-modal">


                        <!-- <input type="hidden" name="channel_id" value="{{$channel->id}}"> -->
                        <div class="form-group form-group1">
                            <label for="channel_id" class="control-label">Channel * </label>
                            <select class="form-control select2" name="channel_id" required data-placeholder="Select Channel*" style="width:100%;">
                                @foreach($channels as $chann)
                                <option value="{{$chann->id}}"  {{ ($chann->id == $channel->id) ? 'selected':'' }}>{{$chann->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="user_id" value="{{$channel->user_id}}">

                        <!-- Text input-->

                        <div class="form-group form-group1">
                            <input type="text" class="form-control signup-form1" placeholder="{{tr('enter_title')}}" id="title" name="title" required="">
                        </div>

                        <div class="form-group radio-btn text-left">

                            <label class="control-label col-xs-6 col-sm-4 zero-padding" for="optradio">Recording The Live</label>

                            <div class="col-xs-6 col-sm-8">

                                <label class="radio-inline width-100" for="record_live">
                                    <input type="checkbox" id="record_live" class="option-input radio" name="record_live" value="0">
                                </label>
                                
                            </div>

                        </div>

                        <div class="form-group radio-btn text-left">

                            <label class="control-label col-xs-4 col-sm-3 zero-padding" for="optradio">{{tr('payment')}}</label>

                            <div class="col-xs-8 col-sm-8">

                                <label class="radio-inline width-100" for="reqType-1">
                                    <input type="radio" id="reqType-1" checked="checked" class="option-input radio" name="payment_status" onchange="return $('#price').hide();" value="0">{{tr('free')}}
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" id="reqType-0" class="option-input radio" name="payment_status" onchange="return $('#price').show()" value="1">{{tr('paid')}}
                                </label>
                            </div>
                        
                        </div>

                        <div class="clearfix"></div>

                        <!-- ======amount===== -->
                        <div class="form-group form-group1" style="display: none" id="price">
                            <input id="Amount" name="amount" type="number" placeholder="Amount" pattern="[0-9]{0,}" class="form-control signup-form1">
                        </div>
                     

                        <div class="form-group form-group1">
                            <textarea id="description" name="description" class="form-control signup-form1" rows="5" id="comment" placeholder="{{tr('description')}}"></textarea>
                        </div>

                    </div>

                    <div class="modal-footer">

                        <button class="btn btn-info pull-left" type="reset">{{tr('reset')}}</button>

                        @if(Setting::get('broadcast_by_user') == 1 || Auth::user()->is_master_user == 1) 
                            <button class="btn btn-danger" type="submit" id="submitButton" name="submitButton">{{tr('broadcast')}}</button>
                        @else

                            <button class="btn btn-danger" type="button" onclick="return alert('Broadcast option is disabled by admin.');">{{tr('broadcast')}}</button>

                        @endif

                    </div>

                </form>

            </div>

          </div>
        
        </div>

    @endif

@endif