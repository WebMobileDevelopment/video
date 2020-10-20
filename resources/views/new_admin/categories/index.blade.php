@extends('layouts.admin')

@section('title', tr('categories'))

@section('content-header', tr('categories'))

@section('breadcrumb')
     
    <li class="active"><i class="fa fa-key"></i> {{ tr('categories') }}</li>
@endsection

@section('content')

	<div class="row">

        <div class="col-xs-12"> 

            @include('notification.notify')

            <div class="box box-primary">

	          	<div class="box-header label-primary">
	                <b>{{ tr('categories') }}</b>
	                <a href="{{ route('admin.categories.create') }}" class="btn btn-default pull-right">{{ tr('create_category') }}</a>
	            </div>
	            
	            <div class="box-body">

	              	<table id="example1" class="table table-bordered table-striped">

						<thead>
						    <tr>
						      	<th>{{ tr('id') }}</th>
						      	<th>{{ tr('name') }}</th>
						      	<th>{{ tr('no_of_uploads') }}</th>
						      	<th>{{ tr('picture') }}</th>
						      	<th>{{ tr('status') }}</th>
						      	<th>{{ tr('action') }}</th>
						    </tr>
						</thead>

						<tbody>
						
							@foreach($categories as $i => $category_details)

							    <tr>
							      	<td>{{ $i+1 }}</td>

							      	<td>
							      		<a target="_blank" href="{{ route('admin.categories.view', ['category_id' => $category_details->id]) }}">{{ $category_details->name }}</a>
							      	</td>

							      	<td>
							      		<a target="_blank" href="{{ route('admin.categories.videos', ['category_id' => $category_details->id]) }}">{{ $category_details->get_videos_count }}</a>
							      	</td>

							      	<td><img src="{{ $category_details->image }}" style="width: 25px;height: 35px">
							      	</td>
							      
							      	<td class="text-center">
						      			@if($category_details->status == DEFAULT_TRUE)
							      			<span class="label label-success">{{ tr('approved') }}</span>
							      		@else
							      			<span class="label label-warning">{{ tr('pending') }}</span>
							      		@endif
							      	</td>							      	

							      	<td >
							      		
					      				<a href="{{ route('admin.categories.view', ['category_id' => $category_details->id]) }}" class="btn btn-sm btn-info" title="{{tr('view')}}">
				              				<i class="fa fa-eye"></i>
				              			</a>
				              			
				              			@if(Setting::get('admin_delete_control') == YES )  

					              			<a href="javascript:;" class="btn btn-sm btn-primary" title="{{tr('edit')}}" >
					              				<i class="fa fa-edit"></i>
					              			</a>

							      			<a href="javascript:;" class="btn btn-sm btn-danger" title="{{tr('delete')}}">
					              				<i class="fa fa-trash"></i>
					              			</a>

				              			@else 

					              			<a href="{{ route('admin.categories.edit' ,['category_id' => $category_details->id]) }}" class="btn btn-sm btn-primary" title="{{tr('edit')}}">
					              				<i class="fa fa-edit"></i>
					              			</a>

							      			<a href="{{ route('admin.categories.delete' ,['category_id' => $category_details->id]) }}" class="btn btn-sm btn-danger" title="{{tr('delete')}}" onclick="return confirm(&quot; {{tr('admin_category_delete_confirmation',$category_details->name) }} &quot;)">
					              				<i class="fa fa-trash"></i>
					              			</a>

				              			@endif

				              			@if($category_details->status == YES)

				              				<a href="{{ route('admin.categories.status' ,['category_id' => $category_details->id]) }}" class="btn btn-sm btn-warning" title="{{tr('decline')}}" onclick="return confirm(&quot;{{ tr('admin_category_decline_confirmation',$category_details->name ) }}&quot;)">
					              				<i class="fa fa-times"></i>
					              			</a>

				              			@else
					              			<a href="{{ route('admin.categories.status' ,['category_id' => $category_details->id] ) }}" class="btn btn-sm btn-success" title="{{tr('approve')}}" onclick="return confirm(&quot;{{ tr('category_approve_notes') }}&quot;)" >
					              			<i class="fa fa-check"></i>

					              			</a>
				              			@endif

				              			<a href="{{route('admin.categories.videos', ['category_id'=> $category_details->id] )}}" title="{{ tr('videos') }}" class="btn btn-sm btn-success" >
											<i class="fa fa-video-camera"></i>
				              			</a>
			              			
							      	</td>
							      	
							    </tr>

							@endforeach

						</tbody>
					
					</table>
				
	            </div>

          	</div>

        </div>

    </div>

@endsection

@section('scripts')

<!-- Add Js files and inline js here -->

<script type="text/javascript">
function loadFile(event, id){
    // alert(event.files[0]);
    var reader = new FileReader();
    reader.onload = function(){
      var output = document.getElementById(id);
      // alert(output);
      output.src = reader.result;
      //$("#c4-header-bg-container .hd-banner-image").css("background-image", "url("+this.result+")");
    };
    reader.readAsDataURL(event.files[0]);
}
</script>

@endsection