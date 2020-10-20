@extends('layouts.admin')

@section('title', tr('view_subscription'))

@section('content-header', tr('view_subscription'))

@section('breadcrumb')
     
    <li><a href="{{ route('admin.subscriptions.index') }}"><i class="fa fa-key"></i> {{ tr('subscriptions') }}</a></li>
    <li class="active"><i class="fa fa-eye"></i>&nbsp;{{ tr('view_subscriptions') }}</li>
@endsection

@section('styles')

<style type="text/css">
    .user-block .username, .user-block .description, .user-block .comment {
        margin-left: 0px;
    }
</style>

@endsection

@section('content')

	<div class="row">

        <div class="col-xs-12">
            
            @include('notification.notify')

            <div class="box">

                <div class="box-header ">

                    <div class='pull-left'>
                        <h3 class="box-title"> <b>{{$subscription_details->title}}</b></h3>
                        <br>
                        <span style="margin-left:0px: color: #ecf0f5 !important;" class="description">Created Time - {{$subscription_details->created_at->diffForHumans()}}</span>
                    </div>

                    <div class='pull-right'>
                       
                        @if(Setting::get('admin_delete_control') == YES )
                            
                            <a class="btn btn-sm btn-warning" href="javascript:;" class="btn disabled" style="text-align: left" title="{{ tr('edit') }}"><i class="fa fa-pencil"></i></a>

                            <a class="btn btn-sm btn-danger" href="javascript:;" class="btn disabled" style="text-align: left" title="{{ tr('delete') }}"><i class="fa fa-trash"></i></a>

                        @else

                             <a class="btn btn-sm btn-warning" href="{{ route('admin.subscriptions.edit' , ['subscription_id' => $subscription_details->id] ) }}" title="{{ tr('edit') }}"><i class="fa fa-pencil"></i></a>

                             <a class="btn btn-sm btn-danger" href="{{ route('admin.subscriptions.delete' , ['subscription_id' => $subscription_details->id] ) }}" onclick="return confirm(&quot;{{ tr('admin_subscription_delete_confirmation', $subscription_details->title ) }}&quot;);"  title="{{ tr('delete') }}"><i class="fa fa-trash"></i></a>

                        @endif

                        @if($subscription_details->status == YES)
                            
                            <a class="btn btn-sm btn-danger" href="{{ route('admin.subscriptions.status', ['subscription_id' => $subscription_details->id] ) }}" onclick="return confirm(&quot;{{ tr('admin_subscription_decline_confirmation', $subscription_details->title) }}&quot;)"  title="{{ tr('decline') }}"><i class="fa fa-close"></i></a>
                            
                        @else
                            
                            <a class="btn btn-sm btn-success" href="{{ route('admin.subscriptions.status', ['subscription_id' => $subscription_details->id]) }}" onclick="return confirm(&quot;{{ tr('admin_subscription_approve_confirmation', $subscription_details->title) }}&quot;)" title="{{ tr('approve') }}"><i class="fa fa-check"></i></a>
                            
                        @endif
                                            
                    </div>

                    <div class="clearfix"></div>
                </div>

                <div class="box box-body">

                    <div class="col-md-6">

                        <strong>{{ tr('title') }}</strong>
                        <h5 class="pull-right">{{ $subscription_details->title }}</h5><hr>

                        <strong>{{ tr('status') }}</strong>
                            @if($subscription_details->status)
                            <span class="label label-success pull-right">{{ tr('approved') }}</span>
                            @else
                            <span class="label label-warning pull-right">{{ tr('pending') }}</span>
                            @endif
                        <hr>
                        <strong>{{ tr('total_subscription') }}</strong>
                        <h4 class="pull-right" ><a href="{{ route('admin.revenues.subscription-payments' , ['subscription_id' => $subscription_details->id] ) }}" class="btn btn-success btn-xs">{{ count($subscription_details->getUserPayments) }}</a></h4>

                    </div>

                    <div class="col-md-6">
                        <strong>{{ tr('plan') }}</strong>
                        <h4 class="pull-right" >{{ $subscription_details->plan }}</h4><hr>

                        <strong>{{ tr('amount') }}</strong>
                        <h4 class="pull-right">{{ formatted_amount($subscription_details->amount) }}</h4><hr>

                    </div>

                    <div class="col-sm-12">
                    <hr>
                        <strong>{{ tr('description') }}</strong>
                        <p><?php echo $subscription_details->description ?></p>
                    </div>

        	</div>

    	</div>

    </div>

@endsection


