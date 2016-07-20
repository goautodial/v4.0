<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('goCRMAPISettings.php');	

if(!isset($_POST['selectable'])){
	$_POST['selectable'] = "N";
}

if(!isset($_POST['human_answered'])){
	$_POST['human_answered'] = "N";
}

if(!isset($_POST['sale'])){
	$_POST['sale'] = "N";
}

if(!isset($_POST['dnc'])){
	$_POST['dnc'] = "N";
}

if(!isset($_POST['scheduled_callback'])){
	$_POST['scheduled_callback'] = "N";
}

if(!isset($_POST['customer_contact'])){
	$_POST['customer_contact'] = "N";
}

if(!isset($_POST['not_interested'])){
	$_POST['not_interested'] = "N";
}

if(!isset($_POST['unworkable'])){
	$_POST['unworkable'] = "N";
}

	$url = gourl."/goDispositions/goAPI.php"; # URL to GoAutoDial API file
	$postfields["goUser"] 			= goUser; #Username goes here. (required)
	$postfields["goPass"] 			= goPass; #Password goes here. (required)
	$postfields["goAction"] 		= "goAddDisposition"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] 	= responsetype; #json (required)
	$postfields["hostname"] 		= $_SERVER['REMOTE_ADDR']; #Default value
	
	$postfields['campaign_id'] 			=  $_POST['campaign'];
	$postfields['status'] 				=  $_POST['status'];
	$postfields['status_name'] 			=  $_POST['status_name'];
	$postfields['selectable'] 			=  $_POST['selectable'];
	$postfields['human_answered'] 		=  $_POST['human_answered'];
	$postfields['sale'] 				=  $_POST['sale'];
	$postfields['dnc'] 					=  $_POST['dnc'];
	$postfields['scheduled_callback'] 	=  $_POST['scheduled_callback'];
	$postfields['customer_contact'] 	=  $_POST['customer_contact'];
	$postfields['not_interested'] 		=  $_POST['not_interested'];
	$postfields['unworkable'] 			=  $_POST['unworkable'];

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	// curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
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