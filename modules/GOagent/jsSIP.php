<?php
namespace creamy;

ini_set('display_errors', 'on');
error_reporting(E_ALL);

$getSlashes = preg_match_all("/\//", $_SERVER['REQUEST_URI']);
$goModDIR = (!empty($_SERVER['REQUEST_URI']) && $getSlashes > 1) ? dirname($_SERVER['REQUEST_URI'])."/" : "/";
$goBaseDIR = str_replace("/modules/GOagent/", "", $goModDIR);
$goBaseURL = $_SERVER['HTTP_HOST'];
?>
<html>
	<head>
		<title>GOagent jsSIP</title>
		<script src="//<?="{$goBaseURL}{$goBaseDIR}"?>/js/jquery.min.js"></script>
		<script src="//<?="{$goBaseURL}{$goModDIR}"?>/js/jsSIP.js"></script>
		
		<script>
			var audioElement = document.querySelector('#remoteStream');
			var localStream;
			var remoteStream;
			
			var configuration = {
				'ws_servers': 'wss://webrtc.goautodial.com:44344',
				'uri': 'sip:38084@webrtc.goautodial.com',
				'password': 'G02x16',
				'session_timers': false,
				'connection_recovery_max_interval': 30,
				'connection_recovery_min_interval': 2,
			};
			
			var rtcninja = JsSIP.rtcninja;
			var phone = new JsSIP.UA(configuration);
			
			phone.on('connected', function(e) {
				console.log('connected', e);
			});
			
			phone.on('disconnected', function(e) {
				console.log('disconnected', e);
			});
			
			phone.on('newRTCSession', function(e) {
				var session = e.session;
				console.log('newRTCSession: originator', e.originator, 'session', e.session, 'request', e.request);
			
				session.on('peerconnection', function (data) {
					console.log('session::peerconnection', data);
				});
			
				session.on('iceconnectionstatechange', function (data) {
					console.log('session::iceconnectionstatechange', data);
				});
			
				session.on('connecting', function (data) {
					console.log('session::connecting', data);
				});
			
				session.on('sending', function (data) {
					console.log('session::sending', data);
				});
			
				session.on('progress', function (data) {
					console.log('session::progress', data);
				});
			
				session.on('accepted', function (data) {
					console.log('session::accepted', data);
				});
			
				session.on('confirmed', function (data) {
					console.log('session::confirmed', data);
				});
			
				session.on('ended', function (data) {
					console.log('session::ended', data);
				});
			
				session.on('failed', function (data) {
					console.log('session::failed', data);
				});
			
				session.on('addstream', function (data) {
					console.log('session::addstream', data);
			
					remoteStream = data.stream;
					audioElement = document.querySelector('#remoteStream');
					audioElement.src = window.URL.createObjectURL(remoteStream);
					
					$(document).on('keydown', function(event) {
						var keys = {
							48: '0', 49: '1', 50: '2', 51: '3', 52: '4', 53: '5', 54: '6', 55: '7', 56: '8', 57: '9'
						};
						
						if (keys[event.which] === undefined) {
							return;
						}
						
						console.log('keydown: '+keys[event.which], event);
						var options = {
							'duration': 160,
							'eventHandlers': {
								'succeeded': function(originator, response) {
									console.log('DTMF succeeded', originator, response);
								},
								'failed': function(originator, response, cause) {
									console.log('DTMF failed', originator, response, cause);
								},
							}
						};
						
						if (live_customer_call) {
							session.sendDTMF(keys[event.which], options);
						}
					});
				});
			
				session.on('removestream', function (data) {
					console.log('session::removestream', data);
				});
			
				session.on('newDTMF', function (data) {
					console.log('session::newDTMF', data);
				});
			
				session.on('hold', function (data) {
					console.log('session::hold', data);
				});
			
				session.on('unhold', function (data) {
					console.log('session::unhold', data);
				});
			
				session.on('muted', function (data) {
					console.log('session::muted', data);
				});
			
				session.on('unmuted', function (data) {
					console.log('session::unmuted', data);
				});
			
				session.on('reinvite', function (data) {
					console.log('session::reinvite', data);
				});
			
				session.on('update', function (data) {
					console.log('session::update', data);
				});
			
				session.on('refer', function (data) {
					console.log('session::refer', data);
				});
			
				session.on('replaces', function (data) {
					console.log('session::replaces', data);
				});
			
				session.on('sdp', function (data) {
					console.log('session::sdp', data);
				});
			
				session.answer({
					mediaConstraints: {
						audio: true,
						video: false
					},
					mediaStream: localStream
				});
			});
			
			phone.on('newMessage', function(e) {
				console.log('newMessage', e);
			});
			
			phone.on('registered', function(e) {
				//var xmlhttp = new XMLHttpRequest();
				var query = "";
				
				query += "SIP_user_DiaL=" + SIP_user_Dial;
				query += "&session_id=" + session_id;
				query += "&phone_login=" + phone_login;
				query += "&phone_pass=" + phone_pass;
				query += "&VD_campaign=" + campaign;
				query += "&enable_sipsak=" + enable_sipsak;
				query += "&campaign_cid=" + campaign_cid;
				query += "&on_hook_agent=" + on_hook_agent;
				
				console.log('registered', e);
				//xmlhttp.open('GET', 'originate.php?' + query); 
				//xmlhttp.send(null); 
				//xmlhttp.onreadystatechange = function() { 
				//	if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				//		console.log('reply!');
				//	}
				//};
			});
			
			phone.on('unregistered', function(e) {
				console.log('unregistered', e);
			});
			
			phone.on('registrationFailed', function(e) {
				console.log('registrationFailed', e);
			});
			
			rtcninja.getUserMedia({
				audio: true,
				video: false
			}, function successCb(stream) {
				localStream = stream;
			
				phone.start();
			}, function failureCb(e) {
				console.error('getUserMedia failed.', e);
			});
		</script>
	</head>
	<body>
		<audio id="remoteStream" autoplay controls></audio>
	</body>
</html>
