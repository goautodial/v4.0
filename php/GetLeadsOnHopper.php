<?php
/**
 * @file        GetLeadsOnHopper.php
 * @brief       Handles Leads on the hopper variables
 * @copyright   Copyright (c) 2018 GOautoial Inc.
 * @author      Alexander Jim Abenoja
 * @author		Demian Lizandro A, Biscocho 
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
	$campaign_id 								= $_POST["campaign_id"];
	$output 									= $api->API_getAllLeadsOnHopper($campaign_id);

	if (!empty($output)) {
		$data 									= '[';
		$i										= 0;
		$count 									= 0;
		$dial_status 							= explode(" ", $output->camp_dial_status[0]);
		$statuses 								= array();
		$availableStats 						= array();
		
		foreach($dial_status as $status){
			if(!empty($status)){
				array_push($statuses, $status);
			}
		}
		
		for($i=0;$i<=count($output->lead_id);$i++) {
			array_push($availableStats, $output->status[$i]);
			if(!empty($output->hopper_id[$i]) && in_array($output->status[$i], $statuses)){
				$count 							= $count + 1;
				$data 							.= '[';
				$data 							.= '"'.$output->hopper_id[$i].'",';
				$data 							.= '"'.$output->priority[$i].'",';
				$data 							.= '"'.$output->lead_id[$i].'",';
				$data 							.= '"'.$output->list_id[$i].'",';
				$data 							.= '"'.$output->phone_number[$i].'",';
				$data 							.= '"'.$output->state[$i].'",';
				$data 							.= '"'.$output->status[$i].'",';
				$data 							.= '"'.$output->called_count[$i].'",';
				$data 							.= '"'.$output->gmt_offset_now[$i].'",';
				$data 							.= '"'.$output->alt_dial[$i].'",';
				$data 							.= '"'.$output->source[$i].'"';
				$data 							.= '],';
			}
		}

		$data 									= rtrim($data, ",");    
		$data 									.= ']';
		
		$details['count'] 						= $count;
		$details['data'] 						= $data;
		$details['stats'] 						= $statuses;
		$details['data_stats'] 					= $availableStats;
		echo json_encode($details, true);
	} else {
		echo json_encode("empty", true);
	}

?>
