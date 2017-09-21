<?php

require_once('CRMDefaults.php');
require_once('goCRMAPISettings.php');

// check required fields
$reason = "Unable to Modify Lead Recycling";

$validated = 1;
if (!isset($_POST["recycleid"])) {
	$validated = 0;
}

if ($validated == 1) {
    
	// collect new user data.	
	$modifyid = $_POST["recycleid"];
    
	$attempt_delay = NULL; if (isset($_POST["attempt_delay"])) { 
		$attempt_delay = $_POST["attempt_delay"]; 
		$attempt_delay = stripslashes($attempt_delay);
	}
	
    $attempt_maximum = NULL; if (isset($_POST["attempt_maximum"])) { 
		$attempt_maximum = $_POST["attempt_maximum"];
		$attempt_maximum = stripslashes($attempt_maximum);
	}

    $active = NULL; if (isset($_POST["active"])) { 
		$active = $_POST["active"]; 
		$active = stripslashes($active);
	}

    $url = gourl."/goLeadRecycling/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"]             = goUser; #Username goes here. (required)
    $postfields["goPass"]             = goPass; #Password goes here. (required)
    $postfields["goAction"]           = "goEditLeadRecycling"; #action performed by the [[API:Functions]]. (required)
    $postfields["responsetype"]       = responsetype; #json. (required)
    $postfields["hostname"]           = $_SERVER['REMOTE_ADDR']; #Default value

    $postfields["recycle_id"] 			= $modifyid; #Desired uniqueid. (required)
    $postfields["attempt_delay"] 		= $attempt_delay;
    $postfields["attempt_maximum"] 		= $attempt_maximum;
    $postfields["active"] 				= $active;
	$postfields["session_user"]         = $_POST['session_user'];

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

	echo $output->result;
} else { 
	//ob_clean(); 
	print $reason; 
}
?>