 @extends('layouts.admin') 

 @section('title', tr('view_channel')) 
 
 @section('content-header', tr('view_channel')) 

 @section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li><a href="{{route('admin.channels')}}"><i class="fa fa-suitcase"></i> {{tr('channels')}}</a></li>
    <li class="active">{{tr('view_channel')}}</li>
@endsection 

@section('content') 

@include('notification.notify')

<div class="col-md-12">
    <!-- Widget: user widget style 1 -->
    <div class="box box-widget widget-user">
        <!-- Add the bg color to the header using any of the bg-* classes -->
        <div class="widget-user-header bg-black" style="background: #000 url('{{$channel->cover}}') center center;">
            <h3 class="widget-user-username text-capitalize">{{$channel->name}}</h3>
            <h5 class="widget-user-desc">{{tr('channel')}}</h5>
        </div>
        <div class="widget-user-image">
            <img class="img-circle" src="{{$channel->picture}}" alt="User Avatar" style="height: 90px">
        </div>
        <div class="box-footer">
            <div class="row">
                <div class="col-sm-4 border-right">
                    <div class="description-block">
                        <h5 class="description-header"><a target="_blank" href="{{route('admin.channels.videos', array('id'=> $channel->id))}}">{{$channel->get_video_tape_count}}</a></h5>
                        <span class="description-text">{{tr('videos')}}</span>
                    </div>
                    <!-- /.description-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-4 border-right">
                    <div class="description-block">
                        <h5 class="description-header"><a  target="_blank" href="{{route('admin.channels.subscribers', array('id'=> $channel->id))}}">{{$channel->get_channel_subscribers_count}}</a></h5>
                        <span class="description-text">{{tr('subscribers')}}</span>
                    </div>
                    <!-- /.description-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-4">
                    <div class="description-block">
                        <h5 class="description-header">{{Setting::get('currency')}} {{number_format_short($channel_earnings)}}</h5>
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
            <div class="user-block">
                <img class="img-circle" src="{{$channel->user_picture}}" alt="{{$channel->user_name}}">
                <span class="username"><a target="_blank" href="{{route('admin.users.view', $channel->user_id)}}">{{$channel->user_name}}</a></span>
                <span class="description">{{tr('shared_publicly')}} - {{$channel->created_at->diffForHumans()}}</span>
              </div>
                <div class="pull-right">
                  <a href="{{route('admin.channels.edit' ,['id'=>$channel->id])}}" class="btn btn-xs btn-warning" title="Edit">
                      <i class="fa fa-edit"></i>
                  </a>
                </div>
             
              <!-- /.box-tools -->

            </div>

            <!-- /.box-tools -->
        </div>
        <!-- /.box-header -->
        <div class="box-body">

            <div class="row col-md-12">
                <!-- Custom Tabs -->
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_1" data-toggle="tab">{{tr('about_channel')}}</a></li>
                        <li><a href="#tab_2" data-toggle="tab">{{tr('videos')}}</a></li>
                        <li><a href="#tab_3" data-toggle="tab">{{tr('subscribers')}}</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_1">
                            <table class="table">
                                <tr>
                                    <th>{{tr('name')}}</th>
                                    <td>{{$channel->name}}</td>
                                </tr>
                                <tr>
                                    <th>{{tr('description')}}</th>
                                    <td>
                                        <?= $channel->description?>
                                    </td>
                                </tr>
                            </table>

                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_2">

                            <blockquote>
                                <p>{{tr('videos_short_notes')}}</p>
                                <small>{{tr('to_view_more')}} <cite><a target="_blank" href="{{route('admin.channels.videos', array('id'=> $channel->id))}}">{{tr('click_here')}}</a></cite></small>
                            </blockquote>

                            @if($channel->get_video_tape_count > 0) 

                                @foreach($videos as $video)
                                    <div class="box-comments">
                                        <!-- /.box-comment -->
                                        <div class="box-comment">
                                            <!-- User image -->
                                            <img class="img-circle img-sm" src="{{$video->default_image}}" alt="{{$channel->user_name}}">

                                            <div class="comment-text">
                                                <span class="username">
                                          <a href="{{route('admin.video_tapes.view', array('id' => $video->id))}}" target="_blank">{{$video->title}}</a>
                                          <span class="text-muted pull-right">{{$video->created_at->diffForHumans()}}</span>
                                                </span>
                                                <!-- /.username -->
                                                <div class="description">
                                                    <?=$video->description?>
                                                </div>
                                            </div>
                                            <!-- /.comment-text -->
                                        </div>
                                    </div>
                            @endforeach 

                            @else {{tr('no_videos_found')}} 

                            @endif
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_3">
                            <blockquote>
                                <p>{{tr('subscribers_short_notes')}}</p>
                                <small>{{tr('to_view_more')}} <cite><a  target="_blank" href="{{route('admin.channels.subscribers', array('id'=> $channel->id))}}">{{tr('click_here')}}</a></cite></small>
                            </blockquote>

                            @if($channel->get_channel_subscribers_count > 0) 

                                @foreach($subscribers as $i => $subscriber)

                                    <div class="col-sm-6 col-md-6">
                                        <div class="box box-solid">
                                            <div class="box-body">
                                                <h4 style="background-color:#f7f7f7; font-size: 18px; text-align: center; padding: 7px 10px; margin-top: 0;">
                                                {{$subscriber->user_name}}
                                            </h4>
                                                <div class="media">
                                                    <div class="media-left">
                                                        <a href="{{route('admin.users.view', array('id' => $subscriber->user_id))}}" target="_blank">
                                                        <img src="{{$subscriber->user_picture}}" alt="{{$subscriber->user_name}}" class="media-object" style="width: 150px;height: auto;border-radius: 4px;box-shadow: 0 1px 3px rgba(0,0,0,.15);">
                                                    </a>
                                                    </div>
                                                    <div class="media-body">
                                                        <div class="clearfix">
                                                            <p class="pull-right">
                                                                <a class="btn btn-success btn-sm" href="{{route('admin.users.view', array('id' => $subscriber->user_id))}}" target="_blank">
                                                                {{tr('view')}}
                                                            </a>
                                                            </p>

                                                            <h4 style="margin-top: 0;text-align:nowrap;overflow: hidden;text-overflow: ellipsis;">{{$subscriber->email}}</h4>

                                                            <p style="max-height: 80px;overflow-y: hidden;">{{$subscriber->description}}</p>
                                                            <!-- <p style="margin-bottom: 0">
                                                            <i class="fa fa-shopping-cart margin-r5"></i> 12+ purchases
                                                        </p> -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if ($i % 2 == 0) 

                                    @else

                                    <div class="clearfix"></div>

                                    @endif 

                                @endforeach 

                            @else 

                                {{tr('no_subscribers_found')}} 

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