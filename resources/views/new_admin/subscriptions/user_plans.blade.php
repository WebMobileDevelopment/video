@extends('layouts.admin')

@section('title', tr('subscriptions'))

@section('content-header')

{{tr('subscriptions')}}

<a href="{{route('admin.users.view', ['user_id' => $user_details->id])}}">

	- {{$user_details->name}}
	
</a>

@endsection

@section('breadcrumb')
     
    <li><a href="{{ route('admin.users.index') }}"><i class="fa fa-user"></i>{{ tr('users') }}</a></li>
    
    <li><a href="{{ route('admin.users.view',['user_id' => $user_details->id] ) }}"><i class="fa fa-user"></i>{{ tr('view_user') }}</a></li>

    <li class="active"><i class="fa fa-key"></i> {{ tr('subscriptions') }}</li>
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

	            	@if(count($payments) > 0)

		              	<table id="example1" class="table table-bordered table-striped">

							<thead>
							    <tr>
							      	<th>{{ tr('id') }}</th>
							      	<th>{{ tr('subscription') }}</th>
							      	<th>{{ tr('payment_id') }}</th>
							      	<th>{{ tr('amount') }}</th>
							      	<th>{{ tr('expiry_date') }}</th>
							      	<th>{{ tr('reason') }}</th>
					      			<th>{{ tr('action') }}</th>
							    </tr>
							</thead>

							<tbody>

								@foreach($payments as $i => $payment_details)

								    <tr>
								      	<td>{{ $i+1 }}</td>

								      	<td><a href="{{ route('admin.subscriptions.view' , ['subscription_id' => $payment_details->subscription_id] ) }}" > {{ $payment_details->title }}</a> 
								      	</td>
								      	
								      	<td>{{ $payment_details->payment_id }}</td>
								      	
								      	<td>{{ formatted_amount($payment_details->amount) }}</td>
								      	
								      	<td>{{ date('d M Y',strtotime($payment_details->expiry_date)) }}</td>
								      	
								      	<td>{{ $payment_details->cancel_reason }}</td>
								      	
								      	<td class="text-center">

								      		@if($i == 0 && !$payment_details->is_cancelled && $payment_details->status == PAID_STATUS) 
								      		<a data-toggle="modal" data-target="#{{ $payment_details->id }}_cancel_subscription" class="pull-right btn btn-sm btn-danger">{{ tr('cancel_subscription') }}</a>

								      		@elseif($i == 0 && $payment_details->is_cancelled && $payment_details->status == PAID_STATUS)

							      				<?php $enable_subscription_notes = tr('enable_subscription_notes') ; ?>
							      			
							      				<a onclick="return confirm('{{ $enable_subscription_notes }}')" href="{{ route('admin.subscription.auto-renewal.enable', ['user_id'=> $payment_details->user_id]) }}" class="pull-right btn btn-sm btn-success">{{ tr('enable_subscription') }}</a>
							      			@else
							      				-							      				
								      		@endif

						      			</td>
									</tr>

								    <div class="modal fade error-popup" id="{{ $payment_details->id }}_cancel_subscription" role="dialog">

				               <div class="modal-dialog">

				                   <div class="modal-content">

				                   		<form method="post" action="{{ route('admin.subscription.auto-renewal.disable', ['id' => $payment_details->id]) }}">

					                        <div class="modal-body">

					                           <div class="media">

					                        		<div class="media-body">

					                                   <h4 class="media-heading">{{ tr('reason') }} *</h4>

					                                   <textarea rows="5" name="cancel_reason" id='cancel_reason' required style="width: 100%"></textarea>

					                               </div>

					                           </div>

					                           <div class="text-right">

					                           		<br>

					                               <button type="submit" class="btn btn-primary top">{{ tr('submit') }}</button>

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
					@else
						<h3 class="no-result">{{ tr('no_subscription_found') }}</h3>
					@endif

	            </div>
          	</div>

        </div>

    </div>

	<div class="row">

		<div class="col-md-12">

			<div class="row">

				@if(count($subscriptions) > 0)

					@foreach($subscriptions as $s => $subscription_detail)

						<div class="col-md-4 col-lg-4 col-sm-6 col-xs-12">

							<div class="thumbnail">

								<div class="caption">

									<h3>
										<a target="_blank" href="{{ route('admin.subscriptions.view' , ['subscription_id' => $subscription_detail->id] ) }}">{{ $subscription_detail->title }}</a>
									</h3>

									<div class="subscription-desc">
										<?php echo $subscription_detail->description; ?>
									</div>

									<br>

									<p>
										<span class="btn btn-danger pull-left" style="cursor: default;">{{ formatted_amount($subscription_detail->amount) }} / {{ $subscription_detail->plan }} M</span>

										<a href="{{ route('admin.users.subscription.save' , ['subscription_id' => $subscription_detail->id, 'user_id' => $user_details->id]) }}" class="btn btn-success pull-right" onclick="return confirm(&quot;{{ tr('admin_user_subscription_confirm', $subscription_detail->title) }}&quot;)">{{ tr('choose') }}</a>
									</p>

									<br>

									<br>

								</div>
							
							</div>
						
						</div>

					@endforeach

				@endif
				
			</div>

		</div>
		
	</div>

@endsection