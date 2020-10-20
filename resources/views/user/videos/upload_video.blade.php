@extends('layouts.user')
 
@section('styles')
<link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/wizard.css')}}">

<link rel="stylesheet" href="{{asset('admin-css/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css')}}">
@endsection

@section('content')

<div class="y-content">

  	<div class="row content-row">

		@include('layouts.user.nav')

    	<div class="page-inner col-sm-9 col-md-10">
    		<div class="upload-video-sec">
    			<div class="upload-video-spacing">
    				<h3 class="mt-0">Upload video</h3>
    				<p class="upload-line"></p>
    				<div class="row">
    					<div class="col-sm-12 col-md-6 col-lg-6">
    						<!-- <h4 class="upload-sub-head">upload your video</h4> -->
    						<div class="upload-sec">
    							<img src="{{asset('images/file.png')}}">
    						</div>
    					</div>
    					<div class="col-sm-12 col-md-6 col-lg-6">
    						<div class="progress">
							  	  <div class="progress-bar progress-bar-striped  progress-bar-primary" role="progressbar"
							  	  aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:40%">
							    	
							  	</div>
							   </div>
    					</div>
    				</div>
    			</div>
    		</div>
    	</div>

  	</div>

</div>


@endsection


@section('scripts')

<script src="{{asset('admin-css/plugins/bootstrap-datetimepicker/js/moment.min.js')}}"></script> 
<script type="text/javascript" src="{{asset('streamtube/js/jquery.bootstrap.js')}}"></script>
<script type="text/javascript" src="{{asset('streamtube/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('streamtube/js/material-bootstrap-wizard.js')}}"></script>
<script src="{{asset('admin-css/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js')}}"></script> 

<script src="{{asset('streamtube/js/jquery-form.js')}}"></script>

<script>

     $("#abort_btn").hide();

    function abortVideo() {

      var id = $("#main_id").val();

      /*if (id != '' && id != undefined) {


      } else {*/

        window.location.reload(true);

      //}

    }

   function redirect() {

      var e = $('#video_file');
      e.wrap('<form>').closest('form').get(0).reset();
      e.unwrap();

      var formData = new FormData($("#video_form")[0]);

      $.ajax({

          method : 'post',
          url : "{{route('user.upload_video_image')}}",
          data : formData,
          async: false,
          cache: false,
          contentType: false,
          processData: false,
          success : function(data) {
              if (data.id)  {
                  window.location.href = '/channel/'+$("#channel_id").val();
              } else {
                  console.log(data);
              }
          }
      });

      // window.location.href = '/channel/'+$("#channel_id").val();
   } 

   function removePicture(idx) {

      $("#image_div_id_"+idx).show();

      $("#preview_image_div_"+idx).hide();

      $("#preview_"+idx).hide();

      var e = $('#img_'+idx);
      e.wrap('<form>').closest('form').get(0).reset();
      e.unwrap();


      return false;

   }

   function loadFile(event, id, idx){

       $("#image_div_id_"+idx).hide();

       $("#preview_image_div_"+idx).show();

       $("#preview_"+idx).show();

       $("#remove_circle_"+idx).show();

        // alert(event.files[0]);
        var reader = new FileReader();
        reader.onload = function(){
          var output = document.getElementById(id);
          // alert(output);
          output.src = reader.result;
           //$("#imagePreview").css("background-image", "url("+this.result+")");
        };
        reader.readAsDataURL(event.files[0]);
    }

    function saveAsDefault(main_id, value, idx, count, image) {

        for(var i = 0; i < count; i++) {

          $("#btn_"+i).removeClass('btn-success'); 

          $("#btn_"+i).addClass('btn-danger');

          $("#btn_"+i).html("Make Default"); 

        }

        if ($("#btn_"+idx).find('btn-danger')) {

          $("#btn_"+idx).removeClass('btn-danger');

          $("#btn_"+idx).addClass('btn-success');

          $("#btn_"+idx).html("Marked Default"); 

        } else {

          $("#btn_"+idx).removeClass('btn-success');

          $("#btn_"+idx).addClass('btn-danger');

          $("#btn_"+idx).html("Make Default"); 
        }

        console.log(value);

        console.log(idx);

        $.ajax({

          type: "post",

          url : "{{route('user.save_default_img')}}",

          data : {id : value, idx : idx, img : image, video_tape_id : main_id},

          success : function(data) {

              console.log("Object "+data);

              console.log(data);
          },

          error:function(data) {

            console.log(data);

          }

        })

    }

    function checkPublishType(val){
        $("#publish_time_div").hide();
        $("#datepicker").prop('required',false);
        $("#datepicker").val("");
        if(val == 2) {
            $("#publish_time_div").show();
            $("#datepicker").prop('required',true);
        }
    }
    var now = new Date();

    now.setHours(now.getHours())
    $('#datepicker').datetimepicker({
        autoclose:true,
        format : 'dd-mm-yyyy hh:ii',
        startDate:now,
    });


    /*$('form').submit(function () {
       window.onbeforeunload = null;
    });
    window.onbeforeunload = function() {
         return "Data will be lost if you leave the page, are you sure?";
    };*/


    var bar = $('.bar');
    var percent = $('.percent');

    $('form').ajaxForm({
        beforeSend: function() {
            // alert("BeforeSend");
            var percentVal = '0%';
            bar.width(percentVal)
            percent.html(percentVal);
            $("#next_btn").val("Wait Progressing...");
            $("#next_btn").attr('disabled', true);
            $("#video_file").attr('disabled', true);
            $("#abort_btn").show();
        },
        uploadProgress: function(event, position, total, percentComplete) {
            console.log(total);
            console.log(position);
            console.log(event);
            var percentVal = percentComplete + '%';
            bar.width(percentVal)
            percent.html(percentVal);
            if (percentComplete == 100) {
                $("#next_btn").val("Video Uploading...");
                // $(".overlay").show();
                $("#next_btn").attr('disabled', true);
                $("#video_file").attr('disabled', true);
            }
        },
        complete: function(xhr) {
            bar.width("100%");
            percent.html("100%");
           //  $(".overlay").show();
            $("#next_btn").val("Next");
            $("#next_btn").attr('disabled', false);
            $("#video_file").removeAttr('disabled');
            console.log(xhr);
            $("#abort_btn").hide();
        },
        error : function(xhr) {
            console.log(xhr);
        },
        success : function(xhr) {
            // $(".overlay").hide();
            if(xhr.data) {

                console.log("inside " +xhr.data);

                $("#select_image_div").html(xhr.path);

                $("#main_id").val(xhr.data.id);

                $("#abort_btn").hide();

                $(".btn-next").click();

            } else {
                console.log(xhr);
            }
        }
    }); 


/**
 * Clear the selected files 
 * @param id
 */
function clearSelectedFiles(id) {
    e = $('#'+id);
    e.wrap('<form>').closest('form').get(0).reset();
    e.unwrap();
}

function checksrt(e,id) {

    console.log(e.files[0].type);

    console.log(e.files[0].type == '');

    if(e.files[0].type == "application/x-subrip" || e.files[0].type == '') {


    } else {

        alert("Please select '.srt' files");

        clearSelectedFiles(id);

    }

    return false;
}
</script>

@endsection