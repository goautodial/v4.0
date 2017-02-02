<?php
	require_once('goCRMAPISettings.php');
	
	$campaign_id = $_POST['campaign_id'];
	$phone_numbers = $_POST['phone_numbers'];
	$stage = $_POST['stageDNC'];
	$user_id = $_POST['user_id'];
	
    $url = gourl."/goLists/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goActionDNC"; #action performed by the [[API:Functions]]. (required)
	$postfields['user_id'] = $user_id;
	$postfields['campaign_id'] = $campaign_id;
	$postfields['phone_numbers'] = $phone_numbers;
	$postfields['stage'] = $stage;
    $postfields["responsetype"] = responsetype; #json. (required)
	$postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
	$postfields["log_user"] = $_POST['log_user'];
	$postfields["log_group"] = $_POST['log_group'];
	
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    $data = curl_exec($ch);
    curl_close($ch);
    $output = json_decode($data);

	if($output->result == "success"){
		$status = $output->msg;
	}else{
		$status = "error";
	}
	
	echo $status;
?>
