<?php
use  Carbon\Carbon;
?>
@extends('layouts.admin')

@section('title', tr('dashboard'))

@section('content-header', tr('dashboard'))

@section('breadcrumb')
    <li class="active"><i class="fa fa-dashboard"></i> {{tr('dashboard')}}</a></li>
@endsection

<style type="text/css">
  .center-card{
    	width: 30% !important;
	}
  .small-box .icon {
    top: 0px !important;
  }
</style>

@section('content')

	<div class="row">

		<!-- Total Users -->

		<div class="col-lg-3 col-xs-6">
          	<div class="small-box bg-green">
            	<div class="inner">
              		<h3>{{$user_count}}</h3>
              		<p>{{tr('total_users')}}</p>
            	</div>
            	
            	<div class="icon">
              		<i class="fa fa-user"></i>
            	</div>

            	<a href="{{route('admin.users.index')}}" class="small-box-footer">
              		{{tr('more_info')}}
              		<i class="fa fa-arrow-circle-right"></i>
            	</a>
          	</div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-red">
                <div class="inner">
                  <h3>{{$channel_count}}</h3>
                  <p>{{tr('total_channels')}}</p>
                </div>
              
                <div class="icon">
                  <i class="fa fa-tv"></i>
                </div>

                <a href="{{route('admin.channels.index')}}" class="small-box-footer">
                  {{tr('more_info')}}
                  <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>        
        </div> 

        <div class="col-lg-3 col-xs-6">
          	<div class="small-box bg-yellow">
            	<div class="inner">
              		<h3>{{$video_count}}</h3>
              		<p>{{tr('total_videos')}}</p>
            	</div>
            	
            	<div class="icon">
              		<i class="fa fa-video-camera"></i>
            	</div>

            	<a href="{{route('admin.video_tapes.index')}}" class="small-box-footer">
              		{{tr('more_info')}}
              		<i class="fa fa-arrow-circle-right"></i>
            	</a>
          	</div>        
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box label-primary">
                <div class="inner">
                    <h3>$ {{($total_revenue > 0 && $total_revenue != '') ? $total_revenue : 0}}</h3>
                    <p>{{tr('total_revenue')}}</p>
                </div>
                
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>

                <a href="{{route('admin.revenues.dashboard')}}" class="small-box-footer">
                    {{tr('more_info')}}
                    <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>        
        </div>

	</div>

    <div class="row">

        <div class="col-md-8">

            <div class="box">

                <div class="box-header with-border">                    
                    <h3 class="box-title">{{tr('daily_view_count')}}</h3>

                    <div class="box-tools pull-right">                       
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>                        
                    </div>
                </div>

                <!-- /.box-header -->

                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p class="text-center">
                                <strong>{{tr('last_10_days')}}</strong>
                            </p>

                            <div class="chart">
                                <!-- Sales Chart Canvas -->
                                <canvas id="dailyChart" style="height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- /.row -->                
                </div>
                <!-- ./box-body -->                
                <!-- /.box-footer -->
            </div>
            <!-- /.box -->
        </div>

        <div class="col-md-4">

            <div class="box">

                <div class="box-header with-border">                    
                    <h3 class="box-title">{{tr('registered_users')}}</h3>

                    <div class="box-tools pull-right">                       
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>

                <!-- /.box-header -->

                <div class="box-body">

                    <div class="row">
                        <div class="col-md-12">
                            <p class="text-center">
                                <strong></strong>
                            </p>
                            
                            <div class="chart-responsive">
                                <canvas id="registerChart" height="200px"></canvas>
                            </div>
                        </div>
                    </div>
                
                </div>

                <div class="box-footer no-padding">

                    <ul class="nav nav-pills nav-stacked">
                        <li>
                            <a>
                                <strong class="text-red">{{tr('total_web')}}</strong>
                                <span class="pull-right text-red">
                                    <i class="fa fa-angle-right"></i> {{$get_registers['web']}}
                                </span>
                            </a>
                        </li>

                        <li>
                            <a>
                                <strong class="text-green">{{tr('total_android')}} </strong>
                                <span class="pull-right text-green">
                                    <i class="fa fa-angle-right"></i> {{$get_registers['android']}}
                                </span>
                            </a>
                        </li>

                        <li>
                            <a>
                                <strong class="text-yellow">{{tr('total_ios')}}</strong>
                                <span class="pull-right text-yellow">
                                    <i class="fa fa-angle-right"></i> {{$get_registers['ios']}}
                                </span>
                            </a>
                        </li>
                    </ul>

                </div>

            </div>     

        </div>

        <!-- /.col -->
    </div>

    <div class="row">
    
        @if(count($recent_users) > 0)

        <div class="col-md-6">
              <!-- USERS LIST -->
            <div class="box box-danger">

                <div class="box-header with-border">
                    <h3 class="box-title">{{tr('latest_users')}}</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>

                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <ul class="users-list clearfix">
                        @foreach($recent_users as $user_details)

                            <li>
                                <img style="width:60px;height:60px" src="@if($user_details->picture) {{$user_details->picture}} @else {{asset('placeholder.png')}} @endif" alt="User Image">
                                <a class="users-list-name" href="{{route('admin.users.view' , ['user_id' => $user_details->id] )}}" target="_blank">{{$user_details->name}}</a>
                                <span class="users-list-date">
                                {{$user_details->created_at->diffForHumans()}}</span>
                            </li>

                        @endforeach
                    </ul>
                  <!-- /.users-list -->
                </div>
                <!-- /.box-body -->

                <div class="box-footer text-center">
                    <a href="{{route('admin.users.index')}}" class="uppercase">{{tr('view_all')}}</a>
                </div>

                <!-- /.box-footer -->
            </div>

              <!--/.box -->
        </div>

        @endif

        @if(count($recent_videos) > 0)
            <div class="col-md-6">
                <div class="box box-primary">

                    <div class="box-header with-border">
                        <h3 class="box-title">{{tr('recent_videos')}}</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                        </div>

                    </div>

                    <!-- /.box-header -->
                    <div class="box-body">

                        <ul class="products-list product-list-in-box">
                            @foreach($recent_videos as $v => $video_details)
                                @if($v < 5)
                                    <li class="item">
                                        <div class="product-img">
                                            <img src="{{$video_details->default_image}}" alt="Product Image">
                                        </div>
                                        <div class="product-info">
                                            <a href="{{route('admin.video_tapes.view' , ['video_tape_id' => $video_details->video_tape_id] )}}" class="product-title">{{substr($video_details->title, 0,50)}}
                                                <span class="label label-warning pull-right">{{$video_details->duration}}</span>
                                            </a>
                                            <span class="product-description text-black" >
                                              <?= substr($video_details->description , 0 , 70) ?>
                                            </span>
                                      </div>
                                    </li>

                                @endif

                                <div class="clearfix"></div>
                            @endforeach
                            <!-- /.item -->
                        </ul>
                    </div>

                    <!-- /.box-body -->
                    <div class="box-footer text-center">
                        <a href="{{route('admin.video_tapes.index')}}" class="uppercase">{{tr('view_all')}}</a>
                    </div>
                    <!-- /.box-footer -->
                </div>
            </div>
        @endif

    </div>


@endsection


@section('scripts')

<script type="text/javascript">
    
//-------------
  //- PIE CHART -
  //-------------
  // Get context with jQuery - using jQuery's .get() method.
  var pieChartCanvas = $("#registerChart").get(0).getContext("2d");
  var pieChart = new Chart(pieChartCanvas);
  var PieData = [
    {
      value: {{$get_registers['web']}},
      color: "#f56954",
      highlight: "#f56954",
      label: "Web"
    },
    {
      value: {{$get_registers['android']}},
      color: "#00a65a",
      highlight: "#00a65a",
      label: "Andorid"
    },
    {
      value: {{$get_registers['ios']}},
      color: "#f39c12",
      highlight: "#f39c12",
      label: "iOS"
    }
  ];
  var pieOptions = {
    //Boolean - Whether we should show a stroke on each segment
    segmentShowStroke: true,
    //String - The colour of each segment stroke
    segmentStrokeColor: "#fff",
    //Number - The width of each segment stroke
    segmentStrokeWidth: 1,
    //Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 50, // This is 0 for Pie charts
    //Number - Amount of animation steps
    animationSteps: 100,
    //String - Animation easing effect
    animationEasing: "easeOutBounce",
    //Boolean - Whether we animate the rotation of the Doughnut
    animateRotate: true,
    //Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale: false,
    //Boolean - whether to make the chart responsive to window resizing
    responsive: true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: false,
    //String - A legend template
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
    //String - A tooltip template
    tooltipTemplate: "<%=value %> <%=label%> users"
  };
  //Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  pieChart.Doughnut(PieData, pieOptions);
  //-----------------
  //- END PIE CHART -
  //-----------------

   //-----------------------
  //- MONTHLY SALES CHART -
  //-----------------------

  // Get context with jQuery - using jQuery's .get() method.
  var salesChartCanvas = $("#dailyChart").get(0).getContext("2d");
  // This will get the first returned node in the jQuery collection.
  var salesChart = new Chart(salesChartCanvas);

  var salesChartData = {
    labels: [<?php foreach($view['get'] as $date) { echo '"'.date('d M', strtotime($date->created_at)).'",';} ?>],
    datasets: [
      {
        label: "Electronics",
        fillColor: "rgb(210, 214, 222)",
        strokeColor: "rgb(210, 214, 222)",
        pointColor: "rgb(210, 214, 222)",
        pointStrokeColor: "#c1c7d1",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgb(220,220,220)",
        data: [<?php foreach($view['get'] as $count) { echo $count->count.',';} ?>]
      }
    ]
  };

  var salesChartOptions = {
    //Boolean - If we should show the scale at all
    showScale: true,
    //Boolean - Whether grid lines are shown across the chart
    scaleShowGridLines: false,
    //String - Colour of the grid lines
    scaleGridLineColor: "rgba(0,0,0,.05)",
    //Number - Width of the grid lines
    scaleGridLineWidth: 1,
    //Boolean - Whether to show horizontal lines (except X axis)
    scaleShowHorizontalLines: true,
    //Boolean - Whether to show vertical lines (except Y axis)
    scaleShowVerticalLines: true,
    //Boolean - Whether the line is curved between points
    bezierCurve: true,
    //Number - Tension of the bezier curve between points
    bezierCurveTension: 0.3,
    //Boolean - Whether to show a dot for each point
    pointDot: false,
    //Number - Radius of each point dot in pixels
    pointDotRadius: 4,
    //Number - Pixel width of point dot stroke
    pointDotStrokeWidth: 1,
    //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
    pointHitDetectionRadius: 20,
    //Boolean - Whether to show a stroke for datasets
    datasetStroke: true,
    //Number - Pixel width of dataset stroke
    datasetStrokeWidth: 2,
    //Boolean - Whether to fill the dataset with a color
    datasetFill: true,
    //String - A legend template
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%=datasets[i].label%></li><%}%></ul>",
    //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: true,
    //Boolean - whether to make the chart responsive to window resizing
    responsive: true
  };

  //Create the line chart
  salesChart.Line(salesChartData, salesChartOptions);

  //---------------------------
  //- END MONTHLY SALES CHART -
  //---------------------------
</script>

@endsection