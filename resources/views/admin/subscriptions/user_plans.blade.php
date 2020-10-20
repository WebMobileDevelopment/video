@extends('layouts.admin')

@section('title', tr('subscriptions'))

@section('content-header', tr('subscriptions'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li><a href="{{route('admin.users')}}"><i class="fa fa-users"></i>{{tr('users')}}</a></li>
    @if($id)
    	<li><a href="{{route('admin.users.view', $id)}}"><i class="fa fa-users"></i>{{tr('view_user')}}</a></li>
    @endif
    <li class="active"><i class="fa fa-key"></i> {{tr('subscriptions')}}</li>
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
								      <th>{{tr('id')}}</th>
								      <th>{{tr('subscription')}}</th>
								      <th>{{tr('payment_id')}}</th>
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
									      	<td>{{$payment->title}}</td>
									      	<td>{{$payment->payment_id}}</td>
									      	<td>{{Setting::get('currency')}} {{$payment->amount}}</td>
									      	<td>{{date('d M Y',strtotime($payment->expiry_date))}}</td>
									      	<td>{{$payment->cancel_reason}}</td>
									      	<td class="text-center">

									      		@if($i == 0 && !$payment->is_cancelled && $payment->status == PAID_STATUS) 
									      		<a data-toggle="modal" data-target="#{{$payment->id}}_cancel_subscription" class="pull-right btn btn-sm btn-danger">{{tr('cancel_subscription')}}</a>

									      		@elseif($i == 0 && $payment->is_cancelled && $payment->status == PAID_STATUS)

								      				<?php $enable_subscription_notes = tr('enable_subscription_notes') ; ?>
								      			
								      				<a onclick="return confirm('{{$enable_subscription_notes}}')" href="{{route('admin.enable.subscription', ['id'=>$payment->user_id])}}" class="pull-right btn btn-sm btn-success">{{tr('enable_subscription')}}</a>


								      			@else

								      				-
								      				

									      		@endif

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
						@else
							<h3 class="no-result">{{tr('no_subscription_found')}}</h3>
						@endif

		            </div>
		          </div>
		        </div>
		    </div>


			<div class="row">

				<div class="col-md-12">

					

					<div class="row">

						@if(count($subscriptions) > 0)

							@foreach($subscriptions as $s => $subscription)

								<div class="col-md-4 col-lg-4 col-sm-6 col-xs-12">

									<div class="thumbnail">

										<?php /*<img alt="{{$subscription->title}}" src="{{$subscription->picture ?  $subscription->picture : asset('common/img/landing-9.png')}}" class="subscription-image" /> */?>

										<div class="caption">

											<h3>
												<a target="_blank" href="{{route('admin.subscriptions.view' , $subscription->unique_id)}}">{{$subscription->title}}</a>
											</h3>

											<div class="subscription-desc">
												<?php echo $subscription->description; ?>
											</div>

											<br>

											<p>
												<span class="btn btn-danger pull-left" style="cursor: default;">{{ Setting::get('currency')}} {{$subscription->amount}} / {{$subscription->plan}} M</span>

												<a href="{{route('admin.users.subscription.save' , ['s_id' => $subscription->id, 'u_id'=>$id])}}" class="btn btn-success pull-right" onclick="return confirm('Are You Sure ?')">{{tr('choose')}}</a>

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