@extends('layouts.user')

@section('styles')

@endsection

@section('content')

<div class="y-content">
        
    <div class="row content-row">

        @include('layouts.user.nav')

		<div class="page-inner col-sm-9 col-md-10">

			@include('notification.notify')

			<div class="sub-history">
				<h3 class="no-margin text-left">{{tr('notifications')}} ({{count($notifications)}})</h3>
			</div>

            @if(count($notifications) > 0)

				<div class="row col-md-12">

					@foreach($notifications as $notification_details)

						<div class="notification-list-content">

				            <a href="{{$notification_details->notification_redirect_url}}" target="_blank">
				                <div class="row">
				                    <div class="col-lg-1 col-sm-3 col-1 text-center">
				                        <img src="{{$notification_details->picture}}" class="w-50 rounded-circle">
				                    </div>

				                    <div class="col-lg-10 col-sm-8 col-10">
				                        <!-- <strong class="text-info">David John</strong> -->
				                        <div>
				                            {{$notification_details->message}}
				                        </div>
				                        <small class="text-warning">{{ common_date($notification_details->created) }}</small>
				                    </div>
				                </div>

				            </a>

				        </div>
			        
			        @endforeach

			        <span id="bell_notifications_list"></span>

			        <div class="row" style="margin-top: 20px">

			            <div id="notification_content_loader" style="display: none;">

			                <h1 class="text-center"><i class="fa fa-spinner fa-spin" style="color:#ff0000"></i></h1>

			            </div>

			            <div class="clearfix"></div>

			            <button class="pull-right btn btn-info mb-15" onclick="getNotificationList()" style="color: #fff">{{tr('view_more')}}</button>

			            <div class="clearfix"></div>

		            </div>

		        </div>

		    @else

		    	<img src="{{asset('images/no-result.jpg')}}" class="img-responsive auto-margin">

		    @endif

		</div>

   	</div>

</div>

@endsection

@section('scripts')

<script>

    var stopPageScroll = false;

    var searchDataLength = "{{count($notifications)}}";

	function getNotificationList() {

        if (searchDataLength > 0) {

            notifications_list(searchDataLength);

        }
    }

	function notifications_list(cnt) {

        $.ajax({

            type: "post",
            async: false,
            url: "{{route('user.bell_notifications.index')}}",
            data: {
                skip: cnt,
                is_json: 1
            },

            beforeSend: function() {

                $("#notification_content_loader").fadeIn();
            },

            success: function(response) {

            	$.each(response.data, function(key,notificationDetails) { 

	                // console.log(JSON.stringify(notificationDetails));

	                var global_notification_redirect_url = "/video/"+notificationDetails.video_tape_id;

	                if(notificationDetails.notification_type == 'NEW_SUBSCRIBER') {

	                    var global_notification_redirect_url = "/channel/"+notificationDetails.channel_id;

	                }

	                var messageTemplate = '';

	                messageTemplate = '<div class="notification-list-content">';

	                messageTemplate += '<a href="'+global_notification_redirect_url+'" target="_blank">';

	                messageTemplate += '<div class="row">';

	                messageTemplate +=  '<div class="col-lg-1 col-sm-3 col-1 text-center">';

	                messageTemplate +=  '<img src="'+notificationDetails.picture+'" class="w-50 rounded-circle">';

	                messageTemplate +=  '</div>';

	                messageTemplate +=  '<div class="col-lg-10 col-sm-10 col-10">';

	                // messageTemplate +=  '<strong class="text-info">'+notificationDetails+'</strong>';

	                messageTemplate +=  '<div>';

	                messageTemplate +=  notificationDetails.message;
	                          
	                messageTemplate +=  '</div>';

	                messageTemplate +=  '<small class="text-warning">27.11.2015, 15:00</small>';
	                              
	                messageTemplate +=  '</div>';

	                messageTemplate +=  '</div>';

	                messageTemplate +=  '</a>';

	                messageTemplate +=  '</div>';
	                
	                $('#bell_notifications_list').append(messageTemplate);

	            });

                if (response.data.length == 0) {

                    stopPageScroll = true;

                } else {

                    stopPageScroll = false;

                    searchDataLength = parseInt(searchDataLength) + response.data.length;

                }

            },

            complete: function() {

                $("#notification_content_loader").fadeOut();

            },

            error: function(data) {

            },

        });

    }

</script>

@endsection