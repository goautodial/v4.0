<?php
$__user = $_REQUEST['__user'];

?>
<!doctype html>
<html>
	<head>
		<title>tryit-jssip</title>
		<meta charset='UTF-8'>
		<meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no'>

		<link rel='stylesheet' href='goautodial-jssip.css'>

		<script src='resources/js/antiglobal.js'></script>
		<script>
			// Set debug
			//window.localStorage.setItem('debug', '* -engine* -socket* *ERROR* *WARN*');

			// Set antiglobal
			//window.antiglobal('___browserSync___oldSocketIo', 'JSON3', 'io', '___browserSync___', '__core-js_shared__', 'MediaStream', 'RTCPeerConnection');
			//setInterval(window.antiglobal, 5000);
		</script>

		<script>
			// Uncomment and fill the SETTINGS object in order to use hardcoded settings
			//
			// You don't need to set display_name nor uri, they can be set via HTTP form in
			// the Login section of the web
			
			window.SETTINGS =
			{
			 	display_name        : '<?=$__user?>',
			 	uri                 : 'sip:8842641193@demo.goautodial.com',
			 	password            : 'g0g0g0',
			 	socket              :
			 	{
			 		uri           : 'wss://demo.goautodial.com:4443',
			 		via_transport : 'auto',
			 	},
			 	registrar_server    : null,
			 	contact_uri         : null,
			 	authorization_user  : null,
			 	instance_id         : null,
			 	session_timers      : true,
			 	use_preloaded_route : false,
			 	pcConfig            :
			 	{
			 		rtcpMuxPolicy : 'negotiate',
			 		iceServers    :
			 		[
			 			{ urls : [ 'stun:stun.l.google.com:19302' ] }
			 		]
			 	},
			 	callstats           :
			 	{
			 		enabled   : false,
			 		AppID     : null,
			 		AppSecret : null
			 	}
			};
		</script>

		<script src='goautodial-jssip.js'></script>
	</head>

	<body>
		<div id='tryit-jssip-container'></div>
		<div id='tryit-jssip-media-query-detector'></div>
	</body>
</html>
