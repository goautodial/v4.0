<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

require_once('CRMDefaults.php');
require_once('goCRMAPISettings.php');

// check required fields
$reason = "Unable to Modify User Group";

$validated = 1;
if (!isset($_POST["modifyid"])) {
	$validated = 0;
}

if ($validated == 1) {
    
	// collect new user data.	
	$modifyid = $_POST["modifyid"];
    
	$group_name = NULL; if (isset($_POST["group_name"])) { 
		$group_name = $_POST["group_name"]; 
		$group_name = stripslashes($group_name);
	}
	
    $group_level = NULL; if (isset($_POST["group_level"])) { 
		$group_level = $_POST["group_level"];
		$group_level = stripslashes($group_level);
	}

    $forced_timeclock_login = NULL; if (isset($_POST["forced_timeclock_login"])) { 
		$forced_timeclock_login = $_POST["forced_timeclock_login"]; 
		$forced_timeclock_login = stripslashes($forced_timeclock_login);
	}
	
    $shift_enforcement = NULL; if (isset($_POST["shift_enforcement"])) { 
		$shift_enforcement = $_POST["shift_enforcement"]; 
		$shift_enforcement = stripslashes($shift_enforcement);
	}
    
	$url = gourl."/goUserGroups/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goEditUserGroup"; #action performed by the [[API:Functions]]
    $postfields["responsetype"] = responsetype; #json (required)
    $postfields["user_group"] = $modifyid; #Desired list id. (required)
	$postfields["group_name"] = $group_name; #Desired value for user (required)
	$postfields["group_level"] = $group_level; #Desired value for user (required)
	$postfields["forced_timeclock_login"] = $forced_timeclock_login; #Desired value for user (required)
	$postfields["shift_enforcement"] = $shift_enforcement; #Desired value for user (required)
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