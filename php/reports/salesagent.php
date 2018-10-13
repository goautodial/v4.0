<?php
/**
 * @file        salesagent.php
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
	
	$api		= \creamy\APIHandler::getInstance();
	$fromDate	= date('Y-m-d 00:00:01');
	$toDate 	= date('Y-m-d 23:59:59');
	$campaign_id 	= NULL;
	$request	= NULL;
	$userID		= NULL;
	$userGroup	= NULL;
	$statuses	= NULL;
			
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
		'goAction' => 'goGetSalesAgent',		
		'pageTitle' => 'dispo',
		'fromDate' => $fromDate,
		'toDate' => $toDate,
		'campaignID' => $campaign_id,
		'request' => $request,
		'statuses' => $statuses
	);

	$output = $api->API_getReports($postfields);
	
	if ($output->result == "success") {
		// SALES PER AGENT
		if (strtolower($_POST['request']) === "outbound") {
			$outbound = '';
			
			// Outbound table
				$outbound .= '<div>
					<legend><small><em class="fa fa-arrow-right"></em><i> OUTBOUND </i></small></legend>
						<table class="display responsive no-wrap table table-striped table-bordered table-hover" id="outbound">
							<thead>
								<tr>
									<th nowrap> Agent Name </th>
									<th nowrap> Agent ID </th>
									<th nowrap> Sales Count </th>
								</tr>
							</thead>
							<tbody>
					';

				if ($output->TOPsorted_output != NULL) {
					//for($i=0; $i <= count($output->TOPsorted_output); $i++) {
						$outbound .= $output->TOPsorted_output;
				 	//}
				}else{
					$outbound .= "";
				}
					
				$outbound .= '</tbody>';

				if ($output->TOPsorted_output != NULL) {
						$outbound .= '<tfoot><tr class="warning"><th nowrap colspan="2"> Total Agents: ';
							$outbound .= count($output->TOPsorted_output).'</th>';
							$outbound .= '<th nowrap>'.$output->TOToutbound.'</th>';
						$outbound .= '</tr></tfoot>';
					}

				$outbound .= '</table></div><br/>'; 

			echo $outbound;
		}

		if (strtolower($_POST['request']) === "inbound") {
			$inbound = '';
			// inbound table
				$inbound .= '<div>
					<legend><small><em class="fa fa-arrow-right"></em><i> INBOUND </i></small></legend>
						<table class="display responsive no-wrap table table-striped table-bordered table-hover" id="inbound">
							<thead>
								<tr>
								<th nowrap> Agent Name </th>
								<th nowrap> Agent ID </th>
								<th nowrap> Sales Count </th>
							</tr>
						</thead>
						<tbody>
				';
				if ($output->BOTsorted_output != NULL) {
					//for($i=0; $i <= count($output->BOTsorted_output); $i++) {
						$inbound .= $output->BOTsorted_output;
					//}
				}else{
					$inbound .= "";
				}
					
				$inbound .= '</tbody>';
				
				if ($output->BOTsorted_output != NULL) {
					$inbound .= '<tfoot><tr class="warning"><th nowrap colspan="2"> Total Agents: ';
						$inbound .= count($output->BOTsorted_output).'</th>';
						$inbound .= '<th nowrap>'.$output->TOTinbound.'</th>';
					$inbound .= '</tr></tfoot>';
				}
					
				$inbound .= '</table></div>';

			echo $inbound; // return for inbound
		}
	}

?>
