@extends('layouts.admin')

@section('title', tr('revenues'))

@section('content-header')

{{ tr('revenues') }} - {{ formatted_amount(total_revenue())}}

@endsection

@section('breadcrumb')
    <li class="active"><i class="fa fa-money"></i> {{tr('revenues')}}</li>
@endsection

@section('content')

	@include('notification.notify')

	<div class="row">

		<div class="col-lg-4 col-xs-4">

			<div class="small-box bg-green">
				<div class="inner">
					<h3>{{ formatted_amount($total) }}</h3>
					<p>{{tr('total')}}</p>
				</div>

				<div class="icon">
					<i class="fa fa-money"></i>
				</div>

				<a class="small-box-footer">
					{{tr('total_revenue')}}
				</a>
			</div>

		</div>

		<div class="col-lg-4 col-xs-4">

			<div class="small-box bg-orange">

				<div class="inner">
					<h3>{{ formatted_amount($ppv_admin_amount) }}</h3>
					<p>{{tr('ppv_payments')}}</p>
				</div>

				<div class="icon">
					<i class="fa fa-money"></i>
				</div>

				<a href="{{route('admin.revenues.ppv_payments')}}" class="small-box-footer">
					{{tr('more_info')}}
					<i class="fa fa-arrow-circle-right"></i>
				</a>
			</div>

		</div>

		<div class="col-lg-4 col-xs-4">

			<div class="small-box bg-red">
				<div class="inner">

					<h3>{{ formatted_amount($subscription_total)}}</h3>
					<p>{{tr('subscription_payments')}}</p>
				</div>

				<div class="icon">
					<i class="fa fa-money"></i>
				</div>

				<a href="{{route('admin.revenues.subscription-payments')}}" class="small-box-footer">
					{{tr('more_info')}}
					<i class="fa fa-arrow-circle-right"></i>
				</a>
			</div>

		</div>

    </div>

    <section id="ppv-section">

		<div class="col-md-6" style="box-shadow: 1px -3px 10px 2px #dddddd;background-color: white">
	        
	        <h3 class="">{{tr('ppv_payments')}}</h3>

			<div class="progress-group">

				<span class="progress-text">{{tr('total')}}</span>

				<span class="progress-number"><b>{{ formatted_amount($ppv_total) }}</b></span>

				<div class="progress sm">
					<div class="progress-bar progress-bar-green" style="width: 100%"></div>
				</div>
			
			</div>

			<!-- /.progress-group -->

			<div class="progress-group">

				<span class="progress-text">{{tr('admin_ppv_commission')}}</span>

				<span class="progress-number"><b>{{ formatted_amount($ppv_admin_amount) }}</b>/ {{ formatted_amount($ppv_total) }}</span>

				<div class="progress sm">
					<div class="progress-bar progress-bar-aqua" style="width: {{get_commission_percentage($ppv_total , $ppv_admin_amount)}}%"></div>
				</div>

			</div>

			<!-- /.progress-group -->
			<div class="progress-group">

				<span class="progress-text">{{tr('user_ppv_commission')}}</span>

				<span class="progress-number"><b>{{ formatted_amount($ppv_user_amount) }}</b>/ {{ formatted_amount($ppv_total) }}</span>

				<div class="progress sm">
					<div class="progress-bar progress-bar-red" style="width: {{ get_commission_percentage($ppv_total , $ppv_user_amount) }}%"></div>
				</div>
			</div>

		
		</div>
		
    </section>

@endsection
