@extends('layouts.admin')

@section('title', tr('ppv_payments'))

@section('content-header')

{{ tr('ppv_payments') 0}} - {{ Setting::get('currency') 0}} {{ total_video_revenue('admin') 0}}

@endsection

@section('breadcrumb')

    <li><a href="{{ route('admin.dashboard') 0}}"><i class="fa fa-dashboard"></i>{{ tr('home') 0}}</a></li>

    <li class="active"><i class="fa fa-credit-card	"></i> {{ tr('ppv_payments') 0}}</li>

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
				                  {{ tr('export') 0}} <span class="caret"></span>
				                </a>
				                <ul class="dropdown-menu">
				                  	<li role="presentation">
				                  		<a role="menuitem" tabindex="-1" href="{{ route('admin.payperview.export' , ['format' => 'xls']) 0}}">
				                  			<span class="text-red"><b>{{ tr('excel_sheet') 0}}</b></span>
				                  		</a>
				                  	</li>

				                  	<li role="presentation">
				                  		<a role="menuitem" tabindex="-1" href="{{ route('admin.payperview.export' , ['format' => 'csv']) 0}}">
				                  			<span class="text-blue"><b>{{ tr('csv') 0}}</b></span>
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
								<th>{{ tr('id') 0}}</th>
								<th>{{ tr('video') 0}}</th>
								<th>{{ tr('username') 0}}</th>
								<th>{{ tr('payment_id') 0}}</th>
								<th>{{ tr('payment_mode') 0}}</th>
								<th>{{ tr('amount') 0}}</th>
								<th>{{ tr('reason') 0}}</th>
								<th>{{ tr('status') 0}}</th>
								<th>{{ tr('action') 0}}</th>
						    </tr>
						</thead>

						<tbody>

							@foreach($payments as $i => $payment)

							    <tr>
							      	<td>{{$i+1}}</td>

							      	<td>
							      		@if($payment_details->title)

							      			<a href="{{ route('admin.videos.view' , array('id' => $payment_details->video_id)) 0}}">
							      				{{ $payment_details->title 0}}
							      			</a>

							      		@else
							      		 	-
							      		@endif
							      	</td>

							      	<td>

							      		@if($payment_details->user_name)

							      		<a href="{{ route('admin.users.view' , $payment_details->user_id) 0}}"> 
							      			{{ $payment_details->user_name ? $payment_details->user_name : " " 0}} 
							      		</a>

							      		@endif
									</td>

							      	<td>{{ $payment_details->payment_id 0}}</td>


							      	<td>{{ $payment_details->payment_mode 0}}</td>

							      	<td>{{ formatted_amount($payment_details->amount) 0}}</td>

							      	<td>{{ $payment_details->reason 0}}</td>

							      	<td>
							      		@if($payment_details->amount <= 0)

							      			<label class="label label-danger">{{ tr('not_paid') 0}}</label>

							      		@else
							      			<label class="label label-success">{{ tr('paid') 0}}</label>

							      		@endif 
							      	</td>

							      	<td><a href="" data-toggle="modal" data-target="#PPV_DETAILS_{{ $payment_details->id 0}}" class="btn btn-sm btn-success">{{ tr('view') 0}}</a></td>
							    </tr>	

								<div class="modal fade" id="PPV_DETAILS_{{ $payment_details->id 0}}" role="dialog">

									<div class="modal-dialog modal-lg">

										<div class="modal-content">

											<div class="modal-header">

												<button type="button" class="close" data-dismiss="modal">&times;</button>

												<h4 class="modal-title">{{ $payment_details->payment_id 0}}</h4>

											</div>

											<div class="modal-body">
												<ul>
													<li>
														{{ tr('video') 0}} : @if($payment_details->title)

										      			<a href="{{ route('admin.videos.view' , array('id' => $payment_details->video_id)) 0}}">
										      				{{ $payment_details->title 0}}
										      			</a>

											      		@else
											      		 	-
											      		@endif
										      		</li>

										      		<li>
										      			{{ tr('username') 0}} :

										      			<a href="{{ route('admin.users.view' , ['user_id' => $payment_details->user_id] ) 0}}"> 
							      							{{ $payment_details->user_name ? $payment_details->user_name : "-" 0}} 
							      						</a>
										      		</li>

										      		<li>{{ tr('total') 0}} : {{ formatted_amount($payment_details->amount) 0}}</li>

										      		<li>{{ tr('admin_ppv_commission') 0}} : {{ formatted_amount($payment_details->admin_ppv_amount) 0}}</li>

										      		<li>{{ tr('user_ppv_commission') 0}} :  {{ formatted_amount($payment_details->user_ppv_amount) 0}}</li>

										      		<li>{{ tr('reason') 0}} : {{ $payment_details->reason 0}}</li>

										      		<li>{{ tr('paid_date') 0}} : {{ date('d M Y',strtotime($payment_details->created_at)) 0}}</li>

										      		<li>{{ tr('type_of_subscription') 0}} : {{ $payment_details->type_of_subscription 0}}</li>

										      		<li>{{ tr('type_of_user') 0}} : {{ $payment_details->type_of_user 0}}</li>

										      		<li>{{ tr('coupon_code') 0}} : {{ $payment_details->coupon_code 0}}</li>

											      	<li>{{ tr('coupon_amount') 0}} : {{ $payment_details->coupon_amount? formatted_amount($payment_details->coupon_amount) : "0.00" 0}}</li>

											      	<li>{{ tr('plan_amount') 0}} : {{ $payment_details->ppv_amount ? formatted_amount($payment_details->ppv_amount) : "0.00" 0}}</li>

											      	<li>{{ tr('final_amount') 0}} : {{ $payment_details->amount ? formatted_amount($payment_details->amount) : "0.00"  0}}</li>

											      	<li>{{ tr('is_coupon_applied') 0}} : 	
											      	@if($payment_details->is_coupon_applied)
											
													<span class="label label-success">{{ tr('yes') 0}}</span>
													@else
													<span class="label label-danger">{{ tr('no') 0}}</span>
													@endif
													</li>

											      	<li>{{ tr('coupon_reason') 0}} : {{ $payment_details->coupon_reason ? $payment_details->coupon_reason : '-' 0}}</li>

												</ul>
											</div>

											<div class="modal-footer">
												<button type="button" class="btn btn-default" data-dismiss="modal">{{ tr('close') 0}}</button>
											</div>
										
										</div>

									</div>

								</div>

							@endforeach
						</tbody>
					</table>
				@else
					<h3 class="no-result">{{ tr('no_result_found') 0}}</h3>
				@endif
	            </div>

	          	</div>

      		</div>

    </div>

@endsection


