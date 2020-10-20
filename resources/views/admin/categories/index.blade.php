@extends('layouts.admin')

@section('title', tr('categories'))

@section('content-header', tr('categories'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li class="active"><i class="fa fa-key"></i> {{tr('categories')}}</li>
@endsection

@section('content')

	@include('notification.notify')



	<div class="row">
        <div class="col-xs-12">        	
          <div class="box box-primary">

          	<div class="box-header label-primary">
                <b>{{tr('categories')}}</b>
                <a href="{{route('admin.categories.create')}}" class="btn btn-default pull-right">{{tr('create_category')}}</a>
            </div>
            
            <div class="box-body">

              	<table id="example1" class="table table-bordered table-striped">

					<thead>
					    <tr>
					      	<th>{{tr('id')}}</th>
					      	<th>{{tr('name')}}</th>
					      	<th>{{tr('no_of_uploads')}}</th>
					      	<th>{{tr('picture')}}</th>
					      	<th>{{tr('status')}}</th>
					      	<th>{{tr('action')}}</th>
					    </tr>
					</thead>

					<tbody>
					
						@foreach($datas as $i => $data)

						    <tr>
						      	<td>{{$i+1}}</td>
						      	<td><a target="_blank" href="{{route('admin.categories.view', ['category_id'=>$data->id])}}">{{$data->name}}</a></td>
						      	<td><a target="_blank" href="{{route('admin.categories.videos', ['category_id'=>$data->id])}}">{{$data->get_videos_count}}</a></td>

						      	<td><img src="{{$data->image}}" style="width: 25px;height: 35px"></td>
						      
						      	<td class="text-center">

					      			@if($data->status)
						      			<span class="label label-success">{{tr('approved')}}</span>
						      		@else
						      			<span class="label label-warning">{{tr('pending')}}</span>
						      		@endif
						      	</td>

						      	

						      	<td class="text-center">

						      		
					      				<a href="{{route('admin.categories.edit' ,['id'=>$data->id])}}" class="btn  btn-xs btn-primary" title="Edit">
				              				<i class="fa fa-edit"></i>
				              			</a>


						      			<a onclick="return confirm('Are you sure want to delete category ? Once you delete the category based on that all videos will delete..!')" href="{{route('admin.categories.delete' ,['id'=>$data->id])}}" class="btn  btn-xs btn-danger" title="Delete">

				              				<i class="fa fa-trash"></i>

				              			</a>

				              			<?php

				              				$category_decline_notes = tr('category_decline_notes');

				              				$category_approve_notes = tr('category_approve_notes');

				              			?>

				              			@if($data->status)

				              				<a href="{{route('admin.categories.status' ,['id'=>$data->id])}}" class="btn  btn-xs btn-warning" title="Decline this Category" onclick='return confirm("{{$category_decline_notes}}")'>

					              				<i class="fa fa-times"></i>

					              			</a>


				              			@else

					              			<a href="{{route('admin.categories.status' ,['id'=>$data->id])}}" class="btn  btn-xs btn-success" title="Approve this Category" onclick='return confirm("{{$category_approve_notes}}")'>

					              				<i class="fa fa-check"></i>

					              			</a>
				              			@endif

			              			
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