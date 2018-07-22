<?php
/**
 * @file        ModifyPauseCode.php
 * @brief       Handles modifying pause codes requests
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho 
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

	require_once('APIHandler.php');
	$api 									= \creamy\APIHandler::getInstance();

	// check required fields
	$reason 								= "Unable to Modify Disposition";
	$disposition 							= $_POST["edit_campaign"];

	// DISPOSITION
	if ($disposition != NULL) {
		if (!isset($_POST['selectable'])) {
			$_POST['selectable'] 			= "N";
		} else {
			$_POST['selectable'] 			= "Y";
		}

		if (!isset($_POST['human_answered'])) {
			$_POST['human_answered'] 		= "N";
		} else {
			$_POST['human_answered'] 		= "Y";
		}

		if (!isset($_POST['sale'])) {
			$_POST['sale'] 					= "N";
		} else {
			$_POST['sale'] 					= "Y";
		}

		if (!isset($_POST['dnc'])) {
			$_POST['dnc'] 					= "N";
		} else {
			$_POST['dnc'] 					= "Y";
		}

		if (!isset($_POST['scheduled_callback'])) {
			$_POST['scheduled_callback'] 	= "N";
		} else {
			$_POST['scheduled_callback'] 	= "Y";
		}

		if (!isset($_POST['customer_contact'])) {
			$_POST['customer_contact'] 		= "N";
		} else {
			$_POST['customer_contact'] 		= "Y";
		}

		if (!isset($_POST['not_interested'])) {
			$_POST['not_interested'] 		= "N";
		} else {
			$_POST['not_interested'] 		= "Y";
		}

		if (!isset($_POST['unworkable'])) {
			$_POST['unworkable'] 			= "N";
		} else {
			$_POST['unworkable'] 			= "Y";
		}
			
		$status								= NULL; 
		if (isset($_POST["status"])) { 
			$status 						= $_POST["status"]; 
			$status 						= stripslashes($status);
		}
		
		$status_name 						= NULL; 
		if (isset($_POST["status_name"])) { 
			$status_name 					= $_POST["status_name"]; 
			$status_name 					= stripslashes($status_name);
		}
		
		$postfields 						= array(
			'goAction' 							=> 'goEditDisposition',		
			'campaign_id' 						=> $disposition,
			'status'							=> $status,
			'status_name' 						=> $status_name,
			'selectable' 						=> $_POST['selectable'],
			'human_answered' 					=> $_POST['human_answered'],
			'sale' 								=> $_POST['sale'],
			'dnc' 								=> $_POST['dnc'],
			'scheduled_callback' 				=> $_POST['scheduled_callback'],
			'customer_contact' 					=> $_POST['customer_contact'],
			'not_interested' 					=> $_POST['not_interested'],
			'unworkable' 						=> $_POST['unworkable']
		);				

		$output 							= $api->API_editDisposition($postfields);

		if ($output->result=="success") {
			$status 						= 1; 
		} else { 
			$status 						= $output->result; 
		}

		echo json_encode($status);
		
	} else {
		echo $reason;
	}
?>
