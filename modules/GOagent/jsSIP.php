<?php
$display_name = $_REQUEST['display_name'];
$phone_login = $_REQUEST['phone_login'];
$phone_pass = $_REQUEST['phone_this'];
$websocketSIP = $_REQUEST['wsSIP'];
$websocketURL = $_REQUEST['wsURL'];
$websocketPORT = $_REQUEST['wsPort'];
$websocketSIP = "sip:{$phone_login}@{$websocketSIP}";
$websocketURI = "wss://{$websocketURL}:{$websocketPORT}";
$moduleURL = "https://".$_SERVER['SERVER_NAME']."/";
?>
<!doctype html>

<html>
	<head>
		<title>tryit-jssip</title>
		<meta charset='UTF-8'>
		<meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no'>

		<link rel='stylesheet' href='<?=$moduleURL?>modules/GOagent/css/jssip.GOautodial.css'>

		<script src='<?=$moduleURL?>modules/GOagent/js/resources/js/antiglobal.js'></script>
		<script>
			// Set debug
			//window.localStorage.setItem('debug', '* -engine* -socket* *ERROR* *WARN*');

			// Set antiglobal
			//window.antiglobal('___browserSync___oldSocketIo', 'JSON3', 'io', '___browserSync___', '__core-js_shared__', 'MediaStream', 'RTCPeerConnection');
			//setInterval(window.antiglobal, 5000);
		</script>

		<script>
			window.SETTINGS =
			{
				display_name        : '<?=$display_name?>',
				uri                 : '<?=$websocketSIP?>',
				password			: '<?=$phone_pass?>',
				socket              :
				{
					uri           : '<?=$websocketURI?>',
					via_transport : 'auto',
				},
				registrar_server    : '<?=$websocketURL?>',
				contact_uri         : null,
				authorization_user  : null,
				instance_id         : null,
				session_timers      : false,
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

		<script src='<?=$moduleURL?>modules/GOagent/js/jssip.GOautodial.js'></script>
	</head>

	<body>
		<div id='GOautodial-jssip-container'></div>
		<div id='GOautodial-jssip-media-query-detector'></div>
	</body>
</html>
