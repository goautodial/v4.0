<?php

####################################################
#### Name: goGetHopperLeadsWarning.php          ####
#### Type: API for dashboard php encode         ####
#### Version: 0.9                               ####
#### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
#### Written by: Demian Lizandro Biscocho       ####
#### License: AGPLv2                            ####
####################################################

require_once('../goCRMAPISettings.php');
$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
$postfields["goUser"] = goUser; #Username goes here. (required)
$postfields["goPass"] = goPass;
$postfields["goAction"] = "goGetOnlineAgents"; #action performed by the [[API:Functions]]
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

if ($output == NULL){
    echo '<strong class="media-box-heading text-primary">
            <span class="circle circle-danger circle-lg text-left"></span>There are no available agents.
            </strong>
            <br/>
            <strong class=""style="padding-left:20px;"></strong>
            <small class="text-muted pull-right ml" style="padding-right:20px;"></small>
            </p>';
    echo 'Showing sample data:<br>
            <a href="#" class="list-group-item">
            <div class="media-box">
            <div class="pull-left">
            <img src="theme_dashboard/img/user/03.jpg" alt="Image" class="media-box-object img-circle thumb32">
            </div>
            <div class="media-box-body clearfix">
            <strong class="media-box-heading text-primary">
            <span class="circle circle-success circle-lg text-left"></span>Jackie "Baby boy" Alfonso</strong>
            <br/>
            <strong class=""style="padding-left:20px;">CS HOTLINE</strong>
            <small class="text-muted pull-right ml" style="padding-right:20px;">1:49</small>
            </div>
            </div>
            </a>
            <!-- END list group item-->
            <!-- START list group item-->
            <a href="#" class="list-group-item">
            <div class="media-box">
            <div class="pull-left">
            <img src="theme_dashboard/img/user/09.jpg" alt="Image" class="media-box-object img-circle thumb32">
            </div>
            <div class="media-box-body clearfix">
            <strong class="media-box-heading text-primary">
            <span class="circle circle-danger circle-lg text-left"></span>Kim Takahashi</strong>
            <br/>
            <strong class=""style="padding-left:20px;">CS HOTLINE</strong>
            <small class="text-muted pull-right ml" style="padding-right:20px;">1:49</small>
            </div>
            </div>
            </a>
            <!-- END list group item-->
            <!-- START list group item-->
            <a href="#" class="list-group-item">
            <div class="media-box">
            <div class="pull-left">
            <img src="theme_dashboard/img/user/12.jpg" alt="Image" class="media-box-object img-circle thumb32">
            </div>
            <div class="media-box-body clearfix">
            <strong class="media-box-heading text-primary">
            <span class="circle circle-danger circle-lg text-left"></span>Khristel Tonolete</strong>
            <br/>
            <strong class=""style="padding-left:20px;">CS HOTLINE</strong>
            <small class="text-muted pull-right ml" style="padding-right:20px;">1:49</small>
            </div>
            </div>
            </a>
            <!-- END list group item-->
            <!-- START list group item-->
            <a href="#" class="list-group-item">
            <div class="media-box">
            <div class="pull-left">
            <img src="theme_dashboard/img/user/10.jpg" alt="Image" class="media-box-object img-circle thumb32">
            </div>
            <div class="media-box-body clearfix">
            <strong class="media-box-heading text-primary">
            <span class="circle circle-danger circle-lg text-left"></span>Andrew Gwaltney</strong>
            <br/>
            <strong class=""style="padding-left:20px;">CS HOTLINE</strong>
            <small class="text-muted pull-right ml" style="padding-right:20px;">1:49</small>
            </div>
            </div>
            </a>
            <!-- END list group item-->
            </div>';
}
foreach ($output->data as $key => $value) {
   
    $campname = $value->campaign;
    $status = $value->status;
    $userid = $value->user_id;
    $agentname =  $value->agent_full_name;
    $last_call_time = $value->last_call_time;
    $last_state_change = $value->last_state_change;
    $lead_id = $value->lead_id;
    $STARTtime = date("U");

    if (preg_match("/READY|CLOSER/",$status)){
        $last_call_time=$last_state_change;
        $class = "circle circle-warning circle-lg text-left";
        if ($lead_id>0){ 
            $status="DISPO";
        }
    }
    if (preg_match("/PAUSED/",$status)){
        $class = "circle circle-danger circle-lg text-left";
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
        $class = "circle circle-success circle-lg text-left";
    }

    $call_time_M = ($call_time_S / 60);
    $call_time_M = round($call_time_M, 2);
    $call_time_M_int = intval("$call_time_M");
    $call_time_SEC = ($call_time_M - $call_time_M_int);
    $call_time_SEC = ($call_time_SEC * 60);
    $call_time_SEC = round($call_time_SEC, 0);
    if ($call_time_SEC < 10) {$call_time_SEC = "0$call_time_SEC";}
    $call_time_MS = "$call_time_M_int:$call_time_SEC";
    $G = '';		$EG = '';

    echo '<strong class="media-box-heading text-primary">
            <span class="'.$class.'"></span><a id="onclick-userinfo" data-toggle="modal" data-target="#view-agent-modal" data-id="'.$userid.'" class="text-danger m0">'.$agentname.'</a>
            </strong>
            <br/>
            <strong class=""style="padding-left:20px;"><a id="onclick-campaigninfo" data-toggle="modal" data-target="#view-campaign-modal" data-id="'.$campname.'" class="text">'.$campname.'</a></strong>
            <small class="text-muted pull-right ml" style="padding-right:20px;">'.$call_time_MS.'</small>
            </p>'; 

}

?>
