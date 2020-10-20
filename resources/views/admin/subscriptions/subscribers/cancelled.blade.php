@extends('layouts.admin')

@section('title', tr('cancelled_subscribers'))

@section('content-header', tr('cancelled_subscribers'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li class="active"><i class="fa fa-key"></i> {{tr('cancelled_subscribers')}}</li>
@endsection

@section('styles')

<style>

.subscription-image {
	overflow: hidden !important;
	position: relative !important;
	height: 15em !important;
	background-position: center !important;
	background-repeat: no-repeat !important;
	background-size: cover !important;
	margin: 0 !important;
	width: 100%;
}


.subscription-desc {
    max-height: 100px;
    overflow-y: auto;
    margin-bottom: 10px !important;
    min-height: 100px;
}

</style>

@endsection

@section('content')

			<div class="row">
		        <div class="col-xs-12">

		        	@include('notification.notify')

		          <div class="box">
		            <div class="box-body table-responsive">


			              	<table id="example1" class="table table-bordered table-striped">

								<thead>
								    <tr>
								      <th>{{tr('id')}}</th>
								      <th>{{tr('username')}}</th>
								      <th>{{tr('subscription_name')}}</th>
								      <!-- <th>{{tr('payment_id')}}</th> -->
								      <th>{{tr('amount')}}</th>
								      <th>{{tr('expiry_date')}}</th>
								       <th>{{tr('reason')}}</th>
						      			<th>{{tr('action')}}</th>
								    </tr>
								</thead>

								<tbody>

									@foreach($payments as $i => $payment)

									    <tr>
									      	<td>{{$i+1}}</td>
									      	<td>@if($payment->user_name)<a href="{{route('admin.users.view' , $payment->user_id)}}"> {{($payment->user_name) ? $payment->user_name : ''}} </a>@endif</td>
									      	<td>
									      		@if($payment->subscription_name)
									      			<a href="{{route('admin.subscriptions.view' , $payment->unique_id)}}" target="_blank"> {{($payment->subscription_name) ? $payment->subscription_name : ''}} </a>
									      		@endif
									      	</td>
									      	<td>{{Setting::get('currency')}} {{$payment->amount}}</td>
									      	<td>{{$payment->expiry_date}}</td>
									      	<td>{{$payment->cancel_reason}}</td>
									      	<td class="text-center">

							      				<?php $enable_subscription_notes = tr('enable_subscription_notes') ; ?>
							      			
							      				<a onclick="return confirm('{{$enable_subscription_notes}}')" href="{{route('admin.enable.subscription', ['id'=>$payment->user_id])}}" class="pull-right btn btn-sm btn-success">{{tr('enable_subscription')}}</a>
							      			</td>
									    </tr>


					           </div>

									@endforeach

								</tbody>
							</table>

							<div>
								
							</div>
	

		            </div>
		          </div>
		        </div>
		    </div>


@endsection