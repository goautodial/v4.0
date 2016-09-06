<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('goCRMAPISettings.php');	

	$url = gourl."/goScripts/goAPI.php"; # URL to GoAutoDial API file
	$postfields["goUser"] 			= goUser; #Username goes here. (required)
	$postfields["goPass"] 			= goPass; #Password goes here. (required)
	$postfields["goAction"] 		= "goAddScript"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] 	= responsetype; #json (required)
	
	$postfields["script_id"] 			= $_POST['script_id']; 
	$postfields["script_name"] 			= $_POST['script_name']; 
	$postfields["script_comments"] 		= $_POST['script_comments'];
	$postfields["script_text"] 			= $_POST['script_text']; 
	$postfields["active"] 				= $_POST['active'];
	$postfields["user"]					= $_POST['script_user'];

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	$data = curl_exec($ch);
	curl_close($ch);
	$output = json_decode($data);

	if ($output->result == "success") {
		# Result was OK!
		$status = "success";
	} else {
		# An error occured
        $status = $output->result;
	}

	echo $status;
?>