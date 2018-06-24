<?php
/**
 * @file        AddDialStatus.php
 * @brief       Handles Add Dial Status Request
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
	$url = gourl."/goCampaigns/goAPI.php"; # URL to GoAutoDial API file
	$postfields["goUser"] 						= goUser; #Username goes here. (required)
	$postfields["goPass"] 						= goPass; #Password goes here. (required)
	$postfields["goAction"] 					= "goUpdateCampaignDialStatus"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] 			= responsetype; #json (required)
	$postfields["hostname"] 					= $_SERVER['REMOTE_ADDR']; #Default value

	$postfields['campaign_id']  			= $_POST['campaign_id'];

	$statuses = explode(" ", $_POST['old_dial_status']);
  
  if(in_array($_POST['dial_status'], $statuses)){
    $new_status = $_POST['old_dial_status'];
  }else{
    $new_status = " ".$_POST['dial_status']." ".$_POST['old_dial_status'];
  }

  $postfields['dial_status']  			= $new_status;
*/
if(isset($_POST['old_dial_status']))
  $statuses = explode(" ", $_POST['old_dial_status']);
  
  if(in_array($_POST['dial_status'], $statuses)){
    $new_status = $_POST['old_dial_status'];
  }else{
    $new_status = " ".$_POST['dial_status']." ".$_POST['old_dial_status'];
  }

  $postfields = array(
		'goAction' => 'goUpdateCampaignDialStatus'
		'campaign_id' => $_POST['campaign_id'],
		'dial_status' => $new_status
	);

  $output = $api->API_addDialStatus($postfields);
  
  if ($output->result=="success") {
  	echo json_encode(1);
  } else {
  	echo json_encode(0);
  }

?>
