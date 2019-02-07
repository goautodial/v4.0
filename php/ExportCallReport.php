<?php
/**
 * @file        ExportAgentDetails.php
 * @brief       Handles Exporting of Call Report Requests
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
require_once('CRMDefaults.php');

$api = \creamy\APIHandler::getInstance();
ini_set('memory_limit', '2048M');

    $postfields["goAction"] = "goExportCallReport";
    $postfields["pageTitle"] = "call_export_report";

    if(isset($_POST['campaigns']) && $_POST['campaigns'] != NULL){
		$campaigns = $_POST['campaigns'];
		$campaigns = implode(",", $campaigns);
		$postfields["campaigns"] = $campaigns;
	}else{
		$postfields["campaigns"] = "";
	}
    
    if(isset($_POST['inbounds']) && $_POST['inbounds'] != NULL){
		$inbounds = $_POST['inbounds'];
		$inbounds = implode(",", $inbounds);
		$postfields["inbounds"] = $inbounds;
	}else{
		$postfields["inbounds"] = "";
	}
    
    if(isset($_POST['lists']) && $_POST['lists'] != NULL){
		$lists = $_POST['lists'];
		$lists = implode(",", $lists);
		$postfields["lists"] = $lists;
	}else{
		$postfields["lists"] = "";
	}
    
    if(isset($_POST['statuses']) && $_POST['statuses'] != NULL){
		$statuses = $_POST['statuses'];
		$statuses = implode(",", $statuses);
		$postfields["statuses"] = $statuses;
	}else{
		$postfields["statuses"] = "";
	}
    
    $custom_fields = $_POST['custom_fields']; 
    $per_call_notes = $_POST['per_call_notes'];
    $rec_location = $_POST['rec_location'];
	
	$toDate = date('Y-m-d H:i:s', strtotime($_POST['toDate']));
    $fromDate = date('Y-m-d H:i:s', strtotime($_POST['fromDate']));

    $postfields["custom_fields"] = $custom_fields;
    $postfields["per_call_notes"] = $per_call_notes;
    $postfields["rec_location"] = $rec_location;

    if($toDate != NULL)
    $postfields["toDate"] = $toDate;
    
    if($fromDate != NULL)
    $postfields["fromDate"] = $fromDate;
    
    $output = $api->API_Request("goReports", $postfields);

    if($output->result == "success"){
        
        $header = implode(",",$output->header);
        
        $filename = "Export_Call_Report.".date("Y-m-d").".csv";
        
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename='.$filename);
        
	echo $header."\n";
	$array_rows = json_decode(json_encode($output->rows), true);
	foreach($array_rows as $temp){
	    foreach($temp as $value){
	        echo $value.",";
	    }
	    echo "\n";
	}  
    }
//    var_dump($output);
?>
