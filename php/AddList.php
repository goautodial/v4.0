<?php
/**
 * @file        AddList.php
 * @brief       Handles Add List Request
 * @copyright   Copyright (C) GOautodial Inc.
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

/*
	$url = gourl."/goLists/goAPI.php"; # URL to GoAutoDial API file
	$postfields["goUser"] 			= goUser; #Username goes here. (required)
	$postfields["goPass"] 			= goPass; #Password goes here. (required)
	$postfields["goAction"] 		= "goAddList"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] 	= responsetype; #json (required)
	$postfields["hostname"] 		= $_SERVER['REMOTE_ADDR']; #Default value
	
	$postfields["list_id"] 			= $_POST['add_list_id']; 
	$postfields["list_name"] 		= $_POST['list_name']; 
	$postfields["list_description"] = $_POST['list_desc'];
	$postfields["campaign_id"] 		= $_POST['campaign_select'];
	$postfields["active"] 			= $_POST['status'];
	
	$postfields["log_user"] 		= $_POST['log_user'];
	$postfields["log_group"] 		= $_POST['log_group'];
*/
	$postfields = array(
		'goAction' => 'goAddList',
		'list_id' => $_POST['add_list_id'], 
		'list_name' => $_POST['list_name'], 
		'list_description' => $_POST['list_desc'],
		'campaign_id' => $_POST['campaign_select'],
		'active' => $_POST['status']
	);

    $output = $api->API_addList($postfields);
	
	if ($output->result=="success") {
		$status = 1;
		//$return['msg'] = "New User has been successfully saved.";
	} else {
		//$status = 0;
		// $return['msg'] = "Something went wrong please see input data on form.";
        $status = $output->result;
	}

	echo $status;

?>