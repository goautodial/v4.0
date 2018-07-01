<?php
/**
 * @file        EmergencyLogout.php
 * @brief       Handles Emergency Logout Request
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      Demian Lizandro A, Biscocho
 * @author      Alexander Jim H. Abenoja
 * @author      Jerico James F. Milo
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
require_once('CRMDefaults.php');

$api = \creamy\APIHandler::getInstance();

/*
require_once('goCRMAPISettings.php');

	$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
	$postfields["goUser"] = goUser; #Username goes here. (required)
	$postfields["goPass"] = goPass;
	$postfields["goAction"] = "goEmergencyLogout"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] = responsetype;
	$postfields["goUserAgent"] = $_POST['goUserAgent'];
	$postfields["log_user"] = $_POST['log_user'];
	$postfields["log_group"] = $_POST['log_group'];
	$postfields["log_ip"] = $_SERVER['REMOTE_ADDR'];
*/
	$postfields = array(
        'goAction' => 'goEmergencyLogout',
        'goUserAgent' => $_POST['goUserAgent']
    );

    $output = $api->API_Request("goDashboard", $postfields);

    if ($output->result=="success") {
    	ob_clean();
        print CRM_DEFAULT_SUCCESS_RESPONSE;
    }else{
        echo $output->result;
    }
	
?>
