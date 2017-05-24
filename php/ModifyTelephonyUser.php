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
	$modifyid = $_POST["modifyid"];
    
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
	
	$voicemail = NULL; if (isset($_POST["voicemail"])) { 
		$voicemail = $_POST["voicemail"]; 
		$voicemail = stripslashes($voicemail);
	}

	$hotkeys_active = NULL; if (isset($_POST["hotkeys"])) { 
		$hotkeys_active = $_POST["hotkeys"]; 
		$hotkeys_active = stripslashes($hotkeys_active);
	}
	
	$pass = ""; if (isset($_POST["password"])) { 
		$pass = $_POST["password"]; 
		$pass = stripslashes($pass);
	}

	$phone_login = NULL; if (isset($_POST["phone_login"])) { 
		$phone_login = $_POST["phone_login"]; 
		$phone_login = stripslashes($phone_login);
	}

	$phone_pass = NULL; if (isset($_POST["phone_password"])) { 
		$phone_pass = $_POST["phone_password"]; 
		$phone_pass = stripslashes($phone_pass);
	}
	
	$vdc_agent_api_access = NULL; if (isset($_POST["api_access"])) { 
		$vdc_agent_api_access = $_POST["api_access"]; 
		$vdc_agent_api_access = stripslashes($vdc_agent_api_access);
	}
	$agent_choose_ingroups = NULL; if (isset($_POST["choose_ingroup"])) { 
		$agent_choose_ingroups = $_POST["choose_ingroup"]; 
		$agent_choose_ingroups = stripslashes($agent_choose_ingroups);
	}

	$vicidial_recording_override = NULL; if (isset($_POST["vicidial_recording_override"])) { 
		$vicidial_recording_override = $_POST["vicidial_recording_override"]; 
		$vicidial_recording_override = stripslashes($vicidial_recording_override);
	}
	$vicidial_transfers = NULL; if (isset($_POST["vicidial_transfers"])) { 
		$vicidial_transfers = $_POST["vicidial_transfers"]; 
		$vicidial_transfers = stripslashes($vicidial_transfers);
	}
	$closer_default_blended = NULL; if (isset($_POST["closer_default_blended"])) { 
		$closer_default_blended = $_POST["closer_default_blended"]; 
		$closer_default_blended = stripslashes($closer_default_blended);
	}
	$agentcall_manual = NULL; if (isset($_POST["agentcall_manual"])) { 
		$agentcall_manual = $_POST["agentcall_manual"]; 
		$agentcall_manual = stripslashes($agentcall_manual);
	}
	$scheduled_callbacks = NULL; if (isset($_POST["scheduled_callbacks"])) { 
		$scheduled_callbacks = $_POST["scheduled_callbacks"]; 
		$scheduled_callbacks = stripslashes($scheduled_callbacks);
	}
	$agentonly_callbacks = NULL; if (isset($_POST["agentonly_callbacks"])) { 
		$agentonly_callbacks = $_POST["agentonly_callbacks"]; 
		$agentonly_callbacks = stripslashes($agentonly_callbacks);
	}
	$agent_lead_search_override = NULL; if (isset($_POST["agent_lead_search_override"])) { 
		$agent_lead_search_override = $_POST["agent_lead_search_override"]; 
		$agent_lead_search_override = stripslashes($agent_lead_search_override);
	}

	$url = gourl."/goUsers/goAPI.php"; # URL to GoAutoDial API file
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goEditUser"; #action performed by the [[API:Functions]]
    $postfields["responsetype"] = responsetype; #json (required)
	$postfields["user_id"] = $modifyid; #Desired value for user (required)
	$postfields["full_name"] = $name; #Desired value for user (required)
	$postfields["user_group"] = $user_group; #Desired value for user (required)
	$postfields["user_level"] = $userlevel; #Desired value for user (required)
	$postfields["active"] = $status; #Desired value for user (required)
	$postfields["voicemail"] = $voicemail; #Desired value for user (required)
    $postfields["email"] = $email;
	$postfields["pass"] = $pass;
    $postfields["phone_login"] = $phone_login;
    $postfields["phone_pass"] = $phone_pass;
    $postfields["hotkeys_active"] = $hotkeys_active;
	$postfields["agent_lead_search_override"] = $agent_lead_search_override;
    $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
	
    $postfields["log_user"] = $_POST['log_user'];
    $postfields["log_group"] = $_POST['log_group'];

    if (isset($_POST["api_access"])) { 
    	$postfields["vdc_agent_api_access"] = $vdc_agent_api_access;
	}
	if (isset($_POST["choose_ingroup"])) { 
    	$postfields["agent_choose_ingroups"] = $agent_choose_ingroups;
	}
	if (isset($_POST["vicidial_recording_override"])) { 
    	$postfields["vicidial_recording_override"] = $vicidial_recording_override;
	}
	if (isset($_POST["vicidial_transfers"])) { 
    	$postfields["vicidial_transfers"] = $vicidial_transfers;
	}
	if (isset($_POST["closer_default_blended"])) { 
    	$postfields["closer_default_blended"] = $closer_default_blended;
	}
	if (isset($_POST["agentcall_manual"])) { 
    	$postfields["agentcall_manual"] = $agentcall_manual;
	}
	if (isset($_POST["scheduled_callbacks"])) { 
    	$postfields["scheduled_callbacks"] = $scheduled_callbacks;
	}
	if (isset($_POST["agentonly_callbacks"])) { 
    	$postfields["agentonly_callbacks"] = $agentonly_callbacks;
	}

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
    
    //var_dump($data);

    if ($output->result=="success") {
    # Result was OK!
        echo 1;
    } else {
		echo $output->result;
        //$lh->translateText("unable_modify_user");
    }
    
} else { ob_clean(); print $reason; }
?>