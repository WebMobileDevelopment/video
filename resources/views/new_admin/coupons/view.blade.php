@extends('layouts.admin')

@section('title',tr('view_coupon'))

@section('content-header',tr('view_coupon'))

@section('breadcrumb')

	<li><a href="{{ route('admin.coupons.index') }}"><i class="fa fa-gift"></i>{{ tr('coupons') }}</a></li>

	<li class="active">{{ tr('view_coupon') }}</li>

@endsection

@section('content')

	<div class="row">

		<div class="col-md-12">
			
			@include('notification.notify')

			<div class="box ">

				<div class="box-header label-primary">
				<b style="font-size: 18px;">{{ tr('coupons') }}</b>

					<div class="pull-right">
					
						@if(Setting::get('admin_delete_control') == YES )
							
							<a class="btn btn-warning" class="btn btn-warning" href="javascript:;" title="{{ tr('edit') }}"><b><i class="fa fa-edit"></i></a>
										

							<a class="btn btn-danger" class="btn btn-warning" href="javascript:;" onclick="return confirm(&quot;{{ tr('admin_coupon_delete_confirmation', $coupon_details->title ) }}&quot;);" title="{{ tr('delete') }}" ><b><i class="fa fa-trash"></i></b></a>

						@else

							<a class="btn btn-warning" href="{{ route('admin.coupons.edit', ['coupon_id' => $coupon_details->id] ) }}" title="{{ tr('edit') }}"><b><i class="fa fa-edit"></i></a>									

							<a class="btn btn-danger" href="{{ route('admin.coupons.delete', ['coupon_id' => $coupon_details->id]) }}" onclick="return confirm(&quot;{{ tr('admin_coupon_delete_confirmation', $coupon_details->title ) }}&quot;);" title="{{ tr('delete') }}" ><b><i class="fa fa-trash"></i></b></a>

						@endif

						@if($coupon_details->status == DECLINED)

							<a class="btn btn-success" href="{{ route('admin.coupons.status',['coupon_id' => $coupon_details->id] ) }}" onclick="return confirm(&quot;{{ tr('admin_coupon_approve_confirmation', $coupon_details->title) }}&quot;)" title="{{ tr('approve') }}"><b><i class="fa fa-check" ></i></b></a>

						@else

							<a class="btn btn-warning" href="{{ route('admin.coupons.status',['coupon_id' => $coupon_details->id] ) }}" onclick="return confirm(&quot;{{ tr('admin_coupon_decline_confirmation', $coupon_details->title) }}&quot;)" title="{{ tr('decline') }}"><b><i class="fa fa-close"></i></b></a>

						@endif

					</div>

				</div>

				<div class="box box-body">

					<div class="col-md-6">

						<strong>{{ tr('title') }}</strong>
						<h5 class="pull-right">{{ $coupon_details->title }}</h5><hr>

						<strong>{{ tr('coupon_code') }}</strong>
						<h4 class="pull-right" style="border: 2px solid #20bd99">{{ $coupon_details->coupon_code }}</h4><hr>

						<strong>{{ tr('amount_type') }}</strong>
							@if($coupon_details->amount_type == PERCENTAGE)
							<span class="label label-primary pull-right">{{ tr('percentage') }}</span>
							@else
							<span class="label label-primary pull-right">{{ tr('absoulte') }}</span>
							@endif
						<hr>

						<strong>{{ tr('amount') }}</strong>
							@if($coupon_details->amount_type == PERCENTAGE)
							<span class="label label-primary pull-right">{{ formatted_amount($coupon_details->amount) }} % </span>
							@else
							<span class="label label-primary pull-right">{{ formatted_amount($coupon_details->amount) }}</span>
							@endif				

					</div>

					<div class="col-md-6">

						<strong>{{ tr('expiry_date') }}</strong>
							<h5 class="pull-right">								
								 {{ date('d M y', strtotime($coupon_details->expiry_date)) }} 
							</h5>
						<hr>

						<strong>{{ tr('no_of_users_limit') }}</strong>
							<h5 class="pull-right">								
								 {{ $coupon_details->no_of_users_limit }} 								
							</h5>
						<hr>

						<strong>{{ tr('per_users_limit') }}</strong>
							<h5 class="pull-right">							
								 {{ $coupon_details->per_users_limit }} 						
							</h5>
						<hr>

						<strong>{{ tr('status') }}</strong>
							@if($coupon_details->status == APPROVED)
								<span class="label label-success pull-right">{{ tr('active') }}</span>
							@else
								<span class="label label-warning pull-right">{{ tr('inactive') }}</span>
							@endif
					</div>
					<div class="col-md-12">					
						
						@if($coupon_details->description == '')

						@else
							<hr>

							<strong>{{ tr('description') }}</strong><br>
							<?php echo $coupon_details->description ?>
						@endif
					</div>

				</div>

			</div>

		</div>

	</div>


@endsection