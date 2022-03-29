<?php
/**
 * @file        inboundreport.php
 * @brief       Handles report requests
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      Alexander Jim H. Abenoja
 *		John Ezra D. Gois
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
	$fromDate 									= date('Y-m-d 00:00:01');
	$toDate 									= date('Y-m-d 23:59:59');
	$campaign_id 								= NULL;
	$request									= NULL;
	$userID										= NULL;
	$userGroup									= NULL;
	$statuses									= NULL;
	
	if (isset($_POST['pageTitle']) && $pageTitle != "call_export_report") {
		$pageTitle = $_POST['pageTitle'];
		$pageTitle = stripslashes($pageTitle);
	}
			
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
		
	if (isset($_POST["statuses"])) {
		$statuses = $_POST["statuses"];
		$statuses = stripslashes($statuses);
	}
		
	$postfields = array(
		'goAction' => 'goGetInboundReport',		
		'pageTitle' => $pageTitle,
		'fromDate' => $fromDate,
		'toDate' => $toDate,
		'campaignID' => $campaign_id,
		'request' => $request,
		'statuses' => $statuses
	);

	$output = $api->API_getReports($postfields);
//var_dump($output);
	if ($output->result == "success") {
/*		echo '<div class="animated slideInLeft">';
			echo '<div>'.$output->TOPsorted_output.'</div>';
		echo '</div>';
		
		echo '<div class="animated slideInRight">';
			echo '<div>'.$output->BOTsorted_output.'</div>';
		echo '</div>';
*/

                $inbound_report .= '
                        <div>
                                <legend><small><i> INBOUND </i></small></legend>
                                        <table class="display responsive no-wrap table table-striped table-bordered table-hover"  id="inbound_report">
                                                <thead>
                                                        <tr>
                                                                <th nowrap> Lead ID </th>
                                                                <th nowrap> Call Date </th>
                                                                <th nowrap> Agent </th>
                                                                <th nowrap> Phone Number </th>
                                                                <th nowrap> Time </th>
                                                                <th nowrap> Length </th>
                                                                <th nowrap> Status </th>
                                                        </tr>
                                                </thead>
                                                <tbody>
                        ';
                if ($output->TOPsorted_output != NULL) {
		//	foreach($output->TOPsorted_output as $row){
		                $inbound_report .= $output->TOPsorted_output;
		//	}
                }else{
 	               $inbound_report .= "";
                }

     	        $inbound_report .= '</tbody>';
                $inbound_report .= '</table></div>';
		
		echo '<div class="animated slideInLeft">';
			echo '<div>'.$inbound_report.'</div>';
		echo '</div>';
	//	var_dump($output->TOPsorted_output);
        }

?>
