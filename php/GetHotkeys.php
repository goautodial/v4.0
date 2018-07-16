<?php
/**
 * @file        GetHotkeys.php
 * @brief       Handles Hotkeys variables
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
	$api 								= \creamy\APIHandler::getInstance();
	$campaign_id 						= $_POST["campaign_id"];
	$user_group							= $_SESSION["usergroup"];
	$perm 								= $api->goGetPermissions('hotkeys', $user_group);	
	$output 							= $api->API_getAllHotkeys($campaign_id);

	$data 							= '[';
	$i								= 0;
	
	for($i=0;$i<=count($output->campaign_id);$i++) {
		if(!empty($output->hotkey[$i])){
			$data 					.= '[';
			$data 					.= '"'.$output->hotkey[$i].'",';
			$data 					.= '"'.$output->status[$i].'",';
			$data 					.= '"'.str_replace('+',' ',$output->status_name[$i]).'",';
			$data 					.= '"<a style=\"margin-right: 5px;\" href=\"#\" class=\"btn-delete-hk btn btn-danger'.($perm->hotkeys_delete === 'N' ? ' hidden' : '').'\" data-camp-id=\"'.$output->campaign_id[$i].'\" data-hotkey=\"'.$output->hotkey[$i].'\"><span class=\"fa fa-trash\"></span></a>"';
			$data 					.= '],';
		}
	}
	
	$data 							= rtrim($data, ",");    
	$data 							.= ']';
	
	echo json_encode($data, true);

?>
