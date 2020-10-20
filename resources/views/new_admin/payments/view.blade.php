@extends('layouts.admin')

@section('title', tr('ppv_payments'))

@section('content-header')

{{ tr('ppv_payments')}}

@endsection

@section('breadcrumb')

    <li><a href="{{ route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{ tr('home')}}</a></li>

    <li class="active"><i class="fa fa-credit-card	"></i> {{ tr('ppv_payments')}}</li>

@endsection

@section('content')

	<div class="row">

		<div class="col-md-12">

			<div class="box ">

				<div class="box-header label-primary">
					<b style="font-size: 18px;">{{ $payment_details->payment_id}}</b>
				</div>
				
				<div class="box box-body">
					
					<div class="col-md-12">
						<strong>{{tr('title')}}</strong>
						<h5>{{$payment_details->title}}</h5><hr>
						<strong>
							{{ tr('video')}} : @if($payment_details->title)

			      			<a href="{{ route('admin.videos.view' , array('id' => $payment_details->video_id))}}">
			      				<h5>{{ $payment_details->title}}</h5>
			      			</a>

				      		@else
				      		 	-
				      		@endif
			      		</strong><hr>
		      		</div>
					
					<div class="col-md-6">
			      		<strong>
			      			{{ tr('username')}} :

			      			<a href="{{ route('admin.users.view' , ['user_id' => $payment_details->user_id] )}}"> 
      							{{ $payment_details->user_name ? $payment_details->user_name : "-"}} 
      						</a>
			      		</strong><hr>

			      		<strong>{{ tr('total')}} : {{ formatted_amount($payment_details->amount) }}</strong><hr>

			      		<strong>{{ tr('admin_ppv_commission')}} : {{ formatted_amount($payment_details->admin_ppv_amount) }}</strong><hr>

			      		<strong>{{ tr('user_ppv_commission')}} : {{ formatted_amount($payment_details->user_ppv_amount) }}</strong><hr>

			      		<strong>{{ tr('reason')}} : {{ $payment_details->reason}}</strong><hr>

			      		<strong>{{ tr('paid_date')}} : {{ date('d M Y',strtotime($payment_details->created_at))}}</strong><hr>

			      		<strong>{{ tr('type_of_subscription')}} : {{ $payment_details->type_of_subscription}}</strong><hr>
					</div>

					<div class="col-md-6">
			      		<strong>{{ tr('type_of_user')}} : {{ $payment_details->type_of_user}}</strong><hr>

			      		<strong>{{ tr('coupon_code')}} : {{ $payment_details->coupon_code}}</strong><hr>

				      	<strong>{{ tr('coupon_amount')}} : {{ formatted_amount($payment_details->coupon_amount? $payment_details->coupon_amount : "0.00") }}</strong><hr>

				      	<strong>{{ tr('plan_amount')}} : {{ formatted_amount($payment_details->ppv_amount ? $payment_details->ppv_amount : "0.00") }}</strong><hr>

				      	<strong>{{ tr('final_amount')}} : {{ formatted_amount($payment_details->amount ? $payment_details->amount : "0.00") }}</strong><hr>

				      	<strong>{{ tr('is_coupon_applied')}} : 	
				      	@if($payment_details->is_coupon_applied)
				
						<span class="label label-success">{{ tr('yes')}}</span>
						@else
						<span class="label label-danger">{{ tr('no')}}</span>
						@endif
						</strong><hr>

				      	<strong>{{ tr('coupon_reason')}} : {{ $payment_details->coupon_reason ? $payment_details->coupon_reason : '-'}}</strong><hr>
					</div>

				</div>

			</div>

		</div>

	</div>


@endsection