<?php
/**
 * @file        AddHotkey.php
 * @brief       Handles Add Hotkey Request
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Noel Umandap
 * @author      Alexander Jim Abenoja  <alex@goautodial.com>
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

	require_once(__DIR__ . '/APIHandler.php');

	$api 											= \creamy\APIHandler::getInstance();
	$route_option 									= is_array($_POST['option'] ?? null) ? $_POST['option'] : [];
	$route_desc 									= is_array($_POST['route_desc'] ?? null) ? $_POST['route_desc'] : [];
	$route_menu 									= is_array($_POST['route_menu'] ?? null) ? $_POST['route_menu'] : [];
	$option_callmenu_value 							= is_array($_POST['option_callmenu_value'] ?? null) ? $_POST['option_callmenu_value'] : [];
	$option_ingroup_value 							= is_array($_POST['option_ingroup_value'] ?? null) ? $_POST['option_ingroup_value'] : [];
	$option_did_value 								= is_array($_POST['option_did_value'] ?? null) ? $_POST['option_did_value'] : [];
	$option_hangup_value 							= is_array($_POST['option_hangup_value'] ?? null) ? $_POST['option_hangup_value'] : [];
	$option_extension_value 						= is_array($_POST['option_extension_value'] ?? null) ? $_POST['option_extension_value'] : [];
	$option_phone_value 							= is_array($_POST['option_phone_value'] ?? null) ? $_POST['option_phone_value'] : [];
	$option_voicemail_value 						= is_array($_POST['option_voicemail_value'] ?? null) ? $_POST['option_voicemail_value'] : [];
	$option_agi_value 								= is_array($_POST['option_agi_value'] ?? null) ? $_POST['option_agi_value'] : [];
	$option_route_context 							= is_array($_POST['option_route_value_context'] ?? null) ? $_POST['option_route_value_context'] : [];
	//$option_route_context_post = array_filter($option_route_context_post);

	$route_value 									= "";
	$route_count = count($route_menu);
	for ($i=0; $i < $route_count; $i++) {
		$current_route_menu = $route_menu[$i] ?? '';
		if ($current_route_menu == "CALLMENU") {
			$route_value 							.= $option_callmenu_value[$i] ?? '';
		}
		if ($current_route_menu == "INGROUP") {
			$route_value 							.= $option_ingroup_value[$i] ?? '';
		}
		if ($current_route_menu == "DID") {
			$route_value 							.= $option_did_value[$i] ?? '';
		}
		if ($current_route_menu == "HANGUP") {
			$route_value 							.= $option_hangup_value[$i] ?? '';
		}
		if ($current_route_menu == "EXTENSION") {
			$route_value 							.= $option_extension_value[$i] ?? '';
		}
		if ($current_route_menu == "PHONE") {
			$route_value 							.= $option_phone_value[$i] ?? '';
		}
		if ($current_route_menu == "VOICEMAIL") {
			$route_value 							.= $option_voicemail_value[$i] ?? '';
		}
		if ($current_route_menu == "AGI") {
			$route_value 							.= $option_agi_value[$i] ?? '';
		}
		$route_value 								.= "+";
	}
	//echo $route_value;
	$option_route_value 							= explode("+", (string) ($route_value ?? ''));

	$ingroup_context 								= "";
	for ($i=0; $i < $route_count; $i++) {
		if (($route_menu[$i] ?? '') == "INGROUP") {
			$ingroup_context 						.= ($_POST['handle_method_'.$i] ?? '').",";
			$ingroup_context 						.= ($_POST['search_method_'.$i] ?? '').",";
			$ingroup_context 						.= ($_POST['list_id_'.$i] ?? '').",";
			$ingroup_context 						.= ($_POST['campaign_id_'.$i] ?? '').",";
			$ingroup_context 						.= ($_POST['phone_code'.$i] ?? '').",";
			$ingroup_context 						.= ($_POST['enter_filename_'.$i] ?? '').",";
			$ingroup_context 						.= ($_POST['id_number_filename_'.$i] ?? '').",";
			$ingroup_context 						.= ($_POST['confirm_filename_'.$i] ?? '').",";
			$ingroup_context 						.= ($_POST['vid_digits_'.$i] ?? '');

			$option_route_context[$i] 				= $ingroup_context;
			$ingroup_context 						= "";
		}
	}

	$items 											= "";
    $counter = (is_countable($route_option) ? count($route_option) : 0);
	for ($i=0;$i < $counter;$i++) {
		if ($route_option[$i] == "A") $route_option[$i] = '#';
		if ($route_option[$i] == "B") $route_option[$i] = '*';
		if ($route_option[$i] == "C") $route_option[$i] = 'TIMECHECK';
		if ($route_option[$i] == "D") $route_option[$i] = 'TIMEOUT';
		if ($route_option[$i] == "E") $route_option[$i] = 'INVALID';
		$items 										.= ($route_option[$i] ?? '')."+".($route_desc[$i] ?? '')."+".($route_menu[$i] ?? '')."+".($option_route_value[$i] ?? '')."+".($option_route_context[$i] ?? '');
		$items 										.= "|";
	}

	$postfields 									= [
		'goAction' 										=> 'goAddIVR',
		'menu_id' 										=> ($_POST['menu_id'] ?? ''),
		'menu_name' 									=> ($_POST['menu_name'] ?? ''),
		'user_group' 									=> ($_POST['user_groups'] ?? ''),
		'menu_prompt' 									=> ($_POST['menu_prompt'] ?? ''),
		'menu_timeout' 									=> ($_POST['menu_timeout'] ?? ''),
		'menu_timeout_prompt' 							=> ($_POST['menu_timeout_prompt'] ?? ''),
		'menu_invalid_prompt' 							=> ($_POST['menu_invalid_prompt'] ?? ''),
		'menu_repeat' 									=> ($_POST['menu_repeat'] ?? ''),
		'postfields' 									=> ($_POST['menu_time_check'] ?? ''),
		'call_time_id' 									=> ($_POST['call_time_id'] ?? ''),
		'track_in_vdac' 								=> ($_POST['track_in_vdac'] ?? ''),
		'custom_dialplan_entry' 						=> ($_POST['custom_dialplan_entry'] ?? ''),
		'tracking_group' 								=> ($_POST['tracking_group'] ?? ''),
		'items' 										=> $items
	];

	$output 										= $api->API_addIVR($postfields);

	if ($output->result=="success") {
		$status 									= 1;
	} else {
		$status 									= $output->result;
	}

	echo json_encode($status);

?>
