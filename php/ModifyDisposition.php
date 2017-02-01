<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('CRMDefaults.php');
require_once('goCRMAPISettings.php');

// check required fields
$reason = "Unable to Modify Disposition";

$disposition = NULL;
if (isset($_POST["disposition"])) {
	$disposition = $_POST["disposition"];
}


// DISPOSITION
if ($disposition != NULL) {
	if(!isset($_POST['selectable'])){
		$_POST['selectable'] = "N";
	}

	if(!isset($_POST['human_answered'])){
		$_POST['human_answered'] = "N";
	}

	if(!isset($_POST['sale'])){
		$_POST['sale'] = "N";
	}

	if(!isset($_POST['dnc'])){
		$_POST['dnc'] = "N";
	}

	if(!isset($_POST['scheduled_callback'])){
		$_POST['scheduled_callback'] = "N";
	}

	if(!isset($_POST['customer_contact'])){
		$_POST['customer_contact'] = "N";
	}

	if(!isset($_POST['not_interested'])){
		$_POST['not_interested'] = "N";
	}

	if(!isset($_POST['unworkable'])){
		$_POST['unworkable'] = "N";
	}
		
	$status = NULL; if (isset($_POST["status"])) { 
		$status = $_POST["status"]; 
		$status = stripslashes($status);
	}
	$status_name = NULL; if (isset($_POST["status_name"])) { 
		$status_name = $_POST["status_name"]; 
		$status_name = stripslashes($status_name);
	}
    
	$url = gourl."/goDispositions/goAPI.php"; # URL to GoAutoDial API file
	$postfields["goUser"] = goUser; #Username goes here. (required)
	$postfields["goPass"] = goPass; #Password goes here. (required)
	$postfields["goAction"] = "goEditDisposition"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] = responsetype; #json (required)
	$postfields["campaign_id"] 			= $disposition;
	$postfields["status"] 				= $status;
	$postfields['status_name'] 			= $status_name;
	$postfields['selectable'] 			= $_POST['selectable'];
	$postfields['human_answered'] 		= $_POST['human_answered'];
	$postfields['sale'] 				= $_POST['sale'];
	$postfields['dnc'] 					= $_POST['dnc'];
	$postfields['scheduled_callback'] 	= $_POST['scheduled_callback'];
	$postfields['customer_contact'] 	= $_POST['customer_contact'];
	$postfields['not_interested'] 		= $_POST['not_interested'];
	$postfields['unworkable'] 			= $_POST['unworkable'];

	$postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
	$postfields["log_user"] = $_POST['log_user'];
	$postfields["log_group"] = $_POST['log_group'];
	
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

    //var_dump($output);

    if ($output->result=="success") {
    # Result was OK!
        echo 1;
    } else {
    # An error occured
		print $output->result;
		//print $output->count;
        //$lh->translateText("unable_modify_list");
    }
}else{
	echo $reason;
}
?>