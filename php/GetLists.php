<?php
/**
 * @file        GetLists.php
 * @brief       Handles Campaigns List Requests
 * @copyright   Copyright (c) 2018 GOautoial Inc.
 * @author      Noel Umandap
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
	$api 											= \creamy\APIHandler::getInstance();
	$campaign_id 									= $_POST["campaign_id"];
	$output 										= $api->API_getAllListsCampaign($campaign_id);
	
	if(!empty($output)){
		$data 										= '[';
		$i											= 0;
		$count_active 								= 0;
		$count_inactive 							= 0;
		$lead_count 								= 0;
		
		for ($i=0;$i<=count($output->list_id);$i++) {
			if (!empty($output->list_id[$i])) {
				if ($output->active[$i] == "Y") {
					$count_active 					= $count_active + 1;
					$lead_count 					= $lead_count + $output->tally[$i];
				} else {
					$count_inactive 				= $count_inactive +1;
				}

				$info								= array(
					'list_id' 							=> $output->list_id[$i],
					'list_name' 						=> $output->list_name[$i],
					'list_description' 					=> $output->list_description[$i],
					'campaign_id' 						=> $output->campaign_id[$i],
					'reset_time' 						=> $output->reset_time[$i],
					'reset_called_lead_status' 			=> $output->reset_called_lead_status[$i],
					'active' 							=> $output->active[$i],
					'agent_script_override' 			=> $output->agent_script_override[$i],
					'campaign_cid_override' 			=> $output->campaign_cid_override[$i],
					'drop_inbound_group_override' 		=> $output->drop_inbound_group_override[$i],
					'web_form_address' 					=> $output->web_form_address[$i],
					'xferconf_a_number' 				=> $output->xferconf_a_number[$i],
					'xferconf_b_number' 				=> $output->xferconf_b_number[$i],
					'xferconf_c_number' 				=> $output->xferconf_c_number[$i],
					'xferconf_d_number' 				=> $output->xferconf_d_number[$i],
					'xferconf_e_number' 				=> $output->xferconf_e_number[$i]
				);
				
				$info								= json_encode($info);
				$info								= base64_encode($info);
				
				$calldate							= $output->list_lastcalldate[$i];
				
				if (is_null($calldate) || empty($calldate) || strstr($calldate, "0000-00-00")) {
					$calldate						= "";
				} else {
					$calldate						= strtotime($calldate);
				}
				
				$data 								.= '[';
				$data 								.= '"'.$output->list_id[$i].'",';
				$data 								.= '"'.$output->list_name[$i].'",';
				$data 								.= '"'.$output->list_description[$i].'",';
				$data 								.= '"'.$output->tally[$i].'",';
				$data 								.= '"'.$output->active[$i].'",';
				$data 								.= '"'.date('M. d, Y h:i A', $calldate).'",';
				$data 								.= '"<a title=\"Modify View List\" class=\"btn btn-primary btn-edit-list edit-list\" href=\"#\" data-info=\"'.$info.'\" data-id=\"'.$output->list_id[$i].'\" data-campaign=\"'.$output->campaign_id[$i].'\"><span class=\"fa fa-pencil\"></span></a></td>"';
				$data 								.= '],';
			}
		}

		$data 										= rtrim($data, ",");    
		$data 										.= ']';
		
		$details['data'] 							= $data;
		$details['count_active'] 					= $count_active;
		$details['count_inactive'] 					= $count_inactive;
		$details['lead_count'] 						= $lead_count;
		
	}

	echo json_encode($details);

?>
