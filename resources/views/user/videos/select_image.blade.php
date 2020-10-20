@foreach($model->video_path as $key => $path)


<div class="col-lg-4 col-md-4 col-sm-12 col-sx-12">
    <a class="category-item text-center">
        <div style="background-image: url({{$path}})" class="category-img bg-img" id="image_div_id_{{$key}}"
         onclick="$('#img_'+{{$key}}).click();"></div>

        <div style="position: relative;" id="preview_image_div_{{$key}}">
        	<img id="preview_{{$key}}" style="width: 200px;height: 150px;display: none;" onclick="$('#img_'+{{$key}}).click();"/>
    		<div class="st_photo" id="remove_circle_{{$key}}">
				<button class="st_profile_btn" onclick="removePicture({{$key}});return false;">
					<i class="fa fa-times-circle" ></i>
				</button>
			</div>
        </div>
    </a>
    <center><button type="button" id="btn_{{$key}}" class="btn {{$key ==0 ? 'btn-success' : 'btn-danger'}}" onclick="saveAsDefault({{$model->data->id}}, $('#other_image_id_'+{{$key}}).val(), {{$key}}, {{count($model->video_path)}}, '{{$path}}');">{{ $key == 0 ? tr('marked_default') : tr('make_default')}}</button></center>
</div>


@if($key == 0) 


<input type="file" style="display:none;" name="default_image" id="img_{{$key}}" onchange="loadFile(this, 'preview_'+{{$key}}, {{$key}})" accept="image/png, image/jpeg">

<input type="hidden" name="default_image_id" id="other_image_id_{{$key}}" value="{{$model->data->id}}">

@else

<?php $pos = $key-1;  ?>

<input type="file"  style="display:none;" name="other_image_{{$key}}" id="img_{{$key}}" onchange="loadFile(this, 'preview_'+{{$key}}, {{$key}})" accept="image/png, image/jpeg">

<input type="hidden" name="other_image_id_{{$key}}" id="other_image_id_{{$key}}" value="{{$tape_images[$pos]->id}}">


@endif

@endforeach

<style type="text/css">
	
.st_profile_btn {
	background: #cccccc none repeat scroll 0 0;
	border: medium none;
	border-radius: 0;
	box-sizing: content-box;
	color: #000;
	cursor: pointer;
	display: inline-block;
	font-size: 13px;
	font-weight: normal;
	height: 29px;
	line-height: 29px;
	vertical-align: bottom;
}

.st_photo {
	position: absolute;
	bottom: 0;
	right: 13.5%;
	display: none;
}


</style>