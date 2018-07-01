<?php
/**
 * @file        DeleteVoicemail.php
 * @brief       Handles Delete Voicemail Requests
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

// check required fields
$validated = 1;
if (!isset($_POST["voicemail_id"])) {
	$validated = 0;
}

if ($validated == 1) {
/*
    $url = gourl."/goVoicemails/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goDeleteVoicemail"; #action performed by the [[API:Functions]]. (required)
    $postfields["responsetype"] = responsetype; #json. (required)
    $postfields["voicemail_id"] = $voicemail_id; #Desired User ID. (required)
    $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
	
	$postfields["log_user"] = $_POST['log_user'];
	$postfields["log_group"] = $_POST['log_group'];
*/
    $postfields = array(
        'goAction' => 'goDeleteVoicemail',
        'voicemail_id' => $_POST['voicemail_id']
    );

    $output = $api->API_Request("goVoicemails", $postfields);

    if ($output->result=="success") {
    	ob_clean();
        print CRM_DEFAULT_SUCCESS_RESPONSE;
    }else{
        echo $output->result;
    }

}
?>