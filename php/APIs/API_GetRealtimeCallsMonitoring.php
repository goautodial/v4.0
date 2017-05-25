<?php
    ####################################################
    #### Name: goGetRealtimeCallsMonitoring.php     ####
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
    $postfields["goAction"] = "goGetRealtimeCallsMonitoring"; #action performed by the [[API:Functions]]
    $postfields["responsetype"] = responsetype; 
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

    $barracks = '[';

    foreach ($output->data as $key => $value) {

        $campname = $value->campaign_id;    
        $status = $value->status;
        $call_type = $value->call_type;
        $call_time = $value->call_time;
        $server_ip = $value->call_server_ip;
        $last_call_time = $value->last_call_time;
        $last_call_finish = $value->last_call_finish;
        $last_state_change = $value->last_state_change;
        $lead_id = $value->lead_id;
        $callerid = $value->callerid;
        $cust_phone = $value->phone_number;
        
        $STARTtime = date("U");
        $textclass = "text-info";
        
        $sessionAvatar = "<div class='media'><avatar username='$calltype' :size='32'></avatar></div>";

        //$call_time_S = ($STARTtime - $last_call_time);         
        $call_time_S = ($STARTtime - $call_time);
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

        if ($call_type == "IN"){
                $calltype = "INBOUND";

        }else{
                $calltype = "OUTBOUND";
        }    
            
        $barracks .='[';       
        $barracks .= '"'.$sessionAvatar.'",';
        $barracks .= '"<b class=\"text-blue\">'.$status.'</b>",'; 
        $barracks .= '"'.$cust_phone.'",';    
        $barracks .= '"<b class=\"'.$textclass.'\">'.$calltype.'</b>",';                   
        $barracks .= '"'.$campname.'",';
        $barracks .= '"'.$call_time_MS.'"';
        //$barracks .= '"'.$user_group.'"';     
        $barracks .='],';
 
    }

    $barracks = rtrim($barracks, ",");    
    $barracks .= ']';
    
    echo json_encode($barracks);

?>
