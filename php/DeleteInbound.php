<?php
/**
 * @file        DeleteInbound.php
 * @brief       Handles Delete Ingroup, IVR & DID Requests
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

require_once('CRMDefaults.php');

$groupid = NULL;
if (isset($_POST["groupid"])) {
    $groupid = $_POST["groupid"];
}

$ivr = NULL;
if (isset($_POST["ivr"])) {
    $ivr = $_POST["ivr"];
}

$did = NULL;
if (isset($_POST["modify_did"])) {
    $did = $_POST["modify_did"];
}

// IF INGROUP IS DELETED
if ($groupid != NULL) {
/*
 * Deleting In-group
 * [[API:Function]] – goDeleteInbound
 * This application is used to delete a in-group. Only in-group that belongs to authenticated user can be delete.
*/
/*
    $url = gourl."/goInbound/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goDeleteInbound"; #action performed by the [[API:Functions]]. (required)
    $postfields["responsetype"] = responsetype; #json. (required)
    $postfields["group_id"] = $groupid; #Desired User ID. (required)
    $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
	$postfields["log_user"]			= $_POST['log_user'];
	$postfields["log_group"]		= $_POST['log_group'];
*/  
    $postfields = array(
        'goAction' => 'goDeleteIngroup',
        'group_id' => $groupid
    );

    $output = $api->API_Request("goInbound", $postfields);

    if ($output->result=="success") {
        ob_clean();
        print CRM_DEFAULT_SUCCESS_RESPONSE;
    } else {
        echo $output->result;
    }

}

// IF IVR IS DELETED
if ($ivr != NULL) {
/*
 * Deleting Interactive Voice Response
 * [[API:Function]] – goDeleteIVR
 * This application is used to delete a IVR menu. Only IVR menu that belongs to authenticated user can be delete.
*/
/*
    $url = gourl."/goInbound/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goDeleteIVR"; #action performed by the [[API:Functions]]. (required)
    $postfields["responsetype"] = responsetype; #json. (required)
    $postfields["menu_id"] = $ivr; #Desired User ID. (required)
    $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
	$postfields["log_user"]			= $_POST['log_user'];
	$postfields["log_group"]		= $_POST['log_group'];
*/    
    $postfields = array(
        'goAction' => 'goDeleteIVR',
        'menu_id' => $ivr
    );

    $output = $api->API_Request("goInbound", $postfields);

    if ($output->result=="success") {
		ob_clean();
		print CRM_DEFAULT_SUCCESS_RESPONSE;
    } else {
        echo $output->result;
    }

}

// IF PHONENUMBER IS DELETED
if ($did != NULL) {
/*
 * Deleting DID
 * [[API:Function]] – goDeleteDID
 * This application is used to delete a did. Only did that belongs to authenticated user can be delete.
*/
/*
    $url = gourl."/goInbound/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goDeleteDID"; #action performed by the [[API:Functions]]. (required)
    $postfields["responsetype"] = responsetype; #json. (required)
    $postfields["did_id"] = $did; #Desired User ID. (required)
    $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
	$postfields["log_user"]			= $_POST['log_user'];
	$postfields["log_group"]		= $_POST['log_group'];
*/  
    $postfields = array(
        'goAction' => 'goDeleteDID',
        'menu_id' => $did
    );

    $output = $api->API_Request("goInbound", $postfields);

    if ($output->result=="success") {
        ob_clean();
        print CRM_DEFAULT_SUCCESS_RESPONSE;
    } else {
        echo $output->result;
    }

}

?>