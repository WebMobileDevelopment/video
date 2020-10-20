@extends('layouts.admin')

@section('title', tr('redeems'))

@section('content-header')

{{ tr('redeems') }} 

	@if($user_details) -
		<a href="{{ route('admin.users.view' , ['user_id' => $user_details->id] ) }}">{{ $user_details->name }}</a>
	@endif

@endsection

@section('breadcrumb')
     
    @if($user_details)

    	<li><a href="{{ route('admin.users.index') }}"><i class="fa fa-user"></i> {{ tr('users') }}</a></li>

    @endif

    <li class="active"><i class="fa fa-trophy"></i> {{ tr('redeems') }}</li>

@endsection

@section('content')

	<div class="row">

        <div class="col-xs-12">
			
			@include('notification.notify')

          	<div class="box box-primary">

	          	<div class="box-header label-primary">

	                <b style="font-size:18px;">{{ tr('redeems') }}</b>

	                <a href="{{ route('admin.users.index') }}" class="btn btn-default pull-right">{{ tr('view_users') }}</a>
	            </div>
            	
            	<div class="box-body">

					<table id="example1" class="table table-bordered table-striped">

						<thead>
						    <tr>
						      <th>{{ tr('id') }}</th>
						      <th>{{ tr('username') }}</th>
						      <th>{{ tr('redeem_amount') }}</th>
						      <th>{{ tr('paid_amount') }}</th>
						      <th>{{ tr('sent_date') }}</th>
						      <th>{{ tr('status') }}</th>
						      <th>{{ tr('action') }}</th>
						    </tr>
						
						</thead>

						<tbody>

							@foreach($redeem_requests as $i => $redeem_request_details)

							    <tr>

							      	<td>{{ $i+1 }}</td>

							      	<td>
							      		<a href="{{ route('admin.users.view' , ['user_id' => $redeem_request_details->user_id] ) }}">
							      			{{ $redeem_request_details->user ? $redeem_request_details->user->name : "" }}
							      		</a>

							      	</td>

							      	<td><b>{{ formatted_amount($redeem_request_details->request_amount) }}</b></td>

							      	<td><b>{{ formatted_amount($redeem_request_details->paid_amount) }}</b></td>

							      	<td>{{ $redeem_request_details->created_at ? $redeem_request_details->created_at->diffForHumans() : "" }}</td>

							      	<td><b>{{ redeem_request_status($redeem_request_details->status) }}</b></td>
							 
							      	<td>
							      		@if(in_array($redeem_request_details->status ,[REDEEM_REQUEST_SENT , REDEEM_REQUEST_PROCESSING]))

								      		<form action="{{ route('admin.users.payout.invoice') }}" method="POST">

								      			<input type="hidden" name="user_id" value="{{ $redeem_request_details->user_id }}">

								      			<input type="hidden" name="redeem_request_id" value="{{ $redeem_request_details->id }}">

								      			<input type="text" name="paid_amount" value="{{ $redeem_request_details->request_amount }}" >

								      			<?php $confirm_message = tr('redeem_pay_confirm'); ?>

								      			<button type="submit" class="btn btn-success btn-sm" onclick='confirm("{{ $confirm_message }}")'>{{ tr('paynow') }}</button>
								      			
								      		</form>
										
												 
									      		
									      <!-- 	<form action="{{route('admin.users.payout.direct')}}" method="post">

						                            <input type="hidden" name="redeem_request_id" value="{{ $redeem_request_details->id }}">

						                            <label>Request amount</label>
						                            <input type="text" value="{{ $redeem_request_details->request_amount }}" disabled="">
						                            
						                            <input type="text" name="paid_amount">

						                            <input type="hidden" name="user_id" value="{{$redeem_request_details->user_id}}">

						                            <?php $confirm_message = tr('redeem_pay_confirm'); ?>

						                            <button type="submit" class="btn btn-success btn-lg" onclick=' return confirm("{{$confirm_message}}")'>
						                                <i class="fa fa-credit-card"></i> 
						                                {{tr('direct_payment')}}
						                            </button>
	 											
	 											
				                        	
				                        	</form> -->

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