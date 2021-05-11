<?php
/**
 * @file        LogoutRocketChat.php
 * @brief       Logs out user to rocketchat
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

$userID = $_POST['userID'];
$authToken = $_POST['authToken'];

//Logs In Rocketchat User
	$curl = curl_init();
       	curl_setopt_array($curl, array(
        CURLOPT_URL => ROCKETCHAT_URL."/api/v1/logout",
        //CURLOPT_URL => ROCKETCHAT_URL."/api/v1/method.callAnon/logoutCleanUp",
	CURLOPT_RETURNTRANSFER => true,
       	CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
       	CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
       	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
       	CURLOPT_POSTFIELDS =>"{\r\n  \"X-User-Id\": \"$userID\",\r\n  \"X-Auth-Token\": \"$authToken\"}",
        CURLOPT_HTTPHEADER => array(
       	        "Content-Type:application/json", "X-User-Id:$userID",  "X-Auth-Token:$authToken"
       	    )
        ));
       	$response = curl_exec($curl);
        curl_close($curl);
	if (isset($_COOKIE['rc_uid']))
	    unset($_COOKIE['rc_uid']);
	if (isset($_COOKIE['rc_token']))
            unset($_COOKIE['rc_token']);
	echo $data = $response;
	//echo json_encode($data);
?>
