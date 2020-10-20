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
				<h3 class="no-margin">{{tr('live_history')}}</h3>
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-10 col-md-offset-1">
					<div class="row">
						@if(count($response->data) > 0)

							@foreach($response->data as $temp)

								<div class="col-xs-12 col-sm-12 col-md-6 top">
									<div class="ppv-card">
										<div class="ppv-video" style="background-image: url({{$temp->picture}});">
											<div class="ppv-title">{{$temp->title}}</div>
											<span class="ppv-view"><i class="fa fa-money"></i> {{$temp->currency}}{{$temp->amount}}</span>
										</div>
										<div class="ppv-details">
											<p>{{tr('paid_status')}} &nbsp;@if($temp->amount > 0) <span class="green-clr">{{tr('paid')}}</span>@else <span class="grey-clr">{{tr('pending')}}</span> @endif</p>
											<p>{{tr('payment_id')}} &nbsp;<span class="grey-clr">{{$temp->payment_id}}</span></p>
											<p class="no-margin">{{tr('paid_at')}} &nbsp;<span class="grey-clr">{{$temp->paid_date}}</span></p>
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
</div>

@endsection