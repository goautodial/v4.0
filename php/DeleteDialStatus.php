<?php

	/** Campaigns API - Add a new Campaign dial status */
	/**
	 * Generates action circle buttons for different pages/module
	 * @param goUser
	 * @param goPass
	 * @param goAction
	 * @param responsetype
	 * @param hostname
	 * @param campaign_id
	 * @param dial status
	 */

    require_once('goCRMAPISettings.php');

	$url = gourl."/goCampaigns/goAPI.php"; # URL to GoAutoDial API file
	$postfields["goUser"] 						= goUser; #Username goes here. (required)
	$postfields["goPass"] 						= goPass; #Password goes here. (required)
	$postfields["goAction"] 					= "goUpdateCampaignDialStatus"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] 			= responsetype; #json (required)
	$postfields["hostname"] 					= $_SERVER['REMOTE_ADDR']; #Default value
	$postfields["log_user"]						= $_POST['log_user'];
	$postfields["log_group"]					= $_POST['log_group'];

	$postfields['campaign_id']  			= $_POST['campaign_id'];
	
	$old_statuses = explode(" ",$_POST['dial_status']);
	$oldStats = array();
	foreach($old_statuses as $old){
		if(!empty($old) && $old != $_POST['selected_status']){
			array_push($oldStats, $old);
		}
	}
	
	$new_status = ' ';
	foreach($oldStats as $OLD){
		$new_status .= $OLD.' ';
	}
	$new_status = rtrim($new_status, " ");
	
	$postfields['dial_status'] = $new_status;


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
		echo json_encode(1);
	} else {
		echo json_encode(0);
	}

?>
