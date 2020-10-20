@extends('layouts.admin')

@section('title',tr('view_coupon'))

@section('content-header',tr('view_coupon'))

@section('breadcrumb')


	<li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>

	<li><a href="{{route('admin.coupon.list')}}"><i class="fa fa-gift"></i>{{tr('coupons')}}</a></li>

	<li class="active">{{tr('view_coupon')}}</li>

@endsection

@section('content')

	<div class="row">

		<div class="col-md-6">

			<div class="box box-info">

				<div class="box-header label-primary">

					<a href="{{route('admin.edit.coupons',$view_coupon->id)}}" class="btn btn-warning pull-right">{{tr('edit')}}</a>

				</div>

				<div class="box box-body">
				
					<strong>{{tr('title')}}</strong>
					<h5 class="pull-right">{{$view_coupon->title}}</h5><hr>

					<strong>{{tr('coupon_code')}}</strong>
					<h4 class="pull-right" style="border: 2px solid #20bd99">{{$view_coupon->coupon_code}}</h4><hr>

					<strong>{{tr('amount_type')}}</strong>
						@if($view_coupon->amount_type == 0)
						<span class="label label-primary pull-right">{{tr('percentage')}}</span>
						@else
						<span class="label label-primary pull-right">{{tr('absoulte')}}</span>
						@endif
					<hr>
					<strong>{{tr('amount')}}</strong>
						@if($view_coupon->amount_type == 0)
						<span class="label label-primary pull-right">{{ formatted_amount($view_coupon->amount) }} % </span>
						@else
						<span class="label label-primary pull-right">{{ formatted_amount($view_coupon->amount) }}</span>
						@endif
					<hr>
					<strong>{{tr('expiry_date')}}</strong>

						<h5 class="pull-right">
							
							 {{date('d M y', strtotime($view_coupon->expiry_date))}} 
							
						</h5>
					<hr>
					<strong>{{tr('no_of_users_limit')}}</strong>

						<h5 class="pull-right">
							
							 {{$view_coupon->no_of_users_limit}} 
							
						</h5>
					<hr>
					<strong>{{tr('per_users_limit')}}</strong>

						<h5 class="pull-right">
							
							 {{$view_coupon->per_users_limit}} 
							
						</h5>
					<hr>
					<strong>{{tr('status')}}</strong>
						@if($view_coupon->status == 0)
						<span class="label label-warning pull-right">{{tr('inactive')}}</span>
						@else
						<span class="label label-success pull-right">{{tr('active')}}</span>
						@endif
					
					@if($view_coupon->description == '')

					@else
					<hr>
					<strong>{{tr('description')}}</strong><br>
						<?php echo $view_coupon->description ?>
					@endif
				</div>
			</div>
		</div>
	</div>


@endsection