<?php
/**
 * @file        API_getSalesAgent.php
 * @brief       Displays Sales Agent Report on Dashboard Statewide Customization
 * @copyright   Copyright (c) 2020 GOautodial Inc.
 * @author	Thom Bernarth D. Patacsil
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
	$output 									= $api->API_getSalesAgent();    
    	$sales										= '[';
     
    foreach($output->amount as $key => $value) {
	$amount_users[]								= $value->user;
	$full_name[] 								= $value->full_name;
	$sale[]									= $value->sale;
	$amount[]								= $value->amount;
    }	

    $k 								= 0;
    foreach($amount_users as $user){
    
       	$sales 								.= '[';  
       	$sales 								.= '"'.$full_name[$k].'",';   
       	$sales 								.= '"'.$user.'",';  
       	$sales 								.= '"'.$sale[$k].'",';
	$sales 								.= '"'.$amount[$k].'"';
       	$sales 								.= '],';
    
	$k++;
    }

    $sales 									= rtrim($sales, ","); 
    $sales 									.= ']';
    
    echo json_encode($sales);     
    


?>
