@extends('layouts.admin')

@section('title', tr('automatic_subscribers'))

@section('content-header', tr('automatic_subscribers').' - '.Setting::get('currency').$amount)

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li class="active"><i class="fa fa-key"></i> {{tr('automatic_subscribers')}}</li>
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
									      	<!-- <td>{{$payment->payment_id}}</td> -->
									      	<td>{{Setting::get('currency')}} {{$payment->amount}}</td>
									      	<td>{{$payment->expiry_date}}</td>
									      	<td class="text-center">
									      		<a data-toggle="modal" data-target="#{{$payment->id}}_cancel_subscription" class="pull-right btn btn-sm btn-danger">{{tr('cancel_subscription')}}</a>
							      			</td>
									    </tr>

									    <div class="modal fade error-popup" id="{{$payment->id}}_cancel_subscription" role="dialog">

					               <div class="modal-dialog">

					                   <div class="modal-content">

					                   		<form method="post" action="{{route('admin.cancel.subscription', ['id'=>$payment->id])}}">

					                       <div class="modal-body">

					                           <div class="media">

					                        		<div class="media-body">

					                                   <h4 class="media-heading">{{tr('reason')}} *</h4>


					                                   <textarea rows="5" name="cancel_reason" id='cancel_reason' required style="width: 100%"></textarea>

					                               </div>

					                           </div>

					                           <div class="text-right">

					                           		<br>

					                               <button type="submit" class="btn btn-primary top">{{tr('submit')}}</button>

					                           </div>

					                       </div>

					                       </form>

					                   </div>

					               </div>

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