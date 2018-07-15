<?php
/**
 * @file        AddUser.php
 * @brief       Handles Add User variables
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A, Biscocho 
 * @author      Alexander Jim Abenoja
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
	$api 										= \creamy\APIHandler::getInstance();

	$postfields 								= array(
		'goAction' 									=> 'goAddUser',
		'user' 										=> $_POST['user_form'], 
		'pass' 										=> $_POST['password'], 
		'full_name' 								=> $_POST['fullname'], 
		'user_group' 								=> $_POST['user_group'], 
		'active' 									=> $_POST['status'], 
		'seats' 									=> $_POST["seats"],
		'phone_login' 								=> $_POST["phone_logins"],
		'phone_pass' 								=> $_POST["phone_pass"]
	);

    $output 									= $api->API_addUser($postfields);
	
	if ($output->result=="success") { $status = 1; } 
		else { $status = $output->result; }
	
	echo json_encode($status);

?>
