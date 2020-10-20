@extends('layouts.user')

@section('styles')

<style>
	.referral-tr-img {
		width: 10%
	}

	.referral-box {
		background: white;
    	box-shadow: 1px 1px 1px 1px #cccccc; 
    	padding: 10px 5px 20px 5px; 
    	margin-top: 20px;
    	margin-bottom: 20px;
	}
</style>

@endsection

@section('content')

<div class="y-content">

	<div class="row content-row">

		@include('layouts.user.nav')

		<div class="page-inner col-sm-9 col-md-10">

			@include('notification.notify')

			<div class="box-primary referral-box">
	          	
	            <div class="box-body table-responsive">

	            	<div class="col-md-12">

	            	<h3>{{tr('referrals')}}

		                <a onclick="window.history.back();" class="btn btn-info pull-right">
		                	{{tr('back')}}
		                </a>

	                </h3>
	                <hr>
	            
	            </div>

	            	<div class="row col-md-12" style="margin-bottom: 20px">

					    <div class="col-xs-3">
					       
				            <p class="text-success"> 
				            	{{tr('referral_code')}}
				            	
				            </p>
				            <p class="product-description">
				                <b>{{$user_referrer_details->referral_code}}</b>
				            </p>
					    </div>

					    <div class="col-xs-3">
					       
				            <p class="text-success"> 
				            	{{tr('username')}}
				            	
				            </p>
				            <p class="product-description">
				                <b>{{$user_details->name}}</b>
				            </p>
					    </div>

					    <div class="col-xs-3">
					       
				            <p class="text-success"> 
				            	{{tr('total_referrals')}}
				            	
				            </p>
				            <p class="product-description">
				                <b>{{$user_referrer_details->total_referrals}}</b>
				            </p>
					    </div>

					    <div class="col-xs-3">
					       
				            <p class="text-success"> 
				            	{{tr('total_referrals_earnings')}}
				            	
				            </p>
				            <p class="product-description">
				                <b>{{$user_referrer_details->total_referrals_earnings}}</b>
				            </p>
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

		    $("#example1,  #datatable-example").DataTable();

		    $('#example2, #datatable-example').DataTable({
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