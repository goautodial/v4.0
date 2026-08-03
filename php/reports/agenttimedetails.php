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

	require_once(__DIR__ . '/APIHandler.php');

	$api 										= \creamy\APIHandler::getInstance();
	$pageTitle									= ($_POST['pageTitle'] ?? '');
	$fromDate 									= date('Y-m-d 00:00:01');
	$toDate 									= date('Y-m-d 23:59:59');
	$campaign_id 								= NULL;
	$request									= NULL;
	$userID										= NULL;
	$userGroup									= NULL;
	$statuses									= NULL;
	$agent_pdetail								= '';

	if (isset($_POST['pageTitle']) && $pageTitle != "call_export_report") {
		$pageTitle 								= ($_POST['pageTitle'] ?? '');
		$pageTitle								= stripslashes((string) $pageTitle);
	}

	if (isset($_POST["fromDate"])) {
		$fromDate 								= date('Y-m-d H:i:s', strtotime(($_POST['fromDate'] ?? '')));
	}

	if (($_POST["toDate"] ?? '') != "" && ($_POST["fromDate"] ?? '') != "") {
		$toDate 								= date('Y-m-d H:i:s', strtotime(($_POST['toDate'] ?? '')));
	}


	if (isset($_POST["campaignID"])) {
		$campaign_id 							= ($_POST["campaignID"] ?? '');
		$campaign_id 							= stripslashes((string) $campaign_id);
	}

	if (isset($_POST["request"])) {
		$request 								= ($_POST["request"] ?? '');
		$request								= stripslashes((string) $request);
	}

	if (isset($_POST["userID"])) {
		$userID 								= ($_POST["userID"] ?? '');
		$userID									= stripslashes((string) $userID);
	}

	if (isset($_POST["userGroup"])) {
		$userGroup 								= ($_POST["userGroup"] ?? '');
		$userGroup								= stripslashes((string) $userGroup);
	}

	if (isset($_POST["statuses"])) {
		$statuses 								= ($_POST["statuses"] ?? '');
		$statuses								= stripslashes($statuses);
	}

	$postfields 								= [
		'goAction'									=> 'goGetAgentTimeDetails',
		'pageTitle' 								=> $pageTitle,
		'fromDate' 									=> $fromDate,
		'toDate' 									=> $toDate,
		'campaignID' 								=> $campaign_id,
		'request' 									=> $request,
		'statuses' 									=> $statuses
	];

	$output = $api->API_getAgentTimeDetails($postfields);
//var_dump($output);

	if ($output->result == "success") {
		echo '<div class="animated bounceInUp">';

	// AGENT TIME DETAIL
		if ($pageTitle == "agent_detail") {
			$tablehtml = '';
			//echo "<pre>";
			//var_dump($output);
			//die("dd");
			// top table
				$tablehtml .= '<div>
					<table class="responsive display no-wrap compact table table-striped" style="width:100%" id="agent_detail_top">
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
					$counter = (isset($output->TOPsorted_output->name) && is_countable($output->TOPsorted_output->name) ? count($output->TOPsorted_output->name) : 0);
                    //echo "<pre>";
					//var_dump($output->TOPsorted_output);
					for ($i=0; $i < $counter; $i++) {
						$tablehtml .= '<tr>
							<td>'.$output->TOPsorted_output->name[$i].'</td>
							<!-- <td>'.$output->TOPsorted_output->user[$i].'</td> -->
							<td>'.$output->TOPsorted_output->number_of_calls[$i].'</td>
							<td>'.$output->TOPsorted_output->agent_time[$i].'</td>
							<td>'.$output->TOPsorted_output->wait_time[$i].'</td>
							<td>'.$output->TOPsorted_output->talk_time[$i].'</td>
							<td>'.$output->TOPsorted_output->dispo_time[$i].'</td>
							<td>'.$output->TOPsorted_output->pause_time[$i].'</td>
							<td>'.$output->TOPsorted_output->wrap_up[$i].'</td>
							<td>'.$output->TOPsorted_output->customer_time[$i].'</td>
						</tr>';
					}
				} else {
					$tablehtml .= "";
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

				$tablehtml .= '</table></div><br/>';

				$sub_statuses = [];
				$full_names = [];

				foreach ($output->PC_statuses as $value) {
					$sub_status = $value->sub_status;
					$full_name = $value->full_name;
					$pause_sec = $value->pause_sec;

					$sub_statuses[] = $sub_status;
					$full_names[] = $full_name;
				}

				$pause_codes = array_unique($sub_statuses);
				$agent_names = array_unique($full_names);

				if (!empty($output->PC_statuses)) {
					$tablehtml .= '<div>';
					$tablehtml .= '<table class="responsive display compact table table-inverse table-bordered" style="width:100%" id="agent_detail_login">';
					$tablehtml .= '<thead><tr>';
					$tablehtml .= '<th> User </th>';

					foreach ($pause_codes as $pause_code) {
						$tablehtml .= '<th>'.$pause_code.'</th>';
					}

					$tablehtml .= '</tr>';
					$tablehtml .= '</thead>';
					$tablehtml .= '<tbody id="agent_detail_tbody">';

					foreach ($output->PC_statuses as $value) {
						$full_name = $value->full_name;
						$sub_status = $value->sub_status;
						$pc_duration = $value->pause_sec;

						//$pcstatus = "$sub_status-$pc_duration";
						//$row_array[] = array("agent" => $full_name, "sub_status" => $sub_status, "duration" => $pc_duration);

						//foreach ($agent_names as $agent_name) {
							//if ($full_name == $agent_name) {
								$tablehtml .= '<tr><td>'.$full_name.'</td>';

								foreach ($pause_codes as $pause_code) {
									if ($sub_status == $pause_code) {
										//$tablehtml .= '<td>'.gmdate('H:i:s', $pc_duration).'</td>';
										$tablehtml .= '<td class="duration">'.$pc_duration.'</td>';
									} else {
										$tablehtml .= '<td>0</td>';
									}
								}

								$tablehtml .= '</tr>';
							//}
						//}
					}

					$tablehtml .= '</tbody>';

					$tablehtml .= '<tfoot><tr>';
					$tablehtml .= '<th> TOTAL </th>';
					foreach ($pause_codes as $pause_code) {
						$tablehtml .= '<th>'.$pause_code.'</th>';
					}
					$tablehtml .= '</tr></tfoot>';

					$tablehtml .= '</table></div><br/>';
				} else {
					$tablehtml .= '';
				}

			 // start of middle table
					$midSortedOutput = isset($output->MIDsorted_output) && is_array($output->MIDsorted_output) ? $output->MIDsorted_output : [];
	                                if (!empty($midSortedOutput)) {
                                        $agent_pdetail .= '<br/><div>
                                                <table class="display responsive no-wrap table table-striped table-bordered table-hover" width="100%" id="agent_pdetail_mid">
                                                        <thead>
                                                                <tr>
                                                                        <th nowrap> Full Name </th>';

                                                                        												if (($output->SstatusesTOP ?? '') != '') {
                                                                        														$agent_pdetail .= $output->SstatusesTOP;
                                                                        												}

                                                $agent_pdetail .=  '</tr></thead><tbody>';
                                                										foreach ($midSortedOutput as $midRow) {
                                                											$agent_pdetail .= $midRow;
                                                										}

                                                $agent_pdetail .= '</tbody>';

                                                										if (!empty($midSortedOutput)) {
                                                												$agent_pdetail .= '<tfoot><tr class="warning"><th nowrap> Total </th>';

                                                														if (($output->SstatusesSUM ?? '') != '') {
                                                																$agent_pdetail .= $output->SstatusesSUM;
                                                														}

                                                        $agent_pdetail .= '</tr></tfoot>';
                                                }

                                                $agent_pdetail .= '</table></div><br/>';
					$tablehtml .= $agent_pdetail;
                                }else{
					$tablehtml .= '';
				}
                        //end of middle table

                        						// start of legend
	                                 if (!empty($midSortedOutput)) {
                                        $agent_pdetail .= '<table class="table table-hover">
                                                                <tr class="info"><th colspan="2"><small>LEGEND: </th></tr>';
                                        										$legend = isset($output->legend) && is_array($output->legend) ? $output->legend : [];
                                        										$counter = count($legend);
                                        														for ($i=0; $i < $counter; $i+=2) {
                                        																$agent_pdetail .= "<tr><td><small>".($legend[$i] ?? '')."</small></td><td><small>".($legend[$i+1] ?? '')."</small></td></tr>";
                                        														}
                                        $agent_pdetail .= '</table><br/>';
					$tablehtml .= $agent_pdetail;
                                 }
                        // end of legend

			//FORM TO BE PASSED WHEN EXPORT IS CALLED
			$tablehtml .= '<form action="php/ExportAgentDetails.php" id="export_agentdetail_form"  method="POST">
								<input type="hidden" name="pageTitle" value="'.$pageTitle.'" />
								<input type="hidden" name="fromDate" value="'.$fromDate.'" />
								<input type="hidden" name="toDate" value="'.$toDate.'" />
								<input type="hidden" name="campaignID" value="'.($_POST["campaignID"] ?? '').'" />
								<input type="hidden" name="session_user" value="'.$_SESSION["user"].'" />
							</form>';

			echo $tablehtml; // return for agent details

		}// end of agent_detail
	}

?>
