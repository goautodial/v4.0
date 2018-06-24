<?php
/**
 * @file        checkCalltime.php
 * @brief       Handles Check Add/Edit Campaign, Disposition & Lead Filter Details Requests
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

	$url = gourl."/goUsers/goAPI.php"; # URL to GoAutoDial API file
	$postfields["goUser"] 	= goUser; #Username goes here. (required)
	$postfields["goPass"] 	= goPass; #Password goes here. (required)
	$postfields["goAction"] 	= "goAddUser"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] 	= responsetype; #json (required)
	$postfields["hostname"] 	= $_SERVER['REMOTE_ADDR']; #Default value
	
	$postfields["user"] 		= $_POST['user_form']; 
	$postfields["pass"] 		= $_POST['password']; 
	$postfields["full_name"] 		= $_POST['fullname']; 
	$postfields["user_group"] 		= $_POST['user_group']; 
	$postfields["active"] 		= $_POST['status']; 
	$postfields["seats"]		= $_POST["seats"];
	$postfields["phone_login"]		= $_POST["phone_logins"];
	$postfields["phone_pass"]		= $_POST["phone_pass"];
	
	$postfields["log_user"]		= $_POST["log_user"];
	$postfields["log_group"]		= $_POST["log_group"];
*/
	$postfields = array(
		'goAction' => 'goAddUser',
		'user' => $_POST['user_form'], 
		'pass' => $_POST['password'], 
		'full_name' => $_POST['fullname'], 
		'user_group' => $_POST['user_group'], 
		'active' => $_POST['status'], 
		'seats' => $_POST["seats"],
		'phone_login' => $_POST["phone_logins"],
		'phone_pass' => $_POST["phone_pass"]
	);

    $output = $api->API_addUser($postfields);
	
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