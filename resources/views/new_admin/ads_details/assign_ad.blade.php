@extends('layouts.admin')

@section('title', tr('assign_ad'))

@section('content-header', tr('assign_ad'))

@section('breadcrumb')
    <li><a href="{{route('admin.ads-details.index')}}"><i class="fa fa-bullhorn"></i>{{tr('video_ads')}}</a></li>
    <li class="active"><i class="fa fa-bullhorn"></i> {{tr('assign_ad')}}</li>
@endsection

@section('content')

@include('notification.notify')

<div class="row">
    
    <div class="col-xs-12">
    
        <div class="box box-primary">

            <div class="box-header label-primary">
                <b style="font-size:18px;">{{tr('assign_ad')}}</b>

                <button id="assign_ad" class="btn btn-default pull-right" data-toggle="modal" data-target="#multiple_ad">{{tr('assign_ad')}}</button>
            </div>

            <div class="box-body">

                @if(count($video_tapes) > 0)

                <table id="example1" class="table table-bordered table-striped">

                    <thead>
                        <tr>
                          <th>{{tr('id')}}</th>
                          <th>{{tr('title')}}</th>
                          <th>{{tr('ads')}}</th>
                          <th> 
                              {{tr('action')}}
                          </th>
                        </tr>
                    </thead>
                    
                    <tbody>
                  
                        @foreach($video_tapes as $i => $video_tape_details)
                            <tr>
                                <td>{{$i+1}}</td>
                                
                                <td><a href="{{route('admin.video_tapes.view', ['video_tape_id' => $video_tape_details->id] )}}" target="_blank">{{$video_tape_details->title}}</a></td>
                                
                                <td>

                                    <?php $types = getVideoAdsTpe($video_tape_details->id); ?>

                                    @if($types)

                                      @foreach($types as $val)
                                        
                                        <span class="label label-success">{{$val}}</span>

                                      @endforeach

                                    @else

                                      -

                                    @endif
                                </td>
                                <td>
                                    @if($type == 1) 

                                      <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#myModal_{{$i}}">{{tr('assign_ad')}}</button>

                                        <div id="myModal_{{$i}}" class="modal fade" role="dialog">
                                          <div class="modal-dialog">
                                            <form method="post" action="{{route('admin.video-ads.assign.ads')}}" id="assing_ad_form">
                                                  <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            <h4 class="modal-title">{{tr('assign_ad')}}</h4>
                                                        </div>
                                                      
                                                        <div class="modal-body">

                                                            <p>{{tr('ad_note_for_video_time')}}</p>
                                              
                                                            <input type="hidden" name="ad_id" id="ad_id" value="{{$ad_details->id}}">

                                                            <input type="hidden" name="type" id="type" value="{{$type}}">

                                                            <input type="hidden" name="video_tape_id" id="video_tape_id" required value="{{$video_tape_details->id}}">

                                                            <div class="row">

                                                            <div class="col-md-12">

                                                              <label>{{tr('ad_type')}}</label>

                                                              <br>

                                                              <input type="checkbox" name="ad_type[]" id="ad_type" value="{{PRE_AD}}"> {{tr('pre_ad')}}
                                                                    <input type="checkbox" name="ad_type[]" id="ad_type" value="{{POST_AD}}"> {{tr('post_ad')}}
                                                                    <input type="checkbox" name="ad_type[]" id="ad_type_id_{{$i}}" value="{{BETWEEN_AD}}" onchange="getCheckBoxValue(this.id, {{$i}})"> {{tr('between_ad')}}

                                                            </div>

                                                          </div>

                                                          <div class="row" style="margin-top: 10px">

                                                            <div class="col-md-6">

                                                              <label>{{tr('ad_time')}} ({{tr('in_sec')}})</label>

                                                              <input type="text" name="ad_time" id="ad_time" class="form-control" value="{{$ad_details->ad_time}}">

                                                            </div>


                                                            <div class="col-lg-6" id="video_time_div_{{$i}}" style="display: none;">

                                                                <label>{{tr('video_time')}}</label>

                                                                <br>

                                                                <input type="text" name="video_time" id="video_time_{{$i}}" class="form-control" placeholder="hh:mm:ss">
                                                              </div>

                                                            </div>
                                                            <div class="clearfix"></div>

                                                          </div>


                                                        <div class="modal-footer">
                                                          <button type="button" class="btn btn-default" data-dismiss="modal">{{tr('close')}}</button>
                                                          <button type="submit" class="btn btn-success">{{tr('assign')}}</button>
                                                        </div>

                                                      </div>

                                                      
                                                  </div>

                                            </form>
                                            
                                          </div>
                                        </div>

                                    @else
                                    <input type="checkbox" class="case" name="select_item" id="select_item" value="{{$video_tape_details->id}}">
                                    @endif
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

                <div id="multiple_ad" class="modal fade" role="dialog">
                    
                    <div class="modal-dialog">
                    
                        <form method="post" action="{{route('admin.video-ads.assign.ads')}}" id="assing_ad_form">
                                
                            <div class="modal-content">
                               
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">{{tr('assign_ad')}}</h4>
                                </div>
                              
                                <div class="modal-body">
                                  
                                    <input type="hidden" name="ad_id" id="ad_id" value="{{$ad_details->id}}">

                                    <input type="hidden" name="type" id="type" value="{{$type}}">

                                    <input type="hidden" name="video_tape_id" id="video_tape_id" required value="{{$video_tape_details->id}}">

                                    <div class="row">

                                        <div class="col-md-6">

                                          <label>{{tr('ad_type')}}</label>

                                          <br>

                                          <input type="checkbox" name="ad_type[]" id="ad_type" value="{{PRE_AD}}"> {{tr('pre_ad')}}
                                                <input type="checkbox" name="ad_type[]" id="ad_type" value="{{POST_AD}}"> {{tr('post_ad')}}
                                        </div>

                                        <div class="col-md-6">

                                          <label>{{tr('ad_time')}} ({{tr('in_sec')}})</label>

                                          <input type="text" name="ad_time" id="ad_time" class="form-control" value="{{$ad_details->ad_time}}">

                                        </div>
                                    
                                    </div>                       

                                </div>

                                <div class="modal-footer">
                                    
                                    <button type="button" class="btn btn-default" data-dismiss="modal">{{tr('close')}}</button>
                                    
                                    <button type="submit" class="btn btn-success" id="submit-btn">{{tr('assign')}}</button>
                                </div>

                            </div>

                        </form>
                    
                    </div>

                </div>

                @else
                    <h3 class="no-result">{{tr('no_ads_found')}}</h3>
                @endif
                
            </div>

        </div>

    </div>

</div>

@endsection

@section('scripts')

<SCRIPT language="javascript">

function getCheckBoxValue(id, i) {

    if($('#'+id).is(":checked")) {

        $("#video_time_div_"+i).show();

        $("#video_time_"+i).attr('required', true);

    } else {

      $('#video_time_'+i).val('');

      $("#video_time_div_"+i).hide();

      $("#video_time_"+i).removeAttr('required');
   

    }

}

$("#assign_ad").click(function(event){

    event.preventDefault();

    var searchIDs = $("#example1 input:checkbox:checked").map(function(){
      return $(this).val();
    }).get(); 

    if(searchIDs.length == 0) {

      alert("Select any one of the Video and Assign ad");

      return false;

    } else {

      $("#video_tape_id").val(searchIDs.join(','));

      @if($ad_details->ad_type == POST_AD || $ad_details->ad_type == PRE_AD)

        $("#submit-btn").click();

      @endif

    }


});
</SCRIPT>

@endsection


<?php /*

@extends('layouts.admin')

@section('title', tr('assign_ad'))

@section('content-header', tr('assign_ad'))

@section('breadcrumb')
    <li><a href="{{route('admin.ads-details.index')}}"><i class="fa fa-bullhorn"></i>{{tr('video_ads')}}</a></li>
    <li class="active"><i class="fa fa-bullhorn"></i> {{tr('assign_ad')}}</li>
@endsection

@section('content')
  <div class="row">

      @include('notification.notify')

      @if(count($video_tapes) > 0)

        <div class="col-lg-12" style="margin-bottom: 10px;">

          <div class="pull-right">

              <input type="checkbox" name="select_all" id="selectall" style="vertical-align: middle;" /> {{tr('check_all')}} &nbsp;&nbsp;

              @if($ad_details->ad_type == PRE_AD || $ad_details->ad_type == POST_AD)

                  <button id="assign_ad" type="button" class="btn btn-sm btn-success">{{tr('assign_ad')}}</button>

              @else

                <!-- Trigger the modal with a button -->
                  <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#myModal" id="assign_ad">{{tr('assign_ad')}}</button>

              @endif

          </div>

          <div class="clearfix"></div>
        </div>

        <div id="assign_ad_div">
          @foreach($video_tapes as $video)
          <div class="col-sm-3">
            <div class="box box-primary">
              <div class="box-header with-border">
                <h3 class="box-title">{{$video->title}}</h3>
                <span class="pull-right"><input type="checkbox" class="case" name="select_item" id="select_item" value="{{$video->id}}"></span>
              </div><!-- /.box-header -->
              <div class="box-body">
                <!-- <p>Compiled and ready to use in production. Download this version if you don't want to customize AdminLTE's LESS files.</p> -->
                <img src="{{$video->default_image}}" style="width: 100%; height: 130px; margin-bottom: 10px;" />
                <p><b>{{tr('type_of_ads')}} : </b> {{($video->getVideoAds) ? implode(',' , getTypeOfAds($video->getVideoAds->types_of_ad)) : '-'}}</p>
                <a href="{{route('admin.video_tapes.view' , array('video_tape_id' => $video->id))}}" class="btn btn-primary" target="_blank"><i class="fa fa-eye"></i> {{tr('view')}}</a>
              </div><!-- /.box-body -->
            </div><!-- /.box -->
          </div><!-- /.col -->
          @endforeach
        </div>

        <div class="col-lg-12">

            <div align="center" id="paglink">{{$video_tapes->links()}}</div>

        </div>

      @endif

      @if(count($video_tapes) == 0) 

      <div class="col-lg-12">{{tr('no_videos_found')}}</div>

      @endif

      <!-- Modal -->
      <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

          <form method="post" action="{{route('admin.video-ads.assign.ads')}}" id="assing_ad_form">

          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">{{tr('assign_ad')}}</h4>
            </div>
            <div class="modal-body">
              <p>{{tr('ad_note_for_video_time')}}</p>

              
                
                <input type="hidden" name="ad_id" id="ad_id" value="{{$ad_details->id}}">

                <input type="hidden" name="video_tape_ids" id="video_tape_id" required>

                <input type="hidden" name="ad_type" id="ad_type" value="{{$ad_details->ad_type}}"> 

                @if($ad_details->ad_type == PRE_AD || $ad_details->ad_type == POST_AD)

                  <input type="hidden" name="video_time" id="video_time">

                @else 

                <div class="row">

                  <div class="col-lg-12">

                    <label>{{tr('video_time')}}</label>

                    <input type="text" name="video_time" id="video_time" class="form-control" placeholder="hh:mm:ss" required>

                  </div>

                </div>

                @endif

              
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">{{tr('close')}}</button>
              <button type="submit" class="btn btn-success" id="submit-btn">{{tr('assign')}}</button>
            </div>
          </div>

          </form>

        </div>
      </div>

@endsection


@section('scripts')

<SCRIPT language="javascript">

$(function(){

  $("#selectall").click(function () {
      $('.case').prop('checked', this.checked);
  });

  $(".case").click(function(){
    if($(".case").length == $(".case:checked").length) {
      $("#selectall").prop("checked", "checked");
    } else {
      $("#selectall").removeAttr("checked");
    }
  });
});

$("#assign_ad").click(function(event){

    event.preventDefault();

    var searchIDs = $("#assign_ad_div input:checkbox:checked").map(function(){
      return $(this).val();
    }).get(); 

    if(searchIDs.length == 0) {

      alert("Select any one of the Video and Assign ad");

      return false;

    } else {

      $("#video_tape_id").val(searchIDs.join(','));

      @if($ad_details->ad_type == POST_AD || $ad_details->ad_type == PRE_AD)

        $("#submit-btn").click();

      @endif

    }


});
</SCRIPT>

@endsection */ ?>