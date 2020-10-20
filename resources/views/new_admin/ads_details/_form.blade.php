<div class="row">

    <div class="col-md-12">

        @include('notification.notify')

        <div class="box box-primary">

            <div class="box-header label-primary">
                <b style="font-size:18px;">@yield('title')</b>
                <!-- <a href="{{route('admin.ads-details.index')}}" class="btn btn-default pull-right">{{tr('video_ads')}}</a> -->
            </div>

            <form  action="{{ Setting::get('admin_delete_control') == YES ? '#' : route('admin.ads-details.save')}}" method="POST" enctype="multipart/form-data" role="form">

                <input type="hidden" name="ads_detail_id" id="id" value="{{$ads_detail_details->id}}">

                <div class="box-body">

                    <div class="col-md-12 form-group" style="margin-top: 10px;">

                    	<div class="row">

                            <div class="col-md-9">

                                <div class="row">

                                    <div class="col-md-6">

                                        <label>{{tr('name')}} *</label>

                                        <input type="text" name="name" id="name" class="form-control" value="{{old('name') ?: $ads_detail_details->name}}" required>

                                    </div>

                                    <div class="col-md-6">

                                        <label>{{tr('ad_time')}} ({{tr('in_sec')}}) *</label>

                                        <input type="text" name="ad_time" id="ad_time" class="form-control" value="{{old('ad_time') ?: $ads_detail_details->ad_time}}" required>

                                    </div>

                                    <div class="clearfix"></div>

                                </div>

                                <br>

                                <div class="row">

                                    <div class="col-md-12">

                                        <label>{{tr('url')}} *</label>

                                        <input type="text" name="ad_url" id="ad_url" class="form-control" value="{{old('ad_url') ?: $ads_detail_details->ad_url}}" required>

                                    </div>

                                </div>

                            </div>

                            <div class="col-md-3">

                                <label>{{tr('image')}} *</label>

                                <input type="file" name="file" id="file" accept="image/png,image/jpeg" onchange="loadFile(this, 'ad_preview')" @if(!$ads_detail_details->id) required @endif>

                                <br>

                                <img src="{{$ads_detail_details->file ? $ads_detail_details->file : asset('images/default-ad.jpg')}}" style="width:100px;height: 100px;" id="ad_preview"/>

                            </div>

                            <div class="clearfix"></div>

                            <br>

                    	</div>
                        
                    </div>
                
                </div>

                <div class="box-footer">
                    <a href="" class="btn btn-danger">{{tr('cancel')}}</a>
                    <button type="submit" class="btn btn-success pull-right">{{tr('submit')}}</button>
                </div>

            </form>
        
        </div>

    </div>

</div>

@section('scripts')


<script type="text/javascript">

function loadFile(event, id){
    var reader = new FileReader();
    reader.onload = function(){
      var output = document.getElementById(id);
      output.src = reader.result;
       //$("#imagePreview").css("background-image", "url("+this.result+")");
    };
    reader.readAsDataURL(event.files[0]);
}

/*function getCheckBoxValue(ad_type) {

    $('#video_time').val('');

    $("#video_time_div").hide();
 
    if(ad_type == "{{BETWEEN_AD}}") {

        $("#video_time_div").show();

    }

}*/
</script>
@endsection
   