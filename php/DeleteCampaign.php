<?php

	/** Campaigns API - Add a new Campaign */
	/**
	 * Generates action circle buttons for different pages/module
	 * @param goUser 
	 * @param goPass 
	 * @param goAction 
	 * @param responsetype
	 * @param hostname
	 * @param campaign_id
	 */
        require_once('goCRMAPISettings.php');
        
	$campaign_id = $_POST['campaign_id'];
	$action = $_POST["action"];
	if($action == "delete_selected"){
		$campaign_id = implode(",",$campaign_id);
	}
	
	$url = gourl."/goCampaigns/goAPI.php"; #URL to GoAutoDial API. (required)

	$postfields["goUser"] 		= goUser; #Username goes here. (required)
	$postfields["goPass"] 		= goPass; #Password goes here. (required)
	$postfields["goAction"] 		= "goDeleteCampaign"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] 	    = responsetype; #json (required)
	$postfields["hostname"] 		= $_SERVER['REMOTE_ADDR']; #Default value
	$postfields["campaign_id"] 		= $campaign_id;; #Desired campaign id. (required)
	$postfields["action"] 		= $action;
	$postfields["log_user"]		= $_POST['log_user'];
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