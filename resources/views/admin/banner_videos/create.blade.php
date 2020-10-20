@extends('layouts.admin')

@section('title', tr('add_banner_videos'))

@section('content-header', tr('add_banner_videos'))

@section('styles')

    <link rel="stylesheet" href="{{asset('assets/css/wizard.css')}}">

    <link rel="stylesheet" href="{{asset('admin-css/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css')}}">

    <link rel="stylesheet" href="{{asset('admin-css/plugins/iCheck/all.css')}}">
@endsection

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li><a href="{{route('admin.banner.videos')}}"><i class="fa fa-university"></i>{{tr('banner_videos')}}</a></li>
    <li class="active"><i class="fa fa-university"></i> {{tr('add_banner_videos')}}</li>
@endsection 

@section('content')

@include('notification.notify')


@if (envfile('QUEUE_DRIVER') != 'redis') 

 <div class="alert alert-warning">
    <button type="button" class="close" data-dismiss="alert">×</button>
        {{tr('warning_error_queue')}}
</div>

@endif

<div class="row">
    <div class="col-lg-12">
        <section>
        <div class="wizard">
            <div class="wizard-inner">
                <div class="connecting-line"></div>
                <ul class="nav nav-tabs" role="tablist">

                    <li role="presentation" class="active">
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

            <form id="video-upload" method="POST" enctype="multipart/form-data" role="form" action="{{route('admin.videos.save')}}">
                <div class="tab-content">
                    <div class="tab-pane active" role="tabpanel" id="step1">
                        <!-- <h3>Video Details</h3> -->
                        <div style="margin-left: 15px"><small>{{tr('note')}} : <span style="color:red">*</span>{{tr('video_fields_mandatory')}}</small></div> 
                        <hr>
                        <div class="">
                            <input type="hidden" name="id" id="main_id">
                            <input type="hidden" name="is_banner" id="is_banner" value="{{DEFAULT_TRUE}}">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label for="title" class="">{{tr('title')}} * </label>
                                    <input type="text" required class="form-control" id="title" name="title" placeholder="{{tr('title')}}">
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label for="ratings" class="">{{tr('ratings')}} *</label>
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

                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                
                                <div class="form-group">

                                    <label for="publish_type" class="">{{tr('publish_type')}}</label>
                                    <div class="clearfix"></div>
                                
                                    <label>
                                        <input type="radio" name="video_publish_type" value="{{PUBLISH_NOW}}" class="flat-red" checked id="video_publish_type" onchange="checkPublishType(this.value)">
                                        {{tr('publish_now')}}
                                    </label>
                                    

                                    <label>
                                        <input type="radio" name="video_publish_type" class="flat-red"  value="{{PUBLISH_LATER}}" id="video_publish_type" onchange="checkPublishType(this.value)">
                                        {{tr('publish_later')}}
                                    </label>

                                </div>
                            </div>


                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <div class="form-group" style="display: none;" id="publish_time_div">
                                    <label for="datepicker" class="">{{tr('publish_time')}} * </label>

                                    <input type="text" name="publish_time" placeholder="{{tr('select_publish_time')}}" class="form-control pull-right" id="datepicker">
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label for="description" class="">{{tr('description')}} * </label>
                                    <textarea  style="overflow:auto;resize:none" class="form-control" required rows="4" cols="50" id="description" name="description"></textarea>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label for="reviews" class="">{{tr('reviews')}} * </label>
                                    <textarea  style="overflow:auto;resize:none" class="form-control" required rows="4" cols="50" id="reviews" name="reviews"></textarea>
                                </div>
                            </div>
                        </div>
                        <ul class="list-inline pull-right">
                            <li>
                                <button type="button" style="display: none;" id="{{REQUEST_STEP_1}}" class="btn btn-primary next-step">{{tr('next')}}</button>
                                <button type="button" class="btn btn-primary" onclick="saveVideoDetails({{REQUEST_STEP_1}})">{{tr('next')}}</button>
                            </li>
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

                         <div class="">
                            <div class="dropify-wrapper" onclick="$('#video_file').click();return false;">
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

                            <input id="video_file" type="file" name="video" style="display: none;" accept="video/mp4" onchange="$('#submit_btn').click();" required>

                            <br>
                            <div class="progress" class="col-sm-12">
                                <div class="bar"></div >
                                <div class="percent">0%</div >
                            </div>

                            <input type="submit" name="submit" id="submit_btn" style="display: none">
                        </div>
                        <div class="clearfix"></div>

                        <ul class="list-inline">
                            <li class="pull-left"><button type="button" class="btn btn-danger prev-step">{{tr('previous')}}</button></li>
                            <li class="pull-right">
                                <button type="button" class="btn btn-primary next-step"  id="btn-next">{{tr('save_continue')}}</button>
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

@endsection

@section('scripts')

    <script src="{{asset('admin-css/plugins/bootstrap-datetimepicker/js/moment.min.js')}}"></script> 

    <script src="{{asset('admin-css/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js')}}"></script> 

    <script src="{{asset('admin-css/plugins/iCheck/icheck.min.js')}}"></script>

    <script src="{{asset('streamtube/js/jquery-form.js')}}"></script>

    
    <script type="text/javascript">

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

    </script>


    <script src="{{asset('assets/js/wizard.js')}}"></script>

    <script>
        $('form').submit(function () {
           window.onbeforeunload = null;
        });
        window.onbeforeunload = function() {
             return "Data will be lost if you leave the page, are you sure?";
        };

        var save_img_url = "{{route('admin.videos.save.default_img')}}";

        var upload_video_image_url ="{{route('admin.videos.upload_image')}}";
    </script>
 
@endsection