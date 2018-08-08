<?php
/**
 * @file        DeleteInbound.php
 * @brief       Handles Delete Ingroup, IVR & DID Requests
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

	require_once('CRMDefaults.php');
	require_once('APIHandler.php');
	
	$api 										= \creamy\APIHandler::getInstance();	
	$id 										= NULL;

	
	if (isset($_POST["groupid"])) {
		$id 									= $_POST["groupid"];
		$type									= "ingroup";
	}
	
	if (isset($_POST["ivr"])) {
		$id 									= $_POST["ivr"];
		$type									= "ivr";
	}
	
	if (isset($_POST["modify_did"])) {
		$id 									= $_POST["modify_did"];
		$type									= "did";
	}

	// IF INGROUP IS DELETED
	if ($id != NULL) {
	
		$postfields 							= array(
			'goAction' 								=> 'goDeleteInbound',
			'inbound_id' 							=> $id,
			'type'									=> $type
		);

		$output	 								= $api->API_Request("goInbound", $postfields);

		if ($output->result=="success") { 
			$status 							= 1; 
		} else { 
			$status 							= $output->result; 
		}
	}

	echo json_encode($status);
?>
