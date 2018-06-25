<?php
/**
 * @file        DeleteDisposition.php
 * @brief       Handles Delete Disposition Requests
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
require_once('CRMDefaults.php');
require_once('goCRMAPISettings.php');
*/
// check required fields
$validated = 1;
if (!isset($_POST["disposition_id"])) {
	$validated = 0;
}

if ($validated == 1) {
	$disposition_id = $_POST["disposition_id"];

	if(isset($_POST['status'])){
        $status = $_POST["status"];
        /*
        $url = gourl."/goDispositions/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = goUser; #Username goes here. (required)
        $postfields["goPass"] = goPass; #Password goes here. (required)
        $postfields["goAction"] = "goDeleteDisposition"; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = responsetype; #json. (required)
        $postfields["campaign_id"] = $disposition_id; #Desired User ID. (required)
        $postfields["statuses"] = $status;
        $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
		$postfields["log_user"] = $_POST['log_user'];
		$postfields["log_group"] = $_POST['log_group'];
        */
        $postfields = array(
            'goAction' => 'goDeleteDisposition',
            'campaign_id' => $disposition_id,
            'statuses' => $status
        );

        $output = $api->API_Request("goDispositions", $postfields);

        if ($output->result=="success") {
            echo 1;
        }else{
            echo $output->result;
        }
    }else{
        /*
        $url = gourl."/goDispositions/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = goUser; #Username goes here. (required)
        $postfields["goPass"] = goPass; #Password goes here. (required)
        $postfields["goAction"] = "goDeleteDisposition"; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = responsetype; #json. (required)
        $postfields["campaign_id"] = $disposition_id; #Desired User ID. (required)
        $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
        */

        $postfields = array(
            'goAction' => 'goDeleteDisposition',
            'campaign_id' => $disposition_id
        );

        $output = $api->API_Request("goDispositions", $postfields);

        if ($output->result=="success") {
    		echo 1;
        }else{
            echo $output->result;
        }
    }
}


?>
