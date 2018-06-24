<?php
/**
 * @file        AddPhone.php
 * @brief       Handles Add Phone Request
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

	$url 	= gourl."/goPhones/goAPI.php"; # URL to GoAutoDial API file
	$postfields["goUser"] 	= goUser; #Username goes here. (required)
	$postfields["goPass"] 	= goPass; #Password goes here. (required)
	$postfields["goAction"] 	= "goAddPhones"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] 	= responsetype; #json. (required)
	
	if($_POST['add_phones'] !== "CUSTOM")
	$postfields["seats"]	= $_POST['add_phones'];
	else
	$postfields["seats"]	= $_POST['custom_seats'];
	
	$postfields["extension"] 	= $_POST['phone_ext']; #Deisred extension (required)
	$postfields["server_ip"] 	= $_POST['ip']; #Desired server_ip (required)
	$postfields["hostname"] 	= $_SERVER['REMOTE_ADDR']; #Default value
	$postfields["pass"] 	= $_POST['phone_pass']; #Desired password (required)
	$postfields["protocol"] 	= $_POST['protocol']; #SIP, Zap, IAX2, or EXTERNAL. (required)
	$postfields["dialplan_number"]	= "9999".$_POST['phone_ext']; #Desired dialplan number (required)
	$postfields["voicemail_id"] 	= $_POST['phone_ext']; #Desired voicemail (required)
	$postfields["status"] 	= "ACTIVE"; #ACTIVE, SUSPENDED, CLOSED, PENDING, or ADMIN (required)
	$postfields["active"] 	= "Y"; #Y or N (required)
	$postfields["fullname"] 	= $_POST['pfullname']; #Desired full name (required)
	$postfields["gmt"] 	= $_POST['gmt']; #Deisred extension (required)
	$postfields["messages"] 	= "0"; #Desire message (required)
	$postfields["old_messages"] 	= "0"; #Desired old message (required)
	$postfields["user_group"] 	= $_POST['user_group']; #Assign to user group (required)
	$postfields["session_user"]	= $_POST['log_user'];
*/
	if($_POST['add_phones'] !== "CUSTOM")
	$seats	= $_POST['add_phones'];
	else
	$seats	= $_POST['custom_seats'];

	$postfields = array(
		'goAction' => 'goAddPhones',
		'seats' => $seats,
		'extension' => $_POST['phone_ext'],
		'server_ip' => $_POST['ip'],
		'pass' => $_POST['phone_pass'],
		'protocol' => $_POST['protocol'], #SIP, Zap, IAX2, or EXTERNAL. (required)
		'dialplan_number' => "9999".$_POST['phone_ext'],
		'voicemail_id' => $_POST['phone_ext'], #Desired voicemail (required)
		'status' => "ACTIVE",
		'active' => "Y",
		'fullname' => $_POST['pfullname'],
		'gmt' => $_POST['gmt'],
		'messages' => "0",
		'old_messages' => "0",
		'user_group' => $_POST['user_group']
	);
	
	$output = $api->API_addPhones($postfields);
	
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