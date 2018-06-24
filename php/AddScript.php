<?php
/**
 * @file        AddScript.php
 * @brief       Handles Add Script Request
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
require_once('goCRMAPISettings.php');	

	$url = gourl."/goScripts/goAPI.php"; # URL to GoAutoDial API file
	$postfields["goUser"] 			= goUser; #Username goes here. (required)
	$postfields["goPass"] 			= goPass; #Password goes here. (required)
	$postfields["goAction"] 		= "goAddScript"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] 	= responsetype; #json (required)
	
	$postfields["script_id"] 			= $_POST['script_id']; 
	$postfields["script_name"] 			= $_POST['script_name']; 
	$postfields["script_comments"] 		= $_POST['script_comments'];
	// $postfields["script_text"] 			= $_POST['script_text']; 
	$postfields["script_text"] 			= $_POST['script_text_value'];
	$postfields["active"] 				= $_POST['active'];
	$postfields["user"]					= $_POST['script_user'];
	$postfields["user_group"]			= $_POST['script_user_group'];
	
	$postfields["hostname"] 			= $_SERVER['REMOTE_ADDR']; 
	$postfields["log_user"] 			= $_POST['log_user'];
	$postfields["log_group"]			= $_POST['log_group'];
*/
	$postfields = array(
		'goAction' => 'goAddScript',
		'script_id' => $_POST['script_id'], 
		'script_name' => $_POST['script_name'], 
		'script_comments' => $_POST['script_comments'],
		'script_text' => $_POST['script_text_value'],
		'active' => $_POST['active'],
		'user' => $_POST['script_user'],
		'user_group' => $_POST['script_user_group']
	);
	
	$output = $api->API_addScript($postfields);

	if ($output->result == "success") {
		$status = "success";
	} else {
        $status = $output->result;
	}

	echo $status;
?>