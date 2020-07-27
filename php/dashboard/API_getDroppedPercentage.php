<?php
/**
 * @file        API_getDroppedPercentage.php
 * @brief       Displays total dropped calls percentage
 * @copyright   Copyright (c) 2020 GOautodial Inc.
 * @author		Thom Bernarth D. Patacsil 
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
	//$output 									= $api->API_getDroppedPercentage();
	$dropped 									= $api->API_getTotalDroppedCalls();
	$totalOut 									= $api->API_getTotalCalls("outbound");
	$totalIn									= $api->API_getTotalCalls("inbound");

	//$output 										= $output->data;
    
	$dropped = $dropped->data;
	$totalCalls = $totalOut->data + $totalIn->data;
	$output = 0;

	if( $totalCalls > 0 ) {
		$output = ( $dropped/$totalCalls )*100;
	}
	if($output == NULL || $output == 0){
        	$output                                                                         = 0;
	}

    echo json_encode($output);
?>
