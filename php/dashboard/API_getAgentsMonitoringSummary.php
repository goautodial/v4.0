<?php
/**
 * @file        API_getAgentsMonitoringSummary.php
 * @brief       Displays summary of agents monitoring data and HTML
 * @copyright   Copyright (c) 2020 GOautodial Inc.
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
        
    if (empty($output->data)) {    
		echo '<span class="list-group-item">
			<div class="media-box">
					<div class="media-box-body clearfix">
						<strong class="media-box-heading text-primary">
						- - There are no available agents - -</strong>
					</div>
				</div>
			</span>
		</div>';

    } else {        
        $max = 0;
		if (is_array($output->data)) {    
			foreach ($output->data as $key => $value) {        
				if(++$max > 6) break;
				
				$userid 							= $api->escapeJsonString($value->vu_user_id);
				$agentid 							= $api->escapeJsonString($value->vla_user);
				$agentname 							= $api->escapeJsonString($value->vu_full_name);
				$campname 							= $api->escapeJsonString($value->vla_campaign_name);    
				$station 							= $api->escapeJsonString($value->vla_extension);
				$user_group 						= $api->escapeJsonString($value->vu_user_group);
				$sessionid 							= $api->escapeJsonString($value->vla_conf_exten);
				$status 							= $api->escapeJsonString($value->vla_status);
				$call_type 							= $api->escapeJsonString($value->vla_comments);
				$server_ip 							= $api->escapeJsonString($value->vla_server_ip);
				$call_server_ip 					= $api->escapeJsonString($value->vla_call_server_ip);
				$last_call_time 					= $api->escapeJsonString($value->last_call_time);
				$last_call_finish 					= $api->escapeJsonString($value->last_call_finish);
				$campaign_id 						= $api->escapeJsonString($value->vla_campaign_id);
				$last_state_change 					= $api->escapeJsonString($value->last_state_change);
				$lead_id 							= $api->escapeJsonString($value->vla_lead_id);
				$agent_log_id 						= $api->escapeJsonString($value->vla_agent_log_id);
				$vla_callerid 						= $api->escapeJsonString($value->vla_callerid);    
				//$cust_phone 						= ( !isset ( $value->vl_phone_number ) ) ? "" : $api->escapeJsonString ( $value->vl_phone_number );
				$pausecode 							= $api->escapeJsonString($value->vla_pausecode);              
				$STARTtime 							= date("U"); 
				
				$sessionAvatar 						= "<avatar username='$agentname' :size='32'></avatar>";
				
				if (preg_match("/READY|CLOSER/",$status)) {
					$last_call_time=$last_state_change;
					$class 							= "circle circle-warning circle-lg text-left";
					if ($lead_id>0){ 
						$status						= "DISPO";
					}
				}
				
				if (preg_match("/PAUSED/",$status)) {
					$class 							= "circle circle-danger circle-lg text-left";
					if ($lead_id>0){ 
						$status						= "DISPO";
					}
				}
				
				if (!preg_match("/INCALL|QUEUE|PARK|3-WAY/",$status)) {
					$call_time_S 					= ($STARTtime - $last_state_change);
				} elseif (preg_match("/3-WAY/",$status)) {
					$call_time_S 					= ($STARTtime - $call_mostrecent);
				} else {
					$call_time_S 					= ($STARTtime - $last_call_time);
					$class 							= "circle circle-success circle-lg text-left";
				}

				$call_time_M 						= ($call_time_S / 60);
				$call_time_M 						= round($call_time_M, 2);
				$call_time_M_int 					= intval("$call_time_M");
				$call_time_SEC 						= ($call_time_M - $call_time_M_int);
				$call_time_SEC 						= ($call_time_SEC * 60);
				$call_time_SEC 						= round($call_time_SEC, 0);
				
				if ($call_time_SEC < 10) {
					$call_time_SEC 					= "0$call_time_SEC";
				}
				
				$call_time_MS 						= "$call_time_M_int:$call_time_SEC";
				$G 									= "";		
				$EG 								= "";
					
				echo '<a class="list-group-item">
					<div class="media-box">
						<div class="pull-left">
							'.$sessionAvatar.'
						</div>            
						<div class="media-box-body clearfix">
							<strong class="media-box-heading text-primary">
							<b id="onclick-userinfo" data-toggle="modal" data-target="#modal_view_agent_information" data-id="'.$userid.'" data-user="'.$agentid.'"><span class="'.$class.'"></span>'.$agentname.'</b>
							</strong><br/>
							<strong style="padding-left:20px;">'.$campname.'</strong>
							<small class="text-muted pull-right ml" style="padding-right:20px;">'.$call_time_MS.'</small>
						</div>
					</div>
				</a>';
			}
		}
    }
?>
