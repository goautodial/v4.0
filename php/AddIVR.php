<?php

	/** Telephony Callmenu API - Add a new Telephony Callmenu */
	/**
	 * Generates action circle buttons for different pages/module
	 */
require_once('goCRMAPISettings.php');
	 
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

	$url = gourl."/goInbound/goAPI.php"; # URL to GoAutoDial API file
	$postfields["goUser"] 			= goUser; #Username goes here. (required)
	$postfields["goPass"] 			= goPass; #Password goes here. (required)
	$postfields["goAction"] 		= "goAddIVRmenu"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] 	= responsetype; #json (required)
	$postfields["hostname"] 		= $_SERVER['REMOTE_ADDR']; #Default value

    $postfields['menu_id'] = $_POST['menu_id'];
	$postfields['menu_name'] = $_POST['menu_name'];
	$postfields['user_group'] = $_POST['user_groups'];
	$postfields['menu_prompt'] = $_POST['menu_prompt'];
	$postfields['menu_timeout'] = $_POST['menu_timeout'];
	$postfields['menu_timeout_prompt'] = $_POST['menu_timeout_prompt'];
	$postfields['menu_invalid_prompt'] = $_POST['menu_invalid_prompt'];
	$postfields['menu_repeat'] = $_POST['menu_repeat'];
	$postfields['postfields'] = $_POST['menu_time_check'];
	$postfields['call_time_id'] = $_POST['call_time_id'];
	$postfields['track_in_vdac'] = $_POST['track_in_vdac'];
	$postfields['custom_dialplan_entry'] = $_POST['custom_dialplan_entry'];
	$postfields['tracking_group'] = $_POST['tracking_group'];
    
    
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	// curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	$data = curl_exec($ch);
	curl_close($ch);

	$output = json_decode($data);
	
	if ($output->result=="success") {
		# Result was OK!
		$status = "success";
		//$return['msg'] = "New User has been successfully saved.";
	} else {
		# An error occured
		//$status = 0;
		// $return['msg'] = "Something went wrong please see input data on form.";
        $status = $output->result;
	}

	echo $status;


?>