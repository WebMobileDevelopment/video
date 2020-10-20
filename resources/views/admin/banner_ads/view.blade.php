@extends('layouts.admin')

@section('title', tr('view_banner_ad'))

@section('content-header', tr('view_banner_ad'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li><a href="{{route('admin.banner-ads.list')}}"><i class="fa fa-bullhorn"></i>{{tr('banner_ads')}}</a></li>
    <li class="active"><i class="fa fa-eye"></i>&nbsp;{{tr('view_banner_ad')}}</li>
@endsection

@section('styles')

<style type="text/css">
    .user-block .username, .user-block .description, .user-block .comment {
        margin-left: 0px;
    }
</style>

@endsection

@section('content')

	@include('notification.notify')

	<div class="row">

        <div class="col-xs-12">

            <div class="panel">
                
                <div class="panel-body">

                    <div class="post">
                        
                        <div class="user-block">

                           <img class="img-circle img-bordered-sm" src="{{$model->file ?  $model->file : asset('placeholder.png') }}" alt="User Image">

                            <span class="username">
                                <a href="{{$model->link}}" target="_blank">{{$model->title}}</a>
                            </span>

                            <span class="description">{{$model->created_at->diffForHumans()}}</span>

                        </div>

                        <div class="row margin-bottom">

                            <div class="col-sm-4">

                            <img src="{{$model->file}}" class="img-responsive">
                                
                            </div>

                            <div class="col-sm-8">

                                <div class="row">

                                    <div class="col-sm-6">

                                        <div class="header">

                                            <h4><b>{{tr('title')}}</b></h4>

                                            <label>{{$model->title}}</label>

                                        </div>

                                    </div>
                                   
                                    

                                    <div class="col-sm-12">

                                        <h3><b>{{tr('description')}}</b></h3>

                                        <p><?= $model->description ?></p>

                                    </div>
                            	
                            	</div>
                        
                       		</div>

                    	</div>
                
                	</div>

            	</div>

        	</div>

    	</div>

    </div>

@endsection


