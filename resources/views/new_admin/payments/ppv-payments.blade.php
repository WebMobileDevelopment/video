@extends('layouts.admin')

@section('title', tr('ppv_payments'))

@section('content-header')

{{ tr('ppv_payments')}} - {{ formatted_amount(total_video_revenue('admin')) }}

@endsection

@section('breadcrumb')

    <li><a href="{{ route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{ tr('home')}}</a></li>

    <li class="active"><i class="fa fa-credit-card	"></i> {{ tr('ppv_payments')}}</li>

@endsection

@section('styles')

<style>
	.modal-body li {
		padding: 12px 5px;
	}
</style>
@endsection

@section('content')

	<div class="row">

        <div class="col-xs-12">

        	@include('notification.notify')

          	<div class="box box-info">  

         		<div class="box-header label-primary">

                <!-- EXPORT OPTION START -->

					@if(count($payments) > 0 )
	                
		                <ul class="admin-action btn btn-default pull-right" style="margin-right: 50px">
		                 	
							<li class="dropdown">
				                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
				                  {{ tr('export')}} <span class="caret"></span>
				                </a>
				                <ul class="dropdown-menu">
				                  	<li role="presentation">
				                  		<a role="menuitem" tabindex="-1" href="{{ route('admin.payperview.export' , ['format' => 'xls'])}}">
				                  			<span class="text-red"><b>{{ tr('excel_sheet')}}</b></span>
				                  		</a>
				                  	</li>

				                  	<li role="presentation">
				                  		<a role="menuitem" tabindex="-1" href="{{ route('admin.payperview.export' , ['format' => 'csv'])}}">
				                  			<span class="text-blue"><b>{{ tr('csv')}}</b></span>
				                  		</a>
				                  	</li>
				                </ul>
							</li>
						</ul>

					@endif

	            <!-- EXPORT OPTION END -->
            	</div>
            	<div class="box-body">
            	@if(count($payments) > 0)

	              	<table id="example1" class="table table-bordered table-striped">

						<thead>
						    <tr>
								<th>{{ tr('id')}}</th>
								<th>{{ tr('video')}}</th>
								<th>{{ tr('username')}}</th>
								<th>{{ tr('payment_id')}}</th>
								<th>{{ tr('payment_mode')}}</th>
								<th>{{ tr('amount')}}</th>
								<th>{{ tr('reason')}}</th>
								<th>{{ tr('status')}}</th>
								<th>{{ tr('action')}}</th>
						    </tr>
						</thead>

						<tbody>

							@foreach($payments as $i => $payment_details)

							    <tr>

							      	<td>{{ $i+1}}</td>

							      	<td>
							      		@if($payment_details->title)

							      			<a href="{{ route('admin.video_tapes.view' , array('video_tape_id' => $payment_details->video_id))}}">
							      				{{ substr($payment_details->title,0,25) }}...
							      			</a>

							      		@else
							      		 	-
							      		@endif
							      	</td>

							      	<td>
							      		@if($payment_details->user_name)

							      		<a href="{{ route('admin.users.view' , ['user_id' => $payment_details->user_id] )}}"> 
							      			{{ $payment_details->user_name ? $payment_details->user_name : " "}} 
							      		</a>

							      		@endif
									</td>

							      	<td>{{ $payment_details->payment_id}}</td>

							      	<td>{{ $payment_details->payment_mode}}</td>

							      	<td>{{ formatted_amount($payment_details->amount) }}</td>

							      	<td>{{ $payment_details->reason}}</td>

							      	<td>
							      		@if($payment_details->amount <= 0)

							      			<label class="label label-danger">{{ tr('not_paid')}}</label>

							      		@else
							      			<label class="label label-success">{{ tr('paid')}}</label>

							      		@endif 
							      	</td>

							      	<td>
							      		<a href="" data-toggle="modal" data-target="#PPV_DETAILS_{{ $payment_details->id }}" class="btn btn-sm btn-info" href="{{route('admin.revenues.ppv_payments.view', ['id' => $payment_details->id] )}}" title="{{ tr('view')}}">
							      		<b><i class="fa fa-eye"></i></b></a>
							      	</td>
							    </tr>	

								<div class="modal fade" id="PPV_DETAILS_{{ $payment_details->id}}" role="dialog">

									<div class="modal-dialog modal-lg">

										<div class="modal-content">

											<div class="modal-header">

												<button type="button" class="close" data-dismiss="modal">&times;</button>

												<h4 class="modal-title">{{ $payment_details->payment_id}}</h4>

											</div>

											<div class="modal-body">

												<ul>
													<li>
														{{ tr('video')}} : @if($payment_details->title)

										      			<a href="{{ route('admin.video_tapes.view' , array('video_tape_id' => $payment_details->video_id))}}">
										      				{{ $payment_details->title}}
										      			</a>

											      		@else
											      		 	-
											      		@endif
										      		</li>

										      		<li>
										      			{{ tr('username')}} :

										      			<a href="{{ route('admin.users.view' , ['user_id' => $payment_details->user_id] )}}"> 
							      							{{ $payment_details->user_name ? $payment_details->user_name : "-"}} 
							      						</a>
										      		</li>

										      		<li>{{ tr('total')}} : {{ formatted_amount($payment_details->amount) }}</li>

										      		<li>{{ tr('admin_ppv_commission')}} : {{ formatted_amount($payment_details->admin_ppv_amount) }}</li>

										      		<li>{{ tr('user_ppv_commission')}} : {{ formatted_amount($payment_details->user_ppv_amount) }}</li>

										      		<li>{{ tr('reason')}} : {{ $payment_details->reason}}</li>

										      		<li>{{ tr('paid_date')}} : {{ date('d M Y',strtotime($payment_details->created_at))}}</li>

										      		<li>{{ tr('type_of_subscription')}} : {{ $payment_details->type_of_subscription}}</li>

										      		<li>{{ tr('type_of_user')}} : {{ $payment_details->type_of_user}}</li>

										      		<li>{{ tr('coupon_code')}} : {{ $payment_details->coupon_code}}</li>

											      	<li>{{ tr('coupon_amount')}} : {{ formatted_amount($payment_details->coupon_amount? $payment_details->coupon_amount : "0.00") }}</li>

											      	<li>{{ tr('plan_amount')}} : {{ formatted_amount($payment_details->ppv_amount ? $payment_details->ppv_amount : "0.00") }}</li>

											      	<li>{{ tr('final_amount')}} : {{ formatted_amount($payment_details->amount ? $payment_details->amount : "0.00") }}</li>

											      	<li>{{ tr('is_coupon_applied')}} : 	
											      	@if($payment_details->is_coupon_applied)
											
													<span class="label label-success">{{ tr('yes')}}</span>
													@else
													<span class="label label-danger">{{ tr('no')}}</span>
													@endif
													</li>

											      	<li>{{ tr('coupon_reason')}} : {{ $payment_details->coupon_reason ? $payment_details->coupon_reason : '-'}}</li>

												</ul>
											</div>

											<div class="modal-footer">
												<button type="button" class="btn btn-default" data-dismiss="modal">{{ tr('close')}}</button>
											</div>
										
										</div>

									</div>

								</div>

							@endforeach

						</tbody>

					</table>

				@else
					<h3 class="no-result">{{ tr('no_result_found')}}</h3>
				@endif
	            </div>

	          	</div>

      		</div>

    </div>

@endsection


