<?php
/**
 * @file        DeleteCampaign.php
 * @brief       Handles Delete Campaign Requests
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
    require_once('goCRMAPISettings.php');
    require_once('Session.php');
	$campaign_id = $_POST['campaign_id'];
	$action = $_POST["action"];
	if($action == "delete_selected"){
		$campaign_id = implode(",",$campaign_id);
	}
	
	$url = gourl."/goCampaigns/goAPI.php"; #URL to GoAutoDial API. (required)

	$postfields["goUser"] 		= goUser; #Username goes here. (required)
	$postfields["goPass"] 		= goPass; #Password goes here. (required)
	$postfields["goAction"] 		= "goDeleteCampaign"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] 	    = responsetype; #json (required)
	$postfields["hostname"] 		= $_SERVER['REMOTE_ADDR']; #Default value
	$postfields["campaign_id"] 		= $campaign_id;; #Desired campaign id. (required)
	$postfields["action"] 		= $action;
	$postfields["log_user"]		= $_POST['log_user'];
	$postfields["log_group"]		= $_POST['log_group'];
	$postfields["session_user"] 				= $_SESSION['user']; #current user
*/
	$postfields = array(
        'goAction' => 'goDeleteCampaign',
        'campaign_id' => $_POST['campaign_id']
    );

    $output = $api->API_Request("goCampaigns", $postfields);

	if ($output->result=="success") {
		$status = 1;
	} else {
		$status = $output->result;
	}

	echo $status;
?>