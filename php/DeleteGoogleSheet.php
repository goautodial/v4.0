<?php
/**
 * @file        DeleteGoogleSheet.php
 * @brief       Handles Delete to Google Sheet Request
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Chris Lumontad
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
    require_once('goCRMAPISettings.php');

	$url = gourl."/goCampaigns/goAPI.php"; # URL to GoAutoDial API file
	$postfields["goUser"] 						= goUser; #Username goes here. (required)
	$postfields["goPass"] 						= goPass; #Password goes here. (required)
	$postfields["goAction"] 					= "goUpdateCampaignGoogleSheet"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] 			= responsetype; #json (required)
	$postfields["hostname"] 					= $_SERVER['REMOTE_ADDR']; #Default value
	$postfields["log_user"]						= $_POST['log_user'];
	$postfields["log_group"]					= $_POST['log_group'];

	$postfields['campaign_id']  			= $_POST['campaign_id'];
*/	
	$old_google_sheet_ids = explode(" ",$_POST['google_sheet_ids']);
	$oldSheets = array();
	foreach($old_google_sheet_ids as $old){
		if(!empty($old) && $old != $_POST['selected_sheet_id']){
			array_push($oldSheets, $old);
		}
	}
	
	$new_sheet_ids = ' ';
	foreach($oldSheets as $OLD){
		$new_sheet_ids .= $OLD.' ';
	}
	$new_sheet_ids = trim($new_sheet_ids, " ");
	
	//$postfields['google_sheet_ids'] = $new_sheet_ids;

 	$postfields = array(
         	'goAction' => 'goUpdateCampaignGoogleSheet',
		'campaign_id' => $_POST['campaign_id'],
         	'google_sheet_ids' => trim($new_sheet_ids)
     	);

    $output = $api->API_updateCampaignGoogleSheet($postfields);

	if ($output->result=="success") {
		echo json_encode(1);
	} else {
		echo json_encode(0);
	}

?>
