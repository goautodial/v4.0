<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
require_once('CRMDefaults.php');
require_once('goCRMAPISettings.php');

// check required fields
$reason = "Unable to Modify Call Times";

$validated = 1;
if (!isset($_POST["modifyid"])) {
	$validated = 0;
}

if ($validated == 1) {
    
	// collect new user data.	
	$modifyid = $_POST["modifyid"];
    
	$calltime_name = NULL; if (isset($_POST["calltime_name"])) { 
		$calltime_name = $_POST["calltime_name"]; 
		$calltime_name = stripslashes($calltime_name);
	}
	
    $calltime_comments = NULL; if (isset($_POST["calltime_comments"])) { 
		$calltime_comments = $_POST["calltime_comments"];
		$calltime_comments = stripslashes($calltime_comments);
	}

    $usergroup = NULL; if (isset($_POST["usergroup"])) { 
		$usergroup = $_POST["usergroup"]; 
		$usergroup = stripslashes($usergroup);
	}

    $url = gourl."/goCalltimes/goAPI.php"; #URL to GoAutoDial API. (required)
	
	if($_POST['start_default'] != "")
		$start_default =  date('Hi', strtotime($_POST['start_default']));
	else
		$start_default =  "0";
	if($_POST['stop_default'] != "")
		$stop_default =  date('Hi', strtotime($_POST['stop_default']));
	else
		$stop_default =  "0";
		
	if($_POST['start_sunday'] != "")
		$start_sunday =  date('Hi', strtotime($_POST['start_sunday']));
	else
		$start_sunday =  "0";
	if($_POST['stop_sunday'] != "")
		$stop_sunday =  date('Hi', strtotime($_POST['stop_sunday']));
	else
		$stop_sunday =  "0";
		
	if($_POST['start_monday'] != "")
		$start_monday =  date('Hi', strtotime($_POST['start_monday']));
	else
		$start_monday =  "0";
	if($_POST['stop_monday'] != "")
		$stop_monday =  date('Hi', strtotime($_POST['stop_monday']));
	else
		$stop_monday =  "0";
		
	if($_POST['start_tuesday'] != "")
		$start_tuesday =  date('Hi', strtotime($_POST['start_tuesday']));
	else
		$start_tuesday =  "0";
	if($_POST['stop_tuesday'] != "")
		$stop_tuesday =  date('Hi', strtotime($_POST['stop_tuesday']));
	else
		$stop_tuesday =  "0";
		
	if($_POST['start_wednesday'] != "")
		$start_wednesday =  date('Hi', strtotime($_POST['start_wednesday']));
	else
		$start_wednesday =  "0";
	if($_POST['stop_wednesday'] != "")
		$stop_wednesday =  date('Hi', strtotime($_POST['stop_wednesday']));
	else
		$stop_wednesday =  "0";
		
	if($_POST['start_thursday'] != "")
		$start_thursday =  date('Hi', strtotime($_POST['start_thursday']));
	else
		$start_thursday =  "0";
	if($_POST['stop_thursday'] != "")
		$stop_thursday =  date('Hi', strtotime($_POST['stop_thursday']));
	else
		$stop_thursday =  "0";
		
	if($_POST['start_friday'] != "")
		$start_friday =  date('Hi', strtotime($_POST['start_friday']));
	else
		$start_friday =  "0";
	if($_POST['stop_friday'] != "")
		$stop_friday =  date('Hi', strtotime($_POST['stop_friday']));
	else
		$stop_friday =  "0";
		
	if($_POST['start_saturday'] != "")
		$start_saturday =  date('Hi', strtotime($_POST['start_saturday']));
	else
		$start_saturday =  "0";
	if($_POST['stop_saturday'] != "")
		$stop_saturday =  date('Hi', strtotime($_POST['stop_saturday']));
	else
		$stop_saturday =  "0";
	
    $postfields["goUser"]             = goUser; #Username goes here. (required)
    $postfields["goPass"]             = goPass; #Password goes here. (required)
    $postfields["goAction"]           = "goEditCalltime"; #action performed by the [[API:Functions]]. (required)
    $postfields["responsetype"]       = responsetype; #json. (required)
    $postfields["hostname"]           = $_SERVER['REMOTE_ADDR']; #Default value

    $postfields["call_time_id"]       = $modifyid; #Desired uniqueid. (required)
    $postfields["call_time_name"]     = $calltime_name;
    $postfields["call_time_comments"] = $calltime_comments;
    $postfields["user_group"]         = $usergroup;
	
	$postfields["log_group"]          = $_POST['log_group'];
	$postfields["log_user"]           = $_POST['log_user'];

    $postfields["ct_default_start"]   = $start_default;
    $postfields["ct_default_stop"]    = $stop_default;

    $postfields["ct_sunday_start"]    = $start_sunday;
    $postfields["ct_sunday_stop"]     = $stop_sunday;

    $postfields["ct_monday_start"]    = $start_monday;
    $postfields["ct_monday_stop"]     = $stop_monday;

    $postfields["ct_tuesday_start"]   = $start_tuesday;
    $postfields["ct_tuesday_stop"]    = $stop_tuesday;

    $postfields["ct_wednesday_start"] = $start_wednesday;
    $postfields["ct_wednesday_stop"]  = $stop_wednesday;

    $postfields["ct_thursday_start"]  = $start_thursday;
    $postfields["ct_thursday_stop"]   = $stop_thursday;

    $postfields["ct_friday_start"]    = $start_friday;
    $postfields["ct_friday_stop"]     = $stop_friday;

    $postfields["ct_saturday_start"]  = $start_saturday;
    $postfields["ct_saturday_stop"]   = $stop_saturday;
	
	$postfields["default_audio"] = $_POST["audio_default"];
	$postfields["sunday_audio"] = $_POST["audio_sunday"];
	$postfields["monday_audio"] = $_POST["audio_monday"];
	$postfields["tuesday_audio"] = $_POST["audio_tuesday"];
	$postfields["wednesday_audio"] = $_POST["audio_wednesday"];
	$postfields["thursday_audio"] = $_POST["audio_thursday"];
	$postfields["friday_audio"] = $_POST["audio_friday"];
	$postfields["saturday_audio"] = $_POST["audio_saturday"];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    //curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $data = curl_exec($ch);
    curl_close($ch);
    $output = json_decode($data);

    //var_dump($output);

    if ($output->result=="success") {
    # Result was OK!
        echo 1;
    } else {
    # An error occured
		echo $output->result;
        //$lh->translateText("unable_modify_list");
    }
    
} else { 
	//ob_clean(); 
	print $reason; 
}
?>