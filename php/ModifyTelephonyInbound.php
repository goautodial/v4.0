<?php

require_once('CRMDefaults.php');
require_once('LanguageHandler.php');
require('Session.php');
require_once('goCRMAPISettings.php');

$lh = \creamy\LanguageHandler::getInstance();
$user = \creamy\CreamyUser::currentUser();

// check required fields
//$reason = $lh->translationFor("unable_modify_inbound");

$validated = 0;

$groupid = NULL;
if (isset($_POST["modify_groupid"])) {
	$groupid = $_POST["modify_groupid"];
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
    
	$url = "https://gadcs.goautodial.com/goAPI/goInbound/goAPI.php"; #URL to GoAutoDial API. (required)
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

    var_dump($output);
	
    if ($output->result=="success") {
    # Result was OK!
        ob_clean();
		print "success";
    } else {
    # An error occured
        ob_clean();
		print $output->result;
        //$lh->translateText("unable_modify_list");
    }
    
} else { ob_clean(); print $reason; }

// PHONE NUMBER / DID
if ($did != NULL) {
	// collect new user data.	
	$modify_did = $_POST["modify_did"];
	
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
    
	$url = "https://encrypted.goautodial.com/goAPI/goInbound/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = "admin"; #Username goes here. (required)
    $postfields["goPass"] = "goautodial"; #Password goes here. (required)
    $postfields["goAction"] = "goEditDIDAPI"; #action performed by the [[API:Functions]]
    $postfields["responsetype"] = "json"; #json (required)
    $postfields["did_pattern"] = $modify_did; #Desired list id. (required)
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
	
    if ($output->result == "success") {
    # Result was OK!
        ob_clean();
		print "success";
    } else {
    # An error occured
        ob_clean();
		print $output->result;
        //$lh->translateText("unable_modify_list");
    }
    
}else { ob_clean();
print $reason;
//print $validated;
 }
?>