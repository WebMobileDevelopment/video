
@extends('layouts.admin')

@section('title', tr('view_category'))

@section('content-header', tr('view_category'))

@section('breadcrumb')
    <li><a href="{{route('admin.categories.index')}}"><i class="fa fa-list"></i> {{tr('categories')}}</a></li>
    <li class="active">{{tr('view_category')}}</li>
@endsection


@section('content')

    <div class="col-md-12">

        @include('notification.notify')

        <div class="box box-widget widget-user">

            <div class="widget-user-header bg-black" style="background: #000">
                <h3 class="widget-user-username text-capitalize">{{$category_details->name}}</h3>
                <h5 class="widget-user-desc">{{tr('category')}}</h5>
            </div>

            <div class="widget-user-image">
                <img class="img-circle" src="{{$category_details->image}}" alt="User Avatar" style="height: 90px">
            </div>

            <div class="box-footer">
                
                <div class="row">
                    <div class="col-sm-4 border-right">
                        <div class="description-block">
                            <h5 class="description-header"><a target="_blank" href="{{route('admin.categories.videos', ['category_id'=> $category_details->id] )}}">{{$category_details->get_videos_count}}</a></h5>
                            <span class="description-text">{{tr('videos')}}</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-4 border-right">
                        <div class="description-block">
                            <h5 class="description-header"><a  target="_blank" href="{{route('admin.categories.channels', ['category_id'=> $category_details->id] )}}">{{$no_of_channels}}</a></h5>
                            <span class="description-text">{{tr('channels')}}</span>
                        </div>
                      <!-- /.description-block -->
                    </div>

                    <!-- /.col -->
                    <div class="col-sm-4">
                        <div class="description-block">
                            <h5 class="description-header">{{Setting::get('currency')}} {{number_format_short($category_earnings)}}</h5>
                            <span class="description-text">{{tr('earnings')}}</span>
                        </div>
                      <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                </div>

            </div>

        </div>

        <div class="box box-widget">

            <div class="box-header with-border">

                <div class="pull-left">
                    <div class="user-block">
                        <img class="img-circle" src="{{$category_details->image}}" alt="{{$category_details->name}}">
                        <span class="username"><a target="_blank" href=_details"{{route('admin.users.view',['user_id' => $category_details->user_id] )}}">{{$category_details->name}}</a></span>
                        <span class="description">{{tr('shared_publicly')}} - {{$category_details->created_at->diffForHumans()}}</span>
                    </div>
                </div>

                <div class="pull-right">

                    @if(Setting::get('admin_delete_control') == YES )  

                        <a href="javascript:;" class="btn btn-sm btn-warning" title="{{tr('edit')}}" >
                            <i class="fa fa-edit"></i>
                        </a>

                        <a href="javascript:;" class="btn btn-sm btn-danger" title="{{tr('delete')}}">
                            <i class="fa fa-trash"></i>
                        </a>

                    @else 

                        <a href="{{ route('admin.categories.edit' ,['category_id' => $category_details->id]) }}" class="btn btn-sm btn-warning" title="{{tr('edit')}}">
                            <i class="fa fa-edit"></i>
                        </a>

                        <a href="{{ route('admin.categories.delete' ,['category_id' => $category_details->id]) }}" class="btn btn-sm btn-danger" title="{{tr('delete')}}" onclick="return confirm(&quot; {{tr('admin_category_delete_confirmation',$category_details->name) }} &quot;)">
                            <i class="fa fa-trash"></i>
                        </a>

                    @endif

                    @if($category_details->status == YES)

                        <a href="{{ route('admin.categories.status' ,['category_id' => $category_details->id]) }}" class="btn btn-sm btn-warning" title="{{tr('decline')}}" onclick="return confirm(&quot;{{ tr('admin_category_decline_confirmation',$category_details->name ) }}&quot;)">
                            <i class="fa fa-times"></i>
                        </a>

                    @else
                        <a href="{{ route('admin.categories.status' ,['category_id' => $category_details->id] ) }}" class="btn btn-sm btn-success" title="{{tr('approve')}}" onclick="return confirm(&quot;{{ tr('category_approve_notes') }}&quot;)" >
                        <i class="fa fa-check"></i>

                        </a>
                    @endif

                </div>

                <div class="clearfix"></div>

            </div>

            <div class="box-body">

                <div class="row col-md-12">

                    <div class="nav-tabs-custom">

                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab_1" data-toggle="tab">{{tr('about_category')}}</a></li>
                            <li><a href="#tab_2" data-toggle="tab">{{tr('videos')}}</a></li>
                            <li><a href="#tab_3" data-toggle="tab">{{tr('channels')}}</a></li>
                        </ul>

                        <div class="tab-content">

                            <div class="tab-pane active" id="tab_1">

                                <table class="table">
                                    <tr>
                                      <th>{{tr('name')}}</th>
                                      <td>{{$category_details->name}}</td>
                                    </tr>

                                    <tr>
                                      <th>{{tr('description')}}</th>
                                      <td><?= $category_details->description ?></td>
                                    </tr>

                                </table>  

                            </div>

                            <div class="tab-pane" id="tab_2">

                                <blockquote>
                                    <p>{{tr('videos_short_notes')}}</p>
                                    <cite><a target="_blank" href="{{route('admin.categories.videos', ['category_id'=> $category_details->id] )}}">{{ tr('to_view_more') }}</a></cite>
                                </blockquote>

                                @if($category_details->get_videos_count > 0)

                                    @foreach($category_videos as $category_video_details)
                                        <div class="box-comments">
                                            <!-- /.box-comment -->
                                            <div class="box-comment">
                                                <!-- User image -->
                                                <img class="img-circle img-sm" src="{{$category_video_details->default_image}}" alt="{{$category_details->user_name}}">

                                                <div class="comment-text">
                                                    <span class="username">
                                                        <a href="{{route('admin.video_tapes.view', ['video_tape_id' => $category_video_details->video_tape_id] )}}" target="_blank">{{$category_video_details->title}}</a>
                                                        <span class="text-muted pull-right">{{$category_video_details->created_at->diffForHumans()}}</span>
                                                        </span><!-- /.username -->
                                                    <div class="description"><?= $category_video_details->description?></div>
                                                </div>
                                                <!-- /.comment-text -->

                                            </div>

                                        </div>
                                    @endforeach

                                @else

                                    {{tr('no_videos_found')}}

                                @endif
                            </div>

                            <div class="tab-pane" id="tab_3">
                               
                                <blockquote>
                                    <p>{{tr('category_short_notes')}}</p>
                                     <cite><a  target="_blank" href="{{route('admin.categories.channels', ['category_id'=> $category_details->id] )}}">{{ tr('to_view_more') }}</a></cite>
                                </blockquote>

                                @if($no_of_channels > 0)

                                    @foreach($channel_lists as $i => $channel_list)

                                        <div class="col-sm-6 col-md-6">
                                        
                                            <div class="box box-solid">
                                                
                                                <div class="box-body">
                                                    
                                                    <h4 style="background-color:#f7f7f7; font-size: 18px; text-align: center; padding: 7px 10px; margin-top: 0;">
                                                        {{$channel_list->title}}
                                                    </h4>
                                                    
                                                    <div class="media">

                                                        <div class="media-left">
                                                            
                                                            <a href="{{route('admin.channels.view', ['channel_id' => $channel_list->channel_id] )}}" target="_blank">
                                                                
                                                            <img src="{{$channel_list->picture}}" alt="{{$channel_list->title}}" class="media-object" style="width: 150px;height: auto;border-radius: 4px;box-shadow: 0 1px 3px rgba(0,0,0,.15);">
                                                            </a>
                                                        </div>
                                                        
                                                        <div class="media-body">
                                                            
                                                            <div class="clearfix">
                                                                
                                                                <p class="pull-right">
                                                                     <a class="btn btn-success btn-sm" href="{{route('admin.channels.view', ['channel_id' => $channel_list->channel_id] )}}" target="_blank">
                                                                        {{tr('view')}}
                                                                    </a>
                                                                </p>

                                                                <h4 style="margin-top: 0;text-align:nowrap;overflow: hidden;text-overflow: ellipsis;">{{tr('no_of_videos')}} - {{$channel_list->no_of_videos}}</h4>

                                                                <h4 style="margin-top: 0;text-align:nowrap;overflow: hidden;text-overflow: ellipsis;">{{tr('no_of_subscribers')}} - {{$channel_list->no_of_subscribers}}</h4>

                                                                <div style="max-height: 80px;overflow-y: hidden;"><?= $channel_list->description ?></div>
                                                                
                                                            </div>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                      @if ($i % 2 == 0)  @else 

                                      <div class="clearfix"></div>

                                      @endif

                                    @endforeach

                                @else

                                  {{tr('no_channels_found')}}

                                @endif

                                <div class="clearfix"></div>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="clearfix"></div>
             
            </div>
          
        </div>

        <div class="clearfix"></div>
        
    </div>

  <div class="clearfix"></div>
@endsection