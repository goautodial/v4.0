<?php

require_once('CRMDefaults.php');
require_once('LanguageHandler.php');
require('Session.php');

$lh = \creamy\LanguageHandler::getInstance();
$user = \creamy\CreamyUser::currentUser();

// check required fields
$reason = $lh->translationFor("unable_modify_phones");

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
    
    $fullname = NULL; if (isset($_POST["fullname"])) { 
		$fullname = $_POST["fullname"]; 
		$fullname = stripslashes($fullname);
	}
    
    $protocol = NULL; if (isset($_POST["protocol"])) { 
		$protocol = $_POST["protocol"]; 
		$protocol = stripslashes($protocol);
	}
  
    
	$url = "https://encrypted.goautodial.com/goAPI/goPhones/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = "admin"; #Username goes here. (required)
    $postfields["goPass"] = "goautodial"; #Password goes here. (required)
    $postfields["goAction"] = "goEditPhone"; #action performed by the [[API:Functions]]
    $postfields["responsetype"] = "json"; #json (required)
    $postfields["extension"] = $modifyid; #Desired list id. (required)
	$postfields["dialplan_number"] = $plan; #Desired value for user (required)
	$postfields["voicemail_id"] = $vmid; #Desired value for user (required)
	$postfields["server_ip"] = $ip; #Desired value for user (required)
	$postfields["active"] = $active; #Desired value for user (required)
    $postfields["status"] = $status; #Desired value for user (required)
	$postfields["fullname"] = $fullname; #Desired value for user (required)
    $postfields["protocol"] = $protocol; #Desired value for user (required)
    $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
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
    
    echo $color;
    
    if ($output->result=="success") {
    # Result was OK!
        ob_clean();
		print (CRM_DEFAULT_SUCCESS_RESPONSE);
    } else {
    # An error occured
        ob_clean();
		echo $output->result;
        //$lh->translateText("unable_modify_list");
    }
    
} else { ob_clean(); print $reason; }
?>