<?php
/**
 * @file        salestracker.php
 * @brief       Handles report requests
 * @copyright   Copyright (c) 2018 GOautodial Inc.
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

	require_once('APIHandler.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);	
	$api 										= \creamy\APIHandler::getInstance();
	$fromDate 									= date('Y-m-d 00:00:01');
	$toDate 									= date('Y-m-d 23:59:59');
	$campaign_id 								= NULL;
	$request									= NULL;
	$userID										= NULL;
	$userGroup									= NULL;
	$statuses									= NULL;
			
	if (isset($_POST["fromDate"])) {
		$fromDate = date('Y-m-d H:i:s', strtotime($_POST['fromDate']));
	}
	
	if ($_POST["toDate"] != "" && $_POST["fromDate"] != "") {
		$toDate = date('Y-m-d H:i:s', strtotime($_POST['toDate']));
	}
	
			
	if (isset($_POST["campaignID"])) { 
		$campaign_id = $_POST["campaignID"]; 
		$campaign_id = stripslashes($campaign_id);
	}
		
	if (isset($_POST["request"])) {
		$request = $_POST["request"];
		$request = stripslashes($request);
	}
			
	if (isset($_POST["userID"])) {
		$userID = $_POST["userID"];
		$userID = stripslashes($userID);
	}
	
	if (isset($_POST["userGroup"])) {
		$userGroup = $_POST["userGroup"];
		$userGroup = stripslashes($userGroup);
	}
		
	$postfields = array(
		'goAction' => 'goGetSalesTracker',		
		'fromDate' => $fromDate,
		'toDate' => $toDate,
		'campaignID' => $campaign_id,
		'request' => $request,
		'statuses' => $statuses
	);
	$sales_tracker = "";
	$output = $api->API_getReports($postfields);
	//var_dump($output);
	if ($output->result == "success") {
		// SALES TRACKER
		if (strtolower($request) === "outbound") {
			//outbound table
			$sales_tracker .= '
			<div>
				<legend><small><em class="fa fa-arrow-right"></em><i> OUTBOUND </i></small></legend>
					<table class="display responsive no-wrap table table-striped table-bordered table-hover" id="outbound_table">
						<thead>
							<tr>
								<th nowrap> Lead ID </th>
								<th nowrap> Call Date & Time </th>
								<th nowrap> Agent </th>
								<th nowrap> Phone Number </th>
								<th nowrap> First Name </th>
								<th nowrap> Last Name </th>
							</tr>
						</thead>
						<tbody>
				';

			if ($output->outbound_result != NULL) {
				for($i=0; $i < count($output->sale_num); $i++) {
					$sales_tracker .= '<tr>
							<td nowrap><a class="edit-contact" data-id="'.$output->lead_id[$i].'">'.$output->lead_id[$i].'</a></td>
							<td nowrap>'.$output->call_date[$i].'</td>
							<td nowrap>'.$output->agent[$i].'</td>
							<td nowrap>'.$output->phone_number[$i].'</td>
							<td nowrap>'.$output->first_name[$i].'</td>
							<td nowrap>'.$output->last_name[$i].'</td>
						</tr>';
				}
			}else{
				$sales_tracker .= "";
			}
				
			$sales_tracker .= '</tbody>';

			$sales_tracker .= '</table></div>';
			
		}
		
		if (strtolower($request) == "inbound") {
		// inbound table
			$sales_tracker .= '
			<div>
				<legend><small><em class="fa fa-arrow-right"></em><i> INBOUND </i></small></legend>
					<table class="display responsive no-wrap table table-striped table-bordered table-hover"  id="inbound_table">
						<thead>
							<tr>
								<th nowrap> Lead ID </th>
								<th nowrap> Call Date & Time </th>
								<th nowrap> Agent </th>
								<th nowrap> Phone Number </th>
								<th nowrap> First Name </th>
								<th nowrap> Last Name </th>
							</tr>
						</thead>
						<tbody>
			';
				if ($output->inbound_result != NULL) {
					for($i=0; $i < count($output->sale_num); $i++) {
						$sales_tracker .= '<tr>
							<td nowrap><a class="edit-contact" data-id="'.$output->lead_id[$i].'">'.$output->lead_id[$i].'</a></td>
							<td nowrap>'.$output->call_date[$i].'</td>
							<td nowrap>'.$output->agent[$i].'</td>
							<td nowrap>'.$output->phone_number[$i].'</td>
							<td nowrap>'.$output->first_name[$i].'</td>
							<td nowrap>'.$output->last_name[$i].'</td>
						</tr>';
					}
				}else{
					$sales_tracker .= "";
				}
				
				$sales_tracker .= '</tbody>';

				$sales_tracker .= '</table></div>';
				
		}

		echo $sales_tracker;
	}

?>

