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
	$postfields["session_user"] = $_SESSION['user']; #current user

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
    
    $max = 0;

    $agentlatestcalls = '['; 
    
    foreach($output->agentoutcalls as $key => $value){
    
        if(++$max > 100) break;

        $first_name = $value->first_name;
        $last_name = $value->last_name;                
        $phone_number = $value->phone_number;
        $lead_id = $value->lead_id;
        $list_id = $value->list_id;
        $campaign_id = $value->campaign_id;
        $call_date = $value->call_date;
        $duration = $value->length_in_sec;
        $statusout = $value->status;
        $called_count = $value->called_count;
        $fullname = "$first_name $last_name";
        
        $textclass = "text-blue";
        $sessionAvatar = "<div class='media'><avatar username='$fullname' :size='32'></avatar></div>";
        
        $agentlatestcalls .='[';
        $agentlatestcalls .= '"'.$sessionAvatar.'",';
        //$agentlatestcalls .= '"<b class=\"'.$textclass.'\">'.$lead_id.'</b>",';
        $agentlatestcalls .= '"<a id=\"onclick-leadinfo\" data-toggle=\"modal\" data-target=\"#view_lead_information\" data-id=\"'.$lead_id.'\" class=\"text-blue\"><strong>'.$lead_id.'</strong></a>",';
        $agentlatestcalls .= '"'.$fullname.'",';
        //$agentlatestcalls .= '"'.$list_id.'",'; 
        $agentlatestcalls .= '"'.$campaign_id.'",';      
        $agentlatestcalls .= '"'.$phone_number.'",';      
        $agentlatestcalls .= '"'.$statusout.'",';
        $agentlatestcalls .= '"'.$call_date.'",';      
        $agentlatestcalls .= '"'.$duration.'"';        
        $agentlatestcalls .='],';
       
    }

    $agentlatestcalls = rtrim($agentlatestcalls, ",");    
    $agentlatestcalls .= ']';

    echo json_encode($agentlatestcalls);    

?>
