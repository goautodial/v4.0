<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
	/** Carrier API - Add carrier */
	
	require_once('goCRMAPISettings.php');

	$url = gourl."/goCarriers/goAPI.php"; #URL to GoAutoDial API. (required)
	$postfields["goUser"]	= goUser; #Username goes here. (required)
	$postfields["goPass"]	= goPass; #Password goes here. (required)
	$postfields["goAction"]	= "goAddCarrier"; #action performed by the [[API:Functions]]. (required)
	$postfields["responsetype"] 	= responsetype; #json. (required)
	$postfields["hostname"]	= $_SERVER['REMOTE_ADDR'];
	$postfields["log_user"] = $_POST['log_user'];
	$postfields["log_group"] = $_POST['log_group'];
	
	$postfields["carrier_type"]	= $_POST['carrier_type'];
	$postfields["carrier_id"]	= $_POST['carrier_id']; 
	$postfields["carrier_name"]	= $_POST['carrier_name'];
	$postfields["active"]	= $_POST['active'];
	$postfields["protocol"]	= $_POST['protocol'];
	$carrier_id = $_POST['carrier_id'];
	
	if($_POST['carrier_type'] == "manual"){
		$postfields["carrier_description"]	= $_POST['carrier_description'];
		$postfields["user_group"]	= $_POST['user_group'];
		$postfields["authentication"]	= $_POST['authentication'];
		if($postfields["authentication"] == "auth_reg"){
			$postfields["username"]	= $_POST['username'];
			$postfields["password"]	= $_POST['password'];
			$postfields["reg_host"]	= $_POST['reg_host'];
			$postfields["reg_port"]	= $_POST['reg_port'];
		}
		if($postfields["authentication"] == "auth_ip"){
			$postfields["sip_server_ip"]	= $_POST['sip_server_ip'];
		}
		$codecs = implode("&", $_POST['codecs']);
		$postfields["codecs"]	= $codecs;
		$postfields["dtmf"]	= $_POST['dtmf'];
			
		if(isset($_POST['custom_dtmf']))	
		    $postfields["custom_dtmf"]	= $_POST['custom_dtmf'];
		
		$postfields["dialprefix"]	= $_POST['dialprefix'];
		
		if($_POST['protocol'] == "CUSTOM"){
			$postfields["cust_protocol"]	= $_POST['cust_protocol'];
			$postfields["registration_string"]	= $_POST['registration_string'];
			$postfields["account_entry"]	= $_POST['account_entry'];
			$postfields["global_string"]	= $_POST['globals_string'];
			$postfields["dialplan_entry"]	= $_POST['dialplan_entry'];
		}else{
			$postfields["protocol"]	= $_POST['protocol'];
		}
		
		$postfields["manual_server_ip"]	= $_POST['server_ip'];
	}
	
	if($_POST['carrier_type'] == "copy"){
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
	
	//print_r($output);
	
	if ($output->result=="success") {
			# Result was OK!
			$status = 1;
			//$return['msg'] = "New User has been successfully saved.";
	} else {
			# An error occured
			//$status = 0;
			// $return['msg'] = "Something went wrong please see input data on form.";
	$status = $output->data;
	}

	echo  $status;
?>