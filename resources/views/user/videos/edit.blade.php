@extends('layouts.user')
 
@section('styles')
<link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/wizard.css')}}">

<link rel="stylesheet" href="{{asset('admin-css/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css')}}">
@endsection

@section('content')
<?php $cur_sub = App\UserPayment::getCurSubscr(auth()->user()->id); ?>

<div class="y-content">

  <div class="row content-row">

		@include('layouts.user.nav')

    <div class="page-inner">
        <!--      Wizard container        -->
          <div class="col-sm-12">
                <div class="wizard-container">
                    <div class="card wizard-card" data-color="red" id="wizard">
                        <form action="{{ (Setting::get('admin_delete_control')) ? '' : route('user.video_save')}}" method="post" id="video_form" enctype="multipart/form-data">
                    <!--        You can switch " data-color="blue" "  with one of the next bright colors: "green", "orange", "red", "purple"             -->

                          <div class="wizard-header">
                              <h3 class="wizard-title">
                                {{tr('edit_video')}}
                              </h3>
                               <h5>{{tr('video_short_notes'  , Setting::get('site_name'))}}</h5>
                          </div>
                          <div class="wizard-navigation">
                              <ul>
                                  <li><a href="#video_type_upload" data-toggle="tab">{{tr('video_type')}}</a></li>
                                  <li><a href="#details" data-toggle="tab">{{tr('video_details')}}</a></li>
                                  <li><a href="#captain" data-toggle="tab">{{tr('upload_video')}}</a></li>
                                  <li><a href="#select_image" data-toggle="tab">{{tr('select_image')}}</a></li>
                              </ul>
                          </div>

                            <div class="tab-content">

                              <div class="tab-pane" id="video_type_upload">

                                  <?php
                                      $u_css = ($model->video_type == VIDEO_TYPE_UPLOAD) ? 'category-item-active' : '';
                                  ?>

                                   <div class="col-lg-6 col-md-6 col-sm-12 col-sx-12">

                                      <a onclick="saveVideoType({{VIDEO_TYPE_UPLOAD}}, {{REQUEST_STEP_PRE_1}})" class="category-item text-center {{$u_css}}">

                                          <div style="background-image: url({{asset('assets/img/file-upload-icon.jpg')}})" class="category-img bg-img"></div>

                                          <h3 class="category-tit">@if($u_css)<i class="fa fa-check-circle" style="color:#51af33" aria-hidden="true"></i>@endif Normal Video</h3>

                                      </a>

                                  </div>

                                  <?php
                                      $y_css = ($model->video_type == VIDEO_TYPE_YOUTUBE) ? 'category-item-active' : '';
                                  ?>

                                  <div class="col-lg-6 col-md-6 col-sm-12 col-sx-12">

                                      <a onclick="saveVideoType({{VIDEO_TYPE_YOUTUBE}}, {{REQUEST_STEP_PRE_1}})" class="category-item text-center {{$y_css}}">

                                          <div style="background-image: url({{asset('assets/img/upload-to-youtube.png')}})" class="category-img bg-img"></div>

                                          <h3 class="category-tit">@if($y_css)<i class="fa fa-check-circle" style="color:#51af33" aria-hidden="true"></i>@endif External Video</h3>

                                      </a>

                                  </div>

                                  <?php
                                      $o_css = ($model->video_type == VIDEO_TYPE_OTHERS) ? 'category-item-active' : '';
                                  ?>

                                  <div class="col-lg-4 col-md-4 col-sm-12 col-sx-12 hidden">

                                      <a onclick="saveVideoType({{VIDEO_TYPE_OTHERS}}, {{REQUEST_STEP_PRE_1}})" class="category-item text-center {{$o_css}}">

                                          <div style="background-image: url({{asset('assets/img/others.png')}})" class="category-img bg-img"></div>

                                          <h3 class="category-tit">@if($o_css)<i class="fa fa-check-circle" style="color:#51af33" aria-hidden="true"></i>@endif Others</h3>

                                      </a>

                                  </div>

                                  <input type="hidden" name="video_type" id="video_type" required value="{{$model->video_type}}" />

                                   <button type='button' class='btn btn-fill btn-danger btn-next pull-right' name='next' value='Next' id="first_btn">{{tr('next')}}</button>

                                   <div class="clearfix"></div>

                              </div>

                                <div class="tab-pane" id="details">
                                  <div class="row">
                                    <div class="col-sm-12">
                                        <h4 class="info-text">{{tr('let_start_basic_details')}}</h4>

                                        <input type="hidden" name="channel_id" id="channel_id" value="{{$model->channel_id}}">

                                        <input type="hidden" name="id" id="main_id" value="{{$model->id}}">
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-12">
                                        <label for="name" class="control-label">{{tr('title')}}</label>
                                        <div>
                                            <input type="text" required class="form-control" id="title" name="title" placeholder="{{tr('video_title')}}" value="{{old('title') ?: $model->title}}">
                                        </div>
                                    </div>

                                   

                                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                       
                                        <label for="video" class="control-label">{{tr('sub_title')}}</label>
                                        <div class="clearfix"></div>
                                        <div>
                                        <input type="file" id="subtitle" name="subtitle" style="width: 100%;overflow: hidden;" onchange="checksrt(this, this.id)">
                                        <p class="help-block">{{tr('subtitle_validate')}}</p>

                                        </div>
                                    </div>

                                     <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            <label for="age_limit" class="control-label">Age Limit * </label>&nbsp;&nbsp;
                                            <div class="clearfix"></div>
                                            <div>
                                                @if(isset($model->age_limit) && $model->age_limit > 0)
                                                <input type="radio" name="age_limit" id="age_limit_all" value="100" class='video_age_limit_class' {{ ($model->age_limit == 100)? 'checked':'' }} required /> For all age &nbsp;
                                                <input type="radio" name="age_limit" id="age_limit_18" value="18" class='video_age_limit_class' {{ ($model->age_limit == 18)? 'checked':'' }} required /> {{tr('18_users')}} &nbsp;
                                                    @if($cur_sub->content_num == 1)
                                                    <input type="radio" name="age_limit" id="age_limit_16" value="16" class="video_age_limit_class" {{ ($model->age_limit == 16)? 'checked':'' }} required /> 16+ Users Only &nbsp;
                                                    <input type="radio" name="age_limit" id="age_limit_14" value="14" class='video_age_limit_class' {{ ($model->age_limit == 14)? 'checked':'' }} required /> 14+ Users Only &nbsp; <br />
                                                    <input type="radio" name="age_limit" id="age_limit_12" value="12" class="video_age_limit_class" {{ ($model->age_limit == 12)? 'checked':'' }} required /> 12+ Users Only &nbsp;
                                                    @endif
                                                @else
                                                <input type="radio" name="age_limit" id="age_limit_all" value="100" class='video_age_limit_class' required /> For all age &nbsp;
                                                <input type="radio" name="age_limit" id="age_limit_18" value="18" checked class='video_age_limit_class' required /> {{tr('18_users')}} &nbsp;
                                                    @if($cur_sub->content_num == 1)
                                                    <input type="radio" name="age_limit" id="age_limit_16" value="16" class="video_age_limit_class" required /> 16+ Users Only &nbsp; 
                                                    <input type="radio" name="age_limit" id="age_limit_14" value="14" class='video_age_limit_class' required /> 14+ Users Only &nbsp;<br />
                                                    <input type="radio" name="age_limit" id="age_limit_12" value="12" class="video_age_limit_class" required /> 12+ Users Only &nbsp;
                                                    @endif
                                                @endif
                                            </div>
                                            <p class="help-block">{{tr('age_limit_note')}}</p>
                                        </div>
                                        <!-- <div class="form-group">
                                            <label for="datepicker" class="">{{tr('18_users')}} * </label>

                                            <div class="clearfix"></div>

                                            <input type="checkbox" name="age_limit" value="1" @if($model->age_limit) checked @endif> {{tr('yes')}}

                                            <p class="help-block">{{tr('age_limit_note')}}</p>
                                        </div> -->
                                    </div>

                                    <div class="col-sm-2">
                                      <div class="form-group">
                                            <label for="name" class="control-label">{{tr('publish_type')}}</label>&nbsp;&nbsp;
                                             <div class="clearfix"></div>
                                             <div>
                                              <input type="radio" onchange="checkPublishType(this.value)" name="video_publish_type" id="video_publish_type" value="{{PUBLISH_NOW}}" checked class='video_publish_type_class' required @if($model->video_publish_type == PUBLISH_NOW) checked @endif> {{tr('publish_now')}} &nbsp;
                                              <input type="radio" onchange="checkPublishType(this.value)" name="video_publish_type" id="video_publish_type" value="{{PUBLISH_LATER}}" class="video_publish_type_class" required @if($model->video_publish_type == PUBLISH_LATER) checked @endif/> {{tr('publish_later')}}
                                             </div>
                                       </div>
                                    </div>
                                    <div class="col-sm-3">
                                      <div class="form-group" style="display: none;" id="publish_time_div">
                                          <label for="datepicker" class="">{{tr('publish_time')}} * </label>
                                          <input type="text" name="publish_time" placeholder="dd-mm-yyyy hh:ii" class="form-control pull-right" id="datepicker" value="{{old('publish_time') ?: $model->publish_time}}">
                                      </div>
                                    </div>
                                    <div class="form-data">

                                      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                         
                                          <label for="video" class="control-label">{{tr('category')}} *</label>
                                          <div class="clearfix"></div>
                                          <div>

                                            <select id="category_id" name="category_id" class="form-control select2" required data-placeholder="{{tr('select_category')}}*" style="width: 100% !important">
                                                @foreach($categories as $category)
                                                      <option value="{{$category->category_id}}" @if($category->category_id ==  $model->category_id) selected @endif>{{$category->category_name}}</option>
                                                    @endforeach
                                            </select>

                                          </div>
                                       
                                      </div>

                                      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                         
                                          <label for="video" class="control-label">{{tr('tags')}}</label>
                                          <div class="clearfix"></div>
                                          <div>
                                            <select id="tag_id" name="tag_id[]" class="form-control select2"  data-placeholder="{{tr('select_tags')}}*" multiple style="width: 100% !important">
                                                @foreach($tags as $tag)
                                                      <option value="{{$tag->tag_id}}" {{in_array($tag->tag_id, $model->tag_id) ? 'selected' : ''}}>{{$tag->tag_name}}</option>
                                                @endforeach
                                            </select>


                                          </div>
                                       
                                      </div>

                                      <div class="clearfix"></div>

                                      <br>
                                    </div>

                                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="display: none;" id="duration_div">
                                      <div class="form-group">
                                          <label>{{tr('duration')}} * : </label><small> 
                                          {{tr('duration_note')}}</small>
                                          <div class="clearfix"></div>
                                          <div class="input-group">
                                              <div class="input-group-addon">
                                                  <i class="fa fa-calendar"></i>
                                              </div>
                                              <input type="text" name="duration" class="form-control" data-inputmask="'alias': 'hh:mm:ss'" data-mask id="duration" value="{{old('duration') ?: $model->duration}}">
                                          </div>
                                      </div>
                                    </div>

                                      <div class="clearfix"></div>
                                    <div class="col-sm-12">
                                       <!--  <label for="name" class="control-label">{{tr('description')}}</label> -->
                                        <div>
                                            <textarea placeholder="{{tr('description')}}" rows="5" required class="form-control" id="description" name="description" >{{old('description') ?: $model->description}}</textarea>
                                        </div>
                                    </div>
                                     <div class="clearfix"></div>

                                      <div class="col-lg-12">


                                          <div class="pull-left">

                                             <input type='button' class='btn btn-previous btn-fill btn-default btn-wd' name='previous' value='Previous' id="previous"/>

                                          </div>

                                          <div class="pull-right">  

                                              <input type='button' class='btn btn-fill btn-danger btn-wd' name='next' value='Next' onclick="countNext('description')" />

                                          </div>

                                          <div class="clearfix"></div>
                                      </div>
                                   
                                  </div>
                                </div>
                                <div class="tab-pane" id="captain">
                                    <h4 class="info-text">{{tr('do_upload')}}</h4>

                                     <div class="row" id="others_video_upload_section" style="display: none;">

                                        <!-- <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                                            <p>{{tr('video_type')}} - <span id="selected_video_type"></span></p>

                                        </div> -->

                                        <div class="clearfix"></div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                                            <div class="form-group">

                                                <label for="other_video" class="">{{tr('video')}} * </label>

                                                <input type="url" class="form-control" id="other_video" name="other_video" placeholder="{{tr('video')}}" value="{{$model->video}}">

                                            </div>

                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                                            <label>Choose Image</label>

                                            <input type="file" name="other_image" id="other_image" class="form-control" accept="image/png, image/jpeg">

                                        </div>

                                    </div>
                                    <div class="row" id="file_video_upload_section">
                                        <div class="dropify-wrapper" onclick="VideoFile({{Setting::get('admin_delete_control')}});">
                                          <div class="dropify-message">
                                            <span class="file-icon">
                                              <i class="fa fa-cloud-upload"></i>
                                            </span>
                                            <p>{{tr('click_here')}}</p>
                                          </div>
                                          <div class="dropify-preview">
                                              <span class="dropify-render">
                                              </span>
                                          </div>
                                        </div>

                                        @if (Setting::get('admin_delete_control'))

                                        @else
                                        <input id="video_file" type="file" name="video" style="display: none;" accept="video/mp4" onchange="$('#submit_btn').click();" required>
                                        @endif

                                        <br>
                                        <div class="progress" class="col-sm-12">
                                            <div class="bar"></div >
                                            <div class="percent">0%</div >
                                        </div>

                                        <input type="submit" name="submit" id="submit_btn" style="display: none">
                                    </div>

                                    <div class="">


                                        <div class="pull-left">

                                           <input type='button' class='btn btn-previous btn-fill btn-default btn-wd' name='previous' value='Previous' id="previous"/>

                                        </div>

                                        <div class="pull-right">  

                                            <input type='button' class='btn btn-fill btn-danger btn-wd' name='next' value='Next' onclick="countNext()" style="display: none" id="next_btn"/>

                                            <input type='submit' class='btn btn-fill btn-danger btn-wd finish' name='finish' value='Finish' id="manual_finish" style="display: none;" />

                                        </div>

                                        

                                        <div class="clearfix"></div>

                                        <br>

                                        
                                    </div>

                                </div>
                                <div class="tab-pane" id="select_image">
                                    <div class="row">
                                        <h4 class="info-text">{{tr('select_image_short_notes')}}</h4>
                                        <div class="col-sm-12" id="select_image_div">

                                        </div>
                                    </div>

                                    <div class="clearfix"></div>

                                    <br>

                                     <div class="">


                                        <div class="pull-left">

                                           <input type='button' class='btn btn-previous btn-fill btn-default btn-wd' name='previous' value='Previous' id="previous"/>

                                        </div>

                                        <div class="pull-right">  

                                            <input type='button' class='btn btn-finish btn-fill btn-danger btn-wd final' name='finish' value='Finish' onclick="redirect()" />

                                        </div>

                                        <br>

                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="wizard-footer" style="display: none">
                                <div class="pull-right">

                                      <input type='button' class='btn btn-abort btn-fill btn-warning btn-wd' name='abort' value="{{tr('abort')}}" id="abort_btn" onclick="abortVideo();"/>

                                      @if (Setting::get('admin_delete_control'))

                                      <input type='button' class='btn btn-fill btn-danger btn-wd' name='next' value='Next' disabled />

                                      @else

                                      <input type='button' class='btn btn-next btn-fill btn-danger btn-wd ctn' name='next' value='Next' id="click"/>
                                      
                                      @endif

                                    

                                      <input type='button' class='btn btn-finish btn-fill btn-danger btn-wd final' name='finish' value='Finish' onclick="redirect()" />
                                  </div>
                                  <div class="pull-left">
                                      <input type='button' class='btn btn-previous btn-fill btn-default btn-wd' name='previous' value='Previous' id="previous"/>
                                  </div>
                                  <div class="clearfix"></div>
                            </div>
                        </form>
                    </div>
                </div> <!-- wizard container -->
            </div>
        <div class="sidebar-back"></div> 
    </div>
    </div>
    		
</div>
<!-- <div class="overlay">
    <div id="loading-img"></div>
</div> -->
@endsection

@section('scripts')

<script src="{{asset('admin-css/plugins/bootstrap-datetimepicker/js/moment.min.js')}}"></script> 
<script type="text/javascript" src="{{asset('streamtube/js/jquery.bootstrap.js')}}"></script>
<script type="text/javascript" src="{{asset('streamtube/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('streamtube/js/material-bootstrap-wizard.js')}}"></script>
<script src="{{asset('admin-css/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js')}}"></script> 

<script src="{{asset('streamtube/js/jquery-form.js')}}"></script>

<script src="https://cdn.ckeditor.com/4.5.5/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace( 'description' );

</script>


<script>

    $("#abort_btn").hide();

    function abortVideo() {

      var id = $("#main_id").val();

        window.location.reload(true);


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
                  window.location.href = '/channel/'+$("#channel_id").val();
              } else {
                  alert(data.error_messages);
              }
          }
      });

      // window.location.href = '/channel/'+$("#channel_id").val();
   } 


    $.ajax({
        method : 'get',
        url : "{{route('user.get_images', $model->id)}}",
        success : function(data) {
          $("#select_image_div").html(data.path);
        }
    });   

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

          url : "{{route('user.save_default_img')}}",

          data : {id : value, idx : idx, img : image, video_tape_id : main_id},

          success : function(data) {

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
            $('.finish').hide();
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

            $("#video_file").removeAttr('disabled');

            if (error == "") {
              $("#next_btn").val("Next");
              $("#next_btn").attr('disabled', false);
              
              console.log(xhr);
              $("#abort_btn").hide();

            } else {

                $("#next_btn").val("Next");

                $("#next_btn").attr('disabled', false);
                $("#video_file").attr('disabled', false);

                var percentVal = '0%';
                bar.width(percentVal)
                percent.html(percentVal);
            }

            $(".finish").show();
            
        },
        error : function(xhr) {
            console.log(xhr);
        },

      
        success : function(xhr) {
            // $(".overlay").hide();

            if (xhr.success) {

              if(typeof xhr.data != 'undefined') {

                  if (xhr.path) {

                    console.log("inside " +xhr.data);

                    $("#select_image_div").html(xhr.path);

                    $("#main_id").val(xhr.data.id);

                    $("#abort_btn").hide();

                    $(".btn-next").click();

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

                      window.location.href = '/channel/'+$("#channel_id").val();

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

var edit_video_type = "{{$model->video_type}}";

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

    $("#duration_div").hide();

    $("#duration").attr('required', false);

    $("#other_video").val("{{$model->video}}");

    if (video_type != 1) {

      $("#duration_div").show();

      $("#duration").attr('required', true);

      if (video_type != edit_video_type) {

          $("#other_video").val("");

      }
    }

   // display_fields();

   // $("#next_btn").click();

   countNext();
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
  }


  if (video_type == 1) {

    $('#others_video_upload_section').hide();

    $('#file_video_upload_section').show();

    $("#next_btn").show();

  } else {


    $('#others_video_upload_section').show();

    $('#file_video_upload_section').hide();

    $("#manual_finish").show();

  }

  $("#click").click();


/*  var active_class = $(".wizard-navigation li.active").attr('id');

  alert(active_class);

  if (active_class == 2) {

      $('.ctn').hide();

  } */
}

function VideoFile(admin_delete_control) {

    if (admin_delete_control) {


    } else {

        $('#video_file').click();return false;

    }

    return false;

}

</script>

@endsection