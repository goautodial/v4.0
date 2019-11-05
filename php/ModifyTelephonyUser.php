<?php
/**
 * @file        ModifyTelephonyUser.php
 * @brief       Modify user account
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

	require_once('APIHandler.php');
	$api 									= \creamy\APIHandler::getInstance();

	// check required fields
	$reason 								= "Unable to Modify Users";
	$validated 								= 1;
	if (!isset($_POST["modifyid"])) {
		$validated							= 0;
	}

	if ($validated == 1) {
		// collect new user data.	
		$modifyid 							= $_POST["modifyid"];
		$user 								= $_POST["user"];		
		$name								= NULL; 
		if (isset($_POST["fullname"])) { 
			$name 							= $_POST["fullname"]; 
			$name 							= stripslashes($name);
		}

		$email 								= NULL; 
		if (isset($_POST["email"])) { 
			$email 							= $_POST["email"]; 
			$email 							= stripslashes($email);
		}

		$user_group 						= NULL; 
		if (isset($_POST["usergroup"])) { 
			$user_group 					= $_POST["usergroup"]; 
			$user_group 					= stripslashes($user_group);
		}
		
		$status 							= NULL; 
		if (isset($_POST["status"])) { 
			$status 						= $_POST["status"]; 
			$status 						= stripslashes($status);
		}
		
		$user_level 						= NULL; 
		if (isset($_POST["userlevel"])) { 
			$user_level 					= $_POST["userlevel"]; 
			$user_level 					= stripslashes($user_level);
		}
		
		$voicemail 							= NULL; 
		if (isset($_POST["voicemail"])) { 
			$voicemail 						= $_POST["voicemail"]; 
			$voicemail 						= stripslashes($voicemail);
		}

		$hotkeys_active 					= NULL; 
		if (isset($_POST["hotkeys"])) { 
			$hotkeys_active 				= $_POST["hotkeys"]; 
			$hotkeys_active 				= stripslashes($hotkeys_active);
		}
		
		$pass 								= ""; 
		if (isset($_POST["password"])) { 
			$pass 							= $_POST["password"]; 
			$pass 							= stripslashes($pass);
		}

		$phone_login 						= NULL; 
		if (isset($_POST["phone_login"])) { 
			$phone_login 					= $_POST["phone_login"]; 
			$phone_login 					= stripslashes($phone_login);
		}

		$phone_pass 						= NULL; 
		if (isset($_POST["phone_password"])) { 
			$phone_pass 					= $_POST["phone_password"]; 
			$phone_pass 					= stripslashes($phone_pass);
		}
		
		$vdc_agent_api_access 				= NULL; 
		if (isset($_POST["api_access"])) { 
			$vdc_agent_api_access 			= $_POST["api_access"]; 
			$vdc_agent_api_access 			= stripslashes($vdc_agent_api_access);
		}
		$agent_choose_ingroups 				= NULL; 
		if (isset($_POST["choose_ingroup"])) { 
			$agent_choose_ingroups 			= $_POST["choose_ingroup"]; 
			$agent_choose_ingroups 			= stripslashes($agent_choose_ingroups);
		}

		$vicidial_recording_override 		= NULL; 
		if (isset($_POST["vicidial_recording_override"])) { 
			$vicidial_recording_override 	= $_POST["vicidial_recording_override"]; 
			$vicidial_recording_override 	= stripslashes($vicidial_recording_override);
		}
		
		$vicidial_transfers 				= NULL; 
		if (isset($_POST["vicidial_transfers"])) { 
			$vicidial_transfers 			= $_POST["vicidial_transfers"]; 
			$vicidial_transfers 			= stripslashes($vicidial_transfers);
		}
		
		$closer_default_blended 			= NULL; 
		if (isset($_POST["closer_default_blended"])) { 
			$closer_default_blended 		= $_POST["closer_default_blended"]; 
			$closer_default_blended 		= stripslashes($closer_default_blended);
		}
		
		$agentcall_manual 					= NULL; 
		if (isset($_POST["agentcall_manual"])) { 
			$agentcall_manual 				= $_POST["agentcall_manual"]; 
			$agentcall_manual 				= stripslashes($agentcall_manual);
		}
		
		$scheduled_callbacks 				= NULL; 
		if (isset($_POST["scheduled_callbacks"])) { 
			$scheduled_callbacks 			= $_POST["scheduled_callbacks"]; 
			$scheduled_callbacks 			= stripslashes($scheduled_callbacks);
		}
		
		$agentonly_callbacks 				= NULL; 
		if (isset($_POST["agentonly_callbacks"])) { 
			$agentonly_callbacks 			= $_POST["agentonly_callbacks"]; 
			$agentonly_callbacks 			= stripslashes($agentonly_callbacks);
		}
		
		$agent_lead_search_override 		= NULL; 
		if (isset($_POST["agent_lead_search_override"])) { 
			$agent_lead_search_override 	= $_POST["agent_lead_search_override"]; 
			$agent_lead_search_override 	= stripslashes($agent_lead_search_override);
		}
		
		$avatar 							= NULL; 
		if (isset($_POST["avatar"])) { 
			$avatar 						= $_POST["avatar"]; 
			//$avatar 						= stripslashes($avatar);
		}	
		
		$enable_webrtc 						= NULL; 
		if (isset($_POST["enable_webrtc"])) { 
			$enable_webrtc 					= $_POST["enable_webrtc"];
		}

		$postfields = array(
			"goAction" 						=> "goEditUser", #action performed by the [[API:Functions]]
			"user_id" 						=> $modifyid,
			"user" 							=> $user,
			"full_name" 					=> $name,
			"user_group" 					=> $user_group,
			"user_level" 					=> $user_level,
			"active"						=> $status,
			"voicemail_id" 					=> $voicemail,
			"email" 						=> $email,
			"pass" 							=> $pass,
			"phone_login" 					=> $phone_login,
			"phone_pass" 					=> $phone_pass,
			"hotkeys_active" 				=> $hotkeys_active,
			"agent_lead_search_override" 	=> $agent_lead_search_override,
			"avatar" 						=> $avatar,
			"vdc_agent_api_access"			=> $vdc_agent_api_access,
			"agent_choose_ingroups"			=> $agent_choose_ingroups,
			"vicidial_recording_override"	=> $vicidial_recording_override,
			"vicidial_transfers"			=> $vicidial_transfers,
			"closer_default_blended"		=> $closer_default_blended,
			"agentcall_manual"				=> $agentcall_manual,
			"scheduled_callbacks"			=> $scheduled_callbacks,
			"agentonly_callbacks"			=> $agentonly_callbacks,
			"enable_webrtc"					=> $enable_webrtc
		);

		$output = $api->API_Request("goUsers", $postfields);

		if ($output->result=="success") { $status = 1; } 
			else { $status = $output->result; }

		echo json_encode($status);
		
	} //else { ob_clean(); print $reason; }
?>
