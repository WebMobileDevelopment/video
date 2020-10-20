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
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li><a href="{{route('admin.ads-details.index')}}"><i class="fa fa-bullhorn"></i> {{tr('view_ads')}}</a></li>
    <li class="active">{{tr('view_ads')}}</li>
@endsection 

@section('content')


<div class="col-md-12">
  <div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
      <li class="active"><a href="#preview_ad" data-toggle="tab" aria-expanded="true">{{tr('preview_ad')}}</a></li>

      <li class="pull-right clearfix">
        <a href="{{route('admin.ads-details.edit' , array('id' => $model->id))}}"><i class="fa fa-pencil"></i> {{tr('edit')}}</a>
      </li>
 
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="preview_ad">

            <div class="col-lg-6">

                <h4>{{$model->name}} {{tr('details')}} (<a href="{{$model->ad_url}}" target="_blank">{{tr('click_here_url')}}</a>)</h4>


                <ul class="timeline timeline-inverse">

              
                  <!-- /.timeline-label -->
                  <!-- timeline item -->
                  <li>
                    <i class="fa fa-bullhorn bg-blue"></i>

                    <div class="timeline-item">
                      <span class="time"><i class="fa fa-clock-o"></i> {{$model->ad_time}} ({{tr('in_sec')}})</span>

                      <h3 class="timeline-header">
                        

                      {{tr('details')}}</h3>

                      <div class="timeline-body">
                            
                            <img src="{{$model->file}}" style="width: 100%;">

                      </div>
                      <!-- <div class="timeline-footer">
                        <a class="btn btn-primary btn-xs">Read more</a>
                        <a class="btn btn-danger btn-xs">Delete</a>
                      </div> -->
                    </div>
                  </li>
                
                
                </ul>
            </div>

            <div class="clearfix"></div>

        </div>

      <!-- /.tab-pane -->
    </div>
    <!-- /.tab-content -->
  </div>
  <!-- /.nav-tabs-custom -->
</div>
<div class="clearfix"></div>
@endsection


