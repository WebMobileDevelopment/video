$(document).ready(function () {
    //Initialize tooltips
    $('.nav-tabs > li a[title]').tooltip();
    
    //Wizard
    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {

        var $target = $(e.target);
    
        if ($target.parent().hasClass('disabled')) {
            return false;
        }
    });

    $(".next-step").click(function (e) {

        var $active = $('.wizard .nav-tabs li.active');

        $active.next().removeClass('disabled');

        nextTab($active);

    });

    $(".prev-step").click(function (e) {

        var $active = $('.wizard .nav-tabs li.active');

        prevTab($active);

       // enablePrevTab()

    });
});


function nextTab(elem) {
    $(elem).next().find('a[data-toggle="tab"]').click();
}
function prevTab(elem) {
    $(elem).prev().find('a[data-toggle="tab"]').click();
}


/**
 * Function Name : saveVideoType()
 * To save second step of the job details
 * 
 * @var category_id Category Id (Dynamic values)
 * @var step        Step Position 2
 *
 * @return Json response
 */
function saveVideoType(video_type, step) {

    $("#video_type").val(video_type);

    $("#other_video").val(main_video);
    if (video_type != 1) {

      $("#duration_div").show();

      if (video_type != edit_video_type) {

          $("#other_video").val("");

      }

    }
    if(video_type == 5) {
        $("#r4d_img_div").show()
        $("#r4d_finish").show()
        $('.finish').hide()
    }
    display_fields();

    $("#"+step).click();

    $("#next_btn").click();

}

function display_fields() {

    var video_type = $("#video_type").val();

    $('.ctn').show();

    $('.finish').show();

    if (video_type == 1) {

        $('.finish').hide();

    } else {

        $('.ctn').hide();

        $("#duration_div").show();

    }


}


/**
 * Function Name : saveVideoDetails()
 * To save first step of the job details
 * 
 * @var step        Step Position 1
 *
 * @return Json response
 */
function saveVideoDetails(step) {
   var title = $("#title").val();
   var datepicker = $("#datepicker").val();
   var rating = $("#rating").val();
   var description = CKEDITOR.instances['description'].getData();
   var reviews = $("#reviews_textarea").val();
   var duration = $("#duration").val();  
   var rating = $('input[name=ratings]:checked').val();

   var video_publish_type = $("#video_publish_type").val();
   var default_image = $('#r4d_cover_img')

   if (title == '') {
        alert('Title Should not be blank');
        return false;
   }
   if (datepicker == '' && video_publish_type == 2) {
        alert('Publish Time Should not be blank');
        return false;
   }

//    if (rating <= 0 || rating == undefined) {
//         alert('Ratings Should not be blank');
//         return false;
//    }
   if (description == '') {
        alert('Description Should not be blank');
        return false;
   } else {

      $("#description").val(description);
    
   }
   
//    if (reviews == '') {
//         alert('Reviews Should not be blank');
//         return false;
//    }
    var video_type = $('#video_type').val();

    console.log('video_type - '+video_type);

    if(parseInt(video_type) == 1) {

        console.log('video_type - YES');

        $('#others_video_upload_section').hide();
        $('#r4d_video_upload_section').hide();

        $('#file_video_upload_section').show();
    
    }else if(parseInt(video_type) == 5) {
        var video_tape_id = $('#video_tape_edit_id').val()
        if(default_image[0].files[0] == undefined && video_tape_id == 0) {
            alert("Please select default image");
            return false;
        }
        // get physical folders
        var video_title = $('#title').val()
        $.ajax({
            type:'POST',
            data:{video_title: video_title, option: 'root'},
            url:'/directory_create',
            success: function(data) {
                display_directory(data); display_directory_move(data)
            }
        })

        $('#others_video_upload_section').hide();
        $('#r4d_video_upload_section').show();

        $('#file_video_upload_section').hide();
    } else {

        $('#others_video_upload_section').show();

        $('#file_video_upload_section').hide();

        // if (duration == '') {

        //   alert('Duration Should not be blank');

        //   return false;

        // }

    }

   $("#"+step).click();
}


/**
 * Function Name : saveCategory()
 * To save second step of the job details
 * 
 * @var category_id Category Id (Dynamic values)
 * @var step        Step Position 2
 *
 * @return Json response
 */
function saveCategory(channel_id, step) {

    var video_type = $("#video_type").val();

    $('.ctn').show();

    $('.finish').show();

    if (video_type == 1) {

        $('.finish').hide();
    }else if(video_type == 5) {
        $('.finish').hide();
        $('#r4d_finish').show();
        $('.ctn').hide();
    } else {

        $('.ctn').hide();

    }

    $("#channel_id").val(channel_id);

    // displaySubCategory(category_id, step);
    var video_tape_edit_id = $('#video_tape_edit_id').val()
        console.log("ok", video_tape_edit_id,"check ")
    if(video_tape_edit_id > 0) {
        console.log("ok", video_tape_edit_id)
        r4d_redirect()
    }
    $("#"+step).click();
}

var bar = $('.bar');
var percent = $('.percent');


var error = "";

$('form').ajaxForm({
    beforeSend: function() {
        var percentVal = '0%';
        bar.width(percentVal)
        percent.html(percentVal);
        $("#btn-next").text("Wait Progressing...");
        $("#btn-next").attr('disabled', true);
    },
    uploadProgress: function(event, position, total, percentComplete) {
        console.log(total);
        console.log(position);
        console.log(event);
        var percentVal = percentComplete + '%';
        bar.width(percentVal)
        percent.html(percentVal);
        if (percentComplete == 100) {
            $("#btn-next").text("Video Uploading...");
            $(".overlay").show();
            $("#btn-next").attr('disabled', true);
        }
    },
    complete: function(xhr) {
        bar.width("100%");
        percent.html("100%");
        $(".overlay").hide();
        if(error == "") {
          $("#btn-next").text("Next");
          $("#btn-next").attr('disabled', false);
        } else {

          var percentVal = '0%';
          bar.width(percentVal)
          percent.html(percentVal);
          
        }
    },
    error : function(xhr) {
        console.log(xhr);
    },
    success : function(xhr) {

        $(".overlay").hide();

        if (xhr.success) {

          if(xhr.data) {

              if (xhr.path) {

                $("#select_image_div").html(xhr.path);

                $("#btn-next").val("Next");

                $("#btn-next").attr('disabled', false);

                $("#main_id").val(xhr.data.id);

                $("#btn-next").click();

              } else {

                 window.location.href = '/admin/videos/view?video_tape_id='+xhr.data.id;

              }

          } else {
              console.log(xhr);
          }

        } else {

            error = 1;

            alert(xhr.error_messages);

            return false;
        }
    }
}); 

function redirect() {

      var e = $('#video_file');
      e.wrap('<form>').closest('form').get(0).reset();
      e.unwrap();

      var formData = new FormData($("#video-upload")[0]);

      window.onbeforeunload = null;

      $.ajax({

          method : 'post',
          url : upload_video_image_url,
          data : formData,
          async: false,
          contentType: false,
          processData: false,
          success : function(data) {
             if (data.success)  {
                  console.log(data);
                  window.location.href = '/admin/videos/view?video_tape_id='+data.default_image_id;
              } else {
                  alert(data.error_messages);
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

       $("#remove_circle_"+idx).show();

       $("#preview_"+idx).show();

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

          url : save_img_url,

          data : {id : value, idx : idx, img : image, video_tape_id : main_id},

          success : function(data) {

              console.log(data);
          },

          error:function(data) {

            console.log(data);

          }

        })

    }




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