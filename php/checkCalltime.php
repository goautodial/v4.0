<?php
/**
 * @file        checkCalltime.php
 * @brief       Handles Check Calltime Requests
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
	$url = gourl."/goCalltimes/goAPI.php"; #URL to GoAutoDial API. (required)
	$postfields["goUser"] = goUser; #Username goes here. (required)
	$postfields["goPass"] = goPass; #Password goes here. (required)
	$postfields["goAction"] = "goCheckCalltimes"; #action performed by the [[API:Functions]]. (required)
	$postfields["responsetype"] = responsetype; #json. (required)
*/
	if(isset($_POST['id'])){
		
		$postfields["call_time_id"] = $_POST['id'];

		$postfields = array(
			'goAction' => 'goCheckCalltimes',
			'call_time_id' => $_POST['id']
		);

	    $output = $api->API_checkCalltimes($postfields);

	    echo $output->result;
	}else{
		echo "Error: Missing Parameters!";
	}

?>
