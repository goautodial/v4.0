<?php

    require_once('goCRMAPISettings.php');
	
    $url = gourl."/goBarging/goAPI.php"; #URL to GoAutoDial API. (required)
	$postfields["goUser"] = goUser; #Username goes here. (required)
	$postfields["goPass"] = goPass; #Password goes here. (required)
	$postfields["goAction"] = "goMonitorAgent"; #action performed by the [[API:Functions]]. (required)
	$postfields["responsetype"] = responsetype; #json. (required)
	$postfields["goAgent"] = $_POST['goAgent'];
	$postfields["goPhoneLogin"] = $_POST['goPhoneLogin'];
	$postfields["goSource"] = $_POST['goSource'];
	$postfields["goFunction"] = $_POST['goFunction'];
	$postfields["goSessionID"] = $_POST['goSessionID'];
	$postfields["goServerIP"] = $_POST['goServerIP'];
	$postfields["goStage"] = $_POST['goStage'];
	$postfields["goUserIP"] = $_POST['goUserIP'];
    
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	//curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$data = curl_exec($ch);
	curl_close($ch);
	
	echo $data;
?>