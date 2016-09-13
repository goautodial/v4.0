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

if(isset($_POST["fromDate"]))
$postfields["fromDate"] 	= $_POST["fromDate"];

if(isset($_POST["toDate"]))
$postfields["toDate"] 		= $_POST["toDate"];

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
	echo '<div class="animated bounceInUp">';

	if($pageTitle == "agent_detail"){

		echo '
			<table class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
			            <th>Full Name</th>
			            <th>User Name</th>
			            <th>Calls</th>
			            <th>Agent Time</th>
			            <th>WAIT</th>
			            <th>Talk</th>
			            <th>Dispo</th>
			            <th>Pause</th>
			            <th>Wrap-Up</th>
			            <th>Customer</th>
		            </tr>
		        </thead>
		        <tbody>
		';

		if($output->getReports->TOPsorted_output != NULL){
			for($i=0; $i <= count($output->getReports->TOPsorted_output); $i++){
		    	echo $output->getReports->TOPsorted_output[$i];
		    }
		}else{
			echo "<tr><td colspan='10'><center> - - - NO AVAILABLE DATA - - - </center></td></tr>";
		}
			
	    echo '</tbody></table>';

	}// end of agent_detail

	if($pageTitle == "agent_pdetail"){

		echo '
			<table class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
			            <th>Full Name</th>
			            <th>ID</th>
			            <th>Calls</th>
			            <th>Time</th>
						<th>Pause</th>
						<th>» Avg</th>
			            <th>WAIT</th>
						<th>» Avg</th>
			            <th>Talk</th>
						<th>» Avg</th>
			            <th>Dispo</th>
						<th>» Avg</th>
			            <th>Wrap-Up</th>
						<th>» Avg</th>
			            <th>Customer</th>
						<th>» Avg</th>
		            </tr>
		        </thead>
		        <tbody>
		';

		if($output->getReports->TOPsorted_output != NULL){
			for($i=0; $i <= count($output->getReports->TOPsorted_output); $i++){
		    	echo $output->getReports->TOPsorted_output[$i];
		    }
		}else{
			echo "<tr><td colspan='16'><center> - - - NO AVAILABLE DATA - - - </center></td></tr>";
		}

	    echo '</tbody></table><br/><br/>';

	    echo '<table class="table table-striped table-bordered table-hover">
	    	<thead>
				<tr>
		            <th>Full Name</th>
		            <th>DNC</th>
		            <th>A</th>
		            <th>N</th>
		            <th>MAIL</th>
	            </tr>
	        </thead>
	        <tbody>
	    ';
	    if($output->getReports->BOTsorted_output != NULL){
		    for($i=0; $i <= count($output->getReports->BOTsorted_output); $i++){
		        echo $output->getReports->BOTsorted_output[$i];
		    }
		}else{
			echo "<tr><td colspan='5'><center> - - - NO AVAILABLE DATA - - - </center></td></tr>";
		}
		    echo '</tbody></table><br/>
		    	<table>
		    	<tr><th colspan="2"><small>LEGEND:</th></tr>
		    	<tr><td><small> DNC = DO NOT CALL </small></td><td><small> A = Answering Machine </small></td></tr>
		    	<tr><td><small> N = No Answer </small></td><td><small> MAIL = MAILER REQUESTED </small></td></tr>
		    	<tr><td><small> DC	= Disconnected Number </small></td><td><small> YESJM = WILL VOTE FOR JMEDINA </small></td></tr>
		    	</table>
		    ';

	}

	if($pageTitle == "dispo"){
		echo $output->getReports->TOPsorted_output;
	}

	echo '</div>';
}

//print_r($output);
?>
