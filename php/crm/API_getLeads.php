<?php
/**
 * @file        API_getLeads.php
 * @brief       Handles requests for displaying leads in the CRM
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

	require_once('APIHandler.php');
			
	$api 										= \creamy\APIHandler::getInstance();
	
	$search 									= $_POST['search'];
	$disposition 								= $_POST['disposition_filter'];
	$list 										= $_POST['list_filter'];
	$address 									= $_POST['address_filter'];
	$city 										= $_POST['city_filter'];
	$state 										= $_POST['state_filter'];
	$limit										= 300;
	$search_customers							= $_POST['search_customers'];
	$output 									= $api->API_getLeads($search, $disposition, $list, $address, $city, $state, $limit, $search_customers);	
	$table 										= '[';
	
	foreach ($output->data as $key => $value) {
		$lead_id								= $value->lead_id;
		$phone_number							= $value->phone_number;
		$first_name								= $value->first_name;
		$middle_initial							= $value->middle_initial;
		$last_name								= $value->last_name;
		$full_name								= $first_name.' '.$middle_initial.' '.$last_name;
		$status									= $value->status;
		$action 								= actionMenu($lead_id);
		
		if (!empty($phone_number)) {
			$table 								.= '[';
			$table 								.= '"<a class=\"edit-contact\" data-id=\"'.$lead_id.'\">'.$lead_id.'</a>",';
			$table 								.= '"'.$full_name.'",';
			$table 								.= '"'.$phone_number.'",';
			$table 								.= '"'.$status.'",';
			$table 								.= '"'.$action.'"';
			$table 								.= '],';
		}
	}
	
	$table 										= rtrim($table, ",");    
	$table 										.= ']';		
	
	echo json_encode($table);

    function actionMenu($lead_id) {
		$actionmenu = '<div class=\"btn-group\"><button type=\"button\" class=\"btn btn-default dropdown-toggle\" data-toggle=\"dropdown\">Choose Action<button type=\"button\" class=\"btn btn-default dropdown-toggle\" data-toggle=\"dropdown\" style=\"height: 34px;\"><span class=\"caret\"></span><span class=\"sr-only\">Toggle Dropdown</span></button><ul class=\"dropdown-menu\" role=\"menu\"><li><a class=\"edit-contact\" data-id=\"'.$lead_id.'\">Contact Details</a></li><li class=\"divider\"></li><li><a class=\"delete-contact\" data-id=\"'.$lead_id.'\">Delete</a></li></ul></div>';
		
		return $actionmenu;
	}
?>
