liveAppCtrl

.factory('commonHelper', function($location) {
	return {
		stringRepeat: function(num, replace) {
			return new Array(num + 1).join(replace);
		},
		externalLinks:function(text){
		return String(text).replace(/href=/gm, "class=\"ex-link\" href=");
		
		},
		localStorageIsEnabled: function() {
			var uid = new Date(),
							result;

			try {
				localStorage.setItem("uid", uid);
				result = localStorage.getItem("uid") === uid;
				localStorage.removeItem("uid");
				return result && localStorage;
			} catch (e) {
			}
		},
		readJsonFromController: function(file) {
			var request = new XMLHttpRequest();
			request.open('GET', file, false);
			request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
			request.send(null);
			try {
				return JSON.parse(request.responseText);
			} catch (e) {
				return '';
			}
		},
		getBadWords: function(input) {
			if (input) {
				var badwords = [];
				for (var i = 0; i < swearwords.length; i++) {
					var swear = new RegExp(swearwords[i], 'g');
					if (input.match(swear)) {
						badwords.push(swearwords[i]);
					}
				}
				return badwords;
			}
		},
		replaceBadWords: function(input) {
			if (this.localStorageIsEnabled()) {
				if (localStorage.getItem('localSwears') === null) {
					// stringify the array so that it can be stored in local storage
					localStorage.setItem('localSwears', JSON.stringify(readJsonFromController(swearWordPath)));
				}
				swearwords = JSON.parse(localStorage.getItem('localSwears'));
			} else {
				swearwords = this.readJsonFromController(swearWordPath);
			}
			if (swearwords === null) {
				return input;
			}
			if (input) {
				for (var i = 0; i < swearwords.length; i++) {
					var swear =  new RegExp('\\b' + swearwords[i] + '\\b', 'gi');
					if (input.match(swear)) {
						var replacement = this.stringRepeat(swearwords[i].length, "*");
						input = input.replace(swear, replacement);
					}
				}
				return input;
			} else {
				return input;
			}
		},
		obToquery: function(obj, prefix) {
			var str = [];
			for (var p in obj) {
				var k = prefix ? prefix + "[" + p + "]" : p,
								v = obj[k];
				str.push(angular.isObject(v) ? this.obToquery(v, k) : (k) + "=" + encodeURIComponent(v));
			}
			return str.join("&");
		},
		isExpired: function(object) {
			if (!object.expiresOn) {
				return false;
			}
			if (new Date(object.expiresOn).getTime() < new Date().getTime() && object.expiresOn) {
				return true;
			}
			return false;
		},
		scrollTo: function(element, to, duration) {
			if (duration < 0)
				return;
			var difference = to - element.scrollTop;
			var perTick = difference / duration * 10;

			setTimeout(function() {
				element.scrollTop = element.scrollTop + perTick;
				if (element.scrollTop == to)
					return;
				scrollTo(element, to, duration - 10);
			}, 10);
		},
		removeLastSpace: function(str) {
			return str.replace(/\s+$/, '');
		},
		numberToAlpha: function(data) {
			var string = '';
			switch (data) {
				case '0':
					string = 'A';
					break;
				case '1':
					string = 'B';
					break;
				case '2':
					string = 'C';
					break;
				case '3':
					string = 'D';
					break;
				case '4':
					string = 'F';
					break;
			}
			return string;
		},
		secondsToDateTime: function(second, type) {
			var string = '';

			var date = this.coverMilisecondToTime(second * 1000, 'minute');
			string = date.seconds + ' second' + date.secondsS;
			if (date.minutes > 0) {
				string = date.minutes + ' min' + date.minutesS + ' ' + string;
			}
			return string;
			// return;
		},
		coverMilisecondToTime: function(millis, type, options) {
			var seconds = 0;
			var minutes = 0;
			var hours = 0;
			var days = 0;
			var months = 0;
			var years = 0;
			if (type === 'day') {
				seconds = Math.round((millis / 1000) % 60);
				minutes = Math.floor(((millis / (60000)) % 60));
				hours = Math.floor(((millis / (3600000)) % 24));
				days = Math.floor(((millis / (3600000)) / 24));
				months = 0;
				years = 0;
			} else if (type === 'second') {
				seconds = Math.floor(millis / 1000);
				minutes = 0;
				hours = 0;
				days = 0;
				months = 0;
				years = 0;
			} else if (type === 'minute') {
				if (options && options.fixed) {
					seconds = (millis / 1000).toFixed(options.fixed);
				} else {
					seconds = Math.round((millis / 1000) % 60);
				}
				minutes = Math.floor(millis / 60000);
				hours = 0;
				days = 0;
				months = 0;
				years = 0;
			} else if (type === 'hour') {
				seconds = Math.round((millis / 1000) % 60);
				minutes = Math.floor(((millis / (60000)) % 60));
				hours = Math.floor(millis / 3600000);
				days = 0;
				months = 0;
				years = 0;
			} else if (type === 'month') {
				seconds = Math.round((millis / 1000) % 60);
				minutes = Math.floor(((millis / (60000)) % 60));
				hours = Math.floor(((millis / (3600000)) % 24));
				days = Math.floor(((millis / (3600000)) / 24) % 30);
				months = Math.floor(((millis / (3600000)) / 24) / 30);
				years = 0;
			} else if (type === 'year') {
				seconds = Math.round((millis / 1000) % 60);
				minutes = Math.floor(((millis / (60000)) % 60));
				hours = Math.floor(((millis / (3600000)) % 24));
				days = Math.floor(((millis / (3600000)) / 24) % 30);
				months = Math.floor(((millis / (3600000)) / 24 / 30) % 12);
				years = Math.floor((millis / (3600000)) / 24 / 365);
			}
			var secondsS = (seconds < 2) ? '' : 's';
			var minutesS = (minutes < 2) ? '' : 's';
			var hoursS = (hours < 2) ? '' : 's';
			var daysS = (days < 2) ? '' : 's';
			var monthsS = (months < 2) ? '' : 's';
			var yearsS = (years < 2) ? '' : 's';
			return {
				seconds: seconds,
				secondsS: secondsS,
				minutes: minutes,
				minutesS: minutesS,
				hours: hours,
				hoursS: hoursS,
				days: days,
				daysS: daysS,
				months: months,
				monthsS: monthsS,
				years: years,
				yearsS: yearsS
			};


		}
	};
}
)
.controller('androidCtrl', ['$rootScope', '$window', 'socketFactory','commonHelper', '$sce',
	function ($rootScope, $window, socketFactory, commonHelper, $sce) {

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
            var isChrome = !!window.chrome && !!window.chrome.webstore;

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

       
		var socket = {};

		// console.log("Before "+socket);

		$scope.socketrun  = function() {

				/*if (appSettings == undefined) {

					$.ajax({
						type : 'post',
						url : apiUrl+'appSettings/'+$stateParams.id,
						contentType : false,
						processData: false,
						async : false,
						data : {},
						success : function(result) {

							// console.log("result "+result);

							memoryStorage.appSettings = result;

						},
						
				    	error : function(result) {

				    	}
					});

				}*/

				// console.log(memoryStorage.appSettings);
			
				//var appSettings = JSON.parse(appSettings);

					 console.log($scope.appSettings);

					  // appSettings = JSON.parse($rootScope.appSettings);

					  // socket.io now auto-configures its connection when we ommit a connection url
					  var ioSocket = io($scope.appSettings.SOCKET_URL, {
						    // Send auth token on connection, you will need to DI the Auth service above
					   	 	'query': commonHelper.obToquery({ token: $scope.appSettings.TOKEN }),
					    	path: '/socket.io-client'
					  });

					  var socket = socketFactory({ ioSocket: ioSocket });

					  socket.on('another-model-connected', function () {

					    //       var cookies = document.cookie.split(";");
					    //       console.log(cookies);
					    //       for(var i=0; i < cookies.length; i++) {
					    //         var equals = cookies[i].indexOf("=");
					    //         var name = equals > -1 ? cookies[i].substr(0, equals) : cookies[i];
					    //         document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
					    //       }
					    //call logout to force remove http flag
					    alert('You are connecting in another session. exit now!');
					    // $window.location.href = appSettings.BASE_URL + 'models/dashboard/profile';
					  });

				  return {
				    socket: socket,

				    /**
				    * send send-tip event to server
				    */
				    sendTip: function sendTip(data) {
				      socket.emit('send-tip', data);
				    },


				    /**
				     * Event for send tip callback
				     */
				    onReceiveTip: function onReceiveTip(cb) {
				      cb = cb || angular.noop;
				      socket.on('send-tip', cb);
				    },


				    /**
				     * new member join to room
				     */

				    joinRoom: function joinRoom(data) {
				      socket.emit('join-room', data);
				    },

				    onLeaveRoom: function onLeaveRoom(cb) {
				      cb = cb || angular.noop;

				      socket.on('leave-room', cb);
				    },
				    onMemberJoin: function onMemberJoin(cb) {
				      cb = cb || angular.noop;
				      //who
				      //total members...
				      //{ member: 2134, .... }
				      socket.on('join-room', cb);
				    },

				    //event get list models online
				    onModelOnline: function onModelOnline(cb) {
				      cb = cb || angular.noop;
				      socket.on('model-online', cb);
				    },

				    //event check current model online
				    getCurrentModelOnline: function getCurrentModelOnline(roomId) {
				      socket.emit('current-model-online', roomId);
				    },

				    //event get current model of room online
				    onCurrentModelOnline: function onCurrentModelOnline(cb) {
				      cb = cb || angular.noop;
				      socket.on('current-model-online', cb);
				    },

				    getModelStreaming: function getModelStreaming(roomId, modelId) {
				      socket.emit('model-streaming', { room: roomId, model: modelId });
				    },

				    /**
				     * notify with model when they receive new tokens
				     */
				    sendModelReceiveInfo: function sendModelReceiveInfo(tokens) {
				      socket.emit('model-receive-info', tokens);
				    },

				    /**
				     * model receive message
				     */
				    onModelReceiveInfo: function onModelReceiveInfo(cb) {
				      cb = cb || angular.noop();
				      socket.on('model-receive-info', cb);
				    },
				    onModelStreaming: function onModelStreaming(cb) {
				      cb = cb || angular.noop;
				      //who
				      //total members...
				      //{ member: 2134, .... }
				      socket.on('model-streaming', cb);
				    },
				    on: function on(event, cb) {
				      socket.on(event, cb);
				    },
				    emit: function emit(event, data, cb) {
				      socket.emit(event, data, cb);
				    }
				  };
		}

		socket = $scope.socketrun();


		// using single socket for RTCMultiConnection signaling
		var onMessageCallbacks = {};

		$scope.isOffline = false;
		$scope.roomId = null;
		$scope.virtualRoom = null;

		$scope.streamingInfo = {
			spendTokens: 0,
			time: 0,
			tokensReceive: 0,
			type: 'public',
			hasRoom: true
		};

		socket.on('broadcast-message', function (data) {
		if (data.sender == connection.userid) {
		  return;
		}
		if (onMessageCallbacks[data.channel]) {
		  onMessageCallbacks[data.channel](data.message);
		}
		});


		socket.on('public-room-status', function (status) {
		if (!status) {
		  $('#videos-container').removeClass('loader');
		  $('#offline-image').show();
		  $scope.isOffline = true;
		} else {
		  $('#videos-container').addClass('loader');
		  $('#offline-image').hide();
		  $scope.isPrivateChat = false;
		  $scope.isGroupLive = false;
		  $scope.isOffline = false;
		}
		});

		$scope.isShowPrivateMessage = false;

		$scope.connectionNow = null;

		socket.on('disconnect', function (data) {




		});

		socket.on('disconnectAll', function (data) {
			console.log("disconectAll");
		if (appSettings.CHAT_ROOM_ID != data.id && data.ownerId == appSettings.USER.id) {
			// console.log("disconect");
		  
		}
		});

		
		// initializing RTCMultiConnection constructor.
		$scope.isStreaming = null;

		function initRTCMultiConnection(userid) {

			var connection = new RTCMultiConnection();

			$scope.connectionNow = connection;

			// memoryStorage.connectionNow = $scope.connectionNow;

			//localStorage.setItem('sessionStorage', JSON.stringify(memoryStorage));	


			connection.body = document.getElementById('videos-container');
			connection.channel = connection.sessionid = connection.userid = userid || connection.userid;

			connection.sdpConstraints.mandatory = {
			  OfferToReceiveAudio: true,
			  OfferToReceiveVideo: true
			};

			// using socket.io for signaling
			connection.openSignalingChannel = function (config) {
			  var channel = config.channel || this.channel;
			  onMessageCallbacks[channel] = config.onmessage;
			  if (config.onopen) {
			    setTimeout(config.onopen, 1000);
			  }

			  return {
			    send: function send(message) {
			      socket.emit('broadcast-message', {
			        sender: connection.userid,
			        channel: channel,
			        message: message
			      });
			    },
			    channel: channel
			  };
			};
			connection.onMediaError = function (error) {
			  //              JSON.stringify(error)
			  alertify.alert('Warning', error.message);
			};

			//fix echo
			connection.onstream = function (event) {
			  if (event.mediaElement) {
			    event.mediaElement.muted = true;
			    delete event.mediaElement;
			  }

			  var video = document.createElement('video');
			  if (event.type === 'local') {
			    video.muted = true;
			  }

			  video.srcObject = event.stream;

			  console.log("Video Src "+video.src);

			  connection.videosContainer.appendChild(video);

			  console.log("StreamId "+event.streamid);

			  console.log(event);

			 


			};

			//disable log
			connection.enableLogs = false;

			return connection;
		}

		var timeout = null;

		// this RTCMultiConnection object is used to connect with existing users
		var connection = initRTCMultiConnection();

		//get other TURN server
		//TODO - config our turn server
		var setupConnection = function setupConnection() {

		connection.getExternalIceServers = true;

		connection.onstream = function (event) {

			console.log("Stream Id "+event.streamid);

			console.log(URL.createObjectURL(event.stream));

			console.log(event);




		  if (event.type == 'local') {

		  	if(is_vod == 1) {

		  		alert(is_vod);

		  		connection.streams[event.streamid].startRecording({ 
			        video: true ,
			        audio:true,
			    });

		  	}

		    var initNumber = 1;


		    console.log("capture image");

		    var capture = function capture() {


		    	console.log("Inside capture image");

		    	console.log(event.userid);

		      connection.takeSnapshot(event.userid, function (snapshot) {

		      	console.log("url "+snapshot);
		      	console.log("url "+url);

		      	$.ajax({

		      		type : 'post',
		      		url : url+'/take_snapshot/'+$scope.videoDetails.id,
		      		data : {base64: snapshot,shotNumber: initNumber},
		      		success : function(data) {
		      			
		      		}

		      	});

		      	$scope.viewerscnt = 0;

		      	$scope.minutes = 0;

		      
		   	 });

		     initNumber = initNumber < 6 ? initNumber + 1 : 1;

		     timeout = setTimeout(capture, 30000);

	
		    };

		    capture();

		    $scope.$on('destroy', function () {
		      clearTimeout(timeout);
		    });
		  }
		  //      event.mediaElement.controls = false;
		 // console.log("Media Element "+event.mediaElement);


		  $("#default_image").hide();
		  $("#loader_btn").hide();
		  
		  $scope.open = true;

		  connection.body.appendChild(event.mediaElement);

		 /* $.ajax({
				type : 'post',
				url : apiUrl+"live_streaming/"+$stateParams.id,
				data : {id : memoryStorage.user_id, token : memoryStorage.access_token},
				success : function(result) {*/

					$scope.open = true;

					$scope.displayStop = true;

		/*		}

		 });*/

		  if (connection.isInitiator == false && !connection.broadcastingConnection) {
		    $scope.isStreaming = true;
		    // "connection.broadcastingConnection" global-level object is used
		    // instead of using a closure object, i.e. "privateConnection"
		    // because sometimes out of browser-specific bugs, browser
		    // can emit "onaddstream" event even if remote user didn't attach any stream.
		    // such bugs happen often in chrome.
		    // "connection.broadcastingConnection" prevents multiple initializations.

		    // if current user is broadcast viewer
		    // he should create a separate RTCMultiConnection object as well.
		    // because node.js server can allot him other viewers for
		    // remote-stream-broadcasting.
		    // connection.userid = 1;
		    connection.broadcastingConnection = initRTCMultiConnection(connection.userid);

		    // to fix unexpected chrome/firefox bugs out of sendrecv/sendonly/etc. issues.
		    connection.broadcastingConnection.onstream = function () {};

		    connection.broadcastingConnection.session = connection.session;
		    connection.broadcastingConnection.attachStreams.push(event.stream); // broadcast remote stream
		    connection.broadcastingConnection.dontCaptureUserMedia = true;

		    // forwarder should always use this!
		    connection.broadcastingConnection.sdpConstraints.mandatory = {
		      OfferToReceiveVideo: false,
		      OfferToReceiveAudio: false
		    };

		    connection.broadcastingConnection.open({
		      dontTransmit: true
		    });
		    $('#offline-image').hide();
		    $('#videos-container').removeClass('loader');
		  }
		};
		};
		setupConnection();


		$scope.initRoom = function (roomId, virtualRoom) {
			$scope.roomId = roomId;
			$scope.virtualRoom = virtualRoom;

			//get model streaming
			socket.emit('join-broadcast', {
			  broadcastid: $scope.virtualRoom,
			  room: $scope.roomId,
			  userid: connection.userid,
			  openBroadcast: false,
			  typeOfStreams: {
			    video: false,
			    screen: false,
			    audio: false,
			    oneway: true
			  }
			});
		};

		// $("#loader_btn").show();

				// ask node.js server to look for a broadcast
		// if broadcast is available, simply join it. i.e. "join-broadcaster" event should be emitted.
		// if broadcast is absent, simply create it. i.e. "start-broadcasting" event should be fired.
		// TODO - model side should start broadcasting and member/client side should join only
		$scope.openBroadcast = function (room, virtualRoom) {

			console.log("Open Broadcast");

			$scope.roomId = room;
			$scope.virtualRoom = virtualRoom;


				connection.session = {
			    video: true,
	            screen: false,
	            audio: true,
	            oneway: true
	          };

	          socket.emit('join-broadcast', {
	            broadcastid: $scope.virtualRoom,
	            room: $scope.roomId,
	            userid: connection.userid,
	            typeOfStreams: connection.session,
	            openBroadcast: true
	          });

	          $scope.isStreaming = true;

	          $scope.open = false;

	          $("#loader_btn").show();

	        
			
		}





		$("#videos-container").show();

		$scope.user_id = live_user_id;

		console.log($scope.user_id );

		console.log($scope.videoDetails.user_id)

		if ($scope.user_id != $scope.videoDetails.user_id) {
			// $("#default_image").hide();
			$("#loader_btn").hide();

		} else {

			$scope.openBroadcast($scope.videoDetails.id, $scope.videoDetails.virtual_id);

		}

		

		$scope.initRoom($scope.videoDetails.id, $scope.videoDetails.virtual_id);

						

		/**
		* join broadcast directly, use for member side
		*/

		$scope.joinBroadcast = function (room, virtualRoom) {
			//check model is online / streaming then open broadcast.
			socket.emit('has-broadcast', virtualRoom, function (has) {

			  if (!has) {
			    //TODO - should show nice alert message
			    $('#offline-image').show();
			    //       $scope.isOffline = true;
			    $('#videos-container').removeClass('loader');
			    return;
			  }
			  $scope.isPrivateChat = false;
			  $scope.isGroupLive = false;
			  $scope.isOffline = false;

			  $scope.roomId = room;
			  $scope.virtualRoom = virtualRoom;
			  //TODO - check model room is open or not first?
			  connection.session = {
			    video: true,
			    screen: false,
			    audio: true,
			    oneway: true
			  };
			  socket.emit('join-broadcast', {
			    broadcastid: $scope.virtualRoom,
			    room: $scope.roomId,
			    userid: connection.userid,
			    typeOfStreams: connection.session
			  });
			});
		};

		// this event is emitted when a broadcast is already created.
		socket.on('join-broadcaster', function (broadcaster, typeOfStreams) {

			connection.session = typeOfStreams;
			connection.channel = connection.sessionid = broadcaster.userid;


			connection.sdpConstraints.mandatory = {
			  OfferToReceiveVideo: !!connection.session.video,
			  OfferToReceiveAudio: !!connection.session.audio
			};

			connection.join({
			  sessionid: broadcaster.userid,
			  userid: broadcaster.userid,
			  extra: {},
			  session: connection.session
			});
		});

		// this event is emitted when a broadcast is absent.
		socket.on('start-broadcasting', function (typeOfStreams) {
			 // console.log('model start broadcast');
			// host i.e. sender should always use this!
			connection.sdpConstraints.mandatory = {
			  OfferToReceiveVideo: false,
			  OfferToReceiveAudio: false
			};
			connection.session = typeOfStreams;
			connection.open({
			  dontTransmit: true
			});

			if (connection.broadcastingConnection) {
			  // if new person is given the initiation/host/moderation control
			  connection.close();
			  connection.broadcastingConnection = null;
			}
		});

		socket.on('model-left', function () {
			//close connect if model live
			connection.close();
			connection.broadcastingConnection = null;

			console.log("connection close");

			// alert("diconn model");
		});

		socket.on('broadcast-error', function (data) {

			// console.log(data);
			if (!appSettings.USER || appSettings.USER.role != 'model') {
			  // alert('Warning', data.msg);
			}
			alert("Broadcast Error");

		});

		//rejoin event
		socket.on('rejoin-broadcast', function (data) {

			connection = initRTCMultiConnection();
			setupConnection();

			socket.emit('join-broadcast', {
			  broadcastid: data.id,
			  room: data.room,
			  userid: connection.userid,
			  typeOfStreams: connection.typeOfStreams
			});
		});



	    $scope.$on('destroy', function () {


	      	clearTimeout(viewerCount);

	    });

	    connection.onstreamended = function (e) {

	    	// alert("streamid"+e.streamid);

	    	if (is_vod == 1) {

		    	connection.streams[e.streamid].stopRecording(function (blob) {
				   // var mediaElement = document.createElement('audio'); 

				   	var blob_url = URL.createObjectURL(blob.video);

				   // alert(URL.createObjectURL(blob.video)); 

				    console.log(blob.video);

					/*var myFile = new File(blob.video);

					console.log(myFile);*/

					var xhr = new XMLHttpRequest;
					xhr.responseType = 'blob';

					xhr.onload = function() {
					   var recoveredBlob = xhr.response;

					   var reader = new FileReader;

					   reader.onload = function() {

					     	var blobAsDataUrl = reader.result;
					     // window.location = blobAsDataUrl;

					     	// console.log(blobAsDataUrl);

						    var data = new FormData();
							//data.append('blob_url', myFile);
							data.append('id', live_user_id);
							data.append('video_blob', blobAsDataUrl);
							data.append('token', user_token);
							data.append('video_id', $scope.videoDetails.id);

							$.ajax({
								type : 'post',
								url : url+'/userApi/save_vod',
								contentType : false,
								processData: false,
								
								async : false,
								data : data,
								success : function(result) {

									console.log(result);

									$scope.stopStreaming();

									alert("Video streaming Stopped");
									
								}, 
						    	error : function(result) {

						    	}
							});
						};

					   reader.readAsDataURL(recoveredBlob);
					};
					xhr.open('GET', blob_url);
					xhr.send();
				    
				});

	    	} else {

	    		$scope.stopStreaming();

	    		alert('streaming stopped');
	    		
	    	}

	    }

	    $scope.stopStreaming = function() {

	    	$rootScope.$emit('model_leave_room');

	    	// stop all local media streams
			connection.streams.stop('local');

			// stop all remote media streams
			connection.streams.stop('remote');

			// stop all media streams
			connection.streams.stop();

			alert("Stop streaming");

	    	
	    };

		

	}
]);