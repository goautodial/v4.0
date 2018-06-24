<?php
/**
 * @file        AddHotkey.php
 * @brief       Handles Add Hotkey Request
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Noel Umandap
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
    $url = gourl."/goHotkeys/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goAddHotkey"; #action performed by the [[API:Functions]]. (required)
    $postfields["responsetype"] = responsetype; #json. (required)
    $postfields["campaign_id"] = $_POST['campaign_id'];
		$postfields["hotkey"] = $_POST['hotkey'];
		$postfields["status"] = $_POST['status'];
		$postfields["status_name"] = $_POST['status_name'];
	
	$postfields["log_user"] = $_POST['log_user'];
	$postfields["log_group"] = $_POST['log_group'];
	$postfields["log_ip"] = $_SERVER['REMOTE_ADDR'];
*/

	$postfields = array(
		'goAction' => 'goAddHotkey',
		'campaign_id' => $_POST['campaign_id'],
		'hotkey' => $_POST['hotkey'],
		'status' => $_POST['status'],
		'status_name' => $_POST['status_name']	
	);

	$output = $api->API_addHotkey($postfields);

	if ($output->result=="success") {
		$status = "success";
    }elseif($output->result=="duplicate"){
        $status = "duplicate";
    }else {
		$status = "error";
	}

	echo $status;
?>
