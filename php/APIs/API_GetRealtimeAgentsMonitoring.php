<?php
/*
 *  Copyright (c) 2018 GOautodial Inc. All Rights Reserved.
 *
 *  Use of this source code is governed by the aGPLv3 license
 *  that can be found in the LICENSE file in the root of the source
 *  tree.
*/

    //initialize session and DDBB handler
    include_once('../UIHandler.php');
    require_once('../LanguageHandler.php');
    require_once('../DbHandler.php');
    require_once('../Session.php');    
    require_once('../goCRMAPISettings.php');

    $ui = \creamy\UIHandler::getInstance();
    $lh = \creamy\LanguageHandler::getInstance();
    $url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)

	$postfields = array(
		'goUser' => goUser,
		'goPass' => goPass,
		'goAction' => 'goGetRealtimeAgentsMonitoring',
		'session_user' => $_SESSION['user'],
		'responsetype' => 'json',
	);				

	// Call the API
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
	$data = curl_exec($ch);
	curl_close($ch);

    $output = json_decode($data);
    $barracks = '[';   
    
    foreach ($output->data as $key => $value) {
   
        $userid = $ui->escapeJsonString($value->vu_user_id);
        $agentid = $ui->escapeJsonString($value->vla_user);
        $agentname = $ui->escapeJsonString($value->vu_full_name);
        $campname = $ui->escapeJsonString($value->vla_campaign_name);    
        $station = $ui->escapeJsonString($value->vla_extension);
        $user_group = $ui->escapeJsonString($value->vu_user_group);
        $sessionid = $ui->escapeJsonString($value->vla_conf_exten);
        $status = $ui->escapeJsonString($value->vla_status);
        $call_type = $ui->escapeJsonString($value->vla_comments);
        $server_ip = $ui->escapeJsonString($value->vla_server_ip);
        $call_server_ip = $ui->escapeJsonString($value->vla_call_server_ip);
        $last_call_time = $ui->escapeJsonString($value->last_call_time);
        $last_call_finish = $ui->escapeJsonString($value->last_call_finish);
        $campaign_id = $ui->escapeJsonString($value->vla_campaign_id);
        $last_state_change = $ui->escapeJsonString($value->last_state_change);
        $lead_id = $ui->escapeJsonString($value->vla_lead_id);
        $agent_log_id = $ui->escapeJsonString($value->vla_agent_log_id);
        $vla_callerid = $ui->escapeJsonString($value->vla_callerid);    
        $cust_phone = $ui->escapeJsonString($value->vl_phone_number);
        $pausecode = $ui->escapeJsonString($value->vla_pausecode);
		
        foreach ($output->callerids as $key => $callerids) {
        
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
		$STARTtime = date("U");       
		$sessionAvatar = "<div class='media'><avatar username='$agentname' :size='32'></avatar></div>";

		if ($status == "INCALL") {
			$textclass = "text-success";        
			
			if ($pc_channel != NULL) {            
				if ($vla_callerid != $vac_callerid) { $last_state_change = $last_call_time; $status = "HUNGUP"; }                    
				if ($call_type == "AUTO") { $CM=" [A]"; }            
				if ($call_type == "INBOUND") { $CM=" [I]"; }            
				if ($call_type == "MANUAL") { $CM=" [M]"; }                        
			}  
		}
		
		if (preg_match("/READY|PAUSED|CLOSER/",$status)){
			$last_call_time = $last_state_change;
			$textclass = "text-info";
			
			if ($lead_id>0) { $status="DISPO"; }
		}
			
		if (!preg_match("/INCALL|QUEUE|PARK|3-WAY/",$status)){
			$call_time_S = ($STARTtime - $last_state_change);
			$textclass = "text-info";
						
			if ($call_time_M_int >= 3) { $textclass = "text-warning"; }                
			if ($call_time_M_int >= 5) { $textclass = "text-danger"; }
				
		} else { $call_time_S = ($STARTtime - $last_call_time); }
		
		if (preg_match("/3-WAY/",$status)) {
			$call_time_S = ($STARTtime - $last_state_change);
			$textclass = "text-success";
		}
	
		$call_time_S = ($STARTtime - $last_state_change);
		$call_time_M = ($call_time_S / 60);
		$call_time_M = round($call_time_M, 2);
		$call_time_M_int = intval("$call_time_M");
		$call_time_SEC = ($call_time_M - $call_time_M_int);
		$call_time_SEC = ($call_time_SEC * 60);
		$call_time_SEC = round($call_time_SEC, 0);
			
		if ($call_time_SEC < 10) {
			$call_time_SEC = "0$call_time_SEC";
		}
		
		$call_time_MS = "$call_time_M_int:$call_time_SEC";
		
		if ($status == "PAUSED") {
			$circleclass = "circle circle-warning circle-lg text-left";
			$textclass = "text-warning";
			$nametextclass = "text-warning";
			
			if (strlen($pausecode) > 0) { $status .= " [$pausecode]"; }
			
			if ($call_time_S >= 10){ $textclass = "text-warning"; }
			if ($call_time_M_int >= 1){ $textclass = "text-warning"; }
			if ($call_time_M_int >= 5){ $textclass = "text-danger"; }
			if ($call_time_M_int >= 15){ $textclass = "text"; }
		}
		
		if ($status == "READY"){                
			$textclass = "text-info";
				
			if ($call_time_M_int >= 3){ $textclass = "text-warning"; }            
			if ($call_time_M_int >= 5){ $textclass = "text-danger"; }            
		}  
			
		if ($status == "DISPO"){                
			$textclass = "text-warning";
			
			if ($call_time_M_int >= 3){ $textclass = "text-danger"; }            
			if ($call_time_M_int >= 5){ $textclass = "text"; }            
		}         
		
		if ($status == "HUNGUP"){ $textclass = "text-danger"; }
	
		$barracks .='[';
		$barracks .= '"'.$sessionAvatar.'",';
		$barracks .= '"<a id=\"onclick-userinfo\" data-toggle=\"modal\" data-target=\"#view_agent_information\" data-id=\"'.$agentid.'\" class=\"text-blue\"><strong>'.$agentname.'</strong></a>",'; 
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
