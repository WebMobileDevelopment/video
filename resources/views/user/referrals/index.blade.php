@extends('layouts.user')

@section('styles')

<style>
	.referral-tr-img {
		width: 10%
	}
</style>

@endsection

@section('content')

<div class="y-content">

	<div class="row content-row">

		@include('layouts.user.nav')

		<div class="page-inner col-sm-9 col-md-10">

			@include('notification.notify')

			<div class="spacing1 top"></div>

			<div class="col-md-8">

				<h2 class="no-margin">{{tr('referral_title')}}</h2>

				<p class="text-gray referrals-text">
					{{tr('referral_benefit')}}
					<span class="text-success">
						<b>{{formatted_amount(Setting::get('referral_commission'))}}</b>
					</span>
				</p>

				<!-- <li class="referrals-text">Subscription commission</li>

				<li class="referrals-text">PPV commission</li>

				<li class="referrals-text">Referral commission</li> -->

				<h4 class="shareUrl-headerText">Click to Copy Invite Link</h4>
	        	
	        	<p class="text-gray"></p>

	        	<input class="shareUrl-input js-shareUrl" type="text" readonly="readonly" />

			</div>

			<div class="col-sm-4" style="background: white">

				<div style="padding: 15px 3px">

			    <div class="card">

			        <div class="card-body">

			            <h5 class="card-title" style="margin-bottom: 15px">{{tr('referrals')}}</h5>
		            	
		            	<p>{{tr('total')}} <span class="pull-right">{{$user_referrer_details->total_referrals}}</span></p>
		            	<hr>

		            	<p>Earnings <span class="pull-right"> {{Setting::get('currency')}} {{$user_referrer_details->total_referrals_earnings ?: 0.00}}</span></p>
		            	<hr>
			            
			        </div>

			        <div class="card-footer top30">

						<a href="{{route('user.redeems')}}" class="btn btn-primary text-uppercase"><i class="fa fa-money"></i> {{ tr('check_redeems') }}</a>			        	
			        </div>

			    </div>

			    </div>

			</div>

			<div class="clearfix"></div>

			<div class="col-md-12">

				<div class="">

					<hr>

					<h3> <span class="text-gray"></span>{{tr('referrals')}}</h3>

					<hr>

					<div class="card">

            			<div class="card-body">

							<table id="example2" class="table table-bordered table-striped table-responsive referral-table">

								<thead>
									<tr style="background: white">
						      			<th>{{tr('s_no')}}</th>
						      			<th>{{tr('image')}}</th>
						      			<th>{{tr('username')}}</th>
						      			<th>{{tr('referral_code')}}</th>
						      			<th>{{tr('created')}}</th>
						      			<th>{{tr('action')}}</th>
						      		</tr>
								</thead>

								<tbody>

									@foreach($referrals as $key => $referral_details)

									<tr>
										<td>{{$key+1}}</td>
										<td >
											<img src="{{$referral_details->picture?: asset('placeholder.png')}}" class="img img-circle" style="width: 50px;height: 50px">
										</td>

										<td >
											{{$referral_details->username}}
										</td>
										<td>{{$referral_details->referral_code}}</td>
										<td>{{ common_date($referral_details->created_at) }}</td>
										<td>
											<a href="{{route('user.referrals.view', ['user_id' => $referral_details->user_id, 'parent_user_id' => $referral_details->parent_user_id])}}" class="btn btn-info">
												{{tr('more')}} <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i>
											</a>
										</td>
									</tr>

									@endforeach

								</tbody>

							</table>

						</div>

					</div>

				</div>

			</div>

        </div>

    </div>

</div>


@endsection

@section('scripts')

    <link rel="stylesheet" href="{{ asset('admin-css/plugins/datatables/dataTables.bootstrap.css')}}">

	<script src="{{asset('admin-css/plugins/datatables/jquery.dataTables.min.js')}}"></script>

    <script src="{{asset('admin-css/plugins/datatables/dataTables.bootstrap.min.js')}}"></script>

	<script>

		$(function () {

		    $("#example1").DataTable();

		    $('#example2').DataTable({
		        "paging": true,
		        "lengthChange": false,
		        "searching": false,
		        "ordering": true,
		        "info": true,
		        "autoWidth": false
		    });
		    
		    // Create reusable copy function

		    function copy(element) {
		        
		        return function() {
		          	document.execCommand('copy', false, element.select());
		        }
		    }
		    
		    // Grab shareUrl element
		    var shareUrl = document.querySelector('.js-shareUrl');

		    // Create new instance of copy, passing in shareUrl element
		    var copyShareUrl = copy(shareUrl);
		    
		    // Set value via markup or JS
		    shareUrl.value = "{{route('referrals_signup', $user_referrer_details->referral_code)}}";
		  
		    // Click listener with copyShareUrl handler
		    shareUrl.addEventListener('click', copyShareUrl, false);
		  
		}());
		
	</script>
@endsection