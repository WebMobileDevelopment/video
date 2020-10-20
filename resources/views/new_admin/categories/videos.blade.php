@extends('layouts.admin')

@section('title', tr('videos'))

@section('content-header')

	<a href="{{ route('admin.categories.view',['category_id' => $category_details->id ] ) }}">{{ $category_details->name }}</a> - {{ tr('category') }} {{ tr('videos') }}

@endsection

@section('breadcrumb')
         
    <li><a href="{{ route('admin.categories.index') }}"><i class="fa fa-list"></i> {{ tr('category') }} </a></li> 
    <li><a href="{{ route('admin.categories.view',['category_id' => $category_details->id ]) }}"><i class="fa fa-list"></i> {{ tr('view_category') }} </a></li>
    <li class="active"><i class="fa fa-video-camera"></i> {{ tr('videos') }}</li>
@endsection

@section('content')

	<div class="row">

        <div class="col-xs-12">
    		
    		@include('notification.notify')

          	<div class="box box-primary">

	          	<div class="box-header label-primary">
	                <b style="font-size:18px;"> {{ $category_details->name }} - {{ tr('category') }} {{ tr('videos') }}</b>
	                <a href="{{ route('admin.video_tapes.create') }}" class="btn btn-default pull-right">{{ tr('add_video') }}</a>
	            </div>

	            <div class="box-body">

	            	@if(count($videos) > 0)

		              	<table id="example1" class="table table-bordered table-striped">

							<thead>

							    <tr>
									<th>{{ tr('id') }}</th>
									<th>{{ tr('channel') }}</th>
									<th>{{ tr('title') }}</th>
									<th>{{ tr('amount') }}</th>

									@if(Setting::get('is_payper_view'))
										<th>{{ tr('ppv') }}</th>
									@endif
									<th>{{ tr('is_ads') }}</th>

									<th>{{ tr('status') }}</th>
									<th>{{ tr('action') }}</th>
							    </tr>
							    
							</thead>

							<tbody>

								@foreach($videos as $i => $video_details)

								    <tr>
								      	
								      	<td>
								      		<a href="{{ route('admin.video_tapes.view' , ['video_tapes_id' => $video_details->video_tape_id] ) }}">{{ $i+1 }}</a>
								      	</td>
								      	
								      	<td>
								      		<a href="{{ route('admin.channels.view', ['channel_id' => $video_details->channel_id] ) }}">{{ $video_details->channel_name }}</a>
								      	</td>
								      	
								      	<td>
								      		<a href="{{ route('admin.video_tapes.view' , ['video_tape_id' => $video_details->video_tape_id] ) }}"> {{ substr($video_details->title , 0,25) }}...</a>
								      	</td>								      	

								      	<td>
								      		<b>{{ formatted_amount($video_details->admin_ppv_amount) }}</b>
								      	</td>

								      	@if(Setting::get('is_payper_view') == DEFAULT_TRUE)
								      	<td class="text-center">
								      		@if($video_details->ppv_amount > 0)
								      			<span class="label label-success">{{ tr('yes') }}</span>
								      		@else
								      			<span class="label label-danger">{{ tr('no') }}</span>
								      		@endif
								      	</td>
								      	@endif

								      	<td class="text-center">
								      		
								      		@if($video_details->ad_status == DEFAULT_TRUE )
								      			<span class="label label-success">{{ tr('yes') }}</span>
								      		@else
								      			<span class="label label-danger">{{ tr('no') }}</span>
								      		@endif

								      	</td>

								      	<td>
								      		@if ($video_details->compress_status == DEFAULT_FALSE)
								      			
								      			<span class="label label-danger">{{ tr('compress') }}</span>

								      		@else

									      		@if($video_details->is_approved == YES )
									      			<span class="label label-success">{{ tr('approved') }}</span>
									       		@else
									       			<span class="label label-warning">{{ tr('pending') }}</span>
									       		@endif

									       	@endif
								      	</td>
									    <td>
	            							<ul class="admin-action btn btn-default">

	            								<li class="dropup">
									                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
									                  {{ tr('action') }} <span class="caret"></span>
									                </a>
									                <ul class="dropdown-menu">

									                	@if ($video_details->compress_status == DEFAULT_TRUE )

									                  	<li role="presentation">

	                                                        @if(Setting::get('admin_delete_control') == YES )
	                                                            <a role="menuitem" tabindex="-1"  href="javascript:;" class="btn disabled" style="text-align: left">{{ tr('edit') }}</a>
	                                                            	
	                                                            <a role="menuitem" tabindex="-1"  href="javascript:;" class="btn disabled" style="text-align: left">{{ tr('delete') }}</a>

	                                                        @else
	                                                            <a role="menuitem" tabindex="-1" href="{{ route('admin.video_tapes.edit' , ['video_tape_id' => $video_details->video_tape_id] ) }}">{{ tr('edit') }}</a>
	                                                        	
	                                                        	<a role="menuitem" tabindex="-1"
										                  			onclick="return confirm(&quot;{{ tr('admin_live_custom_video_delete_confirmation', substr($video_details->title , 0,25) ) }}&quot;)"
										                  			 href="{{ route('admin.video_tapes.delete' , ['video_tape_id' => $video_details->video_tape_id] ) }}">{{ tr('delete') }}</a>
	                                                        @endif
	                                                    </li>

	                                                    @endif

									                  	<li role="presentation"><a role="menuitem" tabindex="-1" target="_blank" href="{{ route('admin.video_tapes.view' , ['video_tape_id' => $video_details->video_tape_id] ) }}">{{ tr('view') }}</a></li>

									               		@if(Setting::get('is_payper_view') == DEFAULT_TRUE)

									                  		<li role="presentation">
									                  			<a role="menuitem" tabindex="-1" data-toggle="modal" data-target="#{{ $video_details->video_tape_id }}">{{ tr('pay_per_view') }}</a>
									                  		</li>

									                  	@endif

									                  	<li class="divider" role="presentation"></li>

									                  	@if($video_details->is_approved == YES )
									                		<li role="presentation"><a role="menuitem" tabindex="-1" href="{{ route('admin.video_tapes.status',$video_details->video_tape_id) }}" onclick="return confirm(&quot;{{ tr('admin_live_custom_video_decline_confirmation',substr($video_details->title , 0,25) ) }}&quot;)" >{{ tr('decline') }}</a></li>
									                	@else
									                		@if ($video_details->compress_status == NO)
									                			<li role="presentation"><a role="menuitem" tabindex="-1">{{ tr('compress') }}</a></li>
									                		@else 
									                  			<li role="presentation"><a role="menuitem" tabindex="-1" href="{{ route('admin.video_tapes.status',$video_details->video_tape_id) }}">{{ tr('approve') }}</a></li>
									                  		@endif
									                  	@endif

									                  	@if($video_details->publish_status == DEFAULT_FALSE && $video_details->compress_status == DEFAULT_TRUE)
									                  		<li role="presentation"><a role="menuitem" tabindex="-1" href="{{ route('admin.video_tapes.publish',$video_details->video_tape_id) }}">{{ tr('publish') }}</a></li>
									                  	@endif
									                  	<li class="divider" role="presentation"></li>

									                  	@if($video_details->ad_status && !$video_details->getScopeVideoAds) 

									                  		<li role="presentation"><a role="menuitem" tabindex="-1" href="{{ route('admin.video_ads.create', ['video_tape_id'=>$video_details->video_tape_id] ) }}">{{ tr('video_ad') }}</a></li>

									                  	@else

									                  		@if ($video_details->getScopeVideoAds)

									                  		<li role="presentation"><a role="menuitem" tabindex="-1" href="{{  route('admin.video_ads.view' , ['id' => $video_details->getScopeVideoAds->id] ) }}">{{ tr('view_ad') }}</a></li>

									                  		@endif

									                  	@endif

									                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="{{ route('admin.ads-details.ad-status-change',$video_details->video_tape_id) }}">{{  ($video_details->ad_status) ? tr('disable_ad') : tr('enable_ad') }}</a></li>

									                </ul>
	              								</li>
	            							</ul>
									    </td>
								    </tr>

								    <div id="{{ $video_details->video_tape_id }}" class="modal fade" role="dialog">
									  <div class="modal-dialog">
									  <form action="{{ route('admin.video_tapes.set-ppv', $video_details->video_tape_id) }}" method="POST">
										    <!-- Modal content-->
										   	<div class="modal-content">
										      <div class="modal-header">
										        <button type="button" class="close" data-dismiss="modal">&times;</button>
										        <h4 class="modal-title">{{ tr('pay_per_view') }}</h4>
										      </div>
										      <div class="modal-body">
										        <div class="row">

										        	<input type="hidden" name="ppv_created_by" id="ppv_created_by" value="{{ Auth::guard('admin')->user()->id }}">
										        	<div class="col-lg-3">
										        		<label>{{ tr('type_of_user') }}</label>
										        	</div>
									                <div class="col-lg-9">
									                  <div class="input-group">
									                        <input type="radio" name="type_of_user" value="{{ NORMAL_USER }}" {{ ($video_details->type_of_user == 0 || $video_details->type_of_user == '') ? 'checked' : (($video_details->type_of_user == NORMAL_USER) ? 'checked' : '') }}>&nbsp;<label>{{ tr('normal_user') }}</label>&nbsp;
									                        <input type="radio" name="type_of_user" value="{{ PAID_USER }}" {{ ($video_details->type_of_user == PAID_USER) ? 'checked' : '' }}>&nbsp;<label>{{ tr('paid_user') }}</label>&nbsp;
									                        <input type="radio" name="type_of_user" value="{{ BOTH_USERS }}" {{ ($video_details->type_of_user == BOTH_USERS) ? 'checked' : '' }}>&nbsp;<label>{{ tr('both_user') }}</label>
									                  </div>
									                  <!-- /input-group -->
									                </div>
									            </div>
									            <br>
									            <div class="row">
										        	<div class="col-lg-3">
										        		<label>{{ tr('type_of_subscription') }}</label>
										        	</div>
									                <div class="col-lg-9">
									                  <div class="input-group">
									                        <input type="radio" name="type_of_subscription" value="{{ ONE_TIME_PAYMENT }}"  {{ ($video_details->type_of_subscription == 0 || $video_details->type_of_subscription == '') ? 'checked' : (($video_details->type_of_subscription == ONE_TIME_PAYMENT) ? 'checked' : '') }}>&nbsp;<label>{{ tr('one_time_payment') }}</label>&nbsp;
									                        <input type="radio" name="type_of_subscription" value="{{ RECURRING_PAYMENT }}" {{ ($video_details->type_of_subscription == RECURRING_PAYMENT) ? 'checked' : '' }}>&nbsp;<label>{{ tr('recurring_payment') }}</label>
									                  </div>
									                  <!-- /input-group -->
									                </div>
									            </div>
									            <br>
									            <div class="row">
										        	<div class="col-lg-3">
										        		<label>{{ tr('amount') }}</label>
										        	</div>
									                <div class="col-lg-9">
									                       <input type="number" required value="{{ $video_details->ppv_amount }}" name="ppv_amount" class="form-control" id="amount" placeholder="{{ tr('amount') }}" step="any" maxlength="6">
									                  <!-- /input-group -->
									                </div>
									            </div>
										      </div>
										      <div class="modal-footer">
										      	<div class="pull-left">
										      		@if($video_details->ppv_amount > 0)
										       			<a class="btn btn-danger" href="{{ route('admin.video_tapes.remove-ppv', $video_details->video_tape_id) }}">{{ tr('remove_pay_per_view') }}</a>
										       		@endif
										       	</div>
										        <div class="pull-right">
											        <button type="button" class="btn btn-default" data-dismiss="modal">{{ tr('cancel') }}</button>
											        <button type="submit" class="btn btn-primary">{{ tr('submit') }}</button>
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
						<h3 class="no-result">{{ tr('no_video_found') }}</h3>
					@endif
	            </div>

           </div>

        </div>

    </div>

@endsection