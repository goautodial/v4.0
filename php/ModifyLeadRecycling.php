<?php
/**
 * @file        ModifyLeadRecycling.php
 * @brief       Handles modifying lead recycling requests
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
	$api 										= \creamy\APIHandler::getInstance();	

	// check required fields
	$reason 									= "Unable to Modify Disposition";	
	$modifyid 									= $_POST["edit_leadrecycling"];
	$campaign_id								= $_POST["edit_leadrecycling_campaign"];

	if ($modifyid != NULL) {		
		$attempt_delay 							= NULL; 
		if (isset($_POST["attempt_delay"])) { 
			$attempt_delay 						= $_POST["attempt_delay"]; 
			$attempt_delay 						= stripslashes($attempt_delay);
		}
		
		$attempt_maximum 						= NULL; 
		if (isset($_POST["attempt_maximum"])) { 
			$attempt_maximum 					= $_POST["attempt_maximum"];
			$attempt_maximum 					= stripslashes($attempt_maximum);
		}

		$active 								= NULL; 
		if (isset($_POST["active"])) { 
			$active 							= $_POST["active"]; 
			$active 							= stripslashes($active);
		}

		$postfields 						= array(
			'goAction' 							=> 'goEditLeadRecycling',		
			'recycle_id' 						=> $modifyid,
			'campaign_id'						=> $campaign_id,
			'attempt_delay'						=> $attempt_delay,
			'attempt_maximum' 					=> $attempt_maximum,
			'active' 							=> $active
		);				

		$output 							= $api->API_editLeadRecycling($postfields);

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
