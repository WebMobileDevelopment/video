angular.module('liveApp')
.controller('androidCtrl', ['$rootScope', '$window', '$sce',
	function ($rootScope, $window, $sce) {

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

        // This function will call, when the streaming started by user
        $scope.live_status = function() {

            var data = new FormData;
            data.append('id', live_user_id);
            data.append('token', user_token);
            data.append('video_id', video_details.id);
            
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

                        alert(response.error_messages);

                    }

                },
                error : function(response) {


                },
            });
        }


        $scope.socket_url = socket_url;

       // alert($scope.socket_url);

        $scope.connectionNow= null;


		window.enableAdapter = true; // enable adapter.js

        // ......................................................
        // .......................UI Code........................
        // ......................................................
        $scope.openRoom = function(deviceId) {
            disableInputButtons();

            connection.attachStreams.forEach(function(stream) {
                stream.stop();
            });

            connection.mediaConstraints = {
                audio: true,
                video: {deviceId : deviceId},
            };

            connection.open(document.getElementById('room-id').value, function() {
               // showRoomURL(connection.sessionid);

               $("#cameras-selection-container").show();

            });
        };

        document.getElementById('join-room').onclick = function() {
            disableInputButtons();

            connection.sdpConstraints.mandatory = {
                OfferToReceiveAudio: true,
                OfferToReceiveVideo: true
            };
            connection.join(document.getElementById('room-id').value);
        };

        $scope.cameras = [];

        var currentDeviceId = "";

        $scope.checkAndOpenRoom = function() {

            let deviceId = "";

            $scope.cameras.forEach(camera => {

                if (currentDeviceId == camera.deviceId) {


                } else {

                    deviceId = camera.deviceId;

                }

            });

            currentDeviceId = deviceId;

            $scope.openRoom(currentDeviceId);
            
        }


        $scope.switch_cameras = function() {

            /*********************Switch camera*************/

            var cameras = [];

            DetectRTC.load(() => {
                DetectRTC.videoInputDevices.forEach(function(camera) {

                    cameras.push({deviceId : camera.deviceId, label : camera.label});
                
                });
                
            });


            setTimeout(()=>{

                $scope.cameras = cameras;

                currentDeviceId = $scope.cameras[0].deviceId;

                $scope.openRoom($scope.cameras[0].deviceId);

            }, 1000);

            /**********************Switch camera***************/
            
        }

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
        connection.onstream = function(event) {
            event.mediaElement.removeAttribute('src');
            event.mediaElement.removeAttribute('srcObject');

            var video = document.createElement('video');
            video.controls = false;
            if(event.type === 'local') {
                video.muted = true;
            }
            video.srcObject = event.stream;

            var width = parseInt(connection.videosContainer.clientWidth / 2) - 20;
            var mediaElement = getHTMLMediaElement(video, {
                title: event.userid,
                buttons: [],
                width: width,
                showOnMouseEnter: false
            });

            connection.videosContainer.appendChild(mediaElement);

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

                }, 6 * 1000);

            }
        };

        connection.onstreamended = function(event) {
            var mediaElement = document.getElementById(event.streamid);
            if (mediaElement) {
                mediaElement.parentNode.removeChild(mediaElement);
            }

            setTimeout(() => {

                console.log("Streaming Ended timeout.");

                if (video_details.user_id != live_user_id) {

                    window.location.reload(true);

                }

               // this.router.reload();
                // window.location.reload(true);

            }, 2000);

           /* window.setTimeout(function(){

                alert("Streaming stopped unfortunately..!");

            }, 2000);*/
        };

        

        function disableInputButtons() {
            //document.getElementById('open-or-join-room').disabled = true;
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

            setTimeout(function() {

                $scope.switch_cameras();
                
            }, 1000);

        } else {

            //alert("Joining Room");

            console.log("Join Room...");

            if(video_details.video_url != null && video_details.video_url != '') {

                console.log("video_url "+video_details.video_url);

            } else {

                $("#join-room").click();

            }
        }

        /*if (roomid && roomid.length) {
            document.getElementById('room-id').value = roomid;
            localStorage.setItem(connection.socketMessageEvent, roomid);

            // auto-join-room
            (function reCheckRoomPresence() {
                connection.checkPresence(roomid, function(isRoomExist) {
                    if (isRoomExist) {
                        connection.sdpConstraints.mandatory = {
                            OfferToReceiveAudio: true,
                            OfferToReceiveVideo: true
                        };
                        connection.join(roomid);
                        return;
                    }

                    setTimeout(reCheckRoomPresence, 5000);
                });
            })();

            disableInputButtons();
        }*/

        $scope.stopStreaming = function(video_id) {

            if (confirm('Do you want to stop streaming ?')) {

                var data = new FormData;
                data.append('id', memoryStorage.user_id);
                data.append('token', memoryStorage.access_token);
                data.append('video_id', video_id);
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

                        alert('Your streaming has been ended successfully.');

                    }

                });

            }

        }
	}
]);

