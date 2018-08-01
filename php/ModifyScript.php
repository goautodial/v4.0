<?php
/**
 * @file        ModifySscript.php
 * @brief       Handles modifying specific scripts
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A, Biscocho 
 * @author      Alexander Jim H. Abenoja
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
	
	$api 										= \creamy\APIHandler::getInstance();
        
	// collect new user data.       
	$modifyid 									= $_POST["modifyid"];
	$script_name 								= NULL;
	
	if ( isset($_POST["script_name"]) ) { 
		$script_name 							= $_POST["script_name"]; 
		$script_name 							= stripslashes($script_name);
	}
	
	$script_comments 							= NULL; 
	
	if ( isset($_POST["script_comments"]) ) { 
		$script_comments 						= $_POST["script_comments"];
		$script_comments 						= stripslashes($script_comments);
	}

	$script_text 								= NULL; 
	
	if ( isset($_POST["script_text"]) ) { 
		$script_text 							= $_POST["script_text_value"]; 
		$script_text 							= urldecode($script_text);
		//$script_text 							= $_POST["script_text"]; 
		//$script_text 							= stripslashes($script_text);
	}

	$active 									= "N"; 
	
	if ( isset($_POST["active"]) ) { 
		$active 								= $_POST["active"]; 
		$active 								= stripslashes($active);
	}

	$script_user_group 							= NULL; 
	
	if ( isset($_POST["script_user_group"]) ) { 
		$script_user_group 						= $_POST["script_user_group"]; 
		$script_user_group 						= stripslashes($script_user_group);
	}

	$postfields 								= array(
		"goAction" 									=> "goEditScript",		
		"script_id" 								=> $modifyid,
		"script_name" 								=> $script_name,
		"script_comments" 							=> $script_comments,
		"active" 									=> $active,		
		"script_text" 								=> $script_text,
		"user_group" 								=> $script_user_group
	);	
			
	$output 									= $api->API_editScript($postfields);	
	
	if ($output->result == "success") { 
		$status 								= 1; 
	} else { 
		$status 								= $output->result; 
	}
	
	echo json_encode($status);
?>
