<?php
/**
 * @file        GetPauseCodes.php
 * @brief       Handles Pause Code variables and HTML
 * @copyright   Copyright (c) 2018 GOautodial Inc. 
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
	$api 							= \creamy\APIHandler::getInstance();
	
	$campaign_id 					= $_POST["campaign_id"];

	$output 						= $api->API_getCampaignDispositions($campaign_id);
	
	$data 							= '[';
	$i								= 0;
	
	for($i=0;$i<=count($output->campaign_id);$i++) {
		if(!empty($output->status[$i])){
			$data 					.= '[';
			$data 					.= '"'.$output->status[$i].'",';
			$data 					.= '"'.$output->status_name[$i].'",';
			$data 					.= '"'.$output->selectable[$i].'",';
			$data 					.= '"'.$output->human_answered[$i].'",';
			$data 					.= '"'.$output->sale[$i].'",';
			$data 					.= '"'.$output->dnc[$i].'",';
			$data					.= '"'.$output->customer_contact[$i].'",';
			$data 					.= '"'.$output->not_interested[$i].'",';
			$data 					.= '"'.$output->unworkable[$i].'",';
			$data 					.= '"'.$output->scheduled_callback[$i].'",';
			$data					.= '"<a class=\"edit_disposition btn btn-primary\" href=\"#\" data-toggle=\"modal\" data-target=\"#edit_disposition_modal\" data-id=\"'.$output->campaign_id[$i].'\" data-status=\"'.$output->status[$i].'\"><i class=\"fa fa-edit\"></i></a><a class=\"delete_disposition btn btn-danger\" href=\"#\" data-id=\"'.$output->campaign_id[$i].'\" data-status=\"'.$output->status[$i].'\"><i class=\"fa fa-trash\"></i></a>"';
			$data 					.= '],';
		}
	}
	
	$data 							= rtrim($data, ",");    
	$data 							.= ']';		

	echo json_encode($data);
?>
