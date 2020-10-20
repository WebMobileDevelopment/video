@extends('layouts.admin')

@section('title', tr('pages'))

@section('content-header', tr('pages'))

@section('breadcrumb')
    <li class="active"><i class="fa fa-book"></i> {{tr('pages')}}</li>
@endsection

@section('content')

    @include('notification.notify')

    <div class="row">
    
        <div class="col-xs-12">

            <div class="box box-primary">

                <div class="box-header label-primary">
                    
                     <h2 class="box-title" style="color: white"><b>{{tr('pages')}}</b></h2>
                    <a href="{{route('admin.pages.create')}}" style="float:right" class="btn btn-default">{{tr('add_page')}}</a>
                </div>

                <div class="box-body table-responsive">

                    @if(count($pages) > 0)

                    <table id="datatable-withoutpagination" class="table table-bordered table-striped">

                        <thead>
                            <tr>
                              <th>{{tr('id')}}</th>
                              <th>{{tr('title')}}</th>
                              <th>{{tr('page_type')}}</th>
                              <th>{{tr('status')}}</th>
                              <th>{{tr('action')}}</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($pages as $i => $page_details)
                    
                                <tr>
                                    <td>{{$i+1}}</td>
                                    <td><a href="{{route('admin.pages.view', ['page_id' => $page_details->id] )}}">{{$page_details->title}}</a></td>
                                   
                                    <td>{{$page_details->type}}</td>
                                    
                                    <td>
                                        @if($page_details->status)

                                            <span class="label label-success">{{ tr('approved') }}</span>

                                        @else

                                            <span class="label label-warning">{{ tr('pending') }}</span>

                                        @endif
                                    </td>
                                    <td>

                                        <div class="dropdown">
                                            
                                            <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                {{tr('action')}}
                                                <span class="caret"></span>
                                            </button>

                                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu">

                                                <li>    
                                                    <a href="{{route('admin.pages.view', ['page_id' => $page_details->id] )}}"><b>{{tr('view')}}</b></a>
                                                </li>

                                                @if(Setting::get('admin_delete_control') == YES )

                                                    <li><a href="javascript:;" class="btn disabled" style="text-align: left"><b>{{tr('edit')}}</b></a></li>

                                                    <li><a href="javascript:;" class="btn disabled" style="text-align: left"><b>{{tr('delete')}}</b></a></li>

                                                @else

                                                    <li><a href="{{route('admin.pages.edit', ['page_id' => $page_details->id] )}}"><b>{{tr('edit')}}</b></a></li>

                                                    <li><a onclick="return confirm(&quot;{{tr('page_delete_confirmation' , $page_details->title)}}&quot;);" href="{{ route('admin.pages.delete',['page_id' => $page_details->id] ) }}"><b>{{tr('delete')}}</b></a></li>
                                                @endif

                                                <li role="presentation" class="divider"></li>

                                                @if($page_details->status == YES )
                                                    <li role="presentation"><a role="menuitem" onclick="return confirm(&quot;{{ $page_details->name }} - {{ tr('admin_page_decline_confirmation') }}&quot;);" tabindex="-1" href="{{ route('admin.pages.status' , ['page_id' => $page_details->id]) }}"> {{ tr('decline') }}</a></li>
                                                 @else 
                                                    <li role="presentation"><a role="menuitem" onclick="return confirm(&quot;{{ $page_details->name }} - {{ tr('admin_page_approve_confirmation') }}&quot;);" tabindex="-1" href="{{ route('admin.pages.status' , ['page_id'=>$page_details->id]) }}"> 
                                                    {{ tr('approve') }} </a></li>
                                                @endif

                                            </ul>

                                        </div>
                                        
                                    </td>
                                
                                </tr>

                            @endforeach

                        </tbody>
                    
                    </table>

                    @if(count($pages) > 0) 

                    <div align="right" id="paglink"><?php echo $pages->links(); ?></div>

                    @endif

                    @else

                    <h3 class="no-result">{{ tr('no_result_found') }}</h3> 

                    @endif
                </div>

            </div>

        </div>

    </div>

@endsection