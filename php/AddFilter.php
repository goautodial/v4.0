<?php
/**
 * @file        AddFilter.php
 * @brief       Handles Add Filter Request
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      Christopher P. Lomuntad
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
		"goAction" 									=> "goAddFilter",
		"lead_filter_id"							=> $_POST["filter_id"], 
		"lead_filter_name" 							=> $_POST["filter_name"], 
		"lead_filter_comments" 						=> $_POST["filter_comments"],
		"lead_filter_sql" 							=> $_POST["filter_sql"],
		"user_group"								=> $_POST["filter_user_group"]
	);
	
	$output 									= $api->API_addFilter($postfields);

	if ($output->result == "success") { 
		$status 								= 1; 
	} else { 
		$status 								= $output->result; 
	}
	
	echo json_encode($status);
?>
