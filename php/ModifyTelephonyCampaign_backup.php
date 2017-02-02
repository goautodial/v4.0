<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('CRMDefaults.php');
require_once('goCRMAPISettings.php');

// check required fields
$reason = "Unable to Modify Campaign";

$validated = 0;

$campaign = NULL;
if (isset($_POST["modify_campaign"])) {
	$campaign = $_POST["modify_campaign"];
}

$disposition = NULL;
if (isset($_POST["disposition"])) {
	$disposition = $_POST["disposition"];
}

$leadfilter = NULL;
if (isset($_POST["modify_leadfilter"])) {
	$leadfilter = $_POST["modify_leadfilter"];
}

// CAMPAIGN
if ($campaign != NULL) {
	// collect new user data.	
	$name = NULL; if (isset($_POST["name"])) { 
		$name = $_POST["name"]; 
		$name = stripslashes($name);
	}

	$dial_method = NULL; if (isset($_POST["dial_method"])) { 
		$dial_method = $_POST["dial_method"]; 
		$dial_method = stripslashes($dial_method);
	}

    $status = NULL; if (isset($_POST["status"])) { 
		$status = $_POST["status"]; 
		$status = stripslashes($status);
	}

	$url = gourl."/goCampaigns/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goEditCampaign"; #action performed by the [[API:Functions]]
    $postfields["responsetype"] = responsetype; #json (required)
    $postfields["campaign_id"] = $campaign; #Desired list id. (required)
	$postfields["campaign_name"] = $name; #Desired value for user (required)
	$postfields["active"] = $status; #Desired value for user (required)
    $postfields["dial_method"] = $dial_method; #Desired value for user (required)
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
}

// LEAD FILTER
if ($leadfilter != NULL) {
	// collect new user data.		
	$name = NULL; if (isset($_POST["name"])) { 
		$name = $_POST["name"]; 
		$name = stripslashes($name);
	}
    
	$url = gourl."/goLeadFilters/goAPI.php"; # URL to GoAutoDial API file
	$postfields["goUser"] = goUser; #Username goes here. (required)
	$postfields["goPass"] = goPass; #Password goes here. (required)
	$postfields["goAction"] = "goEditLeadFilter"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] = responsetype; #json (required)
	$postfields["lead_filter_id"] = $leadfilter;
	$postfields["lead_filter_name"] = $name;
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

    //var_dump($output);

    if ($output->result=="success") {
    # Result was OK!
        ob_clean();
		print (CRM_DEFAULT_SUCCESS_RESPONSE);
    } else {
    # An error occured
        ob_clean();
		print $output->result;
		//print $output->count;
        //$lh->translateText("unable_modify_list");
    }
    
}
?>