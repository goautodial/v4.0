<?php
/**
 * @file        ExportList.php
 * @brief       Handles Exporting of Lists
 * @copyright   Copyright (c) 2018 GOautoial Inc.
 * @author      Alexander Jim Abenoja
 * @author		Demian Lizandro A. Biscocho 
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

	ini_set('memory_limit', '2048M');
	require_once('APIHandler.php');
	$api 										= \creamy\APIHandler::getInstance();
	$list_id									= $_POST["listid"];
    $output										= $api->API_listExport($list_id);
	
    if ($output->result == "success") {
        //$filename = $output->getReports->filename;
        
        $header 								= implode(",", $output->header);        
        $filename 								= "LIST_.".$_POST["listid"]."_".date("Ymd")."_".date("His").".csv";
        //$fp = fopen($filename, 'w');
        
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename='.$filename);
        
        echo $header."\n";
		
        for($i=0; $i < count($output->row); $i++){
			$row = $output->row[$i];
			
			// filter data replaces comma with |
			for($x=0; $x < count($row);$x++){
				$row_data = str_replace(",","|",$row[$x]);
				$filtered_row[] = $row_data;
			}
			
			$array_filtered_row = implode(",",$filtered_row);
			echo $array_filtered_row;
			echo "\n";
			
			$array_filtered_row = "";
			unset($filtered_row);			
        }
        
        
    } else {
		echo "Failed to Process Request... Please inform the administrator.";
	}
   
?>
