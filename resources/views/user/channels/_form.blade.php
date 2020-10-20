
<div class="y-content">

    <div class="row content-row">

        @include('layouts.user.nav')

        <div class="page-inner col-sm-9 col-md-10">

            @include('notification.notify')

            <form action="{{route('user.save_channel')}}" method="post" enctype="multipart/form-data">

			<div class="branded-page-v2-top-row">

				<div class="branded-page-v2-header channel-header yt-card">

					<div id="gh-banner" style="margin: 0px 10px !important">

						<div id="c4-header-bg-container" class="c4-visible-on-hover-container  has-custom-banner">
							<div class="hd-banner">
								<div class="hd-banner-image">
									<img src="{{asset('images/default-cover-image.jpg')}}" id="cover_preview" class="st_cover_photo_img" id="cover_preview" style= "{{($model->id) ? 'display: none' : ''}}"; />
								</div>
							</div>
							<div id="header-links">
								<button class="st_button" onclick="$('#cover').click();return false">
									@if($model->id)
										<i class="fa fa-pencil"></i>&nbsp;{{tr('edit_cover_photo')}}
									@else
										<i class="fa fa-plus-circle"></i>&nbsp;{{tr('add_cover_photo')}}
									@endif
								</button>
							</div>
							<div class="st_photo_div">
								<img class="channel-header-profile-image" src="{{$model->picture ?  $model->picture : asset('images/default.png')}}" title="{{tr('channel_profile_photo')}}" alt="{{tr('channel_profile_photo')}}" id="picture_preview">
								<div class="st_photo">
									<button class="st_profile_btn" onclick="$('#picture').click();return false">
										@if($model->id)
											<i class="fa fa-pencil"></i>
										@else
											<i class="fa fa-plus-circle"></i> 
										@endif
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			
			</div>

			<div class="slide-area recom-area abt-sec des-crt">

				<div class="abt-sec-head description-create">

					<input type="file" style="display: none;" name="picture" id="picture" onchange="loadFile(this, 'picture_preview');" @if(!$model->id) required @endif  accept="image/png, image/jpeg">
            		<input type="file" style="display: none;" name="cover" id="cover" onchange="loadFile(this, 'cover_preview');" @if(!$model->id) required @endif accept="image/png, image/jpeg">
            		
            		<input type="hidden" name="id" id="id" value="{{$model->id}}">

            		<input type="hidden" name="user_id" value="{{Auth::user()->id}}">
					
					<div class="des-box">

						<div>
							<p><small><b>{{tr('note')}} :</b>&nbsp;<span class="dark-grey">{{tr('profile_picture')}} - {{tr('image_square')}}, {{tr('cover_picture')}} - {{tr('rectangle_image')}}</span></small></p>
						</div>

						<div>
							<span id="span_error"></span>
						</div>

						<h5>{{tr('title')}}</h5>

						<input type="text" name="name" id="title" class="form-control" value="{{old('name') ?: $model->name}}" required minlength="6" maxlength="64" title="Enter minimum 6 character and maximum 64 character..!" />

						<!-- <h5>{{tr('youtube_channel_id')}}</h5>

						<input type="text" name="youtube_channel_id" id="youtube_channel_id" class="form-control" value="{{old('youtube_channel_id') ?: $model->youtube_channel_id}}"/> -->

						<h5>{{tr('description')}}</h5>

						<p><small><b>{{tr('note')}} :</b>&nbsp;<span class="dark-grey">{{tr('description_note')}}</span></small></p>

						<textarea class="form-control description" id="ckeditor" name="description" required>{{old('description') ?: $model->description}}</textarea>
						<br>
						<div>
							<a href="" class="btn btn-danger pull-left">{{tr('reset')}}</a>
							<button id="done-create" name="submit" class="btn btn-info pull-right" @if(!$model->id) onclick="return checkImage();" @endif>{{tr('submit')}}</button>
							<div class="clearfix"></div>
						</div>

					</div>

				</div>
			</div>

			</form>

			<div class="sidebar-back"></div> 
        </div>
    </div>
</div>

@section('scripts')

<!-- Add Js files and inline js here -->

<script type="text/javascript">
function loadFile(event, id){
    // alert(event.files[0]);
    if ('cover_preview' == id) {
    	$("#cover_preview").show();
    }
    var reader = new FileReader();
    reader.onload = function(){
      var output = document.getElementById(id);
      // alert(output);
      output.src = reader.result;
      // $("#c4-header-bg-container .hd-banner-image").css("background-image", "url("+reader.result+")");
    };
    reader.readAsDataURL(event.files[0]);
}

function checkImage() {

	$("#span_error").html("");

	var picture_val = $("#picture").val();

	var cover_val = $("#cover").val();

	if(picture_val == '') {

		$("#span_error").html('<p class="text-danger">Channel picture should not be an empty</p>');

		return false;
	}

	if(cover_val == '') {

		$("#span_error").html('<p class="text-danger">Channel Cover should not be an empty</p>');

		return false;
	}
}
</script>

<script src="https://cdn.ckeditor.com/4.5.5/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace( 'ckeditor' );
</script>
@endsection