<?php
/**
 * @file        ModifyTelephonyList.php
 * @brief       Handles Lists variables
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
	$reason 								= "Unable to Modify List";
	$validated 								= 1;
	
	if (!isset($_POST["modifyid"])) {
		$validated 							= 0;
	}

	if ($validated == 1) {

		// collect new user data.	
		$modifyid 							= $_POST["modifyid"];		
		$name 								= NULL; 
		
		if (isset($_POST["name"])) { 
			$name 							= $_POST["name"]; 
			$name 							= stripslashes($name);
		}
		
		$desc 								= NULL; 
		if (isset($_POST["desc"])) { 
			$desc 							= $_POST["desc"]; 
			$desc 							= stripslashes($desc);
		}
		
		$campaign 							= NULL; 
		if (isset($_POST["campaign"])) { 
			$campaign 						= $_POST["campaign"]; 
			$campaign 						= stripslashes($campaign);
		}
		
		$status 							= NULL; 
		if (isset($_POST["active"])) { 
			$status 						= $_POST["active"]; 
			$status 						= stripslashes($status);
		}
		$reset_list 						= NULL; 
		if (isset($_POST["reset_list"])) { 
			$reset_list 					= $_POST["reset_list"]; 
			$reset_list 					= stripslashes($reset_list);
		}

		$reset_time 						= NULL; 
		if (isset($_POST["reset_time"])) { 
			$reset_time 					= $_POST["reset_time"]; 
			$reset_time 					= stripslashes($reset_time);
		}

		$xferconf_a_number 					= NULL; 
		if (isset($_POST["xferconf_a_number"])) { 
			$xferconf_a_number 				= $_POST["xferconf_a_number"]; 
			$xferconf_a_number 				= stripslashes($xferconf_a_number);
		}

		$xferconf_b_number 					= NULL; 
		if (isset($_POST["xferconf_b_number"])) { 
			$xferconf_b_number 				= $_POST["xferconf_b_number"]; 
			$xferconf_b_number 				= stripslashes($xferconf_b_number);
		}

		$xferconf_c_number 					= NULL; 
		if (isset($_POST["xferconf_c_number"])) { 
			$xferconf_c_number 				= $_POST["xferconf_c_number"]; 
			$xferconf_c_number 				= stripslashes($xferconf_c_number);
		}

		$xferconf_d_number 					= NULL; 
		if (isset($_POST["xferconf_d_number"])) { 
			$xferconf_d_number 				= $_POST["xferconf_d_number"]; 
			$xferconf_d_number 				= stripslashes($xferconf_d_number);
		}

		$xferconf_e_number 					= NULL; 
		if (isset($_POST["xferconf_e_number"])) { 
			$xferconf_e_number 				= $_POST["xferconf_e_number"]; 
			$xferconf_e_number 				= stripslashes($xferconf_e_number);
		}

		$agent_script_override 				= NULL; 
		if (isset($_POST["agent_script_override"])) { 
			$agent_script_override 			= $_POST["agent_script_override"]; 
			$agent_script_override 			= stripslashes($agent_script_override);
		}

		$drop_inbound_group_override 		= NULL; 
		if (isset($_POST["drop_inbound_group_override"])) { 
			$drop_inbound_group_override 	= $_POST["drop_inbound_group_override"]; 
			$drop_inbound_group_override 	= stripslashes($drop_inbound_group_override);
		}

		$campaign_cid_override 				= NULL; 
		if (isset($_POST["campaign_cid_override"])) { 
			$campaign_cid_override 			= $_POST["campaign_cid_override"]; 
			$campaign_cid_override 			= stripslashes($campaign_cid_override);
		}

		$web_form 							= NULL; 
		if (isset($_POST["web_form"])) { 
			$web_form 						= $_POST["web_form"]; 
			$web_form 						= stripslashes($web_form);
		}

		$postfields 						= array(
			"goAction" 							=> "goEditList",
			"list_id" 							=> $modifyid,
			"list_name" 						=> $name,
			"list_description" 					=> $desc,
			"campaign_id" 						=> $campaign,
			"active" 							=> $status,
			"reset_list" 						=> $reset_list,
			"reset_time" 						=> $reset_time,
			"xferconf_a_number" 				=> $xferconf_a_number,
			"xferconf_b_number" 				=> $xferconf_b_number,
			"xferconf_c_number" 				=> $xferconf_c_number,
			"xferconf_d_number" 				=> $xferconf_d_number,
			"xferconf_e_number"					=> $xferconf_e_number,
			"agent_script_override" 			=> $agent_script_override,
			"drop_inbound_group_override" 		=> $drop_inbound_group_override,
			"campaign_cid_override" 			=> $campaign_cid_override,
			"web_form_address" 					=> $web_form
		);

		$output 							= $api->API_Request("goLists", $postfields);

		if ($output->result=="success") { 
			$status 						= 1; 
		} else { 
			$status 						= $output->result; 
		}

		echo json_encode($status);
		
	} else { print $reason; }
?>
