<?php
/**
 * @file        GetCampaigns.php
 * @brief       Handles campaign table variables and HTML
 * @copyright   Copyright (c) 2018 GOautodial Inc. 
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
	require_once('LanguageHandler.php');
	
	$api 							= \creamy\APIHandler::getInstance();
	$lh 							= \creamy\LanguageHandler::getInstance();
	$perm 							= $_POST["permission"];
	$output 						= $api->API_getAllCampaigns();

	$data 							= '[';
	$i								= 0;
	
	for($i=0;$i<=count($output->campaign_id);$i++) {
		$campaign_id 				= $output->campaign_id[$i];
		$campaign_name				= $output->campaign_name[$i];
		$dial_method				= $output->dial_method[$i];
		$active						= $output->active[$i];
		
			$data 					.= '[';
			$data 					.= '"'.($perm->campaign->campaign_update !== 'N' ? '<a class="edit-campaign" data-id="'.$campaign_id.'" data-name="'.$campaign_name.'">' : '').'<avatar username="'.$campaign_name.'" :size="32"></avatar>'.($perm->campaign->campaign_update !== 'N' ? '</a>' : '').'",';
			$data 					.= '"'.($perm->campaign->campaign_delete !== 'N' ? '<label for="'.$campaign_id.'"><div class="checkbox c-checkbox"><label><input name="" class="check_campaign" id="'.$campaign_id.'" type="checkbox" value="Y"><span class="fa fa-check"></span></label></div></label>' : '').'",';
			$data 					.= '"'.($perm->campaign->campaign_update !== 'N' ? '<a class="edit-campaign" data-id="'.$campaign_id.'" data-name="'.$campaign_name.'">' : '').''.$campaign_id.''.($perm->campaign->campaign_update !== 'N' ? '</a>' : '').'",';
			$data 					.= '"'.$campaign_name.'",';
			$data 					.= '"'.$dial_method.'",';
			$data 					.= '"'.$active.'",';
			$data 					.= '"'.ActionMenuForCampaigns($campaign_id, $campaign_name, $perm).'"';
			$data 					.= '],';

	}
	
	$data 							= rtrim($data, ",");    
	$data 							.= ']';		

	echo json_encode($data);
	
	function ActionMenuForCampaigns($id, $name, $perm) {

	    $htmlcode = '<div class=\"btn-group\">';
		$htmlcode .= '<button type=\"button\" class=\"btn btn-default dropdown-toggle\" data-toggle=\"dropdown\">'.$lh->translationFor('choose_action').'<button type=\"button\" class=\"btn btn-default dropdown-toggle\" data-toggle=\"dropdown\" style=\"height: 34px;\">';
		$htmlcode .= '<span class=\"caret\"></span><span class=\"sr-only\">Toggle Dropdown</span>';
		$htmlcode .= '</button>';
		$htmlcode .= '<ul class=\"dropdown-menu\" role=\"menu\">';
		$htmlcode .= '<li'.($perm->campaign->campaign_update === 'N' ? ' class="hidden"' : '').'><a class=\"edit-campaign\" href=\"#\" data-id=\"'.$id.'\">'.$lh->translationFor('view_details').'</a></li>';
		$htmlcode .= '<li'.($perm->pausecodes->pausecodes_read === 'N' ? ' class="hidden"' : '').'><a class=\"view-pause-codes\" href=\"#\" data-id=\"'.$id.'\">'.$lh->translationFor('view_pause_codes').'</a></li>';
		$htmlcode .= '<li'.($perm->hotkeys->hotkeys_read === 'N' ? ' class="hidden"' : '').'><a class=\"view-hotkeys\" href=\"#\" data-id=\"'.$id.'\">'.$lh->translationFor('view_hotkeys').'</a></li>';
		$htmlcode .= '<li'.($perm->list->list_read === 'N' ? ' class="hidden"' : '').'><a class=\"view-lists\" href=\"#\" data-id=\"'.$id.'\">'.$lh->translationFor('view_lists').'</a></li>';
		$htmlcode .= '<li'.($perm->campaign->campaign_delete === 'N' ? ' class="hidden"' : '').'><a class=\"delete-campaign\" href=\"#\" data-id=\"'.$id.'\" data-name=\"'.$name.'\">'.$lh->translationFor('delete').'</a></li>';
		$htmlcode .= '</ul></div>';
		
		return $htmlcode;
	}

?>
