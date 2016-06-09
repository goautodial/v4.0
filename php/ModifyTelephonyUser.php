<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('CRMDefaults.php');
require_once('goCRMAPISettings.php');

// check required fields
$reason = "Unable to Modify Users";
$validated = 1;
if (!isset($_POST["modifyid"])) {
	$validated = 0;
}

if ($validated == 1) {

	// collect new user data.	
	echo $modifyid = $_POST["modifyid"];
    
	$name = NULL; if (isset($_POST["fullname"])) { 
		$name = $_POST["fullname"]; 
		$name = stripslashes($name);
	}

	$email = NULL; if (isset($_POST["email"])) { 
		$email = $_POST["email"]; 
		$email = stripslashes($email);
	}

    $user_group = NULL; if (isset($_POST["usergroup"])) { 
		$user_group = $_POST["usergroup"]; 
		$user_group = stripslashes($user_group);
	}
    
    $status = NULL; if (isset($_POST["status"])) { 
		$status = $_POST["status"]; 
		$status = stripslashes($status);
	}
    
    $userlevel = NULL; if (isset($_POST["userlevel"])) { 
		$userlevel = $_POST["userlevel"]; 
		$userlevel = stripslashes($userlevel);
	}
	
	
	$url = gourl."/goUsers/goAPI.php"; # URL to GoAutoDial API file
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goEditUser"; #action performed by the [[API:Functions]]
    $postfields["responsetype"] = responsetype; #json (required)
	$postfields["user"] = $modifyid; #Desired value for user (required)
	$postfields["full_name"] = $name; #Desired value for user (required)
	$postfields["user_group"] = $user_group; #Desired value for user (required)
	$postfields["user_level"] = $userlevel; #Desired value for user (required)
	$postfields["active"] = $status; #Desired value for user (required)
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
    
    //var_dump($output);

    if ($output->result=="success") {
    # Result was OK!
        ob_clean();
		echo "success";
    } else {
    # An error occured
        ob_clean();
		echo $output->result;
        //$lh->translateText("unable_modify_user");
    }
    
} else { ob_clean(); print $reason; }
?>