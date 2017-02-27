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
	$postfields["log_user"]						= $_POST['log_user'];
	$postfields["log_group"]					= $_POST['log_group'];

	$postfields["campaign_id"] 					= $_POST["campaign_id"];
	$postfields["campaign_name"] 				= $_POST["campaign_name"];
	$postfields["campaign_desc"] 				= $_POST["campaign_desc"];
	$postfields["active"] 						= $_POST["active"];
	$postfields["dial_method"] 					= $_POST["dial_method"];
	$postfields["auto_dial_level"]				= $_POST["auto_dial_level"];
	$postfields["auto_dial_level_adv"] 			= $_POST["auto_dial_level_adv"];
	$postfields["dial_prefix"] 					= $_POST["dial_prefix"];
	$postfields["custom_prefix"] 				= $_POST["custom_prefix"];
	$postfields["web_form_address"] 			= $_POST["web_form_address"];
	$postfields["campaign_script"] 				= $_POST["campaign_script"];
	$postfields["campaign_cid"] 				= $_POST["campaign_cid"];
	$postfields["campaign_recording"] 			= $_POST["campaign_recording"];
	$postfields["campaign_vdad_exten"] 			= $_POST["campaign_vdad_exten"];
	$postfields["local_call_time"] 				= $_POST["local_call_time"];
	$postfields["hopper_level"] 				= $_POST["hopper_level"];
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
	$postfields["use_internal_dnc"]				= $_POST["use_internal_dnc"];
	$postfields["use_campaign_dnc"]				= $_POST["use_campaign_dnc"];
	$postfields["manual_dial_list_id"] 			= $_POST["manual_dial_list_id"];
	$postfields["available_only_ratio_tally"] 	= $_POST["available_only_ratio_tally"];
	$postfields["campaign_rec_filename"] 		= $_POST["campaign_rec_filename"];
	$postfields["next_agent_call"] 				= $_POST["next_agent_call"];
	$postfields["xferconf_a_number"] 			= $_POST["xferconf_a_number"];
	$postfields["xferconf_b_number"] 			= $_POST["xferconf_b_number"];
	$postfields["three_way_call_cid"] 			= $_POST["three_way_call_cid"];
	$postfields["three_way_dial_prefix"] 		= $_POST["three_way_dial_prefix"];
	$postfields["customer_3way_hangup_logging"] = $_POST["customer_3way_hangup_logging"];
	$postfields["customer_3way_hangup_seconds"] = $_POST["customer_3way_hangup_seconds"];
	$postfields["customer_3way_hangup_action"] 	= $_POST["customer_3way_hangup_action"];
	$postfields["campaign_allow_inbound"]		= $_POST["campaign_allow_inbound"];
	$postfields["custom_fields_launch"]			= $_POST["custom_fields_launch"];
	$postfields["campaign_type"]				= $_POST["campaign_type"];
	$postfields["custom_fields_list_id"]		= $_POST["custom_fields_list_id"];
	$postfields["per_call_notes"]				= $_POST["per_call_notes"];
	$postfields["url_tab_first_title"]			= $_POST["url_tab_first_title"];
	$postfields["url_tab_first_url"]			= $_POST["url_tab_first_url"];
	$postfields["url_tab_second_title"]			= $_POST["url_tab_second_title"];
	$postfields["url_tab_second_url"]			= $_POST["url_tab_second_url"];
	$postfields["amd_send_to_vmx"]				= $_POST["amd_send_to_vmx"];
	$postfields["waitforsilence_options"]		= $_POST["waitforsilence_options"];
	$postfields["agent_lead_search"]			= $_POST["agent_lead_search"];
	$postfields["agent_lead_search_method"]		= $_POST["agent_lead_search_method"];
	$postfields["omit_phone_code"]				= $_POST["omit_phone_code"];
	
	$postfields["survey_first_audio_file"] 		= (isset($_POST["survey_first_audio_file"]))? $_POST["survey_first_audio_file"] : "";
	$postfields["survey_method"] 				= (isset($_POST["survey_method"]))? $_POST["survey_method"] : "";
	$postfields["survey_menu_id"] 				= (isset($_POST["survey_menu_id"]))? $_POST["survey_menu_id"] : "";
	$postfields["survey_dtmf_digits"] 			= (isset($_POST["survey_dtmf_digits"]))? $_POST["survey_dtmf_digits"] : "";
	$postfields["survey_xfer_exten"] 			= (isset($_POST["survey_xfer_exten"]))? $_POST["survey_xfer_exten"] : "";
	$postfields["survey_ni_digit"] 				= (isset($_POST["survey_ni_digit"]))? $_POST["survey_ni_digit"] : "";
	$postfields["survey_ni_audio_file"] 		= (isset($_POST["survey_ni_audio_file"]))? $_POST["survey_ni_audio_file"] : "";
	$postfields["survey_ni_status"] 			= (isset($_POST["survey_ni_status"]))? $_POST["survey_ni_status"] : "";
	$postfields["survey_third_digit"] 			= (isset($_POST["survey_third_digit"]))? $_POST["survey_third_digit"] : "";
	$postfields["survey_third_audio_file"] 		= (isset($_POST["survey_third_audio_file"]))? $_POST["survey_third_audio_file"] : "";
	$postfields["survey_third_status"] 			= (isset($_POST["survey_third_status"]))? $_POST["survey_third_status"] : "";
	$postfields["survey_third_exten"] 			= (isset($_POST["survey_third_exten"]))? $_POST["survey_third_exten"] : "";
	$postfields["survey_fourth_digit"] 			= (isset($_POST["survey_fourth_digit"]))? $_POST["survey_fourth_digit"] : "";
	$postfields["survey_fourth_audio_file"] 	= (isset($_POST["survey_fourth_audio_file"]))? $_POST["survey_fourth_audio_file"] : "";
	$postfields["survey_fourth_status"] 		= (isset($_POST["survey_fourth_status"]))? $_POST["survey_fourth_status"] : "";
	$postfields["survey_fourth_exten"] 			= (isset($_POST["survey_fourth_exten"]))? $_POST["survey_fourth_exten"] : "";
	$postfields["no_channels"] 					= (isset($_POST["no_channels"]))? $_POST["no_channels"] : 1;
	

	if(is_array($_POST["closer_campaigns"])){
		$closerCampaigns = "";
		foreach($_POST["closer_campaigns"] as $closercamp){
			$closerCampaigns .= $closercamp." ";
		}
		$closerCampaigns .= "- ";
	}else{
		$closerCampaigns = $closer_campaigns;
	}

	if(is_array($_POST["xfer_groups"])){
		$xfergroups = "";
		foreach($_POST["xfer_groups"] as $xfergrp){
			$xfergroups .= $xfergrp." ";
		}
		$xfergroups .= "- ";
	}else{
		$xfergroups = $xfer_groups;
	}

	$postfields["closer_campaigns"] = $closerCampaigns;
	$postfields["xfer_groups"] = $xfergroups;

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
