<?php
/**
 * @file        ViewLeadRecycling.php
 * @brief       Handles custom lead recycling variables and HTML
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

	$output 						= $api->API_getCampaignLeadRecycling($campaign_id);
	
	$data 							= '[';
	$i								= 0;
	
	$optionsHKF 					= array(
		"1" 							=> "1", 
		"2" 							=> "2", 
		"3" 							=> "3", 
		"4" 							=> "4", 
		"5" 							=> "5", 
		"6" 							=> "6", 
		"7" 							=> "7", 
		"8" 							=> "8", 
		"9" 							=> "9"
	);
	
	$optionsYN	 					= array(
		"Y" 							=> "Y", 
		"N" 							=> "N"
	);
	
	for($i=0;$i<=count($output->recycle_id);$i++) {
		if(!empty($output->status[$i])){
			$data 					.= '[';
			$data 					.= '"'.$output->recycle_id[$i].'",';
			$data 					.= '"'.$output->status[$i].'",';
			$data 					.= '"'.singleFormInputElement("attempt_delay-".$output->status[$i], "attempt_delay", "number", $output->attempt_delay[$i]).'",';
			$data 					.= '"'.singleFormGroupWithSelect("", "attempt_maximum-".$output->status[$i], "attempt_maximum", $optionsHKF, $output->attempt_maximum[$i]).'",';			
			$data 					.= '"'.singleFormGroupWithSelect("", "active-".$output->status[$i], "active", $optionsYN, $output->active[$i]).'",';
			$data					.= '"<a id=\"btn-edit-leadrecycling-'.$output->status[$i].'\" class=\"btn btn-primary btn-edit-leadrecycling\" href=\"#\" data-id=\"'.$output->recycle_id[$i].'\" data-status=\"'.$output->status[$i].'\"><i class=\"fa fa-pencil\"></i></a><a id=\"btn-cancel-leadrecycling-'.$output->status[$i].'\" class=\"btn btn-warning btn-cancel-leadrecycling\" href=\"#\" data-id=\"'.$output->recycle_id[$i].'\" data-status=\"'.$output->status[$i].'\" disabled><i class=\"fa fa-recycle\"></i></a><a class=\"delete_leadrecycling btn btn-danger btn-delete-leadrecycling\" href=\"#\" data-id=\"'.$output->recycle_id[$i].'\" data-status=\"'.$output->status[$i].'\" data-campaign=\"'.$output->campaign_id.'\"><i class=\"fa fa-trash\"></i></a>"';
			$data 					.= '],';
		}
	}
	
	$data 							= rtrim($data, ",");    
	$data 							.= ']';		

	echo json_encode($data);
	
    function singleFormGroupWithSelect($label, $id, $name, $options, $selectedOption, $needsTranslation = false) {
	    $labelCode = empty($label) ? '' : '<label class=\"control-label '.$labelClass.'\">'.$label.'</label>';
	    $selectCode = '<select id=\"'.$id.'\" name=\"'.$name.'\" class=\"form-control select2\" disabled>';
	    foreach ($options as $key => $value) {
		    $isSelected = ($selectedOption == $key) ? " selected" : "";
		    $selectCode .= '<option value=\"'.$key.'\" '.$isSelected.'>'.$value.'</option>';
	    }
		$selectCode .= '</select>';
		return $selectCode;
    }
   function singleFormInputElement($id, $name, $type, $value) {
	    return '<input name=\"'.$name.'\" id=\"'.$id.'\" type=\"'.$type.'\" class=\"form-control\" maxlength=\"5\" min=\"120\" max=\"32400\" value=\"'.$value.'\" required disabled>';
    }   
	
?>
