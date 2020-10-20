<?php use App\AssignVideoAd; ?>

@include('notification.notify')

	<div class="row">

    <div class="col-md-12">

        <div class="box box-primary">

            <div class="box-header label-primary">
                <b style="font-size:18px;">@yield('title')</b>
                <a href="{{route('admin.video_ads.list')}}" class="btn btn-default pull-right">{{tr('assigned_ads')}}</a>
            </div>

            <form  action="{{route('admin.video-ads.save')}}" method="POST" enctype="multipart/form-data" role="form">

                <input type="hidden" name="video_tape_id" id="video_tape_id" value="{{$vModel->id}}">

                <input type="hidden" name="video_ad_id" id="id" value="{{$model->id}}">

                <div class="box-body">

                	<div class="col-md-6">
	                    @if($vModel->video)
	                        <?php $url = $vModel->video; ?>
	                        <div id="main-video-player"></div>
	                    @else
	                        <div class="image">
	                            <img src="{{asset('error.jpg')}}" alt="{{Setting::get('site_name')}}">
	                        </div>
	                    @endif
	            	</div>

                    <div class="col-md-6">
                        <ul class="list-group list-group-unbordered">
                            <li class="list-group-item">
                              <b><i class="fa fa-suitcase margin-r-5"></i>{{tr('title')}}</b> <a class="pull-right" href="{{route('admin.video_tapes.view', $vModel->id)}}" target="_blank">{{$vModel->title}}</a>
                            </li>
                            <li class="list-group-item">
                              <b><i class="fa fa-clock-o margin-r-5"></i>{{tr('duration')}}</b> <div class="pull-right">{{$vModel->duration}}</div>
                            </li>
                            <li class="list-group-item">
                              <b><i class="fa fa-clock-o margin-r-5"></i>{{tr('duration_in_seconds')}}</b> <div class="pull-right">{{$duration_in_seconds = convertDurationIntoSeconds($vModel->duration)}}</div>
                            </li>
                        </ul>
                    </div>

	            	<div class="clearfix"></div>

                    <hr>

                    <div class="col-md-12 form-group">

                    	<div class="row">

                    		<div class="col-md-2">

                    			<label>{{tr('ad_type')}}</label>

                                <input type="hidden" name="pre_ad_type_id" id="pre_ad_id" value="{{$preAd->id}}">

                                <br>

                    			<input type="checkbox" name="pre_ad_type" id="pre_ad_type" value="{{PRE_AD}}"
                                @if($preAd->ad_type == PRE_AD) checked @endif onchange="getCheckBoxValue(this.id, this.value, '')"> {{tr('pre_ad')}}

                    		</div>


                    		<div class="col-md-3">

                    			<label>{{tr('ad_time')}} ({{tr('in_sec')}})</label>

                    			<input type="text" name="pre_ad_time" id="pre_ad_time" class="form-control" value="{{$preAd->ad_time}}" maxlength="3" minlength="1" title="Enter Minimum 1 Character to Maximum 3 Character">

                    		</div>


                    		<div class="col-md-6">

                    			<label>{{tr('ad')}}</label>

                    			
                                <select id="pre_parent_ad_id" name="pre_parent_ad_id" class="form-control">
                                    <option value="">{{tr('select_ad')}}</option>
                                    @foreach($ads as $ad)
                                        <option value="{{$ad->id}}" @if($ad->id == $preAd->ad_id) selected @endif>{{$ad->name}}</option>
                                    @endforeach
                                </select>
                               
                    		</div>


                    	</div>
                        
                    </div>


                    <div class="col-md-12 form-group" style="margin-top: 10px;">

                    	<div class="row">

                    		<div class="col-md-2">

                    			<label>{{tr('ad_type')}}</label>

                                <input type="hidden" name="post_ad_type_id" id="post_ad_id" value="{{$postAd->id}}">

                                <br>

                    			<input type="checkbox" name="post_ad_type" id="post_ad_type" value="{{POST_AD}}" @if($postAd->ad_type == POST_AD) checked @endif onchange="getCheckBoxValue(this.id, this.value, '')"> {{tr('post_ad')}}

                    		</div>


                    	   <div class="col-md-3">

                    			<label>{{tr('ad_time')}} ({{tr('in_sec')}})</label>

                    			<input type="text" name="post_ad_time" id="post_ad_time" class="form-control" value="{{$postAd->ad_time}}" title="Enter Minimum 1 Character to Maximum 3 Character" maxlength="3" minlength="1">

                    		</div>


                    		<div class="col-md-6">

                    			<label>{{tr('ad')}}</label>

                    			<select id="post_parent_ad_id" name="post_parent_ad_id" class="form-control">
                                    <option value="">{{tr('select_ad')}}</option>
                                    @foreach($ads as $ad)
                                        <option value="{{$ad->id}}" @if($ad->id == $postAd->ad_id) selected @endif>{{$ad->name}}</option>
                                    @endforeach
                                </select>

                    		</div>


                    	</div>
                        
                    </div>      



                    @if(count($betweenAd) > 0 && $model->id)

                        @foreach($betweenAd as $index => $b_ad) 

                            @include('admin.video_ads._sub_form')

                        @endforeach

                    @else 



                        <?php $b_ad = new AssignVideoAd; ?>

                        @include('admin.video_ads._sub_form')


                    @endif


                    <div id="questionAdd"></div>

                    <input type="hidden" name="totalQuestion" id="totalIndex" value="0">



                </div>
              <div class="box-footer">
                    <button type="reset" class="btn btn-danger">{{tr('cancel')}}</button>
                    <button type="submit" class="btn btn-success pull-right" onclick="return checkAd()">{{tr('submit')}}</button>
              </div>

            </form>
        
        </div>

    </div>

</div>
   
@section('scripts')

<script src="{{asset('jwplayer/jwplayer.js')}}"></script>

<script>jwplayer.key="{{Setting::get('JWPLAYER_KEY')}}";</script>

<script type="text/javascript">
    
    jQuery(document).ready(function(){


            console.log('Inside Video');
                
            console.log('Inside Video Player');

            @if($url)

                var playerInstance = jwplayer("main-video-player");


                
                    var videoPath = "{{$videoPath}}";
                    var videoPixels = "{{$video_pixels}}";

                    var path = [];

                    var splitVideo = videoPath.split(',');

                    var splitVideoPixel = videoPixels.split(',');


                    for (var i = 0 ; i < splitVideo.length; i++) {
                        path.push({file : splitVideo[i], label : splitVideoPixel[i]});
                    }
                    playerInstance.setup({
                        sources: path,
                        image: "{{$vModel->default_image}}",
                        width: "100%",
                        height: "200px !important",
                        aspectratio: "16:9",
                        primary: "flash",
                        controls : true,
                        "controlbar.idlehide" : false,
                        controlBarMode:'floating',
                        "controls": {
                          "enableFullscreen": false,
                          "enablePlay": false,
                          "enablePause": false,
                          "enableMute": true,
                          "enableVolume": true
                        },
                        // autostart : true,
                        "sharing": {
                            "sites": ["reddit","facebook","twitter"]
                          }
                    });
                
                 // console.log("Duration" + playerInstance.getDuration());
                

            @endif

           
    });


function addQuestion(index) {

    index = $('#totalIndex').val();

    $.ajax({
        type : "post",
        url : "{{route('admin.video-ads.inter-ads')}}",
        data : {index:index},
        success : function(data) {

            $('#questionAdd').append(data);

            index = parseInt($('#totalIndex').val())+1;

            $('#totalIndex').val(index);

        },
        error : function(data) {

        }


    });
}

function removeQuestion(index) {

    console.log("Remove Ad");

    $('#adsDiv_'+index).remove();

    $('#adsDiv_'+index).find('input:text').val('');

    /*var e = $('#adsDiv_'+index);
    e.wrap('<form>').closest('form').get(0).reset();
    e.unwrap();    
*/
}

function loadFile(event, id){
    var reader = new FileReader();
    reader.onload = function(){
      var output = document.getElementById(id);
      output.src = reader.result;
       //$("#imagePreview").css("background-image", "url("+this.result+")");
    };
    reader.readAsDataURL(event.files[0]);
}

function getCheckBoxValue(id, ad_type,idx) {


    if(!($('#' + id).is(":checked"))) {

        if(ad_type == 1) {

            $('#pre_ad_time').val('');

            // $('#pre_parent_ad_id option:selected').remove();

            // $('#pre_parent_ad_id option[value=""]').attr("selected",true);

        } 

        if(ad_type == 2) {

            $('#post_ad_time').val('');

            // $('#post_parent_ad_id option:selected').remove();

        }

         if(ad_type == 3) {

            $('#between_ad_time_'+idx).val('');

            $("#between_ad_video_time_"+idx).val("");

            $("#between_ad_type_id_"+idx).val("");

            // $("#ad_id_"+idx+" option:selected").remove();

        }

    }

}


var duration_in_seconds = "{{$duration_in_seconds}}";

var minutes = 5 * 60;

function checkAd() {

    var pre_ad_type = $("#pre_ad_type").is(":checked");

    var post_ad_type = $("#post_ad_type").is(":checked");

    var between_ad_type = $("#between_ad_type_0").is(":checked");

    $("#pre_parent_ad_id").attr('required', false);

    $("#post_parent_ad_id").attr('required', false);

    $("#ad_id_0").attr('required', false);

    if (pre_ad_type == false && post_ad_type == false && between_ad_type == false) {

        alert("Select any one of the Ad Type..!");

        return false;
    }

    if (pre_ad_type) {

        var pre_ad_time = $("#pre_ad_time").val();

        if (pre_ad_time <= 0 || pre_ad_time == '' || pre_ad_time == undefined) {

            alert("Pre Ad Time should not be empty");

            return false;

        }

        if (!jQuery.isNumeric(pre_ad_time)) {

            alert("Pre Ad Time should not be Text");

            return false;

        }

        $("#pre_parent_ad_id").attr('required', true);

        if (parseInt(pre_ad_time) > minutes) {

            alert("Pre Ad Time should be greater than "+minutes+" seconds..!");

            return false;

        }
    }

    if (post_ad_type) {
    
        var post_ad_time = $("#post_ad_time").val();

        if (post_ad_time <= 0 || post_ad_time == '' || post_ad_time == undefined) {

            alert("Post Ad Time should not be empty");

            return false;

        }

        if (!jQuery.isNumeric(post_ad_time)) {

            alert("Post Ad Time should not be Text");

            return false;

        }


        $("#post_parent_ad_id").attr('required', true);

        if (parseInt(post_ad_time) > minutes) {

            alert("Post Ad Time should be greater than "+minutes+" seconds..!");

            return false;
            
        }

    }

    if (between_ad_type) {

        var betweenAd_length = $(".between_ads_class").length;

        var first_between_video_seconds = 0;

        for(var i = 0; i < betweenAd_length; i++) {

            $("#ad_id_"+i).attr('required', true);
     
            var between_ad_time = $("#between_ad_time_"+i).val(); 

            if (between_ad_time <= 0 || between_ad_time == '' || between_ad_time == undefined) {

                alert("Between Ad Time should not be empty");

                return false;

            } 

            if (!jQuery.isNumeric(between_ad_time)) {

                alert("Between Ad Time should not be Text");

                return false;

            }

            var between_video_time = $("#between_ad_video_time_"+i).val(); 

            var a = between_video_time.split(':'); // split it at the colons

            // minutes are worth 60 seconds. Hours are worth 60 minutes.
            var seconds = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2]); 

            
            if (seconds <= 0 || seconds == '' || seconds == undefined) {

                alert("Between Ad Time should not be empty");

                return false;

            } 

            if (parseInt(seconds) > minutes) {

                alert("Between Ad Time should be greater than "+minutes+" seconds..!");

                return false;
            }

            if (parseInt(seconds) > duration_in_seconds) {

                alert("Between Ad - Video Time should not be greater than Video Duration..!");

                return false;
            }

            if(i > 0 && parseInt(seconds) <= first_between_video_seconds) {

                alert("Between Ad - Video Time should be greater than Above Row");

                return false;
            }

            var betweenAd_end_length = betweenAd_length - 1;

            if(betweenAd_end_length == i) {

                if(parseInt(seconds) == duration_in_seconds) {

                    alert("Between Ad - Last Row Video Time should be equal to Video Duration");

                    return false;

                }
            
            }

            first_between_video_seconds = parseInt(seconds);
        }  

    }

}



</script>


@endsection