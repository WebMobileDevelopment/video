@extends('layouts.admin')

@section('title',tr('edit_coupon'))

@section('content-header',tr('edit_coupon'))

@section('breadcrumb')

	 

	<li><a href="{{route('admin.coupons.index')}}"><i class="fa fa-gift"></i>{{tr('coupons')}}</a></li>

	<li class="active">{{tr('edit_coupon')}}</li>

@endsection

@section('content')

	<div class="row">

		<div class="col-md-12">
			
			@include('notification.notify')

			<div class="box box-primary">

				<div class="box-header label-primary">

					<b style="font-size: 18px">{{tr('edit_coupon')}}</b>

					<a href="{{route('admin.coupons.index')}}" class="btn btn-default pull-right"> {{tr('coupons')}}</a>

				</div>

				@include('new_admin.coupons._form')

			</div>

		</div>
		
	</div>
	
@endsection