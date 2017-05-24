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

  $url = gourl."/goCustomFields/goAPI.php"; #URL to GoAutoDial API. (required)
  $postfields["goUser"] = goUser; #Username goes here. (required)
  $postfields["goPass"] = goPass; #Password goes here. (required)
  $postfields["goAction"] = "goCopyCustomFields"; #action performed by the [[API:Functions]]. (required)
  $postfields["responsetype"] = responsetype; #json. (required)
	$postfields["list_to"]	= $_POST['list_to'];
	$postfields["list_from"]	= $_POST['list_from'];
	$postfields["copy_option"]	= $_POST['copy_option'];
	
	$postfields["log_user"] = $_POST['log_user'];
	$postfields["log_group"] = $_POST['log_group'];
	$postfields["hostname"] = $_SERVER['REMOTE_ADDR'];

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
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
		$status = "success";
	} else {
		# An error occured
		$status = "error";
	}

	echo $status;
?>
