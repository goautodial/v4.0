<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

require_once('CRMDefaults.php');
require_once('goCRMAPISettings.php');

// check required fields
$reason = "Unable to Modify Voicemail";

$validated = 1;
if (!isset($_POST["modifyid"])) {
	$validated = 0;
}

if ($validated == 1) {
    
	// collect new user data.	
	$modifyid = $_POST["modifyid"];
    
	$pass = NULL; if (isset($_POST["password"])) { 
		$pass = $_POST["password"]; 
		$pass = stripslashes($password);
	}
	
    $fullname = NULL; if (isset($_POST["fullname"])) { 
		$fullname = $_POST["fullname"];
		$fullname = stripslashes($fullname);
	}

    $email = NULL; if (isset($_POST["email"])) { 
		$email = $_POST["email"]; 
		$email = stripslashes($email);
	}
	
    $active = NULL; if (isset($_POST["active"])) { 
		$active = $_POST["active"]; 
		$active = stripslashes($active);
	}
	
    $delete_vm_after_email = NULL; if (isset($_POST["delete_vm_after_email"])) { 
		$delete_vm_after_email = $_POST["delete_vm_after_email"]; 
		$delete_vm_after_email = stripslashes($delete_vm_after_email);
	}  
    
	$url = gourl."/goVoicemails/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goEditVoicemail"; #action performed by the [[API:Functions]]
    $postfields["responsetype"] = responsetype; #json (required)
    $postfields["voicemail_id"] = $modifyid; #Desired list id. (required)
	$postfields["pass"] = $pass; #Desired value for user (required)
	$postfields["fullname"] = $fullname; #Desired value for user (required)
	$postfields["email"] = $email; #Desired value for user (required)
	$postfields["active"] = $active; #Desired value for user (required)
    $postfields["delete_vm_after_email"] = $delete_vm_after_email; #Desired value for user (required)
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