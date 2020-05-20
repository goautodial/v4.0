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
     
    foreach ($output->sales as $key => $value) {
    
        $full_name[] 								= $value->full_name;
        $users[]	 								= $value->user;
        $sale[] 								= $value->sale;
    }

    foreach($output->amount as $key => $value) {
	$amount_users[]								= $value->user;
	$amount[]								= $value->amount;
    }	

    $k 								= 0;
    foreach($users as $user){
    
       	$sales 								.= '[';  
       	$sales 								.= '"'.$full_name[$k].'",';   
       	$sales 								.= '"'.$user.'",';  
       	$sales 								.= '"'.$sale[$k].'",';

	$i = 0;
	foreach($amount_users as $amount_user){
		if($amount_user == $user){
			$sales .= '"'.$amount[$i].'"';
		}
		$i++;
	}
       	$sales 								.= '],';
    
	$k++;
    }

    $sales 									= rtrim($sales, ","); 
    $sales 									.= ']';
    
    echo json_encode($sales);     
    


?>
