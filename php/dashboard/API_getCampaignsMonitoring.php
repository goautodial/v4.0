<?php
/**
 * @file        API_getCampaignsMonitoring.php
 * @brief       Displays campaigns with < 100 leads in hopper
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
	$output 									= $api->API_getCampaignsMonitoring();
	$max 										= 0;
	$jsonv 										= '['; 	

    if (count($output->data) < 1){
        echo  "No data available";

    } elseif (!empty($output->data)) {
		foreach ($output->data as $key => $value) {		
			if(++$max > 100) break;

			$campname 							= $api->escapeJsonString($value->campaign_name);
			$leadscount 						= $api->escapeJsonString($value->mycnt);
			$campid 							= $api->escapeJsonString($value->campaign_id);
			//$campdesc	 						= $api->escapeJsonString($value->campaign_description);
			//$callrecordings 					= $api->escapeJsonString($value->campaign_recording);
			//$campaigncid 						= $api->escapeJsonString($value->campaign_cid);
			$localcalltime 						= $api->escapeJsonString($value->local_call_time);
			$usergroup 							= $api->escapeJsonString($value->user_group);
			//$camptype 								= $api->escapeJsonString($value->campaign_type);
			
			if ($leadscount == 0){ 
				$textclass 						= "text-blue";
				$sessionAvatar 					= "<div class='media'><avatar username='$localcalltime' :size='32'></avatar></div>";
				
			} 
			
			if ($leadscount > 0){ 
				$textclass 						= "text-blue";
				$sessionAvatar 					= "<div class='media'><avatar username='$localcalltime' :size='32'></avatar></div>";
				
			} 
			
			$jsonv 								.= '[';
			$jsonv 								.= '"'.$sessionAvatar.'",';
			$jsonv 								.= '"<a id=\"onclick-campaigninfo\" data-toggle=\"modal\" data-target=\"#view_campaign_information\" data-id=\"'.$campid.'\" class=\"text-blue\"><strong>'.$campid.'</strong></a>",';        
			$jsonv 								.= '"'.$campname.'",'; 
			$jsonv 								.= '"'.$leadscount.'",';      
			$jsonv 								.= '"'.$localcalltime.'",';      
			$jsonv 								.= '"'.$usergroup.'"';
			$jsonv 								.= '],';

		}

		$jsonv 									= rtrim($jsonv, ",");    
	} 
   
	$jsonv 										.= ']';
	echo json_encode($jsonv);   
?>
