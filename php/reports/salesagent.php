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

	if(STATEWIDE_SALES_REPORT === 'y'){
		$statewide_sales_report = 'y';
	} else {
		$statewide_sales_report = 'n';
	}
		
	$postfields = array(
		'goAction' => 'goGetSalesAgent',		
		'pageTitle' => 'dispo',
		'fromDate' => $fromDate,
		'toDate' => $toDate,
		'campaignID' => $campaign_id,
		'request' => $request,
		'statuses' => $statuses,
		'statewide_sales_report' => $statewide_sales_report
	);

	$output = $api->API_getReports($postfields);
	
	if ($output->result == "success") {
		//echo "<pre>";
		//var_dump($output);
		
		// SALES PER AGENT
		// Statewide Customization
                if(STATEWIDE_SALES_REPORT === 'y' && $output->col_exists == '1'){
                       $statewide = '<th nowrap> Amount </th>';
                } else {
                       $statewide = '';
                }
                // ./Statewide Customization

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
									<!-- statewide customization -->
									'. $statewide .'
									<!-- ./statewide customization -->
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
							$outbound .= $output->TOTAgents.'</th>';
							$outbound .= '<th nowrap>'.$output->TOToutbound.'</th>';
							// Statewide Customization
							if(STATEWIDE_SALES_REPORT === 'y' && $output->col_exists == '1'){
							$outbound .= '<th nowrap>'.$output->TOTOUTamount.'</th>';
							}
							// ./ Statewide Customization
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
								<!-- statewide customization -->
								'. $statewide .'
								<!-- ./ statewide customization -->
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
						// Statewide Customization
						if(STATEWIDE_SALES_REPORT === 'y' && $output->col_exists == '1'){
						$inbound .= '<th nowrap>'.$output->TOTINamount.'</th>';
						}
						// ./ Statewide Customization
					$inbound .= '</tr></tfoot>';
				}
					
				$inbound .= '</table></div>';

			echo $inbound; // return for inbound
		}
	}

?>
