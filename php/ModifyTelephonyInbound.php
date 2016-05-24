<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('CRMDefaults.php');
require_once('goCRMAPISettings.php');

// check required fields
$reason = "Unable to Modify Inbound";

$validated = 0;

$groupid = NULL;
if (isset($_POST["modify_groupid"])) {
	$groupid = $_POST["modify_groupid"];
}

$ivr = NULL;
if (isset($_POST["modify_ivr"])) {
	$ivr = $_POST["modify_ivr"];
}

$did = NULL;
if (isset($_POST["modify_did"])) {
	$did = $_POST["modify_did"];
}


// INGROUPS
if ($groupid != NULL) {
	// collect new user data.	
	$modify_groupid = $_POST["modify_groupid"];
	
	$desc = NULL; if (isset($_POST["desc"])) { 
		$desc = $_POST["desc"]; 
		$desc = stripslashes($desc);
	}
	
    $color = NULL; if (isset($_POST["color"])) { 
		$color = $_POST["color"];
        $color = str_replace("#", '', $color);
		$color = stripslashes($color);
	}

    $status = NULL; if (isset($_POST["status"])) { 
		$status = $_POST["status"]; 
		$status = stripslashes($status);
	}
	
    $webform = NULL; if (isset($_POST["webform"])) { 
		$webform = $_POST["webform"]; 
		$webform = stripslashes($webform);
	}
	
    $nextagent = NULL; if (isset($_POST["nextagent"])) { 
		$nextagent = $_POST["nextagent"]; 
		$nextagent = stripslashes($nextagent);
	}
    
    $prio = NULL; if (isset($_POST["prio"])) { 
		$prio = $_POST["prio"]; 
		$prio = stripslashes($prio);
	}
    
    $display = NULL; if (isset($_POST["display"])) { 
		$display = $_POST["display"]; 
		$display = stripslashes($display);
	}
    
    $script = NULL; if (isset($_POST["script"])) { 
		$script = $_POST["script"]; 
		$script = stripslashes($script);
	}
    
	$url = gourl."/goInbound/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goEditInbound"; #action performed by the [[API:Functions]]
    $postfields["responsetype"] = responsetype; #json (required)
    $postfields["group_id"] = $modify_groupid; #Desired list id. (required)
	$postfields["group_name"] = $desc; #Desired value for user (required)
	$postfields["group_color"] = $color; #Desired value for user (required)
	$postfields["web_form_address"] = $webform; #Desired value for user (required)
	$postfields["active"] = $status; #Desired value for user (required)
    $postfields["next_agent_call"] = $nextagent; #Desired value for user (required)
	$postfields["fronter_display"] = $display; #Desired value for user (required)
    $postfields["ingroup_script"] = $script; #Desired value for user (required)
    $postfields["queue_priority"] = $prio; #Desired value for user (required)
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
	
    if ($output->result=="success") {
    # Result was OK!
        ob_clean();
		print (CRM_DEFAULT_SUCCESS_RESPONSE);
    } else {
    # An error occured
        ob_clean();
		print $output->result;
        //$lh->translateText("unable_modify_list");
    }
    
}

// IVR
if ($ivr != NULL) {
	// collect new user data.		
	$name = NULL; if (isset($_POST["name"])) { 
		$name = $_POST["name"]; 
		$name = stripslashes($name);
	}
	
    $menu_prompt = NULL; if (isset($_POST["menu_prompt"])) { 
		$menu_prompt = $_POST["menu_prompt"]; 
		$menu_prompt = stripslashes($menu_prompt);
	}
	
    $menu_timeout = NULL; if (isset($_POST["menu_timeout"])) { 
		$menu_timeout = $_POST["menu_timeout"]; 
		$menu_timeout = stripslashes($menu_timeout);
	}	

	$menu_timeout_prompt = NULL; if (isset($_POST["menu_timeout_prompt"])) { 
		$menu_timeout_prompt = $_POST["menu_timeout_prompt"]; 
		$menu_timeout_prompt = stripslashes($menu_timeout_prompt);
	}	
	
	$menu_invalid_prompt = NULL; if (isset($_POST["menu_invalid_prompt"])) { 
		$menu_invalid_prompt = $_POST["menu_invalid_prompt"]; 
		$menu_invalid_prompt = stripslashes($menu_invalid_prompt);
	}	

	$menu_repeat = NULL; if (isset($_POST["menu_repeat"])) { 
		$menu_repeat = $_POST["menu_repeat"]; 
		$menu_repeat = stripslashes($menu_repeat);
	}	
    
	$url = gourl."/goInbound/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goEditIVR"; #action performed by the [[API:Functions]]
    $postfields["responsetype"] = responsetype; #json (required)
    $postfields["menu_id"] = $ivr; 
	$postfields["menu_name"] = $name; 
	$postfields["menu_prompt"] = $menu_prompt; 
	$postfields["menu_timeout"] = $menu_timeout; 
	$postfields["menu_timeout_prompt"] = $menu_timeout_prompt; 
	$postfields["menu_invalid_prompt"] = $menu_invalid_prompt; 
	$postfields["menu_repeat"] = $menu_repeat; 
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
	
    if ($output->result=="success") {
    # Result was OK!
        ob_clean();
		print (CRM_DEFAULT_SUCCESS_RESPONSE);
    } else {
    # An error occured
        ob_clean();
		print $output->result;
        //$lh->translateText("unable_modify_list");
    }
    
}

// PHONE NUMBER / DID
if ($did != NULL) {
	// collect new user data.	
	$modify_did = NULL; if (isset($_POST["modify_did"])) { 
		$modify_did = $_POST["modify_did"];
		$modify_did = stripslashes($modify_did);
	}

    $exten = NULL; if (isset($_POST["exten"])) { 
		$exten = $_POST["exten"];
		$exten = stripslashes($exten);
	}
	
	$desc = NULL; if (isset($_POST["desc"])) { 
		$desc = $_POST["desc"]; 
		$desc = stripslashes($desc);
	}

    $route = NULL; if (isset($_POST["route"])) { 
		$route = $_POST["route"]; 
		$route = stripslashes($route);
	}
	
    $status = NULL; if (isset($_POST["status"])) { 
		$status = $_POST["status"]; 
		$status = stripslashes($status);
	}
    
	$url = gourl."/goInbound/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goEditDID"; #action performed by the [[API:Functions]]
    $postfields["responsetype"] = responsetype; #json (required)
    $postfields["did_id"] = $modify_did; #Desired list id. (required)
    $postfields["did_pattern"] = $did; #Desired list id. (required)
	$postfields["did_description"] = $desc; #Desired value for user (required)
	$postfields["route"] = $route; #Desired value for user (required)
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

    //print($output);

    if ($output->result == "success") {
    # Result was OK!
         ob_clean();
		print (CRM_DEFAULT_SUCCESS_RESPONSE);
    } else {
    # An error occured
        ob_clean();
		print $output->result;
        //$lh->translateText("unable_modify_list");
    }
    
}
?>