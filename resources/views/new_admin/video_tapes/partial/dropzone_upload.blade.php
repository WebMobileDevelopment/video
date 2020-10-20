<script>
    @if(app('request')->input('video_type') == VIDEO_TYPE_R4D)
        saveVideoType(5, 5)
        // var x = document.getElementById("4").setAttribute('style', 'display:none')
        $('.video_details_tab').removeClass('disabled')
        $('.video_details_tab a').click()
        $('#video_tape_edit_id').val({{app('request')->input('video_tape_id')}})
        $('#main_id').val({{app('request')->input('video_tape_id')}})
        $('#category').hide();
        @if($video_tape_details->public_type == 1)
        $('#video_publish_type').click()
        @else
        $('#video_publish_type_later').click()
        @endif
        $('#rating5'{{$video_tape_details->rating}}).click()
        $('#r4d_finish').show()
        $('#next_btn').hide()
    @endif

    $(document).on('click', '.r4d_video_remove', function(e) {
        e.preventDefault()
        if(confirm("Do you want to delete this Video?")) {
            var filename = $(this).attr('data-fullfilename')
            var shortname = $(this).attr('data-filename')
            var video_id = $('#main_id').val()
            $.ajax({
                type:"post",
                data:{filename: filename, shortname: shortname},
                url:'{{route("user.delete_r4d_files")}}',
                success: function(res) {
                    $('a.move_files_links[data-filename="'+res+'"]').parent().hide()
                }
            })
        }
        return false;
    })

    $(document).on('click', '.move_files_links', function() {
        $('.move_files_links').removeClass('active')
        $(this).addClass('active')
    })
    $(document).on('click', '#move_folder_btn', function() {
        console.log("ok")
        var target_folder = $('#move_folder').val()
        var sub_folder = $('#uploadfolder').val()
        var video_title = $('#title').val()
        var file_name = $('.r4d_video_remove').attr('data-filename')
        if(sub_folder == 0) {
            alert('Please select source folder first!')
            return false;
        }
        if(target_folder == sub_folder) {
            alert('Please select another folder!')
            return false;
        }

        $.ajax({
            type: "post",
            url : "{{route('user.move_files')}}",
            data : {video_title: video_title, sub_folder: sub_folder, target_folder: target_folder, file_name: file_name},
            success : function(data) {
                $('a[data-filename="'+file_name+'"]').hide()
                $('#move_files').modal('toggle')
            },
            error:function(data) {
                console.log(data);
            }
        })
    })
    $(document).on('click', '#directory_del_btn', function() {
        if(confirm('Selected folder and files will be deleted!')) {
            var subfolderId = $(this).attr('data-id')
            var video_title = $('#title').val()
            $.ajax({
                type: "post",
                url : "{{route('user.directory_delete')}}",
                data : {video_title: video_title, sub_folder: subfolderId},
                success : function(data) {
                    display_directory(data);    display_directory_move(data)
                    $('#directory_del_btn').hide();
                    alert('Successfully deleted!');
                },
                error:function(data) {
                    console.log(data);
                }
            })
        }
    })
    $(document).on('click', '.folder_img', function() {
        // Dropzone.forElement('#dropzone').removeAllFiles(true) //delete all uploaded files really
        $('.dz-preview.dz-file-preview.dz-processing.dz-complete').remove()
        $('.dz-default.dz-message').show();
        var video_title = $('#title').val()
        var subfolderId = $(this).attr('data-id')
        $('.folder_img').removeClass('active_folder')
        $(this).addClass('active_folder')
        $('#uploadfolder').val(subfolderId)
        $.ajax({
            type: "post",
            url : "{{route('user.directory_files')}}",
            data : {video_title: video_title, sub_folder: subfolderId},
            success : function(data) {
                display_directory_files(data);
            },
            error:function(data) {
                console.log(data);
            }
        });
        $('#directory_del_btn').attr('data-id', subfolderId);
        $('#directory_del_btn').show();
    })
    $('#create_new_directory').click(function() {
        var video_title = $('#title').val()
        $.ajax({
            type: "post",
            url : "{{route('user.directory_create')}}",
            data : {video_title: video_title, option:'sub'},
            success : function(data) {
                display_directory(data);    display_directory_move(data)
            },
            error:function(data) {
                console.log(data);
            }
        })
    })
    function display_directory_files(data) {
        console.log(data, "check data")
        if(data.length > 0) {
            var html = '<p>Uploaded Videos</p>';
            data.forEach(function(item) {
                html += '<div class="dz-preview dz-file-preview dz-processing dz-complete">'
                html += '<a data-toggle="modal" data-target="#move_files" data-id="" data-filename="'+item[1]+'" class="move_files_links">'
                html += '<div class="dz-image"></div><div class="dz-details">'
                html += '<div class="dz-size"><span data-dz-size=""><strong></strong></span></div><div class="dz-filename"><span data-dz-name="">'
                html += item[0]+'</span></div></div>'
                html += '<a class="dz-remove r4d_video_remove" data-filename="'+item[1]+'" data-fullfilename="'+item[2]+'" data-dz-remove="">Remove file</a>'
                html += '</a></div>'
            })
        }else
            var html = '<p>No Uploaded Videos</p>';
        
        $('#display_folder_files').html(html)
    }
    function display_directory(data) {
        data.sort((a,b)=>a-b)
        var html = '';
        data.forEach(function(item) {
            html += '<div class="float-left text-center directory"><span class="file-icon"><img data-id="'+item+'" class="folder_img" src="{{asset('assets/img/file-upload-icon.jpg')}}"></span><p>'+item+'</p></div>'
        })
        $('#directory_sub_list').html(html)
    }
    function display_directory_move(data) {
        data.sort((a,b)=>a-b)
        var html = '';
        data.forEach(function(item) {
            html += '<option value="'+item+'">'+item+'</option>'
        })
        $('#move_folder').html(html)
    }

    $("#abort_btn").hide();
    
    function abortVideo() {

      var id = $("#main_id").val();

      /*if (id != '' && id != undefined) {


      } else {*/

        window.location.reload(true);

      //}

    }
	
	//===============Code By G
	$("#canceled").hide();
    
	function checkType()
	{
		var fileInput = document.getElementById('video_file');
        var filePath = fileInput.value;
        // var allowedExtensions = /(\.3g2|\.3gp|\.avi|\.flv|\.mov|\.mp4|\.mpg|\.ogv|\.webm|\.wmv|\.mkv)$/i;
        var allowedExtensions = /(\.mp4|\.avi|\.mov|\.vob|\.wmv|\.R4D|\.mov|\.wmv)$/i;
        if(!allowedExtensions.exec(filePath)){
          alert('Please upload correct file format with extensions .mp4/.avi/.mov/.wmv/.vob/ and .R4D only.');
          fileInput.value = '';
          return false;
         }
		 else{
		     $('#submit_btn').click();
		 }
	}
    //==================

   function r4d_redirect() {

      var e = $('#video_file');
      e.wrap('<form>').closest('form').get(0).reset();
      e.unwrap();

      var formData = new FormData($("#video-upload")[0]);

       $.ajax({

          method : 'post',
          url : "{{route('user.upload_video_image')}}",
          data : formData,
          async: false,
          cache: false,
          contentType: false,
          processData: false,
          success : function(data) {
              if (data.success)  {
                  console.log(data);

                  if(data.message) {

                      var messages = $('#flash_message_ajax');

                      var responseMessage = '<div class="alert alert-success">'+
                    '<button type="button" class="close" data-dismiss="alert">&times;</button>'+
                    '<strong><i class="glyphicon glyphicon-ok-sign push-5-r"></</strong> '+ data.message +
                    '</div>';

                    $(messages).html(responseMessage);
                  }
                  var video_title = $('#title').val()
                  
                    $.ajax({
                        type:'POST',
                        data:{video_title: video_title, option: 'root'},
                        url:'{{route("user.directory_create")}}',
                        success: function(data) {
                            display_directory(data); display_directory_move(data)
                        }
                    })

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

    var error = "";
    function abortHandler(event) {
        console.log("aborted")
    }
    var ajax_xhr;
    $('form').ajaxForm({
        beforeSend: function(xhr) {
            // alert("BeforeSend");
            ajax_xhr = xhr
            var percentVal = '0%';
            bar.width(percentVal)
            percent.html(percentVal);
			$("#canceled").show();
            $("#next_btn").val("Wait Progressing...");
            $("#next_btn").attr('disabled', true);
            $("#video_file").attr('disabled', true);
            $("#abort_btn").show();
            $('.finish').hide();

            $('#canceled').click(function() {
                abort_upload()
            }) // for cancel button
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
				$("#canceled").hide();
            }
        },
        complete: function(xhr) {
            bar.width("100%");
            percent.html("100%");
           //  $(".overlay").show();
            $("#video_file").removeAttr('disabled');

            if (error == "") {

                $("#next_btn").val("Next");
                $("#next_btn").attr('disabled', false);
                console.log(xhr);
                $("#abort_btn").hide();

            }  else {

                $("#next_btn").val("Next");
                $("#canceled").hide();
                $("#next_btn").attr('disabled', false);
                $("#video_file").attr('disabled', false);

                var percentVal = '0%';
                bar.width(percentVal)
                percent.html(percentVal);
                
            }

            $(".finish").show();

        },
        error : function(xhr, result) {

        },
        success : function(xhr) {

            if (xhr.success) {

              if(typeof xhr.data != 'undefined') {

                  if (xhr.path) {

                    console.log("inside " +xhr.data);

                    $("#select_image_div").html(xhr.path);

                    $("#main_id").val(xhr.data.id);

                    $("#abort_btn").hide();
                   if(xhr.data.video_type == 1){
                        $(".btn-next").click();
                   }else if(xhr.data.video_type == 5) {
                        // $('#uploadfolder').val()
                        // $('#r4d_status').val(1)
                   }

                    $(".final").show();

                  } else {

                      console.log(xhr);

                      if(xhr.message) {

                          var messages = $('#flash_message_ajax');

                          var responseMessage = '<div class="alert alert-success">'+
                        '<button type="button" class="close" data-dismiss="alert">&times;</button>'+
                        '<strong><i class="glyphicon glyphicon-ok-sign push-5-r"></</strong> '+ xhr.message +
                        '</div>';

                        $(messages).html(responseMessage);
                      }

                      window.location.href = '/channel/'+$("#channel_id").val()+"#videos";

                  }
              } else {

                  alert(xhr.message);
                  $(".finish").show();

              }

            } else {

                error = 1;

                alert(xhr.error_messages);

                return false;

            }
        },
    }); 

    function abort_upload() {
        if(ajax_xhr) {
            var confirm1 = confirm('Do you want to cancel , Are you sure?');
            if(confirm1==true)
            {    
                ajax_xhr.abort()
                ajax_xhr = null;
                // ajaxReq = null
                // $("#file_video_upload_section").load(location.href + " #file_video_upload_section");
                percentVal = '0%';
                bar.width(percentVal)
                percent.html(percentVal);
                $("#canceled").hide();
                $(".finish").hide();
                document.getElementById("video_file").value = "";
                // $("#next_btn").attr('disabled', true);
                return false;
            }
        }
        return true;
    }

    // $('#canceled').trigger(function() {
	// 	$("#file_video_upload_section").load(location.href + " #file_video_upload_section");
    // })
   function canFun(e)
	{
		 var confirm1 = confirm('Do you want to cancel , Are you sure?');
		 if(confirm1==true)
		 {
			//  location.reload();
            console.log(ajaxCall,"ajaxcall")
            uploadProgress.abort();
            return false;
		 }
		// $("#file_video_upload_section").load(location.href + " #file_video_upload_section");
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




function countNext(desc_present) {

  var video_type = $("#video_type").val();

  $("#next_btn").hide();

  $("#manual_finish").hide();

  if(desc_present == 'description') {

    var description = CKEDITOR.instances['description'].getData();

    if (description == '') {

      alert("Description should not be blank");

      return false;

    } else {

        $("#description").val(description);

    }
    
    //send ajax request to create folder 
    if(video_type == {{VIDEO_TYPE_R4D}}) {
        $('#next_btn').addClass('hidden')
        var cover_img = $('#r4d_cover_img').val()
        
        var video_title = $('#title').val()
        var video_status = $('#r4d_status').val()
        if(video_status == 'r4d_edit') {
            r4d_redirect();
        }else {
            if(!cover_img) {
                alert('Please upload image!');
                return false;
            }
        }
        $.ajax({
            type:'POST',
            data:{video_title: video_title, option: 'root'},
            url:'{{route("user.directory_create")}}',
            success: function(data) {
                display_directory(data); display_directory_move(data)
            }
        })
    }
  }

  if (video_type == 1) {

    $('#others_video_upload_section').hide();

    $('#file_video_upload_section').show();

    $('#r4d_video_upload_section').hide();

    $("#next_btn").show();

  }  else if(video_type == 5) {

    $('#others_video_upload_section').hide();

    $('#file_video_upload_section').hide();

    $('#r4d_video_upload_section').show();

    $("#next_btn").show();

    $('#select_image').hide();
  }

  else {

    $('#others_video_upload_section').show();

    $('#file_video_upload_section').hide();

    $('#r4d_video_upload_section').hide();

    $("#manual_finish").show();

  }

  $("#first_btn").click();


/*  var active_class = $(".wizard-navigation li.active").attr('id');

  alert(active_class);

  if (active_class == 2) {

      $('.ctn').hide();

  } */
}

function VideoFile(admin_delete_control) {

    if (admin_delete_control) {


    } else {
		document.getElementById('video_file').value = '';
        $('#video_file').click();return false;

    }

    return false;

}

window.setTimeout(function(){

    $("#first_btn").hide();

}, 1000);
</script>
<script>
    // $(document).on('click', '#r4d_finish', function() {
    //     var r4d_status = $('#r4d_status').val()
    //     if(r4d_status == 'r4d_edit') {
    //         var r4d_id = $('#main_id').val()

    //     }else{

    //     }
    //     window.location.href = '/channel/'+$("#channel_id").val()+"#videos";
    // })
    $(document).on('change', '#channel_id', function() {
        $('#channel_id').val($(this).val())
    })
</script>
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
<script src="{{asset('assets/dropzone/dropzone.js')}}" type='text/javascript'></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script> -->
<script type="text/javascript">
Dropzone.autoDiscover = false;
$(".dropzone").dropzone({
    init: function() {
            var _this = this;
            // _this.on('removedfile', function() {
            //     console.log("ok check remove")
            // })
            // this.on("error", function (file, message) {
            //     alert(message);
            //     this.removeFile(file);
            // }); 
            // Setup the observer for the button.
            // document.getElementsByClassName("folder_img").addEventObserver("click", function() {
            //     console.log("clicked foldre uimg")
            //     // Using "_this" here, because "this" doesn't point to the dropzone anymore
            //     _this.removeAllFiles();
            //     // If you want to cancel uploads as well, you
            //     // could also call _this.removeAllFiles(true);
            // });
            // clearDropzone = function(){
            //     _this.removeAllFiles(true);
            // };
            // clearDropzone()
        },
        // autoProcessQueue:false,
        // uploadMultiple: true,
        // parallelUploads:3,
        // maxFiles: 1,
        renameFile: function (file) {
            var dt = new Date();
            var time = dt.getTime();
            str = file.name.replace(/\s/g, '');
            return '608198452_'+str;
        },
        acceptedFiles: ".mp4,.avi,.mov,.vob,.wmv",
        addRemoveLinks: true,
        timeout: 50000,
        url:'{{ route("user.video_save") }}',
        // maxFilesize: 1,//max file size in MB,
        // canceled: function(file) {
        //     console.log("cancel")
        //     $('#uploaded_video_id').val(1);
        //     var fileRef;
        //     return (fileRef = file.previewElement) != null ?
        //         fileRef.parentNode.removeChild(file.previewElement) : void 0;
        //     return false;    
        // },
        removedfile: function (file) {
            console.log(file.length,"chekc length")
            console.log("remove")
            console.log(file.status,"file status")
            // isFileUploadSuccess = (file.status === Dropzone.SUCCESS),
            
            var uploaded_video_id = $('#uploaded_video_id').val();

            if(file.status != "canceled" && confirm("Do you want to delete the video?")) {
                var name = file.upload.filename;
                var video_title = $('#title').val()
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: 'POST',
                    url: '{{route("user.delete.video.o_r4d")}}',
                    data: {filename: name, video_title: video_title},
                    success: function (data) {
                        $('#uploaded_video_id').val(0);
                        console.log("File has been successfully removed!");
                    },
                    error: function (e) {
                        console.log(e);
                    }
                });
            }

            var fileRef;
            return (fileRef = file.previewElement) != null ?
                fileRef.parentNode.removeChild(file.previewElement) : void 0;
            
        },
        sending: function(file, xhr, formData){
            var uploadfolder = $('#uploaded_video_id').val();
            var video_data = $('#video-upload').serializeArray(); // You need to use standard javascript object here
            var subtitle = $('#subtitle').val()
            var other_image = $('#r4d_cover_img')
            var submit = $('#submit').val()
            video_data.forEach(function(item) {
                formData.append(item.name, item.value);
            })

            formData.append('subtitle', subtitle);
            // formData.append('other_image', other_image[0].files[0],'coverimg.png');
            if(other_image[0].files[0] != undefined) {
                console.log(other_image[0].files[0],"other_image2")
                formData.append('banner_image', other_image[0].files[0],'coverimg.png');
            }
            // formData.append('other_image_1', other_image[0].files[0],'coverimg.png');
            // formData.append('other_image_2', other_image[0].files[0],'coverimg.png');
            formData.append('submit', submit);
            // $('#submit_btn').click();
            // return false;
        },
        success: function (file, response) {
            // var count= myDropzoneNST.files.length;

            console.log(response, "response")
            if(response.success) {
                console.log(response.data.id)
                $('#uploaded_video_id').val(1)
                $('#select_image_div').html(response.path)
                $('#r4d_finish').show()
            }else {
                if(!response.fileupload)
                    alert(response.error_messages)
            }
        },
        error: function (file, response) {
            return false;
        }
    });
</script>    
<script>
    $('#r4d_cover_img_button').click(function () {
        $("#r4d_cover_img").trigger('click');
    })
    $("#r4d_cover_img").change(function () {
        $('#r4d_cover_img_val').text(this.value.replace(/C:\\fakepath\\/i, ''))
    })
    $('#subtitle_button').click(function () {
        $("#subtitle").trigger('click');
    })
    $("#subtitle_img").change(function () {
        $('#subtitle_val').text(this.value.replace(/C:\\fakepath\\/i, ''))
    })
</script>