<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

require_once('CRMDefaults.php');
require_once('goCRMAPISettings.php');

// check required fields
$reason = "Unable to Modify Phones";

$validated = 1;
if (!isset($_POST["modifyid"])) {
	$validated = 0;
}

if ($validated == 1) {
    
	// collect new user data.	
	$modifyid = $_POST["modifyid"];
    
	$plan = NULL; if (isset($_POST["plan"])) { 
		$plan = $_POST["plan"]; 
		$plan = stripslashes($plan);
	}
	
    $vmid = NULL; if (isset($_POST["vmid"])) { 
		$vmid = $_POST["vmid"];
		$vmid = stripslashes($vmid);
	}

    $ip = NULL; if (isset($_POST["ip"])) { 
		$ip = $_POST["ip"]; 
		$ip = stripslashes($ip);
	}
	
    $active = NULL; if (isset($_POST["active"])) { 
		$active = $_POST["active"]; 
		$active = stripslashes($active);
	}
	
    $status = NULL; if (isset($_POST["status"])) { 
		$status = $_POST["status"]; 
		$status = stripslashes($status);
	}
    
    $fullname = NULL; if (isset($_POST["pfullname"])) { 
		$fullname = $_POST["pfullname"]; 
		$fullname = stripslashes($fullname);
	}
    
    $protocol = NULL; if (isset($_POST["protocol"])) { 
		$protocol = $_POST["protocol"]; 
		$protocol = stripslashes($protocol);
	}
  
    
	$url = gourl."/goPhones/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goEditPhone"; #action performed by the [[API:Functions]]
    $postfields["responsetype"] = responsetype; #json (required)
    $postfields["extension"] = $modifyid; #Desired list id. (required)
	$postfields["dialplan_number"] = $plan; #Desired value for user (required)
	$postfields["voicemail_id"] = $vmid; #Desired value for user (required)
	$postfields["server_ip"] = $ip; #Desired value for user (required)
	$postfields["active"] = $active; #Desired value for user (required)
    $postfields["status"] = $status; #Desired value for user (required)
	$postfields["fullname"] = $fullname; #Desired value for user (required)
    $postfields["protocol"] = $protocol; #Desired value for user (required)
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
    } else {
    # An error occured
		echo $output->result;
        //$lh->translateText("unable_modify_list");
    }
    
} else { ob_clean(); print $reason; }
?>