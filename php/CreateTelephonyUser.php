<?php

/** Telephony Users API - Add a new Telephony User */

require_once('goCRMAPISettings.php');	

	$url = gourl."/goUsers/goAPI.php"; # URL to GoAutoDial API file
	$postfields["goUser"] 	= goUser; #Username goes here. (required)
	$postfields["goPass"] 	= goPass; #Password goes here. (required)
	$postfields["goAction"] 	= "goAddUser"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] 	= responsetype; #json (required)
	$postfields["hostname"] 	= $_SERVER['REMOTE_ADDR']; #Default value
	
	$postfields["user"] 		= $_POST['user_form']; 
	$postfields["pass"] 		= $_POST['password']; 
	$postfields["full_name"] 		= $_POST['fullname']; 
	$postfields["user_group"] 		= $_POST['user_group']; 
	$postfields["active"] 		= $_POST['status']; 
	$postfields["seats"]		= $_POST["seats"];
	$postfields["phone_login"]		= $_POST["phone_logins"];
	$postfields["phone_pass"]		= $_POST["phone_pass"];
	
	$postfields["log_user"]		= $_POST["log_user"];
	$postfields["log_group"]		= $_POST["log_group"];

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