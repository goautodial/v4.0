<?php
/**
 * @file        AddDisposition.php
 * @brief       Handles Add Disposition Request
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
	$api 								= \creamy\APIHandler::getInstance();

	if(!isset($_POST['selectable'])){
		$_POST['selectable'] 			= "N";
	}

	if(!isset($_POST['human_answered'])){
		$_POST['human_answered'] 		= "N";
	}

	if(!isset($_POST['sale'])){
		$_POST['sale'] 					= "N";
	}

	if(!isset($_POST['dnc'])){
		$_POST['dnc'] 					= "N";
	}

	if(!isset($_POST['scheduled_callback'])){
		$_POST['scheduled_callback'] 	= "N";
	}

	if(!isset($_POST['customer_contact'])){
		$_POST['customer_contact'] 		= "N";
	}

	if(!isset($_POST['not_interested'])){
		$_POST['not_interested'] 		= "N";
	}

	if(!isset($_POST['unworkable'])){
		$_POST['unworkable'] 			= "N";
	}

	$postfields 						= array(
		'goAction' 							=> 'goAddDisposition',
		'userid' 							=> $_POST['userid'],
		'campaign_id' 						=> $_POST['campaign'],
		'status' 							=> $_POST['status'],
		'status_name' 						=> $_POST['status_name'],
		'selectable' 						=> $_POST['selectable'],
		'human_answered' 					=> $_POST['human_answered'],
		'sale' 								=> $_POST['sale'],
		'dnc' 								=> $_POST['dnc'],
		'scheduled_callback' 				=> $_POST['scheduled_callback'],
		'customer_contact'					=> $_POST['customer_contact'],
		'not_interested' 					=> $_POST['not_interested'],
		'unworkable' 						=> $_POST['unworkable']
	);

	$output 							= $api->API_addDisposition($postfields);
	
	if ($output->result=="success") { 
		$status 						= 1; 
	} else { 
		$status 						= $output->result; 
	}
	
	echo json_encode($status);

?>
