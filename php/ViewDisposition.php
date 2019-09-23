<?php
/**
 * @file        ViewDisposition.php
 * @brief       Handles custom disposition variables and HTML
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
	$view_type                      = $_POST["view_type"];
	$dispo_update                   = $_POST["dispo_update"];
	$dispo_delete                   = $_POST["dispo_delete"];

	$output 						= $api->API_getCampaignDispositions($campaign_id);
	
	$data 							= '[';
	$i								= 0;
	
	for($i=0;$i<=count($output->campaign_id);$i++) {
		if(!empty($output->status[$i])){
			if (preg_match("/\\s/", $output->status[$i])) {
				$status_id = str_replace(" ", "-", $output->status[$i]);
			} else {
				$status_id = $output->status[$i];
			}
			$data 					.= '[';
			$data 					.= '"'.$status_id.'",';
			$data 					.= '"'.$output->status_name[$i].'",';
			$data 					.= '"'.checkboxInputWithLabel("selectable", "edit_selectable-".$status_id, "selectable", $output->selectable[$i]).'",';
			$data 					.= '"'.checkboxInputWithLabel("human_answered", "edit_human_answered-".$status_id, "human_answered", $output->human_answered[$i]).'",';
			$data 					.= '"'.checkboxInputWithLabel("sale", "edit_sale-".$status_id, "sale", $output->sale[$i]).'",';
			$data 					.= '"'.checkboxInputWithLabel("dnc", "edit_dnc-".$status_id, "dnc", $output->dnc[$i]).'",';
			$data					.= '"'.checkboxInputWithLabel("customer_contact", "edit_customer_contact-".$status_id, "customer_contact", $output->customer_contact[$i]).'",';
			$data 					.= '"'.checkboxInputWithLabel("not_interested", "edit_not_interested-".$status_id, "not_interested", $output->not_interested[$i]).'",';
			$data 					.= '"'.checkboxInputWithLabel("unworkable", "edit_unworkable-".$status_id, "unworkable", $output->unworkable[$i]).'",';
			$data 					.= '"'.checkboxInputWithLabel("scheduled_callback", "edit_scheduled_callback-".$status_id, "scheduled_callback", $output->scheduled_callback[$i]).'",';
			if ($dispo_update !== 'N' && $view_type == 'update') {
				$data				.= '"<a id=\"btn-edit-disposition-'.$status_id.'\" class=\"btn btn-primary btn-edit-disposition\" href=\"#\" data-id=\"'.$output->campaign_id[$i].'\" data-status=\"'.$output->status[$i].'\"><i class=\"fa fa-pencil\"></i></a>';
				$data               .= '<a id=\"btn-cancel-disposition-'.$status_id.'\" class=\"btn btn-warning btn-cancel-disposition\" href=\"#\" data-id=\"'.$output->campaign_id[$i].'\" data-status=\"'.$output->status[$i].'\" disabled><i class=\"fa fa-recycle\"></i></a>';
			} else {
				$data               .= '"';
			}
			if ($dispo_delete !== 'N') {
				$data               .= '<a class=\"delete_disposition btn btn-danger btn-delete-disposition\" href=\"#\" data-id=\"'.$output->campaign_id[$i].'\" data-status=\"'.$output->status[$i].'\"><i class=\"fa fa-trash\"></i></a>"';
			} else {
				$data               .= '"';
			}
			$data 					.= '],';
		}
	}
	
	$data 							= rtrim($data, ",");    
	$data 							.= ']';		

	echo json_encode($data);
	
    function checkboxInputWithLabel($label, $id, $name, $enabled, $disabled = "true") {
		if ($enabled == "Y") { 
			$enabled				= "Y";
		} else {
			$enabled				= "";
		}
		
	    return '<div class=\"form-group\"><label for=\"'.$id.'\" class=\"checkbox-inline c-checkbox\"><input type=\"checkbox\" id=\"'.$id.'\" name=\"'.$name.'\"/ '.($enabled ? "checked": "").' value=\"'.($enabled ? "Y": "N").'\" '.($disabled ? "disabled": "").'><span class=\"fa fa-check\"></span></label></div>';
    }	
	
?>
