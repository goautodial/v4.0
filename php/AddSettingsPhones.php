<?php

	/** Telephony Users API - Add a new Telephony User */
	/**
	 * Generates action circle buttons for different pages/module
	 */
require_once('goCRMAPISettings.php');	

	$url 							= gourl"/goPhones/goAPI.php"; # URL to GoAutoDial API file
	$postfields["goUser"] 			= goUser; #Username goes here. (required)
	$postfields["goPass"] 			= goPass; #Password goes here. (required)
	$postfields["goAction"] 		= "goAddPhones"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] 	= responsetype; #json. (required)
	$postfields["extension"] 		= $_POST['start_ext']; #Deisred extension (required)
	$postfields["server_ip"] 		= $_POST['ip']; #Desired server_ip (required)
	$postfields["hostname"] 		= $_SERVER['REMOTE_ADDR']; #Default value
	$postfields["pass"] 			= $_POST['pass']; #Desired password (required)
	$postfields["protocol"] 		= "EXTERNAL"; #SIP, Zap, IAX2, or EXTERNAL. (required)
	$postfields["dialplan_number"]	= ""; #Desired dialplan number (required)
	$postfields["voicemail_id"] 	= ""; #Desired voicemail (required)
	$postfields["status"] 			= "PENDING"; #ACTIVE, SUSPENDED, CLOSED, PENDING, or ADMIN (required)
	$postfields["active"] 			= "Y"; #Y or N (required)
	$postfields["fullname"] 		= $_POST['name']; #Desired full name (required)
	$postfields["messages"] 		= ""; #Desire message (required)
	$postfields["old_messages"] 	= ""; #Desired old message (required)
	$postfields["user_group"] 		= $_POST['user_group']; #Assign to user group (required)
 
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	$data = curl_exec($ch);
	curl_close($ch);
	$output = json_decode($data);
	 
	// print_r($data);
	
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

	echo $status;

?>