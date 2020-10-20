@extends('layouts.admin')

@section('title', tr('redeems'))

@section('content-header', tr('redeems'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li><a href="{{route('admin.users')}}"><i class="fa fa-user"></i> {{tr('users')}}</a></li>
    <li class="active"><i class="fa fa-trophy"></i> {{tr('redeems')}}</li>
@endsection

@section('content')

	@include('notification.notify')

	<div class="row">

        <div class="col-xs-12">

          	<div class="box box-primary">

	          	<div class="box-header label-primary">

	                <b style="font-size:18px;">{{tr('redeems')}}</b>

	                <a href="{{route('admin.users')}}" class="btn btn-default pull-right">{{tr('view_users')}}</a>
	            </div>
            	
            	<div class="box-body">

					<table id="example1" class="table table-bordered table-striped">

						<thead>
						    <tr>
						      <th>{{tr('id')}}</th>
						      <th>{{tr('username')}}</th>
						      <th>{{tr('redeem_amount')}}</th>
						      <th>{{tr('paid_amount')}}</th>
						      <th>{{tr('sent_date')}}</th>
						      <th>{{tr('status')}}</th>
						      <th>{{tr('action')}}</th>
						    </tr>
						
						</thead>

						<tbody>

							@foreach($data as $i => $value)

							    <tr>

							      	<td>{{$i+1}}</td>

							      	<td>

							      		<a href="{{route('admin.users.view' , $value->user_id)}}">
							      			{{$value->user ? $value->user->name : ""}}
							      		</a>

							      	</td>

							      	<td><b>{{Setting::get('currency')}}{{$value->request_amount}}</b></td>

							      	<td><b>{{Setting::get('currency')}}{{$value->paid_amount}}</b></td>

							      	<td>{{$value->created_at ? $value->created_at->diffForHumans() : ""}}</td>

							      	<td><b>{{redeem_request_status($value->status)}}</b></td>
							 
							      	<td>

							      		@if(in_array($value->status ,[REDEEM_REQUEST_SENT , REDEEM_REQUEST_PROCESSING]))

								      		<form action="{{route('admin.users.payout.invoice')}}" method="POST">

								      			<input type="hidden" name="user_id" value="{{$value->user_id}}">

								      			<input type="hidden" name="redeem_request_id" value="{{$value->id}}">

								      			<input type="text" name="paid_amount" value="{{$value->request_amount}}">

								      			<?php $confirm_message = tr('redeem_pay_confirm'); ?>

								      			<button type="submit" class="btn btn-success btn-sm" onclick='confirm("{{$confirm_message}}")'>{{tr('paynow')}}</button>
								      			
								      		</form>

								      	@else
								      		<span>-</span>
							      		@endif

							      	</td>
							    </tr>
							@endforeach
						
						</tbody>

					</table>

				</div>
			</div>
		</div>
	</div>

@endsection