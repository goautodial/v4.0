<?php
    require_once('UIHandler.php');
    require_once('goCRMAPISettings.php');
	include('Session.php');
	
	$ui = \creamy\UIHandler::getInstance();
	//$perm = $ui->goGetPermissions('pausecodes', $_SESSION['usergroup']);

    $url = gourl."/goCampaigns/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "getDIDSettings"; #action performed by the [[API:Functions]]. (required)
    $postfields["responsetype"] = responsetype; #json. (required)
    $postfields["did"] = $_POST['did']; #json. (required)

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    $data = curl_exec($ch);
    curl_close($ch);
    $output = json_decode($data);
    //echo json_encode($data, true);die;
    if($output->result=="success"){
        $data = $output->data;
        echo json_encode($data, true);
    }else{
        echo json_encode("empty", true);
    }
?>