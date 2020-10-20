@extends( 'layouts.user' )

@section( 'styles' )

<link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/custom-style.css')}}"> 
<link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/responsive1.css')}}"> 

<style>
	
	.list-style li {
		margin: 10px auto;
		text-align: center;
	}
</style>

@endsection

@section('content')

<div class="y-content">

	<div class="row content-row">

		@include('layouts.user.nav')

		<div class="page-inner col-sm-9 col-md-10">

			@include('notification.notify')

			<div class="invoice">

				<div class="row"> 

					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">

						<img src="{{asset('payment-failure.png')}}">

					</div>

					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 payment-failure">

						<div class="text-center" style="margin-top: 25px;">

							<h4>{{tr('payment_failed')}}</h4>

							<p>

								{{tr('payment_caused')}} : 
							</p>

							@if($paypal_error)

								<span style="color: red">{{$paypal_error}}</span>

							@else

							<ul class="list-style">

								<li><span>* {{tr('insufficient_funds')}}</span></li>
								<li><span>* {{tr('payment_configuration_issues')}}</span></li>
								<li><span>* {{tr('unexcepted_error')}} ..etc</span></li>
							</ul>

							@endif

							<div class="clearfix"></div>

							<a href="{{url('/')}}" class="btn btn-primary">{{tr('go_home')}}</a>

						</div>
						
					</div>

				</div>
			</div>

		</div>

	</div>

</div>

@endsection