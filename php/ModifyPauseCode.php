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
	$api 							= \creamy\APIHandler::getInstance();

	// collect new user data.       
	$campaign_id					= $_POST["campaign_id"];

	$pause_code = NULL; if (isset($_POST["pause_code"])) { 
		$pause_code 				= $_POST["pause_code"]; 
		$pause_code 				= stripslashes($pause_code);
	}
	$pause_code_name = NULL; if (isset($_POST["pause_code_name"])) { 
		$pause_code_name			= $_POST["pause_code_name"];
		$pause_code_name 			= stripslashes($pause_code_name);
	}
	$billable = NULL; if (isset($_POST["billable"])) { 
		$billable 					= $_POST["billable"]; 
		$billable 					= stripslashes($billable);
	}

	$postfields 					= array(
		'goUser' 						=> goUser,
		'goPass' 						=> goPass,
		'goAction' 						=> 'goEditPauseCode',		
		'responsetype' 					=> responsetype,
		'pauseCampID' 					=> $campaign_id,
		'pause_code' 					=> $pause_code,
		'pause_code_name' 				=> $pause_code_name,
		'billable' 						=> $billable,
		'session_user' 					=> $_SESSION['user'],
		'log_user' 						=> $_SESSION['user'],
		'log_ip' 						=> $_SERVER['REMOTE_ADDR'],
		'hostname' 						=> $_SERVER['REMOTE_ADDR']
	);	
			
	$output 						= $api->API_modifyPauseCode($postfields);
	
	if ($output->result=="success") { $status = 1; } 
		else { $status = $output->result; }

	echo json_encode($status);
?>
