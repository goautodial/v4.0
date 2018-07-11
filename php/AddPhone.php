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

	echo json_encode($status);

?>
