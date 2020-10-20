@extends('layouts.admin')

@section('title', tr('view_and_assign_ad'))

@section('content-header', tr('view_and_assign_ad'))

@section('breadcrumb')
     

    <li><a href="{{route('admin.ads-details.index')}}"><i class="fa fa-bullhorn"></i>{{tr('video_ads')}}</a></li>

    <li class="active"><i class="fa fa-bullhorn"></i> {{ tr('view_and_assign_ad') }}</li>
@endsection

@section('content')

	<div class="row">

        <div class="col-xs-12">

            @include('notification.notify')

	        <div class="box box-primary">

	          	<div class="box-header label-primary">
	                <b style="font-size:18px;">{{ tr('view_and_assign_ad') }}</b>
	            </div>

	            <div class="box-body">

	            	@if(count($ads_details) > 0)

		              	<table id="example1" class="table table-bordered table-striped">

							<thead>
							    <tr>
							      	<th>{{ tr('id') }}</th>
							      	<th>{{ tr('name') }}</th>
							      	<th>{{ tr('url') }}</th>
							      	<th>{{ tr('ad_time') }} ({{ tr('in_sec') }})</th>
							      	<th>{{ tr('image') }}</th>
							      	<th>{{ tr('status') }}</th>
							      	<th>{{ tr('action') }}</th>
							    </tr>
							</thead>

							<tbody>

								@foreach($ads_details as $i => $ads_detail_details)
								   @if($ads_detail_details->type == 0)
								    <tr>
								      	<td>
								      		<a href="{{ route('admin.ads-details.view' , ['ads_detail_id' => $ads_detail_details->id] ) }}">{{ $i+1 }}
								      		</a>
								      	</td>

								      	<td>
								      		<a href="{{ route('admin.ads-details.view' , ['ads_detail_id' => $ads_detail_details->id] ) }}">{{ $ads_detail_details->name }}</a>
								      	</td>
								      	
								      	<td>
								      		<a href="{{$ads_detail_details->ad_url }}"> {{ $ads_detail_details->ad_url }}</a>
								      	</td>
								      	
								      	<td>{{ $ads_detail_details->ad_time }}</td>
								      	
								      	<td>
								      		<img src="{{ $ads_detail_details->file }}" style="width: 30px;height: 30px;" />
								      	</td>
								      	
								      	<td>								      		
								      		@if($ads_detail_details->status == DEFAULT_TRUE)
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
									                		
									                  	<li role="presentation">
									                  		<a role="menuitem" tabindex="-1" href="{{ route('admin.ads-details.view' , ['ads_detail_id' => $ads_detail_details->id] ) }}">{{ tr('view') }}</a>
									                  	</li>							                  	
                                                        @if(Setting::get('admin_delete_control') == YES )            
                                                            <li role="presentation">
                                                            	<a role="button" href="javascript:;" class="btn disabled" style="text-align: left">{{ tr('edit') }}</a>
                                                            </li>

                                                            <li role="presentation">
                                                            	<a role="button" href="javascript:;" class="btn disabled" style="text-align: left">{{ tr('delete') }}</a>
                                                            </li>

                                                        @else

                                                            <li role="presentation">
                                                            	<a role="menuitem" tabindex="-1" href="{{ route('admin.ads-details.edit' , ['ads_detail_id' => $ads_detail_details->id] ) }}">{{ tr('edit') }}</a>
                                                            </li>

                                                            <li role="presentation">
                                                            	<a role="menuitem" tabindex="-1" onclick="return confirm(&quot;{{ tr('admin_ads_detail_delete_confirmation', $ads_detail_details->name) }}&quot;)"  href="{{ route('admin.ads-details.delete' , ['ads_detail_id' => $ads_detail_details->id] ) }}">{{ tr('delete') }}</a>
                                                            </li>
                                                        @endif
	                                                    
									                  	@if($ads_detail_details->status == DEFAULT_TRUE)
									                  		<li role="presentation">
									                  			<a role="menuitem" tabindex="-1" href="{{ route('admin.ads-details.status' , ['ads_detail_id' => $ads_detail_details->id] ) }}" onclick="return confirm(&quot;{{ tr('admin_ads_detail_decline_confirmation', $ads_detail_details->name) }}&quot;)">{{ tr('decline') }}</a>
									                  		</li>
									                  	@else
									                  		<li role="presentation">
									                  			<a role="menuitem" tabindex="-1" href="{{ route('admin.ads-details.status' , ['ads_detail_id' => $ads_detail_details->id ] ) }}" onclick="return confirm(&quot;{{ tr('admin_ads_detail_approve_confirmation', $ads_detail_details->name) }}&quot;)" >{{ tr('approve') }}</a>
									                  		</li>
									                  	@endif

									                  	@if($ads_detail_details->status == DEFAULT_TRUE)

									                  		<li role="presentation">
									                  			<a role="menuitem" tabindex="-1" target="_blank" href="#" data-toggle="modal" data-target="#myModal_{{ $i }}">{{ tr('assign_ad') }}</a>
									                  		</li>

									                  	@endif
									              															
									                  	<li class="divider" role="presentation"></li>

									                </ul>

	              								</li>

	            							</ul>

									    </td>

								    </tr>

							    	<!-- Modal -->
							      	<div id="myModal_{{ $i }}" class="modal fade" role="dialog">
							        
							        	<div class="modal-dialog">

							        		<form method="get" action="{{ route('admin.videos.assign_ad') }}">
							        	
									        	<div class="modal-content">
										            
										            <div class="modal-header">
										              	<button type="button" class="close" data-dismiss="modal">&times;</button>
										              	
										              	<h4 class="modal-title">{{ tr('assign_ad') }}</h4>
										            </div>
										            
										            <div class="modal-body">

										            	<div class="row">

											            	<div class="col-lg-12">

											            		<input type="hidden" name="id" value="{{ $ads_detail_details->id }}">

											            		<input type="radio" name="type" value="1" id="video_type" required> {{ tr('single') }}

											            		<input type="radio" name="type" value="2" id="video_type" required> {{ tr('multiple') }}

											            	</div>

										            	</div>

										            </div>

										            <div class="modal-footer">
										              	<button type="button" class="btn btn-default" data-dismiss="modal">{{ tr('close') }}</button>
										              	
										              	<button type="submit" class="btn btn-success" id="submit-btn">{{ tr('assign') }}</button>
										            </div>

										        </div>

								     		</form>
							          
							        	</div>

							      	</div>
									@endif
								@endforeach

							</tbody>

						</table>

					@else
						<h3 class="no-result">{{ tr('no_ads_found') }}</h3>
					@endif
					
	            </div>

	        </div>

        </div>

    </div>

@endsection


@section('scripts')


<script type="text/javascript">
	
function redirectView() {

	var value = $('input[name=type]:checked').val();;

	alert(value);

}

</script>

@endsection