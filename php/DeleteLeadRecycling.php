<?php
/**
 * @file        DeleteHotkey.php
 * @brief       Handles Delete Hotkey Request
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Noel Umandap
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

    require_once('goCRMAPISettings.php');
    
    $campaign_id = NULL;
    if(isset($_POST['campaign_id'])){
        $campaign_id = $_POST['campaign_id'];
    }
    $recycleid = NULL;
    if(isset($_POST['recycleid'])){
        $recycleid = $_POST['recycleid'];
    }
/*
    $url = gourl."/goLeadRecycling/goAPI.php"; #URL to GoAutoDial API. (required)
    
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goDeleteLeadRecycling"; #action performed by the [[API:Functions]]. (required)
    $postfields["responsetype"] = responsetype; #json. (required)

    $postfields["campaign_id"] = $campaign_id;
    $postfields["recycle_id"] = $recycleid;
    $postfields["session_user"] = $_POST['session_user'];
*/
    $postfields = array(
        'goAction' => 'goDeleteLeadRecycling',
        'campaign_id' => $campaign_id,
        'recycle_id' => $recycleid
    );

    $output = $api->API_Request("goLeadRecycling", $postfields);
    
    echo $output->result;
?>