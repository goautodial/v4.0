<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
require_once('CRMDefaults.php');
//require_once('LanguageHandler.php');
require_once('goCRMAPISettings.php');
//require_once('DbHandler.php');

//$lh = \creamy\LanguageHandler::getInstance();

// check required fields
$validated = 1;
if (!isset($_POST["usergroup_id"])) {
	$validated = 0;
}

if ($validated == 1) {
	$usergroup_id = $_POST["usergroup_id"];
	
    $url = gourl."/goUserGroups/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goDeleteUserGroup"; #action performed by the [[API:Functions]]. (required)
    $postfields["responsetype"] = responsetype; #json. (required)
    $postfields["user_group"] = $usergroup_id; #Desired User ID. (required)
    $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
    $postfields["log_user"] = $_POST['log_user'];
    $postfields["log_group"] = $_POST['log_group'];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    $data = curl_exec($ch);
    curl_close($ch);
    $output = json_decode($data);
     
    if ($output->result=="success") {
    # Result was OK!
		echo 1;
    }else{
        echo $output->result;
    }

}
?>