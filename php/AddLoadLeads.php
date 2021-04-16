<?php 
/**
 * @file        AddLoadLeads.php
 * @brief       Handles Upload Leads Request
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Noel Umandap
 * @author      Alexander Jim Abenoja  <alex@goautodial.com>
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

//ini_set('display_errors',1);
//error_reporting(E_ALL);

	require_once('APIHandler.php');
	require_once('CRMDefaults.php');
	$api = \creamy\APIHandler::getInstance();

	ini_set('memory_limit','2048M');
	ini_set('upload_max_filesize', '600M');
	ini_set('post_max_size', '600M');
	ini_set('max_execution_time', 3600);
	
	$postfields = array(
		'goFileMe' => curl_file_create($_FILES['file_upload']['tmp_name'], $_FILES['file_upload']['type'], $_FILES["file_upload"]["name"]),
		'goListId' => $_REQUEST['list_id'], 
		'goDupcheck' => $_REQUEST['goDupcheck'],
		'phone_code_override' => $_REQUEST['phone_code_override']
	);
	
	//customizations
	$postfields["custom_delimiter"] = LEADUPLOAD_CUSTOM_DELIMITER;
	
	//if(LEADUPLOAD_LEAD_MAPPING === "y"){
                if(!isset($_POST["LeadMapSubmit"]))
                        $postfields["goAction"]       = "goUploadMe";
                elseif(isset($_POST["LeadMapSubmit"]) && $_POST["LeadMapSubmit"] === "1")
                        $postfields["goAction"]       = "goUploadMe"; 
		elseif(isset($_POST["LeadMapSubmit"]) && $_POST["LeadMapSubmit"] === "0")
                        $postfields["goAction"]       = "goReadUpload";
		else
			die("Couldn't catch Action");
        /*}else{
                $postfields["goAction"]       = "goUploadMe";
        }*/
	
	//if(LEADUPLOAD_LEAD_MAPPING === "y" && isset($_POST["LeadMapSubmit"]) && $_POST["LeadMapSubmit"] === "1"){
	if(isset($_POST["LeadMapSubmit"]) && $_POST["LeadMapSubmit"] === "1"){
                $map_data = $_POST["map_data"];
		$map_data = implode(",",$map_data);
		$postfields["lead_mapping_data"] = $map_data;
		
		$map_fields = $_POST["map_fields"];
		$map_fields = implode(",", $map_fields);
		$postfields["lead_mapping_fields"] = $map_fields;
		
		$postfields["lead_mapping"] = "y";
		//$postfields["lead_mapping"] = LEADUPLOAD_LEAD_MAPPING;
	}
	
	$return = $api->API_Upload("goUploadLeads", $postfields, "data");
	$output = $return["output"];
	$data = $return["data"];
	
	//if(LEADUPLOAD_LEAD_MAPPING === "y" && $_POST["LeadMapSubmit"] === "0"){
	if(isset($_POST["LeadMapSubmit"]) && $_POST["LeadMapSubmit"] === "0"){
		print_r($data);
	}else{
		
		$res["result"] = $output->result;
		$res["msg"] = $output->message;	
		$res["dups"] = $output->duplicates;	
		print_r(json_encode($res));
		//var_dump($data);
	}
?>
