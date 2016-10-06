<?php

	/** Campaigns API - Add a new Campaign */
	/**
	 * Generates action circle buttons for different pages/module
	 */

    require_once('goCRMAPISettings.php');

    $url = gourl."/goPauseCodes/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goDeletePauseCode"; #action performed by the [[API:Functions]]. (required)
    $postfields["responsetype"] = responsetype; #json. (required)
		$postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
    $postfields["pauseCampID"] = $_POST['campaign_id'];
		$postfields["pause_code"] = $_POST['pause_code'];

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
			$status = "error";
		}

		echo $status;
?>
