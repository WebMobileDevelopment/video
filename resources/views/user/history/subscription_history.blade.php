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

			<div class="spacing1 top">
				<div class="pull-right">
                	
                	<a href="{{route('user.subscriptions')}}"><button class="btn btn-sm btn-info mb-20">{{tr('view_plans')}}</button></a>

                </div>

				<h3 class="no-margin">{{tr('subscription_history')}}</h3>	

				<?php $subscription_details = get_expiry_days(Auth::user()->id);?>

                <br>


                @if(count($response->data) > 0)

					@foreach($response->data as  $key => $temp)

	               	 	@if($key == 0 && $temp->status == PAID_STATUS && $temp->current_subscription_amount > 0)

							@if ($temp->is_cancelled == AUTORENEWAL_ENABLED)

								<button class="btn btn-danger" data-toggle="modal" data-target="#disable">{{tr('pause_autorenewal')}}</button>


							@else 
								
								<button class="btn btn-danger" data-toggle="modal" data-target="#enable">{{tr('enable_autorenewal')}}</button>

							@endif
						
						@endif

					@endforeach

				@endif

                <div class="clearfix"></div>

				<div class="row">
					@if(count($response->data) > 0)
						@foreach($response->data as $temp)
						<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
							<div class="new-subs-card">
								<div class="new-subs-card-img">
									<img src="{{asset('images/subscriptions2.png')}}">
									<div class="new-subs-card-title">
										<div>
											<div class="text-right">
													<img src="{{asset('images/guarantee.png')}}" class="active-plan">
												@if($temp->status)
													<span class="label label-info">{{tr('success')}}</span>
												@else
													<span class="label label-warning">{{tr('failure')}}</span>
												@endif
											</div>
											<h3 class="amount">
												<span class="sign">{{$temp->currency}}</span>
												<span class="cash">{{$temp->amount}}</span>
												<span class="period">/ {{$temp->plan}} month</span>
											</h3>
											<h4 class="title">{{$temp->title}}</h4>
										</div>
									</div>
								</div>

								<div class="new-subs-card-details">
									<div class="new-sub-payment-details">
										<h4>
											<span class="bold-text">{{tr('is_coupon_applied')}}:&nbsp;</span>
											<span>{{$temp->is_coupon_applied ? tr('yes') : tr('no')}}</span>
										</h4>
										@if($temp->coupon_code)
										<h4>
											<span class="bold-text">{{tr('coupon_code')}}:&nbsp;</span>
											<span>{{$temp->coupon_code}}</span>
										</h4>
										@endif
										@if($temp->coupon_code)
										<h4>
											<span class="bold-text">{{tr('coupon_amount')}}:&nbsp;</span>
											<span>{{$temp->currency}} {{$temp->coupon_amount}}</span>
										</h4>
										@endif
										<h4>
											<span class="bold-text">{{tr('subscription_amount')}}:&nbsp;</span>
											<span>{{$temp->currency}} {{$temp->subscription_amount}}</span>
										</h4>
										<h4>
											<span class="bold-text">{{tr('transaction_id')}}:&nbsp;</span>
											<span>{{$temp->payment_id}}</span>
										</h4>

										<h4>
											<span class="bold-text">{{tr('payment_mode')}}:&nbsp;</span>
											<span>{{$temp->payment_mode}}</span>
										</h4>
										@if($temp->status)
										<h4>
											<span class="bold-text">{{tr('paid_at')}}:&nbsp;</span>
											<span>{{date('d M, Y', strtotime($temp->created_at))}}</span>
										</h4>
										@endif

										@if($temp->coupon_code)
										<h4>
											<span class="bold-text">{{tr('coupon_reason')}}:&nbsp;</span>
											<span>{{$temp->coupon_reason}}</span>
										</h4>
										@endif

										@if($temp->is_cancelled)
										<h4>
											<span class="bold-text">{{tr('cancel_reason')}}:&nbsp;</span>
											<span>{{$temp->cancel_reason}}</span>
										</h4>
										@endif
									</div>
									<div>
										<?= $temp->description;?>
									</div>
								</div>
								<div>
									<a class="subscribe-btn"><i class="fa fa-clock-o"></i>&nbsp;{{$temp->expiry_date}}</a>
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

				<!--enable modal -->
				<div class="modal fade" id="enable" role="dialog">
					<div class="modal-dialog">

					  	<!-- Modal content-->
					  	<div class="modal-content autorenewal">
					    	<div class="modal-header">
					      		<button type="button" class="close" data-dismiss="modal">&times;</button>
					      		<h4 class="modal-title">{{tr('enable_autorenewal')}}</h4>
					    	</div>
					    	<div class="modal-body">

					    		<form method="post" action="{{route('user.subscriptions.enable-subscription')}}">
						      		<p class="note grey-clr text-left">{{tr('enable_autorenewal_notes')}}</p>
						      		<div class="text-right">
						      			<button type="submit" class="btn btn-primary mr-10">{{tr('enable')}}</button>
						      			<button type="button" class="btn btn-default" data-dismiss="modal">{{tr('close')}}</button>
						      		</div>
						      	</form>
					    	</div>
					  </div>
					  
					</div>
				</div>
				<div class="modal fade" id="disable" role="dialog">
					<div class="modal-dialog">

					  	<!-- Modal content-->
					  	<div class="modal-content autorenewal">
					    	<div class="modal-header">
					      		<button type="button" class="close" data-dismiss="modal">&times;</button>
					      		<h4 class="modal-title">{{tr('pause_autorenewal')}}</h4>
					    	</div>
					    	<div class="modal-body">

					    		<form method="post" action="{{route(
					    		'user.subscriptions.pause-subscription')}}">
						      		<p class="note grey-clr text-left">{{tr('pause_autorenewal_notes')}}</p>
						      			<div class="form-group" id="disable_form">
										  	<textarea class="form-control" rows="4" id="comment" placeholder="{{tr('reason_for_cancellation')}}" name="cancel_reason" required></textarea>
										  	<p class="underline2"></p>
										</div>
						      		
						      		<div class="text-right">
						      			<button type="submit" class="btn btn-primary mr-10">{{tr('pause')}}</button>
						      			<button type="button" class="btn btn-default" data-dismiss="modal">{{tr('close')}}</button>
						      		</div>
					      		</form>
					    	</div>
					  </div>
					  
					</div>
				</div>
				<!-- modal -->
			</div>
		</div>

	</div>
</div>

@endsection