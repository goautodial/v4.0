<?php
/**
 * @file        checkUser.php
 * @brief       Validate user entries
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      Alexander Jim H. Abenoja  <alex@goautodial.com>
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
	$url = gourl."/goUsers/goAPI.php"; #URL to GoAutoDial API. (required)
	$postfields["goUser"] = goUser; #Username goes here. (required)
	$postfields["goPass"] = goPass; #Password goes here. (required)
	$postfields["goAction"] = "goCheckUser"; #action performed by the [[API:Functions]]. (required)
	$postfields["responsetype"] = responsetype; #json. (required)
*/
	$postfields["goAction"] = "goCheckUser";

	if(isset($_POST['user'])){
		$postfields["user"] = $_POST['user'];
	}
	

	if(isset($_POST['phone_login'])){
		$postfields["phone_login"] = $_POST['phone_login']; 
	}
	
	$output = $api->API_checkUser($postfields);

	if($output->result == "success"){
		echo $output->result;
	}else{
		if($output->user != NULL){
			echo $output->result;
		}
		if($output->result == "fail"){
			echo $output->phone_login;
		}
	}

?>
