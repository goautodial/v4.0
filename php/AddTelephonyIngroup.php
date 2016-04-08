<?php

	/** Telephony In-group API - Add a new Telephony In-group */
	/**
	 * Generates action circle buttons for different pages/module
	 */
require_once('goCRMAPISettings.php');
	 
$validate = 0;	
    if($_POST['groupid'] == ""){
        $validate = 1;
    }
    if($_POST['groupname'] == ""){
        $validate = 1;
    }
    if($_POST['color'] == ""){
        $validate = 1;
    }
    if($_POST['active'] == ""){
        $validate = 1;
    }
    if($_POST['web_form'] == ""){
        $validate = 1;
    }
    if($_POST['user_group'] == ""){
        $validate = 1;
    }
    if($_POST['ingroup_voicemail'] == ""){
        $validate = 1;
    }
    if($_POST['next_agent_call'] == ""){
        $validate = 1;
    }
    if($_POST['display'] == ""){
        $validate = 1;
    }
    if($_POST['script'] == ""){
        $validate = 1;
    }
    if($_POST['call_launch'] == ""){
        $validate = 1;
    }
    
$color = $_POST["color"];
$color = str_replace("#", '', $color);

if($validate == 1){
    echo "incomplete";
}

if($validate == 0){
	$url = "http://gadcs.goautodial.com/goAPI/goInbound/goAPI.php"; # URL to GoAutoDial API file
	$postfields["goUser"] 			= goUser; #Username goes here. (required)
	$postfields["goPass"] 			= goPass; #Password goes here. (required)
	$postfields["goAction"] 		= "goAddInbound"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] 	= responsetype; #json (required)
	$postfields["hostname"] 		= $_SERVER['REMOTE_ADDR']; #Default value

    
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
}
?>