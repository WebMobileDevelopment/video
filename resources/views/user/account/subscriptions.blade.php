@extends('layouts.user')


@section('content')

<div class="y-content">

    <div class="row y-content-row">

        @include('layouts.user.nav')

		<div class="page-inner col-sm-9 col-md-10 profile-edit">
				
				<div class="profile-content">	

					<div class="row no-margin">

                    	<div class="col-sm-12 profile-view">

							<h3 class="m-0">{{tr('subscriptions')}}</h3>

							@include('notification.notify')

							<!-- new ui -->
							<div class="row">
								@if(count($subscriptions) > 0)
									@foreach($subscriptions as $s => $subscription)
									<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
										<div class="new-subs-card">
											<div class="new-subs-card-img">
												<img src="{{asset('images/subscriptions1.png')}}" alt="{{$subscription->title}}">
												<div class="new-subs-card-title">
													<div>
														<h3 class="amount">
															<span class="sign">{{ Setting::get('currency')}}</span>
															<span class="cash">{{$subscription->amount}}</span>
															<span class="period">/ {{$subscription->plan}} month</span>
														</h3>
														<h4 class="title">{{$subscription->title}}</h4>
													</div>
												</div>
											</div>
											<div class="new-subs-card-details">
												
												<?= $subscription->description; ?>
											</div>
											<div>
												<a href="{{route('user.subscription.invoice' , ['s_id' => $subscription->id])}}" class="subscribe-btn">{{tr('choose_plan')}}</a>
											</div>
										</div>
									</div>
									@endforeach

									@else
									<img src="{{asset('images/no-result.jpg')}}" class="img-responsive auto-margin">
								@endif
							</div>
							<!-- new ui -->
						</div>
					</div>
				</div>
			<div class="sidebar-back"></div> 
		</div>

	</div>

</div>

@endsection
