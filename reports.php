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

	require_once('./php/APIHandler.php');
	
	$api 										= \creamy\APIHandler::getInstance();
	$pageTitle									= $_POST['pageTitle'];
	$fromDate 									= date('Y-m-d H:i:s');
	$toDate 									= date('Y-m-d H:i:s');
	$campaignID 								= NULL;
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
		$campaignID 							= $_POST["campaignID"]; 
		$campaignID 							= stripslashes($campaignID);
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
		
	if(isset($_POST["statuses"])) {
		$statuses 								= $_POST["statuses"];
		$statuses								= stripslashes($statuses);
	}
		
	$postfields 								= array(
		'goAction'									=> 'goGetReports',		
		'pageTitle' 								=> $pageTitle,
		'fromDate' 									=> $fromDate,
		'toDate' 									=> $toDate,
		'campaignID' 								=> $campaignID,
		'request' 									=> $request,
		'statuses' 									=> $statuses
	);				

	$output 									= $api->API_getReports($postfields);

if($output->result == "success"){
	echo '<div class="responsive animated bounceInUp">';

// AGENT TIME DETAIL
	if($pageTitle == "agent_detail"){
		$agent_detail = '';

		// top table
			$agent_detail .= '<div>
				<table class="display responsive no-wrap table table-striped table-bordered table-hover" width="100%" id="agent_detail_top">
					<thead>
						<tr>
				            <th nowrap> Full Name </th>
				            <th nowrap> User Name </th>
				            <th nowrap> Calls </th>
				            <th nowrap> Agent Time </th>
				            <th nowrap> WAIT </th>
				            <th nowrap> Talk </th>
				            <th nowrap> Dispo </th>
				            <th nowrap> Pause </th>
				            <th nowrap> Wrap-Up </th>
				            <th nowrap> Customer </th>
			            </tr>
			        </thead>
			        <tbody>
			';

			if($output->getReports->TOPsorted_output != NULL){
				//var_dump($output->getReports->TOPsorted_output[$i]->name);
				for($i=0; $i < count($output->getReports->TOPsorted_output); $i++){
			    	$agent_detail .= '<tr>
			    						<td nowrap>'.$output->getReports->TOPsorted_output[$i]->name.'</td>
			    						<td nowrap>'.$output->getReports->TOPsorted_output[$i]->user.'</td>
			    						<td nowrap>'.$output->getReports->TOPsorted_output[$i]->number_of_calls.'</td>
			    						<td nowrap>'.$output->getReports->TOPsorted_output[$i]->agent_time.'</td>
			    						<td nowrap>'.$output->getReports->TOPsorted_output[$i]->wait_time.'</td>
			    						<td nowrap>'.$output->getReports->TOPsorted_output[$i]->talk_time.'</td>
			    						<td nowrap>'.$output->getReports->TOPsorted_output[$i]->dispo_time.'</td>
			    						<td nowrap>'.$output->getReports->TOPsorted_output[$i]->pause_time.'</td>
			    						<td nowrap>'.$output->getReports->TOPsorted_output[$i]->wrap_up.'</td>
			    						<td nowrap>'.$output->getReports->TOPsorted_output[$i]->customer_time.'</td>
			    					</tr>';
			    }
			}else{
				$agent_detail .= "";
			}
				
		    $agent_detail .= '</tbody>';

		    if($output->getReports->TOTcalls != NULL){
			   		$agent_detail .= '<tfoot><tr class="warning"><th nowrap> Total </th>';
					    $agent_detail .= '<th nowrap>'.$output->getReports->TOT_AGENTS.'</th>';
					    $agent_detail .= '<th nowrap>'.$output->getReports->TOTcalls.'</th>';
					    $agent_detail .= '<th nowrap>'.$output->getReports->TOTALtime.'</th>';
					    $agent_detail .= '<th nowrap>'.$output->getReports->TOTwait.'</th>';
					    $agent_detail .= '<th nowrap>'.$output->getReports->TOTtalk.'</th>';
					    $agent_detail .= '<th nowrap>'.$output->getReports->TOTdispo.'</th>';
					    $agent_detail .= '<th nowrap>'.$output->getReports->TOTpause.'</th>';
					    $agent_detail .= '<th nowrap>'.$output->getReports->TOTdead.'</th>';
					    $agent_detail .= '<th nowrap>'.$output->getReports->TOTcustomer.'</th>';
					$agent_detail .= '</tr></tfoot>';
				}

			$agent_detail .= '</table></div><br/>'; 

	    // login table
		    if($output->getReports->sub_statusesTOP != NULL){
			    $agent_detail .= '<div>
					<table class="display responsive no-wrap table table-striped table-bordered table-hover" width="100%" id="agent_detail_login">
						<thead>
							<tr>
								<th nowrap> User </th>';
								//var_dump($output->getReports->sub_statusesTOP);
						for($i=0; $i < count($output->getReports->sub_statusesTOP); $i++){
							//$Hstatuses = explode(",", $output->getReports->sub_statusesTOP[$i]);
							$agent_detail .= '<th nowrap>'.$output->getReports->sub_statusesTOP[$i].'</th>';
						}
					           // $agent_detail .= $output->getReports->sub_statusesTOP;
				
				$agent_detail .= '</tr>
				        </thead>
				        <tbody>
				';

				if($output->getReports->BOTsorted_output != NULL){
					for($i=0; $i < count($output->getReports->BOTsorted_output); $i++){
						$statuses = explode(",", $output->getReports->BOTsorted_output[$i]->statuses);
						$agent_detail .= '<tr>
				    						<td nowrap>'.$output->getReports->BOTsorted_output[$i]->name.'</td>';
					    					for($a=0; $a < count($output->getReports->sub_statusesTOP); $a++){
					    						if(!empty($statuses[$a]))
					    							$agent_detail .= '<td nowrap>'.$statuses[$a].'</td>';
					    						else
					    							$agent_detail .= '<td nowrap>00:00:00</td>';
					    					}
				    					'</tr>';
			    		unset($statuses);
				    	//$agent_detail .= $output->getReports->BOTsorted_output[$i];
				    }
				}else{
					$agent_detail .= "";
				}
		   		
		   		$agent_detail .= '</tbody>';
				
		    		if($output->getReports->SUMstatuses != NULL){
			    		$agent_detail .= '<tfoot><tr class="warning"><th nowrap> Total </th>';
			    		for($i=0; $i < count($output->getReports->SUMstatuses); $i++){
				 	    	$agent_detail .= '<th nowrap>'.$output->getReports->SUMstatuses[$i].'</th>';
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

// AGENT PERFORMANCE DETAIL
	if($pageTitle == "agent_pdetail"){
		$agent_pdetail = '';
		// start of top table
			$agent_pdetail .= '<div>
					<table class="display responsive no-wrap table table-striped table-bordered table-hover" width="100%" id="agent_pdetail_top">
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
						if($output->getReports->TOPsorted_output != NULL){
							for($i=0; $i <= count($output->getReports->TOPsorted_output); $i++){
						    	$agent_pdetail .= $output->getReports->TOPsorted_output[$i];
						    }
						}else{
							$agent_pdetail .= "";
						}
		   		$agent_pdetail .= '</tbody>';

		   		if($output->getReports->TOPsorted_output != NULL){
			   		$agent_pdetail .= '<tfoot><tr class="warning"><th nowrap> Total </th>';
							$agent_pdetail .= $output->getReports->TOT_AGENTS;
							$agent_pdetail .= $output->getReports->TOTcalls;
							$agent_pdetail .= $output->getReports->TOTtime_MS;
							$agent_pdetail .= $output->getReports->TOTtotPAUSE_MS;
							$agent_pdetail .= $output->getReports->TOTavgPAUSE_MS;
							$agent_pdetail .= $output->getReports->TOTtotWAIT_MS;
							$agent_pdetail .= $output->getReports->TOTavgWAIT_MS;
							$agent_pdetail .= $output->getReports->TOTtotTALK_MS;
							$agent_pdetail .= $output->getReports->TOTavgTALK_MS;
							$agent_pdetail .= $output->getReports->TOTtotDISPO_MS;
							$agent_pdetail .= $output->getReports->TOTavgDISPO_MS;
							$agent_pdetail .= $output->getReports->TOTtotDEAD_MS;
							$agent_pdetail .= $output->getReports->TOTavgDEAD_MS;
							$agent_pdetail .= $output->getReports->TOTtotCUSTOMER_MS;
							$agent_pdetail .= $output->getReports->TOTavgCUSTOMER_MS;
					$agent_pdetail .= '</tr></tfoot>';
				}

				$agent_pdetail .= '</table></div><br/>'; 
	    // end of top table

	    // start of middle table
			if($output->getReports->MIDsorted_output != NULL){
			    $agent_pdetail .= '<br/><div>
					<table class="display responsive no-wrap table table-striped table-bordered table-hover" width="100%" id="agent_pdetail_mid">
				    	<thead>
							<tr>
					            <th nowrap> Full Name </th>';

								if($output->getReports->SstatusesTOP != NULL){
								    $agent_pdetail .= $output->getReports->SstatusesTOP;
								}
					        
					$agent_pdetail .=  '</tr></thead><tbody>';

						    for($i=0; $i <= count($output->getReports->MIDsorted_output); $i++){
						        $agent_pdetail .= $output->getReports->MIDsorted_output[$i];
						    }

					$agent_pdetail .= '</tbody>';

					if($output->getReports->MIDsorted_output != NULL){
						$agent_pdetail .= '<tfoot><tr class="warning"><th nowrap> Total </th>';

							if($output->getReports->SstatusesSUM != NULL){
							    $agent_pdetail .= $output->getReports->SstatusesSUM;
							}

						$agent_pdetail .= '</tr></tfoot>';
					}

					$agent_pdetail .= '</table></div><br/>';
			}
		//end of middle table
		
		// start of legend
				if($output->getReports->MIDsorted_output != NULL){
					$agent_pdetail .= '<table class="table table-hover">
					    	<tr class="info"><th colspan="2"><small>LEGEND: </th></tr>';
					    	for ($i=0; $i < count($output->getReports->Legend); $i+=2) { 
					    		$agent_pdetail .= "<tr><td><small>".$output->getReports->Legend[$i]."</small></td><td><small>".$output->getReports->Legend[$i+1]."</small></td></tr>";
					    	}
					$agent_pdetail .= '</table><br/>';
			    }
		// end of legend

		// start of bottom table
			$agent_pdetail .= '<div>
					<table class="display responsive no-wrap table table-striped table-bordered table-hover" width="100%" id="agent_pdetail_bottom">
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
					    if($output->getReports->BOTsorted_output != NULL){
						    for($i=0; $i <= count($output->getReports->BOTsorted_output); $i++){
						        $agent_pdetail .= $output->getReports->BOTsorted_output[$i];
						    }
						}else{
							$agent_pdetail .= "";
						}
				$agent_pdetail .= '</tbody>';

				if($output->getReports->BOTsorted_output != NULL){
					$agent_pdetail .= '<tfoot><tr class="warning"><th nowrap> Total </th>';
							$agent_pdetail .= $output->getReports->TOT_AGENTS;
							$agent_pdetail .= $output->getReports->TOTtotTOTAL_MS;
							$agent_pdetail .= $output->getReports->TOTtotNONPAUSE_MS;
							$agent_pdetail .= $output->getReports->TOTtotPAUSEB_MS;
					$agent_pdetail .= '</tr></tfoot>';
				}

				$agent_pdetail .= '</table></div><br/>'; 
		// end of bottom table


		// start of login table
			if($output->getReports->SstatusesBOT != NULL){
				$agent_pdetail .= '<div>
					<table class="display responsive no-wrap table table-striped table-bordered table-hover" width="100%" id="agent_pdetail_login">
				    	<thead>
							<tr>';
								$agent_pdetail .= $output->getReports->SstatusesBOT;
								
				    $agent_pdetail .='</tr>
				        </thead>
				        <tbody>';
						    if($output->getReports->SstatusesBOTR != NULL){
							    for($i=0; $i <= count($output->getReports->SstatusesBOTR); $i++){
							        $agent_pdetail .= '<tr>'.$output->getReports->SstatusesBOTR[$i].'</tr>';
							    }
							}else{
								$agent_pdetail .= "";
							}
					$agent_pdetail .= '</tbody>';

					if($output->getReports->SstatusesBOTR != NULL){
						$agent_pdetail .= '<tfoot><tr class="warning">';
									if($output->getReports->SstatusesBSUM != NULL){
									    $agent_pdetail .= $output->getReports->SstatusesBSUM;
									}
						$agent_pdetail .= '</tr></tfoot>';
					}
			}
				$agent_pdetail .= '</table></div><br/>';
		// -- end of login table
		
		echo $agent_pdetail; // return for agent performance detail

	}// end of agent_pdetail

// DIAL STATUSES SUMMARY
	if($pageTitle == "dispo"){
		//var_dump($output->getReports->queryx);
		echo '<div>'.$output->getReports->TOPsorted_output.'</div>';
	}// end of dispo

// STATISTICAL REPORT
	if($pageTitle == "stats"){
		//print_r($output->getReports);
		//$increment_color = "009688";
		if($_POST["request"] == "daily"){
			$max = max(/*$output->getReports->data_calls->hour0, $output->getReports->data_calls->hour1, $output->getReports->data_calls->hour2, $output->getReports->data_calls->hour3, 
				$output->getReports->data_calls->hour4, $output->getReports->data_calls->hour5, $output->getReports->data_calls->hour6, $output->getReports->data_calls->hour7, */
				$output->getReports->data_calls->hour8, $output->getReports->data_calls->hour9, $output->getReports->data_calls->hour10, $output->getReports->data_calls->hour11, 
				$output->getReports->data_calls->hour12, $output->getReports->data_calls->hour13, $output->getReports->data_calls->hour14, $output->getReports->data_calls->hour15, 
				$output->getReports->data_calls->hour16, $output->getReports->data_calls->hour17, $output->getReports->data_calls->hour18, $output->getReports->data_calls->hour19, 
				$output->getReports->data_calls->hour20, $output->getReports->data_calls->hour21, $output->getReports->data_calls->hour22);/*, $hour23);*/
		}
		if($_POST["request"] == "weekly"){
			$max = max($output->getReports->data_calls->Day0, $output->getReports->data_calls->Day1, $output->getReports->data_calls->Day2, $output->getReports->data_calls->Day3, 
				$output->getReports->data_calls->Day4, $output->getReports->data_calls->Day5, $output->getReports->data_calls->Day6);
		}
		if($_POST["request"] == "monthly"){
			$max = max($output->getReports->data_calls->Month1, $output->getReports->data_calls->Month2, $output->getReports->data_calls->Month3, $output->getReports->data_calls->Month4, 
				$output->getReports->data_calls->Month5, $output->getReports->data_calls->Month6, $output->getReports->data_calls->Month7, $output->getReports->data_calls->Month8, 
				$output->getReports->data_calls->Month9, $output->getReports->data_calls->Month10, $output->getReports->data_calls->Month11, $output->getReports->data_calls->Month12);
		}
		
		if($max != NULL){
			$max_count = max($max);
		}else{
			$max_count = $max;
		}
		

		if($max_count <= 4){
			$max_count = 4;
		}

	?>
		<div collapse="panelChart9" class="panel-wrapper">
            <div class="panel-body">
               <div class="chart-splinev1 flot-chart"></div> <!-- data is in JS -> demo-flot.js -> search (Overall/Home/Pagkain)--> 
            </div>
        </div>
        <div id="legendBox"></div>

	    <br/><br/>
        <legend><small>Call Statistics</small></legend>
        	
		<div class="row">
			<div class="col-lg-4">
				<div class="panel widget bg-gray-light" style="height: 95px;">
					<div class="row status-box">
						<div class="col-xs-6 text-center bg-gray-lighter pv-md animated pulse">
							<h2><?php echo $output->getReports->total_calls;?></h2>
						</div>
						<div class="col-xs-6 pv-lg">
							<div class="text-sm">Total Calls</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="panel widget bg-gray-light" style="height: 95px;">
					<div class="row status-box">
						<div class="col-xs-6 text-center bg-gray-lighter pv-md animated pulse">
							<h2><?php echo count($output->getReports->data_agents->cuser);?></h2>
						</div>
						<div class="col-xs-6 pv-lg">
							<div class="text-sm">Total Agents</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="panel widget bg-gray-light" style="height: 95px;">
					<div class="row status-box">
						<div class="col-xs-6 text-center bg-gray-lighter pv-md animated pulse">
							<h2><?php echo $output->getReports->total_leads;?></h2>
						</div>
						<div class="col-xs-6 pv-lg">
							<div class="text-sm">Lead Count</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<br/><br/>
       	<legend><small>Disposition Stats</small></legend>

       	<?php 
       		if($output->getReports->data_status->status != NULL){
       	?>
    	<div class="row">
        	<div class="col-lg-4">
	       			<?php
	       				for($i = 0; $i < count($output->getReports->data_status->status); $i++){
	       					$percentage_stats = ($output->getReports->data_status->ccount[$i]/$output->getReports->total_calls)*100;
	       					$percentage_stats = number_format($percentage_stats, 2);
	       			?>
	       				<div class="row mb">
	       					<div class="col-lg-6">
	       						<h2 class="pull-right"><?php echo $percentage_stats;?>%</h2>
	       					</div>
	       					<div class="col-lg-6">
	       						<h5><?php echo $output->getReports->data_status->status_name[$i];?> (<?php echo $output->getReports->data_status->status[$i];?>)</h5>
	       						<span class="label label-purple"><?php echo $output->getReports->data_status->ccount[$i];?> calls</span>
	       					</div>
	       				</div>	
	       			<?php
	       				}
	       			?>
        	</div>

        	<div class="col-lg-8">
        		<div class="panel-body">
                   <div class="chart-pie flot-chart"></div>
                </div>
        	</div>
        </div>
        <?php
        	}else{
		?>
			<div class="row mb">
				<center><h3> - - - NO DATA - - - </h3></center>
			</div>	
		<?php
			}
        ?>

	<script>
        $(function(){
            var datav3 = [
            
            <?php
                	if($_POST["request"] == "daily"){
		                if($output->getReports->data_calls->cdate != NULL){ // if data exists
		                	for($i = 0; $i < count($output->getReports->data_calls->cdate); $i++){
								$cdate = $output->getReports->data_calls->cdate[$i];
								$hour0 = $output->getReports->data_calls->hour0[$i];
									if($hour0 == NULL)$hour0 = 0;
								$hour1 = $output->getReports->data_calls->hour1[$i];
									if($hour1 == NULL)$hour1 = 0;
								$hour2 = $output->getReports->data_calls->hour2[$i];
									if($hour2 == NULL)$hour2 = 0;
								$hour3 = $output->getReports->data_calls->hour3[$i];
									if($hour3 == NULL)$hour3 = 0;
								$hour4 = $output->getReports->data_calls->hour4[$i];
									if($hour4 == NULL)$hour4 = 0;
								$hour5 = $output->getReports->data_calls->hour5[$i];
									if($hour5 == NULL)$hour5 = 0;
								$hour6 = $output->getReports->data_calls->hour6[$i];
									if($hour6 == NULL)$hour6 = 0;
								$hour7 = $output->getReports->data_calls->hour7[$i];
									if($hour7 == NULL)$hour7 = 0;
								$hour8 = $output->getReports->data_calls->hour8[$i];
									if($hour8 == NULL)$hour8 = 0;
								$hour9 = $output->getReports->data_calls->hour9[$i];
									if($hour9 == NULL)$hour9 = 0;
								$hour10 = $output->getReports->data_calls->hour10[$i];
									if($hour10 == NULL)$hour10 = 0;
								$hour11 = $output->getReports->data_calls->hour11[$i];
									if($hour11 == NULL)$hour11 = 0;
								$hour12 = $output->getReports->data_calls->hour12[$i];
									if($hour12 == NULL)$hour12 = 0;
								$hour13 = $output->getReports->data_calls->hour13[$i];
									if($hour13 == NULL)$hour13 = 0;
								$hour14 = $output->getReports->data_calls->hour14[$i];
									if($hour14 == NULL)$hour14 = 0;
								$hour15 = $output->getReports->data_calls->hour15[$i];
									if($hour15 == NULL)$hour15 = 0;
								$hour16 = $output->getReports->data_calls->hour16[$i];
									if($hour16 == NULL)$hour16 = 0;
								$hour17 = $output->getReports->data_calls->hour17[$i];
									if($hour17 == NULL)$hour17 = 0;
								$hour18 = $output->getReports->data_calls->hour18[$i];
									if($hour18 == NULL)$hour18 = 0;
								$hour19 = $output->getReports->data_calls->hour19[$i];
									if($hour19 == NULL)$hour19 = 0;
								$hour20 = $output->getReports->data_calls->hour20[$i];
									if($hour20 == NULL)$hour20 = 0;
								$hour21 = $output->getReports->data_calls->hour21[$i];
									if($hour21 == NULL)$hour21 = 0;
								$hour22 = $output->getReports->data_calls->hour22[$i];
									if($hour22 == NULL)$hour22 = 0;
								$hour23 = $output->getReports->data_calls->hour23[$i];
									if($hour23 == NULL)$hour23 = 0;
            ?>
                {"label": <?php echo '"'.$cdate.'"';?>,
                "color": getRandomColor(),
                "data": [
	                <?php
	                    echo '["8 AM",'. $hour8 .'],';
	                    echo '["9 AM",'. $hour9 .'],';
	                    echo '["10 AM",'. $hour10 .'],';
	                    echo '["11 AM",'. $hour11 .'],';
	                    echo '["12 NN",'. $hour12 .'],';
	                    echo '["1 PM",'. $hour13 .'],';
	                    echo '["2 PM",'. $hour14 .'],';
	                    echo '["3 PM",'. $hour15 .'],';
	                    echo '["4 PM",'. $hour16 .'],';
	                    echo '["5 PM",'. $hour17 .'],';
	                    echo '["6 PM",'. $hour18 .'],';
	                    echo '["7 PM",'. $hour19 .'],';
	                    echo '["8 PM",'. $hour20 .'],';
	                    echo '["9 PM",'. $hour21 .'],';
	                    echo '["10 PM",'. $hour22 .']';
	                    echo ']}';
	                    /*echo '["11 PM",'. $hour23 .'],';
	                    echo '["12 AM",'. $hour0 .'],';
	                    echo '["1 AM",'. $hour1 .'],';
	                    echo '["2 AM",'. $hour2 .'],';
	                    echo '["3 AM",'. $hour3 .'],';
	                    echo '["4 AM",'. $hour4 .'],';
	                    echo '["5 AM",'. $hour5 .'],';
	                    echo '["6 AM",'. $hour6 .'],';
	                    echo '["7 AM",'. $hour7 .']';*/

			                $a = $i + 1;
			                	if(count($output->getReports->data_calls->cdate) > $a)
			                	echo ", ";
			               	}
			            }else{ //if data is null
			            		$cdate = "";
	        ?>
			            	{"label": <?php echo '"'.$cdate.'"';?>,
			                "color": getRandomColor(),
			                "data": [
			<?php
			                    echo '["8 AM", 0],';
			                    echo '["9 AM", 0],';
			                    echo '["10 AM", 0],';
			                    echo '["11 AM", 0],';
			                    echo '["12 NN", 0],';
			                    echo '["1 PM", 0],';
			                    echo '["2 PM", 0],';
			                    echo '["3 PM", 0],';
			                    echo '["4 PM", 0],';
			                    echo '["5 PM", 0],';
			                    echo '["6 PM", 0],';
			                    echo '["7 PM", 0],';
			                    echo '["8 PM", 0],';
			                    echo '["9 PM", 0],';
			                    echo '["10 PM", 0]';
			                    /*echo '["11 PM",'. $hour15 .'],';
			                    echo '["12 AM",'. $hour16 .'],';
			                    echo '["1 AM",'. $hour17 .'],';
			                    echo '["2 AM",'. $hour18 .'],';
			                    echo '["3 AM",'. $hour19 .'],';
			                    echo '["4 AM",'. $hour20 .'],';
			                    echo '["5 AM",'. $hour21 .'],';
			                    echo '["6 AM",'. $hour22 .'],';
			                    echo '["7 AM",'. $hour23 .']';*/
			            		echo "]}";
			            }
			        } //end of daily

			        else if($_POST["request"] == "weekly"){ // weekly
			        	if($output->getReports->data_calls->weekno != NULL){
				        	for($i = 0; $i < count($output->getReports->data_calls->weekno); $i++){
					        	$weekno = $output->getReports->data_calls->weekno[$i];
								$day0 = $output->getReports->data_calls->Day0[$i];
									if($day0 == NULL)$day0 = 0;
								$day1 = $output->getReports->data_calls->Day1[$i];
									if($day1 == NULL)$day1 = 0;
								$day2 = $output->getReports->data_calls->Day2[$i];
									if($day2 == NULL)$day2 = 0;
								$day3 = $output->getReports->data_calls->Day3[$i];
									if($day3 == NULL)$day3 = 0;
								$day4 = $output->getReports->data_calls->Day4[$i];
									if($day4 == NULL)$day4 = 0;
								$day5 = $output->getReports->data_calls->Day5[$i];
									if($day5 == NULL)$day5 = 0;
								$day6 = $output->getReports->data_calls->Day6[$i];
									if($day6 == NULL)$day6 = 0;
			?>
                {"label": <?php echo '"'.$weekno.'"';?>,
                "color": getRandomColor(),
                "data": [
	                <?php
	                    echo '["Mon",'. $day0 .'],';
	                    echo '["Tue",'. $day1 .'],';
	                    echo '["Wed",'. $day2 .'],';
	                    echo '["Thu",'. $day3 .'],';
	                    echo '["Fri",'. $day4 .'],';
	                    echo '["Sat",'. $day5 .'],';
	                    echo '["Sun",'. $day6 .']';
	                    echo ']}';

                			$a = $i + 1;
			                	if(count($output->getReports->data_calls->weekno) > $a)
			                	echo ", ";
			               	}
                		}else{ //if data is null
			            		$weekno = "";
	        ?>
			            	{"label": <?php echo '"'.$weekno.'"';?>,
			                "color": getRandomColor(),
			                "data": [
			<?php
			                    echo '["Mon", 0],';
			                    echo '["Tue", 0],';
			                    echo '["Wed", 0],';
			                    echo '["Thu", 0],';
			                    echo '["Fri", 0],';
			                    echo '["Sat", 0],';
			                    echo '["Sun", 0]';
			            		echo ']}';
			            }
            }
                	else if($_POST["request"] == "monthly"){ // weekly
                		if($output->getReports->data_calls->monthname != NULL){
				        	for($i = 0; $i < count($output->getReports->data_calls->monthname); $i++){
					        	$monthname = $output->getReports->data_calls->monthname[$i];
								$month1 = $output->getReports->data_calls->Month1[$i];
									if($month1 == NULL)$month1 = 0;
								$month2 = $output->getReports->data_calls->Month2[$i];
									if($month2 == NULL)$month2 = 0;
								$month3 = $output->getReports->data_calls->Month3[$i];
									if($month3 == NULL)$month3 = 0;
								$month4 = $output->getReports->data_calls->Month4[$i];
									if($month4 == NULL)$month4 = 0;
								$month5 = $output->getReports->data_calls->Month5[$i];
									if($month5 == NULL)$month5 = 0;
								$month6 = $output->getReports->data_calls->Month6[$i];
									if($month6 == NULL)$month6 = 0;
								$month7 = $output->getReports->data_calls->Month7[$i];
									if($month7 == NULL)$month7 = 0;
								$month8 = $output->getReports->data_calls->Month8[$i];
									if($month8 == NULL)$month8 = 0;
								$month9 = $output->getReports->data_calls->Month9[$i];
									if($month9 == NULL)$month9 = 0;
								$month10 = $output->getReports->data_calls->Month10[$i];
									if($month10 == NULL)$month10 = 0;
								$month11 = $output->getReports->data_calls->Month11[$i];
									if($month11 == NULL)$month11 = 0;
								$month12 = $output->getReports->data_calls->Month12[$i];
									if($month12 == NULL)$month12 = 0;
			    ?>
	                {"label": <?php echo '"'.$monthname.'"';?>,
	                "color": getRandomColor(),
	                "data": [
	        <?php
	                    echo '["Jan",'. $month1 .'],';
	                    echo '["Feb",'. $month2 .'],';
	                    echo '["Mar",'. $month3 .'],';
	                    echo '["Apr",'. $month4 .'],';
	                    echo '["May",'. $month5 .'],';
	                    echo '["Jun",'. $month6 .'],';
	                    echo '["Jul",'. $month7 .'],';
	                    echo '["Aug",'. $month8 .'],';
	                    echo '["Sep",'. $month9 .'],';
	                    echo '["Oct",'. $month10 .'],';
	                    echo '["Nov",'. $month11 .'],';
	                    echo '["Dec",'. $month12 .']';
	                    echo ']}';

                			$a = $i + 1;
			                	if(count($output->getReports->data_calls->monthname) > $a)
			                	echo ", ";
			            	}
                		}else{ //if data is null
			            		$monthname = "";
	        ?>
			            	{"label": <?php echo '"'.$monthname.'"';?>,
			                "color": getRandomColor(),
			                "data": [
			<?php
			                    echo '["Jan", 0],';
			                    echo '["Feb", 0],';
			                    echo '["Mar", 0],';
			                    echo '["Apr", 0],';
			                    echo '["May", 0],';
			                    echo '["Jun", 0],';
			                    echo '["Jul", 0],';
			                    echo '["Aug", 0],';
			                    echo '["Sep", 0],';
			                    echo '["Oct", 0],';
			                    echo '["Nov", 0],';
			                    echo '["Dec", 0]';
			            		echo ']}';
			            }
                	}

            	
            ?>
            ];

            var options = { series: { lines: {show: false}, points: {show: true,radius: 4},
                    splines: {show: true,tension: 0.4,lineWidth: 1,fill: 0.5}
                },
                grid: { borderColor: '#eee', borderWidth: 1, hoverable: true, backgroundColor: '#fcfcfc' },
                tooltip: true, 
                tooltipOpts: { content: function (label, x, y) { return y + ' Calls in ' + label; } },
                xaxis: { tickColor: '#fcfcfc', mode: 'categories' },
                yaxis: { min: 0, max: <?php echo $max_count;?>, // optional: use it for a clear represetation
                    tickColor: '#eee',
                    //position: 'right' or 'left',
                    tickFormatter: function (v) { return v/* + ' visitors'*/; }
                },
                shadowSize: 0,
                legend: {
                	show:true, 
                	noColumns: 8, 
                	container: $('#legendBox')
				}
              };
              var chartv3 = $('.chart-splinev1');
              if(chartv3.length)
                $.plot(chartv3, datav3, options);
        });

		function getRandomColor() {
		    var letters = '0123456789ABCDEF';
		    var color = '#';
		    for (var i = 0; i < 6; i++ ) {
		        color += letters[Math.floor(Math.random() * 16)];
		    }
		    return color;
		}

		// CHART PIE
		// ----------------------------------- 
		  $(function(){

		    var data = [
		    	<?php
		    		if($output->getReports->data_status->status != NULL){
		    			for($i = 0; $i < count($output->getReports->data_status->status); $i++){
		    	?>
					    { "label": <?php echo '"'.$output->getReports->data_status->status_name[$i].'('.$output->getReports->data_status->status[$i].')"'; ?>,
					      "color": getRandomColor(),
					      "data": <?php echo $output->getReports->data_status->ccount[$i]; ?>
					    }
		    	<?php
			    			$a = $i + 1;
		                	if(count($output->getReports->data_status->status) > $a)
		                	echo ", ";
		    			}
		    		}else{
		    	?>
		    			{ "label": "",
					      "color": getRandomColor(),
					      "data": 0
					    }
		    	<?php
		    		}
		    	?>
		    ];

		    var options = {
                series: {
                    pie: {
                        show: true,
                        innerRadius: 0,
                        label: {
                            show: true,
                            radius: 1,
                            formatter: function (label, series) {
                                return '<div class="flot-pie-label">' +
                                //label + ' : ' +
                                Math.round(series.percent) +
                                '%</div>';
                            },
                            background: {
                                opacity: 0.8,
                                color: '#222'
                            }
                        }
                    }
                }
            };

		    var chart = $('.chart-pie');
		    if(chart.length)
		      $.plot(chart, data, options);

		  });
	</script>
	
	<?php
	}

// SALES PER AGENT
	if($pageTitle == "sales_agent"){
		//var_dump($output->getReports);
		if($_POST['request'] == "outbound"){
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

				if($output->getReports->TOPsorted_output != NULL){
					//for($i=0; $i <= count($output->getReports->TOPsorted_output); $i++){
				    	$outbound .= $output->getReports->TOPsorted_output;
				   // }
				}else{
					$outbound .= "";
				}
					
			    $outbound .= '</tbody>';

			    if($output->getReports->TOPsorted_output != NULL){
				   		$outbound .= '<tfoot><tr class="warning"><th nowrap colspan="2"> Total Agents: ';
						    $outbound .= count($output->getReports->TOPsorted_output).'</th>';
						    $outbound .= '<th nowrap>'.$output->getReports->TOToutbound.'</th>';
						$outbound .= '</tr></tfoot>';
					}

				$outbound .= '</table></div><br/>'; 

			echo $outbound;
		}

		if($_POST['request'] == "inbound"){
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
					if($output->getReports->BOTsorted_output != NULL){
						//for($i=0; $i <= count($output->getReports->BOTsorted_output); $i++){
					    	$inbound .= $output->getReports->BOTsorted_output;
					    //}
					}else{
						$inbound .= "";
					}
			   		
			   		$inbound .= '</tbody>';
					
					if($output->getReports->BOTsorted_output != NULL){
				   		$inbound .= '<tfoot><tr class="warning"><th nowrap colspan="2"> Total Agents: ';
						    $inbound .= count($output->getReports->BOTsorted_output).'</th>';
						    $inbound .= '<th nowrap>'.$output->getReports->TOTinbound.'</th>';
						$inbound .= '</tr></tfoot>';
					}
					
					$inbound .= '</table></div>';

		    echo $inbound; // return for inbound
		}
	}

// SALES TRACKER
	if($pageTitle == "sales_tracker"){
		//var_dump($output->getReports);
		if($_POST['request'] == "outbound"){
		// Outbound table
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

			if($output->getReports->outbound_result != NULL){
				for($i=0; $i < count($output->getReports->sale_num); $i++){
			    	$sales_tracker .= '<tr>
							<td nowrap><a class="edit-contact" data-id="'.$output->getReports->lead_id[$i].'">'.$output->getReports->lead_id[$i].'</a></td>
							<td nowrap>'.$output->getReports->call_date[$i].'</td>
							<td nowrap>'.$output->getReports->agent[$i].'</td>
							<td nowrap>'.$output->getReports->phone_number[$i].'</td>
							<td nowrap>'.$output->getReports->first_name[$i].'</td>
							<td nowrap>'.$output->getReports->last_name[$i].'</td>
						</tr>';
			    }
			}else{
				$sales_tracker .= "";
			}
				
		    $sales_tracker .= '</tbody>';

			$sales_tracker .= '</table></div>';
			
		}
		
		if($_POST['request'] == "inbound"){
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
				if($output->getReports->inbound_result != NULL){
					for($i=0; $i < count($output->getReports->sale_num); $i++){
				    	$sales_tracker .= '<tr>
							<td nowrap><a class="edit-contact" data-id="'.$output->getReports->lead_id[$i].'">'.$output->getReports->lead_id[$i].'</a></td>
							<td nowrap>'.$output->getReports->call_date[$i].'</td>
							<td nowrap>'.$output->getReports->agent[$i].'</td>
							<td nowrap>'.$output->getReports->phone_number[$i].'</td>
							<td nowrap>'.$output->getReports->first_name[$i].'</td>
							<td nowrap>'.$output->getReports->last_name[$i].'</td>
						</tr>';
				    }
				}else{
					$sales_tracker .= "";
				}
		   		
		   		$sales_tracker .= '</tbody>';

				$sales_tracker .= '</table></div>';
				
		}
		
	echo $sales_tracker; // return for inbound
		
	}

// INBOUND CALL REPORT
	if($pageTitle == "inbound_report"){
		//var_dump($output);
		
		$inbound_report = "";

		$inbound_report .= '
		<div>
			<table class="display responsive no-wrap table table-striped table-bordered table-hover" id="inbound_report">
				<thead>
					<tr>
			            <th nowrap> # </th>
			            <th nowrap> Date </th>
			            <th nowrap> Agent ID </th>
			            <th nowrap> Phone Number </th>
			            <th nowrap> Time </th>
			            <th nowrap> Call Duration (IN SEC) </th>
			            <th nowrap> Disposition </th>
		            </tr>
		        </thead>
		        <tbody>
		';

		if($output->getReports->TOPsorted_output != NULL){
				for($i=0; $i < count($output->getReports->TOPsorted_output); $i++){
			    	$inbound_report .= $output->getReports->TOPsorted_output[$i];
			    }
			}else{
				$inbound_report .= "";
			}
				
		    $inbound_report .= '</tbody>';

		$inbound_report .= '</table></div>'; 

		echo $inbound_report;
	}
// DASHBOARD
	if($pageTitle == "dashboard"){
		var_dump($output->getReports);
	}
	
// EXPORT CALL REPORT	
	if ($pageTitle == "call_export_report"){
		// GET CAMPAIGNS
			$url = gourl."/goCampaigns/goAPI.php"; //URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; //Username goes here. (required)
			$postfields["goPass"] = goPass; //Password goes here. (required)
			$postfields["goAction"] = "getAllCampaigns"; //action performed by the [[API:Functions]]. (required)
			$postfields["responsetype"] = responsetype; //json. (required)
			$postfields["user_group"] = $_SESSION['usergroup'];
			$postfields["session_user"] = $_SESSION['user']; //current user
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			$data = curl_exec($ch);
			curl_close($ch);
			$campaigns = json_decode($data);
		
		// GET INBOUND GROUPS
			$url = gourl."/goInbound/goAPI.php"; //URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; //Username goes here. (required)
			$postfields["goPass"] = goPass; //Password goes here. (required)
			$postfields["goAction"] = "goGetAllInboundList"; //action performed by the [[API:Functions]]. (required)
			$postfields["responsetype"] = responsetype; //json. (required)
			$postfields["user_group"] = $_SESSION['usergroup'];
			$postfields["session_user"] = $_SESSION['user']; //current user
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			$data = curl_exec($ch);
			curl_close($ch);
			$inbound = json_decode($data);
		
		// GET LISTS
			$url = gourl."/goLists/goAPI.php"; //URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; //Username goes here. (required)
			$postfields["goPass"] = goPass; //Password goes here. (required)
			$postfields["goAction"] = "goGetAllLists"; //action performed by the [[API:Functions]]. (required)
			$postfields["responsetype"] = responsetype; //json. (required)
			$postfields["user_group"] = $_SESSION['usergroup'];
			$postfields["session_user"] = $_SESSION['user']; //current user
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			$data = curl_exec($ch);
			curl_close($ch);
			$list = json_decode($data);
			
		// GET STATUSES
			$url = gourl."/goDispositions/goAPI.php"; //URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; //Username goes here. (required)
			$postfields["goPass"] = goPass; //Password goes here. (required)
			$postfields["goAction"] = "getAllDispositions"; //action performed by the [[API:Functions]]. (required)
			$postfields["responsetype"] = responsetype; //json. (required)
	
			 $ch = curl_init();
			 curl_setopt($ch, CURLOPT_URL, $url);
			 curl_setopt($ch, CURLOPT_POST, 1);
			 curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			 curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			 $data = curl_exec($ch);
			 curl_close($ch);
			 $disposition = json_decode($data);
		
		$display = '';
		$display .= '
				<form action="./php/ExportCallReport.php" id="export_callreport_form" method="POST">
					<input type="hidden" name="log_user" value="'.$log_user.'" />
					<input type="hidden" name="log_group" value="'.$log_group.'" />
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label>Campaigns:</label>
								<div class="mb">
									 <div class="">
										 <select multiple="multiple" class="select2-3 form-control" id="selected_campaigns" name="campaigns[]" style="width:100%;">';
												for($i=0; $i < count($campaigns->campaign_id);$i++){
													$display .= '<option value="'.$campaigns->campaign_id[$i].'">'.$campaigns->campaign_id[$i].' - '.$campaigns->campaign_name[$i].'</option>';
												}
			$display .= '				 </select>
									 </div>
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label>Inbound Groups:</label>
								<div class="mb">
									 <div class="">
										 <select multiple="multiple" class="select2-3 form-control" id="selected_inbounds" name="inbounds[]" style="width:100%;">';
												for($i=0; $i < count($inbound->group_id);$i++){
													$display .= '<option value="'.$inbound->group_id[$i].'">'.$inbound->group_id[$i].' - '.$inbound->group_name[$i].'</option>';
												}
			$display .= '				 </select>
									 </div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label>Lists:</label>
								<div class="mb">
									 <div class="">
										 <select multiple="multiple" class="select2-3 form-control" id="selected_lists" name="lists[]" style="width:100%;">';
										 $display .= '<option value="ALL" selected>--- ALL ---</option>';
												for($i=0; $i < count($list->list_id);$i++){
													$display .= '<option value="'.$list->list_id[$i].'">'.$list->list_id[$i].' - '.$list->list_name[$i].'</option>';
												}
			$display .= '				 </select>
									 </div>
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label>Statuses:</label>
								<div class="mb">
									 <div class="">
										 <select multiple="multiple" class="select2-3 form-control" id="selected_statuses" name="statuses[]" style="width:100%;">';
										 $display .= '<option value="ALL" selected>--- ALL ---</option>';
												for($i=0; $i < count($disposition->status);$i++){
													$display .= '<option value="'.$disposition->status[$i].'">'.$disposition->status[$i].' - '.$disposition->status_name[$i].'</option>';
												}
			$display .= '				 </select>
									 </div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label>Custom Fields:</label>
								<div class="mb">
									 <div class="">
										 <select class="form-control" id="selected_custom_fields" name="custom_fields">
											<option value="N">NO</option>
											<option value="Y">YES</option>
										 </select>
									 </div>
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label>Per Call Notes:</label>
								<div class="mb">
									 <div class="">
										 <select class="form-control" id="selected_per_call_notes" name="per_call_notes">
											<option value="N">NO</option>
											<option value="Y">YES</option>
										 </select>
									 </div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label>Recording Location:</label>
								<div class="mb">
									 <div class="">
										 <select class="form-control" id="selected_rec_location" name="rec_location">
											<option value="N">NO</option>
											<option value="Y">YES</option>
										 </select>
									 </div>
								</div>
							</div>
						</div>
					</div>
				</form>
					<div class="row">
						<center><button class="btn btn-info" name="submit_export" id="submit_export"><li class="fa fa-download"> Submit & Download</button>
					</div>
					<span id="result_export"></span>
		';
		//var_dump($disposition);
		echo $display;
	?>
		<script>
			// initialize multiple selecting
			$('.select2-3').select2({
				theme: 'bootstrap'
			});
			
			$(document).on('click','#submit_export',function() {
				$('#submit_export').html("Downloading.....");
				$('#submit_export').attr("disabled", true);
				
				toDateVal = $('#start_filterdate').val();
				$('#export_callreport_form').append("<input type='hidden' name='fromDate' value='"+
									toDateVal+"' />");
				fromDateVal = $('#end_filterdate').val();
				$('#export_callreport_form').append("<input type='hidden' name='toDate' value='"+
									fromDateVal+"' />");
				
				//alert($("#toDate").val());
				
				$( "#export_callreport_form" ).submit();
				
				$('#submit_export').html('<li class="fa fa-download"> Submit & Download');
				$('#submit_export').attr("disabled", false);
				
				/*$.ajax({
					url: "./php/ExportCallReport.php",
					type: 'POST',
					data: {
						campaigns : $('#selected_campaigns').val(),
						inbounds : $('#selected_inbounds').val(),
						lists : $('#selected_lists').val(),
						statuses : $('#selected_statuses').val(),
						custom_fields : $('#selected_custom_fields').val(),
						per_call_notes : $('#selected_per_call_notes').val(),
						start_filterdate : $('#start_filterdate').val(),
						end_filterdate : $('#end_filterdate').val()
					},
					success: function(data) {
						$('#submit_export').html('<li class="fa fa-download"> Submit & Download');
						$('#submit_export').attr("disabled", false);
						$('#result_export').html(data);
						console.log(data);
						var uri = 'data:application/csv;charset=UTF-8,' + encodeURIComponent(response);
						window.open(uri, 'test.csv');
					}
				});*/
			});
		</script>
	<?php	
	}
	
	echo '</div>';
	// end of display
	
// ------ FOR MODALS AND OTHERS
	if($pageTitle == "sales_tracker"){
		if($output->getReports->inbound_result != NULL || $output->getReports->outbound_result != NULL){
			
			$modal_sales_tracker = "";
			
			for($i=0; $i < count($output->getReports->phone_number); $i++){
				$sale_num = $output->getReports->sale_num[$i];
				if($sale_num == NULL)
					$sale_num = " - - - ";
				$call_date = $output->getReports->call_date[$i];
				if($call_date == NULL)
					$call_date = " - - - ";
				$agent = $output->getReports->agent[$i];
				if($agent == NULL)
					$agent = " - - - ";
				$phone_number = $output->getReports->phone_number[$i];
				if($phone_number == NULL)
					$phone_number = " - - - ";
				$full_name = $output->getReports->first_name[$i].' '.$output->getReports->last_name[$i];
				if($full_name == NULL || $full_name == " ")
					$full_name = " - - - ";
				$address = $output->getReports->address[$i];
				if($address == NULL)
					$address = " - - - ";
				$city = $output->getReports->city[$i];
				if($city == NULL)
					$city = " - - - ";
				$state = $output->getReports->state[$i];
				if($state == NULL)
					$state = " - - - ";
				$postal = $output->getReports->postal[$i];
				if($postal == NULL)
					$postal = " - - - ";
				$email = $output->getReports->email[$i];
				if($email == NULL)
					$email = " - - - ";
				$alt_phone = $output->getReports->alt_phone[$i];
				if($alt_phone == NULL)
					$alt_phone = " - - - ";
				$comments = $output->getReports->comments[$i];
				if($comments == NULL)
					$comments = " - - - ";
			### MODAL ###
			$modal_sales_tracker .= '
				<div class="modal fade" id="modal_sale_num_'.$output->getReports->sale_num[$i].'" >
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title animated bounceInRight"> 
									<b>Customer Sales Information</b>
									<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
								</h4>
							</div>
							<div class="modal-body">
								<div class="form-group">
									<label class="col-lg-6 control-label">Sale Number: </label>
									<div class="col-lg-6 reverse_control_label mb">
										'.$sale_num.'
									</div>
								</div>
								<div class="form-group">
									<label class="col-lg-6 control-label">Call Date: </label>
									<div class="col-lg-6 reverse_control_label mb">
										'.$call_date.'
									</div>
								</div>
								<div class="form-group">
									<label class="col-lg-6 control-label">Agent: </label>
									<div class="col-lg-6 reverse_control_label mb">
										'.$agent.'
									</div>
								</div>
								<div class="form-group">
									<label class="col-lg-6 control-label">Phone Number: </label>
									<div class="col-lg-6 reverse_control_label mb">
										'.$phone_number.'
									</div>
								</div>
								<div class="form-group">
									<label class="col-lg-6 control-label">Full Name: </label>
									<div class="col-lg-6 reverse_control_label mb">
										'.$full_name.'
									</div>
								</div>
								<div class="form-group">
									<label class="col-lg-6 control-label">Address: </label>
									<div class="col-lg-6 reverse_control_label mb">
										'.$address.'
									</div>
								</div>
								<div class="form-group">
									<label class="col-lg-6 control-label">City: </label>
									<div class="col-lg-6 reverse_control_label mb">
										'.$city.'
									</div>
								</div>
								<div class="form-group">
									<label class="col-lg-6 control-label">State: </label>
									<div class="col-lg-6 reverse_control_label mb">
										'.$state.'
									</div>
								</div>
								<div class="form-group">
									<label class="col-lg-6 control-label">Postal: </label>
									<div class="col-lg-6 reverse_control_label mb">
										'.$postal.'
									</div>
								</div>
								<div class="form-group">
									<label class="col-lg-6 control-label">Email: </label>
									<div class="col-lg-6 reverse_control_label mb">
										'.$email.'
									</div>
								</div>
								<div class="form-group">
									<label class="col-lg-6 control-label">Alt Phone: </label>
									<div class="col-lg-6 reverse_control_label mb">
										'.$alt_phone.'
									</div>
								</div>
								<div class="form-group">
									<label class="col-lg-6 control-label">Comments: </label>
									<div class="col-lg-6 reverse_control_label mb">
										'.$comments.'
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>';
			}
			
			echo $modal_sales_tracker;
		}
	}
	
}else{
	echo $output->result;
}

//print_r($output);
?>
