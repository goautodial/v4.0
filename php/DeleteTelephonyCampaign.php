<?php

require_once('CRMDefaults.php');
require_once('LanguageHandler.php');
//require_once('DbHandler.php');
require_once('goCRMAPISettings.php');

$lh = \creamy\LanguageHandler::getInstance();

$campaign = NULL;
if (isset($_POST["campaign"])) {
    $campaign = $_POST["campaign"];
}

$disposition = NULL;
if (isset($_POST["disposition"])) {
    $disposition = $_POST["disposition"];
}

$leadfilter = NULL;
if (isset($_POST["leadfilter"])) {
    $leadfilter = $_POST["leadfilter"];
}

// IF CAMPAIGN IS DELETED
if ($campaign != NULL) {
/*
 * Deleting Campaign
 * [[API:Function]] – goDeleteCampaign
 * This application is used to delete a campaign.
 */

    $url = gourl."/goAPI/goCampaigns/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goDeleteCampaign"; #action performed by the [[API:Functions]]. (required)
    $postfields["responsetype"] = responsetype; #json. (required)
    $postfields["campaign_id"] = $campaign; #Desired User ID. (required)
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
        ob_clean();
        print CRM_DEFAULT_SUCCESS_RESPONSE;
    } else {
        ob_clean(); 
        $lh->translateText("unable_delete_campaign");
    }

} else {
    ob_clean(); $lh->translateText("some_fields_missing");
}

// IF DISPOSITION IS DELETED
if ($disposition != NULL) {
/*
 * Deleting Disposition
 * [[API:Function]] – goDeleteDisposition
 * This application is used to delete a disposition. Only disposition that belongs to authenticated user can be delete.
*/
    $url = gourl."/goInbound/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goDeleteDisposition"; #action performed by the [[API:Functions]]. (required)
    $postfields["responsetype"] = responsetype; #json. (required)
    $postfields["campaign_id"] = $disposition; #Desired campaign id. (required)
    $postfields["statuses"] = ""; #Desired campaign status. (required)
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
		print CRM_DEFAULT_SUCCESS_RESPONSE;
    } else {
		ob_clean(); 
		$lh->translateText("unable_delete_ivr");
    }

} else {
	ob_clean(); $lh->translateText("some_fields_missing");
}

// IF LEAD FILTER IS DELETED
if ($leadfilter != NULL) {
/*
 * Deleting Lead Filter
 * [[API:Function]] – goDeleteLeadFilter
 * This application is used to delete a lead filter. Only lead filter that belongs to authenticated user can be delete.
*/
    $url = gourl."/goLeadFilters/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goDeleteLeadFilter"; #action performed by the [[API:Functions]]. (required)
    $postfields["responsetype"] = responsetype; #json. (required)
    $postfields["lead_filter_id"] = $leadfilter; #Desired User ID. (required)
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
        print CRM_DEFAULT_SUCCESS_RESPONSE;
    } else {
        ob_clean(); 
        $lh->translateText("unable_delete_leadfilter");
    }

} else {
    ob_clean(); $lh->translateText("some_fields_missing");
}

?>