
@extends('layouts.admin')

@section('title', tr('view_category'))

@section('content-header', tr('view_category'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li><a href="{{route('admin.categories.list')}}"><i class="fa fa-list"></i> {{tr('channels')}}</a></li>
    <li class="active">{{tr('view_category')}}</li>
@endsection


@section('content')


  <div class="col-md-12">
    @include('notification.notify')

          <!-- Widget: user widget style 1 -->
          <div class="box box-widget widget-user">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header bg-black" style="background: #000">
              <h3 class="widget-user-username text-capitalize">{{$category->name}}</h3>
              <h5 class="widget-user-desc">{{tr('category')}}</h5>
            </div>
            <div class="widget-user-image">
              <img class="img-circle" src="{{$category->image}}" alt="User Avatar" style="height: 90px">
            </div>
            <div class="box-footer">
              <div class="row">
                <div class="col-sm-4 border-right">
                  <div class="description-block">
                    <h5 class="description-header"><a target="_blank" href="{{route('admin.categories.videos', array('category_id'=> $category->id))}}">{{$category->get_videos_count}}</a></h5>
                    <span class="description-text">{{tr('videos')}}</span>
                  </div>
                  <!-- /.description-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-4 border-right">
                  <div class="description-block">
                    <h5 class="description-header"><a  target="_blank" href="{{route('admin.categories.channels', array('category_id'=> $category->id))}}">{{$no_of_channels}}</a></h5>
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
              <!-- /.row -->
            </div>
          </div>
          <!-- /.widget-user -->


          <!-- Box Comment -->
          <div class="box box-widget">
            <div class="box-header with-border">

              <div class="pull-left">
                  <div class="user-block">
                    <img class="img-circle" src="{{$category->image}}" alt="{{$category->name}}">
                    <span class="username"><a target="_blank" href="{{route('admin.users.view', $category->user_id)}}">{{$category->name}}</a></span>
                    <span class="description">{{tr('shared_publicly')}} - {{$category->created_at->diffForHumans()}}</span>
                  </div>
              </div>

              <div class="pull-right">
                  <a href="{{route('admin.categories.edit' ,['id'=>$category->id])}}" class="btn btn-xs btn-warning" title="Edit">
                      <i class="fa fa-edit"></i>
                    </a>
              </div>
              <div class="clearfix"></div>
              <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body">

              <div class="row col-md-12">
                <!-- Custom Tabs -->
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
                          <td>{{$category->name}}</td>
                        </tr>
                        <tr>
                          <th>{{tr('description')}}</th>
                          <td><?= $category->description ?></td>
                        </tr>
                      </table>

                     
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_2">

                      <blockquote>
                        <p>{{tr('videos_short_notes')}}</p>
                        <small>{{tr('to_view_more')}} <cite><a target="_blank" href="{{route('admin.categories.videos', array('category_id'=> $category->id))}}">{{tr('click_here')}}</a></cite></small>
                    </blockquote>

                    @if($category->get_videos_count > 0)

                      @foreach($category_videos as $video)
                      <div class="box-comments">
                        <!-- /.box-comment -->
                        <div class="box-comment">
                          <!-- User image -->
                          <img class="img-circle img-sm" src="{{$video->default_image}}" alt="{{$category->user_name}}">
                         
                          <div class="comment-text">
                                <span class="username">

                                  <a href="{{route('admin.video_tapes.view', array('id' => $video->id))}}" target="_blank">{{$video->title}}</a>
                                  
                                  <span class="text-muted pull-right">{{$video->created_at->diffForHumans()}}</span>
                                </span><!-- /.username -->
                              <div class="description"><?= $video->description?></div>
                          </div>
                          <!-- /.comment-text -->
                        </div>
                      </div>
                      @endforeach

                    @else


                        {{tr('no_videos_found')}}

                    @endif
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_3">
                      <blockquote>
                          <p>{{tr('category_short_notes')}}</p>
                          <small>{{tr('to_view_more')}} <cite><a  target="_blank" href="{{route('admin.categories.channels', array('category_id'=> $category->id))}}">{{tr('click_here')}}</a></cite></small>
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
                                             <a href="{{route('admin.channels.view', array('id' => $channel_list->channel_id))}}" target="_blank">
                                                <img src="{{$channel_list->picture}}" alt="{{$channel_list->title}}" class="media-object" style="width: 150px;height: auto;border-radius: 4px;box-shadow: 0 1px 3px rgba(0,0,0,.15);">
                                            </a>
                                        </div>
                                        <div class="media-body">
                                            <div class="clearfix">
                                                <p class="pull-right">
                                                     <a class="btn btn-success btn-sm" href="{{route('admin.channels.view', array('id' => $channel_list->channel_id))}}" target="_blank">
                                                        {{tr('view')}}
                                                    </a>
                                                </p>

                                                <h4 style="margin-top: 0;text-align:nowrap;overflow: hidden;text-overflow: ellipsis;">{{tr('no_of_videos')}} - {{$channel_list->no_of_videos}}</h4>

                                                <h4 style="margin-top: 0;text-align:nowrap;overflow: hidden;text-overflow: ellipsis;">{{tr('no_of_subscribers')}} - {{$channel_list->no_of_subscribers}}</h4>

                                                <div style="max-height: 80px;overflow-y: hidden;"><?= $channel_list->description ?></div>
                                                <!-- <p style="margin-bottom: 0">
                                                    <i class="fa fa-shopping-cart margin-r5"></i> 12+ purchases
                                                </p> -->
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
                    <!-- /.tab-pane -->
                  </div>
                  <!-- /.tab-content -->
                </div>
                <!-- nav-tabs-custom -->
              </div>

             <div class="clearfix"></div>
             
            </div>
          
          </div>

          <div class="clearfix"></div>
        
  </div>

  <div class="clearfix"></div>
@endsection