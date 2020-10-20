@extends('layouts.admin') 

@section('title', tr('channels')) 

@section('content-header') 

@if(isset($user)) 

	<span class="text-green"> {{ $user->name }} </span>- 

@endif {{ tr('channels') }}

@endsection 

@section('breadcrumb')
	 
	<li class="active"><i class="fa fa-suitcase"></i> {{ tr('channels') }}</li>
@endsection 

@section('content')

<div class="row">

    <div class="col-xs-12">

        @include('notification.notify')

        <div class="box box-primary">

            <div class="box-header label-primary">

                <b style="font-size:18px;">{{ tr('channels') }}</b>
                <a href="{{ route('admin.channels.create') }}" class="btn btn-default pull-right">{{ tr('add_channel') }}</a>

                <!-- EXPORT OPTION START -->

                @if(count($channels) > 0 )

                <ul class="admin-action btn btn-default pull-right" style="margin-right: 20px">
                    
                    <li class="dropdown">

                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
	                      {{ tr('export') }} <span class="caret"></span>
	                    </a>

                        <ul class="dropdown-menu">
                            <li role="presentation">
                                <a role="menuitem" tabindex="-1" href="{{ route('admin.channels.export' , ['format' => 'xlsx']) }}">
                                    <span class="text-red"><b>{{ tr('excel_sheet') }}</b></span>
                                </a>
                            </li>

                            <li role="presentation">
                                <a role="menuitem" tabindex="-1" href="{{ route('admin.channels.export' , ['format' => 'csv']) }}">
                                    <span class="text-blue"><b>{{ tr('csv') }}</b></span>
                                </a>
                            </li>
                        </ul>

                    </li>

                </ul>

                @endif

                <!-- EXPORT OPTION END -->

            </div>
            <div class="box-body table-responsive">

                @if(count($channels) > 0)

                <table id="example1" class="table table-bordered table-striped">

                    <thead>
                        <tr>
                            <th>{{ tr('id') }}</th>
                            <th>{{ tr('channel') }}</th>
                            <th>{{ tr('user_name') }}</th>
                            <th>{{ tr('no_of_videos') }}</th>
                            <th>{{ tr('subscribers') }}</th>
                            <th>{{ tr('amount') }}</th>
                            <th>{{ tr('status') }}</th>
                            <th>{{ tr('action') }}</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($channels as $i => $channel_details)

	                        <tr>
	                            <td>{{ $i+1 }}</td>

	                            <td><a target="_blank" href="{{ route('admin.channels.view',['channel_id' => $channel_details->id] ) }}">{{ $channel_details->name }}</a></td>

	                            <td><a target="_blank" href="{{ route('admin.users.view', ['user_id' => $channel_details->user_id] ) }}">{{ $channel_details->getUser ? $channel_details->getUser->name : '' }}</a></td>

	                            <td><a target="_blank" href="{{ route('admin.channels.videos', ['channel_id'=> $channel_details->id] ) }}">{{ $channel_details->get_video_tape_count }}</a></td>

	                            <td><a target="_blank" href="{{ route('admin.channels.subscribers', ['channel_id' => $channel_details->id]) }}">{{ $channel_details->get_channel_subscribers_count }}</a></td>

	                            <td>{{ formatted_amount(getAmountBasedChannel($channel_details->id)) }}</td>

	                            <td>
	                                @if($channel_details->is_approved)
	                                <span class="label label-success">{{ tr('approved') }}</span> @else
	                                <span class="label label-warning">{{ tr('pending') }}</span> @endif
	                            </td>

	                            <td>
	                                <ul class="admin-action btn btn-default">

	                                    <li class="{{ $i <=2 ? 'dropdown' : 'dropup' }}">

	                                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
									           {{ tr('action') }} <span class="caret"></span>
									        </a>
	                                        <ul class="dropdown-menu">

                                                <li role="presentation">
                                                    <a role="menuitem" tabindex="-1" href="{{ route('admin.channels.view',['channel_id' => $channel_details->id] ) }}">{{tr('view')}}</a>
                                                </li>

                                                @if(Setting::get('admin_delete_control') == YES)
                                                
                                                    <li role="presentation"><a role="button" href="javascript:;" class="btn disabled" style="text-align: left">{{ tr('edit') }}</a></li> 

                                                    <li role="presentation"><a role="button"  href="javascript:;" class="btn disabled" style="text-align: left" onclick="return confirm(&quot;{{ tr('admin_channel_delete_confirmation', $channel_details->name) }}&quot;)" >{{ tr('delete') }}</a></li> 
                                                    
                                                @else

                                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="{{ route('admin.channels.edit' , ['channel_id' => $channel_details->id] ) }}">{{ tr('edit') }}</a></li> 

                                                    <li role="presentation"><a role="menuitem" tabindex="-1" onclick="return confirm(&quot;{{ tr('admin_channel_delete_confirmation', $channel_details->name) }}&quot;)" href="{{ route('admin.channels.delete' , ['channel_id' => $channel_details->id] ) }}">{{ tr('delete') }}</a></li>

                                                @endif
                                          
	                                            <li class="divider" role="presentation"></li>

                                                <li role="presentation">
    	                                            @if($channel_details->is_approved == APPROVED)
    	                                            	<a role="menuitem" tabindex="-1" href="{{ route('admin.channels.status' , ['channel_id' => $channel_details->id] ) }}" onclick="return confirm(&quot;{{ tr('admin_channel_decline_notes', $channel_details->name) }}&quot;)"> {{ tr('decline') }}</a>
    	                                            @else
    	                                            	<a role="menuitem" tabindex="-1" href="{{ route('admin.channels.status' , ['channel_id' => $channel_details->id] ) }}" onclick="return confirm(&quot;{{ tr('admin_channel_approve_notes', $channel_details->name) }}&quot;)"> {{ tr('approve') }}</a>
    	                                            @endif
                                                </li>

                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="{{ route('admin.channels.playlists.index' , ['channel_id' => $channel_details->id] ) }}">{{ tr('playlist') }}</a></li>

	                                        </ul>

	                                    </li>

	                                </ul>

	                            </td>

	                        </tr>

                        @endforeach

                    </tbody>

                </table>

                @else

                	<h3 class="no-result">{{ tr('no_result_found') }}</h3> 

                @endif

            </div>

        </div>

    </div>

</div>


@endsection