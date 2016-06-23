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
        
        require_once('goCRMAPISettings.php');
	
	$url = gourl."/goCampaigns/goAPI.php"; # URL to GoAutoDial API file
	$postfields["goUser"] 						= goUser; #Username goes here. (required)
	$postfields["goPass"] 						= goPass; #Password goes here. (required)
	$postfields["goAction"] 					= "goAddCampaign"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] 				= responsetype; #json (required)
	$postfields["hostname"] 					= $_SERVER['REMOTE_ADDR']; #Default value
	$postfields["did_pattern"] 					= $_POST['did_tfn']; #Desired did pattern (required if campaign type is BLENDED)
	// $postfields["group_color"] 		= $_POST['']; #Desired group color (required if campaign type is BLENDED)
	$postfields["call_route"] 					= $_POST['call_route']; #Desired call route (required if campaign type is BLENDED)
	$postfields["survey_type"] 					= $_POST['survey_type']; #survey type values is BROADCAST or PRESS1 only (required if campaign type is survey)
	$postfields["number_channels"] 				= $_POST['no_channels']; #number channel values is 1,5,10,15,20, or 30 only (requred)
	$postfields["campaign_type"] 				= $_POST['campaign_type']; #Type of campaign, values is OITBOUND, INBOUND, BLENDED or SURVEY only. (required)
	$postfields["campaign_id"] 					= $_POST['campaign_id']; #Desired campaign id (required)
	$postfields["campaign_name"] 				= $_POST['campaign_name']; #Desired name of campaign
	$postfields["call_time"] 					= $_POST['call_time'];
	$postfields["dial_status"] 					= $_POST['dial_status'];
	$postfields["list_order"] 					= $_POST['list_order'];
	$postfields["lead_filter"] 					= $_POST['lead_filter'];
	$postfields["dial_timeout"] 				= $_POST['dial_timeout'];
	$postfields["manual_dial_prefix"] 			= $_POST['manual_dial_prefix'];
	$postfields["call_launch"] 					= $_POST['call_launch'];
	$postfields["answering_machine_message"] 	= $_POST['answering_machine_message'];
	$postfields["pause_codes"] 					= $_POST['pause_codes'];
	$postfields["manual_dial_filter"] 			= $_POST['manual_dial_filter'];
	$postfields["manual_dial_list_id"] 			= $_POST['manual_dial_list_id'];
	$postfields["availability_only_tally"] 		= $_POST['availability_only_tally'];
	$postfields["recording_filename"] 			= $_POST['recording_filename'];
	$postfields["next_agent_call"] 				= $_POST['next_agent_call'];
	$postfields["caller_id_3_way_call"] 		= $_POST['caller_id_3_way_call'];
	$postfields["dial_prefix_3_way_call"] 		= $_POST['dial_prefix_3_way_call'];
	$postfields["three_way_hangup_logging"] 	= $_POST['three_way_hangup_logging'];
	$postfields["three_way_hangup_seconds"] 	= $_POST['three_way_hangup_seconds'];
	$postfields["three_way_hangup_action"] 		= $_POST['three_way_hangup_action'];
	$postfields["reset_leads_on_hopper"] 		= $_POST['reset_leads_on_hopper'];

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