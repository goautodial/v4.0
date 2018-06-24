<?php
/**
 * @file        AddIngroup.php
 * @brief       Handles Add Ingroup Request
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

$color = $_POST["color"];
$color = str_replace("#", '', $color);
/*
echo 'groupid:'.$_POST['groupid']; echo "<br/>";
echo 'groupname:'.$_POST['groupname']; echo "<br/>";
echo 'color:'.$color;  echo "<br/>";
echo 'active:'.$_POST['active'];  echo "<br/>";
echo 'webform:'.$_POST['web_form']; echo "<br/>";
echo 'usergroup:'.$_POST['user_group'];  echo "<br/>";
echo 'ingroupvoicemail:'.$_POST['ingroup_voicemail'];  echo "<br/>";
echo 'nextagentcall:'.$_POST['next_agent_call']; echo "<br/>";
echo 'display:'.$_POST['display']; echo "<br/>";
echo 'script:'.$_POST['script'];  echo "<br/>";
echo 'calllaunch:'.$_POST['call_launch']; echo "<br/>";
*/

if($validate == 0){
	/*
	$url = gourl."/goInbound/goAPI.php"; # URL to GoAutoDial API file
	$postfields["goUser"] 			= goUser; #Username goes here. (required)
	$postfields["goPass"] 			= goPass; #Password goes here. (required)
	$postfields["goAction"] 		= "goAddIngroup"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] 	= responsetype; #json (required)
	$postfields["hostname"] 		= $_SERVER['REMOTE_ADDR']; #Default value
	$postfields["log_user"]			= $_POST['log_user'];
	$postfields["log_group"]		= $_POST['log_group'];

    
    $postfields["group_id"]         = $_POST['groupid']; #Desired group ID (required)
    $postfields["group_name"]       = $_POST['groupname']; #Desired name (required)
    $postfields["group_color"]      = $color; #Desired color (required)
    $postfields["active"]           = $_POST['active']; #Y or N (required)
    $postfields["web_form_address"] = $_POST['web_form']; #Desired web form address (required)
    $postfields["user_group"]       = $_POST['user_group']; #Assign user group (required)
    
    $postfields["voicemail_ext"]    = $_POST['ingroup_voicemail']; #Desired voicemail (required)
    $postfields["next_agent_call"]  = $_POST['next_agent_call']; #'fewest_calls_campaign','longest_wait_time','ring_all','random','oldest_call_start','oldest_call_finish','overall_user_level','inbound_group_rank','campaign_rank', or 'fewest_calls' (required)
    $postfields["fronter_display"]  = $_POST['display']; #Y or N (required)
    $postfields["ingroup_script"]   = $_POST['script']; #Desired script (required)
    $postfields["get_call_launch"]  = $_POST['call_launch']; #Desired call launch (required)
    */

    $postfields = array(
		'goAction' => 'goAddIngroup',
		'group_id' => $_POST['groupid'],
	    'group_name' => $_POST['groupname'],
	    'group_color' => $color,
	    'active' => $_POST['active'],
	    'web_form_address' => $_POST['web_form'],
	    'user_group' => $_POST['user_group'],
	    'voicemail_ext' => $_POST['ingroup_voicemail'],
	    'next_agent_call' => $_POST['next_agent_call'], #'fewest_calls_campaign','longest_wait_time','ring_all','random','oldest_call_start','oldest_call_finish','overall_user_level','inbound_group_rank','campaign_rank', or 'fewest_calls' (required)
	    'fronter_display' => $_POST['display'], #Y or N (required)
	    'ingroup_script' => $_POST['script'],
	    'get_call_launch' => $_POST['call_launch']
	);

    $output = $api->API_addIngroup($postfields);

	if ($output->result=="success") {
		$status = "success";
		//$return['msg'] = "New User has been successfully saved.";
	} else {
		//$status = 0;
		// $return['msg'] = "Something went wrong please see input data on form.";
        $status = $output->result;
	}

	echo $status;
}

?>