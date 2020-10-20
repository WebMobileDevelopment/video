@extends('layouts.admin')

@section('title', tr('pages'))

@section('content-header', tr('pages'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li class="active"><i class="fa fa-book"></i> {{tr('pages')}}</li>
@endsection

@section('content')

    @include('notification.notify')

    <div class="row">
        <div class="col-xs-12">

          <div class="box box-primary">

            <div class="box-header label-primary">
                <b>{{tr('pages')}}</b>
                <a href="{{route('admin.pages.create')}}" style="float:right" class="btn btn-default">{{tr('add_page')}}</a>
            </div>

            <div class="box-body">

                <table id="example1" class="table table-bordered table-striped">

                    <thead>
                        <tr>
                          <th>#{{tr('id')}}</th>
                          <th>{{tr('heading')}}</th>
                          <th>{{tr('page_type')}}</th>
                          <th>{{tr('action')}}</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($data as $i => $result)
                
                            <tr>
                                <td>{{$i+1}}</td>
                                <td>{{$result->heading}}</td>
                                <td>{{$result->type}}</td>
                                
                                <td>

                                    <div class="dropdown">
                                        
                                        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            {{tr('action')}}
                                            <span class="caret"></span>
                                        </button>

                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu">


                                            <li>
                                               
                                                <a href="{{route('admin.pages.view', array('id' => $result->id))}}"><b>{{tr('view')}}</b></a>
                                            </li>

                                            <li>
                                                @if(Setting::get('admin_delete_control'))
                                                    <a href="javascript:;" class="btn disabled"  style="text-align: left"><b>{{tr('edit')}}</b></a>
                                                @else
                                                    <a href="{{route('admin.pages.edit', array('id' => $result->id))}}"><b>{{tr('edit')}}</b></a>
                                                @endif
                                            </li>

                                            <li>
                                                @if(Setting::get('admin_delete_control'))
                                                    <a href="javascript:;" class="btn disabled" style="text-align: left"><b>{{tr('delete')}}</b></a>

                                                @else
                                                    <a onclick="return confirm('Are you sure?')" href="{{route('admin.pages.delete',array('id' => $result->id))}}"><b>{{tr('delete')}}</b></a>

                                                @endif

                                            </li>                                

                                        </ul>

                                    </div>
                                    
                                </td>
                            
                            </tr>

                        @endforeach

                    </tbody>
                </table>

            </div>
          </div>
        </div>
    </div>

@endsection