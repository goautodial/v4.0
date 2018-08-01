<?php
/**
 * @file        AddScript.php
 * @brief       Handles Add Script Request
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      Alexander Jim H. Abenoja
 * @author		Demian Lizandro A. Biscocho
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

	$postfields 								= array(
		"goAction" 									=> "goAddScript",
		"script_id"									=> $_POST["script_id"], 
		"script_name" 								=> $_POST["script_name"], 
		"script_comments" 							=> $_POST["script_comments"],
		"script_text" 								=> urldecode($_POST["script_text_value"]),
		"user_group"								=> $_POST["script_user_group"],
		"active" 									=> $_POST["active"]
	);
	
	$output 									= $api->API_addScript($postfields);

	if ($output->result == "success") { 
		$status 								= 1; 
	} else { 
		$status 								= $output->result; 
	}
	
	echo json_encode($status);
?>
