<?php

####################################################
#### Name: API_EmergencyLogout.php              ####
#### Type: API for emergency logout             ####
#### Version: 0.9                               ####
#### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
#### Written by: Jerico James Milo              ####
#### License: AGPLv2                            ####
####################################################

require_once('goCRMAPISettings.php');

	$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
	$postfields["goUser"] = goUser; #Username goes here. (required)
	$postfields["goPass"] = goPass;
	$postfields["goAction"] = "goEmergencyLogout"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] = responsetype;
	$postfields["goUserAgent"] = $_POST['goUserAgent'];

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	$data = curl_exec($ch);
	curl_close($ch);

	$output = json_decode($data);

	if ($output->result=="success") {
	   # Result was OK!
	    $status = "success";
	 } else {
	   # An error occured
		$status = $output->result;
	}

	echo $status;

	
?>
