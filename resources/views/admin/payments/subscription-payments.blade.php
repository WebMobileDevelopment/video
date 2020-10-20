@extends('layouts.admin')

@section('title', tr('subscription_payments'))

@section('content-header')

{{ tr('subscription_payments') }} - {{Setting::get('currency')}} {{total_subscription_revenue($subscription ? $subscription->id : "")}}

@endsection

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li class="active"><i class="fa fa-money"></i> {{tr('subscription_payments')}}</li>
@endsection

@section('content')

@include('notification.notify')

	<div class="row">

        <div class="col-xs-12">

          	<div class="box box-primary">

          		<div class="box-header label-primary">
	                <b style="font-size: 18px;">{{tr('subscription_payments')}}</b>
	                <a href="{{route('admin.users')}}" style="float:right" class="btn btn-default">{{tr('view_users')}}</a>

	                <!-- EXPORT OPTION START -->

					@if(count($data) > 0 )
	                
		                <ul class="admin-action btn btn-default pull-right" style="margin-right: 40px">
		                 	
							<li class="dropdown">
				                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
				                  {{tr('export')}} <span class="caret"></span>
				                </a>
				                <ul class="dropdown-menu">
				                  	<li role="presentation">
				                  		<a role="menuitem" tabindex="-1" href="{{route('admin.subscription.export' , ['format' => 'xls'])}}">
				                  			<span class="text-red"><b>{{tr('excel_sheet')}}</b></span>
				                  		</a>
				                  	</li>

				                  	<li role="presentation">
				                  		<a role="menuitem" tabindex="-1" href="{{route('admin.subscription.export' , ['format' => 'csv'])}}">
				                  			<span class="text-blue"><b>{{tr('csv')}}</b></span>
				                  		</a>
				                  	</li>
				                </ul>
							</li>
						</ul>

					@endif

	            <!-- EXPORT OPTION END -->

	            </div>

	            <div class="box-body table-responsive">

	              	<table id="example1" class="table table-bordered table-striped">

						<thead>
						    <tr>
								<th>{{tr('id')}}</th>
								<th>{{tr('subscription')}}</th>
								<th>{{tr('payment_id')}}</th>
								<th>{{tr('username')}}</th>
								<th>{{tr('plan')}}</th>
								<th>{{tr('amount')}}</th>
								<th>{{tr('payment_mode')}}</th>
								<th>{{tr('coupon_code')}}</th>
								<th>{{tr('coupon_amount')}}</th>
								<th>{{tr('plan_amount')}}</th>
								<th>{{tr('final_amount')}}</th>
								<th>{{tr('expiry_date')}}</th>
								<th>{{tr('is_coupon_applied')}}</th>
								<th>{{tr('coupon_reason')}}</th>
								<th>{{tr('status')}}</th>
						    </tr>
						</thead>

						<tbody>

							@if(count($data) > 0)

								@foreach($data as $i => $payment)

								    <tr>
								      	<td>{{$i+1}}</td>

								      	<td>	
								      		@if($payment->getSubscription)

								      			<a href="{{route('admin.subscriptions.view' , $payment->getSubscription->unique_id)}}">

								      				{{$payment->getSubscription ? $payment->getSubscription->title : ""}}

								      			</a>

							      			@else 
							      				{{$payment->getSubscription ? $payment->getSubscription->title : ""}}
							      			@endif

								      	</td>
								      	
								      	<td>{{$payment->payment_id}}</td>

								      	<td>
								      		<a href="{{route('admin.users.view' , $payment->user_id)}}"> @if($payment->user) {{$payment->user ? $payment->user->name : "-"}} @endif</a>
								      	</td>


								      	<td>{{$payment->getSubscription ? $payment->getSubscription->plan : ""}}</td>

								      	<td class="text-red"><b>{{Setting::get('currency')}} {{$payment->amount}}</b></td>

							   
							      		<td class="text-capitalize">{{$payment->payment_mode ?: tr('cod')}}</td>
								      	<td>{{$payment->coupon_code}}</td>

								      	<td>{{Setting::get('currency')}} {{$payment->coupon_amount? $payment->coupon_amount : "0.00"}}</td>

								      	<td>{{Setting::get('currency')}} {{$payment->subscription_amount ? $payment->subscription_amount : "0.00"}}</td>

								      	<td>{{Setting::get('currency')}} {{$payment->amount ? $payment->amount : "0.00" }}</td>
								      	
								      	<td>{{date('d M Y',strtotime($payment->expiry_date))}}</td>
								      	<td>
								      		@if($payment->is_coupon_applied)
											<span class="label label-success">{{tr('yes')}}</span>
											@else
											<span class="label label-danger">{{tr('no')}}</span>
											@endif
								      	</td>
								      	<td>
								      		{{$payment->coupon_reason ? $payment->coupon_reason : '-'}}
								      	</td>
								      	<td>
								      		@if($payment->status) 
								      			<span style="color: green;"><b>{{tr('paid')}}</b></span>
								      		@else
								      			<span style="color: red"><b>{{tr('not_paid')}}</b></span>

								      		@endif
								      	</td>
								    </tr>					

								@endforeach

							@endif

						</tbody>
					
					</table>
					
	            </div>

          	</div>
        </div>
    </div>

@endsection


