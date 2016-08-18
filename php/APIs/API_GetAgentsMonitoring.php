<?php

 ####################################################
 #### Name: goGetAgentsMonitoring.php            ####
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
$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
$postfields["goUser"] = goUser; #Username goes here. (required)
$postfields["goPass"] = goPass;
$postfields["goAction"] = "goGetAgentsMonitoring"; #action performed by the [[API:Functions]]
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

$sessionAvatar = $ui->getSessionAvatar();

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
   
    $userid = $value->user_id;
    $agentname =  $value->agent_full_name;
    $campname = $value->campaign;    
    $station = $value->station;
    $user_group = $value->tenant_id;
    $sessionid = $value->sessionid;
    $status = $value->status;
    $call_type = $value->comments;
    $server_ip = $value->server_ip;
    $call_server_ip = $value->call_server_ip;
    $last_call_time = $value->last_call_time;
    $last_call_finish = $value->last_call_finish;
    $campaign_id = $value->campaign;
    $last_state_change = $value->last_state_change;
    $lead_id = $value->lead_id;
    $agent_log_id = $value->agent_log_id;
    $caller_id = $value->callerid;
    $cust_phone = "";
    $pausecode = "";   
        
    $STARTtime = date("U");

    if (preg_match("/READY|CLOSER/",$status)){
        $last_call_time=$last_state_change;
        $class = "text-warning m0";
        if ($lead_id>0){ 
            $status="DISPO";
        }
    }
    if (preg_match("/PAUSED/",$status)){
        $class = "text-danger m0";
        if ($lead_id>0){ 
            $status="DISPO";
        }
    }
    if (!preg_match("/INCALL|QUEUE|PARK|3-WAY/",$status)){
        $call_time_S = ($STARTtime - $last_state_change);
        
    }
    else if (preg_match("/3-WAY/",$status)){
        $call_time_S = ($STARTtime - $call_mostrecent);
    }
    else{
        $call_time_S = ($STARTtime - $last_call_time);
        $class = "text-success m0";
    }

    $call_time_M = ($call_time_S / 60);
    $call_time_M = round($call_time_M, 2);
    $call_time_M_int = intval("$call_time_M");
    $call_time_SEC = ($call_time_M - $call_time_M_int);
    $call_time_SEC = ($call_time_SEC * 60);
    $call_time_SEC = round($call_time_SEC, 0);
    if ($call_time_SEC < 10) {$call_time_SEC = "0$call_time_SEC";}
    $call_time_MS = "$call_time_M_int:$call_time_SEC";
    $G = '';
    $EG = '';

    $barracks .='[';
    $barracks .= '"'.$agentname.'",';
    $barracks .= '"'.$user_group.'",';
    $barracks .= '"'.$status.'",';
    $barracks .= '"'.$cust_phone.'",';
    $barracks .= '"'.$call_time_MS.'",';
    $barracks .= '"'.$campname.'"';
    $barracks .='],';
 
 
 
 
    /*echo '<tr><th><div class="col-sm-12 pull-left">
            <div class="pull-left">
                <img src="'.$sessionAvatar.'" alt="Image" class="media-box-object img-circle thumb32">
            </div>
            <span id="modal-realtime-agent" class="control-label"></span>
            <a data-toggle="modal" class="'.$class.'">'.$agentname.'</a>
        </div></th>
        <th><div class="col-sm-12">
            <span id="modal-realtime-usergroup" class="control-label"></span>        
            <a data-toggle="modal" class="'.$class.'">'.$user_group.'</a>
        </div></th>        
        <th><div class="col-sm-12">
            <span id="modal-realtime-status" class="control-label"></span>        
            <a data-toggle="modal" class="'.$class.'">'.$status.'</a>
        </div></th>
        <th><div class="col-sm-12">
            <span id="modal-realtime-custphone" class="control-label"></span>        
            <a data-toggle="modal" class="'.$class.'">'.$cust_phone.'</a>
        </div></th>
        <th><div class="col-sm-12">
            <span id="modal-realtime-calltime" class="control-label"></span>        
            <a data-toggle="modal" class="'.$class.'">'.$call_time_MS.'</a>
        </div></th>        
        <th><div class="col-sm-12">
            <span id="modal-realtime-campname" class="control-label"></span>        
            <a data-toggle="modal" class="text">'.$campname.'</a>
        </div></th></tr>'; */
}

    $barracks = rtrim($barracks, ",");
    $barracks .= ']';
    echo json_encode($barracks);
?>
