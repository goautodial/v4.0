<?php

	/** Telephony Users API - Add a new Telephony User */
	/**
	 * Generates action circle buttons for different pages/module
	 */
require_once('goCRMAPISettings.php');	

	$url = gourl."/goUserGroups/goAPI.php"; # URL to GoAutoDial API file
	$postfields["goUser"] 			= goUser; #Username goes here. (required)
	$postfields["goPass"] 			= goPass; #Password goes here. (required)
	$postfields["goAction"] 		= "goAddUserGroup"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] 	= responsetype; #json (required)
	$postfields["hostname"] 		= $_SERVER['REMOTE_ADDR']; #Default value
	
	$postfields["user_group"] 		= $_POST['usergroup_id']; 
	$postfields["group_name"] 		= $_POST['groupname']; 
	$postfields["group_level"] 		= $_POST['grouplevel'];
	
	$postfields["log_user"] 		= $_POST['log_user']; 
	$postfields["log_group"] 		= $_POST['log_group'];

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

	echo $status;

?>