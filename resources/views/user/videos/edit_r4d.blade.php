@extends('layouts.user')
 
@section('styles')
<link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/wizard.css')}}">

<link rel="stylesheet" href="{{asset('admin-css/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css')}}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />
<style>
.dropzone {
    background:#f0ad4e54;
}
.dz-remove {
    color: red !important;
    /* display:none !important; */
}
#create_new_directory {
    cursor: pointer;
}
.folder_img {
    width: 90%;
    cursor: pointer;
}
.folder_create_img {
    width: 15%;
    cursor: pointer;
}
.float_left {
    float:left;
}
.active_folder {
    border: 2px solid green;
    border-radius: 25% 25%;
}
.directory {
    width: 60px;
}
.move_files_links.active:parent {
    border: 2px solid green;
    border-radius: 25% 25%;
}
</style>
@endsection

@section('content')

<div class="y-content">

 <div class="row content-row">

    @include('layouts.user.nav')
    <div class="page-inner">

        <!--Wizard container-->
        <div class="col-sm-12">

            <div class="wizard-container">

                <div class="card wizard-card" data-color="red" id="wizard">

                    <form action="{{Setting::get('admin_delete_control') ? '' : route('user.video_save')}}" method="post" id="video_form" enctype="multipart/form-data">
                        <!--        You can switch " data-color="blue" "  with one of the next bright colors: "green", "orange", "red", "purple"             -->

                        <div class="wizard-header">
                            <h3 class="wizard-title">
                                Upload <img src="{{Setting::get('site_icon')}}" class="site_icon" /> Video
                              </h3>
                        </div>

                        <div class="wizard-navigation">
                            <ul>
                                <li id="1">
                                    <a href="#video_type_upload" data-toggle="tab" class="dis-all">{{tr('video_type')}}</a>
                                    <a href="#video_type_upload" data-toggle="tab" class="dis-mob">type</a>
                                </li>
                                <li id="2">
                                    <a href="#details" id="details_tab" data-toggle="tab">{{tr('video_details')}}</a>
                                </li>
                                <li id="3">
                                    <a href="#captain" data-toggle="tab">{{tr('upload_video')}}</a>
                                </li>
                                <li id="4">
                                    <a href="#select_image" data-toggle="tab">{{tr('select_image')}}</a>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content">

                            <div class="tab-pane" id="video_type_upload">

                                <div class="col-lg-3 col-md-3 col-sm-12 col-sx-12">
                                    <a onclick="saveVideoType({{VIDEO_TYPE_R4D}}, {{REQUEST_STEP_PRE_1}})" class="category-item text-center">

                                        <div style="background-image: url({{Setting::get('site_icon')}})" class="category-img bg-img1"></div>

                                        <h3 class="category-tit">R4D</h3>

                                    </a>

                                </div>

                                <div class="col-lg-3 col-md-3 col-sm-12 col-sx-12">

                                    <a onclick="saveVideoType({{VIDEO_TYPE_UPLOAD}}, {{REQUEST_STEP_PRE_1}})" class="category-item text-center">

                                        <div style="background-image: url({{asset('images/folder.png')}})" class="category-img bg-img1"></div>

                                        <h3 class="category-tit">File Upload</h3>

                                    </a>

                                </div>

                                <div class="col-lg-3 col-md-3 col-sm-12 col-sx-12">

                                    <a onclick="saveVideoType({{VIDEO_TYPE_YOUTUBE}}, {{REQUEST_STEP_PRE_1}})" class="category-item text-center">

                                        <div style="background-image: url({{asset('images/down-arrow.png')}})" class="category-img bg-img1"></div>

                                        <h3 class="category-tit">YouTube Link</h3>

                                    </a>

                                </div>

                                <div class="col-lg-3 col-md-3 col-sm-12 col-sx-12">

                                    <a onclick="saveVideoType({{VIDEO_TYPE_OTHERS}}, {{REQUEST_STEP_PRE_1}})" class="category-item text-center">

                                        <div style="background-image: url({{asset('images/link.png')}})" class="category-img bg-img1"></div>

                                        <h3 class="category-tit">Others</h3>

                                    </a>

                                </div>

                                <input type="hidden" name="video_type" id="video_type" required />

                                <input type='button' class='btn btn-fill btn-danger btn-next' name='next' value='Next' style="display: none" id="first_btn" />

                            </div>

                            <div class="tab-pane" id="details">
                                <div class="">
                                    <h4 class="info-text">{{tr('start_basics_details')}}</h4>
                                    
                                    @if(app('request')->input('id'))
                                    <input type="hidden" name="channel_id" id="channel_id" value="{{app('request')->input('id')}}">
                                    @else
                                    <input type="hidden" name="channel_id" id="channel_id" value="{{$id}}">
                                    @endif

                                    <input type="hidden" name="id" id="main_id">
                                </div>
                                <div class="row">

                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <label for="name" class="control-label">{{tr('title')}} * </label>
                                        <div>
                                            <input type="text" required class="form-control" id="title" name="title" placeholder="{{tr('video_title')}}" value="{{old('title')}}">
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

                                        <label for="video" class="control-label">{{tr('sub_title')}}</label>
                                        <div class="clearfix"></div>
                                        <div>
                                            <input type="file" id="subtitle" name="subtitle" style="width: 100%;overflow: hidden;" onchange="checksrt(this, this.id)">
                                            <p class="help-block">{{tr('subtitle_validate')}}</p>

                                        </div>

                                    </div>

                                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            <label for="datepicker" class="">{{tr('18_users')}} *</label>

                                            <div class="clearfix"></div>

                                            <input type="checkbox" id="age_limit" name="age_limit" value="1" required> {{tr('yes')}}

                                            <p class="help-block">{{tr('age_limit_note')}}</p>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            <label for="name" class="control-label">{{tr('publish_type')}}</label>&nbsp;&nbsp;
                                            <div class="clearfix"></div>
                                            <div>
                                                <input type="radio" onchange="checkPublishType(this.value)" name="video_publish_type" id="video_publish_type" value="{{PUBLISH_NOW}}" checked class='video_publish_type_class' required> {{tr('publish_now')}} &nbsp;
                                                <input type="radio" onchange="checkPublishType(this.value)" name="video_publish_type" id="video_publish_type" value="{{PUBLISH_LATER}}" class="video_publish_type_class" required /> {{tr('publish_later')}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                        <!-- <div class="form-group" style="display: none;" id="publish_time_div">
                                            <label for="datepicker" class="">{{tr('publish_time')}} * </label>
                                            <input type="text" name="publish_time" placeholder="dd-mm-yyyy hh:ii" class="form-control pull-right" id="datepicker" value="{{old('publish_time')}}">
                                        </div> -->
                                        <label for="video" class="control-label">Channel * </label>
                                        <div>

                                            <select id="channel_id_list" name="channel_id_list" class="form-control select2" required data-placeholder="Select Channel*" style="width: 100% !important">
                                                @if( app('request')->input('id') )
                                                    <?php $channel_id = app('request')->input('id'); ?>
                                                @else    
                                                    <?php $channel_id = $channel->id; ?>
                                                @endif

                                                @foreach($channels as $channel)
                                                <option value="{{$channel->id}}" {{ ($channel_id == $channel->id)?'selected':'' }}>{{$channel->name}}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>

                                    <div class="clearfix"></div>

                                    <div class="form-data">

                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

                                            <label for="video" class="control-label">{{tr('category')}} * </label>
                                            <div>

                                                <select id="category_id" name="category_id" class="form-control select2" required data-placeholder="{{tr('select_category')}}*" style="width: 100% !important">
                                                    @foreach($categories as $category)
                                                    <option value="{{$category->category_id}}">{{$category->category_name}}</option>
                                                    @endforeach
                                                </select>

                                            </div>

                                        </div>

                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

                                            <label for="video" class="control-label">{{tr('tags')}}</label>
                                            <div>

                                                <select id="tag_id" name="tag_id[]" class="form-control select2" data-placeholder="{{tr('select_tags')}}" multiple style="width: 100% !important">
                                                    @foreach($tags as $tag)
                                                    <option value="{{$tag->tag_id}}">{{$tag->tag_name}}</option>
                                                    @endforeach
                                                </select>

                                            </div>

                                        </div>
                                    </div>

                                    <div class="clearfix"></div>
                                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="display: none;" id="duration_div">
                                        <div class="form-group">
                                            <label>{{tr('duration')}} : </label><small> 
                                          {{tr('duration_note')}}</small>
                                            <div class="clearfix"></div>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                                <input type="text" name="duration" class="form-control" data-inputmask="'alias': 'hh:mm:ss'" data-mask id="duration" value="{{old('duration')}}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="clearfix"></div>
                                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="display: none;" id="r4d_img_div">
                                        <div class="form-group">
                                            <label>Choose Image * : </label>
                                            <div class="clearfix"></div>
                                            <div class="input-group">
                                                <input type="file" name="r4d_cover_img" id="r4d_cover_img" class="form-control" accept="image/png, image/jpeg">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="clearfix"></div>
                                    <div class="col-lg-12">
                                        <label for="name" class="control-label">{{tr('description')}} *</label>

                                        <div class="col-sm-12 col-lg-12">

                                            <textarea placeholder="{{tr('description')}}" rows="5" required class="form-control" id="description" name="description">{{old('description')}}</textarea>
                                        </div>
                                    </div>

                                    <div class="clearfix"></div>

                                    <div class="col-lg-12">

                                        <div class="pull-left">

                                            <input type='button' class='btn btn-previous btn-fill btn-default btn-wd' name='previous' value='Previous' id="previous" />

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

                                            <input type="url" class="form-control" id="other_video" name="other_video" placeholder="{{tr('video')}}">

                                        </div>

                                    </div>

                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                                        <label>Choose Image</label>

                                        <input type="file" name="other_image" id="other_image" class="form-control" accept="image/png, image/jpeg">

                                    </div>

                                </div>

                                <div class="" id="file_video_upload_section">
                                   
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
                                    
                                    @if(Setting::get('admin_delete_control')) 

                                    @else
                                        <input id="video_file" type="file" name="video" style="display: none;"  onchange="checkType()" accept=".mp4,.avi,.mov, .vob,.wmv,.R4D" required> 
                                    @endif

                                    <br>
                                    <div class="progress" class="col-sm-12">
                                        <div class="bar"></div>
                                        <div class="percent">0%</div>
                                    </div>

                                    <input type="submit" name="submit" id="submit_btn" style="display: none">
                                </div>
                                <!-- r4d_video_upload_section -->
                                <div id="r4d_video_upload_section" style="display: none;">
                                    @if(VIDEO_TYPE_R4D)
                                    <div id="r4d_folders">
                                        <input type="hidden" name="uploadfolder" id="uploadfolder" value="1"/>
                                        <input type="hidden" name="r4d_status" id="r4d_status" value="{{VIDEO_TYPE_R4D}}" />
                                        <div class="pull-left">
                                            <div id="directory_list">
                                                <div class="dropify-message" id="create_new_directory">
                                                    <span class="file-icon">
                                                        <img class="folder_create_img" src="{{asset('images/folder.png')}}" />
                                                    </span>
                                                    Create New Folder
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pull-right">
                                            <div id="directory_sub_list"></div>
                                            <input type="button" class="btn btn-danger" data-id="" id="directory_del_btn" style="display:none" value="Delete Selected Folder" />
                                            <div id="directory_file_list"></div>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="clearfix"></div>
                                    <div class="dropzone" id="dropzone">
                                        <div id="display_folder_files" class=""></div>
                                    </div>
                                </div>
                                <div class="">

                                    <div class="pull-left">

                                        <input type='button' class='btn btn-previous btn-fill btn-default btn-wd' name='previous' value='Previous' id="previous" />

                                    </div>

                                    <div class="pull-right">
									<!-- Code by G -->
                                        <input type="button" id="canceled" class='btn btn-fill btn-warning btn-wd' value="Cancel" onclicks="canFun()">
									<!-- ========= -->	
                                        <input type='button' class='btn btn-fill btn-danger btn-wd' name='next' value='Next' onclick="countNext()" style="display: none" id="next_btn" />

                                        <button type='submit' class='btn btn-fill btn-danger btn-wd finish' name='finish' value='Finish' id="manual_finish" style="display: none;">{{tr('finish')}}</button>

                                        <input type='button' class='btn btn-fill btn-danger btn-wd r4d_finish' name='r4d_finish' value="{{tr('finish')}}" id="r4d_finish" style="display: none;" />
                                        
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

                                        <input type='button' class='btn btn-previous btn-fill btn-default btn-wd' name='previous' value='Previous' id="previous" />

                                    </div>

                                    <div class="pull-right">

                                        <input type='button' class='btn btn-finish btn-fill btn-danger btn-wd final' name='finish' value='Finish' onclick="redirect()" />

                                    </div>

                                    <br>

                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
			
                        <div class="wizard-footer" style="display: none;">
                            <div class="pull-right">

                                <input type='button' class='btn btn-abort btn-fill btn-warning btn-wd' name='abort' value="{{tr('abort')}}" id="abort_btn" onclick="abortVideo();" /> 

                                @if (Setting::get('admin_delete_control'))

                                    <input type='button' class='btn btn-fill btn-danger btn-wd' name='next' value='Next' disabled /> 

                                @else

                                    <input type='button' class='btn btn-next btn-fill btn-danger btn-wd' name='next' value='Next2' id="next_btn" /> 

                                @endif

                                <input type='button' class='btn btn-finish btn-fill btn-danger btn-wd' name='finish' value='Finish' onclick="redirect()" />
                            </div>
                            <div class="pull-left">
                                <input type='button' class='btn btn-previous btn-fill btn-default btn-wd' name='previous' value='Previous' />
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- wizard container -->
        </div>
        <div class="sidebar-back"></div>
    </div>
</div>
    		
</div>
<input type="hidden" id="uploaded_video_id" />
<!-- <div class="overlay">
    <div id="loading-img"></div>
</div> -->
<div class="modal fade modal-top" id="move_files" role="dialog">
    <div class="modal-dialog bg-img modal-sm" style="">

        <div class="modal-content earning-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title no-margin">Move Videos</h3>
            </div>
            <div class="modal-body text-center">
                <div>
                    <label for="move_folder" class="control-label">Please Select folder * </label>
                    <select name="move_folder" id="move_folder" class="form-control select2" required data-placeholder="Select Directory *" style="width: 100% !important">
                        <option></option>
                    </select>
                </div>
                <div>
                    <a class="btn btn-success top" id="move_folder_btn">Move</a>
                    <a class="btn btn-danger top" data-dismiss="modal">Cancel</a>
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

<script src="https://cdn.ckeditor.com/4.5.5/standard/ckeditor.js"></script>

<script>
    CKEDITOR.replace( 'description' );
</script>
<script>
    @if(app('request')->input('video_type') == VIDEO_TYPE_R4D)
        saveVideoType(5, 5)
        $('#details_tab').click()
    @endif

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

        if (video_type != 1 && video_type != 5) {

        $("#duration_div").show();

        // $("#duration").attr('required', true);
        }
        if(video_type == 5) {
            $('#r4d_img_div').show();
            $("#r4d_cover_img").attr('required', true);
        }
        // display_fields();

        // $("#next_btn").click();

        countNext();
    }

    $(document).on('click', '.r4d_video_remove', function(e) {
        e.preventDefault()
        if(confirm("Do you want to delete this Video?")) {
            var filename = $(this).attr('data-fullfilename')
            var shortname = $(this).attr('data-filename')
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
        var sub_folder = $('.folder_img.active_folder').attr('data-id')
        var video_title = $('#title').val()
        var file_name = $('.r4d_video_remove').attr('data-filename')
        if(target_folder == sub_folder) {
            alert('Please select another folder!')
            return false;
        }

        $.ajax({
            type: "post",
            url : "{{route('user.move_files')}}",
            data : {video_title: video_title, sub_folder: sub_folder, target_folder: target_folder, file_name: file_name},
            success : function(data) {
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
        Dropzone.forElement('#dropzone').removeAllFiles(true)

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

                  window.location.href = '/channel/'+$("#channel_id").val()+"#videos";
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

          console.log(xhr,"-----",xhr.path, "this is xhr");

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
                        $('#r4d_status').val(1)
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
        var cover_img = $('#r4d_cover_img').val()
        if(!cover_img) {
            alert('Please upload image!');
            return false;
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
    $(document).on('click', '#r4d_finish', function() {
        window.location.href = '/channel/'+$("#channel_id").val()+"#videos";
    })
    $(document).on('change', '#channel_id_list', function() {
        $('#channel_id').val($(this).val())
    })
//     navigator.getMedia = ( navigator.getUserMedia || // use the proper vendor prefix
//                        navigator.webkitGetUserMedia ||
//                        navigator.mozGetUserMedia ||
//                        navigator.msGetUserMedia);
// console.log(navigator.getMedia,"check")
//     navigator.getMedia({video: true}, function() {
//     // webcam is available
//     console.log("yes ok")
//     }, function() {
//         // var check_camera = confirm("Please enable your camera!");
//         // if(check_camera) {

//         // }else {
//         //     e.request.allow();
//         //     console.log(e,"allowed")
//         //     location.reload()	
//         // }
//     console.log("yes no")
//     // webcam is not available
//     });
    
    // webview.addEventListener('permissionrequest', function(e) {
    //     if (e.permission === 'media') {
    //         e.request.allow();
    //     }
    // });
    
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
<script type="text/javascript">
    
    Dropzone.options.dropzone =
        {
            init: function() {
                var _this = this;
                // Setup the observer for the button.
                // document.getElementsByClassName("folder_img").addEventObserver("click", function() {
                //     console.log("clicked foldre uimg")
                //     // Using "_this" here, because "this" doesn't point to the dropzone anymore
                //     _this.removeAllFiles();
                //     // If you want to cancel uploads as well, you
                //     // could also call _this.removeAllFiles(true);
                // });
                clearDropzone = function(){
                    _this.removeAllFiles(true);
                };
                // clearDropzone()
            },
            maxFilesize: 12,
            // maxFiles: 1,
            renameFile: function (file) {
                var dt = new Date();
                var time = dt.getTime();
                str = file.name.replace(/\s/g, '');
                return str;
            },
            acceptedFiles: ".mp4,.avi,.mov,.vob,.wmv",
            addRemoveLinks: true,
            timeout: 50000,
            url:'{{ route("user.video_save") }}',
            // uploadMultiple: 'multiple',
            removedfile: function (file) {
                var name = file.upload.filename;
                var uploaded_video_id = $('#uploaded_video_id').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: 'GET',
                    url: '/delete/video/'+uploaded_video_id,
                    data: {filename: name},
                    success: function (data) {
                        console.log("File has been successfully removed!!");
                    },
                    error: function (e) {
                        console.log(e);
                    }
                });
                var fileRef;
                return (fileRef = file.previewElement) != null ?
                    fileRef.parentNode.removeChild(file.previewElement) : void 0;
            },
            sending: function(file, xhr, formData){
                var uploadfolder = $('#uploaded_video_id').val();
                var video_data = $('#video_form').serializeArray(); // You need to use standard javascript object here
                var subtitle = $('#subtitle').val()
                var other_image = $('#r4d_cover_img')
                var submit = $('#submit').val()
                video_data.forEach(function(item) {
                    formData.append(item.name, item.value);
                })

                formData.append('subtitle', subtitle);
                formData.append('other_image', other_image[0].files[0],'coverimg.png');
                formData.append('submit', submit);
		        // $('#submit_btn').click();
                // return false;
            },
            success: function (file, response) {
                if(response.success) {
                    console.log(response.data.id)
                    $('#uploaded_video_id').val(response.data.id)
                    $('#select_image_div').html(response.path)
                    $('#r4d_finish').show()
                }
            },
            error: function (file, response) {
                return false;
            }
        };
 
</script>
@endsection