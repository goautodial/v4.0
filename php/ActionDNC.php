<?php
	require_once('goCRMAPISettings.php');
	
	$campaign_id = $_POST['campaign_id'];
	$phone_numbers = rawurlencode($_POST['phone_numbers']);
	$stage = $_POST['stageDNC'];
	
    $url = gourl."/goLists/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goActionDNC"; #action performed by the [[API:Functions]]. (required)
	$postfields['campaign_id'] = $campaign_id;
	$postfields['phone_numbers'] = $phone_numbers;
	$postfields['stage'] = $stage;
    $postfields["responsetype"] = responsetype; #json. (required)
	$postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
	$postfields["session_user"] = $_POST['session_user'];
	
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

	if($output->result == "success"){
		$status = $output->msg;
	}else{
		$status = "error";
	}
	
	echo $status;
?>
