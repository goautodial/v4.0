<?php

require_once('goCRMAPISettings.php');	

/**
  * Telephony Lead Filters API - create a new lead filter
  * [[API - Function]] - goAddLeadFilter
  * This application is used to create new lead filter. Newly created lead filter belongs to user that authenticated a request.
**/

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


     $ch = curl_init();
     curl_setopt($ch, CURLOPT_URL, $url);
     curl_setopt($ch, CURLOPT_POST, 1);
     curl_setopt($ch, CURLOPT_TIMEOUT, 100);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
     curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
     $data = curl_exec($ch);
     curl_close($ch);
     $output = json_decode($data);

    if ($output->result=="success") {
       # Result was OK!
            $status = "Added New Filter ID: ".$_REQUEST['lead_filter_id'];
     } else {
       # An error occurred
            $status = $output->result;
    }

	echo $status;
	
}else{
	echo "incomplete";
}
?>