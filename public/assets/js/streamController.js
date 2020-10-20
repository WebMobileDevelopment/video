liveAppCtrl
.controller('streamCtrl', ['$rootScope', '$window','$sce',
	function ($rootScope, $window,$sce) {

		$scope = $rootScope;

		function getBrowser() {

            // Opera 8.0+
            var isOpera = (!!window.opr && !!opr.addons) || !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;

            // Firefox 1.0+
            var isFirefox = typeof InstallTrigger !== 'undefined';

            // Safari 3.0+ "[object HTMLElementConstructor]" 
            var isSafari = /constructor/i.test(window.HTMLElement) || (function (p) { return p.toString() === "[object SafariRemoteNotification]"; })(!window['safari'] || safari.pushNotification);

            // Internet Explorer 6-11
            var isIE = /*@cc_on!@*/false || !!document.documentMode;

            // Edge 20+
            var isEdge = !isIE && !!window.StyleMedia;

            // Chrome 1+
            var isChrome = (!!window.chrome && !!window.chrome.webstore) || navigator.userAgent.indexOf("Chrome") !== -1;

            // Blink engine detection
            var isBlink = (isChrome || isOpera) && !!window.CSS;

            var b_n = '';

            switch(true) {

                case isFirefox :

                        b_n = "Firefox";

                        break;
                case isChrome :

                        b_n = "Chrome";

                        break;

                case isSafari :

                        b_n = "Safari";

                        break;
                case isOpera :

                        b_n = "Opera";

                        break;

                case isIE :

                        b_n = "IE";

                        break;

                case isEdge : 

                        b_n = "Edge";

                        break;

                case isBlink : 

                        b_n = "Blink";

                        break;

                default :

                        b_n = "Unknown";

                        break;

            }

            return b_n;

        }

        var mobile_type = "";

        function getMobileOperatingSystem() {

		  var userAgent = navigator.userAgent || navigator.vendor || window.opera;

		  if( userAgent.match( /iPad/i ) || userAgent.match( /iPhone/i ) || userAgent.match( /iPod/i ) )
		  {
		    mobile_type =  'ios';

		  }
		  else if( userAgent.match( /Android/i ) )
		  {

		    mobile_type =  'andriod';
		  }
		  else
		  {
		    mobile_type =  'unknown'; 
		  }

		  return mobile_type;
		
		}

        var browser = getBrowser();

        var m_type = getMobileOperatingSystem();

        $scope.redirectToLive = function(id, amount) {

			$state.go('restricted.join-video', {id : id}, {reload:true});
			
		}

        $scope.wowza_ip_address = wowza_ip_address;

        $scope.kurento_socket_url = kurento_socket_url;

        var is_kurento_running = 0;

        if (browser == 'Safari' || m_type =='ios') {

            console.log("kurento not supported..!");

        } else {

            if ($scope.kurento_socket_url) {

                is_kurento_running = 1;

                var ws = new WebSocket('wss://'+$scope.kurento_socket_url+'/rtprelay');

                console.log(ws);

                var videoInput;
                var videoOutput;
                var webRtcPeer;
                var state = null;
                var destinationIp;
                var destinationPort;
                var rtpSdp;

                console.log('Page loaded ...');
                videoInput = document.getElementById('videoInput');
                videoOutput = document.getElementById('videoOutput');
                rtpSdp = document.getElementById('rtpSdp');

                ws.onmessage = function(message) {
                    var parsedMessage = JSON.parse(message.data);
                    console.info('Received message: ' + message.data);

                    switch (parsedMessage.id) {
                    case 'startResponse':
                        startResponse(parsedMessage);
                        break;
                    case 'error':
                        onError('Error message from server: ' + parsedMessage.message);
                        break;
                    case 'iceCandidate':
                        webRtcPeer.addIceCandidate(parsedMessage.candidate)
                        break;
                    default:
                        onError('Unrecognized message', parsedMessage);
                    }
                }

                $scope.start = function() {
                    console.log('Starting video call ...')

                    // showSpinner(videoInput);

                    console.log('Creating WebRtcPeer and generating local sdp offer ...');

                    var options = {
                      localVideo: videoInput,
                      onicecandidate : onIceCandidate
                    }

                    webRtcPeer = kurentoUtils.WebRtcPeer.WebRtcPeerSendrecv(options, function(error) {
                        if(error) return onError(error);
                        this.generateOffer(onOffer);
                    });
                }

                function onIceCandidate(candidate) {
                       console.log('Local candidate' + JSON.stringify(candidate));

                       var message = {
                          id : 'onIceCandidate',
                          candidate : candidate
                       };
                       sendMessage(message);
                }

                function onOffer(error, offerSdp) {
                    if(error) return onError(error);

                    console.info('Invoking SDP offer callback function ' + location.host);
                    var message = {
                        id : 'start',
                        sdpOffer : offerSdp,
                        rtpSdp : rtpSdp.value
                    }
                    console.log("This is the offer sdp:");
                    console.log(offerSdp);
                    sendMessage(message);
                }

                function onError(error) {
                    console.error(error);
                }

                function startResponse(message) {
                    console.log('SDP answer received from server. Processing ...');
                    webRtcPeer.processAnswer(message.sdpAnswer);
                }

                $scope.stop = function() {
                    console.log('Stopping video call ...');
                    if (webRtcPeer) {
                        webRtcPeer.dispose();
                        webRtcPeer = null;

                        var message = {
                            id : 'stop'
                        }
                        sendMessage(message);
                    }
                    // hideSpinner(videoInput, videoOutput);
                }

                function sendMessage(message) {
                    var jsonMessage = JSON.stringify(message);
                    console.log('Senging message: ' + jsonMessage);
                    ws.send(jsonMessage);
                }

                /*function showSpinner() {
                    for (var i = 0; i < arguments.length; i++) {
                        arguments[i].poster = './img/transparent-1px.png';
                        arguments[i].style.background = 'center transparent url("./img/spinner.gif") no-repeat';
                    }
                }

                function hideSpinner() {
                    for (var i = 0; i < arguments.length; i++) {
                        arguments[i].src = '';
                        arguments[i].poster = './img/webrtc.png';
                        arguments[i].style.background = '';
                    }
                }*/

                function forceEvenRtpPort(rtpPort) {
                    if ((rtpPort > 0) && (rtpPort % 2 != 0))
                        return rtpPort - 1;
                    else return rtpPort;
                }

                function updateRtpSdp() {
                    var destination_ip;
                    var destination_port;

                    if (!destinationIp.value)
                        destination_ip= $scope.wowza_ip_address;
                    else
                        destination_ip = destinationIp.value.trim();

                    if (!destinationPort.value)
                        destination_port="33124";
                    else
                        destination_port = forceEvenRtpPort(destinationPort.value.trim());


                    destination_ip= $scope.wowza_ip_address;

                        rtpSdp.value = 'v=0\n'
                        + 'o=- 0 0 IN IP4 ' + destination_ip + '\n'
                        + 's=Kurento\n'
                        + 'c=IN IP4 ' + destination_ip + '\n'
                        + 't=0 0\n'
                        + 'm=video ' + destination_port + ' RTP/AVP 100\n'
                        + 'a=rtpmap:100 H264/90000\n';

                    console.log(rtpSdp.value);
                }



            }

        }


		// This function will call, when the streaming started by user
		$scope.live_status = function() {

			var data = new FormData;
			data.append('id', live_user_id);
			data.append('token', user_token);
			data.append('video_id', $scope.videoDetails.id);
			
			$.ajax({
	      		type : 'post',
	      		url : apiUrl+"userApi/streaming/status",
	      		contentType : false,
				processData: false,
				async : false,
				data : data,
				success : function(response) {

					if (response.success) {

					} else {

						UIkit.notify({message : response.error_messages,timeout:3000, status:'danger', position:'top-center'});

					}

				},
				error : function(response) {


				},
		  	});
		}

		window.enableAdapter = false; // enable adapter.js


        $scope.socket_url = socket_url;

       // alert($scope.socket_url);

        $scope.connectionNow= null;


        window.enableAdapter = true; // enable adapter.js

        // ......................................................
        // .......................UI Code........................
        // ......................................................
        $scope.openRoom = function() {
            // disableInputButtons();
            connection.open(document.getElementById('room-id').value, function() {
               // showRoomURL(connection.sessionid);

                if (is_kurento_running) {

                    $scope.start();

                }

                   $("#default_image").hide();

                   $("#loader_btn").hide();

                   $("#open-room").hide();

                   $("#stop-room").show();
            });
        };

        document.getElementById('join-room').onclick = function() {
            // disableInputButtons();

            $("#default_image").hide();

            connection.sdpConstraints.mandatory = {
                OfferToReceiveAudio: true,
                OfferToReceiveVideo: true
            };

            console.log("Room Id "+ document.getElementById('room-id').value);


            connection.join(document.getElementById('room-id').value);
        };

        document.getElementById('open-or-join-room').onclick = function() {
            disableInputButtons();
            connection.openOrJoin(document.getElementById('room-id').value, function(isRoomExist, roomid) {
                if (!isRoomExist) {
                   // showRoomURL(roomid);
                }
                else {
                    connection.sdpConstraints.mandatory = {
                        OfferToReceiveAudio: true,
                        OfferToReceiveVideo: true
                    };
                }
            });
        };

        $scope.socket_url = socket_url;

        // ......................................................
        // ..................RTCMultiConnection Code.............
        // ......................................................

        var connection = new RTCMultiConnection();

        // by default, socket.io server is assumed to be deployed on your own URL
        connection.socketURL = $scope.socket_url;

        // comment-out below line if you do not have your own socket.io server
        // connection.socketURL = 'https://rtcmulticonnection.herokuapp.com:443/';

        connection.socketMessageEvent = 'video-broadcast-demo';

        connection.session = {
            audio: true,
            video: true,
            oneway: true
        };

        connection.sdpConstraints.mandatory = {
            OfferToReceiveAudio: false,
            OfferToReceiveVideo: false
        };

        connection.videosContainer = document.getElementById('videos-container');

        var append_already = 0;

        connection.onstream = function(event) {
            event.mediaElement.removeAttribute('src');
            event.mediaElement.removeAttribute('srcObject');

            var video = document.createElement('video');
            video.controls = true;
            if(event.type === 'local') {
                video.muted = true;
            }
            video.srcObject = event.stream;

            var width = parseInt(connection.videosContainer.clientWidth / 2) - 20;
            var mediaElement = getHTMLMediaElement(video, {
                title: event.userid,
                buttons: ['full-screen'],
                width: width,
                showOnMouseEnter: false
            });

            if (append_already == 0) { 

                connection.videosContainer.appendChild(mediaElement);

                // if (browser == 'Safari' || m_type =='ios') {

                    append_already = 1;

                // }

            }

            setTimeout(function() {
                mediaElement.media.play();
            }, 5000);

            mediaElement.id = event.streamid;

             function takePhoto(video) {
                var canvas = document.createElement('canvas');
                canvas.width = video.videoWidth || video.clientWidth;
                canvas.height = video.videoHeight || video.clientHeight;

                var context = canvas.getContext('2d');
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                return canvas.toDataURL('image/png');
            }

            if (event.type == 'local') {

                var yourVideoElement = document.querySelector('video');

                var initNumber = 1;
                var capture = function capture() {
                    
                    var snapshot_pic = takePhoto(yourVideoElement);
                    
                    $.ajax({

                        type : 'post',
                        url : apiUrl+'/take_snapshot/'+video_details.id,
                        data : {base64: snapshot_pic,shotNumber: initNumber, 
                            id : live_user_id, token : user_token},
                        success : function(data) {
                            // console.log(data);
                        }

                    });
                  
                  initNumber = initNumber < 6 ? initNumber + 1 : 1;

                  timeout = setTimeout(capture, 120 * 1000);

                };

                window.setTimeout(function(){

                    capture();

                }, 6000);

            }
        };

        connection.onstreamended = function(event) {
            var mediaElement = document.getElementById(event.streamid);
            if (mediaElement) {
                mediaElement.parentNode.removeChild(mediaElement);
            }

            connection.attachStreams.forEach(function (stream) {
                stream.stop();
            });

            connection.videosContainer.innerHTML = '';

            connection.autoCloseEntireSession = true;
             
           // $scope.connectionNow.close();
                        
            window.setTimeout(function(){

                // alert("Streaming stopped unfortunately..!");

                window.location.reload(true);

            }, 2000);
        };

        function disableInputButtons() {
            document.getElementById('open-or-join-room').disabled = true;
          //  document.getElementById('open-room').disabled = true;
            document.getElementById('join-room').disabled = true;
            document.getElementById('room-id').disabled = true;
        }

        // ......................................................
        // ......................Handling Room-ID................
        // ......................................................

        (function() {
            var params = {},
                r = /([^&=]+)=?([^&]*)/g;

            function d(s) {
                return decodeURIComponent(s.replace(/\+/g, ' '));
            }
            var match, search = window.location.search;
            while (match = r.exec(search.substring(1)))
                params[d(match[1])] = d(match[2]);
            window.params = params;
        })();

        var roomid = '';

        roomid = $scope.videoDetails.virtual_id;

        if (roomid == '') {

            if (localStorage.getItem(connection.socketMessageEvent)) {

                roomid = localStorage.getItem(connection.socketMessageEvent);

            } else {
                roomid = connection.token();
            }

        }

        document.getElementById('room-id').value = roomid;
        document.getElementById('room-id').onkeyup = function() {
            localStorage.setItem(connection.socketMessageEvent, this.value);
        };


       

        if (video_details.user_id == live_user_id) {

            console.log("room...");

            $scope.openRoom();

        } else {

            if(video_details.video_url != null && video_details.video_url != '') {

                console.log(video_details.video_url);

                if (jwplayer_key == '' && video_details.video_url) {

                    alert("Configure Jwplayer Key, Kindly contact Admin");

                    return false;
                }

                jwplayer.key = jwplayer_key;

                var playerInstance1 = jwplayer("videos-container");

                $("#loader_btn").hide();

                playerInstance1.setup({

                   file : $sce.trustAsResourceUrl(video_details.video_url),
                    width: "100%",
                    aspectratio: "16:9",
                    primary: "flash",
                    controls : true,
                    "controlbar.idlehide" : false,
                    controlBarMode:'floating',
                    "controls": {
                      "enableFullscreen": false,
                      "enablePlay": false,
                      "enablePause": false,
                      "enableMute": true,
                      "enableVolume": true
                    },
                    autostart : true,
                   /* "sharing": {
                        "sites": ["reddit","facebook","twitter"]
                      }*/
                });

                playerInstance1.on('error', function(e) {

                    console.log("setupError1");

                    console.log(e);

                    $("#videos-container").hide();

                    $("#loader_btn").hide();

                    var hasFlash = false;
                    
                   try {
                        var fo = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
                        if (fo) {
                            hasFlash = true;
                        }
                    } catch (e) {
                        if (navigator.mimeTypes
                                && navigator.mimeTypes['application/x-shockwave-flash'] != undefined
                                && navigator.mimeTypes['application/x-shockwave-flash'].enabledPlugin) {
                            hasFlash = true;
                        }
                    }

                    console.log(hasFlash == false);

                    $('#main_video_setup_error').css('display', 'block');

                    if (hasFlash == false) {

                        $("#flash_error_display").show();  

                        $('#main_video_setup_error').show();      
                    }

                   
                   alert("Video Setup Error...!");

                    //$state.go('guest.home', {}, {reload : true});

                });


                playerInstance1.on('setupError', function(e) {

                    console.log("setupError");

                    console.log(e);

                    $("#videos-container").hide();

                    $("#loader_btn").hide();

                    var hasFlash = false;
                   try {
                        var fo = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
                        if (fo) {
                            hasFlash = true;
                        }
                    } catch (e) {
                        if (navigator.mimeTypes
                                && navigator.mimeTypes['application/x-shockwave-flash'] != undefined
                                && navigator.mimeTypes['application/x-shockwave-flash'].enabledPlugin) {
                            hasFlash = true;
                        }
                    }

                    $('#main_video_setup_error').show();

                    if (hasFlash == false) {
                        
                        $("#flash_error_display").show();  

                        $('#main_video_setup_error').show();      
                    }


                    alert("Video Setup Error...!");

                    //$state.go('guest.home', {}, {reload : true});
                
                });

                $("#loader_btn").hide();

            } else {

                //alert("Joining Room");

                console.log("Join Room...");

                $("#join-room").click();

            }
        }

		$scope.stopStreaming = function(video_id) {

			if (confirm('Do you want to stop streaming ?')) {

				var data = new FormData;
				data.append('id', live_user_id);
				data.append('token',user_token);
				data.append('video_id', video_details.id);
                data.append('device_type', 'web');
				
				$.ajax({

		      		type : 'post',
		      		url : apiUrl+"userApi/close_streaming",
		      		contentType : false,
					processData: false,
					async : false,
					data : data,
		      		success : function(data) {

				    	connection.attachStreams.forEach(function (stream) {
					        stream.stop();
					    });

					    connection.videosContainer.innerHTML = '';

					    connection.autoCloseEntireSession = true;
					     
					    $scope.connectionNow.close();

                        if (is_kurento_running) {

                            ws.close();

                            $scope.stop();
                        }

		      			UIkit.notify({message : 'Your streaming has been ended successfully.', status : 'success', timeout:5000, pos : 'top-center'});

		      			$state.go('static.home', {}, {reload : true});
		      		}

		      	});

			}

		}

    }

])

.controller('chatBarCtrl', ['$scope', '$http', '$rootScope', '$window',
	function ($scope, $http, $rootScope, $window) {


			console.log('chat');

			console.log(chat_socket_url);


			var appSettings = $scope.appSettings;

	        var defaultImage = "";

	        var chatBox = document.getElementById('chat-box'); // Chat Box container

	        var chatInput = document.getElementById('chat-input'); // User Typed text in input
	 
	        var chatSend = document.getElementById('chat-send'); // Send Box 


	        var liveVideoID = $scope.videoDetails.id;

	        var liveVideoViewerID = (appSettings.USER == null) ? live_user_id : 0;

	        var userID = (appSettings.USER != null) ?  appSettings.USER.id : 0;

	        var userToViewer ="uv";

	        var viewerToUser ="vu";

	        // let's assume that the client page, once rendered, knows what room it wants to join

	        var room = $scope.videoDetails.unique_id; // Room will be video ID

	        // set-up a connection between the client and the server

	        var socket = io(chat_socket_url ,  { secure: true , query: "room="+room});

	        var socketState = false;

	        // The socket state will be enable, once the socket is connected

	        socket.on('connected', function (data) {

	            socketState = true;

	            // Enable chat input box

                if (live_user_id != '' && live_user_id != undefined) {

	               chatInput.enable();

                }
	        });



            socket.on('stream-stop', function(video_id) {

                alert("Streaming Stopped..!");

                window.location.reload(true);

            });

            socket.on('video-streaming-status', function(no_of_views) {

                    $("#viewers_cnt").html(no_of_views);

                

            });

                                    // console.log(result);

            var viewer_cnt = video_details.viewer_cnt;

     
            window.setTimeout(function(){

                console.log("viewer_cnt "+viewer_cnt);

                socket.emit('check-video-streaming', viewer_cnt);

            }, 5 * 1000);

                
	        socket.on('message', function(data) {


	           if(data.message){

	                $('#chat-box').append(messageTemplate(data));

	                // $("#chat-box").animate({ scrollTop: $('#chat-box').prop("scrollHeight")}, 300);
	                // $('#chat-box').scrollTop($('#chat-box').height());
	                // $('#chat_box_scroll').scrollTop($('#chat_box_scroll')[0].scrollHeight);
	                $('.chat_box_scroll').scrollTop($('.chat_box_scroll')[0].scrollHeight);
	            }

	        });

	        socket.on('disconnect', function (data) {
	            socketState = false;
	            chatInput.disable();
	           //  console.log('Disconnected from server');
	        });

            if (live_user_id != '' && live_user_id != undefined) {

    	        chatInput.enable = function() {
    	            this.disabled = false;
    	        };



    	        chatInput.clear = function() {
    	            this.value = "";
    	        };

    	        chatInput.disable = function() {
    	            this.disabled = true;
    	        };

    	        chatInput.addEventListener("keyup", function (e) {

    	            if (e.which == 13) {
    	                sendMessage(chatInput);
    	                return false;
    	            }
    	        });

    	        // User Click send message , this function will trigger

    	        chatSend.addEventListener('click', function() {
    	            sendMessage(chatInput);
    	        });

            }

	        function sendMessage(input) {

	            chatMessage = input.value.trim();

	            if(socketState && chatMessage != '') {
	                message = {};
	                message.type = (appSettings.USER == null)  ? viewerToUser : userToViewer;
	                message.live_video_viewer_id = liveVideoViewerID;
	                message.live_video_id = liveVideoID;
	                message.user_id = userID;
	                message.profile_id = (appSettings.USER == null) ? live_user_id : appSettings.USER.id;
	                message.room = room;
	                message.message = chatMessage;
	                // message.created_at = appSettings.created_at;
	                // message.username = $scope.videoDetails.name;
	                message.userpicture = appSettings.USER_PICTURE;
	                message.username = appSettings.NAME;
	                message.class = appSettings.CLASS;

	                // The user send message display to other users

	                updateMessageToOthers(message);

	                // socketClient.sendMessage(text);

	                $('#chat-box').append(messageTemplate(message));


	                // $("#chat-box").animate({ scrollTop: $('#chat-box').prop("scrollHeight")}, 300);

	                chatInput.clear();

	                $('.chat_box_scroll').scrollTop($('.chat_box_scroll')[0].scrollHeight);
	                
	                /*$(chatBox).animate({
	                    scrollTop: chatBox.scrollHeight,
	                }, 500);*/
	            
	            }
	        
	        }

	        // The user send message display to other users

	        function updateMessageToOthers(data) {

	            socket.emit('message', data); 
	        }

	        // Message Template

	        // <small class="pull-right text-muted"> <span class="glyphicon glyphicon-time"></span>12 mins ago</small>

	        function messageTemplate(data) {

	        	// <small class="text-muted pull-right">'+data.created_at+'</small>

	            var messageTemplate = '';

	            // if (data.class == 'left') {
		            messageTemplate = '<div class="item">';
		            messageTemplate += '<div class="col-lg-2 col-md-2 col-xs-2 col-sm-2" style="padding: 0">';
		            messageTemplate += '<a target="_blank" href="'+apiUrl+'/profile?id='+data.profile_id+'"><img class="chat_img" src="'+data.userpicture+'" alt="'+data.username+'"></a>';
		            messageTemplate += '</div>';
		            messageTemplate += '<div class="message col-lg-10 col-md-10 col-xs-10 col-sm-10">';
		            messageTemplate += '<a target="_blank" href="'+apiUrl+'/profile?id='+data.profile_id+'" class="clearfix"><small class="text-muted pull-left">'+data.username+'</small></a>';
		            messageTemplate += ' <div>'+data.message+'</div>';
		            messageTemplate += '</div>';
		            messageTemplate += '<div class="clearfix"></div>';
		            messageTemplate += '</div>';
		        // }

		         /*else {
		        	messageTemplate = '<li class="'+data.class+' clearfix">';
		            messageTemplate += '<span class="chat-img pull-right">';
		            messageTemplate += '<img style="width: 60px;height: 60px;" src="'+data.userpicture+'" alt="'+data.username+'" class="img-circle">';
		            messageTemplate += '</span>';
		            messageTemplate += '<div class="chat-body clearfix">';
		            messageTemplate += ' <div class="header"> ';
		            messageTemplate += ' <strong class="pull-right primary-font">'+data.username+'</strong>';
		            messageTemplate += '</div>';
		            messageTemplate += ' <p>'+data.message+'</p>';
		            messageTemplate += '</div>';
		            messageTemplate += '</li>';
		        }
	*/
	            return messageTemplate;

	        }

	    
    

    }

    ]);
