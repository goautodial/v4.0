<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('CRMDefaults.php');
require_once('goCRMAPISettings.php');

// check required fields
$reason = "Unable to Modify Carrier";

$validated = 1;
if (!isset($_POST["modifyid"])) {
	$validated = 0;
}

if ($validated == 1) {
    
	// collect new user data.	
	$modifyid = $_POST["modifyid"];
    
	$carrier_name = NULL; if (isset($_POST["carrier_name"])) { 
		$carrier_name = $_POST["carrier_name"]; 
		$carrier_name = stripslashes($carrier_name);
	}
	
    $carrier_description = NULL; if (isset($_POST["carrier_description"])) { 
		$carrier_description = $_POST["carrier_description"];
		$carrier_description = stripslashes($carrier_description);
	}

    $protocol = NULL; if (isset($_POST["protocol"])) { 
		$protocol = $_POST["protocol"]; 
		$protocol = stripslashes($protocol);
	}
	
    $server_ip = NULL; if (isset($_POST["server_ip"])) { 
		$server_ip = $_POST["server_ip"]; 
		$server_ip = stripslashes($server_ip);
	}

	$active = NULL; if (isset($_POST["active"])) { 
		$active = $_POST["active"]; 
		$active = stripslashes($active);
	}
    
	$url = gourl."/goCarriers/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goEditCarrier"; #action performed by the [[API:Functions]]
    $postfields["responsetype"] = responsetype; #json (required)
    $postfields["carrier_id"] = $modifyid; 
	$postfields["carrier_name"] = $carrier_name; 
	$postfields["carrier_description"] = $carrier_description; 
	$postfields["protocol"] = $protocol; 
	$postfields["server_ip"] = $server_ip; 
	$postfields["active"] = $active; 

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    //curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
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