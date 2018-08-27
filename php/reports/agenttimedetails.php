<?php
/**
 * @file        reports.php
 * @brief       Handles report requests
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A, Biscocho
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
	
	$api 										= \creamy\APIHandler::getInstance();
	$pageTitle									= $_POST['pageTitle'];
	$fromDate 									= date('Y-m-d 00:00:01');
	$toDate 									= date('Y-m-d 23:59:59');
	$campaign_id 								= NULL;
	$request									= NULL;
	$userID										= NULL;
	$userGroup									= NULL;
	$statuses									= NULL;
	
	if (isset($_POST['pageTitle']) && $pageTitle != "call_export_report") {
		$pageTitle 								= $_POST['pageTitle'];
		$pageTitle								= stripslashes($pageTitle);
	}
			
	if (isset($_POST["fromDate"])) {
		$fromDate 								= date('Y-m-d H:i:s', strtotime($_POST['fromDate']));
	}
	
	if ($_POST["toDate"] != "" && $_POST["fromDate"] != "") {
		$toDate 								= date('Y-m-d H:i:s', strtotime($_POST['toDate']));
	}
	
			
	if (isset($_POST["campaignID"])) { 
		$campaign_id 							= $_POST["campaignID"]; 
		$campaign_id 							= stripslashes($campaign_id);
	}
		
	if (isset($_POST["request"])) {
		$request 								= $_POST["request"];
		$request								= stripslashes($request);
	}
			
	if (isset($_POST["userID"])) {
		$userID 								= $_POST["userID"];
		$userID									= stripslashes($userID);
	}
	
	if (isset($_POST["userGroup"])) {
		$userGroup 								= $_POST["userGroup"];
		$userGroup								= stripslashes($userGroup);
	}
		
	if (isset($_POST["statuses"])) {
		$statuses 								= $_POST["statuses"];
		$statuses								= stripslashes($statuses);
	}
		
	$postfields 								= array(
		'goAction'									=> 'goGetAgentTimeDetails',		
		'pageTitle' 								=> $pageTitle,
		'fromDate' 									=> $fromDate,
		'toDate' 									=> $toDate,
		'campaignID' 								=> $campaign_id,
		'request' 									=> $request,
		'statuses' 									=> $statuses
	);				

	$output 									= $api->API_getAgentTimeDetails($postfields);

	if ($output->result == "success") {
		echo '<div class="animated bounceInUp">';

	// AGENT TIME DETAIL
		if ($pageTitle == "agent_detail") {
			$agent_detail = '';
			//echo "<pre>";
			//var_dump($output);
			//die("dd");
			// top table
				$agent_detail .= '<div>
					<table class="responsive display no-wrap compact table table-striped" width="100%" id="agent_detail_top">
						<thead>
							<tr>
								<th> Agent Name </th>
								<!-- <th> User Name </th> -->
								<th> Calls </th>
								<th> Agent Time </th>
								<th> Wait </th>
								<th> Talk </th>
								<th> Dispo </th>
								<th> Pause </th>
								<th> Wrap-Up </th>
								<th> Customer </th>
							</tr>
						</thead>
						<tbody>
				';

				if ($output->TOPsorted_output != NULL) {
					//echo "<pre>";
					//var_dump($output->TOPsorted_output);
					for ($i=0; $i < count($output->TOPsorted_output->name); $i++) {
						$agent_detail .= '<tr>
											<td>'.$output->TOPsorted_output->name[$i].'</td>
											<!-- <td>'.$output->TOPsorted_output->user[$i].'</td> -->
											<td>'.$output->TOPsorted_output->number_of_calls[$i].'</td>
											<td>'.gmdate('H:i:s', $output->TOPsorted_output->agent_time[$i]).'</td>
											<td>'.gmdate('H:i:s', $output->TOPsorted_output->wait_time[$i]).'</td>
											<td>'.gmdate('H:i:s', $output->TOPsorted_output->talk_time[$i]).'</td>
											<td>'.gmdate('H:i:s', $output->TOPsorted_output->dispo_time[$i]).'</td>
											<td>'.gmdate('H:i:s', $output->TOPsorted_output->pause_time[$i]).'</td>
											<td>'.gmdate('H:i:s', $output->TOPsorted_output->wrap_up[$i]).'</td>
											<td>'.gmdate('H:i:s', $output->TOPsorted_output->customer_time[$i]).'</td>
										</tr>';
					}
				} else {
					$agent_detail .= "";
				}
					
				$agent_detail .= '</tbody>';

				if ($output->TOTcalls != NULL) {
						$agent_detail .= '<tfoot><!-- <tr class="warning"><th> Total </th> -->';
							$agent_detail .= '<th>'.$output->TOT_AGENTS.'</th>';
							$agent_detail .= '<th>'.$output->TOTcalls.'</th>';
							$agent_detail .= '<th>'.$output->TOTALtime.'</th>';
							$agent_detail .= '<th>'.$output->TOTwait.'</th>';
							$agent_detail .= '<th>'.$output->TOTtalk.'</th>';
							$agent_detail .= '<th>'.$output->TOTdispo.'</th>';
							$agent_detail .= '<th>'.$output->TOTpause.'</th>';
							$agent_detail .= '<th>'.$output->TOTdead.'</th>';
							$agent_detail .= '<th>'.$output->TOTcustomer.'</th>';
						$agent_detail .= '</tr></tfoot>';
					}

				$agent_detail .= '</table></div><br/>'; 

			// login table
				if ($output->sub_statusesTOP != NULL) {
					$agent_detail .= '<div>
						<table class="responsive display no-wrap compact table table-striped" width="100%" id="agent_detail_login">
							<thead>
								<tr>
									<th> User </th>';
							for($i=0; $i < count($output->sub_statusesTOP); $i++) {
								$agent_detail .= '<th>'.$output->sub_statusesTOP[$i].'</th>';
							}
					
					$agent_detail .= '</tr>
							</thead>
							<tbody>
					';

					if ($output->BOTsorted_output != NULL) {				
						for($i=0; $i < count($output->BOTsorted_output->name); $i++) {
							$statuses = $output->BOTsorted_output->statuses[$i];
							$agent_names = $output->BOTsorted_output->name[$i];
							$agent_detail .= '<tr>';
							$agent_detail .= '<td>'.$agent_names.'</td>';
							
							for($a=0; $a < count($output->sub_statusesTOP); $a++) {
								//if (!empty($output->BOTsorted_output->statuses[$a])) {
								if ($statuses == $output->BOTsorted_output->statuses[$a]) {
									$agent_detail .= '<td>'.gmdate('H:i:s', $output->BOTsorted_output->statuses[$a]).'</td>';
								} else {
									$agent_detail .= '<td>00:00:00</td>';
								}
							}
							
							$agent_detail .= '</tr>';
							unset($statuses);
							//$agent_detail .= $output->BOTsorted_output;
						}
					} else {
						$agent_detail .= "";
					}
					
					$agent_detail .= '</tbody>';
					
						if ($output->SUMstatuses != NULL) {					
							$agent_detail .= '<tfoot><tr class="warning"><th> Total </th>';
							for($i=0; $i < count($output->SUMstatuses); $i++) {
								$agent_detail .= '<th>'.$output->SUMstatuses.'</th>';
							}
							$agent_detail .= '</tr></tfoot>';
						}

					$agent_detail .= '</table></div><br/>'; 
				
				}
			
			//FORM TO BE PASSED WHEN EXPORT IS CALLED
			$agent_detail .= '<form action="php/ExportAgentDetails.php" id="export_agentdetail_form"  method="POST">
								<input type="hidden" name="pageTitle" value="'.$pageTitle.'" />
								<input type="hidden" name="fromDate" value="'.$fromDate.'" />
								<input type="hidden" name="toDate" value="'.$toDate.'" />
								<input type="hidden" name="campaignID" value="'.$_POST["campaignID"].'" />
								<input type="hidden" name="session_user" value="'.$_SESSION["user"].'" />
							</form>';

			echo $agent_detail; // return for agent details

		}// end of agent_detail


	}
//print_r($output);
?>
