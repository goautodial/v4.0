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
    $sessionAvatar ="<avatar username='$agentname' src='$creamyAvatar' :size='36'></avatar>";
    
    if ($output == NULL){
        echo '<strong class="media-box-heading text-primary">
                <span class="circle circle-danger circle-lg text-left"></span>There are no available agents.
                </strong>
                <br/>
                <strong class=""style="padding-left:20px;"></strong>
                <small class="text-muted pull-right ml" style="padding-right:20px;"></small>
                </p>';

    }

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
    $vac_callerid = $value->vac_callerid;
    $cust_phone = $value->vl_phone_number;
    $pausecode = "";
    $parked_channel = $value->pc_channel;
    $STARTtime = date("U");
    $CM = "";
    $textclass = "text-info";
    
    
    if ($status == "INCALL"){
        $last_call_time = $last_state_change;
        $textclass = "text-success";
        
            if ($call_time_M_int >= 3) {
                $textclass = "text-warning";
            }        
        
            if (!is_null($parked_channel)){
                $status = "PARK";
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
            //if ($vla_callerid != $vac_callerid){
                //$last_call_time=$last_state_change;
                //$status = "HANGUP";
            //}            
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
                    
            if ($call_time_M_int >= 3) {
                $textclass = "text-warning";
            }
            
            if ($call_time_M_int >= 5) {
                $textclass = "text-danger";
            }
        
    }
    
    else if (preg_match("/3-WAY/",$status)){
        $call_time_S = ($STARTtime - $call_mostrecent);
        $textclass = "text-success";
    }

    else {
        $call_time_S = ($STARTtime - $last_call_time);         
    }

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
    $G = "";
    $EG = "";
    
    if ($status=="PAUSED") {
        $circleclass = "circle circle-warning circle-lg text-left";
        $textclass = "text-warning";
        
            if ($call_time_S >= 10) {
                $textclass = "text-warning";
            }
            if ($call_time_M_int >= 1) {
                $textclass = "text-warning";
            }
            if ($call_time_M_int >= 5) {
                $textclass = "text-danger";
            }
            if ($call_time_M_int >= 15) {
                $textclass = "text";
            }            
        }
    
    if ($status=="READY") {                
            $textclass = "text-info";
            
            if ($call_time_M_int >= 3) {
                $textclass = "text-warning";
            }            
            if ($call_time_M_int >= 5) {
                $textclass = "text-danger";
            }            
        }  
        
    if ($status=="DISPO") {                
            $textclass = "text-warning";
            
            if ($call_time_M_int >= 3) {
                $textclass = "text-danger";
            }            
            if ($call_time_M_int >= 5) {
                $textclass = "text";
            }            
        }         
    
    if ( preg_match("DEAD",$status) ) {
        $textclass = "text-danger";
        }
    
    
    $barracks .='[';       
    //$barracks .= '"<img src=\"'.$sessionAvatar.'\" class=\"img-circle thumb48\"> <b class=\"text-blue\">'.$agentname.'</b>",';
    $barracks .= '"'.$sessionAvatar.'",';
    $barracks .= '"'.$agentname.'",'; 
    $barracks .= '"'.$user_group.'",';    
    $barracks .= '"<b class=\"'.$textclass.'\">'.$status.''.$CM.'</b>",';    
    $barracks .= '"'.$cust_phone.'",';    
    $barracks .= '"'.$call_time_MS.'",';    
    $barracks .= '"'.$campname.'"';    
    $barracks .='],';
 
}

    $barracks = rtrim($barracks, ",");    
    $barracks .= ']';
    
    echo json_encode($barracks);
    
    //echo $barracks;
?>
