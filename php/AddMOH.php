<?php
require_once('goCRMAPISettings.php');	

	$url = gourl."/goMusicOnHold/goAPI.php"; # URL to GoAutoDial API file
	$postfields["goUser"] 			= goUser; #Username goes here. (required)
	$postfields["goPass"] 			= goPass; #Password goes here. (required)
	$postfields["goAction"] 		= "goAddMOH"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] 	= responsetype; #json (required)
	$postfields["hostname"] 		= $_SERVER['REMOTE_ADDR']; #Default value
	
	$postfields["moh_id"] 			= $_POST['moh_id']; 
	$postfields["moh_name"] 		= $_POST['moh_name']; 
	$postfields["user_group"] 		= $_POST['user_group'];
	$postfields["active"] 			= $_POST['active']; 
	$postfields["random"] 			= $_POST['random'];
	
	$postfields["log_user"]			= $_POST['log_user'];
	$postfields["log_group"]		= $_POST['log_group'];

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
	} else {
		# An error occured
		$status = $output->result;
	}

	echo $status;

?>