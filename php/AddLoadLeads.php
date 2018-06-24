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
require_once('APIHandler.php');
$api = \creamy\APIHandler::getInstance();

ini_set('memory_limit','1024M');
	ini_set('upload_max_filesize', '6000M');
	ini_set('post_max_size', '6000M');
	
	/*
	$url = gourl."/goUploadLeads/goAPI.php"; #URL to GoAutoDial API. (required)
	
	$postfields["goUser"]         = goUser; #Username goes here. (required)
	$postfields["goPass"]         = goPass; #Password goes here. (required)
	$postfields["goAction"]       = "goUploadMe"; #action performed by the [[API:Functions]]. (required)
	$postfields["responsetype"]   = responsetype; #json. (required)
	$postfields["hostname"]       = $_SERVER['REMOTE_ADDR']; #Default value
	$postfields["goFileMe"]       = curl_file_create($_FILES['file_upload']['tmp_name'], $_FILES['file_upload']['type'], $_FILES["file_upload"]["name"]);
	$postfields["goListId"]       = $_REQUEST['list_id'];
	$postfields["goDupcheck"]       = $_REQUEST['goDupcheck'];
	$postfields["log_user"]		  = $_REQUEST['log_user'];
	$postfields["log_group"]	  = $_REQUEST['log_group'];
	$home = "../telephonylist.php";
	*/

	$postfields = array(
		'goAction' => 'goUploadMe',
		'goFileMe' => curl_file_create($_FILES['file_upload']['tmp_name'], $_FILES['file_upload']['type'], $_FILES["file_upload"]["name"]),
		'goListId' => $_REQUEST['list_id'], 
		'goDupcheck' => $_REQUEST['goDupcheck']
	);

	$output = $api->API_addLoadLeads($postfields);

	if ($output->result == "success") {
		
		//header("Location: ".$home."?message=success&RetMesg=".$output->message);
		$status = $output->message;
	} else {
		
		#header("Location: ".$home."?message=error&RetMesg=".$output->message);
		$status = $output->message;
		
	}
	
	echo $status;

?>