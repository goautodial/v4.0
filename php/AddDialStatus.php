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

	$postfields['campaign_id']  			= $_POST['campaign_id'];

	$statuses = explode(" ", $_POST['old_dial_status']);
  // print_r($_POST);
  // echo "<br />";
  // print_r($checkStatus);
  // die;
  if(in_array($_POST['dial_status'], $statuses)){
    $new_status = $_POST['old_dial_status'];
  }else{
    $new_status = $_POST['dial_status']." ".$_POST['old_dial_status'];
  }

  $postfields['dial_status']  			= $new_status;

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
