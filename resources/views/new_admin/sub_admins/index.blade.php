											                 
@extends('layouts.admin')

@section('title', tr('sub_admins'))

@section('content-header') 

	{{ tr('sub_admins') }} 

@endsection

@section('breadcrumb')
   	 
    <li class="active"><i class="fa fa-user-plus"></i> {{ tr('sub_admins') }}</li>
@endsection

@section('content')

	<div class="row">

        <div class="col-xs-12">
          	
          	@include('notification.notify')

          	<div class="box box-primary">

	            <div class="box-header label-primary">
	                <b style="font-size:18px;">{{tr('sub_admins')}}</b>
	                <a href="{{ route('admin.sub_admins.create') }}" class="btn btn-default pull-right">
	                	{{ tr('sub_admin_create') }}
	                </a>
	            </div>

	            <div class="box-body">

	            	<div class="table-responsive" style="padding: 35px 0px"> 
	            		
		            		<div class="table table-responsive">
				              	
				              	<table id="example1" class="table table-bordered table-striped ">

									<thead>
									    <tr>
											<th>{{ tr('id') }}</th>
											<th>{{ tr('username') }}</th>
											<th>{{ tr('email') }}</th>
											<th>{{ tr('mobile') }}</th>
											<th>{{ tr('status') }}</th>
											<th>{{ tr('action') }}</th>
									    </tr>
									
									</thead>

									<tbody>
										
										@foreach($sub_admins as $i => $sub_admin_details)

										    <tr>
										      	<td>{{  $i+1  }}</td>
										      	<td>
										      		<a href="{{ route('admin.sub_admins.view' , ['sub_admin_id' => $sub_admin_details->id]) }}">
										      			{{ $sub_admin_details->name }}
										      		</a>
										      	</td>

										      	<td>{{ $sub_admin_details->email }}</td>
										      											      
										      	<td>
										      		{{ $sub_admin_details->mobile }}
										      	</td>
										      	
										      	<td>
											      	@if($sub_admin_details->status)

											      		<span class="label label-success">{{ tr('approved') }}</span>

											      	@else

											      		<span class="label label-warning">{{ tr('pending') }}</span>

											      	@endif
										     	</td>
										 
										      	<td>
			            							
			            							<ul class="admin-action btn btn-default">
			            								
			            								<li class="@if($i < 2) dropdown @else dropup @endif">
											                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
											                  {{ tr('action') }} <span class="caret"></span>
											                </a>
											                <ul class="dropdown-menu dropdown-menu-right">

											                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="{{ route('admin.sub_admins.view' , ['sub_admin_id' => $sub_admin_details->id]) }}">{{ tr('view') }}</a></li>

											                  	@if(Setting::get('admin_delete_control') == YES)

												                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="javascript:;">{{ tr('edit') }}</a></li>

												                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="javascript:;">{{ tr('delete') }}</a></li>

											                  	@else
											                  	
												                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="{{ route('admin.sub_admins.edit' , ['sub_admin_id'=>$sub_admin_details->id] ) }}">{{ tr('edit') }}</a></li>

												                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="{{ route('admin.sub_admins.delete' , ['sub_admin_id' => $sub_admin_details->id]) }}" onclick="return confirm(&quot;{{ tr('admin_sub_admin_delete_confirmation' , $sub_admin_details->name) }}&quot;);" >{{ tr('delete') }}</a></li>
											                  	
											                  	@endif
											                  
											                  	<li role="presentation" class="divider"></li>

											                  	@if($sub_admin_details->status == YES )
											                  		<li role="presentation"><a role="menuitem" onclick="return confirm(&quot;{{ $sub_admin_details->name }} - {{ tr('admin_sub_admin_decline_confirmation') }}&quot;);" tabindex="-1" href="{{ route('admin.sub_admins.status' , ['sub_admin_id' => $sub_admin_details->id]) }}"> {{ tr('decline') }}</a></li>
											                  	 @else 
											                  	 	<li role="presentation"><a role="menuitem" onclick="return confirm(&quot;{{ $sub_admin_details->name }} - {{ tr('admin_sub_admin_approve_confirmation') }}&quot;);" tabindex="-1" href="{{ route('admin.sub_admins.status' , ['sub_admin_id'=>$sub_admin_details->id]) }}"> 
											                  		{{ tr('approve') }} </a></li>
											                  	@endif

											                </ul>

			              								</li>

			            							</ul>
										      	
										      	</td>

										    </tr>

										@endforeach
									
									</tbody>
								
								</table>

							</div>

					</div>

	            </div>

          	</div>

        </div>
    
    </div>

@endsection
