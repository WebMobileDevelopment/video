@extends('layouts.admin')

@section('title', tr('videos'))

@section('content-header')

{{tr('videos')}}

	@if(isset($channel)) - <a href="{{ route('admin.channels.view',['channel_id' => $channel->id]) }}"> <span class="text-green"> {{$channel->name}} </span></a> @endif 

	@if(!empty($tag_details)) - <a href="{{ route('admin.tags.index') }}"> <span class="text-green"> {{ $tag_details->name }} </span> </a> @endif 

@endsection

@section('breadcrumb')
    <li class="active"><i class="fa fa-video-camera"></i> {{tr('videos')}}</li>
@endsection

@section('content')

    @include('notification.notify')
	
	<div class="row">
        
        <div class="col-xs-12">
        
          	<div class="box box-primary">

	          	<div class="box-header label-primary">
	                <b style="font-size:18px;">{{tr('videos')}}</b>

	                <a href="{{route('admin.video_tapes.create')}}" class="btn btn-default pull-right">{{tr('add_video')}}</a>
	                
	                <!-- EXPORT OPTION START -->

						@if(count($video_tapes) > 0 )
		                
			                <ul class="admin-action btn btn-default pull-right" style="margin-right: 20px">
			                 	
								<li class="dropdown">
					                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
					                  {{tr('export')}} <span class="caret"></span>
					                </a>
					                <ul class="dropdown-menu">
					                  	<li role="presentation">
					                  		<a role="menuitem" tabindex="-1" href="{{route('admin.video_tapes.export' , ['format' => 'xls'])}}">
					                  			<span class="text-red"><b>{{tr('excel_sheet')}}</b></span>
					                  		</a>
					                  	</li>

					                  	<li role="presentation">
					                  		<a role="menuitem" tabindex="-1" href="{{route('admin.video_tapes.export' , ['format' => 'csv'])}}">
					                  			<span class="text-blue"><b>{{tr('csv')}}</b></span>
					                  		</a>
					                  	</li>
					                </ul>
								</li>
								
							</ul>

						@endif

		            <!-- EXPORT OPTION END -->
	            </div>

	            <div class="box-body">

	            	@if(count($video_tapes) > 0)

		              	<table id="example1" class="table table-bordered table-striped">

							<thead>

							    <tr>
									<th>{{tr('id')}}</th>
									<th>{{tr('title')}}</th>
									<th>{{tr('channel')}}</th>
									<th>{{tr('category')}}</th>
									<th>{{tr('video_type')}}</th>
									<th>User Name</th>
									<th>Video Format</th>

									<?php /*@if(Setting::get('is_banner_video'))
										<th>{{tr('slider_video')}}</th>
									@endif */?>

									<!-- <th>{{tr('likes')}}</th>
									<th>{{tr('dislikes')}}</th> -->
									<th>{{tr('amount')}}</th>

									@if(Setting::get('is_payper_view'))
										<th>{{tr('ppv')}}</th>
									@endif
									<th>{{tr('is_ads')}}</th>

									<th>{{tr('status')}}</th>
									<th>{{tr('action')}}</th>
							    </tr>
							    
							</thead>

							<tbody>

								@foreach($video_tapes as $i => $video_tape_details)
								    <tr>
								      	
								      	<td><a href="{{route('admin.video_tapes.view' , ['video_tape_id' => $video_tape_details->video_tape_id] ) }}">{{$i+1}}</a></td>
								      	
										<td><a href="{{route('admin.video_tapes.view' , ['video_tape_id' => $video_tape_details->video_tape_id] )}}"> {{substr($video_tape_details->title , 0,25)}}...</a></td>
								      	
								      	<td><a href="{{route('admin.channels.view', ['channel_id' => $video_tape_details->channel_id] ) }}">{{$video_tape_details->channel_name}}</a></td>
								      		
								      	<td><a href="{{route('admin.categories.view', ['category_id' => $video_tape_details->category_id] ) }}" target="_blank">{{$video_tape_details->category_name}}</a></td>

								      	<td>
								      		
								      		@if($video_tape_details->video_type == VIDEO_TYPE_UPLOAD) 
	                                            
	                                            {{tr('manual_upload')}}
											@elseif($video_tape_details->video_type == VIDEO_TYPE_R4D) 
											
												R4D
	                                        @elseif($video_tape_details->video_type == VIDEO_TYPE_YOUTUBE)

	                                            {{tr('youtube_links')}}

	                                        @else

	                                            {{tr('other_links')}}

	                                        @endif
								      	</td>
										  <td>
											{{ get_current_user_name($video_tape_details->video_tape_id) }}
										  </td>
										  
										<td> {{pathinfo($video_tape_details->video, PATHINFO_EXTENSION)}} </td>
										  
								      	
								      	<?php /*@if(Setting::get('theme') == 'default')
								      	
									      	<td>
									      		@if($video_tape_details->is_home_slider == 0 && $video_tape_details->is_approved && $video->status)
									      			<a href="{{route('admin.slider.video' , $video['video_tape_id'])}}"><span class="label label-danger">{{tr('set_slider')}}</span></a>
									      		@elseif($video['is_home_slider'])
									      			<span class="label label-success">{{tr('slider')}}</span>
									      		@else
									      			-
									      		@endif
									      	</td>

								      	@endif */?>

								      	<td><b>{{ formatted_amount($video_tape_details->ppv_amount) }}</b></td>

								      	@if(Setting::get('is_payper_view'))
								      	<td class="text-center">
								      		@if($video_tape_details->ppv_amount > 0)
								      			<span class="label label-success">{{tr('yes')}}</span>
								      		@else
								      			<span class="label label-danger">{{tr('no')}}</span>
								      		@endif
								      	</td>
								      	@endif

								      	<td class="text-center">
								      		
								      		@if($video_tape_details->ad_status)
								      			<span class="label label-success">{{tr('yes')}}</span>
								      		@else
								      			<span class="label label-danger">{{tr('no')}}</span>
								      		@endif

								      	</td>

								      	<td>
								      		@if ($video_tape_details->compress_status == 0)
								      			<span class="label label-danger">{{tr('compress')}}</span>
								      		@else
									      		@if($video_tape_details->is_approved)
									      			<span class="label label-success">{{tr('approved')}}</span>
									       		@else
									       			<span class="label label-warning">{{tr('pending')}}</span>
									       		@endif
									       	@endif
								      	</td>
									    <td>
	            							<ul class="admin-action btn btn-default">
	            								
	            								<li class="{{ $i <= 2 ? 'dropdown' : 'dropup' }} ">
									                
									                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
									                  {{tr('action')}} <span class="caret"></span>
									                </a>
									                
									                <ul class="dropdown-menu dropdown-menu-right">

									                  	<li role="presentation"><a role="menuitem" tabindex="-1" target="_blank" href="{{route('admin.video_tapes.view' , ['video_tape_id' => $video_tape_details->video_tape_id] )}}">{{tr('view')}}</a></li>

									                	@if ($video_tape_details->compress_status == DEFAULT_TRUE)
									                  	
									                  	<li role="presentation">
	                                                        @if(Setting::get('admin_delete_control') == YES )
	                                                            <a role="button" href="javascript:;" class="btn disabled" style="text-align: left">{{tr('edit')}}</a>
	                                                        @else
									                			@if ($video_tape_details->video_type == VIDEO_TYPE_R4D)
	                                                            <a role="menuitem" tabindex="-1" href="{{route('admin.video_tapes.edit' , ['video_tape_id' => $video_tape_details->video_tape_id, 'video_type' => $video_tape_details->video_type] )}}">{{tr('edit')}}</a>
																@else
																<a role="menuitem" tabindex="-1" href="{{route('admin.video_tapes.edit' , ['video_tape_id' => $video_tape_details->video_tape_id] )}}">{{tr('edit')}}</a>
																@endif
	                                                        @endif
	                                                    </li>

	                                                    @else

	                                                    	<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.compress.status' , array('id' => $video_tape_details->video_tape_id))}}">{{tr('do_compression_in_background')}}</a></li>

	                                                    @endif

									               		@if(Setting::get('is_payper_view'))

									                  		<li role="presentation">
									                  			<a role="menuitem" tabindex="-1" data-toggle="modal" data-target="#{{$video_tape_details->video_tape_id}}">{{tr('pay_per_view')}}</a>
									                  		</li>

									                  	@endif

									                  	<li class="divider" role="presentation"></li>

									                  	@if($video_tape_details->is_approved)
									                		<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.video_tapes.status', ['video_tape_id' => $video_tape_details->video_tape_id] )}}" onclick="return confirm('{{tr("decline_video")}}')">{{tr('decline')}}</a></li>
									                	@else
									                		
									                		@if ($video_tape_details->compress_status == 0)
									                			<li role="presentation"><a role="menuitem" tabindex="-1">{{tr('compress')}}</a></li>
									                		@else 
									                  			<li role="presentation"><a role="menuitem" tabindex="-1" onclick="return confirm('{{tr("approve_video")}}')" href="{{route('admin.video_tapes.status',['video_tape_id' => $video_tape_details->video_tape_id] )}}">{{tr('approve')}}</a></li>
									                  		@endif
									                  	
									                  	@endif

									                  	@if($video_tape_details->publish_status == 0 && $video_tape_details->compress_status == 1)
									                  		<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.video_tapes.publish',$video_tape_details->video_tape_id)}}">{{tr('publish')}}</a></li>
									                  	@endif

									                  	@if ($video_tape_details->compress_status == 1)
										                  	<li role="presentation">

										                  		@if(Setting::get('admin_delete_control') == YES)

											                  	 	<a role="button" href="javascript:;" class="btn disabled" style="text-align: left">{{tr('delete')}}</a>

											                  	@else
										                  			<a role="menuitem" tabindex="-1" onclick="return confirm('Are you sure?')" href="{{route('admin.video_tapes.delete' , ['video_tape_id' => $video_tape_details->video_tape_id] )}}">{{tr('delete')}}</a>
										                  		@endif
										                
										                  	</li>
									                  	
									                  	@endif

									                  	<li class="divider" role="presentation"></li>

									                  	@if($video_tape_details->ad_status && !$video_tape_details->getScopeVideoAds) 

									                  		<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.video_ads.create', array('video_tape_id'=>$video_tape_details->video_tape_id))}}">{{tr('video_ad')}}</a></li>

									                  	@else

									                  		@if ($video_tape_details->getScopeVideoAds)

									                  		<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.video_ads.view' , ['id' => $video_tape_details->getScopeVideoAds->id] ) }}">{{tr('view_ad')}}</a></li>

									                  		@endif

									                  	@endif

									                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.ads-details.ad-status-change', ['video_tape_id' => $video_tape_details->video_tape_id] )}}">{{ ($video_tape_details->ad_status) ? tr('disable_ad') : tr('enable_ad')}}</a></li>

									                </ul>

	              								</li>

	            							</ul>

									    </td>

								    </tr>

								    <div id="{{$video_tape_details->video_tape_id}}" class="modal fade" role="dialog">
									  	<div class="modal-dialog">
										  	
										  	<form action="{{route('admin.video_tapes.set-ppv', $video_tape_details->video_tape_id)}}" method="POST">
											   
											    <!-- Modal content-->
											   	<div class="modal-content">
											      <div class="modal-header">
											        <button type="button" class="close" data-dismiss="modal">&times;</button>
											        <h4 class="modal-title">{{tr('pay_per_view')}}</h4>
											      </div>
											      <div class="modal-body">
											        <div class="row">

											        	<input type="hidden" name="ppv_created_by" id="ppv_created_by" value="{{Auth::guard('admin')->user()->id}}">

										                <input type="hidden" name="type_of_user" value="{{BOTH_USERS}}">
										                
										            </div>
										            <br>
										            <div class="row">
											        	<div class="col-lg-3">
											        		<label>{{tr('type_of_subscription')}}</label>
											        	</div>
										                <div class="col-lg-9">
										                  <div class="input-group">
										                        <input type="radio" name="type_of_subscription" value="{{ONE_TIME_PAYMENT}}"  {{($video_tape_details->type_of_subscription == 0 || $video_tape_details->type_of_subscription == '') ? 'checked' : (($video_tape_details->type_of_subscription == ONE_TIME_PAYMENT) ? 'checked' : '')}}>&nbsp;<label>{{tr('one_time_payment')}}</label>&nbsp;
										                        <input type="radio" name="type_of_subscription" value="{{RECURRING_PAYMENT}}" {{($video_tape_details->type_of_subscription == RECURRING_PAYMENT) ? 'checked' : ''}}>&nbsp;<label>{{tr('recurring_payment')}}</label>
										                  </div>
										                  <!-- /input-group -->
										                </div>
										            </div>
										            <br>
										            <div class="row">
											        	<div class="col-lg-3">
											        		<label>{{tr('amount')}}</label>
											        	</div>
										                <div class="col-lg-9">
										                       <input type="number" required value="{{$video_tape_details->ppv_amount}}" name="ppv_amount" class="form-control" id="amount" placeholder="{{tr('amount')}}" step="any" maxlength="6">
										                  <!-- /input-group -->
										                </div>
										            </div>
											      </div>
											      <div class="modal-footer">
											      	<div class="pull-left">
											      		@if($video_tape_details->ppv_amount > 0)
											       			<a class="btn btn-danger" href="{{route('admin.video_tapes.remove-ppv', $video_tape_details->video_tape_id)}}">{{tr('remove_pay_per_view')}}</a>
											       		@endif
											       	</div>
											        <div class="pull-right">
												        <button type="button" class="btn btn-default" data-dismiss="modal">{{tr('cancel')}}</button>
												        <button type="submit" class="btn btn-primary">{{tr('submit')}}</button>
												    </div>
												    <div class="clearfix"></div>
											      </div>
											    </div>
											
											</form>
									  	
									  	</div>
									
									</div>
									<!-- Modal -->
									
								@endforeach
							</tbody>
						</table>
					@else
						<h3 class="no-result">{{tr('no_video_found')}}</h3>
					@endif
	            </div>
          	
          	</div>
   
        </div>
   
    </div>

@endsection