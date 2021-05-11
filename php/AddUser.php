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
	require_once('Session.php');
	$api 										= \creamy\APIHandler::getInstance();

	$email = $_POST["email"];
	$full_name = $_POST["fullname"];
	$password = $_POST["password"];
	$username = $_POST["user_form"];
	$usergroup = $_POST["user_group"];
	$postfields = array(
		'goAction' 	=> 'goAddUser',
		'user' 		=> $username, 
		'pass' 		=> $password, 
		'full_name' 	=> $full_name, 
		'user_group' 	=> $usergroup,
		'email' 	=> $email, 
		'active' 	=> $_POST['status'], 
		'seats' 	=> $_POST["seats"],
		'phone_login' 	=> $_POST["phone_logins"],
		'phone_pass' 	=> $_POST["phone_pass"],
		'server_ip' 	=> $_POST["ip"]
	);

    	$output = $api->API_addUser($postfields);
	
	if ($output->result=="success") { 
		//insert curl rocketchat insert; POST: email, name, password, username; HEADER: xauth xtoken
		$authToken = $_SESSION['gad_authToken'];//"Azve2taXDIxZiIkFYvs-yWIBfLd3lLGOkezRFKPGxt3";
		$userID = $_SESSION['gad_userID'];//"4yM7o5Feayn9uWj7j";
		if($usergroup === "ADMIN"){
		$roles = "admin\", \"livechat-agent\", \"livechat-manager\", \"bot";
		}else{
		$roles = "livechat-agent";
		}		
		//Logs In Rocketchat User
	        $curl = curl_init();
	        curl_setopt_array($curl, array(
	        CURLOPT_URL => ROCKETCHAT_URL."/api/v1/users.create",
	        CURLOPT_RETURNTRANSFER => true,
	        CURLOPT_ENCODING => "",
	        CURLOPT_MAXREDIRS => 10,
	        CURLOPT_TIMEOUT => 0,
	        CURLOPT_FOLLOWLOCATION => true,
	        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	        CURLOPT_CUSTOMREQUEST => "POST",
	        CURLOPT_POSTFIELDS =>"{\r\n  \"email\": \"$email\",\r\n  \"name\": \"$full_name\",\r\n  \"password\": \"$password\",\r\n  \"username\": \"$username\",\r\n  \"roles\": [\"$roles\"]}",
	        CURLOPT_HTTPHEADER => array(
        	        "Content-Type:application/json", "X-Auth-Token:$authToken", "X-User-Id:$userID"
	            )
        	));
	        $response = curl_exec($curl);
        	curl_close($curl);
		//echo $output = $response;
        	$status = 1;
		//echo json_encode($data);	
		/*if($data === true)
		$status = 1; 
		else
		$status = $output;*/
	} 
	else { $status = $output->result; }
	
	echo json_encode($status);

?>
