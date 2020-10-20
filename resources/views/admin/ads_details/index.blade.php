@extends('layouts.admin')

@section('title', tr('view_and_assign_ad'))

@section('content-header', tr('view_and_assign_ad'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li class="active"><i class="fa fa-bullhorn"></i> {{tr('view_and_assign_ad')}}</li>
@endsection

@section('content')

    @include('notification.notify')

	<div class="row">
        <div class="col-xs-12">
          <div class="box box-primary">

          	<div class="box-header label-primary">
                <b style="font-size:18px;">{{tr('view_and_assign_ad')}}</b>
            </div>

            <div class="box-body">

            	@if(count($model) > 0)

	              	<table id="example1" class="table table-bordered table-striped">

						<thead>
						    <tr>
						      <th>{{tr('id')}}</th>
						      <th>{{tr('name')}}</th>
						      <th>{{tr('url')}}</th>
						      <th>{{tr('ad_time')}} ({{tr('in_sec')}})</th>
						      <th>{{tr('image')}}</th>
						      <th>{{tr('status')}}</th>
						      <th>{{tr('action')}}</th>
						    </tr>
						</thead>
						<tbody>


							@foreach($model as $i => $data)
							    <tr>
							      	<td><a href="{{route('admin.ads-details.view' , array('id' => $data->id))}}">{{$i+1}}</a></td>
							      	<td><a href="{{route('admin.ads-details.view' , array('id' => $data->id))}}">{{$data->name}}</a></td>
							      	<td>{{$data->ad_url}}</td>
							      	<td>{{$data->ad_time}}</td>
							      	<td>
							      		<img src="{{$data->file}}" style="width: 30px;height: 30px;" />
							      	</td>
							      	<td>
							      		
							      		@if($data->status)
							      			<span class="label label-success">{{tr('approved')}}</span>
							       		@else
							       			<span class="label label-danger">{{tr('pending')}}</span>
							       		@endif
							      	</td>
								    <td>
            							<ul class="admin-action btn btn-default">
            								<li class="dropup">
								                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
								                  {{tr('action')}} <span class="caret"></span>
								                </a>
								                <ul class="dropdown-menu">
								                	
								                  	<li role="presentation">
                                                        @if(Setting::get('admin_delete_control'))
                                                            <a role="button" href="javascript:;" class="btn disabled" style="text-align: left">{{tr('edit')}}</a>
                                                        @else
                                                            <a role="menuitem" tabindex="-1" href="{{route('admin.ads-details.edit' , array('id' => $data->id))}}">{{tr('edit')}}</a>
                                                        @endif
                                                    </li>
                                                    
								                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.ads-details.view' , array('id' => $data->id))}}">{{tr('view')}}</a></li>


								                  	<?php $confirm_message = tr('are_you_sure_ad_status'); ?>

								                  	@if($data->status)
								                  		<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.ads-details.status' , array('id' => $data->id , 'status' =>0))}}" onclick="return confirm('{{$confirm_message}}')">{{tr('decline')}}</a></li>
								                  	@else
								                  		<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.ads-details.status' , array('id' => $data->id , 'status' => 1))}}" onclick="return confirm('{{$confirm_message}}')">{{tr('approve')}}</a></li>
								                  	@endif

								                  	@if($data->status)

								                  	<li role="presentation"><a role="menuitem" tabindex="-1" target="_blank" href="#" data-toggle="modal" data-target="#myModal_{{$i}}">{{tr('assign_ad')}}</a></li>
								                  	@endif
								              
														
								                  	<li class="divider" role="presentation"></li>

								                  	<li role="presentation">
								                  		@if(Setting::get('admin_delete_control'))

									                  	 	<a role="button" href="javascript:;" class="btn disabled" style="text-align: left">{{tr('delete')}}</a>

									                  	@else
								                  			<a role="menuitem" tabindex="-1" onclick="return confirm('Are you sure?')" href="{{route('admin.ads-details.delete' , array('id' => $data->id))}}">{{tr('delete')}}</a>
								                  		@endif
								                  	</li>
								                </ul>
              								</li>
            							</ul>
								    </td>
							    </tr>

							    <!-- Modal -->
							      <div id="myModal_{{$i}}" class="modal fade" role="dialog">
							        <div class="modal-dialog">

							        <form method="get" action="{{route('admin.video_tapes.assign_ad')}}">
							        	<div class="modal-content">
								            <div class="modal-header">
								              <button type="button" class="close" data-dismiss="modal">&times;</button>
								              <h4 class="modal-title">{{tr('assign_ad')}}</h4>
								            </div>
								            <div class="modal-body">

								            	<div class="row">

									            	<div class="col-lg-12">

									            		<input type="hidden" name="id" value="{{$data->id}}">

									            		<input type="radio" name="type" value="1" id="video_type"> {{tr('single')}}

									            		<input type="radio" name="type" value="2" id="video_type"> {{tr('multiple')}}

									            	</div>

								            	</div>

								            </div>

								            <div class="modal-footer">
								              <button type="button" class="btn btn-default" data-dismiss="modal">{{tr('close')}}</button>
								              <button type="submit" class="btn btn-success" id="submit-btn">{{tr('assign')}}</button>
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
					<h3 class="no-result">{{tr('no_ads_found')}}</h3>
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