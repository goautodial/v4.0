<?php
    ####################################################
    #### Name: goGetAgentsMonitoringSummary.php     ####
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
    $postfields["goAction"] = "goGetAgentsMonitoringSummary"; #action performed by the [[API:Functions]]
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
    
    if ($output->data.length == 0){
    
    echo '<a href="#" class="list-group-item">
            <div class="media-box">
                    <div class="pull-left">
                        <img src="'.$sessionAvatar.'" alt="Image" class="media-box-object img-circle thumb32">
                    </div>
                    <div class="media-box-body clearfix">
                        <strong class="media-box-heading text-primary">
                        <span class="circle circle-danger circle-lg text-left"></span>There are no available agents.</strong><br/>
                        <strong class=""style="padding-left:20px;">Showing sample data.</strong>
                    </div>
                </div>
            </a>            
            <a href="#" class="list-group-item">
                <div class="media-box">
                    <div class="pull-left">
                        <img src="theme_dashboard/img/user/03.jpg" alt="Image" class="media-box-object img-circle thumb32">
                    </div>
                    <div class="media-box-body clearfix">
                        <strong class="media-box-heading text-primary">
                        <span class="circle circle-success circle-lg text-left"></span>Jackie "Baby boy" Alfonso</strong><br/>
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
                        <span class="circle circle-danger circle-lg text-left"></span>Kim Takahashi</strong><br/>
                        <strong class=""style="padding-left:20px;">CS HOTLINE</strong>
                        <small class="text-muted pull-right ml" style="padding-right:20px;">1:49</small>
                    </div>
                </div>
            </a>
            <!-- END list group item-->            <a href="#" class="list-group-item">
                <div class="media-box">
                    <div class="pull-left">
                        <img src="theme_dashboard/img/user/09.jpg" alt="Image" class="media-box-object img-circle thumb32">
                    </div>
                    <div class="media-box-body clearfix">
                        <strong class="media-box-heading text-primary">
                        <span class="circle circle-danger circle-lg text-left"></span>Kim Takahashi</strong><br/>
                        <strong class=""style="padding-left:20px;">CS HOTLINE</strong>
                        <small class="text-muted pull-right ml" style="padding-right:20px;">1:49</small>
                    </div>
                </div>
            </a>
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

     if ($output->data.length == 0){
     
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
        $G = "";		
        $EG = "";
            
        echo    '<a class="list-group-item">
                    <div class="media-box">
                        <div class="pull-left">
                            <img src="'.$sessionAvatar.'" alt="Image" class="media-box-object img-circle thumb32">
                        </div>            
                        <div class="media-box-body clearfix">
                            <strong class="media-box-heading text-primary">
                            <b id="onclick-userinfo" data-toggle="modal" data-target="#view_agent_information" data-id="'.$userid.'"><span class="'.$class.'"></span>'.$agentname.'</b>
                            </strong><br/>
                            <strong style="padding-left:20px;">'.$campname.'</strong>
                            <small class="text-muted pull-right ml" style="padding-right:20px;">'.$call_time_MS.'</small>
                        </div>
                    </div>
                </a>'; 

        }
    }
?>
