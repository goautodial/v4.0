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

    $postfields["goAction"] = "goGetAgentPerformanceDetails";

    if(isset($_POST['campaigns']) && $_POST['campaigns'] != NULL){
		$campaigns = $_POST['campaigns'];
		$campaigns = implode(",", $campaigns);
		$postfields["campaigns"] = $campaigns;
	}else{
		$postfields["campaigns"] = "";
	}
    
    $request = "EXPORT";
    $postfields["request"] = $request;
    
    $toDate = date('Y-m-d H:i:s', strtotime($_POST['toDate']));
    $fromDate = date('Y-m-d H:i:s', strtotime($_POST['fromDate']));

    if($toDate != NULL)
    $postfields["toDate"] = $toDate;
    
    if($fromDate != NULL)
    $postfields["fromDate"] = $fromDate;
    
    $output = $api->API_Request("goReports", $postfields);

    if($output->result == "export"){
        
        $header = $output->cols;
        
        $filename = "Export_Performance_Detail.".date("Y-m-d").".csv";
        
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename='.$filename);
        
	echo $header."\n";
    	echo $output->rows;
    }
 //   var_dump($output);
?>
