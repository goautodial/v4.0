<?php
/**
 * @file        LoginRocketChat.php
 * @brief       Logs in user to rocketchat
 * @copyright   Copyright (c) 2018 GOautodial Inc.
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

include('./CRMDefaults.php');
require_once('./Session.php');

$user = $_POST['user'];
$pass = $_POST['pass'];
//$user = "devadmin";
//$pass = "hayopka2021";
//Logs In Rocketchat User
	$curl = curl_init();
       	curl_setopt_array($curl, array(
        CURLOPT_URL => ROCKETCHAT_URL."/api/v1/login",
        CURLOPT_RETURNTRANSFER => true,
       	CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
       	CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
       	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
       	CURLOPT_POSTFIELDS =>"{\r\n  \"user\": \"$user\",\r\n  \"password\": \"$pass\"}",
        CURLOPT_HTTPHEADER => array(
       	        "Content-Type:application/json"
       	    )
        ));
       	$response = curl_exec($curl);
        curl_close($curl);
	$output = json_decode($response, TRUE);
	//$_SESSION['gad_authToken'] = $response->data['authToken'];
	if(!isset($_SESSION['gad_userID'])){
	$_SESSION["gad_userID"] = $output["data"]["userId"];
	$_SESSION["gad_authToken"] = $output["data"]["authToken"];
	}
	echo $data = $response;	
	//var_dump($response);
?>
