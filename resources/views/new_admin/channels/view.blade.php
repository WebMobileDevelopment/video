 @extends('layouts.admin') 

@section('title', tr('view_channel')) 

@section('content-header', tr('view_channel')) 

@section('breadcrumb')

     
    <li><a href="{{ route('admin.channels.index') }}"><i class="fa fa-suitcase"></i> {{ tr('channels') }}</a></li>
    <li class="active">{{ tr('view_channel') }}</li>

@endsection 

@section('content') 


<div class="col-md-12">

    @include('notification.notify')

    <!-- Widget: user widget style 1 -->
    <div class="box box-widget widget-user">
        <!-- Add the bg color to the header using any of the bg-* classes -->
        <div class="widget-user-header bg-black" style="background: #000 url('{{ $channel_details->cover }}') center center;">
            <h3 class="widget-user-username text-capitalize">{{ $channel_details->name }}</h3>
            <h5 class="widget-user-desc">{{ tr('channel') }}</h5>
        </div>

        <div class="widget-user-image">
            <img class="img-circle" src="{{ $channel_details->picture ?: asset('placeholder.png') }}" alt="User Avatar" style="height: 90px">
        </div>

        <div class="box-footer">
           
            <div class="row">
                
                <div class="col-sm-3 border-right">
                    <div class="description-block">
                        <h5 class="description-header"><a target="_blank" href="{{ route('admin.channels.videos', ['channel_id' => $channel_details->id] ) }}">{{ $channel_details->get_video_tape_count }}</a></h5>
                        <span class="description-text">{{ tr('videos') }}</span>
                    </div>
                    <!-- /.description-block -->
                </div>
                <!-- /.col -->
                
                <div class="col-sm-3 border-right">
                    <div class="description-block">
                        <h5 class="description-header"><a target="_blank" href="{{ route('admin.channels.subscribers', ['channel_id' => $channel_details->id] ) }}">{{ $channel_details->get_channel_subscribers_count }}</a></h5>
                        <span class="description-text">{{ tr('subscribers') }}</span>
                    </div>
                    <!-- /.description-block -->
                </div>
                <!-- /.col -->
                
                <div class="col-sm-3">
                    <div class="description-block">
                        <h5 class="description-header">{{ Setting::get('currency') }} {{ number_format_short($channel_earnings) }}</h5>
                        <span class="description-text">{{ tr('earnings') }}</span>
                    </div>
                    <!-- /.description-block -->
                </div>

                <div class="col-sm-3">
                    <div class="description-block">
                        <h5 class="description-header">
                            <a target="_blank" href="{{ route('admin.channels.playlists.index', ['channel_id' => $channel_details->id] ) }}">{{ $channel_details->get_playlist_count }}</a>
                        </h5>
                        <span class="description-text">{{ tr('playlist') }}</span>
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

        <div class="pull-right">            

            @if(Setting::get('admin_delete_control') == YES)
                                                
                <a type="button" class="btn btn-warning" title="{{ tr('edit') }}" href="javascript:;"><b><i class="fa fa-edit"></i></b></a></li> 

                <a type="button" class="btn btn-danger" title="{{ tr('delete') }}" href="javascript:;"  onclick="return confirm(&quot;{{ tr('admin_channel_delete_confirmation', $channel_details->name) }}&quot;)" t><b><i class="fa fa-trash"></i></b></a>
                
            @else 

                <a type="button" class="btn btn-warning" title="{{ tr('edit') }}" href="{{ route('admin.channels.edit' , ['channel_id' => $channel_details->id] ) }}" ><b><i class="fa fa-edit"></i></b></a>

                <a type="button" class="btn btn-danger" title="{{ tr('delete') }}" onclick="return confirm(&quot;{{ tr('admin_channel_delete_confirmation', $channel_details->name) }}&quot;)"  href="{{ route('admin.channels.delete' , ['channel_id' => $channel_details->id] ) }}" ><b><i class="fa fa-trash"></i></b></a>

            @endif

            @if($channel_details->is_approved == APPROVED)
                <a type="button" class="btn btn-warning" title="{{ tr('decline') }}" href="{{ route('admin.channels.status' , ['channel_id' => $channel_details->id] ) }}" onclick="return confirm(&quot;{{ tr('admin_channel_decline_notes', $channel_details->name) }}&quot;)"><b><i class="fa fa-close"></i></b></a>
            @else
                <a type="button" class="btn btn-success" title=" {{ tr('approve') }}" href="{{ route('admin.channels.status' , ['channel_id' => $channel_details->id] ) }}" onclick="return confirm(&quot;{{ tr('admin_channel_approve_notes', $channel_details->name) }}&quot;)"><b><i class="fa fa-check"></i></b></a>
            @endif

            <a type="button" class="btn btn-info" title="{{ tr('add_channel') }}" href="{{ route('admin.channels.create') }}" ><b><i class="fa fa-plus"></i></b></a>

        </div>

            <div class="user-block">

                <img class="img-circle" src="{{ $channel_details->user_picture ?: asset('placeholder.png') }}" alt="{{ $channel_details->user_name }}">
                <span class="username"><a target="_blank" href="{{ route('admin.users.view', ['user_id' => $channel_details->user_id] ) }}">{{ $channel_details->user_name }}</a></span>
                <span class="description">{{ tr('shared_publicly') }} - {{ $channel_details->created_at->diffForHumans() }}</span>

            </div>     
            <!-- /.box-tools -->
        </div>
        <!-- /.box-header -->
        <div class="box-body">

            <div class="row col-md-12">
                <!-- Custom Tabs -->
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_1" data-toggle="tab">{{ tr('about_channel') }}</a></li>
                        <li><a href="#tab_2" data-toggle="tab">{{ tr('videos') }}</a></li>
                        <li><a href="#tab_3" data-toggle="tab">{{ tr('subscribers') }}</a></li>

                        <li><a href="#tab_4" data-toggle="tab">{{ tr('playlist') }}</a></li>
                    </ul>
                    <div class="tab-content">
                        
                        <div class="tab-pane active" id="tab_1">
                            <table class="table">
                                <tr>
                                    <th>{{ tr('name') }}</th>
                                    <td>{{ $channel_details->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ tr('description') }}</th>
                                    <td>
                                        <?= $channel_details->description?>
                                    </td>
                                </tr>
                            </table>

                        </div>
                        
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_2">

                            <blockquote>
                                <p>{{ tr('videos_short_notes') }}</p>
                                <small>{{ tr('to_view_more') }} <cite><a target="_blank" href="{{ route('admin.channels.videos', ['channel_id' => $channel_details->id] ) }}">{{ tr('click_here') }}</a></cite></small>
                            </blockquote>

                            @if($channel_details->get_video_tape_count > 0) 

                                @foreach($videos as $video)
                                    <div class="box-comments">
                                        <!-- /.box-comment -->
                                        <div class="box-comment">
                                            <!-- User image -->
                                            <img class="img-circle img-sm" src="{{ $video->default_image }}" alt="{{ $channel_details->user_name }}">

                                            <div class="comment-text">
                                                <span class="username">
                                          <a href="{{ route('admin.video_tapes.view', ['video_tape_id' => $video->id] ) }}" target="_blank">{{ $video->title }}</a>
                                          <span class="text-muted pull-right">{{ $video->created_at->diffForHumans() }}</span>
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

                            @else 

                                {{ tr('no_videos_found') }} 

                            @endif
                        </div>
                        
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_3">
                            
                            <blockquote>
                                <p>{{ tr('subscribers_short_notes') }}</p>
                                <small>{{ tr('to_view_more') }} <cite><a  target="_blank" href="{{ route('admin.channels.subscribers', ['channel_id' => $channel_details->id] ) }}">{{ tr('click_here') }}</a></cite></small>
                            </blockquote>

                            @if($channel_details->get_channel_subscribers_count > 0) 

                                @foreach($channel_subscriptions as $i => $channel_subscription_details)

                                    <div class="col-sm-6 col-md-6">
                                        
                                        <div class="box box-solid">
                                            
                                            <div class="box-body">
                                                
                                                <h4 style="background-color:#f7f7f7; font-size: 18px; text-align: center; padding: 7px 10px; margin-top: 0;">
                                                {{ $channel_subscription_details->user_name }}
                                                </h4>
                                                
                                                <div class="media">
                                                    
                                                    <div class="media-left">
                                                        
                                                        <a href="{{ route('admin.users.view', ['user_id' => $channel_subscription_details->user_id ] ) }}" target="_blank">
                                                        
                                                        <img src="{{ $channel_subscription_details->user_picture ?: asset('placeholder.png') }}" alt="{{ $channel_subscription_details->user_name }}" class="media-object" style="width: 150px;height: auto;border-radius: 4px;box-shadow: 0 1px 3px rgba(0,0,0,.15);">
                                                        </a>
                                                    </div>

                                                    <div class="media-body">
                                                        <div class="clearfix">
                                                            <p class="pull-right">
                                                                <a class="btn btn-success btn-sm" href="{{ route('admin.users.view', ['user_id' => $channel_subscription_details->user_id] ) }}" target="_blank">
                                                                {{ tr('view') }}
                                                            </a>
                                                            </p>

                                                            <h4 style="margin-top: 0;text-align:nowrap;overflow: hidden;text-overflow: ellipsis;">{{ $channel_subscription_details->email }}</h4>

                                                            <p style="max-height: 80px;overflow-y: hidden;">{{ $channel_subscription_details->description }}</p>
                                                        </div>
                                                        
                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                    @if ($i % 2 == 0) @else

                                        <div class="clearfix"></div>

                                    @endif 

                                @endforeach 

                            @else 

                                {{ tr('no_subscribers_found') }} 

                            @endif

                            <div class="clearfix"></div>
                        </div>
                        <!-- /.tab-pane -->

                         <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_4">
                            
                           <blockquote>
                                <p>{{ tr('playlist_short_notes') }}</p>
                                <small>{{ tr('to_view_more') }} <cite><a target="_blank" href="{{ route('admin.channels.playlists.index', ['channel_id' => $channel_details->id] ) }}">{{ tr('click_here') }}</a></cite></small>
                            </blockquote>
                                
                                @if($channel_playlists) 
    
                                <div class="box-comments">
                                   
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th><center>{{ tr('playlist') }}</center></th>
                                                <th><center>{{ tr('total_videos') }}</center></th>
                                            </tr>
                                        </thead>
                                        @foreach($channel_playlists as $channel_playlist_details)
                                        <tr>                                        
                                            <td>
                                                <center>
                                                <a href="{{ route('admin.channels.playlists.view' , [ 'channel_id' => $channel_details->id, 'playlist_id' => $channel_playlist_details->playlist_id] ) }}">
                                                {{ $channel_playlist_details->title }}</a>
                                                </center>
                                            </td>
                                        
                                            <td>
                                                <center>{{ $channel_playlist_details->total_videos}}</center>
                                            </td>
                                        </tr>

                                        @endforeach

                                    </table> 

                                </div>
                                
                                @else 

                                    {{ tr('no_videos_found') }} 

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