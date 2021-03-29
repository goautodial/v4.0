<?php
/**
 * @file        API_getRealtimeInboundMonitoring.php
 * @brief       Displays realtime monitoring data and HTML
 * @copyright   Copyright (c) 2020 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author      Christopher P. Lomuntad
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
	
	require_once('APIHandler.php');
	
    $ingroup 										= $_REQUEST['ingroup'];
	
	$api 											= \creamy\APIHandler::getInstance();
	$output 										= $api->API_getRealtimeInboundMonitoring($ingroup);

    $barracks 										= '[';   
    
    if (is_array($output->data)) {
        $calls_queue                                = (array) $output->calls_in_queue;
        
		foreach ($output->data as $key => $value) {
	
			$userid 								= $api->escapeJsonString($value->vu_user_id);
			$agentid 								= $api->escapeJsonString($value->vla_user);
			$agentname 								= $api->escapeJsonString($value->vu_full_name);
			$campname 								= $api->escapeJsonString($value->vla_campaign_name);    
			$station 								= $api->escapeJsonString($value->vla_extension);
			$user_group 							= $api->escapeJsonString($value->vu_user_group);
			$closer_campaigns						= $api->escapeJsonString($value->vla_closer_campaigns);
			$sessionid 								= $api->escapeJsonString($value->vla_conf_exten);
			$status 								= $api->escapeJsonString($value->vla_status);
			$call_type 								= $api->escapeJsonString($value->vla_comments);
			$server_ip 								= $api->escapeJsonString($value->vla_server_ip);
			$call_server_ip 						= $api->escapeJsonString($value->vla_call_server_ip);
			$last_call_time 						= $api->escapeJsonString($value->last_call_time);
			$last_update_time 						= $api->escapeJsonString($value->last_update_time);
			$last_call_finish 						= $api->escapeJsonString($value->last_call_finish);
			$campaign_id 							= $api->escapeJsonString($value->vla_campaign_id);
			$last_state_change 						= (!isset($value->last_state_change)) ? $last_call_finish : $api->escapeJsonString($value->last_state_change);
			$lead_id 								= $api->escapeJsonString($value->vla_lead_id);
			$agent_log_id 							= $api->escapeJsonString($value->vla_agent_log_id);
			$vla_callerid 							= $api->escapeJsonString($value->vla_callerid);
			$cust_phone 							= (!isset($value->vl_phone_number)) ? "" : $api->escapeJsonString($value->vl_phone_number);
			$pausecode 								= $api->escapeJsonString($value->vla_pausecode);
			//$vla_conference						= $api->escapeJsonString($value->vla_conf_exten);
			//$ol_conference							= (!isset($value->ol_conference)) ? "" : $api->escapeJsonString($value->ol_conference);
			//$ol_callerid							= (!isset($value->ol_callerid)) ? "" : $api->escapeJsonString($value->ol_callerid);
			
			if (!empty($output->callerids)) {
				foreach ($output->callerids as $key => $callerids) {
				
					$vac_callerid 					= $api->escapeJsonString($callerids->vac_callerid);
					$vac_lead_id 					= $api->escapeJsonString($callerids->vac_lead_id);
					$vac_phone_number 				= $api->escapeJsonString($callerids->vac_phone_number);
				}			
			}
			
			if (!empty($output->parked)) {
				foreach ($output->parked as $key => $parked){
				
					$pc_channel 					= $parked->pc_channel;
					$pc_channel_group 				= $parked->pc_channel_group;
					$pc_extension 					= $parked->pc_extension;
					$pc_parked_by 					= $parked->pc_parked_by;
					$pc_parked_time 				= $parked->pc_parked_time;
				}
			}
			
			$CM 									= "";        
			$STARTtime 								= date("U");       
			$sessionAvatar 							= "<div class='media'><avatar username='$agentname' :size='32'></avatar></div>";
			
			$call_time_S 							= ($STARTtime - $last_state_change);
			$call_time_M 							= ($call_time_S / 60);
			$call_time_M 							= round($call_time_M, 2);
			$call_time_M_int 						= intval("$call_time_M");
			$call_time_SEC 							= ($call_time_M - $call_time_M_int);
			$call_time_SEC 							= ($call_time_SEC * 60);
			$call_time_SEC 							= round($call_time_SEC, 0);
			
			if ($status == "INCALL") {
				$textclass 							= "text-success";        
				
				if ($pc_channel != NULL) {
					$status 						= "PARK"; 
				}
				
				if ($call_type == "AUTO") {
					$CM								= " [A]"; 
				}
				
				if ($call_type == "INBOUND") {
					$CM								= " [I]"; 
				}            
				
				if ($call_type == "MANUAL") {
					$CM								= " [M]"; 
				}
				
				//if (($vla_callerid != $vac_callerid) && ($last_state_change != $last_call_time)) {
				if (($vla_callerid != $ol_callerid) && ($last_state_change != $last_call_time)) {					
					$status 						= "DEAD"; 
				}				
			}
			
			if (preg_match("/READY|PAUSED|CLOSER/",$status)){
				$last_call_time 					= $last_state_change;
				$textclass 							= "text-info";
				
				if ($lead_id>0) { 
					$status 						= "DISPO"; 
				}
			}
				
			if (!preg_match("/INCALL|QUEUE|PARK|3-WAY/",$status)){
				$call_time_S 						= ($STARTtime - $last_state_change);
				$textclass 							= "text-info";
							
				if ($call_time_M_int >= 3) { 
					$textclass 						= "text-warning"; 
				}
				
				if ($call_time_M_int >= 5) { 
					$textclass 						= "text-danger"; 
				}
					
			} else { 
				$call_time_S 						= ($STARTtime - $last_call_time); 
			}
			
			if (preg_match("/3-WAY/",$status)) {
				$call_time_S 						= ($STARTtime - $last_state_change);
				$textclass 							= "text-success";
			}
				
			if ($call_time_SEC < 10) { 
				$call_time_SEC 						= "0$call_time_SEC"; 
			}
			
			$call_time_MS 							= "$call_time_M_int:$call_time_SEC";
			$statustxt								= $status;
            
            $closer_campaigns                       = trim(substr($closer_campaigns, 0, -1));
			$closer_campaigns						= explode(" ", $closer_campaigns);
			$ingroup_exists							= true;
			if (isset($ingroup)) {
				$ingroup_exists						= (preg_grep ("/$ingroup/i", $closer_campaigns) ? true : false);
			}
            $closer_campaigns                       = implode(", ", $closer_campaigns);
            
            $calls_in_queue                         = 0;
            if (is_array($calls_queue) && isset($calls_queue[$agentid])) {
                $calls_in_queue = $calls_queue[$agentid];
            }
			
			switch ($status) {
				case "PAUSED":
					$circleclass 					= "circle circle-warning circle-lg text-left";
					$textclass 						= "text-warning";
					$nametextclass 					= "text-warning";
					
					if (strlen($pausecode) > 0) { 
						$CM 						= " [$pausecode]"; 
					}
					
					if ($call_time_S >= 10){ 
						$textclass 					= "text-warning";
					}
					
					if ($call_time_M_int >= 1) { 
						$textclass 					= "text-warning"; 
					}
					
					if ($call_time_M_int >= 5) { 
						$textclass 					= "text-danger"; 
					}
					
					if ($call_time_M_int >= 15) { 
						$textclass 					= "text"; 
					}
					
				break;
				
				case "READY":
					$textclass 						= "text-info";
						
					if ($call_time_M_int >= 3) { 
						$textclass 					= "text-warning"; 
					}
					
					if ($call_time_M_int >= 5) { 
						$textclass 					= "text-danger"; 
					}
					
				break;
				
				case "DISPO":
					$textclass 						= "text-warning";
					
					if ($call_time_M_int >= 3) { 
						$textclass 					= "text-danger"; 
					}
					
					if ($call_time_M_int >= 5) { 
						$textclass 					= "text"; 
					} 	
					
				break;
				
				case "DEAD":
					$textclass 						= "text-danger";
					$statustxt 						= "HUNGUP";
					
				break;
			}

            if (!empty($closer_campaigns) && $ingroup_exists) {
                $barracks 								.= '[';
                $barracks 								.= '"'.$sessionAvatar.'",';
                $barracks 								.= '"<a id=\"onclick-userinfo\" data-toggle=\"modal\" data-target=\"#modal_view_agent_information\" data-id=\"'.$userid.'\" data-user=\"'.$agentid.'\" class=\"text-blue\"><strong>'.$agentname.'</strong></a>",'; 
                $barracks 								.= '"'.$closer_campaigns.'",';    
                $barracks 								.= '"<b class=\"'.$textclass.'\">'.$statustxt.''.$CM.'</b>",';    
                $barracks 								.= '"'.$calls_in_queue.'",';
                $barracks 								.= '"<b class=\"'.$textclass.'\">'.$call_time_MS.'</b>",';
                $barracks 								.= '"'.$campname.'",';
                $barracks 								.= '"'.$cust_phone.'"';         
                $barracks 								.= '],';
            }
		}
		
		$barracks 									= rtrim($barracks, ","); 	
    }
    
    $barracks 										.= ']';
	echo json_encode($barracks);
    
?>
