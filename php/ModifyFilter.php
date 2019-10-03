<?php
/**
 * @file        ModifyFilter.php
 * @brief       Handles modifying specific filters
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author		Christopher P. Lomuntad
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
	$filter_name 								= NULL;
	
	if ( isset($_POST["filter_name"]) ) { 
		$filter_name 							= $_POST["filter_name"]; 
		$filter_name 							= stripslashes($filter_name);
	}
	
	$filter_comments 							= NULL; 
	
	if ( isset($_POST["filter_comments"]) ) { 
		$filter_comments 						= $_POST["filter_comments"];
		$filter_comments 						= stripslashes($filter_comments);
	}

	$filter_sql 								= NULL; 
	
	if ( isset($_POST["filter_sql"]) ) { 
		$filter_sql 							= $_POST["filter_sql"]; 
		//$filter_sql 							= urldecode($filter_sql);
	}

	$filter_user_group 							= NULL; 
	
	if ( isset($_POST["filter_user_group"]) ) { 
		$filter_user_group 						= $_POST["filter_user_group"]; 
		$filter_user_group 						= stripslashes($filter_user_group);
	}

	$postfields 								= array(
		"goAction" 									=> "goEditFilter",		
		"lead_filter_id" 							=> $modifyid,
		"lead_filter_name" 							=> $filter_name,
		"lead_filter_comments" 						=> $filter_comments,
		"lead_filter_sql" 							=> $filter_sql,
		"user_group" 								=> $filter_user_group
	);	
			
	$output 									= $api->API_editFilter($postfields);	
	
	if ($output->result == "success") { 
		$status 								= 1; 
	} else { 
		$status 								= $output->result; 
	}
	
	echo json_encode($status);
?>
