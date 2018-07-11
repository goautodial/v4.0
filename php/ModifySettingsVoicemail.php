<?php
/**
 * @file        ModifySettingsVoicemail.php
 * @brief       API to handle voicemail variables
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

	require_once("APIHandler.php");
	$api 								= \creamy\APIHandler::getInstance();;

	// check required fields
	$validated 							= 1;
	if (!isset($_POST["modifyid"])) {
		$validated 						= 0;
	}

	if ($validated == 1) {		
		// collect new user data.	
		$modifyid 						= $_POST["modifyid"];
		
		$pass = NULL; if (isset($_POST["password"])) { 
			$pass 						= $_POST["password"]; 
			$pass 						= stripslashes($pass);
		}
		
		$fullname = NULL; if (isset($_POST["fullname"])) { 
			$fullname 					= $_POST["fullname"];
			$fullname 					= stripslashes($fullname);
		}

		$email = NULL; if (isset($_POST["email"])) { 
			$email 						= $_POST["email"]; 
			$email 						= stripslashes($email);
		}
		
		$active = NULL; if (isset($_POST["active"])) { 
			$active 					= $_POST["active"]; 
			$active 					= stripslashes($active);
		}
		
		$delete_vm_after_email = NULL; if (isset($_POST["delete_vm_after_email"])) { 
			$delete_vm_after_email	 	= $_POST["delete_vm_after_email"]; 
			$delete_vm_after_email		= stripslashes($delete_vm_after_email);
		}  
		
		$postfields 					= array(
			"goUser" 						=> goUser,
			"goPass" 						=> goPass,
			"goAction" 						=> "goEditVoicemail",		
			"responsetype" 					=> responsetype,
			"voicemail_id" 					=> $modifyid,
			"pass" 							=> $pass,
			"fullname" 						=> $fullname,
			"email" 						=> $email,
			"active" 						=> $active,
			"delete_vm_after_email" 		=> $delete_vm_after_email,
			"session_user" 					=> $_POST["log_user"],
			"log_user" 						=> $_POST["log_user"],
			"log_ip" 						=> $_SERVER["REMOTE_ADDR"],
			"hostname" 						=> $_SERVER["REMOTE_ADDR"]
		);				

		$output 						= $api->API_editVoicemail($postfields);
		
		if ($output->result=="success") { 
			$status 					= 1; 
		} else { 
			$status 					= $output->result; 
		}

		echo json_encode($status);
	}
?>
