@extends('layouts.admin')

@section('title', tr('live_video_payments'))

@section('content-header',tr('live_video_payments'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li class="active"><i class="fa fa-credit-card"></i> {{tr('live_video_payments')}}</li>
@endsection

@section('content')

@include('notification.notify')

	<div class="row">
        <div class="col-xs-12">

          <div class="box">
          	<div class="box-header label-primary">

          	<!-- EXPORT OPTION START -->

					@if(count($data) > 0 )
	                
		                <ul class="admin-action btn btn-default pull-right" style="margin-right: 50px">
		                 	
							<li class="dropdown">
				                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
				                  {{tr('export')}} <span class="caret"></span>
				                </a>
				                <ul class="dropdown-menu">
				                  	<li role="presentation">
				                  		<a role="menuitem" tabindex="-1" href="{{route('admin.livevideo-payment.export' , ['format' => 'xls'])}}">
				                  			<span class="text-red"><b>{{tr('excel_sheet')}}</b></span>
				                  		</a>
				                  	</li>

				                  	<li role="presentation">
				                  		<a role="menuitem" tabindex="-1" href="{{route('admin.livevideo-payment.export' , ['format' => 'csv'])}}">
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
            	@if(count($data) > 0)

	              	<table id="example1" class="table table-bordered table-striped">

						<thead>
						    <tr>
						      <th>{{tr('id')}}</th>
								<th>{{tr('title')}}</th>
								<th>{{tr('user_name')}}</th>
								<th>{{tr('payment_id')}}</th>
								<th>{{tr('amount')}}</th>
								<th>{{tr('admin_commission')}}</th>
								<th>{{tr(('user_commission'))}}</th>
								<th>{{tr(('payment_mode'))}}</th>
								<th>{{tr('coupon_code')}}</th>
						      	<th>{{tr('coupon_amount')}}</th>
						      	<th>{{tr('plan_amount')}}</th>
						      	<th>{{tr('final_amount')}}</th>
						      	<th>{{tr('is_coupon_applied')}}</th>
						      	<th>{{tr('coupon_reason')}}</th>
								<th>{{tr('status')}}</th>
						    </tr>
						</thead>

						<tbody>

							@foreach($data as $i => $value)

							    <tr>
							      	<td>{{$i+1}}</td>
							      	<td><a target="_blank" href="{{route('admin.live-videos.view' , $value->id)}}">{{$value->getVideoPayment ? $value->getVideoPayment->title : ''}}</a></td>

							      	<td><a  href="{{route('admin.users.view' , ['user_id' => $value->user_id])}}"> {{$value->user ? $value->user->name : '-'}} </a></td>
							      	<td>{{$value->payment_id}}</td>
							      	<td>{{Setting::get('currency')}}{{$value->amount ? $value->amount : 0}}</td>

										<td>{{Setting::get('currency')}}{{$value->admin_amount ? $value->admin_amount : 0}}</td>

										<td>{{Setting::get('currency')}}{{$value->user_amount ? $value->user_amount : 0}}</td>
										<td class="text-capitalize">{{$value->payment_mode ?: tr('cod')}}</td>
										<td>{{$value->coupon_code}}</td>

							      	<td>{{Setting::get('currency')}} {{$value->coupon_amount? $value->coupon_amount : "0.00"}}</td>

							      	<td>{{Setting::get('currency')}} {{$value->live_video_amount ? $value->live_video_amount : "0.00"}}</td>

							      	<td>{{Setting::get('currency')}} {{$value->amount ? $value->amount : "0.00" }}</td>
							      	
							      	<td>
							      		@if($value->is_coupon_applied)
										<span class="label label-success">{{tr('yes')}}</span>
										@else
										<span class="label label-danger">{{tr('no')}}</span>
										@endif
							      	</td>
							      	<td>
							      		{{$value->coupon_reason ? $value->coupon_reason : '-'}}
							      	</td>
										<td>
											@if($value->amount > 0)
												<span class="label label-success">{{tr('paid')}}</span>
											@else
												<span class="label label-danger">{{tr('not_paid')}}</span>
											@endif
										</td>
							    </tr>					

							@endforeach
						</tbody>
					</table>
				@else
					<h3 class="no-result">{{tr('no_result_found')}}</h3>
				@endif
            </div>
          </div>
        </div>
    </div>

@endsection


