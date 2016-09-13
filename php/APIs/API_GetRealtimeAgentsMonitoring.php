<?php
    ####################################################
    #### Name: goGetRealtimeAgentsMonitoring.php    ####
    #### Type: API for dashboard php encode         ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
    #### Written by: Demian Lizandro Biscocho       ####
    #### License: AGPLv2                            ####
    ####################################################

    //initialize session and DDBB handler
    include_once('../UIHandler.php');
    require_once('../LanguageHandler.php');
    require_once('../DbHandler.php');
    $ui = \creamy\UIHandler::getInstance();
    $lh = \creamy\LanguageHandler::getInstance();
    //$colors = $ui->generateStatisticsColors();

    require_once('../Session.php');    
    require_once('../goCRMAPISettings.php');

    $url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass;
    $postfields["goAction"] = "goGetRealtimeAgentsMonitoring"; #action performed by the [[API:Functions]]
    $postfields["responsetype"] = responsetype; 

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
                        
    $creamyAvatar = $ui->getSessionAvatar();

    $barracks = '[';   
    
    foreach ($output->data as $key => $value) {
   
        $userid = $value->vu_user_id;
        $agentid = $value->vla_user;
        $agentname =  $value->vu_full_name;
        $campname = $value->vla_campaign_id;    
        $station = $value->vla_extension;
        $user_group = $value->vu_user_group;
        $sessionid = $value->vla_conf_exten;
        $status = $value->vla_status;
        $call_type = $value->vla_comments;
        $server_ip = $value->vla_server_ip;
        $call_server_ip = $value->vla_call_server_ip;
        $last_call_time = $value->last_call_time;
        $last_call_finish = $value->last_call_finish;
        $campaign_id = $value->vla_campaign_id;
        $last_state_change = $value->last_state_change;
        $lead_id = $value->vla_lead_id;
        $agent_log_id = $value->vla_agent_log_id;
        $vla_callerid = $value->vla_callerid;    
        $cust_phone = $value->vl_phone_number;
        
        foreach ($output->callerids as $key => $callerids){
        
            $vac_callerid = $callerids->vac_callerid;        
            $vac_lead_id = $callerids->vac_lead_id;
            $vac_phone_number = $callerids->vac_phone_number;
        }
        
        foreach ($output->parked as $key => $parked){
        
            $pc_channel = $parked->pc_channel;
            $pc_channel_group = $parked->pc_channel_group;
            $pc_extension = $parked->pc_extension;
            $pc_parked_by = $parked->pc_parked_by;
            $pc_parked_time = $parked->pc_parked_time;
        }
        
    $CM = "";
    
    //$call_time_MS = "";
    $STARTtime = date("U");       
    
    //$sessionAvatar = "<div class='media'><avatar username='$agentname' src='$creamyAvatar' :size='36'></avatar></div>";
    $sessionAvatar = "<div class='media'><avatar username='$agentname' :size='32'></avatar></div>";

    if ($status == "INCALL"){
        //$last_call_time = $last_state_change;
        $textclass = "text-success";        
        
        if ($pc_channel != NULL){
                //$last_call_time = $pc_parked_time;
                $status = "PARK";
        }
    
        if ($vla_callerid != $vac_callerid){
                $last_call_time = $last_state_change;
                $status = "HUNGUP";
        }        
        
        if ($call_type == "AUTO"){
                $CM="[A]";
        }
        
        if ($call_type == "INBOUND"){
                $CM="[I]";
        }
        
        if ($call_type == "MANUAL"){
                $CM="[M]";
        }                        
    }  
    
    if (preg_match("/READY|PAUSED|CLOSER/",$status)){
        $last_call_time = $last_state_change;
        $textclass = "text-info";
        
        if ($lead_id>0){ 
            $status="DISPO";
        }
    }
           
    if (!preg_match("/INCALL|QUEUE|PARK|3-WAY/",$status)){
        $call_time_S = ($STARTtime - $last_state_change);
        $textclass = "text-info";
                    
            if ($call_time_M_int >= 3){
                $textclass = "text-warning";
            }
            
            if ($call_time_M_int >= 5){
                $textclass = "text-danger";
            }        
    } else {
        $call_time_S = ($STARTtime - $last_call_time);         
    }
    
    if (preg_match("/3-WAY/",$status)){
        $call_time_S = ($STARTtime - $last_state_change);
        $textclass = "text-success";
    }

    //$call_time_S = ($STARTtime - $call_time);
    $call_time_S = ($STARTtime - $last_state_change);
    $call_time_M = ($call_time_S / 60);
    $call_time_M = round($call_time_M, 2);
    $call_time_M_int = intval("$call_time_M");
    $call_time_SEC = ($call_time_M - $call_time_M_int);
    $call_time_SEC = ($call_time_SEC * 60);
    $call_time_SEC = round($call_time_SEC, 0);
        
    if ($call_time_SEC < 10){
        $call_time_SEC = "0$call_time_SEC";
    }
    $call_time_MS = "$call_time_M_int:$call_time_SEC"; 
   
    if ($status == "PAUSED"){
        $circleclass = "circle circle-warning circle-lg text-left";
        $textclass = "text-warning";
        $nametextclass = "text-warning";
        
            if ($call_time_S >= 10){
                $textclass = "text-warning";
            }
            if ($call_time_M_int >= 1){
                $textclass = "text-warning";
            }
            if ($call_time_M_int >= 5){
                $textclass = "text-danger";
            }
            if ($call_time_M_int >= 15){
                $textclass = "text";
            }            
        }
    
    if ($status == "READY"){                
            $textclass = "text-info";
            
            if ($call_time_M_int >= 3){
                $textclass = "text-warning";
            }            
            if ($call_time_M_int >= 5){
                $textclass = "text-danger";
            }            
        }  
        
    if ($status == "DISPO"){                
            $textclass = "text-warning";
            
            if ($call_time_M_int >= 3){
                $textclass = "text-danger";
            }            
            if ($call_time_M_int >= 5){
                $textclass = "text";
            }            
        }         
    
    if ($status == "HUNGUP"){
        $textclass = "text-danger";
    }


    $barracks .='[';       
    $barracks .= '"'.$sessionAvatar.'",';
    $barracks .= '"<a href=\"#\" id=\"onclick-userinfo\" data-toggle=\"modal\" data-target=\"#view_agent_information\" data-id=\"'.$agentid.'\" class=\"text-blue\"><strong>'.$agentname.'</strong></a>",'; 
    $barracks .= '"'.$user_group.'",';    
    $barracks .= '"<b class=\"'.$textclass.'\">'.$status.''.$CM.'</b>",';    
    $barracks .= '"'.$cust_phone.'",';         
    $barracks .= '"<b class=\"'.$textclass.'\">'.$call_time_MS.'</b>",';
    $barracks .= '"'.$campname.'"';
    $barracks .='],';
}

    $barracks = rtrim($barracks, ",");    
    $barracks .= ']';
    
    echo json_encode($barracks);
    
?>
