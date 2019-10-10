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
		//'goAction'								=> 'goGetAgentPerformanceDetails', Service Monkey Performance Detail		
		'goAction'                                                              => 'goGetPerformanceDetails',
		'pageTitle' 								=> $pageTitle,
		'fromDate' 								=> $fromDate,
		'toDate' 								=> $toDate,
		'campaignID' 								=> $campaign_id
	);				

	$output = $api->API_Request("goReports",$postfields);
//var_dump($output);

	if ($output->result == "success") {
		echo '<div class="animated bounceInUp">';
	// AGENT PERFORMANCE DETAILi
		$tablehtml = '';
		// start of top table
			$tablehtml .= '<div class="table-responsive">
				<table class="table table-striped table-bordered table-hover" id="agent_pdetail_top">
					<thead>
						<tr>
				            <th nowrap> Full Name </th>
				            <th nowrap> ID </th>
				            <th nowrap> Calls </th>
				            <th nowrap> Time </th>
							<th nowrap> Pause </th>
							<th nowrap> » Avg </th>
				            <th nowrap> WAIT </th>
							<th nowrap> » Avg </th>
				            <th nowrap> Talk </th>
							<th nowrap> » Avg </th>
				            <th nowrap> Dispo </th>
							<th nowrap> » Avg </th>
				            <th nowrap> Wrap-Up </th>
							<th nowrap> » Avg </th>
				            <th nowrap> Customer </th>
							<th nowrap> » Avg </th>
			            </tr>
			        </thead>
			        <tbody>';
						if($output->TOPsorted_output != NULL){
							for($i=0; $i < count($output->TOPsorted_output); $i++){
						    		$tablehtml .= $output->TOPsorted_output[$i];
						    	}
						}else{
							$tablehtml .= "";
						}
		   		$tablehtml .= '</tbody>';

		   		if($output->TOPsorted_output != NULL){
			   		$tablehtml .= '<tfoot><tr class="warning"><th nowrap> Total </th>';
							$tablehtml .= $output->TOT_AGENTS;
							$tablehtml .= $output->TOTcalls;
							$tablehtml .= $output->TOTtime_MS;
							$tablehtml .= $output->TOTtotPAUSE_MS;
							$tablehtml .= $output->TOTavgPAUSE_MS;
							$tablehtml .= $output->TOTtotWAIT_MS;
							$tablehtml .= $output->TOTavgWAIT_MS;
							$tablehtml .= $output->TOTtotTALK_MS;
							$tablehtml .= $output->TOTavgTALK_MS;
							$tablehtml .= $output->TOTtotDISPO_MS;
							$tablehtml .= $output->TOTavgDISPO_MS;
							$tablehtml .= $output->TOTtotDEAD_MS;
							$tablehtml .= $output->TOTavgDEAD_MS;
							$tablehtml .= $output->TOTtotCUSTOMER_MS;
							$tablehtml .= $output->TOTavgCUSTOMER_MS;
					$tablehtml .= '</tr></tfoot>';
				}

				$tablehtml .= '</table></div><br/>'; 
	    // end of top table

	    // start of middle table
			if($output->MIDsorted_output != NULL){
			    $tablehtml .= '<br/><div class="table-responsive">
				    <table class="table table-striped table-bordered table-hover" id="agent_pdetail_mid">
				    	<thead>
							<tr>
					            <th nowrap> Full Name </th>';

								if($output->SstatusesTOP != NULL){
								    $tablehtml .= $output->SstatusesTOP;
								}
					        
					$tablehtml .=  '</tr></thead><tbody>';

						    for($i=0; $i <= count($output->MIDsorted_output); $i++){
						        $tablehtml .= $output->MIDsorted_output[$i];
						    }

					$tablehtml .= '</tbody>';

					if($output->MIDsorted_output != NULL){
						$tablehtml .= '<tfoot><tr class="warning"><th nowrap> Total </th>';

							if($output->SstatusesSUM != NULL){
							    $tablehtml .= $output->SstatusesSUM;
							}

						$tablehtml .= '</tr></tfoot>';
					}

					$tablehtml .= '</table></div><br/>';
			}
		//end of middle table
		
		// start of legend
				if($output->MIDsorted_output != NULL){
					$tablehtml .= '<table class="table table-hover">
					    	<tr class="info"><th colspan="2"><small>LEGEND: </th></tr>';
					    	for ($i=0; $i < count($output->Legend); $i+=2) { 
					    		$tablehtml .= "<tr><td><small>".$output->Legend[$i]."</small></td><td><small>".$output->Legend[$i+1]."</small></td></tr>";
					    	}
					$tablehtml .= '</table><br/>';
			    }
		// end of legend

		// start of bottom table
			$tablehtml .= '
				<div class="row">
				<div class="col-sm-8 col-md-8">
				<div class="table-responsive">
				<table class="table table-striped table-bordered table-hover" id="agent_pdetail_bottom">
			    	<thead>
						<tr>
				            <th nowrap> Full Name </th>
				            <th nowrap> ID </th>
				            <th nowrap> Total </th>
				            <th nowrap> NonPause </th>
				            <th nowrap> Pause </th>
			            </tr>
			        </thead>
			        <tbody>';
					    if($output->BOTsorted_output != NULL){
						    for($i=0; $i <= count($output->BOTsorted_output); $i++){
						        $tablehtml .= $output->BOTsorted_output[$i];
						    }
						}else{
							$tablehtml .= "";
						}
				$tablehtml .= '</tbody>';

				if($output->BOTsorted_output != NULL){
					$tablehtml .= '<tfoot><tr class="warning"><th nowrap> Total </th>';
							$tablehtml .= $output->TOT_AGENTS;
							$tablehtml .= $output->TOTtotTOTAL_MS;
							$tablehtml .= $output->TOTtotNONPAUSE_MS;
							$tablehtml .= $output->TOTtotPAUSEB_MS;
					$tablehtml .= '</tr></tfoot>';
				}

				$tablehtml .= '</table>
				</div></div>';
				//</div><br/>'; 
		// end of bottom table


		// start of login table
			if($output->SstatusesBOT != NULL){
				$tablehtml .= '
				<div class="col-sm-4 col-md-4">
					<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover" id="agent_pdetail_login">
				    	<thead>
							<tr>';
								$tablehtml .= $output->SstatusesBOT;
								
				    $tablehtml .='</tr>
				        </thead>
				        <tbody>';
						    if($output->SstatusesBOTR != NULL){
							    for($i=0; $i <= count($output->SstatusesBOTR); $i++){
							        $tablehtml .= '<tr>'.$output->SstatusesBOTR[$i].'</tr>';
							    }
							}else{
								$tablehtml .= "";
							}
					$tablehtml .= '</tbody>';

					if($output->SstatusesBOTR != NULL){
						$tablehtml .= '<tfoot><tr class="warning">';
									if($output->SstatusesBSUM != NULL){
									    $tablehtml .= $output->SstatusesBSUM;
									}
						$tablehtml .= '</tr></tfoot>';
					}
			}
				$tablehtml .= '</table></div>
				</div></div>';
		// -- end of login table
		
		echo $tablehtml; // return for agent performance detail
	}

?>
