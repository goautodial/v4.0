<?php
/**
 * @file        AddDID.php
 * @brief       Handles Add List Request
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
	$url = gourl."/goInbound/goAPI.php"; # URL to GoAutoDial API file
	$postfields["goUser"] 			= goUser; #Username goes here. (required)
	$postfields["goPass"] 			= goPass; #Password goes here. (required)
	$postfields["goAction"] 		= "goAddDID"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] 	= responsetype; #json (required)
	$postfields["hostname"] 		= $_SERVER['REMOTE_ADDR']; #Default value
	$postfields["session_user"]			= $_POST['log_user'];
    
	$postfields["did_pattern"]              = $_POST['did_exten']; #Desired pattern (required)
    $postfields["did_description"]          = $_POST['desc']; #Desired description(required)
    $postfields["did_route"]                = $_POST['route']; #'EXTEN','VOICEMAIL','AGENT','PHONE','IN_GROUP','CALLMENU', or'VMAIL_NO_INST' (required)
    $postfields["user_group"]               = $_POST['user_groups']; #Assign to user group
    $postfields["did_active"]               = $_POST['active']; #Y or N (required)

	if($_POST['route'] == "AGENT"){
	    $postfields["user"]                     = $_POST['route_agentid']; #Desired user (required if did_route is AGENT)
	    $postfields["user_unavailable_action"]  = $_POST['route_unavail']; #Desired user unavailable action (required if did_route is AGENT)
	}

	if($_POST['route'] == "IN_GROUP"){
	    $postfields["group_id"]                 = $_POST['route_ingroupid']; #Desired group ID (required if did_route is IN-GROUP)
	}

	if($_POST['route'] == "PHONE"){
	    $postfields["phone"]                    = $_POST['route_phone_exten']; #Desired phone (required if did_route is PHONE)
	    $postfields["server_ip"]                = $_POST['route_phone_server']; #Desired server ip (required if did_route is PHONE)
	}

	if($_POST['route'] == "CALLMENU"){
	    $postfields["menu_id"]                  = $_POST['route_ivr']; #Desired menu id (required if did_route is IVR)
	}

	if($_POST['route'] == "VOICEMAIL"){
	    $postfields["voicemail_ext"]            = $_POST['route_voicemail']; #Desired voicemail (required if did_route is VOICEMAIL)
	}

	if($_POST['route'] == "EXTEN"){
	    $postfields["extension"]                = $_POST['route_exten']; #Desired extension (required if did_route is CUSTOM EXTENSION)
	    $postfields["exten_context"]            = $_POST['route_exten_context']; #Deisred context (required if did_route is CUSTOM EXTENSION)
	}
*/

	$postfields = array(
		'goAction' => 'goAddDID',
		'did_pattern' => $_POST['did_exten'], //Desired pattern (required)
	    'did_description' => $_POST['desc'], //Desired description(required)
	    //'did_route' => $_POST['route'], //'EXTEN','VOICEMAIL','AGENT','PHONE','IN_GROUP','CALLMENU', or'VMAIL_NO_INST' (required)
	    'user_group' => $_POST['user_groups'], //Assign to user group
	    'did_active' => $_POST['active'], //Y or N (required)
	    /*'user' => $_POST['route_agentid'], //Desired user (required if did_route is AGENT)
	    'user_unavailable_action' => $_POST['route_unavail'], //Desired user unavailable action (required if did_route is AGENT)
	    'group_id' => $_POST['route_ingroupid'], //Desired group ID (required if did_route is IN-GROUP)
	    'phone' => $_POST['route_phone_exten'], //Desired phone (required if did_route is PHONE)
	    'server_ip' => $_POST['route_phone_server'], //Desired server ip (required if did_route is PHONE)
	    'menu_id' => $_POST['route_ivr'], //Desired menu id (required if did_route is IVR)
	    'voicemail_ext' => $_POST['route_voicemail'], //Desired voicemail (required if did_route is VOICEMAIL)
	    'extension' => $_POST['route_exten'], //Desired extension (required if did_route is CUSTOM EXTENSION)
	    'exten_context' => $_POST['route_exten_context'], //Deisred context (required if did_route is CUSTOM EXTENSION)*/
	);

    $output = $api->API_addDID($postfields);

	if($output->result=="success"){
		$status = 1;
		//$return['msg'] = "New User has been successfully saved.";
	}else {
		//$status = 0;
		// $return['msg'] = "Something went wrong please see input data on form.";
        $status = $output->result;
	}

echo $status;
?>
