<?php
/**
 * @file        AddLeadFilter.php
 * @brief       Handles Add Lead Filter Request
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

$validate = 0;

if(isset($_POST['lf_id'])){
	$lf_id = $_POST['lf_id'];
}else{
	$validate = 1;
}
if(isset($_POST['lf_name'])){
	$lf_name = $_POST['lf_name'];
}else{
	$validate = 1;
}
if(isset($_POST['lf_comments'])){
	$lf_comments = $_POST['lf_comments'];
}else{
	$validate = 1;
}
if(isset($_POST['lf_sql'])){
	$lf_sql = $_POST['lf_sql'];
}else{
	$validate = 1;
}
if(isset($_POST['user_group'])){
	$user_group = $_POST['user_group'];
}else{
	$validate = 1;
}

if($validate == 0){
    /*
    $url = gourl."/goLeadFilters/goAPI.php"; # URL to GoAutoDial API file
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goAddLeadFilter"; #action performed by the [[API:Functions]]
    $postfields["responsetype"] = responsetype; #json (required)
    $postfields["lead_filter_id"] = $lf_id; #lead filter ID. (required)
    $postfields["lead_filter_name"] = $lf_name; #lead filter name. (required)
    $postfields["lead_filter_comments"] = $lf_comments; #lead filter comments. (optional)
    $postfields["lead_filter_sql"] = $lf_sql; #lead filter SQL. (required)
    $postfields["user_group"] = $user_group; #user group. (required)

    $postfields["log_user"] = $_POST['log_user'];
    $postfields["log_group"] = $_POST['log_group'];
    $postfields["log_ip"] = $_SERVER['REMOTE_ADDR'];
    */

    $postfields = array(
        'goAction' => 'goAddLeadFilter',
        'lead_filter_id' => $lf_id,
        'lead_filter_name' => $lf_name,
        'lead_filter_comments' => $lf_comments,
        'lead_filter_sql' => $lf_sql,
        'user_group' => $user_group
    );

    $output = $api->API_addLeadFilter($postfields);

    if ($output->result=="success") {
        $status = "Added New Filter ID: ".$_REQUEST['lead_filter_id'];
     } else {
        $status = $output->result;
    }

	echo $status;
	
}else{
	echo "incomplete";
}
?>