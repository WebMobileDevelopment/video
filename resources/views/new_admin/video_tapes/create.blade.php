@extends('layouts.admin')
@if(app('request')->input('video_tape_id') && app('request')->input('video_type') == VIDEO_TYPE_R4D)
    @section('title', "Edit R4D Video")
    @section('content-header', 'Edit <img src="https://r4d4us.com/uploads/settings/8ee69f8cd641cc967de53ff2512a68db32af800b.png" class="site_icon_r4d"> Video')
@else
    @section('title', tr('add_video'))
    @section('content-header', tr('add_video'))
@endif
@section('styles')

    <link rel="stylesheet" href="{{asset('assets/css/wizard.css')}}">

    <link rel="stylesheet" href="{{asset('admin-css/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css')}}">

    <link rel="stylesheet" href="{{asset('admin-css/plugins/iCheck/all.css')}}">

    <link rel='stylesheet' type='text/css' href="{{asset('assets/dropzone/dropzone.css')}}">
    <link rel='stylesheet' type='text/css' href="{{asset('assets/css/dropzone_upload.css')}}">
    <style>
        .site_icon_r4d {
            width: 5%;
        }
        .site_icon_r4d_title {
            width: 25%;
        }
    </style>
@endsection

@section('breadcrumb')
    <li><a href="{{route('admin.video_tapes.index')}}"><i class="fa fa-video-camera"></i>{{tr('videos')}}</a></li>
    <li class="active"><i class="fa fa-video-camera"></i> {{tr('add_video')}}</li>
@endsection 

@section('content')

@include('notification.notify')


@if(envfile('QUEUE_DRIVER') != 'redis') 

 <div class="alert alert-warning">
    <button type="button" class="close" data-dismiss="alert">×</button>
        {{tr('warning_error_queue')}}
</div>
@endif

@if(checkSize())

 <div class="alert alert-warning">
    <button type="button" class="close" data-dismiss="alert">×</button>
        {{tr('max_upload_size')}} <b>{{ini_get('upload_max_filesize')}}</b>&nbsp;&amp;&nbsp;{{tr('post_max_size')}} <b>{{ini_get('post_max_size')}}</b>
</div>


@if(Setting::get('ffmpeg_installed') == FFMPEG_NOT_INSTALLED) 

 <div class="alert alert-warning">
    <button type="button" class="close" data-dismiss="alert">×</button>
        {{tr('ffmpeg_warning_notes')}}
</div>
@endif

@endif

<div class="row">
    <div class="col-lg-12">
        <section>
        <div class="wizard">
            <div class="wizard-inner">
                <div class="connecting-line"></div>
                <ul class="nav nav-tabs" role="tablist">

                    <li role="presentation" class="active">

                        <a href="#pre-step1" data-toggle="tab" aria-controls="pre-step1" role="tab" title="{{tr('choose_video_type')}}">
                            <span class="round-tab">
                                <i class="fa fa-file-video-o"></i>
                            </span>
                        </a>
                       
                    </li>

                    <li role="presentation" class="disabled video_details_tab">
                        <a href="#step1" data-toggle="tab" aria-controls="step1" role="tab" title="{{tr('video_details')}}">
                            <span class="round-tab">
                                <i class="glyphicon glyphicon-book"></i>
                            </span>
                        </a>
                    </li>

                    <li role="presentation" class="disabled">
                        <a href="#step2" data-toggle="tab" aria-controls="step2" role="tab" title="{{tr('channels')}}">
                            <span class="round-tab">
                                <i class="fa fa-tv"></i>
                            </span>
                        </a>
                    </li>
                    <li role="presentation" class="disabled">
                        <a href="#step3" data-toggle="tab" aria-controls="step3" role="tab" title="{{tr('upload_video')}}">
                            <span class="round-tab">
                                <i class="fa fa-video-camera"></i>
                            </span>
                        </a>
                    </li>

                    <li role="presentation" class="disabled">
                        <a href="#complete" data-toggle="tab" aria-controls="complete" role="tab" title="{{tr('select_image')}}">
                            <span class="round-tab">
                                <i class="glyphicon glyphicon-picture"></i>
                            </span>
                        </a>
                    </li>
                </ul>
            </div>

            <form id="video-upload" method="POST" enctype="multipart/form-data" role="form" action="{{route('admin.video_tapes.save')}}">

                <!-- Version 3.1 feature -->

                <input type="hidden" name="uploaded_by" value="{{ADMIN}}">
                <input type="hidden" name="video_tape_edit_id" id="video_tape_edit_id" value="0">
                @if(app('request')->input('video_tape_id') && app('request')->input('video_type') == VIDEO_TYPE_R4D)
                <input type="hidden" name="r4d_status" id="r4d_status" value="r4d_edit">
                @else
                <input type="hidden" name="r4d_status" id="r4d_status" value="">
                @endif
                <div class="tab-content">
                    <div class="tab-pane active" role="tabpanel" id="pre-step1">

                            <h3>{{tr('choose')}} {{tr('video_upload_type')}}</h3>
                            <hr>

                            <div id="category">

                                <div class="col-lg-4 col-md-4 col-sm-12 col-sx-12 r4d_video">
                                    <a onclick="saveVideoType({{VIDEO_TYPE_R4D}}, {{REQUEST_STEP_PRE_1}})" class="category-item text-center">

                                        <div style="background-image: url({{Setting::get('site_icon')}})" class="category-img bg-img1"></div>

                                        <h3 class="category-tit">R4D Video</h3>

                                    </a>

                                </div>

                                <div class="col-lg-4 col-md-4 col-sm-12 col-sx-12 video_details">

                                    <a onclick="saveVideoType({{VIDEO_TYPE_UPLOAD}}, {{REQUEST_STEP_PRE_1}})" class="category-item text-center">

                                        <div style="background-image: url({{asset('assets/img/file-upload-icon.jpg')}})" class="category-img bg-img"></div>

                                        <h3 class="category-tit">Normal Video</h3>

                                    </a>

                                </div>

                                <div class="col-lg-4 col-md-4 col-sm-12 col-sx-12">

                                    <a onclick="saveVideoType({{VIDEO_TYPE_YOUTUBE}}, {{REQUEST_STEP_PRE_1}})" class="category-item text-center">

                                        <div style="background-image: url({{asset('assets/img/upload-to-youtube.png')}})" class="category-img bg-img"></div>

                                        <h3 class="category-tit">External Video</h3>

                                    </a>

                                </div>

                                <div class="col-lg-3 col-md-3 col-sm-12 col-sx-12 hidden">

                                    <a onclick="saveVideoType({{VIDEO_TYPE_OTHERS}}, {{REQUEST_STEP_PRE_1}})" class="category-item text-center">

                                        <div style="background-image: url({{asset('assets/img/others.png')}})" class="category-img bg-img"></div>

                                        <h3 class="category-tit">Others</h3>

                                    </a>

                                </div>

                                <input type="hidden" name="video_type" id="video_type" />

                            </div>

                            <div class="clearfix"></div>

                            <ul class="list-inline">

                                <!-- <li class="pull-left">

                                    <button type="button" class="btn btn-danger prev-step">{{tr('previous')}}</button>

                                </li> -->

                                <li class="pull-right" style="display: none;">

                                    <button type="button" class="btn btn-primary next-step" id="{{REQUEST_STEP_PRE_1}}">{{tr('next')}}</button>

                                </li>

                                <div class="clearfix"></div>

                            </ul>

                    </div>

                    <div class="tab-pane" role="tabpanel" id="step1">
                        <!-- <h3>Video Details</h3> -->
                        <div style="margin-left: 15px"><small>{{tr('note')}} : <span style="color:red">*</span>{{tr('video_fields_mandatory')}}</small></div> 
                        <hr>
                        <div class="">
                            <input type="hidden" name="id" id="main_id">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    @if(app('request')->input('video_type') == VIDEO_TYPE_R4D)
                                    <label for="title" class=""><img src="{{Setting::get('site_icon')}}" class="site_icon_r4d_title" /> {{tr('title')}} * </label>
                                    <input type="text" required class="form-control" id="title" name="title" placeholder="{{tr('title')}}" value="{{$video_tape_details->title}}">
                                    @else
                                    <label for="title" class="">{{tr('title')}} * </label>
                                    <input type="text" required class="form-control" id="title" name="title" placeholder="{{tr('title')}}">
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label for="ratings" class="">{{tr('ratings')}} </label>
                                    <div class="starRating">
                                        <input id="rating5" type="radio" name="ratings" value="5">
                                        <label for="rating5">5</label>

                                        <input id="rating4" type="radio" name="ratings" value="4">
                                        <label for="rating4">4</label>

                                        <input id="rating3" type="radio" name="ratings" value="3">
                                        <label for="rating3">3</label>

                                        <input id="rating2" type="radio" name="ratings" value="2">
                                        <label for="rating2">2</label>

                                        <input id="rating1" type="radio" name="ratings" value="1">
                                        <label for="rating1">1</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label for="video" class="">{{tr('sub_title')}}</label>
                                    <input type="file" id="subtitle" name="subtitle" onchange="checksrt(this, this.id)">
                                    <p class="help-block">{{tr('subtitle_validate')}}</p>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label for="age_limit" class="control-label">Age Limit * </label>&nbsp;&nbsp;
                                    <div class="clearfix"></div>
                                    <div>
                                        @if(app('request')->input('video_type') == VIDEO_TYPE_R4D)
                                        <input type="radio" name="age_limit" id="age_limit_all" value="100" class='video_age_limit_class' {{ ($video_tape_details->age_limit == 100)? 'checked':'' }} required /> For all age &nbsp;
                                        <input type="radio" name="age_limit" id="age_limit_18" value="18" class='video_age_limit_class' {{ ($video_tape_details->age_limit == 18)? 'checked':'' }} required /> {{tr('18_users')}} &nbsp;
                                        <input type="radio" name="age_limit" id="age_limit_16" value="16" class="video_age_limit_class" {{ ($video_tape_details->age_limit == 16)? 'checked':'' }} required /> 16+ Users Only &nbsp;
                                        <input type="radio" name="age_limit" id="age_limit_14" value="14" class='video_age_limit_class' {{ ($video_tape_details->age_limit == 14)? 'checked':'' }} required /> 14+ Users Only &nbsp; <br />
                                        <input type="radio" name="age_limit" id="age_limit_12" value="12" class="video_age_limit_class" {{ ($video_tape_details->age_limit == 12)? 'checked':'' }} required /> 12+ Users Only &nbsp;
                                        @else
                                        <input type="radio" name="age_limit" id="age_limit_all" value="100" class='video_age_limit_class' required /> For all age &nbsp;
                                        <input type="radio" name="age_limit" id="age_limit_18" value="18" checked class='video_age_limit_class' required /> {{tr('18_users')}} &nbsp;
                                        <input type="radio" name="age_limit" id="age_limit_16" value="16" class="video_age_limit_class" required /> 16+ Users Only &nbsp; 
                                        <input type="radio" name="age_limit" id="age_limit_14" value="14" class='video_age_limit_class' required /> 14+ Users Only &nbsp;<br />
                                        <input type="radio" name="age_limit" id="age_limit_12" value="12" class="video_age_limit_class" required /> 12+ Users Only &nbsp;
                                        @endif
                                    </div>
                                    <p class="help-block">{{tr('age_limit_note')}}</p>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                <div class="form-group">

                                    <label for="publish_type" class="">{{tr('publish_type')}} * </label>
                                    <div class="clearfix"></div>

                                    <label>
                                        <input type="radio" name="video_publish_type" value="{{PUBLISH_NOW}}" class="flat-red" checked id="video_publish_type" onchange="checkPublishType(this.value)">
                                        {{tr('publish_now')}}
                                    </label>
                                    <label>
                                        <input type="radio" name="video_publish_type" class="flat-red"  value="{{PUBLISH_LATER}}" id="video_publish_type_later" onchange="checkPublishType(this.value)">
                                        {{tr('publish_later')}}
                                    </label>
            
                                </div>
                            </div>


                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <div class="form-group" style="display: none;" id="publish_time_div">
                                    <label for="datepicker" class="">{{tr('publish_time')}} * </label>

                                    <input type="text" name="publish_time" placeholder="{{tr('select_publish_time')}}" class="form-control pull-right" id="datepicker" readonly>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div class="form-data">

                              <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                 
                                  <label for="video" class="control-label">{{tr('category')}} * </label>
                                  <div class="clearfix"></div>
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
                                  <div class="clearfix"></div>
                                  <div>
                                      
                                    <select id="tag_id" name="tag_id[]" class="form-control select2" data-placeholder="{{tr('select_tags')}}" multiple style="width: 100% !important">
                                        @foreach($tags as $tag)
                                              <option value="{{$tag->tag_id}}">{{$tag->tag_name}}</option>
                                        @endforeach
                                    </select>


                                  </div>
                               
                              </div>

                              <div class="clearfix"></div>

                              <br>
                            </div>


                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12" style="display: none;" id="publish_time_div">
                                <div class="form-group" >
                                    <label for="datepicker" class="">{{tr('publish_time')}} * </label>

                                    <input type="text" name="publish_time" placeholder="Select the Publish Time i.e YYYY-MM-DD" class="form-control pull-right" id="datepicker">
                                </div>
                            </div>

                            
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12" style="display: none;" id="duration_div">
                                    <div class="form-group">
                                        <label>{{tr('duration')}} : </label><small> {{tr('duration_note')}}</small>

                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            @if(app('request')->input('video_tape_id') && app('request')->input('video_type') == VIDEO_TYPE_R4D)
                                                <input type="text" value="{{$video_tape_details->duration}}" name="duration" class="form-control" data-inputmask="'alias': 'hh:mm:ss'" data-mask id="duration">
                                            @else
                                                <input type="text" name="duration" class="form-control" data-inputmask="'alias': 'hh:mm:ss'" data-mask id="duration">
                                            @endif
                                        </div>
                                    </div>
                                </div>

                             
                             <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="display: none;" id="r4d_img_div">
                                <div class="form-group">
                                    <label>Choose Image * : </label><small>( Please upload *.png or *.jpeg files )</small>
                                    <div class="clearfix"></div>
                                    <div class="input-group">
                                        <span id='r4d_cover_img_val' class="input_file_val">
                                        @if(app('request')->input('video_type') == VIDEO_TYPE_R4D)
                                            @if($video_tape_details->default_image)
                                                uploaded_image.png
                                            @endif
                                        @endif
                                        </span>
                                        <span id='r4d_cover_img_button' class="input_file_button">Select File</span>
                                        <input type="file" name="r4d_cover_img" id="r4d_cover_img" class="form-control hidden" accept="image/png, image/jpeg">
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label for="reviews" class="">{{tr('reviews')}} </label>
                                    <textarea  style="overflow:auto;resize:none" class="form-control" rows="4" cols="50" id="reviews_textarea" name="reviews">@if(app('request')->input('video_type') == VIDEO_TYPE_R4D) {{$video_tape_details->reviews}} @endif</textarea>
                                </div>
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> 
                                <div class="form-group">
                                    <label for="description" class="">{{tr('description')}} * </label>
                                    <textarea  style="overflow:auto;resize:none" class="form-control" required rows="4" cols="50" id="description" name="description">@if(app('request')->input('video_type') == VIDEO_TYPE_R4D) {{$video_tape_details->description}} @endif</textarea>
                                </div>
                            </div>
                            
                        </div>
                        <ul class="list-inline">
                             <li class="pull-left">

                                    <button type="button" class="btn btn-danger prev-step">{{tr('previous')}}</button>

                                </li>

                                <li class="pull-right">

                                    <button type="button" style="display: none;" id="{{REQUEST_STEP_1}}" class="btn btn-primary next-step">{{tr('next')}}</button>

                                    <button type="button" class="btn btn-primary" onclick="saveVideoDetails({{REQUEST_STEP_1}})">{{tr('next')}}</button>
                                </li>

                                <div class="clearfix"></div>
                        </ul>
                    </div>
                    <div class="tab-pane" role="tabpanel" id="step2">
                        <h3>{{tr('channel')}}</h3>
                        <hr>
                        <div id="category">
                            @foreach($channels as $channel)
                            <div class="col-lg-4 col-md-4 col-sm-12 col-sx-12">
                                <a onclick="saveCategory({{$channel->id}}, {{REQUEST_STEP_2}})" class="category-item text-center">
                                    <div style="background-image: url({{$channel->picture}})" class="category-img bg-img"></div>
                                    <h3 class="category-tit">{{$channel->name}}</h3>
                                </a>
                            </div>
                            @endforeach
                            <input type="hidden" name="channel_id" id="channel_id" />
                        </div>
                        <div class="clearfix"></div>
                        <ul class="list-inline">
                            <li class="pull-left"><button type="button" class="btn btn-danger prev-step">{{tr('previous')}}</button></li>
                            <li class="pull-right" style="display: none"><button type="button" class="btn btn-primary next-step" id="{{REQUEST_STEP_2}}">{{tr('save_continue')}}</button></li>
                            <div class="clearfix"></div>
                        </ul>
                    </div>

                    <div class="tab-pane" role="tabpanel" id="step3">

                        <div id="others_video_upload_section" style="display: none;">

                            <!-- <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                                <p>{{tr('video_type')}} - <span id="selected_video_type"></span></p>

                            </div> -->

                            <div class="clearfix"></div>

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                                <div class="form-group">

                                    <label for="other_video" class="">{{tr('video')}} * </label>

                                    <input type="url" class="form-control" id="other_video" name="other_video" placeholder="{{ tr('video_link') }}">

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

                            <input id="video_file" type="file" name="video" style="display: none;" accept="video/mp4" onchange="$('#submit_btn').click();">

                            <br>
                            <div class="progress" class="col-sm-12">
                                <div class="bar"></div >
                                <div class="percent">0%</div >
                            </div>

                           
                        </div>
                        <div id="r4d_video_upload_section" style="display: none;">
                            @if(VIDEO_TYPE_R4D)
                            <div id="r4d_folders">
                                <input type="hidden" name="uploadfolder" id="uploadfolder" value="1"/>
                                <!-- <input type="hidden" name="r4d_status" id="r4d_status" value="{{VIDEO_TYPE_R4D}}" /> -->
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
                         <button type="submit" name="submit" id="submit_btn" style="display: none">{{tr('submit')}}</button>

                        <div class="clearfix"></div>

                         <ul class="list-inline">
                            <li class="pull-left">
                                    <button type="button" class="btn btn-danger prev-step">{{tr('previous')}}</button>
                                </li>

                                <li class="pull-right">
                                    <a href="{{route('admin.video_tapes.index')}}" id="r4d_finish" style="display:none;" type="button" class="btn btn-primary btn-info-full">{{tr('finish')}}</a>
                                    @if(Setting::get('admin_delete_control') == 1) 
                                        <button disabled id="{{REQUEST_STEP_FINAL}}" type="button" class="btn btn-primary btn-info-full finish">{{tr('finish')}}</button>
                                    @else
                                        <button id="{{REQUEST_STEP_FINAL}}" type="button" class="btn btn-primary btn-info-full finish" onclick="$('#submit_btn').click();">{{tr('finish')}}</button>
                                    @endif

                                    <button type="button" onchange="$('#submit_btn').click();" class="btn btn-primary next-step ctn" id="btn-next">{{tr('save_continue')}}</button>
                                    
                                </li>
                            <div class="clearfix"></div>
                        </ul>

                    </div>
                    
                    <div class="tab-pane" role="tabpanel" id="complete">
                        <!-- <h3>{{tr('upload_video_image')}}</h3> -->
                        <div style="margin-left: 15px"><small>{{tr('note')}} : {{tr('select_image_short_notes')}}</small></div> 
                        <hr>
                        <div class="row">
                           <!--  <h4 class="info-text">{{tr('select_image_short_notes')}}</h4> -->
                            <div class="col-sm-12" id="select_image_div">

                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <hr>
                        <ul class="list-inline">
                            <li><button type="button" class="btn btn-danger prev-step">{{tr('previous')}}</button></li>
                            <!-- <li><button type="button" class="btn btn-default next-step">Skip</button></li> -->
                            @if(Setting::get('admin_delete_control') == 1) 
                            <li class="pull-right"><button disabled id="{{REQUEST_STEP_FINAL}}" type="button" class="btn btn-primary btn-info-full">{{tr('finish')}}</button></li>
                            @else
                                <li class="pull-right"><button id="{{REQUEST_STEP_FINAL}}" type="button" class="btn btn-primary btn-info-full" onclick="redirect()">{{tr('finish')}}</button></li>
                            @endif
                            <div class="clearfix"></div>
                        </ul>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </form>
        </div>
    </section>
   </div>
</div>


<div class="overlay">
    <div id="loading-img"></div>
</div>
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
                    <select name="move_folder" id="move_folder" class="form-control" required data-placeholder="Select Directory *" style="width: 100% !important">
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

    <script src="{{asset('admin-css/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js')}}"></script> 

    <script src="{{asset('admin-css/plugins/iCheck/icheck.min.js')}}"></script>

    <script src="{{asset('streamtube/js/jquery-form.js')}}"></script>
    <script src="{{asset('assets/dropzone/dropzone.js')}}" type='text/javascript'></script>
    
    <script type="text/javascript">
        $.ajaxSetup({
            data:{
                uploaded_by: 'admin'
            }
        });
        function checkPublishType(val){
            $("#publish_time_div").hide();
            $("#datepicker").prop('required',false);
            $("#datepicker").val("");
            if(val == 2) {
                $("#publish_time_div").show();
                $("#datepicker").prop('required',true);
            }
        }

        var cat_url = "{{ url('select/sub_category')}}";
        var step3 = "{{REQUEST_STEP_3}}";
        var sub_cat_url = "{{ url('select/genre')}}";
        var final = "{{REQUEST_STEP_FINAL}}";

        var now = new Date();

        now.setHours(now.getHours())
        $('#datepicker').datetimepicker({
            autoclose:true,
            format : 'dd-mm-yyyy hh:ii',
            startDate:now,
        });

        $('#upload').show();
        $('#others').hide();
        $("#compress").show();
        $("#resolution").show();

        $("#video_upload").click(function(){
            $("#upload").show();
            $("#others").hide();
            $("#compress").show();
            $("#resolution").show();
        });

        $("#streamtube").click(function(){
            $("#others").show();
            $("#upload").hide();
            $("#compress").hide();
            $("#resolution").hide();
        });

        $("#other_link").click(function(){
            $("#others").show();
            $("#upload").hide();
            $("#compress").hide();
            $("#resolution").hide();
        });

        var main_video = "";
        
        var edit_video_type = "";

    </script>


    <script src="{{asset('assets/js/wizard.js')}}"></script>

    <script>
        $('form').submit(function () {
           window.onbeforeunload = null;
        });
        @if(!app('request')->input('video_tape_id') && app('request')->input('video_type') != VIDEO_TYPE_R4D)
            window.onbeforeunload = function() {
                if(parseInt($("#video_type").val()) != 5) {
                    return "Data will be lost if you leave the page, are you sure?";
                }
            };
        @endif
        var save_img_url = "{{route('admin.video_tapes.save.default_img')}}";

        var upload_video_image_url ="{{route('admin.video_tapes.upload_image')}}";

        function VideoFile(admin_delete_control) {

            if (admin_delete_control) {


            } else {

                $('#video_file').click();return false;

            }

            return false;

        }
    </script>

    <script src="https://cdn.ckeditor.com/4.5.5/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace( 'description' );
        //CKEDITOR.replace( 'reviews' );
    </script>
    <!-- <script src="{{asset('assets/js/dropzone_upload.js')}}"></script> -->
    @include('new_admin.video_tapes.partial.dropzone_upload')    
@endsection


