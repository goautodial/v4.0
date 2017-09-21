<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
	/** LeadRecycling API - Add lead recycling */
	
	require_once('goCRMAPISettings.php');
	require_once('Session.php');

	$url = gourl."/goLeadRecycling/goAPI.php"; #URL to GoAutoDial API. (required)
	$postfields["goUser"]	= goUser; #Username goes here. (required)
	$postfields["goPass"]	= goPass; #Password goes here. (required)
	$postfields["goAction"]	= "goAddLeadRecycling"; #action performed by the [[API:Functions]]. (required)
	$postfields["responsetype"] = responsetype; #json. (required)
	$postfields["log_ip"]	= $_SERVER['REMOTE_ADDR'];
	$postfields["session_user"] = $_SESSION['user'];
	
	$postfields["campaign_id"] 		= $_POST['leadrecycling_campaign'];
	$postfields["status"] 			= $_POST['leadrecycling_status']; 
	$postfields["attempt_delay"] 	= $_POST['attempt_delay'];
	$postfields["active"] 			= $_POST['active'];
	$postfields["attempt_maximum"] 	= $_POST['attempt_maximum'];
	
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
	$output = json_decode($data);
	
	// if ($output->result=="success") {
	// 	$status = 1;
	// } else {
	// 	$status = $output->data;
	// }

	echo $output->result;
?>