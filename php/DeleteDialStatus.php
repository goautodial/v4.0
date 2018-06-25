<?php
/**
 * @file        DeleteDialStatus.php
 * @brief       Handles Delete Dial Status Requests
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

	$url = gourl."/goCampaigns/goAPI.php"; # URL to GoAutoDial API file
	$postfields["goUser"] 						= goUser; #Username goes here. (required)
	$postfields["goPass"] 						= goPass; #Password goes here. (required)
	$postfields["goAction"] 					= "goUpdateCampaignDialStatus"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] 			= responsetype; #json (required)
	$postfields["hostname"] 					= $_SERVER['REMOTE_ADDR']; #Default value
	$postfields["log_user"]						= $_POST['log_user'];
	$postfields["log_group"]					= $_POST['log_group'];

	$postfields['campaign_id']  			= $_POST['campaign_id'];
*/	
	$old_statuses = explode(" ",$_POST['dial_status']);
	$oldStats = array();
	foreach($old_statuses as $old){
		if(!empty($old) && $old != $_POST['selected_status']){
			array_push($oldStats, $old);
		}
	}
	
	$new_status = ' ';
	foreach($oldStats as $OLD){
		$new_status .= $OLD.' ';
	}
	$new_status = rtrim($new_status, " ");
	
	//$postfields['dial_status'] = $new_status;

	$postfields = array(
        'goAction' => 'goUpdateCampaignDialStatus',
        'campaign_id' => $_POST['campaign_id'],
        'dial_status' => $new_status
    );

	$output = $api->API_Request("goCampaigns", $postfields);

	if ($output->result=="success") {
		echo json_encode(1);
	} else {
		echo json_encode(0);
	}

?>
