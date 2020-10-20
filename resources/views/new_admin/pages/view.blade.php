@extends('layouts.admin')

@section('title', tr('view_page'))

@section('content-header', tr('view_page'))

@section('breadcrumb')
    <li><a href="{{route('admin.pages.index')}}"><i class="fa fa-book"></i> {{tr('pages')}}</a></li>
    <li class="active"> {{tr('view_page')}}</li>
@endsection

@section('content')

    @include('notification.notify')
    
    <div class="row">

        <div class="col-md-12">

            <div class="box ">

                <div class="box-header label-primary">

                    <div class="pull-left">
                        <h2 class="box-title" style="color: white"><b>{{tr('view_page')}}</b></h2>
                    </div>

                    <div class="pull-right">
                       
                        @if(Setting::get('admin_delete_control') == YES )

                            <a href="javascript:;" class="btn btn-sm btn-warning" style="text-align: left"><b><i class="fa fa-edit"></i>&nbsp;{{tr('edit')}}</b></a>

                            <a href="javascript:;" class="btn btn-sm btn-danger" style="text-align: left"><b><i class="fa fa-trash"></i>&nbsp;{{tr('delete')}}</b></a>
                                
                        @else

                            <a href="{{route('admin.pages.edit', ['page_id' => $page_details->id] )}}" class="btn btn-sm btn-warning"><b><i class="fa fa-edit"></i>&nbsp;{{tr('edit')}}</b></a>

                            <a onclick="return confirm(&quot;{{tr('page_delete_confirmation' , $page_details->title)}}&quot;);"  href="{{ route('admin.pages.delete',['page_id' => $page_details->id] ) }}" class="btn btn-sm btn-danger"><b><i class="fa fa-trash"></i>&nbsp;{{tr('delete')}}</b></a>
                            
                        @endif

                        @if($page_details->status == YES )
                            <a class="btn btn-sm btn-warning" onclick="return confirm(&quot;{{ $page_details->name }} - {{ tr('admin_page_decline_confirmation') }}&quot;);" tabindex="-1" href="{{ route('admin.pages.status' , ['page_id' => $page_details->id]) }}" title="{{tr('decline')}}"> <b><i class="fa fa-close"></i>&nbsp;{{tr('decline')}}</b></a>
                         @else 
                            <a class="btn btn-sm btn-success" onclick="return confirm(&quot;{{ $page_details->name }} - {{ tr('admin_page_approve_confirmation') }}&quot;);" tabindex="-1" href="{{ route('admin.pages.status' , ['page_id'=>$page_details->id]) }}" title="{{tr('approve')}}"> <b><i class="fa fa-check"></i>&nbsp;{{tr('approve')}}</b></a>
                        @endif

                    </div>
                    <div class="clearfix"></div>
                </div>

                <div class="box-body">

                    <strong><i class="fa fa-book margin-r-5"></i> {{tr('title')}}</strong>
                    <p class="text-muted">{{$page_details->title}}</p>
                    <hr>

                    <strong><i class="fa fa-book margin-r-5"></i> {{tr('page_type')}}</strong>
                    <p class="text-muted">{{$page_details->type}}</p>
                    <hr>

                    <strong><i class="fa fa-book margin-r-5"></i> {{tr('description')}}</strong>
                    <p class="text-muted"><?= $page_details->description?></p>
                    <hr>

                    <strong>
                        <i class="fa fa-calendar margin-r-5"></i> {{tr('created_at')}}
                    </strong>
                    <p><?= $page_details->created_at?></p>

                    <strong>
                        <i class="fa fa-calendar margin-r-5"></i> {{tr('updated_at')}}
                    </strong>
                    <p class="text-muted"><?= $page_details->updated_at?></p>
                    <hr>

                </div>

            </div>
            <!-- /.box -->
        </div>

    </div>
@endsection


