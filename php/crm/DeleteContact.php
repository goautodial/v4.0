<?php
/**
 * @file        DeleteContact.php
 * @brief       Handles Delete Lead/Contact Requests
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      Alexander Jim H. Abenoja  <alex@goautodial.com>
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

	$validated 									= 1;
	if (!isset($_POST["leadid"])) {
		$validated 								= 0;
	}

	if ($validated == 1) {
		$postfields 							= array(
			"goAction" 								=> "goDeleteLead",
			"lead_id" 								=> $_POST["leadid"]
		);

		$output 								= $api->API_Request("goGetLeads", $postfields);

		if ($output->result=="success") { 
			$status 							= 1; 
		} else { 
			$status 							= $output->result; 
		}

		echo json_encode($status);

	} else {
		echo "Error: Please Contact System Administrator.";
	}
?>
