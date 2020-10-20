@extends( 'layouts.user' )


@section( 'styles' )

<link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/custom-style.css')}}"> 

@endsection

@section('content')

<div class="y-content">

	<div class="row content-row">

		@include('layouts.user.nav')

		<div class="page-inner col-sm-9 col-md-10">

			@include('notification.notify')

			<div class="sub-history">
=
				<h3 class="no-margin text-left">{{tr('ppv_history_user')}}</h3>
			
				<div class="row">
					@if(count($response->data) > 0)

						@foreach($response->data as $temp)

							<div class="col-xs-12 col-sm-12 col-md-4 top">
								<div class="ppv-card">
									<div class="height-120">
										<div class="ppv-video" style="background-image: url({{$temp->picture}});"></div>
										<div class="ppv-title">{{$temp->title}}</div>
										<span class="ppv-view"><i class="fa fa-money"></i> {{$temp->currency}}{{$temp->amount}}</span>
									</div>
									<div class="ppv-details">
										<!-- <p>Paid Status: &nbsp;
											@if($temp->amount > 0) 
											<span class="green-clr">{{tr('paid')}}</span>
											@else 
											<span class="grey-clr">{{tr('pending')}}</span> 
											@endif
										</p>

										<p>{{tr('payment_id')}}: &nbsp;<span class="grey-clr">{{$temp->payment_id}}</span></p>
										<p class="no-margin">{{tr('paid_at')}}: &nbsp;<span class="grey-clr">{{$temp->paid_date}}</span></p>-->
										<p>
											<span class="ppv-small-head">paid status</span>
											@if($temp->amount > 0) 
											<span class="label label-info pull-right">{{tr('paid')}}</span>
											@else 
											<span class="label label-warning pull-right">{{tr('pending')}}</span> 
											@endif
										</p>
										<p class="ppv-small-head">{{tr('is_coupon_applied')}}</p>
										<h4 class="ppv-text overflow">{{$temp->is_coupon_applied ? tr('yes') : tr('no')}}</h4>
										@if($temp->coupon_code)
										<p class="ppv-small-head">{{tr('coupon_code')}}</p>
										<h4 class="ppv-text overflow">{{$temp->coupon_code}}</h4>
										@endif
										@if($temp->coupon_code)
										<p class="ppv-small-head">{{tr('coupon_amount')}}</p>
										<h4 class="ppv-text overflow">{{$temp->currency}}{{$temp->coupon_amount}}</h4>
										@endif
										<p class="ppv-small-head">{{tr('video_amount')}}</p>
										<h4 class="ppv-text overflow">{{$temp->currency}}{{$temp->ppv_amount}}</h4>
										<p class="ppv-small-head">{{tr('transaction_id')}}</p>
										<h4 class="ppv-text overflow">{{$temp->payment_id}}</h4>
										<p class="ppv-small-head">{{tr('payment_mode')}}</p>
										<h4 class="ppv-text overflow">{{$temp->payment_mode}}</h4>
										<p class="ppv-small-head">{{tr('paid_at')}}</p>
										<h4 class="ppv-text">{{$temp->paid_date}}</h4>
										@if($temp->coupon_reason)
										<p class="ppv-small-head">{{tr('coupon_reason')}}</p>
										<h4 class="ppv-text">{{$temp->coupon_reason}}</h4>
										@endif
									</div>
								</div>
							</div>

						@endforeach


                        <div class="row">
                            <div class="col-md-12">
                                <div align="center" id="paglink"><?php echo $response->pagination; ?></div>
                            </div>
                        </div>

					@else

						<img src="{{asset('images/no-result.jpg')}}" class="img-responsive auto-margin">

					@endif
				</div>

			</div>
		</div>

	</div>
</div>

@endsection