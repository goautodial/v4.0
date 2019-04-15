<?php
/**
 * @file        GetDialStatuses.php
 * @brief       Handles Dial Status Request
 * @copyright   Copyright (c) 2018 GOautoial Inc.
 * @author      Alexander Jim Abenoja
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
	$campaign_id 								= $_POST["campaign_id"];
	$add_hotkey									= $_POST["add_hotkey"];
	$is_selectable								= $_POST["is_selectable"];
	$output 									= $api->API_getAllDialStatuses($campaign_id, $add_hotkey, $is_selectable);

	if(!empty($output)){
		$data 									= '';
		// $i=0;
		
		if (count($output->status->system) > 0) {
			$data								.= '<optgroup label="System Statuses">';
			foreach($output->status->system as $key => $val){
			// for($i=0;$i<=count($output->status);$i++) {
				$data 							.= '<option value="'.$val.'" data-name="'.$output->status_name->system[$key].'">'.$val.' - '.$output->status_name->system[$key].'</option>';
			}
			$data								.= '</optgroup>';
		}
		
		if (count($output->status->campaign) > 0) {
			$data								.= '<optgroup label="Campaign Statuses">';
			foreach($output->status->campaign as $key => $val){
			// for($i=0;$i<=count($output->status);$i++) {
				$data 							.= '<option value="'.$val.'" data-name="'.$output->status_name->campaign[$key].'">'.$val.' - '.$output->status_name->campaign[$key].'</option>';
			}
			$data								.= '</optgroup>';
		}
	}
	
	echo json_encode($data);

?>
