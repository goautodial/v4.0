<?php

	/** Campaigns API - Update Campaign */
	/**
	 * Generates action circle buttons for different pages/module
	 * @param form data
	 */

	require_once('goCRMAPISettings.php');+

	$url = gourl."/goCampaigns/goAPI.php"; # URL to GoAutoDial API file
	$postfields["goUser"] 						= goUser; #Username goes here. (required)
	$postfields["goPass"] 						= goPass; #Password goes here. (required)
	$postfields["goAction"] 					= "goEditCampaign"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] 				= responsetype; #json (required)
	$postfields["hostname"] 					= $_SERVER['REMOTE_ADDR']; #Default value

	$postfields["campaign_id"] 					= $_POST["campaign_id"];
	$postfields["campaign_name"] 				= $_POST["campaign_name"];
	$postfields["campaign_desc"] 				= $_POST["campaign_desc"];
	$postfields["active"] 						= $_POST["active"];
	$postfields["dial_method"] 					= $_POST["dial_method"];

	if($_POST["dial_method"] == "AUTO_DIAL")
		$postfields["auto_dial_level"]			= $_POST["auto_dial_level"];

	$postfields["auto_dial_level_adv"] 			= $_POST["auto_dial_level_adv"];
	$postfields["dial_prefix"] 					= $_POST["dial_prefix"];
	$postfields["custom_prefix"] 				= $_POST["custom_prefix"];
	$postfields["web_form_address"] 			= $_POST["web_form_address"];
	$postfields["campaign_script"] 				= $_POST["campaign_script"];
	$postfields["campaign_cid"] 				= $_POST["campaign_cid"];
	$postfields["campaign_recording"] 			= $_POST["campaign_recording"];
	$postfields["campaign_vdad_exten"] 			= $_POST["campaign_vdad_exten"];
	$postfields["local_call_time"] 				= $_POST["local_call_time"];
	$postfields["force_reset_hopper"] 			= $_POST["force_reset_hopper"];
	$postfields["dial_status"] 					= $_POST["dial_status"];
	$postfields["lead_order"] 					= $_POST["lead_order"];
	$postfields["lead_filter"] 					= $_POST["lead_filter"];
	$postfields["dial_timeout"] 				= $_POST["dial_timeout"];
	$postfields["manual_dial_prefix"] 			= $_POST["manual_dial_prefix"];
	$postfields["get_call_launch"] 				= $_POST["get_call_launch"];
	$postfields["am_message_exten"] 			= $_POST["am_message_exten"];
	$postfields["am_message_chooser"] 			= $_POST["am_message_chooser"];
	$postfields["agent_pause_codes_active"] 	= $_POST["agent_pause_codes_active"];
	$postfields["manual_dial_filter"] 			= $_POST["manual_dial_filter"];
	$postfields["manual_dial_list_id"] 			= $_POST["manual_dial_list_id"];
	$postfields["available_only_ratio_tally"] 	= $_POST["available_only_ratio_tally"];
	$postfields["campaign_rec_filename"] 		= $_POST["campaign_rec_filename"];
	$postfields["next_agent_call"] 				= $_POST["next_agent_call"];
	$postfields["three_way_call_cid"] 			= $_POST["three_way_call_cid"];
	$postfields["three_way_dial_prefix"] 		= $_POST["three_way_dial_prefix"];
	$postfields["customer_3way_hangup_logging"] = $_POST["customer_3way_hangup_logging"];
	$postfields["customer_3way_hangup_seconds"] = $_POST["customer_3way_hangup_seconds"];
	$postfields["customer_3way_hangup_action"] 	= $_POST["customer_3way_hangup_action"];

	if ($_POST["dial_method"] == "INBOUND_MAN") {
		$postfields["inbound_man"] 					= $_POST["inbound_man"];
	} else {
		$postfields["inbound_man"] 					= "";
	}


	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	// curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$data = curl_exec($ch);

	curl_close($ch);

	$output = json_decode($data);
	// echo "<pre>";
	// print_r($output);die;
	$home = $_SERVER['HTTP_REFERER'];
	if ($output->result == "success") {
		# Result was OK!
		$url = str_replace("?message=Success&campaign=".$_POST["campaign_id"], "", $home);
		header("Location: ".$url."?message=Success&campaign=".$_POST["campaign_id"]);
	} else {
		# An error occured
		$url = str_replace("?message=Error&campaign=".$_POST["campaign_id"], "", $home);
		header("Location: ".$url."?message=Error&campaign=".$_POST["campaign_id"]);
	}

?>
