<?php
/**
 * @file        ModifyTelephonyCampaign.php
 * @brief       Modify campaign entries
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A, Biscocho
 * @author      Alexander Jim H. Abenoja
 * @author		Noel Umandap
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

	require_once('APIHandler.php');
	$api 											= \creamy\APIHandler::getInstance();

	// check required fields
	$reason 										= "Unable to Modify Campaign";
	$validated 										= 1;
	
	if (!isset($_POST["campaign_id"])) {
		$validated 									= 0;
	}

	if ($validated == 1) {
		// collect new user data.	
		$campaign_id 								= $_POST["campaign_id"];		
		$campaign_name 								= NULL;		
		if (isset($_POST["campaign_name"])) { 
			$campaign_name 							= $_POST["campaign_name"]; 
			$campaign_name 							= stripslashes($campaign_name);
		}

		$campaign_desc 								= NULL; 		
		if (isset($_POST["campaign_desc"])) { 
			$campaign_desc 							= $_POST["campaign_desc"]; 
			$campaign_desc 							= stripslashes($campaign_desc);
		}	

		$active 									= NULL; 		
		if (isset($_POST["active"])) { 
			$active 								= $_POST["active"]; 
			$active 								= stripslashes($active);
		}

		$dial_method 								= NULL; 		
		if (isset($_POST["dial_method"])) { 
			$dial_method 							= $_POST["dial_method"]; 
			$dial_method 							= stripslashes($dial_method);
		}

		$auto_dial_level 							= NULL; 
		if (isset($_POST["auto_dial_level"])) { 
			$auto_dial_level 						= $_POST["auto_dial_level"]; 
			$auto_dial_level 						= stripslashes($auto_dial_level);
		}
		
		$auto_dial_level_adv 						= NULL; 
		if (isset($_POST["auto_dial_level_adv"])) { 
			$auto_dial_level_adv 					= $_POST["auto_dial_level_adv"]; 
			$auto_dial_level_adv 					= stripslashes($auto_dial_level_adv);
		}
		
		$dial_prefix 								= NULL; 
		if (isset($_POST["dial_prefix"])) { 
			$dial_prefix 							= $_POST["dial_prefix"]; 
			$dial_prefix 							= stripslashes($dial_prefix);
		}
		
		$custom_prefix 								= NULL; 
		if (isset($_POST["custom_prefix"])) { 
			$custom_prefix 							= $_POST["custom_prefix"]; 
			$custom_prefix 							= stripslashes($custom_prefix);
		}

		$web_form_address 							= NULL; 
		if (isset($_POST["web_form_address"])) { 
			$web_form_address 						= $_POST["web_form_address"]; 
			$web_form_address 						= stripslashes($web_form_address);
		}
		
		$campaign_script 							= NULL; 
		if (isset($_POST["campaign_script"])) { 
			$campaign_script 						= $_POST["campaign_script"]; 
			$campaign_script 						= stripslashes($campaign_script);
		}

		$campaign_cid 								= NULL; 
		if (isset($_POST["campaign_cid"])) { 
			$campaign_cid 							= $_POST["campaign_cid"]; 
			$campaign_cid 							= stripslashes($campaign_cid);
		}

		$use_custom_cid								= 'N';
		if (isset($_POST["use_custom_cid"]))  {
			$use_custom_cid 						= $_POST["use_custom_cid"];
			$use_custom_cid 						= stripslashes($use_custom_cid);
		}

		$campaign_recording 						= NULL; 
		if (isset($_POST["campaign_recording"])) { 
			$campaign_recording 					= $_POST["campaign_recording"]; 
			$campaign_recording 					= stripslashes($campaign_recording);
		}
		
		$campaign_vdad_exten 						= NULL; 
		if (isset($_POST["campaign_vdad_exten"])) { 
			$campaign_vdad_exten 					= $_POST["campaign_vdad_exten"]; 
			$campaign_vdad_exten 					= stripslashes($campaign_vdad_exten);
		}
		
		$local_call_time 							= NULL; 
		if (isset($_POST["local_call_time"])) { 
			$local_call_time 						= $_POST["local_call_time"]; 
			$local_call_time 						= stripslashes($local_call_time);
		}

		$hopper_level 								= NULL; 
		if (isset($_POST["hopper_level"])) { 
			$hopper_level 							= $_POST["hopper_level"]; 
			$hopper_level							= stripslashes($hopper_level);
		}
		
		$force_reset_hopper 						= NULL; 
		if (isset($_POST["force_reset_hopper"])) { 
			$force_reset_hopper 					= $_POST["force_reset_hopper"]; 
			$force_reset_hopper 					= stripslashes($force_reset_hopper);
		}
		
		$dial_status 								= NULL; 
		if (isset($_POST["dial_status"])) { 
			$dial_status 							= $_POST["dial_status"]; 
			$dial_status 							= stripslashes($dial_status);
		}
		
		$lead_order 								= NULL; 
		if (isset($_POST["lead_order"])) { 
			$lead_order 							= $_POST["lead_order"]; 
			$lead_order 							= stripslashes($lead_order);
		}
		
		$lead_order_secondary 						= NULL; 
		if (isset($_POST["lead_order_secondary"])) { 
			$lead_order_secondary 					= $_POST["lead_order_secondary"]; 
			$lead_order_secondary 					= stripslashes($lead_order_secondary);
		}
		
		$lead_filter 								= ""; 
		if (isset($_POST["lead_filter"])) { 
			$lead_filter 							= $_POST["lead_filter"]; 
			$lead_filter 							= stripslashes($lead_filter);
		}
		
		$call_count_limit 							= ""; 
		if (isset($_POST["call_count_limit"])) { 
			$call_count_limit 						= $_POST["call_count_limit"]; 
			$call_count_limit 						= stripslashes($call_count_limit);
		}
		
		$call_count_target 							= ""; 
		if (isset($_POST["call_count_target"])) { 
			$call_count_target 						= $_POST["call_count_target"]; 
			$call_count_target 						= stripslashes($call_count_target);
		}
		
		$dial_timeout 								= NULL; 
		if (isset($_POST["dial_timeout"])) { 
			$dial_timeout 							= $_POST["dial_timeout"]; 
			$dial_timeout 							= stripslashes($dial_timeout);
		}
		
		$manual_dial_prefix 						= NULL; 
		if (isset($_POST["manual_dial_prefix"])) { 
			$manual_dial_prefix 					= $_POST["manual_dial_prefix"]; 
			$manual_dial_prefix 					= stripslashes($manual_dial_prefix);
		}	
	
		$get_call_launch 							= NULL; 
		if (isset($_POST["get_call_launch"])) { 
			$get_call_launch 						= $_POST["get_call_launch"]; 
			$get_call_launch 						= stripslashes($get_call_launch);
		}

		$am_message_exten 							= NULL; 
		if (isset($_POST["am_message_exten"])) {
			if(!empty($_POST["am_message_exten"])){ 
				$am_message_exten 						= $_POST["am_message_exten"]; 
				$am_message_exten 						= stripslashes($am_message_exten);
			} else {
				$am_message_exten						= 'vm-goodbye';
			}
		}

		$am_message_chooser 						= NULL;
		if (isset($_POST["am_message_chooser"])) { 
			$am_message_chooser 					= $_POST["am_message_chooser"]; 
			$am_message_chooser 					= stripslashes($am_message_chooser);
		}
		
		$agent_pause_codes_active 					= NULL; 
		if (isset($_POST["agent_pause_codes_active"])) { 
			$agent_pause_codes_active 				= $_POST["agent_pause_codes_active"]; 
			$agent_pause_codes_active 				= stripslashes($agent_pause_codes_active);
		}
		
		$manual_dial_filter 						= NULL; 
		if (isset($_POST["manual_dial_filter"])) { 
			$manual_dial_filter 					= $_POST["manual_dial_filter"]; 
			$manual_dial_filter 					= stripslashes($manual_dial_filter);
		}
		
		$manual_dial_search_filter 					= NULL; 
		if (isset($_POST["manual_dial_search_filter"])) { 
			$manual_dial_search_filter 				= $_POST["manual_dial_search_filter"]; 
			$manual_dial_search_filter 				= stripslashes($manual_dial_search_filter);
		}
		
		$use_internal_dnc 							= NULL; 
		if (isset($_POST["use_internal_dnc"])) { 
			$use_internal_dnc 						= $_POST["use_internal_dnc"]; 
			$use_internal_dnc 						= stripslashes($use_internal_dnc);
		}

		$use_campaign_dnc 							= NULL; 
		if (isset($_POST["use_campaign_dnc"])) { 
			$use_campaign_dnc 						= $_POST["use_campaign_dnc"]; 
			$use_campaign_dnc 						= stripslashes($use_campaign_dnc);
		}
		
		$manual_dial_list_id 						= NULL; 
		if (isset($_POST["manual_dial_list_id"])) { 
			$manual_dial_list_id 					= $_POST["manual_dial_list_id"]; 
			$manual_dial_list_id 					= stripslashes($manual_dial_list_id);
		}

		$available_only_ratio_tally 				= NULL; 
		if (isset($_POST["available_only_ratio_tally"])) { 
			$available_only_ratio_tally 			= $_POST["available_only_ratio_tally"]; 
			$available_only_ratio_tally 			= stripslashes($available_only_ratio_tally);
		}

		$campaign_rec_filename 						= NULL; 
		if (isset($_POST["campaign_rec_filename"])) { 
			$campaign_rec_filename 					= $_POST["campaign_rec_filename"]; 
			$campaign_rec_filename 					= stripslashes($campaign_rec_filename);
		}
		
		$next_agent_call 							= NULL; 
		if (isset($_POST["next_agent_call"])) { 
			$next_agent_call 						= $_POST["next_agent_call"]; 
			$next_agent_call 						= stripslashes($next_agent_call);
		}
		$xferconf_a_number 							= NULL; 
		if (isset($_POST["xferconf_a_number"])) { 
			$xferconf_a_number 						= $_POST["xferconf_a_number"]; 
			$xferconf_a_number 						= stripslashes($xferconf_a_number);
		}

		$xferconf_b_number 							= NULL; 
		if (isset($_POST["xferconf_b_number"])) { 
			$xferconf_b_number 						= $_POST["xferconf_b_number"]; 
			$xferconf_b_number 						= stripslashes($xferconf_b_number);
		}
		
		$three_way_call_cid 						= NULL; 
		if (isset($_POST["three_way_call_cid"])) { 
			$three_way_call_cid 					= $_POST["three_way_call_cid"]; 
			$three_way_call_cid 					= stripslashes($three_way_call_cid);
		}
		
		$three_way_dial_prefix 						= NULL; 
		if (isset($_POST["three_way_dial_prefix"])) { 
			$three_way_dial_prefix 					= $_POST["three_way_dial_prefix"]; 
			$three_way_dial_prefix 					= stripslashes($three_way_dial_prefix);
		}
		
		$customer_3way_hangup_logging 				= NULL; 
		if (isset($_POST["customer_3way_hangup_logging"])) { 
			$customer_3way_hangup_logging 			= $_POST["customer_3way_hangup_logging"]; 
			$customer_3way_hangup_logging 			= stripslashes($customer_3way_hangup_logging);
		}
		
		$customer_3way_hangup_seconds 				= NULL; 
		if (isset($_POST["customer_3way_hangup_seconds"])) { 
			$customer_3way_hangup_seconds 			= $_POST["customer_3way_hangup_seconds"]; 
			$customer_3way_hangup_seconds 			= stripslashes($customer_3way_hangup_seconds);
		}
		
		$customer_3way_hangup_action 				= 'NONE'; 
		if (isset($_POST["customer_3way_hangup_action"])) { 
			$customer_3way_hangup_action 			= $_POST["customer_3way_hangup_action"]; 
			$customer_3way_hangup_action 			= stripslashes($customer_3way_hangup_action);
		}
		
		$campaign_allow_inbound 					= NULL; 
		if (isset($_POST["campaign_allow_inbound"])) { 
			$campaign_allow_inbound 				= $_POST["campaign_allow_inbound"]; 
			$campaign_allow_inbound 				= stripslashes($campaign_allow_inbound);
		}
		
		$custom_fields_launch 						= NULL; 
		if (isset($_POST["custom_fields_launch"])) { 
			$custom_fields_launch 					= $_POST["custom_fields_launch"]; 
			$custom_fields_launch 					= stripslashes($custom_fields_launch);
		}
		
		$manual_dial_min_digits 					= NULL; 
		if (isset($_POST["manual_dial_min_digits"])) { 
			$manual_dial_min_digits 				= $_POST["manual_dial_min_digits"]; 
			$manual_dial_min_digits 				= stripslashes($manual_dial_min_digits);
		}
		
		$campaign_type 								= NULL; 
		if (isset($_POST["campaign_type"])) { 
			$campaign_type 							= $_POST["campaign_type"]; 
			$campaign_type 							= stripslashes($campaign_type);
		}

		$custom_fields_list_id 						= NULL; 
		if (isset($_POST["custom_fields_list_id"])) { 
			$custom_fields_list_id 					= $_POST["custom_fields_list_id"]; 
			$custom_fields_list_id 					= stripslashes($custom_fields_list_id);
		}

		$per_call_notes 							= NULL; 
		if (isset($_POST["per_call_notes"])) { 
			$per_call_notes 						= $_POST["per_call_notes"]; 
			$per_call_notes 						= stripslashes($per_call_notes);
		}
		
		$url_tab_first_title 						= 0; 
		if (isset($_POST["url_tab_first_title"])) { 
			$url_tab_first_title 					= $_POST["url_tab_first_title"]; 
			$url_tab_first_title 					= stripslashes($url_tab_first_title);
		}
		
		$url_tab_first_url 							= 0; 
		if (isset($_POST["url_tab_first_url"])) { 
			$url_tab_first_url 						= $_POST["url_tab_first_url"]; 
			$url_tab_first_url 						= stripslashes($url_tab_first_url);
		}
		
		$url_tab_second_title 						= 0; 
		if (isset($_POST["url_tab_second_title"])) { 
			$url_tab_second_title 					= $_POST["url_tab_second_title"]; 
			$url_tab_second_title 					= stripslashes($url_tab_second_title);
		}

		$url_tab_second_url 						= 0; 
		if (isset($_POST["url_tab_second_url"])) { 
			$url_tab_second_url 					= $_POST["url_tab_second_url"]; 
			$url_tab_second_url 					= stripslashes($url_tab_second_url);
		}
		
		$amd_send_to_vmx 							= NULL; 
		if (isset($_POST["amd_send_to_vmx"])) { 
			$amd_send_to_vmx 						= $_POST["amd_send_to_vmx"]; 
			$amd_send_to_vmx 						= stripslashes($amd_send_to_vmx);
		}

		$waitforsilence_options 					= NULL; 
		if (isset($_POST["waitforsilence_options"])) { 
			$waitforsilence_options 				= $_POST["waitforsilence_options"]; 
			$waitforsilence_options 				= stripslashes($waitforsilence_options);
		}

		$agent_lead_search 							= NULL; 
		if (isset($_POST["agent_lead_search"])) { 
			$agent_lead_search 						= $_POST["agent_lead_search"]; 
			$agent_lead_search 						= stripslashes($agent_lead_search);
		}
		
		$agent_lead_search_method 					= NULL; 
		if (isset($_POST["agent_lead_search_method"])) { 
			$agent_lead_search_method 				= $_POST["agent_lead_search_method"]; 
			$agent_lead_search_method 				= stripslashes($agent_lead_search_method);
		}
		
		$omit_phone_code 							= NULL; 
		if (isset($_POST["omit_phone_code"])) { 
			$omit_phone_code 						= $_POST["omit_phone_code"]; 
			$omit_phone_code 						= stripslashes($omit_phone_code);
		}

		$alt_number_dialing 						= NULL; 
		if (isset($_POST["alt_number_dialing"])) { 
			$alt_number_dialing 					= $_POST["alt_number_dialing"]; 
			$alt_number_dialing 					= stripslashes($alt_number_dialing);
		}
		
		$enable_callback_alert 						= 0; 
		if (isset($_POST["enable_callback_alert"])) { 
			$enable_callback_alert 					= $_POST["enable_callback_alert"]; 
			$enable_callback_alert 					= stripslashes($enable_callback_alert);
		}
		
		$cb_noexpire 								= 0; 
		if (isset($_POST["cb_noexpire"])) { 
			$cb_noexpire 							= $_POST["cb_noexpire"]; 
			$cb_noexpire 							= stripslashes($cb_noexpire);
		}
		
		$cb_sendemail 								= 0; 
		if (isset($_POST["cb_sendemail"])) { 
			$cb_sendemail 							= $_POST["cb_sendemail"]; 
			$cb_sendemail 							= stripslashes($cb_sendemail);
		}
		
		$google_sheet_list_id						= NULL; 
		if (isset($_POST["google_sheet_list_id"])) { 
			$google_sheet_list_id 					= $_POST["google_sheet_list_id"]; 
			$google_sheet_list_id 					= stripslashes($google_sheet_list_id);
		}
		
		$dynamic_cid 								= NULL; 
		if (isset($_POST["dynamic_cid"])) { 
			$dynamic_cid 							= $_POST["dynamic_cid"]; 
			$dynamic_cid 							= stripslashes($dynamic_cid);
		}
		
		$survey_first_audio_file 					= NULL; 
		if (isset($_POST["survey_first_audio_file"])) { 
			$survey_first_audio_file 				= $_POST["survey_first_audio_file"]; 
			$survey_first_audio_file 				= stripslashes($survey_first_audio_file);
		}
		
		$survey_method 								= NULL; 
		if (isset($_POST["survey_method"])) { 
			$survey_method 							= $_POST["survey_method"]; 
			$survey_method 							= stripslashes($survey_method);
		}	
	
		$survey_menu_id 							= NULL; 
		if (isset($_POST["survey_menu_id"])) { 
			$survey_menu_id 						= $_POST["survey_menu_id"]; 
			$survey_menu_id 						= stripslashes($survey_menu_id);
		}

		$survey_dtmf_digits 						= NULL; 
		if (isset($_POST["survey_dtmf_digits"])) { 
			$survey_dtmf_digits 					= $_POST["survey_dtmf_digits"]; 
			$survey_dtmf_digits						= stripslashes($survey_dtmf_digits);
		}

		$survey_xfer_exten	 						= NULL; 
		if (isset($_POST["survey_xfer_exten"])) { 
			$survey_xfer_exten 						= $_POST["survey_xfer_exten"]; 
			$survey_xfer_exten 						= stripslashes($survey_xfer_exten);
		}
		
		$survey_ni_digit 							= NULL; 
		if (isset($_POST["survey_ni_digit"])) { 
			$survey_ni_digit 						= $_POST["survey_ni_digit"]; 
			$survey_ni_digit 						= stripslashes($survey_ni_digit);
		}
		
		$survey_ni_audio_file 						= NULL; 
		if (isset($_POST["survey_ni_audio_file"])) { 
			$survey_ni_audio_file 					= $_POST["survey_ni_audio_file"]; 
			$survey_ni_audio_file 					= stripslashes($survey_ni_audio_file);
		}
		
		$survey_ni_status 							= NULL; 
		if (isset($_POST["survey_ni_status"])) { 
			$survey_ni_status 						= $_POST["survey_ni_status"]; 
			$survey_ni_status 						= stripslashes($survey_ni_status);
		}

		$survey_third_digit 						= NULL; 
		if (isset($_POST["survey_third_digit"])) { 
			$survey_third_digit 					= $_POST["survey_third_digit"]; 
			$survey_third_digit 					= stripslashes($survey_third_digit);
		}
		
		$survey_third_audio_file 					= NULL; 
		if (isset($_POST["survey_third_audio_file"])) { 
			$survey_third_audio_file 				= $_POST["survey_third_audio_file"]; 
			$survey_third_audio_file 				= stripslashes($survey_third_audio_file);
		}

		$survey_third_status 						= NULL; 
		if (isset($_POST["survey_third_status"])) { 
			$survey_third_status 					= $_POST["survey_third_status"]; 
			$survey_third_status 					= stripslashes($survey_third_status);
		}

		$survey_third_exten 						= NULL; 
		if (isset($_POST["survey_third_exten"])) { 
			$survey_third_exten 					= $_POST["survey_third_exten"]; 
			$survey_third_exten 					= stripslashes($survey_third_exten);
		}
		
		$survey_fourth_digit 						= NULL; 
		if (isset($_POST["survey_fourth_digit"])) { 
			$survey_fourth_digit 					= $_POST["survey_fourth_digit"]; 
			$survey_fourth_digit 					= stripslashes($survey_fourth_digit);
		}
		$survey_fourth_audio_file 					= NULL; 
		if (isset($_POST["survey_fourth_audio_file"])) { 
			$survey_fourth_audio_file 				= $_POST["survey_fourth_audio_file"]; 
			$survey_fourth_audio_file 				= stripslashes($survey_fourth_audio_file);
		}

		$survey_fourth_status 						= NULL; 
		if (isset($_POST["survey_fourth_status"])) { 
			$survey_fourth_status 					= $_POST["survey_fourth_status"]; 
			$survey_fourth_status 					= stripslashes($survey_fourth_status);
		}
		
		$survey_fourth_exten 						= NULL; 
		if (isset($_POST["survey_fourth_exten"])) { 
			$survey_fourth_exten 					= $_POST["survey_fourth_exten"]; 
			$survey_fourth_exten 					= stripslashes($survey_fourth_exten);
		}
		
		$no_channels 								= "1"; 
		if (isset($_POST["no_channels"])) { 
			$no_channels 							= $_POST["no_channels"]; 
			$no_channels 							= stripslashes($no_channels);
		}
		
		$disable_alter_custdata 					= "N"; 
		if (isset($_POST["disable_alter_custdata"])) { 
			$disable_alter_custdata 				= $_POST["disable_alter_custdata"]; 
			$disable_alter_custdata 				= stripslashes($disable_alter_custdata);
		}
		
		$disable_alter_custphone 					= "Y"; 
		if (isset($_POST["disable_alter_custphone"])) { 
			$disable_alter_custphone 				= $_POST["disable_alter_custphone"]; 
			$disable_alter_custphone 				= stripslashes($disable_alter_custphone);
		}

		$inbound_man 								= NULL; 
		if ($dial_method == "INBOUND_MAN") { 
			$inbound_man 							= $_POST["dial_method"]; 
			$inbound_man 							= stripslashes($inbound_man);
		}
		
		/*$closer_campaigns 							= NULL; 
		if (isset($_POST["closer_campaigns"])) { 
			$closer_campaigns 						= $_POST["closer_campaigns"]; 
			$closer_campaigns 						= stripslashes($closer_campaigns);
		}
		
		$xfer_groups 								= NULL; 
		if (isset($_POST["xfer_groups"])) { 
			$xfer_groups 							= $_POST["xfer_groups"]; 
			$xfer_groups 							= stripslashes($xfer_groups);
		}*/
		
		$survey_wait_sec	 						= NULL; 
		if (isset($_POST["survey_wait_sec"])) { 
			$survey_wait_sec 						= $_POST["survey_wait_sec"]; 
			$survey_wait_sec 						= stripslashes($survey_wait_sec);
		}
		
		$survey_no_response_action 					= NULL; 
		if (isset($_POST["survey_no_response_action"])) { 
			$survey_no_response_action 				= $_POST["survey_no_response_action"]; 
			$survey_no_response_action 				= stripslashes($survey_no_response_action);
		}
		
		if (is_array($_POST["closer_campaigns"])) {
			$closerCampaigns 						= "";
			
			foreach($_POST["closer_campaigns"] as $closercamp) { 
				$closerCampaigns 					.= $closercamp." "; 
			}
				
			$closerCampaigns 						.= "- ";
			
		} else { 
			$closerCampaigns 						= $closer_campaigns; 
		}

		if (is_array($_POST["xfer_groups"])) {
			$xfergroups 							= "";
			
			foreach($_POST["xfer_groups"] as $xfergrp) { 
				$xfergroups 						.= $xfergrp." "; 
			}
				
			$xfergroups 							.= "- ";
			
		} else { 
			$xfergroups 							= $xfer_groups; 
		}
		
		$default_country_code 						= NULL; 
		if (isset($_POST["default_country_code"])) { 
			$default_country_code 					= $_POST["default_country_code"]; 
			$default_country_code 					= stripslashes($default_country_code);
		}
		
		$postfields 								= array(
			"goAction" 									=> "goEditCampaign", #action performed by the [[API:Functions]]
			"campaign_id" 								=> $campaign_id,
			"campaign_name" 							=> $campaign_name,
			"campaign_desc" 							=> $campaign_desc,
			"active" 									=> $active,
			"dial_method" 								=> $dial_method,
			"auto_dial_level"							=> $auto_dial_level,
			"auto_dial_level_adv" 						=> $auto_dial_level_adv,
			"dial_prefix" 								=> $dial_prefix,
			"custom_prefix" 							=> $custom_prefix,
			"web_form_address" 							=> $web_form_address,
			"campaign_script" 							=> $campaign_script,
			"campaign_cid" 								=> $campaign_cid,
			"campaign_recording" 						=> $campaign_recording,
			"campaign_vdad_exten" 						=> $campaign_vdad_exten,
			"local_call_time" 							=> $local_call_time,
			"hopper_level" 								=> $hopper_level,
			"force_reset_hopper" 						=> $force_reset_hopper,
			"dial_status" 								=> $dial_status,
			"lead_order" 								=> $lead_order,
			"lead_order_secondary" 						=> $lead_order_secondary,
			"lead_filter" 								=> $lead_filter,
			"call_count_limit"							=> $call_count_limit,
			"call_count_target"							=> $call_count_target,
			"dial_timeout" 								=> $dial_timeout,
			"manual_dial_prefix" 						=> $manual_dial_prefix,
			"get_call_launch" 							=> $get_call_launch,
			"am_message_exten" 							=> $am_message_exten,
			"am_message_chooser" 						=> $am_message_chooser,
			"agent_pause_codes_active" 					=> $agent_pause_codes_active,
			"manual_dial_filter" 						=> $manual_dial_filter,
			"manual_dial_search_filter"					=> $manual_dial_search_filter,
			"use_internal_dnc"							=> $use_internal_dnc,
			"use_campaign_dnc"							=> $use_campaign_dnc,
			"manual_dial_list_id" 						=> $manual_dial_list_id,
			"available_only_ratio_tally" 				=> $available_only_ratio_tally,
			"campaign_rec_filename" 					=> $campaign_rec_filename,
			"next_agent_call" 							=> $next_agent_call,
			"xferconf_a_number" 						=> $xferconf_a_number,
			"xferconf_b_number" 						=> $xferconf_b_number,
			"three_way_call_cid" 						=> $three_way_call_cid,
			"three_way_dial_prefix" 					=> $three_way_dial_prefix,
			"customer_3way_hangup_logging" 				=> $customer_3way_hangup_logging,
			"customer_3way_hangup_seconds" 				=> $customer_3way_hangup_seconds,
			"customer_3way_hangup_action" 				=> $customer_3way_hangup_action,
			"campaign_allow_inbound"					=> $campaign_allow_inbound,
			"custom_fields_launch"						=> $custom_fields_launch,
			"manual_dial_min_digits"					=> $manual_dial_min_digits,
			"campaign_type"								=> $campaign_type,
			"custom_fields_list_id"						=> $custom_fields_list_id,
			"per_call_notes"							=> $per_call_notes,
			"url_tab_first_title"						=> $url_tab_first_title,
			"url_tab_first_url"							=> $url_tab_first_url,
			"url_tab_second_title"						=> $url_tab_second_title,
			"url_tab_second_url"						=> $url_tab_second_url,
			"amd_send_to_vmx"							=> $amd_send_to_vmx,
			"waitforsilence_options"					=> $waitforsilence_options,
			"agent_lead_search"							=> $agent_lead_search,
			"agent_lead_search_method"					=> $agent_lead_search_method,
			"omit_phone_code"							=> $omit_phone_code,
			"alt_number_dialing"						=> $alt_number_dialing,		
			"enable_callback_alert"						=> $enable_callback_alert,
			"cb_noexpire"								=> $cb_noexpire,
			"cb_sendemail"								=> $cb_sendemail,
			"google_sheet_list_id"						=> $google_sheet_list_id,		
			"dynamic_cid"								=> $dynamic_cid,
			"survey_first_audio_file" 					=> $survey_first_audio_file,
			"survey_method" 							=> $survey_method,
			"survey_menu_id" 							=> $survey_menu_id,
			"survey_dtmf_digits" 						=> $survey_dtmf_digits,
			"survey_xfer_exten" 						=> $survey_xfer_exten,
			"survey_ni_digit"							=> $survey_ni_digit,
			"survey_ni_audio_file" 						=> $survey_ni_audio_file,
			"survey_ni_status"	 						=> $survey_ni_status,
			"survey_third_digit" 						=> $survey_third_digit,
			"survey_third_audio_file" 					=> $survey_third_audio_file,
			"survey_third_status" 						=> $survey_third_status,
			"survey_third_exten" 						=> $survey_third_exten,
			"survey_fourth_digit"						=> $survey_fourth_digit,
			"survey_fourth_audio_file"	 				=> $survey_fourth_audio_file,
			"survey_fourth_status" 						=> $survey_fourth_status,
			"survey_fourth_exten" 						=> $survey_fourth_exten,
			"no_channels" 								=> $no_channels,
			"disable_alter_custdata" 					=> $disable_alter_custdata,
			"disable_alter_custphone" 					=> $disable_alter_custphone,
			"inbound_man"								=> $inbound_man,
			"closer_campaigns"							=> $closerCampaigns,
			"xfer_groups" 								=> $xfergroups,
			"use_custom_cid"							=> $use_custom_cid,
			"survey_wait_sec"							=> $survey_wait_sec,
			"survey_no_response_action"					=> $survey_no_response_action,
			"default_country_code"						=> $default_country_code
		);
		
		$output 									= $api->API_Request("goCampaigns", $postfields);

		if ($output->result=="success") { 
			$status 								= 1; 
		} else { 
			$status 								= $output->result; 
		}

		echo json_encode($status);
		
	}	
?>
