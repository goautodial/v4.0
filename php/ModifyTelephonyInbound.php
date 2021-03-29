<?php
/**
 * @file        ModifyTelephonyInbound.php
 * @brief       Handles Modify Requests for Inbound, IVR & DID
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      Alexander Jim Abenoja
 * @author		Demian Lizandro A, Biscocho 
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

	$api 										= \creamy\APIHandler::getInstance();

	// check required fields
	$reason 									= "Unable to Modify Inbound";
	$validated 									= 0;

	$groupid 									= NULL;
	if (isset($_POST["modify_groupid"])) {
		$groupid 								= $_POST["modify_groupid"];
	}

	$ivr 										= NULL;
	if (isset($_POST["modify_ivr"])) {
		$ivr 									= $_POST["modify_ivr"];
	}

	$did 										= NULL;
	if (isset($_POST["modify_did"])) {
		$did 									= $_POST["modify_did"];
	}

	// INGROUPS
	if ($groupid != NULL) {
		// collect new user data.	
		$modify_groupid 						= $_POST["modify_groupid"];
		
		$desc				 					= NULL; 
		if (isset($_POST["desc"])) { 
			$desc 								= $_POST["desc"]; 
			$desc 								= stripslashes($desc);
		}
		
		$color 									= NULL; 
		if (isset($_POST["color"])) { 
			$color 								= $_POST["color"];
			$color 								= str_replace("#", '', $color);
			$color 								= stripslashes($color);
		}

		$status 								= NULL; 
		if (isset($_POST["status"])) { 
			$status 							= $_POST["status"]; 
			$status 							= stripslashes($status);
		}
		
		$webform 								= NULL; 
		if (isset($_POST["webform"])) { 
			$webform 							= $_POST["webform"]; 
			$webform 							= stripslashes($webform);
		}
		
		$nextagent 								= NULL; 
		if (isset($_POST["nextagent"])) { 
			$nextagent 							= $_POST["nextagent"]; 
			$nextagent 							= stripslashes($nextagent);
		}
		
		$prio 									= NULL; 
		if (isset($_POST["priority"])) { 
			$prio 								= $_POST["priority"]; 
			$prio 								= stripslashes($prio);
		}
		
		$display 								= NULL; 
		if (isset($_POST["display"])) { 
			$display 							= $_POST["display"]; 
			$display 							= stripslashes($display);
		}
		
		$script 								= NULL; 
		if (isset($_POST["script"])) { 
			$script 							= $_POST["script"]; 
			$script 							= stripslashes($script);
		}
		
		$call_time_id 							= NULL; 
		if (isset($_POST["call_time_id"])) { 
			$call_time_id 						= $_POST["call_time_id"]; 
			$call_time_id 						= stripslashes($call_time_id);
		}
	// -------

		$drop_call_seconds 						= NULL; 
		if (isset($_POST["drop_call_seconds"])) { 
			$drop_call_seconds 					= $_POST["drop_call_seconds"]; 
			$drop_call_seconds 					= stripslashes($drop_call_seconds);
		}
		
		$drop_action 							= NULL; 
		if (isset($_POST["drop_action"])) { 
			$drop_action 						= $_POST["drop_action"]; 
			$drop_action 						= stripslashes($drop_action);
		}
		
		$drop_exten 							= NULL; 
		if (isset($_POST["drop_exten"])) { 
			$drop_exten 						= $_POST["drop_exten"]; 
			$drop_exten 						= stripslashes($drop_exten);
		}
		
		$voicemail_ext 							= NULL;
		if (isset($_POST["voicemail_ext"])) { 
			$voicemail_ext 						= $_POST["voicemail_ext"]; 
			$voicemail_ext 						= stripslashes($voicemail_ext);
		}
		
		$drop_inbound_group 					= NULL; 
		if (isset($_POST["drop_inbound_group"])) { 
			$drop_inbound_group 				= $_POST["drop_inbound_group"]; 
			$drop_inbound_group 				= stripslashes($drop_inbound_group);
		}
		
		$drop_callmenu 							= NULL; 
		if (isset($_POST["drop_callmenu"])) { 
			$drop_callmenu 						= $_POST["drop_callmenu"]; 
			$drop_callmenu 						= stripslashes($drop_callmenu);
		}
		
		$after_hours_action 					= NULL; 
		if (isset($_POST["after_hours_action"])) { 
			$after_hours_action 				= $_POST["after_hours_action"]; 
			$after_hours_action 				= stripslashes($after_hours_action);
		}
		
		$after_hours_voicemail 					= NULL; 
		if (isset($_POST["after_hours_voicemail"])) { 
			$after_hours_voicemail 				= $_POST["after_hours_voicemail"]; 
			$after_hours_voicemail 				= stripslashes($after_hours_voicemail);
		}
		
		$after_hours_exten 						= NULL; 
		if (isset($_POST["after_hours_exten"])) { 
			$after_hours_exten 					= $_POST["after_hours_exten"]; 
			$after_hours_exten 					= stripslashes($after_hours_exten);
		}
		
		$after_hours_message_filename 			= NULL; 
		if (isset($_POST["after_hours_message_filename"])) { 
			$after_hours_message_filename 		= $_POST["after_hours_message_filename"]; 
			$after_hours_message_filename 		= stripslashes($after_hours_message_filename);
		}
		
		$after_hours_callmenu 					= NULL; 
		if (isset($_POST["after_hours_callmenu"])) { 
			$after_hours_callmenu 				= $_POST["after_hours_callmenu"]; 
			$after_hours_callmenu 				= stripslashes($after_hours_callmenu);
		}
		
		$get_call_launch 						= NULL; 
		if (isset($_POST["call_launch"])) { 
			$get_call_launch 					= $_POST["call_launch"]; 
			$get_call_launch 					= stripslashes($get_call_launch);
		}
		
		$no_agent_no_queue 						= NULL; 
		if (isset($_POST["no_agent_no_queue"])) { 
			$no_agent_no_queue 					= $_POST["no_agent_no_queue"]; 
			$no_agent_no_queue 					= stripslashes($no_agent_no_queue);
		}
		
		$no_agent_action 						= NULL; 
		if (isset($_POST["no_agent_action"])) { 
			$no_agent_action 					= $_POST["no_agent_action"]; 
			$no_agent_action 					= stripslashes($no_agent_action);
		}
		
		$no_agent_action_value					= NULL; 
		if (isset($_POST["no_agent_action_value"])) { 
			$no_agent_action_value 				= $_POST["no_agent_action_value"]; 
			$no_agent_action_value 				= stripslashes($no_agent_action_value);
		}
		
		$no_agents_exten 						= NULL; 
		if (isset($_POST["no_agents_exten"])) { 
			$no_agents_exten 					= $_POST["no_agents_exten"]; 
			$no_agents_exten 					= stripslashes($no_agents_exten);
		}
		
		$no_agents_voicemail 					= NULL; 
		if (isset($_POST["no_agents_voicemail"])) { 
			$no_agents_voicemail 				= $_POST["no_agents_voicemail"]; 
			$no_agents_voicemail 				= stripslashes($no_agents_voicemail);
		}
		
		$no_agents_ingroup 						= NULL; 
		if (isset($_POST["no_agents_ingroup"])) { 
			$no_agents_ingroup 					= $_POST["no_agents_ingroup"]; 
			$no_agents_ingroup 					= stripslashes($no_agents_ingroup);
		}
		
		$no_agents_callmenu 					= NULL; 
		if (isset($_POST["no_agents_callmenu"])) { 
			$no_agents_callmenu 				= $_POST["no_agents_callmenu"]; 
			$no_agents_callmenu 				= stripslashes($no_agents_callmenu);
		}
		
		$no_agents_did	 					= NULL; 
		if (isset($_POST["no_agents_did"])) { 
			$no_agents_did 					= $_POST["no_agents_did"]; 
			$no_agents_did 					= stripslashes($no_agents_did);
		}
		
		$no_agents_extension 					= NULL; 
		if (isset($_POST["no_agents_extension"])) { 
			$no_agents_extension				= $_POST["no_agents_extension"]; 
			$no_agents_extension				= stripslashes($no_agents_extension);
		}
		
		$no_agents_extension_context 				= NULL; 
		if (isset($_POST["no_agents_extension_context"])) { 
			$no_agents_extension_context			= $_POST["no_agents_extension_context"]; 
			$no_agents_extension_context			= stripslashes($no_agents_extension_context);
		}
		
		$welcome_message_filename 				= NULL; 
		if (isset($_POST["welcome_message_filename"])) { 
			$welcome_message_filename 			= $_POST["welcome_message_filename"]; 
			$welcome_message_filename 			= stripslashes($welcome_message_filename);
		}
		
		$play_welcome_message 					= NULL; 
		if (isset($_POST["play_welcome_message"])) { 
			$play_welcome_message 				= $_POST["play_welcome_message"]; 
			$play_welcome_message 				= stripslashes($play_welcome_message);
		}
		
		$moh_context 							= NULL; 
		if (isset($_POST["moh_context"])) { 
			$moh_context 						= $_POST["moh_context"]; 
			$moh_context 						= stripslashes($moh_context);
		}
		
		$onhold_prompt_filename 				= NULL; 
		if (isset($_POST["onhold_prompt_filename"])) { 
			$onhold_prompt_filename 			= $_POST["onhold_prompt_filename"]; 
			$onhold_prompt_filename 			= stripslashes($onhold_prompt_filename);
		}
		
		$no_agents_extension_context					= NULL;
		if (isset($_POST["no_agents_extension_context"])) {
			$no_agents_extension_context 				= $_POST["no_agents_extension_context"];
			$no_agents_extension_context 				= stripslashes($no_agents_extension_context);
		}

		$no_agents_extension							= NULL;
		if (isset($_POST["no_agents_extension"])) {
			$no_agents_extension		 				= $_POST["no_agents_extension"];
			$no_agents_extension		 				= stripslashes($no_agents_extension);
		}
		
		$postfields 							= array(
			'goAction' 								=> 'goEditIngroup',
			'group_id' 								=> $modify_groupid, 
			'group_name' 							=> $desc, 
			'group_color' 							=> $color, 
			'web_form_address' 						=> $webform, 
			'active' 								=> $status, 
			'next_agent_call' 						=> $nextagent, 
			'fronter_display' 						=> $display, 
			'ingroup_script' 						=> $script, 
			'queue_priority' 						=> $prio, 
			'call_time_id' 							=> $call_time_id, 
			'drop_call_seconds' 					=> $drop_call_seconds, 
			'drop_action' 							=> $drop_action, 
			'drop_exten' 							=> $drop_exten, 
			'voicemail_ext' 						=> $voicemail_ext, 
			'drop_inbound_group' 					=> $drop_inbound_group, 
			'drop_callmenu' 						=> $drop_callmenu, 
			'after_hours_action' 					=> $after_hours_action, 
			'after_hours_voicemail' 				=> $after_hours_voicemail, 
			'after_hours_exten' 					=> $after_hours_exten,
			'after_hours_message_filename' 			=> $after_hours_message_filename,
			'after_hours_callmenu' 					=> $after_hours_callmenu,
			'get_call_launch' 						=> $get_call_launch, 
			'no_agent_no_queue' 					=> $no_agent_no_queue, 
			'no_agent_action'						=> $no_agent_action, 
			'no_agent_action_value'					=> $no_agent_action_value,
			'no_agents_exten' 						=> $no_agents_exten, 
			'no_agents_voicemail' 					=> $no_agents_voicemail, 
			'no_agents_ingroup' 					=> $no_agents_ingroup, 
			'no_agents_callmenu' 					=> $no_agents_callmenu, 
			'no_agents_did'						=> $no_agents_did, 
			'no_agents_extension'					=> $no_agents_extension, 
			'no_agents_extension_context'				=> $no_agents_extension_context, 
			'welcome_message_filename' 				=> $welcome_message_filename, 
			'play_welcome_message' 					=> $play_welcome_message, 
			'moh_context' 							=> $moh_context, 
			'onhold_prompt_filename' 				=> $onhold_prompt_filename
		);				

		$output 								= $api->API_modifyInGroups($postfields);
		
		if ($output->result == "success") { 
			$status 							= 1; 
		} else { 
			$status 							= $output->result; 
		}
		
		echo json_encode($status);
	}

	// IVR
	if ($ivr != NULL) {
		// collect new user data.		
		$menu_name 								= NULL; 
		if (isset($_POST["menu_name"])) { 
			$menu_name 							= $_POST["menu_name"]; 
			$menu_name 							= stripslashes($menu_name);
		}
		
		$menu_prompt 							= NULL; 
		if (isset($_POST["menu_prompt"])) {
			$menu_prompt 						= $_POST["menu_prompt"]; 
			$menu_prompt 						= stripslashes($menu_prompt);
		}
		
		$menu_timeout	 						= NULL; 
		if (isset($_POST["menu_timeout"])) {
			$menu_timeout 						= $_POST["menu_timeout"]; 
			$menu_timeout 						= stripslashes($menu_timeout);
		}

		$menu_timeout_prompt 					= NULL; 
		if (isset($_POST["menu_timeout_prompt"])) {
			$menu_timeout_prompt 				= $_POST["menu_timeout_prompt"]; 
			$menu_timeout_prompt 				= stripslashes($menu_timeout_prompt);
		}
		
		$menu_invalid_prompt 					= NULL; 
		if (isset($_POST["menu_invalid_prompt"])) { 
			$menu_invalid_prompt 				= $_POST["menu_invalid_prompt"]; 
			$menu_invalid_prompt 				= stripslashes($menu_invalid_prompt);
		}	

		$menu_repeat 							= NULL; 
		if (isset($_POST["menu_repeat"])) { 
			$menu_repeat 						= $_POST["menu_repeat"]; 
			$menu_repeat 						= stripslashes($menu_repeat);
		}

		$menu_time_check 						= NULL; 
		if (isset($_POST["menu_time_check"])) { 
			$menu_time_check 					= $_POST["menu_time_check"]; 
			$menu_time_check 					= stripslashes($menu_time_check);
		}

		$call_time_id 							= NULL; 
		if (isset($_POST["call_time_id"])) { 
			$call_time_id 						= $_POST["call_time_id"]; 
			$call_time_id 						= stripslashes($call_time_id);
		}

		$track_in_vdac 							= NULL; 
		if (isset($_POST["track_in_vdac"])) { 
			$track_in_vdac 						= $_POST["track_in_vdac"]; 
			$track_in_vdac 						= stripslashes($track_in_vdac);
		}

		$tracking_group 						= NULL; 
		if (isset($_POST["tracking_group"])) { 
			$tracking_group 					= $_POST["tracking_group"]; 
			$tracking_group 					= stripslashes($tracking_group);
		}

		$user_group 							= NULL; 
		if (isset($_POST["user_group"])) { 
			$user_group 						= $_POST["user_group"]; 
			$user_group 						= stripslashes($user_group);
		}

		// options
		$route_option 							= $_POST['option'];
		$route_menu 							= $_POST['route_menu'];
		$route_desc 							= $_POST['route_desc'];
		
		$option_callmenu_value 					= $_POST['option_callmenu_value'];
		$option_ingroup_value 					= $_POST['option_ingroup_value'];
		$option_did_value 						= $_POST['option_did_value'];
		$option_hangup_value 					= $_POST['option_hangup_value'];
		$option_extension_value 				= $_POST['option_extension_value'];
		$option_phone_value 					= $_POST['option_phone_value'];
		$option_voicemail_value 				= $_POST['option_voicemail_value'];
		$option_agi_value 						= $_POST['option_agi_value'];
		
		$option_route_context 					= $_POST['option_route_value_context'];
		
		$route_value 							= "";
		
		for ($i=0; $i < 14; $i++) {
			if($route_menu[$i] == "CALLMENU"){
				$route_value 					.= $option_callmenu_value[$i];
			}
			
			if($route_menu[$i] == "INGROUP"){
				$route_value 					.= $option_ingroup_value[$i];
			}
			
			if($route_menu[$i] == "DID"){
				$route_value 					.= $option_did_value[$i];
			}
			
			if($route_menu[$i] == "HANGUP"){
				$route_value 					.= $option_hangup_value[$i];
			}
			
			if($route_menu[$i] == "EXTENSION"){
				$route_value 					.= $option_extension_value[$i];
			}
			
			if($route_menu[$i] == "PHONE"){
				$route_value 					.= $option_phone_value[$i];
			}
			
			if($route_menu[$i] == "VOICEMAIL"){
				$route_value 					.= $option_voicemail_value[$i];
			}
			
			if($route_menu[$i] == "AGI"){
				$route_value 					.= $option_agi_value[$i];
			}
			
			$route_value 						.= "+";
			
		}
		//echo $route_value;
		$option_route_value 					= explode("+", $route_value);
		
		$ingroup_context 						= "";
		
		for ($i=0; $i < 14; $i++) {
			if ($route_menu[$i] == "INGROUP") {
				$ingroup_context 				.= $_POST['handle_method_'.$i].",";
				$ingroup_context 				.= $_POST['search_method_'.$i].",";
				$ingroup_context 				.= $_POST['list_id_'.$i].",";
				$ingroup_context 				.= $_POST['campaign_id_'.$i].",";
				$ingroup_context 				.= $_POST['phone_code'.$i].",";
				$ingroup_context 				.= $_POST['enter_filename_'.$i].",";
				$ingroup_context 				.= $_POST['id_number_filename_'.$i].",";
				$ingroup_context 				.= $_POST['confirm_filename_'.$i].",";
				$ingroup_context 				.= $_POST['vid_digits_'.$i];
				
				$option_route_context[$i] 		= $ingroup_context;
				$ingroup_context 				= "";
			}
		}
		
		$items 									= "";
		
		for ($i=0;$i < count($route_option);$i++) {
			if($route_option[$i] == "A") $route_option[$i] = '#';
			if($route_option[$i] == "B") $route_option[$i] = '*';
			if($route_option[$i] == "C") $route_option[$i] = 'TIMECHECK';
			if($route_option[$i] == "D") $route_option[$i] = 'TIMEOUT';
			if($route_option[$i] == "E") $route_option[$i] = 'INVALID';
			
			$items 								.= $route_option[$i]."+".$route_desc[$i]."+".$route_menu[$i]."+".$option_route_value[$i]."+".$option_route_context[$i];
			$items 								.= "|";
		}

		$postfields 							= array(
			'goAction' 								=> 'goEditIVR',
			'menu_id' 								=> $ivr, 
			'menu_name' 							=> $menu_name, 
			'menu_prompt' 							=> $menu_prompt, 
			'menu_timeout' 							=> $menu_timeout, 
			'menu_timeout_prompt' 					=> $menu_timeout_prompt, 
			'menu_invalid_prompt' 					=> $menu_invalid_prompt, 
			'menu_repeat' 							=> $menu_repeat, 
			'menu_time_check' 						=> $menu_time_check, 
			'call_time_id' 							=> $call_time_id, 
			'track_in_vdac' 						=> $track_in_vdac, 
			'tracking_group' 						=> $tracking_group, 
			'user_group' 							=> $user_group,
			'items' 								=> $items
		);

		$output 								= $api->API_modifyIVR($postfields);
		
		if ($output->result=="success") { 
			$status 							= 1; 
		} else { 
			$status 							= $output->result; 
		}
		
		echo json_encode($status);
	}

	// PHONE NUMBER / DID
	if ($did != NULL) {
		// collect new user data.	
		$modify_did 							= NULL; 
		if (isset($_POST["modify_did"])) { 
			$modify_did 						= $_POST["modify_did"];
			$modify_did 						= stripslashes($modify_did);
		}

		$did_pattern 							= NULL; 
		if (isset($_POST["did_pattern"])) { 
			$did_pattern 						= $_POST["did_pattern"];
		}
		
		$desc 									= NULL; 
		if (isset($_POST["desc"])) { 
			$desc 								= $_POST["desc"]; 
			$desc 								= stripslashes($desc);
		}

		$route 									= NULL; 
		if (isset($_POST["route"])) { 
			$route 								= $_POST["route"]; 
			$route 								= stripslashes($route);
		}
		
		$status 								= NULL; 
		if (isset($_POST["status"])) { 
			$status 							= $_POST["status"]; 
			$status 							= stripslashes($status);
		}

		$filter_clean_cid_number 				= NULL; 
		if (isset($_POST["cid_num"])) { 
			$filter_clean_cid_number 			= $_POST["cid_num"]; 
			$filter_clean_cid_number 			= stripslashes($filter_clean_cid_number);
		}
		
		$list_id	 							= NULL; 
		if (isset($_POST["list_id"])) { 
			$list_id 							= $_POST["list_id"];
			$list_id 							= stripslashes($list_id);
		}
		
		$voicemail_ext = $_POST['route_voicemail'];
		$ext = $_POST['route_exten'];
		$ext_cont = $_POST['route_exten_context'];
		$phone = $_POST['route_phone_exten'];
		$ingroup = $_POST['route_ingroupid'];
		$defaultAD = $_POST['user_route_settings_ingroup'];
		$call_handle_method = $_POST['call_handle_method'];
		$agent_search_method = $_POST['agent_search_method'];

		if($route == "AGENT"){
			$voicemail_ext = $_POST['ru_voicemail'];
			$ext = $_POST['ru_exten'];
			$ext_cont = $_POST['ru_exten_context'];
			$phone = $_POST['ru_phone'];
			$ingroup = $_POST['ru_ingroup'];
		}else{
			$defaultAD = "AGENTDIRECT";
		}
		

		$postfields = array(
			'goAction' 						=> 'goEditDID',
			'did_id' 						=> $modify_did,
			'did_pattern' 						=> $did_pattern,
			'did_description' 					=> $desc,
			'did_route' 						=> $route,
			'did_active' 						=> $status,
			'filter_clean_cid_number' 				=> $filter_clean_cid_number,
			'user' 							=> $_POST['route_agentid'],
			'user_unavailable_action' 				=> $_POST['route_unavail'],
			'user_route_settings_ingroup' 				=> $defaultAD,
			'group_id' 						=> $ingroup,
			'phone' 						=> $phone,
			'server_ip' 						=> $_POST['route_phone_server'],
			'menu_id' 						=> $_POST['route_ivr'],
			'voicemail_ext' 					=> $voicemail_ext,
			'extension' 						=> $ext,
			'exten_context' 					=> $ext_cont,
			'list_id'						=> $list_id,
			'call_handle_method'			=> $call_handle_method,
			'agent_search_method'			=> $agent_search_method
		);				

		$output 								= $api->API_modifyDID($postfields);
		
		if ($output->result == "success") { 
			$status 							= 1; 
		} else { 
			$status 							= $output->result; 
		}
		
		echo json_encode($status);
	}
		
	
?>
