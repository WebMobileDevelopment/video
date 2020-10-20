@extends('layouts.admin')

@section('title', tr('subscription_payments'))

@section('content-header')

{{  tr('subscription_payments')  }} - {{ formatted_amount(total_subscription_revenue($subscription_details ? $subscription_details->id : "")) }}

@endsection

@section('breadcrumb')
     
    <li class="active"><i class="fa fa-money"></i> {{ tr('subscription_payments') }}</li>
@endsection

@section('content')

	<div class="row">

        <div class="col-xs-12">
			
			@include('notification.notify')

          	<div class="box box-primary">

          		<div class="box-header label-primary">
	                <b style="font-size: 18px;">{{ tr('subscription_payments') }}</b>
	                <a href="{{ route('admin.users.index') }}" style="float:right" class="btn btn-default">{{ tr('view_users') }}</a>

	                <!-- EXPORT OPTION START -->

					@if(count($payments) > 0 )
	                
		                <ul class="admin-action btn btn-default pull-right" style="margin-right: 40px">
		                 	
							<li class="dropdown">
				                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
				                  {{ tr('export') }} <span class="caret"></span>
				                </a>
				                <ul class="dropdown-menu">
				                  	<li role="presentation">
				                  		<a role="menuitem" tabindex="-1" href="{{ route('admin.subscription.export' , ['format' => 'xlsx']) }}">
				                  			<span class="text-red"><b>{{ tr('excel_sheet') }}</b></span>
				                  		</a>
				                  	</li>

				                  	<li role="presentation">
				                  		<a role="menuitem" tabindex="-1" href="{{ route('admin.subscription.export' , ['format' => 'csv']) }}">
				                  			<span class="text-blue"><b>{{ tr('csv') }}</b></span>
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
								<th>{{ tr('id') }}</th>
								<th>{{ tr('subscription') }}</th>
								<th>{{ tr('payment_id') }}</th>
								<th>{{ tr('username') }}</th>
								<th>{{ tr('plan') }}</th>
								<th>{{ tr('amount') }}</th>
								<th>{{ tr('payment_mode') }}</th>
								<th>{{ tr('coupon_code') }}</th>
								<th>{{ tr('coupon_amount') }}</th>
								<th>{{ tr('plan_amount') }}</th>
								<th>{{ tr('final_amount') }}</th>
								<th>{{ tr('expiry_date') }}</th>
								<th>{{ tr('is_coupon_applied') }}</th>
								<th>{{ tr('coupon_reason') }}</th>
								<th>{{ tr('status') }}</th>
						    </tr>
						</thead>

						<tbody>

							@if(count($payments) > 0)

								@foreach($payments as $i => $payment_details)

								    <tr>
								      	<td>{{ $i+1 }}</td>

								      	<td>	
								      		@if($payment_details->getSubscription)

								      			<a href="{{ route('admin.subscriptions.view' , ['subscription_id' => $payment_details->getSubscription->id] ) }}">

								      				{{ $payment_details->getSubscription ? $payment_details->getSubscription->title : "" }}

								      			</a>

							      			@else 
							      				{{ $payment_details->getSubscription ? $payment_details->getSubscription->title : "" }}
							      			@endif

								      	</td>
								      	
								      	<td>{{ $payment_details->payment_id }}</td>

								      	<td>
								      		<a href="{{route('admin.users.view',['user_id' => $payment_details->user_id])}}">{{$payment_details->user ?$payment_details->user->name: "-"}}</a>
								      	</td>


								      	<td>{{ $payment_details->getSubscription ? $payment_details->getSubscription->plan : "" }}</td>

								      	<td class="text-red"><b>{{ formatted_amount($payment_details->amount) }}</b></td>

							   
							      		<td class="text-capitalize">{{ $payment_details->payment_mode }}</td>
								      	<td>{{ $payment_details->coupon_code }}</td>

								      	<td>{{ formatted_amount($payment_details->coupon_amount? $payment_details->coupon_amount : "0.00") }}</td>

								      	<td>{{ formatted_amount($payment_details->subscription_amount ? $payment_details->subscription_amount : "0.00") }}</td>

								      	<td>{{ formatted_amount($payment_details->amount ? $payment_details->amount : "0.00")  }}</td>
								      	
								      	<td>{{ date('d M Y',strtotime($payment_details->expiry_date)) }}</td>
								      	<td>
								      		@if($payment_details->is_coupon_applied)
											<span class="label label-success">{{ tr('yes') }}</span>
											@else
											<span class="label label-danger">{{ tr('no') }}</span>
											@endif
								      	</td>
								      	<td>
								      		{{ $payment_details->coupon_reason ? $payment_details->coupon_reason : '-' }}
								      	</td>
								      	<td>
								      		@if($payment_details->status) 
								      			<span style="color: green;"><b>{{ tr('paid') }}</b></span>
								      		@else
								      			<span style="color: red"><b>{{ tr('not_paid') }}</b></span>

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


