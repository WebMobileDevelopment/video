@extends('layouts.admin')

@section('title', tr('tags'))

@section('content-header', tr('tags'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li class="active"><i class="fa fa-tag"></i> {{tr('tags')}}</li>
@endsection

@section('content')

	@include('notification.notify')

	<div class="row">

        <div class="col-xs-12 text-right">        	

        	<button class="btn btn-success" type="button" onclick="$('#display_form').toggle()">{{tr('create_tag')}}</button>

        </div>

        <div class="col-xs-12" style="{{$model->id ? '' : 'display: none'}}" id="display_form">    

        	<div class="clearfix"></div>

        	<form action="{{route('admin.save.tag')}}" method="post" enctype="multipart/form-data">

        		<br>

        		<input type="hidden" name="id" value="{{$model->id}}">

        		<div class="row">

	        		<div class="col-xs-4">

	        		<input type="text" name="name" value="{{$model->name}}" required class="form-control" placeholder="{{tr('name')}}" pattern="[a-zA-Z]+" title="Enter only alphabets" maxlength="15"> 

	        		</div>

	        		<div class="col-xs-4">

	        		<input type="submit" name="button" value="{{tr('submit')}}" class="btn btn-success">

	        		</div>

        		</div>
        	

        	</form>

        </div>

    </div>

    <br>

	<div class="row">
        <div class="col-xs-12">        	
          <div class="box box-primary">

          	<div class="box-header label-primary">
                <b>{{tr('categories')}}</b>
            </div>
            
            <div class="box-body">

              	<table id="example1" class="table table-bordered table-striped">

					<thead>
					    <tr>
					      	<th>{{tr('id')}}</th>
					      	<th>{{tr('name')}}</th>
					      	<!-- <th>{{tr('count')}}</th> -->
					      	<th>{{tr('status')}}</th>
					      	<th>{{tr('action')}}</th>
					    </tr>
					</thead>

					<tbody>
					
						@foreach($datas as $i => $data)

						    <tr>
						      	<td>{{$i+1}}</td>
						      	<td class="text-capitalize">{{$data->name}}</td>
						      	<?php /*<td>{{$data->search_count}}</td>*/?>

						      
						      	<td class="text-center">

					      			@if($data->status)
						      			<span class="label label-success">{{tr('approved')}}</span>
						      		@else
						      			<span class="label label-warning">{{tr('pending')}}</span>
						      		@endif
						      	</td>

						      	

						      	<td class="text-center">
						      		
					      				<a href="{{route('admin.tags.index' ,['id'=>$data->id])}}" class="btn  btn-xs btn-primary" title="Edit">
				              				<i class="fa fa-edit"></i>
				              			</a>


						      			<a href="{{route('admin.tags.delete' ,['id'=>$data->id])}}" class="btn  btn-xs btn-danger" title="Delete" onclick="return confirm('Are you sure want to delete ?')">

				              				<i class="fa fa-trash"></i>

				              			</a>	

				              			<?php 

				              				$tag_approve_notes = tr('tag_approve_notes');

				              				$tag_decline_notes = tr('tag_decline_notes');
				              				

				              			?>

				              			@if($data->status)

				              				<a href="{{route('admin.tags.status' ,['id'=>$data->id])}}" class="btn  btn-xs btn-warning" title="Decline this Category"  onclick='return confirm("{{$tag_decline_notes}}")'>

					              				<i class="fa fa-times"></i>

					              			</a>


				              			@else

					              			<a href="{{route('admin.tags.status' ,['id'=>$data->id])}}" class="btn  btn-xs btn-success" title="Approve this Category" onclick='return confirm("{{$tag_approve_notes}}")'>

					              				<i class="fa fa-check"></i>

					              			</a>
				              			@endif

			              				<a href="{{route('admin.tags.videos' ,['tag_id'=>$data->id])}}" class="btn  btn-xs btn-success" title="Tagged Videos">

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