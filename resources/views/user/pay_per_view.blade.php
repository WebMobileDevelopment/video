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

			
			<!-- <div class="row"> -->
				<div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3 text-center">
					<div class="row">
						<h3 class="no-margin payment-section">{{tr('select_payment')}}</h3>
						<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
							<div class="payment-card">
								<h4>{{tr('subscription_plan')}}</h4>
								<img src="{{asset('images/subscriptions-new.png')}}">
								<div  class="h-60">
									<p>{{tr('click_here_for_subscription')}}</p>
								</div>
								<div>
									<a href="{{route('user.subscriptions')}}">
									<button class="btn btn-danger">{{tr('click_here_subscription')}}</button>
									</a>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
							<div class="payment-card">
								<h4>{{tr('pay_per_video')}}</h4>
								<img src="{{asset('images/PayPer_Icon.png')}}">
								<div class="h-60">
									@if($video->type_of_subscription == 1)

							    		{{tr('one_time_payment')}}

							    	@else

							    		{{tr('recurring_payment')}}

							    	@endif
	
									<p>{{tr('amount')}} - {{Setting::get('currency')}} {{$video->ppv_amount}}</p>
								</div>
								<div>
									<a href="{{route('user.subscription.ppv_invoice', $video->id)}}">
									<button class="btn btn-danger">{{tr('click_here_subscription')}}</button>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			<!-- </div> -->
			  
		</div>

	</div>

</div>
@endsection


