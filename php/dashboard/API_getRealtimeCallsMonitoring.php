<?php
/**
 * @file        API_getRealtimeAgentsMonitoring.php
 * @brief       Displays realtime calls monitoring data and HTML
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
	$output 									= $api->API_getRealtimeCallsMonitoring();

    $barracks 									= '[';

    if (!empty($output->data)) {
		foreach ($output->data as $key => $value) {

			$campname 								= $value->campaign_id;    
			$status 								= $value->status;
			$call_type 								= $value->call_type;
			$call_time 								= $value->call_time;
			$server_ip 								= $value->call_server_ip;
			$last_call_time 						= $value->last_call_time;
			$last_call_finish 						= $value->last_call_finish;
			$last_state_change 						= $value->last_state_change;
			$lead_id 								= $value->lead_id;
			$callerid 								= $value->callerid;
			$cust_phone 							= $value->phone_number;
			
			$STARTtime 								= date("U");
			$textclass 								= "text-info";
			
			$sessionAvatar 							= "<div class='media'><avatar username='$calltype' :size='32'></avatar></div>";

			//$call_time_S 							= ($STARTtime - $last_call_time);         
			$call_time_S 							= ($STARTtime - $call_time);
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

			if ($call_type == "IN") {
					$calltype 						= "INBOUND";

			} else {
					$calltype 						= "OUTBOUND";
			}    
				
			$barracks 								.= '[';       
			$barracks 								.= '"'.$sessionAvatar.'",';
			$barracks 								.= '"<b class=\"text-blue\">'.$status.'</b>",'; 
			$barracks 								.= '"'.$cust_phone.'",';    
			$barracks 								.= '"<b class=\"'.$textclass.'\">'.$calltype.'</b>",';                   
			$barracks 								.= '"'.$campname.'",';
			$barracks 								.= '"'.$call_time_MS.'"';
			//$barracks 							.= '"'.$user_group.'"';     
			$barracks 								.= '],';
	
		}

		$barracks 									= rtrim($barracks, ",");    
    }
    
    $barracks 									.= ']';    
    echo json_encode($barracks);

?>
