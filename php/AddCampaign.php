<?php

	/** Campaigns API - Add a new Campaign */
	/**
	 * Generates action circle buttons for different pages/module
	 * @param goUser 
	 * @param goPass 
	 * @param goAction 
	 * @param responsetype
	 * @param hostname
	 * @param did_pattern
	 * @param group_color
	 * @param call_route
	 * @param survey_type
	 * @param number_channels
	 * @param campaign_type
	 * @param campaign_id
	 * @param campaign_name
	 */
	
	$url = "http://gadcs.goautodial.com/goAPI/goCampaigns/goAPI.php"; # URL to GoAutoDial API file
	$postfields["goUser"] 			= "admin"; #Username goes here. (required)
	$postfields["goPass"] 			= "kam0teque1234"; #Password goes here. (required)
	$postfields["goAction"] 		= "goAddCampaign"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] 	= "json"; #json (required)
	$postfields["hostname"] 		= $_SERVER['REMOTE_ADDR']; #Default value
	$postfields["did_pattern"] 		= $_POST['did_tfn']; #Desired did pattern (required if campaign type is BLENDED)
	// $postfields["group_color"] 		= $_POST['']; #Desired group color (required if campaign type is BLENDED)
	$postfields["call_route"] 		= $_POST['call_route']; #Desired call route (required if campaign type is BLENDED)
	$postfields["survey_type"] 		= $_POST['survey_type']; #survey type values is BROADCAST or PRESS1 only (required if campaign type is survey)
	$postfields["number_channels"] 	= $_POST['no_channels']; #number channel values is 1,5,10,15,20, or 30 only (requred)
	$postfields["campaign_type"] 	= $_POST['campaign_type']; #Type of campaign, values is OITBOUND, INBOUND, BLENDED or SURVEY only. (required)
	$postfields["campaign_id"] 		= $_POST['campaign_id']; #Desired campaign id (required)
	$postfields["campaign_name"] 	= $_POST['campaign_name']; #Desired name of campaign

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
		// $return['msg'] = "New Campaign has been successfully saved.";
	} else {
		# An error occured
		$status = 0;
		// $return['msg'] = "Something went wrong please see input data on form.";
	}

	echo $status;
?>