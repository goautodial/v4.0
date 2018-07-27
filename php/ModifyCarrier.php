<?php
/**
 * @file        ModifyCarrier.php
 * @brief       
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A, Biscocho
 * @author      Alexander Jim H. Abenoja
 * @author		Jerico James F. Milo
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

	//require_once('CRMDefaults.php');
	//require_once('goCRMAPISettings.php');
	require_once('APIHandler.php');
	$api = \creamy\APIHandler::getInstance();
	
	// check required fields
	$reason = "Unable to Modify Carrier";

	$validated = 1;
	if (!isset($_POST["modifyid"])) {
		$validated = 0;
	}

	if ($validated == 1) {
		
		// collect new user data.	
		$modifyid = $_POST["modifyid"];
		
		$carrier_name = NULL; if (isset($_POST["carrier_name"])) { 
			$carrier_name = $_POST["carrier_name"]; 
			$carrier_name = stripslashes($carrier_name);
		}
		
		$carrier_description = NULL; if (isset($_POST["carrier_description"])) { 
			$carrier_description = $_POST["carrier_description"];
			$carrier_description = stripslashes($carrier_description);
		}

		$protocol = NULL; if (isset($_POST["protocol"])) { 
			$protocol = $_POST["protocol"]; 
			$protocol = stripslashes($protocol);
		}
		
		$server_ip = NULL; if (isset($_POST["server_ip"])) { 
			$server_ip = $_POST["server_ip"]; 
			$server_ip = stripslashes($server_ip);
		}

		$active = NULL; if (isset($_POST["active"])) { 
			$active = $_POST["active"]; 
			$active = stripslashes($active);
		}
		
		$registration_string = NULL; if (isset($_POST["registration_string"])) { 
			$registration_string = $_POST["registration_string"]; 
			$registration_string = stripslashes($registration_string);
		}
		
		$account_entry = NULL; if (isset($_POST["account_entry"])) { 
			$account_entry = $_POST["account_entry"]; 
			$account_entry = stripslashes($account_entry);
		}
		
		$globals_string = NULL; if (isset($_POST["globals_string"])) { 
			$globals_string = $_POST["globals_string"]; 
			$globals_string = stripslashes($globals_string);
		}
		
		$dialplan_entry = NULL; if (isset($_POST["dialplan_entry"])) { 
			$dialplan_entry = $_POST["dialplan_entry"]; 
			$dialplan_entry = stripslashes($dialplan_entry);
		}
		
		$postfields = array(
			'goUser' => goUser,
			'goPass' => goPass,
			'goAction' => 'goEditCarrier',		
			'responsetype' => responsetype,
			'carrier_id' => $modifyid,
			'carrier_name' => $carrier_name,
			'carrier_description' => $carrier_description,
			'protocol' => $protocol,
			'server_ip' => $server_ip,
			'registration_string' => $registration_string,
			'account_entry' => $account_entry,
			'globals_string' => $globals_string,
			'dialplan_entry' => $dialplan_entry,
			'active' => $active,
			'session_user' => $_POST['log_user'],
			'log_user' => $_POST['log_user'],
			'log_ip' => $_SERVER['REMOTE_ADDR']
		);				

		$output = $api->API_editCarrier($postfields);

		if ($output->result=="success") { $status = 1; } 
			else { $status = $output->result; }

		echo json_encode($status);
		
	} else { 
		//ob_clean(); 
		print $reason; 
	}
?>
