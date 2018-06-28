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
	
	/*if ($output->result=="success") {
		$status = 1;
		//$return['msg'] = "New User has been successfully saved.";
	} else {
		//$status = 0;
		// $return['msg'] = "Something went wrong please see input data on form.";
        $status = $output->result;
	}*/
	echo $output;

?>
