<?php

	/** Telephony Users API - Add a new Telephony User */
	/**
	 * Generates action circle buttons for different pages/module
	 */
require_once('goCRMAPISettings.php');
	 
$validate = 0;	
    if($_POST['route'] == ""){
        $validate = 1;
    }
    if($_POST['did_exten'] == ""){
        $validate = 1;
    }
    if($_POST['desc'] == ""){
        $validate = 1;
    }

if($validate == 1){
    echo "incomplete";    
}  
    
if($validate == 0){
	$url = gourl."/goInbound/goAPI.php"; # URL to GoAutoDial API file
	$postfields["goUser"] 			= goUser; #Username goes here. (required)
	$postfields["goPass"] 			= goPass; #Password goes here. (required)
	$postfields["goAction"] 		= "goAddDID"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] 	= responsetype; #json (required)
	$postfields["hostname"] 		= $_SERVER['REMOTE_ADDR']; #Default value
    
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
    
        $postfields["did_pattern"]              = $_POST['did_exten']; #Desired pattern (required)
        $postfields["did_description"]          = $_POST['desc']; #Desired description(required)
        $postfields["did_route"]                = $_POST['route']; #'EXTEN','VOICEMAIL','AGENT','PHONE','IN_GROUP','CALLMENU', or'VMAIL_NO_INST' (required)
        $postfields["user_group"]               = $_POST['user_groups']; #Assign to user group
        $postfields["did_active"]               = $_POST['active']; #Y or N (required)
    
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