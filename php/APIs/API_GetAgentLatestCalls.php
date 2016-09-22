<?php
    ####################################################
    #### Name: GetAgentInformation.php              ####
    #### Type: API for dashboard php encode         ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
    #### Written by: Demian Lizandro Biscocho       ####
    #### License: AGPLv2                            ####
    ####################################################

    // initialize session and DDBB handler
    include_once('../UIHandler.php');
    require_once('../LanguageHandler.php');
    require_once('../DbHandler.php');
    $ui = \creamy\UIHandler::getInstance();
    $lh = \creamy\LanguageHandler::getInstance();
    //$colors = $ui->generateStatisticsColors();

    require_once('../Session.php');
    require_once('../goCRMAPISettings.php');    

    $url = gourl."/goUsers/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goGetUserInfo"; #action performed by the [[API:Functions]]. (required)
    $postfields["responsetype"] = responsetype; #json. (required)
    $postfields["user_id"] = $_REQUEST['user_id']; #User ID (required)

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    $data = curl_exec($ch);
    curl_close($ch);

    $output = json_decode($data);
    //echo "<pre>";
    //print_r($output);
    //var_dump($output);
    
    $max = 0;
            
    foreach($output->agentoutcalls as $key => $value){

        $first_name = $value->first_name;
        $last_name = $value->last_name;                
        $phone_number = $value->phone_number;
        $lead_id = $value->lead_id;
        $list_id = $value->list_id;
        $campaign_id = $value->campaign_id;
        $call_date = $value->call_date;
        $length_in_sec = $value->length_in_sec;
        $statusout = $value->status;
        $called_count = $value->called_count;
                
    }
    
    $max = 0;
    
    foreach($output->agentincalls as $key => $value){
    

        $first_name = $value->first_name;
        $last_name = $value->last_name;
        $phone_number = $value->phone_number;
        $lead_id = $value->lead_id;
        $list_id = $value->list_id;
        $campaign_id = $value->campaign_id;
        $call_date = $value->call_date;
        $length_in_sec = $value->length_in_sec;
        $statusin = $value->status;
                

    }


?>
