<?php
/**
 * @file        AddCampaign.php
 * @brief       Add New Campaign
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Noel Umandap
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

	$api 										= \creamy\APIHandler::getInstance();
	$call_route									= $_POST['call_route'] ?? '';
	$dial_prefix								= $_POST['dial_prefix'] ?? 9;
	$auto_dial_level							= $_POST['auto_dial_level'] ?? 'OFF';

	$lead_file									= null;
	$leads										= null;
	$uploaded_wav								= null;
	$custom_dial_prefix							= 0;
	$postValue                                  = static fn(string $key, mixed $default = '') => $_POST[$key] ?? $default;

	if ( $dial_prefix == "CUSTOM") {
		$custom_dial_prefix						= $postValue('custom_prefix', 0);
	}

    switch ($call_route){
	        case "INGROUP":
	            $call_route_text			 		= $postValue('ingroup_text');
	        break;

	        case "IVR":
	            $call_route_text			 		= $postValue('ivr_text');
	        break;

	        case "AGENT":
	            $call_route_text 					= $postValue('agent_text');
	        break;

	        case "VOICEMAIL":
	            $call_route_text		 			= $postValue('voicemail_text');
	        break;

			default:
				$call_route_text					= '';
			break;
    }

	if ( !empty($_FILES["lead_file"]["name"]) ) {
		$lead_file								= curl_file_create( $_FILES['lead_file']['tmp_name'], $_FILES['lead_file']['type'], $_FILES["lead_file"]["name"] );
	}

	if ( !empty($_FILES["leads"]["name"]) ) {
		$leads 									= curl_file_create( $_FILES['leads']['tmp_name'], $_FILES['leads']['type'], $_FILES["leads"]["name"] );
	}

    if ( !empty($_FILES["uploaded_wav"]["name"]) ) {
		$uploaded_wav 							= curl_file_create( $_FILES['uploaded_wav']['tmp_name'], $_FILES['uploaded_wav']['type'], $_FILES["uploaded_wav"]["name"] );
	}

	$postfields 								= [
		'goAction' 									=> 'goAddCampaign',
		'campaign_id'								=> $postValue('campaign_id'),
		'campaign_name' 							=> $postValue('campaign_name'),
		'campaign_type'								=> $postValue('campaign_type'),
		'did_tfn_extension' 						=> $postValue('did_tfn_extension'),
		'call_route'								=> $call_route,
		'call_route_text' 							=> $call_route_text,
		'group_color' 								=> $postValue('group_color'),
		'survey_type' 								=> $postValue('survey_type'),
		'no_channels'								=> $postValue('no_channels'),
		'copy_from_campaign'						=> $postValue('copy_from_campaign'),
		'list_id'									=> $postValue('list_id'),
		'country'									=> $postValue('country'),
		'check_for_duplicates' 						=> $postValue('check_for_duplicates'),
		'dial_method' 								=> $postValue('dial_method'),
		'auto_dial_level'							=> $auto_dial_level,
		'auto_dial_level_adv' 						=> $postValue('auto_dial_level_adv'),
		'dial_prefix' 								=> $dial_prefix,
		'custom_dial_prefix' 						=> $custom_dial_prefix,
		'description' 								=> $postValue('description'),
		'status' 									=> $postValue('status'),
		'script' 									=> $postValue('script'),
		'answering_machine_detection' 				=> $postValue('answering_machine_detection'),
		'caller_id'									=> $postValue('caller_id'),
		'force_reset_hopper' 						=> $postValue('force_reset_hopper'),
		'campaign_recording' 						=> $postValue('campaign_recording'),
		'lead_file' 								=> $lead_file,
		'leads' 									=> $leads,
		'uploaded_wav'								=> $uploaded_wav
	];

	$output 									= $api->API_addCampaign($postfields);
	$home 										= $_SERVER['HTTP_REFERER'];

	header('Content-Type: application/json');

	if ($output->result=="success") {
		$response 							= [
			'status' 	=> 1,
			'result' 	=> 'success',
			'message' 	=> 'Success'
		];
	} else {
		$response 							= [
			'status' 	=> 0,
			'result' 	=> $output->result,
			'message' 	=> $output->result
		];
	}

	echo json_encode($response);

?>
