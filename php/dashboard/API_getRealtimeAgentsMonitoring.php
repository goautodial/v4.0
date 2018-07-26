<?php
/**
 * @file        API_getRealtimeAgentsMonitoring.php
 * @brief       Displays realtime monitoring data and HTML
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho 
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
	
	$api 										= \creamy\APIHandler::getInstance();
	$output 									= $api->API_getRealtimeAgentsMonitoring();

    $barracks 									= '[';   
    
    if ( !empty($output->data) ) {
		foreach ($output->data as $key => $value) {
	
			$userid 								= $api->escapeJsonString($value->vu_user_id);
			$agentid 								= $api->escapeJsonString($value->vla_user);
			$agentname 								= $api->escapeJsonString($value->vu_full_name);
			$campname 								= $api->escapeJsonString($value->vla_campaign_name);    
			$station 								= $api->escapeJsonString($value->vla_extension);
			$user_group 							= $api->escapeJsonString($value->vu_user_group);
			$sessionid 								= $api->escapeJsonString($value->vla_conf_exten);
			$status 								= $api->escapeJsonString($value->vla_status);
			$call_type 								= $api->escapeJsonString($value->vla_comments);
			$server_ip 								= $api->escapeJsonString($value->vla_server_ip);
			$call_server_ip 						= $api->escapeJsonString($value->vla_call_server_ip);
			$last_call_time 						= $api->escapeJsonString($value->last_call_time);
			$last_call_finish 						= $api->escapeJsonString($value->last_call_finish);
			$campaign_id 							= $api->escapeJsonString($value->vla_campaign_id);
			$last_state_change 						= $api->escapeJsonString($value->last_state_change);
			$lead_id 								= $api->escapeJsonString($value->vla_lead_id);
			$agent_log_id 							= $api->escapeJsonString($value->vla_agent_log_id);
			$vla_callerid 							= $api->escapeJsonString($value->vla_callerid);    
			//$cust_phone 							= $api->escapeJsonString($value->vl_phone_number);
			$pausecode 								= $api->escapeJsonString($value->vla_pausecode);
			
			foreach ($output->callerids as $key => $callerids) {
			
				$vac_callerid 						= $api->escapeJsonString($callerids->vac_callerid);        
				$vac_lead_id 						= $api->escapeJsonString($callerids->vac_lead_id);
				$vac_phone_number 					= $api->escapeJsonString($callerids->vac_phone_number);
			}
			
			foreach ($output->parked as $key => $parked){
			
				$pc_channel 						= $parked->pc_channel;
				$pc_channel_group 					= $parked->pc_channel_group;
				$pc_extension 						= $parked->pc_extension;
				$pc_parked_by 						= $parked->pc_parked_by;
				$pc_parked_time 					= $parked->pc_parked_time;
			}
		
			$CM 									= "";        
			$STARTtime 								= date("U");       
			$sessionAvatar 							= "<div class='media'><avatar username='$agentname' :size='32'></avatar></div>";
			
			if ($status == "INCALL") {
				$textclass 							= "text-success";        
				
				if ($pc_channel != NULL) {
					$status 						= "PARK"; 
				}            
				
				if (($vla_callerid != $vac_callerid) && ($last_state_change != $last_call_time)) {
					$status 						= "HUNGUP"; 
				}
				
				if ($call_type == "AUTO") {
					$CM								= " [A]"; 
				}
				
				if ($call_type == "INBOUND") {
					$CM								= " [I]"; 
				}            
				
				if ($call_type == "MANUAL") {
					$CM								= " [M]"; }                        
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
		
			$call_time_S 							= ($STARTtime - $last_state_change);
			$call_time_M 							= ($call_time_S / 60);
			$call_time_M 							= round($call_time_M, 2);
			$call_time_M_int 						= intval("$call_time_M");
			$call_time_SEC 							= ($call_time_M - $call_time_M_int);
			$call_time_SEC 							= ($call_time_SEC * 60);
			$call_time_SEC 							= round($call_time_SEC, 0);
				
			if ($call_time_SEC < 10) { 
				$call_time_SEC 						= "0$call_time_SEC"; 
			}
			
			$call_time_MS 							= "$call_time_M_int:$call_time_SEC";
			
			switch ($status) {
				case "PAUSED":
					$circleclass 					= "circle circle-warning circle-lg text-left";
					$textclass 						= "text-warning";
					$nametextclass 					= "text-warning";
					
					if (strlen($pausecode) > 0) { 
						$status 					.= " [$pausecode]"; 
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
				
				case "HUNGUP":
					$textclass 						= "text-danger";
					
				break;
			}
		
			$barracks 								.= '[';
			$barracks 								.= '"'.$sessionAvatar.'",';
			$barracks 								.= '"<a id=\"onclick-userinfo\" data-toggle=\"modal\" data-target=\"#modal_view_agent_information\" data-id=\"'.$userid.'\" data-user=\"'.$agentid.'\" class=\"text-blue\"><strong>'.$agentname.'</strong></a>",'; 
			$barracks 								.= '"'.$user_group.'",';    
			$barracks 								.= '"<b class=\"'.$textclass.'\">'.$status.''.$CM.'</b>",';    
			$barracks 								.= '"'.$cust_phone.'",';         
			$barracks 								.= '"<b class=\"'.$textclass.'\">'.$call_time_MS.'</b>",';
			$barracks 								.= '"'.$campname.'"';
			$barracks 								.= '],';
		}    
		
		$barracks 									= rtrim($barracks, ",");    
		//$barracks 									.= ']';		
		//echo json_encode($barracks);		
    }
    
    $barracks 									.= ']';
	echo json_encode($barracks);
    
?>
