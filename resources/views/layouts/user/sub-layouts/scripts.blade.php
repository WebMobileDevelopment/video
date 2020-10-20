
<script src="{{asset('streamtube/js/jquery.min.js')}}"></script>

<script src="{{asset('streamtube/js/bootstrap.min.js')}}"></script>

<script src="{{asset('assets/bootstrap/js/jquery-ui.js')}}"></script>

<script src="{{asset('admin-css/plugins/input-mask/jquery.inputmask.js')}}"></script>

<script type="text/javascript" src="{{asset('streamtube/js/jquery-migrate-1.2.1.min.js')}}"></script>

<script type="text/javascript" src="{{asset('streamtube/js/slick.min.js')}}"></script>

<script type="text/javascript" src="{{asset('streamtube/js/script.js')}}"></script>

<script src="{{asset('admin-css/plugins/select2/select2.full.min.js')}}"></script>

<!-- input Mask -->

<script src="{{asset('admin-css/plugins/input-mask/jquery.inputmask.js')}}"></script>

<script src="{{asset('admin-css/plugins/input-mask/jquery.inputmask.date.extensions.js')}}"></script>

<script src="{{asset('admin-css/plugins/input-mask/jquery.inputmask.extensions.js')}}"></script>    

<script src="{{asset('assets/js/jstz.min.js')}}"></script>

<script type="text/javascript">

    function updateTimezone() {

        var timezone = jstz.determine().name();

        $.post('{{ route("user.timezone.save")}}', {'timezone': timezone, 'is_json': 1})

        .done(function(response) {

            // $('#global-notifications-count').html(response.count);
            
        })
        .fail(function(response) {
            // console.log(response);
        })
        .always(function(response) {
            // console.log(response);
        });

    }

    $(window).load(function() {
        
        $('.placeholder').each(function () {
            var imagex = jQuery(this);
            var imgOriginal = imagex.data('src');
            $(imagex).attr('src', imgOriginal);
        });
        
        $('#preloader').fadeOut(2000);

    });

    $(document).ready(function() {

        //Initialize Select2 Elements

        $(".select2").select2();

        $("[data-mask]").inputmask();

        $('.video-list-slider').slick({
            dots: true,
            infinite: false,
            speed: 300,
            slidesToShow: 5,
            arrows: true,
            slidesToScroll: 5,
            responsive: [
                {
                    breakpoint: 1200,
                    settings: {
                        slidesToShow: 4,
                        slidesToScroll: 4,
                        infinite: true,
                        dots: true
                    }
                },
                {
                    breakpoint: 992,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 3,
                        infinite: true,
                        dots: true
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2
                    } 
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
                // You can unslick at a given breakpoint now by adding:
                // settings: "unslick"
                // instead of a settings object
              ]
        
        });

 
        $('.box').slick({
            dots: true,
            infinite: false,
            speed: 300,
            slidesToShow: 5,
            arrows: true,
            slidesToScroll: 5,
            responsive: [
                {
                    breakpoint: 1200,
                    settings: {
                        slidesToShow: 4,
                        slidesToScroll: 4,
                        infinite: true,
                        dots: true
                    }
                },
                {
                    breakpoint: 992,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 3,
                        infinite: true,
                        dots: true
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2
                    } 
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
                // You can unslick at a given breakpoint now by adding:
                // settings: "unslick"
                // instead of a settings object
              ]
        
        });
        @if(Auth::check())


        updateTimezone();

        $.post('{{ route("user.bell_notifications.index")}}', {'is_json': 1})

        .done(function(response) {

            if(response.success == false) {
                return false;
            }

            $('#global-notifications-count').html(response.data.length);
            
            $.each(response.data, function(key,notificationDetails) { 

                // console.log(JSON.stringify(notificationDetails));

                var global_notification_redirect_url = "/video/"+notificationDetails.video_tape_id;

                if(notificationDetails.notification_type == 'NEW_SUBSCRIBER') {

                    var global_notification_redirect_url = "/channel/"+notificationDetails.channel_id;

                }

                var messageTemplate = '';

                messageTemplate = '<li class="notification-box">';

                messageTemplate += '<a href="'+global_notification_redirect_url+'" target="_blank">';

                messageTemplate += '<div class="row">';

                messageTemplate +=  '<div class="col-lg-3 col-sm-3 col-3 text-center">';

                messageTemplate +=  '<img src="'+notificationDetails.picture+'" class="w-50 rounded-circle">';

                messageTemplate +=  '</div>';

                messageTemplate +=  '<div class="col-lg-8 col-sm-8 col-8">';

                // messageTemplate +=  '<strong class="text-info">'+notificationDetails+'</strong>';

                messageTemplate +=  '<div>';

                messageTemplate +=  notificationDetails.message;
                          
                messageTemplate +=  '</div>';

                messageTemplate +=  '<small class="text-warning">'+notificationDetails.created+'</small>';
                              
                messageTemplate +=  '</div>';

                messageTemplate +=  '</div>';

                messageTemplate +=  '</a>';

                messageTemplate +=  '</li>';
                
                $('#global-notifications-box').append(messageTemplate);
                
                // $(chatBox).animate({scrollTop: chatBox.scrollHeight}, 500);

            });

        })
        .fail(function(response) {
            console.log(response);
        })
        .always(function(response) {
            console.log(response);
        });

        function loadNotificationsCount() {
            
            $.post('{{ route("user.bell_notifications.count")}}', {'is_json': 1})

            .done(function(response) {

                $('#global-notifications-count').html(response.count);
                
            })
            .fail(function(response) {
                // console.log(response);
            })
            .always(function(response) {
                // console.log(response);
            });
  
        }

        setInterval(loadNotificationsCount, 10000);
    @endif
    });


    $(document).on("click", ".notification-link a", function(){ 

        $.post('{{ route("user.bell_notifications.update")}}', {'is_json': 1})

        .done(function(response) {

            //$('#global-notifications-count').html(response.count);
            return true;
            
        })
        .fail(function(response) {
            console.log(response);
        })
        .always(function(response) {
            console.log(response);
        });


    });

    jQuery(document).ready( function () {
        //autocomplete
        jQuery("#auto_complete_search").autocomplete({
            source: "{{route('search')}}",
            minLength: 1,
            select: function(event, ui){

                // set the value of the currently focused text box to the correct value

                if (event.type == "autocompleteselect"){
                    
                    // console.log( "logged correctly: " + ui.item.value );

                    var username = ui.item.value;

                    if(ui.item.value == 'View All') {

                        // console.log('View AALLLLLLLLL');

                        window.location.href = "{{route('search-all', array('q' => 'all'))}}";

                    } else {
                        // console.log("User Submit");

                        jQuery('#auto_complete_search').val(ui.item.value);

                        jQuery('#userSearch').submit();
                    }

                }                        
            }      // select

        }); 

        jQuery("#auto_complete_search_min").autocomplete({
            source: "{{route('search')}}",
            minLength: 1,
            select: function(event, ui){

                // set the value of the currently focused text box to the correct value

                if (event.type == "autocompleteselect"){
                    
                    // console.log( "logged correctly: " + ui.item.value );

                    var username = ui.item.value;

                    if(ui.item.value == 'View All') {

                        // console.log('View AALLLLLLLLL');

                        window.location.href = "{{route('search-all', array('q' => 'all'))}}";

                    } else {
                        // console.log("User Submit");

                        jQuery('#auto_complete_search_min').val(ui.item.value);

                        jQuery('#userSearch_min').submit();
                    }

                }                        
            }      // select

        }); 

    });

   function notificationsStatusUpdate(){

        $.post('{{ route("user.bell_notifications.count")}}', {'is_json': 1})

        .done(function(response) {

            var x = document.getElementById("viewAll");

            if (response.count == 0) {

                x.style.display = "none";
            } 
        })
        .fail(function(response) {
            // console.log(response);
        })
        .always(function(response) {
            // console.log(response);
        });
   }

</script>

@yield('scripts')

<script type="text/javascript">
    @if(isset($page))
        $("#{{$page}}").addClass("active");
    @endif
</script>

<?php echo Setting::get('body_scripts') ?: ""; ?>