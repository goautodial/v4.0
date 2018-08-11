<?php
/**
 * @file        ModifyAgentRank.php
 * @brief       Handles Modify Requests for Agent Ranks
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      Alexander Jim Abenoja
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

	require_once('APIHandler.php');

	$api 										= \creamy\APIHandler::getInstance();
	$reason 									= "Unable to Modify Agent Rank";
	$validated 									= 1;
	
	if (!isset($_POST["idgroup"])) {
		$validated 								= 0;
	}

	if ($validated == 1) {		
		// collect new user data.	
		$modifyid 								= $_POST['idgroup'];
		$itemrank 								= $_POST['itemrank'];

		$postfields 							= array(
			'goAction' 								=> 'goEditAgentRank',
			'idgroup' 								=> $modifyid, 
			'itemrank' 								=> $itemrank
		);				

		$output 								= $api->API_modifyAgentRank($postfields);
		
		if ($output->result=="success") { 
			$status 							= 1; 
		} else { 
			$status 							= $output->result; 
		}		
		
		echo json_encode($status);
		
	} else { 
		//ob_clean(); 
		print $reason; 
	}
?>
