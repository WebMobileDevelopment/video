@extends('layouts.admin')

@section('title', tr('view_banner_ad'))

@section('content-header', tr('view_banner_ad'))

@section('breadcrumb')
     
    <li><a href="{{ route('admin.banner_ads.index') }}"><i class="fa fa-bullhorn"></i>{{ tr('banner_ads') }}</a></li>
    <li class="active"><i class="fa fa-eye"></i>&nbsp;{{ tr('view_banner_ad') }}</li>
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

                    <div class="user-block">

                        <div class="col-sm-4">
                            <img class="img-circle img-bordered-sm" src="{{ $banner_ad_details->file ?  $banner_ad_details->file : asset('placeholder.png')  }}" alt="User Image" >

                            <span class="username" style="margin-left: 50px ">
                                <a href="{{ $banner_ad_details->link }}" target="_blank">{{ $banner_ad_details->title }}</a>
                            </span>

                            <span class="description" style="margin-left: 50px">{{ $banner_ad_details->created_at->diffForHumans() }}</span>
                        
                        </div>

                        <div class="col-sm-8">

                            <div class="pull-right">
                                
                                @if(Setting::get('admin_delete_control') == YES)
                                    <a class="btn btn-warning" href="javascript:;" class="btn disabled" title="{{  tr('edit')  }}"><b><i class="fa fa-edit"></i></b></a>

                                    <a class="btn btn-danger" href="javascript:;" class="btn disabled" style="text-align: left" onclick="return confirm(&quot;{{  tr('admin_banner_ad_delete_confirmation', $banner_ad_details->title )  }}&quot;);" title="{{  tr('delete')  }}"><b><i class="fa fa-trash"></i></b></a>
                                
                                @else

                                    <a class="btn btn-warning" href="{{  route('admin.banner_ads.edit', ['banner_ad_id' => $banner_ad_details->id] )  }}" title="{{  tr('edit')  }}"><b><i class="fa fa-edit"></i></b></a>
                                                                    
                                    <a class="btn btn-danger" href="{{  route('admin.banner_ads.delete',['banner_ad_id' => $banner_ad_details->id] )  }}" onclick="return confirm(&quot;{{  tr('admin_banner_ad_delete_confirmation', $banner_ad_details->title )  }}&quot;);" title="{{  tr('delete')  }}"><b><i class="fa fa-trash"></i></b></a>
                                                                    
                                @endif

                                @if( $banner_ad_details->status == DEFAULT_TRUE )  

                                    <a class="btn btn-danger" href="{{  route('admin.banner_ads.status', ['banner_ad_id' => $banner_ad_details->id] )  }}" onclick="return confirm(&quot;{{ tr('admin_banner_ad_decline_confirmation', $banner_ad_details->title)  }}&quot;)" title="{{  tr('decline') }}"><b><i class="fa fa-close"></i></b></a>
                                
                                @else

                                    <a class="btn btn-success" href="{{ route('admin.banner_ads.status',['banner_ad_id' => $banner_ad_details->id] ) }}" onclick="return confirm(&quot;{{ tr('admin_banner_ad_approve_confirmation', $banner_ad_details->title)  }}&quot;)" title="{{  tr('approve')  }}"><b><i class="fa fa-check"></i></b></a>

                                @endif
                            
                            </div>
                        
                        </div>

                    </div>
                    
                </div>

                <div class="box box-body">

                    <div class="row margin-bottom">

                        <div class="col-sm-4">

                        <img src="{{ $banner_ad_details->file }}" class="img-responsive">
                            
                        </div>

                        <div class="col-sm-8">

                            <div class="row">

                                <div class="col-sm-12">

                                    <div class="header">

                                        <h4><b>{{ tr('title') }}</b></h4>

                                        <label>{{ $banner_ad_details->title }}</label>

                                    </div>

                                </div>

                                <div class="col-sm-12">

                                    <div class="header">

                                        <h4><b>{{ tr('position') }}</b></h4>

                                        <label>{{ $banner_ad_details->position }}</label>

                                    </div>

                                </div>

                                <div class="col-sm-12">

                                    <h3><b>{{ tr('description') }}</b></h3>

                                    <p><?= $banner_ad_details->description ?></p>

                                </div>
                        	
                        	</div>
                    
                   		</div>

                	</div>
                
            	</div>

        	</div>

    	</div>

    </div>

@endsection


