<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

require_once('php/goCRMAPISettings.php');

$pageTitle = $_POST['pageTitle'];

$url = gourl."/goJamesReports/goAPI.php"; #URL to GoAutoDial API. (required)
$postfields["goUser"] = goUser; #Username goes here. (required)
$postfields["goPass"] = goPass; #Password goes here. (required)
$postfields["goAction"] = "goGetReports"; #action performed by the [[API:Functions]]. (required)
$postfields["responsetype"] = responsetype; #json. (required)
$postfields["pageTitle"] = $pageTitle;

if(isset($_POST["fromDate"])){
	$fromDate = date('Y-m-d H:i:s', strtotime($_POST['fromDate']));
}else{
	$fromDate = date('Y-m-d H:i:s');
}

if($_POST["toDate"] != "" && $_POST["fromDate"] != ""){
	$toDate = date('Y-m-d H:i:s', strtotime($_POST['toDate']));
}else{
	$toDate = date('Y-m-d H:i:s');
}

$postfields["fromDate"] 	= $fromDate;
$postfields["toDate"] 		= $toDate;

if(isset($_POST["campaignID"]))
$postfields["campaignID"] 	= $_POST["campaignID"];

if(isset($_POST["request"]))
$postfields["request"] 		= $_POST["request"];

if(isset($_POST["userID"]))
$postfields["userID"] 		= $_POST["userID"];

if(isset($_POST["userGroup"]))
$postfields["userGroup"] 	= $_POST["userGroup"];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 100);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
$data = curl_exec($ch);
curl_close($ch);
$output = json_decode($data);

if($output->result == "success"){
	echo '<div class="responsive animated bounceInUp">';

	if($pageTitle == "agent_detail"){
		$agent_detail = '';
		
		$agent_detail .= '<div class="table-responsive">
			<table class="table table-striped table-bordered table-hover">
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
			for($i=0; $i <= count($output->getReports->TOPsorted_output); $i++){
		    	$agent_detail .= $output->getReports->TOPsorted_output[$i];
		    }
		}else{
			$agent_detail .= "<tr><td colspan='10'><center> - - - NO AVAILABLE DATA - - - </center></td></tr>";
		}
			
	    $agent_detail .= '</tbody></table>';

	    echo $agent_detail; // return for agent details

	}// end of agent_detail

	if($pageTitle == "agent_pdetail"){
		$agent_pdetail = '';

		// start of top table
			$agent_pdetail .= '<div class="table-responsive">
				<table class="table table-striped table-bordered table-hover">
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
							$agent_pdetail .= "<tr><td colspan='16'><center> - - - NO AVAILABLE DATA - - - </center></td></tr>";
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
			    $agent_pdetail .= '<div class="table-responsive">
				    <table class="table table-striped table-bordered table-hover">
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
					$agent_pdetail .= '<table class="table">
					    	<tr><th colspan="2"><small>LEGEND: </th></tr>
					    	<tr><td><small> DNC = DO NOT CALL </small></td><td><small> A = Answering Machine </small></td></tr>
					    	<tr><td><small> N = No Answer </small></td><td><small> MAIL = MAILER REQUESTED </small></td></tr>
					    	<tr><td><small> DC	= Disconnected Number </small></td><td><small> YESJM = WILL VOTE FOR JMEDINA </small></td></tr>
					    	</table><br/>';
			    }
		// end of legend

		// start of bottom table
			$agent_pdetail .= '<div class="table-responsive">
				<table class="table table-striped table-bordered table-hover">
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
							$agent_pdetail .= "<tr><td colspan='5'><center> - - - NO AVAILABLE DATA - - - </center></td></tr>";
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
				$agent_pdetail .= '<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover">
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
								$agent_pdetail .= "<tr><td colspan='3'><center> - - - NO AVAILABLE DATA - - - </center></td></tr>";
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

	if($pageTitle == "dispo"){
		echo '<div class="table-responsive">'.$output->getReports->TOPsorted_output.'</div>';
	}// end of dispo

	echo '</div>';
}else{
	echo $output->result;
}

//print_r($output);
?>
