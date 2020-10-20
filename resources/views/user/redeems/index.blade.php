@extends('layouts.user')

@section('styles')

<style>
    
/**
 * Circle Styles
 */

.circle {
 /* position: relative;*/
  display: block;
  margin: 1em 0;
  background-color: transparent;
  color: #222;
  text-align: center;
  width: 150px;
  height: 150px;
  border-radius: 50%;
  background-color: #f1f1f1;
  box-shadow: 2px 5px 10px grey;
  margin: 0 auto;
  display: flex;
  justify-content: center;
  align-items: center;
}

/*.circle:after {
  display: block;
  width: 150px;
  height: 150px;
  border-radius: 50%;
  background-color: #f1f1f1;
  content: "";
  box-shadow: 2px 5px 10px grey;
  margin: 0 auto;
}*/

.circle__inner {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    width:150px;
    margin: 0 auto;
    height: 100%;
}

.circle__wrapper {
    display: table;
    width: 100%;
    height: 100%;
}

.circle__content {
    display: table-cell;
    padding: 1em;
    vertical-align: middle;
    color: #D9230F;
}

@media (min-width: 480px) {
    .circle__content {
        font-size: 1.3em;
    }
}

@media (min-width: 768px) {
  .circle__content {
    font-size: 1.3em;
  }
}

.redeem-content {
    margin:3em 0 1em 0;line-height: 1.8em;
}

table {
    box-shadow: 0px 1px 5px grey !important;
}
thead>tr>th {
    padding: 1% !important;
}
</style>

@endsection

@section('content')

    <div class="y-content">
    
        <div class="row content-row">

            @include('layouts.user.nav')

            <div class="history-content page-inner col-sm-9 col-md-10">

                @include('notification.notify')

                <!-- new ui @ranjitha -->
                <div class="new-history">
                    
                    <h3 class="">{{tr('redeems')}}</h3>
                    
                    <div class="row">
                        
                        <div class="col-sm-12 col-md-4 col-lg-4">
                            
                            <div class="new-redeem-sec">
                                
                                <div class="circle">
                                    
                                    <div class="circle__content">
                                        <b>{{formatted_amount($redeem_details->remaining)}}</b>
                                    </div>
                                
                                </div>
                                
                                <p class="redeem-content">{{ tr('redeem_content') }}</p>                              
                                @if($redeem_details->send_redeem_btn_status)
                                
                                    <div class="text-right">
                                        <a href="{{route('user.redeems.send.request')}}" class="btn btn-info">{{tr('send_redeem')}}</a>
                                    </div>

                                @else
                                    <div class="text-right">
                                        <a href="javascript:void(0);" disabled class="btn btn-info">{{tr('send_redeem')}}</a>
                                    </div>
                                @endif

                            </div>

                            <div class="new-redeem-sec">
                                <p style="color: #3398E6">{{tr('paypal_email_note')}}</p>
                                <form  action="{{ route('user.update.paypal_email') }}" method="POST" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label for="paypal_email">{{tr('paypal_email')}}</label>
                                        <input type="paypal_email" value="{{old('paypal_email') ?: Auth::user()->paypal_email}}" name="paypal_email" class="form-control" id="paypal_email" aria-describedby="emailHelp" placeholder="{{tr('enter_email')}}" required>
                                    </div>

                                    <div class="text-right">
                                        <button type="submit" class="btn btn-info">{{tr('submit')}}</button>
                                    </div> 
                                </form>
                            </div>
                       
                        </div>
                       
                        <div class="col-sm-12 col-md-8 col-lg-8">
                       
                            <div class="new-redeem-sec">
                                <!-- redeems -->
                       
                                <div class="timeline">

                                    @if($redeem_requests)

                                        @foreach($redeem_requests as $rr => $redeem_request)
                                            <!-- 1 -->
                                            <div class="timeline-item" *ngFor="let redeem_request of redeem_requests">
                                                <div class="timeline-bar">
                                                    <div class="version"></div>
                                                </div>
                                                <div class="redeem-content1">
                                                    <ul>
                                                        <li>
                                                            <p class="text-grey-clr m-0">
                                                                {{tr('sent_date')}} : {{$redeem_request->created_at->diffForHumans()}}
                                                            </p>

                                                            <h4 class="redeem-amount">
                                                                {{tr('redeem_amount')}} <span class="bold">{{Setting::get('currency')}} {{$redeem_request->request_amount}}</span>
                                                            </h4>

                                                            <p class="text-grey-clr mt-0">
                                                                {{tr('paid_amount')}}: {{Setting::get('currency')}} {{$redeem_request->paid_amount}}
                                                            </p>

                                                            <p class="text-grey-clr mt-0">
                                                                {{tr('paid_date')}}: {{$redeem_request->updated_at->diffForHumans()}}
                                                            </p>

                                                            <p class="text-grey-clr mt-0">
                                                                {{tr('status')}} : {{redeem_request_status($redeem_request->status)}}
                                                            </p>
                                                            

                                                            @if(in_array($redeem_request->status, [REDEEM_REQUEST_SENT , REDEEM_REQUEST_PROCESSING]))
                                                                <a href="{{route('user.redeems.request.cancel' , ['redeem_request_id' => $redeem_request->id])}}" class="btn btn-danger">{{tr('cancel')}}</a>
                                                            @else
                                                                <span class="text-center">-</span>
                                                            @endif

                                                        </li>
                                                        
                                                        <li>

                                                            @if($redeem_request->paid_amount > 0)
                                                                <img src="{{asset('images/paid.png')}}">
                                                            @else
                                                                <img src="{{asset('images/pending.png')}}">
                                                            @endif

                                                        </li>
                                           
                                                    </ul>
                                           
                                                </div>
                                           
                                            </div>
                                        
                                        @endforeach

                                    @else

                                        <img src="{{asset('images/no-result.jpg')}}" class="img-responsive auto-margin">

                                    @endif
                                   
                                </div>
                                <!-- redeems --> 
                            </div>
                        </div>
                    </div>
                </div>
                <!-- new ui @ranjitha -->

                <div class="sidebar-back"></div> 
            </div>
    
        </div>
    </div>

@endsection