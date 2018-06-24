<?php
/**
 * @file        AddMOH.php
 * @brief       Handles Add MOH Requests
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
	$url = gourl."/goMusicOnHold/goAPI.php"; # URL to GoAutoDial API file
	$postfields["goUser"] 			= goUser; #Username goes here. (required)
	$postfields["goPass"] 			= goPass; #Password goes here. (required)
	$postfields["goAction"] 		= "goAddMOH"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] 	= responsetype; #json (required)
	$postfields["hostname"] 		= $_SERVER['REMOTE_ADDR']; #Default value
	
	$postfields["moh_id"] 			= $_POST['moh_id']; 
	$postfields["moh_name"] 		= $_POST['moh_name']; 
	$postfields["user_group"] 		= $_POST['user_group'];
	$postfields["active"] 			= $_POST['active']; 
	$postfields["random"] 			= $_POST['random'];
	
	$postfields["log_user"]			= $_POST['log_user'];
	$postfields["log_group"]		= $_POST['log_group'];
*/

	$postfields = array(
		'goAction' => 'goAddMOH',
		'moh_id' => $_POST['moh_id'], 'moh_name' => $_POST['moh_name'],
		'user_group' => $_POST['user_group'],
		'active' => $_POST['active'], 
		'random' => $_POST['random']
	);

	$output = $api->API_addMOH($postfields);
	
	if ($output->result=="success") {
		$status = 1;
	} else {
		$status = $output->result;
	}

	echo $status;

?>