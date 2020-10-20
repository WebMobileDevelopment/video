@extends('layouts.admin')

@section('title', tr('custom_live_videos'))

@section('content-header', tr('custom_live_videos'))

@section('breadcrumb')
     
    <li class="active"><i class="fa fa-video-camera"></i> {{ tr('custom_live_videos') }}</li>
@endsection

@section('content')

    <div class="row">
        
        <div class="col-xs-12">

            @include('notification.notify')

            <div class="box box-primary">

                <div class="box-header label-primary">
                    <b style="font-size:18px;">{{ tr('custom_live_videos') }}</b>
                    <a href="{{ route('admin.custom.live.create') }}" class="btn btn-default pull-right">{{ tr('create_custom_live_video') }}</a>
                </div>

                <div class="box-body">

                    @if(count($custom_live_videos) > 0)

                        <table id="example1" class="table table-bordered table-striped">

                            <thead>
                                <tr>
                                  <th>{{ tr('id') }}</th>
                                  <th>{{ tr('title') }}</th>
                                  <th>{{ tr('description') }}</th>
                                  <th>{{ tr('image') }}</th>
                                  <th>{{ tr('status') }}</th>
                                  <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($custom_live_videos as $i => $custom_live_video_details)

                                    <tr>
                                        <td>{{ $i+1 }}</td>

                                        <td><a href="{{ route('admin.custom.live.view' , ['custom_live_video_id' => $custom_live_video_details->id] ) }}">{{ substr($custom_live_video_details->title , 0,25) }}...</a></td>

                                        <td>{{ substr($custom_live_video_details->description , 0,25) }}...</td>

                                        <td><img src="{{ $custom_live_video_details->image }}" style="width: 75px;height: 50px;"></td>
                                        <td>                                            
                                            @if($custom_live_video_details->status == DEFAULT_TRUE)
                                                <span class="label label-success">{{ tr('approved') }}</span>
                                            @else
                                                <span class="label label-warning">{{ tr('pending') }}</span>
                                            @endif
                                        </td>

                                        <td>
                                            <ul class="admin-action btn btn-default">
                                                <li class="dropup">
                                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                                      {{ tr('action') }} <span class="caret"></span>
                                                    </a>
                                                    <ul class="dropdown-menu">
                                                        
                                                        <li role="presentation"><a role="menuitem" tabindex="-1" target="_blank" href="{{ route('admin.custom.live.view' , ['custom_live_video_id' => $custom_live_video_details->id] ) }}">{{ tr('view') }}</a></li>
                                                           
                                                        @if(Setting::get('admin_delete_control') == YES )
                                                            
                                                            <li role="presentation"><a role="button" href="javascript:;" class="btn disabled" style="text-align: left">{{ tr('edit') }}</a></li>

                                                            <li role="presentation"><a role="button" href="javascript:;" class="btn disabled" style="text-align: left">{{ tr('delete') }}</a></li>

                                                        @else

                                                            <li role="presentation"> <a role="menuitem" tabindex="-1" href="{{ route('admin.custom.live.edit' , ['custom_live_video_id' => $custom_live_video_details->id] ) }}">{{ tr('edit') }}</a></li>

                                                            <li role="presentation"> <a role="menuitem" tabindex="-1" href="{{ route('admin.custom.live.delete' , ['custom_live_video_id' => $custom_live_video_details->id] ) }}" onclick="return confirm(&quot;{{ tr('admin_custom_live_video_delete_confirmation', $custom_live_video_details->title ) }}&quot;);" >{{ tr('delete') }}</a></li>

                                                        @endif

                                                        <li class="divider" role="presentation"></li>

                                                        @if($custom_live_video_details->status == YES)
                                                            <li role="presentation"><a role="menuitem" tabindex="-1" href="{{ route('admin.custom.live.status', ['custom_live_video_id' => $custom_live_video_details->id] ) }}" onclick="return confirm(&quot;{{ tr('admin_custom_live_video_decline_confirmation', $custom_live_video_details->title) }}&quot;)" >{{ tr('decline') }}</a></li>
                                                        @else
                                                            
                                                            <li role="presentation"><a role="menuitem" tabindex="-1" href="{{ route('admin.custom.live.status', ['custom_live_video_id' => $custom_live_video_details->id]) }}" onclick="return confirm(&quot;{{ tr('admin_custom_live_video_approve_confirmation', $custom_live_video_details->title) }}&quot;)">{{ tr('approve') }}</a></li>
                                                            
                                                        @endif
                                                        
                                                    </ul>

                                                </li>

                                            </ul>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    @else
                        <h3 class="no-result">{{ tr('no_video_found') }}</h3>
                    @endif
                </div>

            </div>

        </div>

    </div>

@endsection
