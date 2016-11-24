<?php

	/** Carrier API - Add carrier */
	
	require_once('goCRMAPISettings.php');

	$url = gourl."/goCarriers/goAPI.php"; #URL to GoAutoDial API. (required)
	$postfields["goUser"]	= goUser; #Username goes here. (required)
	$postfields["goPass"]	= goPass; #Password goes here. (required)
	$postfields["goAction"]	= "goAddCarrier"; #action performed by the [[API:Functions]]. (required)
	$postfields["responsetype"] 	= responsetype; #json. (required)
	$postfields["hostname"]	= $_SERVER['REMOTE_ADDR']; #Default value
	$postfields["carrier_id"]	= $_POST['carrier_id']; #Desired uniqueid. (required)
	$postfields["carrier_name"]	= $_POST['carrier_name'];
	$postfields["active"]	= $_POST['active'];
	$postfields["carrier_type"]	= $_POST['carrier_type'];
	
	if($_POST['carrier_type'] == "manual"){
		$postfields["carrier_description"]	= $_POST['carrier_description'];
		$postfields["user_group"]	= $_POST['user_group'];
		$postfields["authentication"]	= $_POST['authentication'];
			
		if($_POST['authentication'] == "registration"){
			$postfields["username"]	= $_POST['username'];
			$postfields["password"]	= $_POST['password'];
		}
		
		$postfields["sip_server_ip"]	= $_POST['sip_server_ip'];
		$postfields["codecs"]	= $_POST['codecs'];
		$postfields["dtmf"]	= $_POST['dtmf'];
		$postfields["custom_dtmf"]	= $_POST['custom_dtmf'];
		$postfields["protocol"]	= $_POST['protocol'];
		$postfields["manual_server_ip"]	= $_POST['manual_server_ip'];
	
	}
	
	if($_POST['carrier_type'] == "manual"){
		$postfields["copy_server_ip"]	= $_POST['copy_server_ip'];
		$postfields["source_carrier"]	= $_POST['source_carrier'];
	}
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	//curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	$data = curl_exec($ch);
	curl_close($ch);
	$output = json_decode($data);
	
	if ($output->result=="success") {
			# Result was OK!
			$status = 1;
			//$return['msg'] = "New User has been successfully saved.";
	} else {
			# An error occured
			//$status = 0;
			// $return['msg'] = "Something went wrong please see input data on form.";
	$status = $output->result;
	}

	echo  $status;
?>