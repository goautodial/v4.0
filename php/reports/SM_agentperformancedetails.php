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
		'goAction'								=> 'goGetAgentPerformanceDetails', //Service Monkey Performance Detail		
		//'goAction'                                                              => 'goGetPerformanceDetails',
		'pageTitle' 								=> $pageTitle,
		'fromDate' 								=> $fromDate,
		'toDate' 								=> $toDate,
		'campaignID' 								=> $campaign_id
	);				

	$output = $api->API_Request("goReports",$postfields);

	if ($output->result == "success") {
		echo '<div class="animated bounceInUp">';
	// AGENT PERFORMANCE DETAIL
		$tablehtml = '';
			$tablehtml .= '<div class="table-responsive">
				<table class="table table-striped table-bordered table-hover" id="table_agent_pdetailSM">
					<thead>
						<tr>
							<th> Agent Name </th>
							<th> SID </th>
							<th> WRAP % </th>
							<th> DISP % </th>
							<th> TALK TIME </th>
							<th> SPH </th>
							<th> CALL VOLUME </th>
							<th> AM </th>
							<th> NI </th>
							<th> CB </th>
						</tr>
					</thead>
					<tbody>
			';

			if(count($output->agent_name) > 0){

				for ($i=0; $i < count($output->agent_name); $i++) {
					$tablehtml .= '<tr>
						<td>'.$output->agent_name[$i].'</td>
						<td>'.$output->sid[$i].'</td>
						<td>'.$output->wrap[$i].'</td>
						<td>'.$output->dispo[$i].'</td>
						<td>'.$output->talk_sec[$i].'</td>
						<td>'.$output->sph[$i].'</td>
						<td>'.$output->call_vol[$i].'</td>
						<td>'.$output->am[$i].'</td>
						<td>'.$output->ni[$i].'</td>
						<td>'.$output->cb[$i].'</td>	
					</tr>';
				}
				
				$tablehtml .= '</tbody>';
			
				if ($output->TOTcalls != NULL) {
						$tablehtml .= '<tfoot><!-- <tr class="warning"><th> Total </th> -->';
							$tablehtml .= '<th>'.$output->TOT_AGENTS.'</th>';
							$tablehtml .= '<th>'.$output->TOTcalls.'</th>';
							$tablehtml .= '<th>'.$output->TOTALtime.'</th>';
							$tablehtml .= '<th>'.$output->TOTwait.'</th>';
							$tablehtml .= '<th>'.$output->TOTtalk.'</th>';
							$tablehtml .= '<th>'.$output->TOTdispo.'</th>';
							$tablehtml .= '<th>'.$output->TOTpause.'</th>';
							$tablehtml .= '<th>'.$output->TOTdead.'</th>';
							$tablehtml .= '<th>'.$output->TOTcustomer.'</th>';
						$tablehtml .= '</tr></tfoot>';
					}
			}else{
				$tablehtml .= "";	
			}
				$tablehtml .= '</table></div><br/>'; 
			
			//FORM TO BE PASSED WHEN EXPORT IS CALLED
			$tablehtml .= '<form action="php/ExportPerformanceDetailsSM.php" id="export_agentPdetailSM_form"  method="POST">
								<input type="hidden" name="pageTitle" value="'.$pageTitle.'" />
								<input type="hidden" name="fromDate" value="'.$fromDate.'" />
								<input type="hidden" name="toDate" value="'.$toDate.'" />
								<input type="hidden" name="campaignID" value="'.$_POST["campaignID"].'" />
								<input type="hidden" name="session_user" value="'.$_SESSION["user"].'" />
					</form>';

			echo $tablehtml; // return for agent details

	}
?>
