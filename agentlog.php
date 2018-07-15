<?php
/**
 * @file        agentlog.php
 * @brief       View agent logs
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A, Biscocho
 * @author      Alexander Jim H. Abenoja
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

	require_once('./php/APIHandler.php');
	$api 									= \creamy\APIHandler::getInstance();
	
	$user 									= $_POST['user'];
	$start_date								= $_POST['start_date'];
	$end_date								= $_POST['end_date'];
	$agentlog								= $_POST['agentlog'];

	$output 								= $api->API_getAgentLog($user, $start_date, $end_date, $agentlog);	
	$i										= 0;
	
	if ($agentlog == "outbound") {
		$outbound 							= '[';
		
		for($i=0;$i<=count($output->data->user);$i++) {
			if (!empty($output->data->phone_number[$i])) {
				$outbound 					.= '[';
				$outbound 					.= '"'.date('M. d, Y h:i A', strtotime($output->data->call_date[$i])).'",';
				$outbound 					.= '"'.$output->data->status[$i].'",';
				$outbound 					.= '"'.$output->data->phone_number[$i].'",';
				$outbound 					.= '"'.$output->data->campaign_id[$i].'",';
				$outbound 					.= '"'.$output->data->user_group[$i].'",';
				$outbound 					.= '"'.$output->data->list_id[$i].'",';
				$outbound 					.= '"'.$output->data->lead_id[$i].'",';
				$outbound 					.= '"'.$output->data->term_reason[$i].'"';
				$outbound 					.= '],';			
			}
		}
		
		$outbound 							= rtrim($outbound, ",");    
		$outbound 							.= ']';		
		
		echo json_encode($outbound);
		
	} elseif ($agentlog == "inbound") {
		$inbound 							= '[';
		
		for($i=0;$i<=count($output->data->user);$i++) {
			if (!empty($output->data->phone_number[$i])) {
				$inbound 					.= '[';
				$inbound 					.= '"'.date('M. d, Y h:i A', strtotime($output->data->call_date[$i])).'",';
				$inbound 					.= '"'.$output->data->status[$i].'",';
				$inbound 					.= '"'.$output->data->phone_number[$i].'",';
				$inbound 					.= '"'.$output->data->length_in_sec[$i].'",';
				$inbound 					.= '"'.$output->data->queue_seconds[$i].'",';		
				$inbound 					.= '"'.$output->data->campaign_id[$i].'",';
				$inbound 					.= '"'.$output->data->user_group[$i].'",';
				$inbound 					.= '"'.$output->data->term_reason[$i].'"';
				$inbound 					.= '],';			
			}			
		}	
		
		$inbound 							= rtrim($inbound, ",");    
		$inbound 							.= ']';	
		
		echo json_encode($inbound);	
		
	} elseif ($agentlog == "userlog"){
		$userlog 							= '[';
		
		for($i=0;$i<=count($output->data->agent_log_id);$i++) {
			if (!empty($output->data->agent_log_id[$i])) { 
				$userlog 					.= '[';
				$userlog 					.= '"'.$output->data->agent_log_id[$i].'",';
				$userlog 					.= '"'.$output->data->user[$i].'",';
				$userlog 					.= '"'.$output->data->sub_status[$i].'",';
				$userlog 					.= '"'.date('M. d, Y h:i A', strtotime($output->data->event_time[$i])).'",';
				$userlog 					.= '"'.$output->data->campaign_id[$i].'",';
				$userlog 					.= '"'.$output->data->user_group[$i].'"';			
				$userlog 					.= '],';			
			}
		}
		
		$userlog 							= rtrim($userlog, ",");    
		$userlog 							.= ']';	
		
		echo json_encode($userlog);	
	}	

?>
