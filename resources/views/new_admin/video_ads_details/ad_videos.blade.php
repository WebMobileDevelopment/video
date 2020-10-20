@extends('layouts.admin')

@section('title', tr('assigned_ads'))

@section('content-header', tr('assigned_ads'))

@section('breadcrumb')
     
    <li class="active"><i class="fa fa-bullhorn"></i> {{ tr('assigned_ads') }}</li>
@endsection

@section('content')

	<div class="row">

        <div class="col-xs-12">
    		
    		@include('notification.notify')

          	<div class="box box-primary">

	          	<div class="box-header label-primary">
	                <b style="font-size:18px;">{{ tr('assigned_ads') }}</b>
	            </div>

	            <div class="box-body">

	            	@if(count($video_ads) > 0)

		              	<table id="example1" class="table table-bordered table-striped">

							<thead>
							    <tr>
							      <th>{{tr('id')}}</th>
							      <th>{{tr('channel')}}</th>
							      <th>{{tr('title')}}</th>
							      <th>{{tr('type_of_ads')}}</th>
							      <th>{{tr('status')}}</th>
							      <th>{{tr('updated_at')}}</th>
							      <th>{{tr('created_at')}}
							      <th>{{ tr('action') }}</th>
							    </tr>
							</thead>

							<tbody>

								@foreach($video_ads as $i => $video_ad_details)

								    <tr>
								      	<td>{{ $i+1 }}</td>
								      							      		
								      	<td>
								      		@if($video_ad_details->name)
								      		
								      			<a href="{{ route('admin.channels.view', ['channel_id' => $video_ad_details->channel_id] ) }}" target="_blank">{{ $video_ad_details->name }}</a>

								      		@endif
								      	</td>

								      	<td>
								      		@if($video_ad_details->title)

								      			<a href="{{ route('admin.video_tapes.view', ['video_tape_id' => $video_ad_details->video_tape_id] ) }}" target="_blank">{{ substr($video_ad_details->title , 0,25) }}</a>
								      		@endif
								      	</td>

								      	<td>
								      		<?php $types = getTypeOfAds($video_ad_details->types_of_ad);?>

								      		@foreach($types as $type)
									      		
									      		<span class="label label-success">{{ $type }}</span>

									       	@endforeach
								      	</td>

								      	<td>							      		
								      		@if($video_ad_details->status)
								      			<span class="label label-success">{{ tr('approved') }}</span>
								       		@else
								       			<span class="label label-danger">{{ tr('pending') }}</span>
								       		@endif
								      	</td>

								      	<td>
								      		{{common_date($video_ad_details->created_at,Auth::guard('admin')->user()->timezone,'d M Y H:i:s')}}
								      	</td>

								      	<td>
								      		{{common_date($video_ad_details->updated_at,Auth::guard('admin')->user()->timezone,'d M Y H:i:s')}}
								      	</td>

									    <td>
	            							<ul class="admin-action btn btn-default">
	            								
	            								<li class="dropup">
									                
									                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
									                  {{ tr('action') }} <span class="caret"></span>
									                </a>
									                
									                <ul class="dropdown-menu">
									                	
	                                                    
									                  	<li role="presentation"><a role="menuitem" tabindex="-1" target="_blank" href="{{ route('admin.video_ads.view' , ['id' => $video_ad_details->id] ) }}">{{ tr('view') }}</a></li>

                                                        @if(Setting::get('admin_delete_control'))
                                                            <li role="presentation"><a role="button" href="javascript:;" class="btn disabled" style="text-align: left">{{ tr('edit') }}</a></li>

                                                            <li role="presentation"><a role="button" href="javascript:;" class="btn disabled" style="text-align: left">{{ tr('delete') }}</a></li>
                                                        @else
                                                            <li role="presentation"><a role="menuitem" tabindex="-1" href="{{ route('admin.video_ads.edit' , ['id' => $video_ad_details->id] ) }}">{{ tr('edit') }}</a></li>
                                                            <li role="presentation"><a role="menuitem" tabindex="-1" onclick="return confirm(&quot;{{ tr('are_you_sure') }}&quot;);" href="{{ route('admin.video_ads.delete' ,  ['id' => $video_ad_details->id] ) }}">{{ tr('delete') }}</a></li>

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
						<h3 class="no-result">{{ tr('no_assigned_ads_found') }}</h3>
					@endif
	            </div>

          	</div>

        </div>

    </div>

@endsection