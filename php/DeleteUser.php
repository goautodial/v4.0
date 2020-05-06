<?php
/**
 * @file        DeleteUser.php
 * @brief       Handles Delete User/s Requests
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A, Biscocho 
 * @author      Alexander Jim Abenoja
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
	
	if (isset($_POST["userid"])) {
		// sanity checks	
		$userid 							= $_POST["userid"];
		$action 							= $_POST["action"];
		
		$postfields 						= array(
			'goAction' 							=> 'goDeleteUser',
			'user_id' 							=> $userid,
			'action' 							=> $action
		);

		$output 							= $api->API_Request("goUsers", $postfields);
			
		if ($output->result=="success") { $status = 1; } 
			else { $status = $output->result; }
		
		echo json_encode($status);
	}
?>
