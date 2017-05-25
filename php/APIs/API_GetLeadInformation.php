<?php
    ####################################################
    #### Name: GetLeadInformation.php               ####
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

    $url = gourl."/goGetLeads/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goGetLeadsInfo"; #action performed by the [[API:Functions]]. (required)
    $postfields["responsetype"] = responsetype; #json. (required)
    $postfields["lead_id"] = $_REQUEST['lead_id']; #Desired exten ID. (required)
    $postfields["log_user"] = $_REQUEST['log_user'];
    $postfields["log_group"] = $_REQUEST['log_group'];
    $postfields["log_ip"] = $_SERVER['REMOTE_ADDR'];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $data = curl_exec($ch);
    curl_close($ch);

    $output = json_decode($data);
    //echo "<pre>";
    //print_r($output);
    //var_dump($output);
    //die("dd");
    
    $leadinformation = '[';

    $lead_id = $output->data->lead_id;
    $list_id = $output->data->list_id;
    $campaign_id = $output->data->campaign_id;
    $first_name = $output->data->first_name;
    $last_name = $output->data->last_name;
    $phone_number = $output->data->phone_number;
    $address1 = $output->data->address1;
    $address2 = $output->data->address2;
    $city = $output->data->city;
    $state = $output->data->state;
    $postal_code = $output->data->postal_code;
    $country_code = $output->country_code;
    $status = $output->data->status;
    $user = $output->data->user;

    $leadinformation .='[';       
    $leadinformation .= '"'.$lead_id.'",';       
    $leadinformation .= '"'.$list_id.'",';   
    $leadinformation .= '"'.$campaign_id.'",';      
    $leadinformation .= '"'.$phone_number.'",';      
    $leadinformation .= '"'.$status.'",';
    $leadinformation .= '"'.$user.'"';
    $leadinformation .='],';        
        
    $leadinformation = rtrim($leadinformation, ",");    
    $leadinformation .= ']';
    
    echo json_encode($leadinformation);

?>
