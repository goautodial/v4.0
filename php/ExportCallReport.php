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
    $postfields["campaigns"] = NULL;
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

$limit = 20000;
$offset = 0;

if($toDate != NULL)
    $postfields["toDate"] = $toDate;

if($fromDate != NULL)
    $postfields["fromDate"] = $fromDate;

$postfields["goAction"] = "goExportCountRows";
$row_output = $api->API_Request("goReports", $postfields);

$postfields["goAction"] = "goExportCallReport";

$data_header = [];
$data_row = "";
$display = "";
$display2 = "";

if($row_output->result == "success"){
	$count = $row_output->row_count;

	if($count > $limit){
		$postfields["limit"] = $limit;
		$postfields["offset"] = $offset;
		while($last_row_offset <= $count){
			$postfields["offset"] = $offset;
			$output = $api->API_Request("goReports", $postfields);

			if($output->result == "success"){

				if($offset == 0){
					$data_header = $output->header;
				}
                //$data_row[] = json_decode(json_encode($output->rows), true);
                $data_row .= $output->rows;
			}
			$last_row_offset = $offset;
			$offset = $offset + $limit;
			// $data_row = array_merge($data_row);
		}
		$i = 0;
		// foreach($data_row as $array_rows){
		//     foreach($array_rows as $temp){
	    //     	foreach($temp as $value){
		// 		if($i <= 250000){
	    //     	    		$display .= $value.",";
		// 		} else {
		// 			$display2 .= $value.",";
		// 		}
		//         }
		// 	if($i <= 250000){
	    //     		$display .= "\n";
		// 	} else {
		// 		$display2 .= "\n";
		// 	}
	    //         }
		//     $i++;
        // }
        
        $display = $data_row;
	} else {
		$output = $api->API_Request("goReports", $postfields);
		$data_header = $output->header;
		$data_row = $output->rows;
		
		// $array_rows = json_decode(json_encode($data_row), true);
        // foreach($array_rows as $temp){
        //     foreach($temp as $value){
        //         $display .= $value.",";
        //     }
        //     $display .= "\n";
        // }

        $display = $data_row;
	}

    if($output->result == "success"){
    
    //$header = implode(",",$output->header);
    $header = implode(",",$data_header);

    $filename = "Export_Call_Report.".date("Y-m-d").".csv";
    
    header('Content-type: application/csv');
    header('Content-Disposition: attachment; filename='.$filename);
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    ob_clean();
    flush();

    echo $header."\n";
    echo $display;
    
    }

}
//var_dump($array_rows);
?>
