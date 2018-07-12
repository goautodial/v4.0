<?php
/**
 * @file        GetDialStatuses.php
 * @brief       Handles Dial Status Request
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
	$output 									= $api->API_getAllDialStatuses($campaign_id);

	if(!empty($output)){
		$data 									= '';
		// $i=0;
		foreach($output->status as $key => $val){
		// for($i=0;$i<=count($output->status);$i++) {
			$data 								.= '<option value="'.$val.'" data-name="'.$output->status_name[$key].'">'.$val.' - '.$output->status_name[$key].'</option>';
		}
	}
	
	echo json_encode($data);

?>
