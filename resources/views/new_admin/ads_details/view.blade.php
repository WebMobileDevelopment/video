@extends('layouts.admin')

@section('title', tr('view_ads'))

@section('content-header', tr('view_ads'))

@section('styles')

<style>
hr {
    margin-bottom: 10px;
    margin-top: 10px;
}
</style>

@endsection

@section('breadcrumb')

     
   
    <li><a href="{{route('admin.ads-details.index')}}"><i class="fa fa-bullhorn"></i>{{tr('video_ads')}}</a></li>

    <li><a href="{{route('admin.ads-details.index')}}"><i class="fa fa-bullhorn"></i> {{ tr('view_and_assign_ad') }}</a></li>

    <li class="active">{{tr('view_ads')}}</li>

@endsection 

@section('content')

<div class="col-md-12">

    <div class="nav-tabs-custom">

        <ul class="nav nav-tabs">
            
            <li class="active"><a href="#preview_ad" data-toggle="tab" aria-expanded="true">{{tr('preview_ad')}}</a></li>

            @if(Setting::get('admin_delete_control') == YES )            
                
                <a role="button" style="margin: 4px !important" class="btn btn-warning pull-right" href="javascript:;" class="btn disabled" style="text-align: left" title="{{ tr('edit') }}"><b><i class="fa fa-edit"></i></b></a>

                <a role="button" style="margin: 4px !important" class="btn btn-danger pull-right" href="javascript:;" class="btn disabled" style="text-align: left" title="{{ tr('delete') }}"><b><i class="fa fa-trash"></i></b></a>

            @else
            
                <a role="button" style="margin: 4px !important" class="btn btn-warning pull-right" href="{{ route('admin.ads-details.edit' , ['ads_detail_id' => $ads_detail_details->id] ) }}" title="{{ tr('edit') }}"><b><i class="fa fa-edit"></i></b></a>

                <a role="button" style="margin: 4px !important" class="btn btn-danger pull-right" onclick="return confirm(&quot;{{ tr('admin_ads_detail_delete_confirmation', $ads_detail_details->name) }}&quot;)" href="{{ route('admin.ads-details.delete' , ['ads_detail_id' => $ads_detail_details->id] ) }}" title="{{ tr('delete') }}"><b><i class="fa fa-trash"></i></b></a>

            @endif
            
            @if($ads_detail_details->status == DEFAULT_TRUE)
                
                <a role="button" style="margin: 4px !important" class="btn btn-warning pull-right" href="{{ route('admin.ads-details.status' , ['ads_detail_id' => $ads_detail_details->id] ) }}" onclick="return confirm(&quot;{{ tr('admin_ads_detail_decline_confirmation', $ads_detail_details->name) }}&quot;)" title="{{ tr('decline') }}"><b><i class="fa fa-close"></i></b></a>
            
            @else
                
                <a role="button" style="margin: 4px !important" class="btn btn-success pull-right" href="{{ route('admin.ads-details.status' , ['ads_detail_id' => $ads_detail_details->id ] ) }}" onclick="return confirm(&quot;{{ tr('admin_ads_detail_approve_confirmation', $ads_detail_details->name) }}&quot;)"  title="{{ tr('approve') }}"><b><i class="fa fa-check"></i></b></a>
            @endif
            
        </ul>

        <div class="tab-content">
            
            <div class="tab-pane active" id="preview_ad">

                <div class="col-lg-6">

                    <h4>{{$ads_detail_details->name}} {{tr('details')}}  ( <a href="{{$ads_detail_details->ad_url}}" target="_blank">{{tr('view')}}</a> )</h4>

                    <ul class="timeline timeline-inverse">

                      <li>

                        <i class="fa fa-bullhorn bg-blue"></i>

                        <div class="timeline-item">

                            <span class="time"><i class="fa fa-clock-o"></i> {{$ads_detail_details->ad_time}} ({{tr('in_sec')}})</span>

                            <h3 class="timeline-header">
                                
                              {{tr('details')}}</h3>

                            <div class="timeline-body">                                
                                <img src="{{$ads_detail_details->file}}" style="width: 100%;">
                            </div>

                        </div>

                      </li>                
                    
                    </ul>

                </div>

                <div class="clearfix"></div>

            </div>

        </div>

    </div>

</div>

<div class="clearfix"></div>

@endsection


