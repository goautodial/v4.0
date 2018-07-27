<?php
/**
 * @file        AddDialStatus.php
 * @brief       Handles Add Dial Status Request
 * @copyright   Copyright (c) 2018 GOautoial Inc.
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
	
	$api 											= \creamy\APIHandler::getInstance();

	if ( isset($_POST['old_dial_status']) ) {
		$statuses 									= explode(" ", $_POST['old_dial_status']);
	}
	
	if ( in_array($_POST['dial_status'], $statuses) ) {
		$new_status 								= $_POST['old_dial_status'];
	} else {
		$new_status 								= " ".$_POST['dial_status']." ".$_POST['old_dial_status'];
	}		

	$postfields 									= array(
		'goAction' 										=> 'goUpdateCampaignDialStatus',
		'campaign_id' 									=> $_POST['campaign_id'],
		'dial_statuses' 								=> $new_status
	);

	$output 										= $api->API_addDialStatus( $postfields );
	
	if ( $output->result=="success" ) { 
		$status 									= 1; 
	} else { 
		$status 									= $output->result; 
	}

	echo json_encode($status);

?>
