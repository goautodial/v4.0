<?php

/** Server API - Add a new Server */
/**
 * Generates action circle buttons for different pages/module
 */

require_once('goCRMAPISettings.php');	

	$url = gourl."/goServers/goAPI.php"; # URL to GoAutoDial API file
	$postfields["goUser"] 		= goUser; #Username goes here. (required)
	$postfields["goPass"] 		= goPass; #Password goes here. (required)
	$postfields["goAction"] 		= "goAddServer"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] 	= responsetype; #json (required)
	$postfields["hostname"] 		= $_SERVER['REMOTE_ADDR']; #Default value
	
	$postfields["server_id"] 		= $_POST['server_id']; 
	$postfields["server_description"] 		= $_POST['server_description']; 
	$postfields["server_ip"] 		= $_POST['server_ip'];
	$postfields["active"] 		= $_POST['active'];
	$postfields["asterisk_version"] 		= $_POST['asterisk_version'];
	$postfields["user_group"] 		= $_POST['user_group'];
	
	$postfields["log_user"] 		= $_POST['log_user']; 
	$postfields["log_group"] 		= $_POST['log_group'];

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	// curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$data = curl_exec($ch);
	curl_close($ch);

	$output = json_decode($data);
	
	if ($output->result=="success") {
		# Result was OK!
		$status = $output->result;
		//$return['msg'] = "New User has been successfully saved.";
	} else {
		# An error occured
		//$status = 0;
		// $return['msg'] = "Something went wrong please see input data on form.";
        $status = $output->result;
	}

	echo $status;

?>