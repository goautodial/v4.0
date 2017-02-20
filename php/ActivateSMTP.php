<?php
	require_once('goCRMAPISettings.php');
	
	$id = $_POST['action_id'];
	
    $url = gourl."/goSMTP/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goSMTPActivation"; #action performed by the [[API:Functions]]. (required)
	$postfields['action_smtp'] = $id;
	
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
		$status = $output->result;
	}else{
		$status = "error";
	}
	
	echo $status;
?>
