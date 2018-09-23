<?php
/**
 * @file        ModifySettingsPhones.php
 * @brief       Modify specific phone extension
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


	require_once('CRMDefaults.php');
	require_once('goCRMAPISettings.php');
	require_once('APIHandler.php');
	$api = \creamy\APIHandler::getInstance();	

	// check required fields
	$reason = "Unable to Modify Phones";

	$validated = 1;
	if (!isset($_POST["modifyid"])) {
		$validated = 0;
	}

	if ($validated == 1) {
		
		// collect new user data.	
		$modifyid = $_POST["modifyid"];
		
		$dialplan = NULL; if (isset($_POST["dialplan"])) { 
			$dialplan = $_POST["dialplan"]; 
			$dialplan = stripslashes($dialplan);
		}
		
		$vmid = NULL; if (isset($_POST["vmid"])) { 
			$vmid = $_POST["vmid"];
			$vmid = stripslashes($vmid);
		}

		$ip = NULL; if (isset($_POST["ip"])) { 
			$ip = $_POST["ip"]; 
			$ip = stripslashes($ip);
		}
		
		$active = NULL; if (isset($_POST["active"])) { 
			$active = $_POST["active"]; 
			$active = stripslashes($active);
		}
		
		$status = NULL; if (isset($_POST["status"])) { 
			$status = $_POST["status"]; 
			$status = stripslashes($status);
		}
		
		$fullname = NULL; if (isset($_POST["fullname"])) { 
			$fullname = $_POST["fullname"]; 
			$fullname = stripslashes($fullname);
		}
		
		$protocol = NULL; if (isset($_POST["protocol"])) { 
			$protocol = $_POST["protocol"]; 
			$protocol = stripslashes($protocol);
		}
	
		$password = NULL; if (isset($_POST["password"])) { 
			$password = $_POST["password"]; 
			$password = stripslashes($password);
		}
		
		$postfields = array(
			'goAction' 			=> 'goEditPhone',		
			'extension' 		=> $modifyid,
			'dialplan_number' 	=> $dialplan,
			'voicemail_id' 		=> $vmid,			
			'server_ip' 		=> $ip,
			'active' 			=> $active,						
			'status'			=> $status,
			'fullname' 			=> $fullname,
			'protocol' 			=> $protocol,
			'pass' 				=> $password
		);				

		$output = $api->API_editPhone($postfields);

	if ($output->result=="success") { $status = 1; } 
		else { $status = $output->result; }
	
	echo json_encode($status);
		
	} else { 
		//ob_clean(); 
		print $reason; 
	}
	
?>
